<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['room', 'customer', 'invoice'])
            ->orderBy('check_in_date', 'asc')
            ->get();

        return response()->json($reservations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'customer_id' => 'required|exists:users,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        DB::beginTransaction();

        try {
            $reservation = Reservation::create([
                'room_id' => $request->room_id,
                'customer_id' => $request->customer_id,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'status' => 'pending',
            ]);

            $reservation->invoice()->create([
                'amount_due' => $request->amount_due ?? 0,
                'amount_paid' => 0,
                'payment_status' => 'unpaid',
            ]);

            if ($reservation->room) {
                $reservation->room->update(['status' => 'occupied']);
            }

            DB::commit();

            return response()->json($reservation, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating reservation', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $reservation = Reservation::with(['room', 'customer', 'invoice'])->findOrFail($id);
        return response()->json($reservation);
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $reservation->update($request->only([
            'room_id',
            'customer_id',
            'check_in_date',
            'check_out_date',
            'status'
        ]));

        return response()->json($reservation);
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->room) {
            $reservation->room->update(['status' => 'available']);
        }
        $reservation->delete();

        return response()->json(['message' => 'Reservation deleted successfully']);
    }

    public function myReservations(Request $request)
    {
        $user = $request->user();
        $reservations = $user->reservations()->with(['room', 'invoice'])->get();

        return response()->json($reservations);
    }
}
