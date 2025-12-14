<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
            'room_id' => $this->id,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
