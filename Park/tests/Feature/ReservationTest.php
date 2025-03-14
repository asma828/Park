<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Parking;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
   // In your ReservationTest.php
public function user_can_create_reservation()
{
    $user = User::factory()->create(['role' => 'user']); 
    $this->actingAs($user);
    
    $parking = Parking::factory()->create();
    
    $response = $this->postJson('/api/reservations', [
        'parking_id' => $parking->id,
        'start_time' => Carbon::now()->addHour(),
        'end_time' => Carbon::now()->addHours(2),
    ]);
    
    $response->assertStatus(201)
        ->assertJsonStructure([
            'reservation' => [
                'id', 'user_id', 'parking_id', 'start_time', 'end_time'
            ]
        ]);
}

    /** @test */
    public function user_can_update_reservation()
    {
        $user = User::factory()->create(['role' => 'user']);
        $parking = Parking::factory()->create(['available_spaces' => 5]);
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'parking_id' => $parking->id,
            'start_time' => Carbon::now()->addHour(),
            'end_time' => Carbon::now()->addHours(2),
        ]);

        $this->actingAs($user);

        $response = $this->putJson("/api/reservations/{$reservation->id}", [
            'start_time' => Carbon::now()->addHours(3),
            'end_time' => Carbon::now()->addHours(4),
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['reservation']);
    }

    /** @test */
    public function user_can_cancel_reservation()
    {
        $user = User::factory()->create(['role' => 'user']);
        $parking = Parking::factory()->create(['available_spaces' => 5]);
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'parking_id' => $parking->id,
        ]);

        $this->actingAs($user);

        $response = $this->deleteJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Reservation canceled successfully.']);
        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }

    /** @test */
    public function user_can_view_reservations()
    {
        $user = User::factory()->create(['role' => 'user']);
        $parking = Parking::factory()->create(['available_spaces' => 5]);
        Reservation::factory()->count(3)->create(['user_id' => $user->id, 'parking_id' => $parking->id]);

        $this->actingAs($user);

        $response = $this->getJson('/api/reservations');

        $response->assertStatus(200)
            ->assertJsonStructure(['reservations']);
    }

    /** @test */
    public function user_can_view_reservation_history()
    {
        $user = User::factory()->create(['role' => 'user']);
        $parking = Parking::factory()->create(['available_spaces' => 5]);

        // Création de réservations passées et en cours
        Reservation::factory()->create([
            'user_id' => $user->id,
            'parking_id' => $parking->id,
            'start_time' => Carbon::now()->subDays(2),
            'end_time' => Carbon::now()->subDay(),
        ]);

        Reservation::factory()->create([
            'user_id' => $user->id,
            'parking_id' => $parking->id,
            'start_time' => Carbon::now()->addHour(),
            'end_time' => Carbon::now()->addHours(2),
        ]);

        $this->actingAs($user);

        $response = $this->getJson('/api/reservations/history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_reservations', 'past_reservations'
            ]);
    }
}
