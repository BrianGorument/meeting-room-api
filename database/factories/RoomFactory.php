<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'name' => 'Meeting Room ' . $this->faker->unique()->randomLetter(),
            'capacity' => $this->faker->numberBetween(4, 20),
        ];
    }
}
