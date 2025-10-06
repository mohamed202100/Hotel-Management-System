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
        // Retrieve room_id from the request if the user clicked 'Book Now'
        $selectedRoomId = $request->input('room_id');
        $rooms = Room::where('status', 'available')->get();

        return view('my_reservations.create-guest', compact('rooms', 'selectedRoomId'));
    }

    /**
     * Store a new reservation in storage for the Guest.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        $user = Auth::user();

        // 1. Find or create Customer associated with the logged-in User
        $customer = Customer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $user->name,
                'last_name' => '',
                'email' => $user->email,
                'phone_number' => 'N/A',
                'passport_id' => 'N/A',
            ]
        );

        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);

        // 2. Check for room availability based on dates
        $isAvailable = $this->checkRoomAvailability($validated['room_id'], $checkIn, $checkOut, null);

        if (!$isAvailable) {
            return back()->withErrors(['room_id' => 'The selected room is no longer available during the specified dates.'])->withInput();
        }

        // 3. Calculate total amount
        $nights = $checkOut->diffInDays($checkIn);
        $totalAmount = $room->base_price * $nights * 1.15; // Assuming 15% tax included in calculation

        // 4. Start Database Transaction
        DB::beginTransaction();
        try {
            $reservation = Reservation::create([
                'room_id' => $validated['room_id'],
                'customer_id' => $customer->id,
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            Invoice::create([
                'reservation_id' => $reservation->id,
                'amount_due' => $totalAmount,
                'amount_paid' => 0,
                'tax_rate' => 0.15,
                'payment_status' => 'unpaid',
            ]);

            DB::commit();

            return redirect()->route('guest.reservations.index')->with('success', 'Your reservation request (ID: ' . $reservation->id . ') has been submitted successfully.');
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
