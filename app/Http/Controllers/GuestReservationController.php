<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuestReservationController extends Controller
{
    /**
     * Display a listing of reservations for the logged-in Guest user.
     */
    public function index()
    {
        $user = Auth::user();
        $customer = $user->customer;

        if (!$customer) {
            $reservations = collect();
        } else {
            $reservations = Reservation::where('customer_id', $customer->id)
                ->with(['room', 'invoice'])
                ->orderBy('check_in_date', 'asc')
                ->get();
        }

        return view('my_reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new reservation for Guest.
     */
    public function create(Request $request)
    {
        $selectedRoomId = $request->input('room_id');
        $rooms = Room::where('status', 'available')->get();
        $customer = Auth::user()->customer;

        return view('my_reservations.create-guest', compact('rooms', 'selectedRoomId', 'customer'));
    }

    /**
     * Store a new reservation in storage for the Guest.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $customer = $user->customer;

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        $room = Room::findOrFail($validated['room_id']);

        try {
            $checkIn = Carbon::createFromFormat('Y-m-d', $validated['check_in_date'])->startOfDay();
            $checkOut = Carbon::createFromFormat('Y-m-d', $validated['check_out_date'])->startOfDay();
        } catch (\Exception $e) {
            return back()->withErrors(['check_in_date' => 'Invalid date format'])->withInput();
        }

        if ($checkOut->lte($checkIn)) {
            return back()->withErrors(['check_out_date' => 'Check-out date must be after check-in'])->withInput();
        }

        $nights = $checkOut->diffInDays($checkIn);

        $isAvailable = $this->checkRoomAvailability($validated['room_id'], $checkIn, $checkOut, null);
        if (!$isAvailable) {
            return back()->withErrors(['room_id' => 'The selected room is not available for these dates'])->withInput();
        }

        $subtotal = $room->base_price * $nights;
        $taxRate = 0.15;
        $totalAmount = $subtotal * (1 + $taxRate);

        DB::beginTransaction();
        try {
            $reservation = Reservation::create([
                'room_id' => $validated['room_id'],
                'customer_id' => $customer->id,
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            Invoice::create([
                'reservation_id' => $reservation->id,
                'amount_due' => $totalAmount,
                'amount_paid' => 0,
                'tax_rate' => $taxRate,
                'payment_status' => 'unpaid',
            ]);

            DB::commit();

            return redirect()
                ->route('guest.reservations.index')
                ->with('success', 'Your reservation request (ID: ' . $reservation->id . ') is submitted and PENDING confirmation.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit reservation: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Check if a room is available for given dates.
     */
    private function checkRoomAvailability(int $roomId, Carbon $checkIn, Carbon $checkOut, ?int $excludeReservationId): bool
    {
        $query = Reservation::where('room_id', $roomId)
            ->whereNotIn('status', ['cancelled', 'pending'])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in_date', [$checkIn, $checkOut->subDay()])
                    ->orWhereBetween('check_out_date', [$checkIn->addDay(), $checkOut])
                    ->orWhere(function ($qq) use ($checkIn, $checkOut) {
                        $qq->where('check_in_date', '<', $checkIn)
                            ->where('check_out_date', '>', $checkOut);
                    });
            });

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query->doesntExist();
    }


    public function cancel(Reservation $reservation)
    {
        $customer = Auth::user()->customer;

        if ($reservation->customer_id !== $customer->id) {
            return redirect()->route('guest.reservations.index')
                ->with('error', 'You are not authorized to cancel this reservation.');
        }

        $reservation->invoice()->delete();

        $reservation->delete();

        return redirect()->route('guest.reservations.index')
            ->with('success', 'Your reservation has been successfully cancelled.');
    }
}
