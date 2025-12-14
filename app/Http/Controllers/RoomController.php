<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Booking;


class RoomController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Room::all()
        ]);
    }

    public function show($id)
    {
        $room = Room::findOrFail($id);

        return response()->json([
            'data' => $room
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        $room = Room::create($data);

        return response()->json([
            'message' => 'Room created successfully',
            'data' => $room
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        $room->update($data);

        return response()->json([
            'message' => 'Room updated successfully',
            'data' => $room
        ]);
    }

    public function delete($id)
        {
            $room = Room::findOrFail($id);
            $room->delete();

            return response()->json([
                'message' => 'Room deleted successfully'
            ]);
        }

    public function availability(Request $request, $id)
    {
        $date = $request->query('date');

        if (!$date) {
            return response()->json([
                'message' => 'date query is required (YYYY-MM-DD)'
            ], 422);
        }

        $room = Room::findOrFail($id);

        $dayStart = Carbon::parse($date)->setTime(8, 0);
        $dayEnd   = Carbon::parse($date)->setTime(18, 0);

        $bookings = Booking::where('room_id', $room->id)
            ->whereDate('start_datetime', $date)
            ->orderBy('start_datetime')
            ->get();

        $availableSlots = [];
        $currentStart = $dayStart->copy();

        foreach ($bookings as $booking) {
            $bookingStart = Carbon::parse($booking->start_datetime);
            $bookingEnd   = Carbon::parse($booking->end_datetime);

            if ($bookingStart->greaterThan($currentStart)) {
                $diffInHours = $currentStart->diffInHours($bookingStart);

                if ($diffInHours >= 1) {
                    $availableSlots[] = [
                        'start' => $currentStart->format('H:i'),
                        'end'   => $bookingStart->format('H:i'),
                    ];
                }
            }

            if ($bookingEnd->greaterThan($currentStart)) {
                $currentStart = $bookingEnd;
            }
        }

        if ($currentStart->lessThan($dayEnd)) {
            $diffInHours = $currentStart->diffInHours($dayEnd);

            if ($diffInHours >= 1) {
                $availableSlots[] = [
                    'start' => $currentStart->format('H:i'),
                    'end'   => $dayEnd->format('H:i'),
                ];
            }
        }

        return response()->json([
            'date' => $date,
            'available_time_slots' => $availableSlots,
        ]);
    }

}
