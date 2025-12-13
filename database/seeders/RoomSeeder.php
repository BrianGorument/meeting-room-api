<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::insert([
            [
                'name' => 'Meeting Room Alpaca',
                'capacity' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Meeting Room Byson',
                'capacity' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
