<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number',
            'type' => 'required|string',
            'capacity' => 'required|integer',
            'base_price' => 'required|numeric',
            'status' => 'in:available,occupied,cleaning,maintenance',
        ]);

        $room = Room::create($request->all());
        return response()->json($room, 201);
    }

    public function show($id)
    {
        $room = Room::findOrFail($id);
        return response()->json($room);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $request->validate([
            'room_number' => "required|unique:rooms,room_number,{$id}",
            'type' => 'sometimes|string',
            'capacity' => 'sometimes|integer',
            'base_price' => 'sometimes|numeric',
            'status' => 'sometimes|in:available,occupied,cleaning,maintenance',
        ]);

        $room->update($request->all());
        return response()->json($room);
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();
        return response()->json(['message' => 'Room deleted successfully']);
    }
}
