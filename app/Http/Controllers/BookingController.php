<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class BookingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['required', 'date', 'after:start_datetime'],
        ]);

        $start = Carbon::parse($data['start_datetime']);
        $end   = Carbon::parse($data['end_datetime']);

        //validate booking time
        if ($start->isPast()) {
            return response()->json([
                'message' => 'Cannot book past time'
            ], 422);
        }

        if ($start->diffInHours($end) < 1) {
            return response()->json([
                'message' => 'Minimum booking is 1 hour'
            ], 422);
        }

        // Booking di hari yang sama
        if ($start->toDateString() !== $end->toDateString()) {
            return response()->json([
                'message' => 'Booking must be within the same day'
            ], 422);
        }

        //booking di jam kerja saja
        if ($start->hour < 8 || $end->hour > 18) {
            return response()->json([
                'message' => 'Booking must be between 08:00 and 18:00'
            ], 422);
        }

        // Booking dengan satuan jam
        if ($start->minute !== 0 || $end->minute !== 0) {
            return response()->json([
                'message' => 'Booking must start and end on the hour'
            ], 422);
        }
        
        $timenow = now();

        if ($end->lessThanOrEqualTo($timenow)) {
            return response()->json([
                'message' => 'Cannot book a room that has already ended'
            ], 422);
        }
        //db transaction
        try {
            $booking = DB::transaction(function () use ($data, $start, $end) {

                // Lock existing bookings for this room & date range
                $conflict = Booking::where('room_id', $data['room_id'])
                ->where(function ($query) use ($start, $end) {
                    $query
                        ->where('start_datetime', '<', $end)
                        ->where('end_datetime', '>', $start);
                })
                ->lockForUpdate()
                ->exists();

                if ($conflict) {
                    abort(409, 'Room already booked for this time');
                }

                return Booking::create([
                    'user_id' => auth()->id(),
                    'room_id' => $data['room_id'],
                    'start_datetime' => $start,
                    'end_datetime' => $end,
                ]);
            });

            return (new BookingResource($booking))
                ->additional(['message' => 'Booking created successfully'])
                ->response()
                ->setStatusCode(201);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 409);
        }
    }

    public function myBookings(Request $request)
    {
        $user = $request->user();

        $bookings = Booking::with('room')
            ->where('user_id', $user->id)
            ->orderBy('start_datetime')
            ->get();

        return BookingResource::collection($bookings);
    }
}
