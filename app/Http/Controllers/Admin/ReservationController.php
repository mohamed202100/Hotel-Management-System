<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    public function index()
    {
        $query = Reservation::with(['room', 'customer', 'invoice'])->orderBy('check_in_date', 'desc');

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $reservations = $query->paginate(10);

        return view('admin.reservations.index', compact('reservations'));
    }

    public function create()
    {
        $rooms = Room::where('status', 'available')->orderBy('room_number')->get();
        $customers = Customer::orderBy('last_name')->get();

        return view('admin.reservations.create', compact('rooms', 'customers'));
    }

    private function isRoomAvailable($roomId, $checkIn, $checkOut, $excludeReservationId = null)
    {
        return Reservation::where('room_id', $roomId)
            ->when($excludeReservationId, fn($q) => $q->where('id', '!=', $excludeReservationId))
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in_date', '<', $checkIn)
                            ->where('check_out_date', '>', $checkOut);
                    });
            })
            ->whereNotIn('status', ['cancelled'])
            ->doesntExist();
    }

    private function updateRoomStatus(Reservation $reservation)
    {
        if (!$reservation->room) return;

        match ($reservation->status) {
            'checked_in', 'confirmed' => $reservation->room->update(['status' => 'occupied']),
            'checked_out', 'cancelled' => $reservation->room->update(['status' => 'available']),
            default => null,
        };
    }

    public function store(StoreReservationRequest $request)
    {
        if (!$this->isRoomAvailable($request->room_id, $request->check_in_date, $request->check_out_date)) {
            return back()->withErrors([
                'room_id' => 'The selected room is already booked for the specified date range.'
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            $reservation = Reservation::create($request->only([
                'room_id',
                'customer_id',
                'check_in_date',
                'check_out_date',
                'subtotal',
                'total_amount',
                'status',
            ]));

            $reservation->invoice()->create([
                'amount_due' => $request->total_amount,
                'amount_paid' => 0.00,
                'payment_status' => 'unpaid',
            ]);

            $this->updateRoomStatus($reservation);

            DB::commit();

            return redirect()->route('reservations.index')
                ->with('success', 'Reservation created successfully and invoice generated.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()
                ->withErrors(['error' => 'An error occurred during reservation. Please try again.'])
                ->withInput();
        }
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['room', 'customer', 'invoice']);
        return view('admin.reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $rooms = Room::orderBy('room_number')->get();
        $customers = Customer::orderBy('last_name')->get();

        return view('admin.reservations.edit', compact('reservation', 'rooms', 'customers'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $isAdmin = auth()->user()?->role === 'admin';
        $isPending = $reservation->status === 'pending';

        if (!$isPending && !$isAdmin && !$this->isRoomAvailable(
            $request->room_id,
            $request->check_in_date,
            $request->check_out_date,
            $reservation->id
        )) {
            return back()
                ->withErrors(['room_id' => 'The selected room is already booked for the specified date range.'])
                ->withInput();
        }

        if ($isAdmin && !$this->isRoomAvailable(
            $request->room_id,
            $request->check_in_date,
            $request->check_out_date,
            $reservation->id
        ) && !$request->has('confirm_override')) {
            return back()
                ->with('warning', 'This room is already booked for the selected date range. Confirm override to proceed.')
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $reservation->update($request->only([
                'room_id',
                'customer_id',
                'check_in_date',
                'check_out_date',
                'subtotal',
                'total_amount',
                'status',
            ]));

            if ($reservation->invoice) {
                $reservation->invoice->update([
                    'amount_due' => $request->total_amount,
                    'amount_paid' => $request->input('amount_paid', $reservation->invoice->amount_paid),
                    'payment_status' => $request->input('payment_status', $reservation->invoice->payment_status),
                ]);
            }

            $this->updateRoomStatus($reservation);

            DB::commit();

            return redirect()->route('reservations.index')
                ->with('success', 'Reservation updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation update failed', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()
                ->withErrors(['error' => 'An error occurred during reservation update. Please try again.'])
                ->withInput();
        }
    }

    public function destroy(Reservation $reservation)
    {
        DB::beginTransaction();
        try {
            $reservation->invoice()->delete();
            $reservation->delete();

            if ($reservation->room) {
                $reservation->room->update(['status' => 'available']);
            }

            DB::commit();

            return redirect()->route('reservations.index')
                ->with('success', 'Reservation deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation deletion failed', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'An error occurred while deleting the reservation.']);
        }
    }

    public function invoice(Reservation $reservation)
    {
        if (!$reservation->invoice) {
            return back()->with('error', 'Invoice details are not available for this reservation.');
        }

        $reservation->load(['room', 'customer', 'invoice']);
        return view('admin.invoices.show', compact('reservation'));
    }
}
