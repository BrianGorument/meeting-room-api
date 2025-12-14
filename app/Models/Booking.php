<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'start_datetime',
        'end_datetime',
    ];

    protected $casts = [
        'start_datetime' => 'datetime:Y-m-d H:i',
        'end_datetime' => 'datetime:Y-m-d H:i',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

}


