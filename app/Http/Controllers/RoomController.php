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
        return json_success('Rooms retrieved successfully', Room::all()->toArray());
    }

    public function show($id)
    {
        $room = Room::findOrFail($id);

        return json_success('Room retrieved successfully', $room->toArray());
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        $room = Room::create($data);

        return json_success('Room created successfully', $room->toArray(), 201);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        $room->update($data);

        return json_success('Room updated successfully', $room->toArray());
    }

    public function delete($id)
        {
            $room = Room::findOrFail($id);
            $room->delete();

            return json_success('Room deleted successfully');
        }

    public function availability(Request $request, $id)
    {
        $date = $request->query('date');

        if (!$date) {
            return json_error('Query tanggal diperlukan (format: YYYY-MM-DD)', 'date query is required (YYYY-MM-DD)', 422);
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

        return json_success('Availability retrieved successfully', [
            'date' => $date,
            'available_time_slots' => $availableSlots,
        ]);
    }

}
