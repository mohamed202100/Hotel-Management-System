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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all reservations with related room, customer and invoice data
        $reservations = Reservation::with(['room', 'customer', 'invoice'])
            ->orderBy('check_in_date', 'asc')
            ->get();

        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get available rooms for selection
        $rooms = Room::where('status', 'available')->orderBy('room_number')->get();
        // Get all customers for linking to the reservation
        $customers = Customer::orderBy('last_name')->get();

        return view('admin.reservations.create', compact('rooms', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define common validation rules
        $rules = [
            'customer_id' => ['required', 'exists:customers,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', Rule::in(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])],
        ];

        $request->validate($rules);

        // 1. Check for room availability (Overlap check)
        $isAvailable = Reservation::where('room_id', $request->room_id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                    ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date])
                    ->orWhere(function ($q) use ($request) {
                        // Check if new reservation totally encompasses an existing one
                        $q->where('check_in_date', '<', $request->check_in_date)
                            ->where('check_out_date', '>', $request->check_out_date);
                    });
            })
            // Only consider non-cancelled reservations
            ->whereNotIn('status', ['cancelled'])
            ->doesntExist();


        if (!$isAvailable) {
            return redirect()->back()->withErrors(['room_id' => 'The selected room is already booked for the specified date range.'])->withInput();
        }

        // Use DB transaction for atomicity
        DB::beginTransaction();
        try {
            // 2. Create the reservation
            $reservation = Reservation::create($request->only([
                'room_id',
                'customer_id',
                'check_in_date',
                'check_out_date',
                'subtotal',
                'total_amount',
                'status',
            ]));

            // 3. Create the corresponding invoice
            $reservation->invoice()->create([
                // Use total_amount from request as the amount_due
                'amount_due' => $request->total_amount,
                'amount_paid' => 0.00,
                // Use 'unpaid' as the correct enum value for a new invoice
                'payment_status' => 'unpaid',
            ]);

            // 4. Update room status if confirmed or checked_in
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

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        // Load relationships needed for the show view
        $reservation->load(['room', 'customer', 'invoice']);
        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        // We will not implement this view now as requested by user
        // However, the function needs to exist for the resource route to work.
        // If needed, we would fetch $rooms and $customers here.
        $rooms = Room::orderBy('room_number')->get();
        $customers = Customer::orderBy('last_name')->get();

        return view('admin.reservations.edit', compact('reservation', 'rooms', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        // Validation rules are similar to store, but 'room_id' check is complex
        $rules = [
            'customer_id' => ['required', 'exists:customers,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            // Allow update of status to any valid enum value
            'status' => ['required', Rule::in(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])],
            // New fields for invoice update
            'payment_status' => [Rule::in(['unpaid', 'paid', 'partially_paid', 'refunded'])],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
        ];

        $request->validate($rules);

        // 1. Check for room availability, excluding the current reservation
        $isAvailable = Reservation::where('room_id', $request->room_id)
            ->where('id', '!=', $reservation->id) // Exclude current reservation being updated
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
            // 2. Update the reservation
            $reservation->update($request->only([
                'room_id',
                'customer_id',
                'check_in_date',
                'check_out_date',
                'subtotal',
                'total_amount',
                'status',
            ]));

            // 3. Update the corresponding invoice (assuming one invoice per reservation)
            if ($reservation->invoice) {
                $reservation->invoice->update([
                    'amount_due' => $request->total_amount,
                    'amount_paid' => $request->amount_paid ?? $reservation->invoice->amount_paid,
                    'payment_status' => $request->payment_status ?? $reservation->invoice->payment_status,
                ]);
            }

            // 4. Update Room Status based on Reservation Status
            $room = $reservation->room; // Get the room model
            if ($room) {
                if (in_array($reservation->status, ['checked_in', 'confirmed'])) {
                    // If reservation is active, room should be occupied
                    $room->update(['status' => 'occupied']);
                } elseif ($reservation->status == 'checked_out' || $reservation->status == 'cancelled') {
                    // If reservation is finished or cancelled, room should be available
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        // Use DB transaction for atomicity
        DB::beginTransaction();
        try {
            // 1. Delete the associated invoice first
            $reservation->invoice()->delete();

            // 2. Delete the reservation
            $reservation->delete();

            // 3. Update the room status back to available
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
}
