<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['room', 'customer'])->get();
        return response()->json($reservations);
    }

    public function show($id)
    {
        $reservation = Reservation::with(['room', 'customer'])->find($id);
        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }
        return response()->json($reservation);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'customer_id' => 'required|exists:customers,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:pending,confirmed,canceled,checked_in,checked_out',
        ]);

        $reservation = Reservation::create($validated);
        return response()->json($reservation, 201);
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) return response()->json(['message' => 'Reservation not found'], 404);

        $reservation->update($request->all());
        return response()->json($reservation);
    }

    public function destroy($id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) return response()->json(['message' => 'Reservation not found'], 404);

        $reservation->delete();
        return response()->json(['message' => 'Reservation deleted successfully']);
    }
}
