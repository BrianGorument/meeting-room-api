<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Room;

class BookingFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser(array $overrides = []): string
    {
        User::factory()->create(array_merge([
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ], $overrides));

        $response = $this->postJson('/api/auth/login', [
            'email' => 'user@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        return $response->json('data.token');
    }

    public function test_user_can_book_room_successfully()
    {
        $token = $this->authenticateUser();

        $room = Room::factory()->create();

        $response = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'start_datetime' => '2025-12-16 10:00:00',
            'end_datetime'   => '2025-12-16 11:00:00',
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response
        ->assertStatus(201)
        ->assertJson([
            'message' => 'Booking created successfully',
        ])
        ->assertJsonStructure([
            'data' => [
                'booking_id',
                'user_id',
                'room_id',
                'start_datetime',
                'end_datetime',
                'created_at',
            ],
        ]);
    }

    public function test_booking_cannot_overlap_but_allows_adjacent_time()
    {
        $token = $this->authenticateUser();
        $room = Room::factory()->create();

        // Booking pertama: 08–12
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'start_datetime' => '2025-12-16 08:00:00',
            'end_datetime' => '2025-12-16 12:00:00',
        ], [
            'Authorization' => "Bearer {$token}"
        ])->assertStatus(201);

        // Booking overlap: 11–13 (NEGATIVE CASE)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'start_datetime' => '2025-12-16 11:00:00',
            'end_datetime' => '2025-12-16 13:00:00',
        ], [
            'Authorization' => "Bearer {$token}"
        ])
        ->assertStatus(409)
        ->assertJson([
            'Error' => 'Room already booked for this time',
            'Massage' => 'Ruangan sudah dipesan untuk waktu ini',
        ]);

        // Booking sambung: 12–13 (HARUS BOLEH)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'start_datetime' => '2025-12-16 12:00:00',
            'end_datetime' => '2025-12-16 13:00:00',
        ], [
            'Authorization' => "Bearer {$token}"
        ])
        ->assertStatus(201);
    }

    public function test_user_can_book_ongoing_time_slot_if_end_time_is_in_future()
    {
        $token = $this->authenticateUser();
        $room = Room::factory()->create();

       $start = '2025-12-16 12:00:00';
       $end   = '2025-12-16 13:00:00';


        $response = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'start_datetime' => $start,
            'end_datetime' => $end,
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(201);
    }

    public function test_user_can_only_see_their_own_bookings()
    {
        $token = $this->authenticateUser();
        $room = Room::factory()->create();

        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'start_datetime' => '2025-12-16 14:00:00',
            'end_datetime' => '2025-12-16 15:00:00',
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response = $this->getJson('/api/bookings/my', [
            'Authorization' => "Bearer {$token}"
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'room_id',
                        'start_datetime',
                        'end_datetime',
                    ]
                ]
            ]);
    }



}

