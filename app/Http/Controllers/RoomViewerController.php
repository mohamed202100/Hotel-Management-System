<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomViewerController extends Controller
{
    /**
     * Display a list of only 'available' rooms for guests/regular users.
     */
    public function available()
    {
        // Fetch only rooms where status is 'available'
        $availableRooms = Room::where('status', 'available')
            ->orderBy('room_number')
            ->get();

        // Pass the available rooms to the dedicated guest view
        return view('rooms.available', [
            'rooms' => $availableRooms
        ]);
    }
}
