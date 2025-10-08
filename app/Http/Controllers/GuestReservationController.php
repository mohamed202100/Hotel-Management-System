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
     * (Guest home page)
     */
    public function index()
    {
        $userId = Auth::id();

        // 1. Find the customer associated with the logged-in user
        $customer = Customer::where('user_id', $userId)->first();

        // If the user hasn't made a booking or their customer profile doesn't exist, return empty
        if (!$customer) {
            $reservations = collect();
        } else {
            // 2. Guest user sees only reservations linked to their customer profile
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

        // Pass the customer object itself for the warning check in the view
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

        // ✅ Validation
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
            return back()->withErrors([
                'check_in_date' => 'Invalid date format provided.'
            ])->withInput();
        }

        if ($checkOut->lte($checkIn)) {
            return back()->withErrors([
                'check_out_date' => 'Check-out date must be after check-in date.'
            ])->withInput();
        }

        $nights = $checkOut->diffInDays($checkIn);

        $isAvailable = $this->checkRoomAvailability($validated['room_id'], $checkIn, $checkOut, null);
        if (!$isAvailable) {
            return back()->withErrors([
                'room_id' => 'The selected room is no longer available during the specified dates.'
            ])->withInput();
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
                ->with('success', 'Your reservation request (ID: ' . $reservation->id . ') has been submitted successfully and is pending confirmation by the hotel administration.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit reservation: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Private helper function to check room availability.
     * Logic is duplicated from AdminController to keep the controllers decoupled.
     */
    private function checkRoomAvailability(int $roomId, Carbon $checkIn, Carbon $checkOut, ?int $excludeReservationId): bool
    {
        $query = Reservation::where('room_id', $roomId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($checkIn, $checkOut) {
                // Check for overlapping reservations
                $q->whereBetween('check_in_date', [$checkIn, $checkOut->subDay()])
                    ->orWhereBetween('check_out_date', [$checkIn->addDay(), $checkOut])
                    ->orWhere(function ($qq) use ($checkIn, $checkOut) {
                        $qq->where('check_in_date', '<', $checkIn)
                            ->where('check_out_date', '>', $checkOut);
                    });
            });

        // Exclude the current reservation when updating
        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query->doesntExist();
    }
}
