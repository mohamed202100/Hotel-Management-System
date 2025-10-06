<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::paginate(15);

        return view('admin.rooms.index', compact('rooms'));
    }


    public function create(): View
    {
        return view('admin.rooms.create');
    }


    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms,room_number',
            'type' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        Room::create([
            'room_number' => $request->room_number,
            'type' => $request->type,
            'capacity' => $request->capacity,
            'price' => $request->price,
            'is_available' => true,
        ]);

        return redirect()->route('rooms.index')
            ->with('success', 'Room Added Successfully');
    }


    public function show(Room $room): View
    {
        return view('admin.rooms.show', compact('room'));
    }


    public function edit(Room $room): View
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms,room_number,' . $room->id,
            'type' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $room->update($request->all());

        return redirect()->route('rooms.index')
            ->with('success', 'Room updated Successfully');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $room->delete();

        return redirect()->route('rooms.index')
            ->with('success', 'Room Deleted Successfully');
    }
}
