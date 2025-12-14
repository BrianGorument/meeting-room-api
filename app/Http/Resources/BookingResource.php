<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'booking_id' => $this->id,
            'user_id' => $this->user_id,
            'room_id' => $this->room_id,
            'start_datetime' => $this->start_datetime->format('Y-m-d H:i'),
            'end_datetime' => $this->end_datetime->format('Y-m-d H:i'),
            'room' => new RoomResource($this->whenLoaded('room')),
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
