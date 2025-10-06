<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['room', 'customer', 'invoice'])
            ->orderBy('check_in_date', 'asc')
            ->get();

        return view('admin.reservations.index', compact('reservations'));
    }


    public function create()
    {
        $rooms = Room::where('status', 'available')->orderBy('room_number')->get();
        $customers = Customer::orderBy('last_name')->get();

        return view('admin.reservations.create', compact('rooms', 'customers'));
    }


    public function store(Request $request)
    {
        $rules = [
            'customer_id' => ['required', 'exists:customers,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', Rule::in(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])],
        ];

        $request->validate($rules);

        $isAvailable = Reservation::where('room_id', $request->room_id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                    ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('check_in_date', '<', $request->check_in_date)
                            ->where('check_out_date', '>', $request->check_out_date);
                    });
            })
            ->whereNotIn('status', ['cancelled'])
            ->doesntExist();


        if (!$isAvailable) {
            return redirect()->back()->withErrors(['room_id' => 'The selected room is already booked for the specified date range.'])->withInput();
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

            if (in_array($reservation->status, ['confirmed', 'checked_in'])) {
                Room::where('id', $reservation->room_id)->update(['status' => 'occupied']);
            }

            DB::commit();

            return redirect()->route('reservations.index')
                ->with('success', 'Reservation created successfully and invoice generated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
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


    public function update(Request $request, Reservation $reservation)
    {
        $rules = [
            'customer_id' => ['required', 'exists:customers,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', Rule::in(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])],
            'payment_status' => [Rule::in(['unpaid', 'paid', 'partially_paid', 'refunded'])],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
        ];

        $request->validate($rules);

        $isAvailable = Reservation::where('room_id', $request->room_id)
            ->where('id', '!=', $reservation->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                    ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('check_in_date', '<', $request->check_in_date)
                            ->where('check_out_date', '>', $request->check_out_date);
                    });
            })
            ->whereNotIn('status', ['cancelled'])
            ->doesntExist();

        if (!$isAvailable) {
            return redirect()->back()->withErrors(['room_id' => 'The selected room is already booked for the specified date range.'])->withInput();
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
                    'amount_paid' => $request->amount_paid ?? $reservation->invoice->amount_paid,
                    'payment_status' => $request->payment_status ?? $reservation->invoice->payment_status,
                ]);
            }

            $room = $reservation->room;
            if ($room) {
                if (in_array($reservation->status, ['checked_in', 'confirmed'])) {
                    $room->update(['status' => 'occupied']);
                } elseif ($reservation->status == 'checked_out' || $reservation->status == 'cancelled') {
                    $room->update(['status' => 'available']);
                }
            }


            DB::commit();

            return redirect()->route('reservations.index')
                ->with('success', 'Reservation updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
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

            Room::where('id', $reservation->room_id)->update(['status' => 'available']);

            DB::commit();

            return redirect()->route('reservations.index')
                ->with('success', 'Reservation deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while deleting the reservation.']);
        }
    }

    public function invoice(Reservation $reservation)
    {
        // 1. Check if the invoice exists before showing.
        if (!$reservation->invoice) {
            // Optional: Redirect back with an error, or generate a 404
            return redirect()->back()->with('error', 'Invoice details are not available for this reservation.');
        }

        // 2. Load related data (Room and Customer)
        $reservation->load(['room', 'customer', 'invoice']);

        // 3. Return the print-friendly view
        return view('admin.invoices.show', compact('reservation'));
    }
}
