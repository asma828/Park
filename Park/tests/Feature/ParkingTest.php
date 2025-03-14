<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Parking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_parking()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $response = $this->postJson('/api/admin/parkings', [
            'name' => 'Parking Central',
            'location' => 'Downtown',
            'total_spaces' => 60,
            'available_spaces' => 50,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['parking' => ['id', 'name', 'location', 'available_spaces']]);
    }

    /** @test */
    /** @test */
/** @test */
/** @test */
public function user_can_view_all_parkings()
{
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user);
    
    Parking::factory()->count(3)->create();

    $response = $this->getJson('/api/parkings');

    $response->assertJsonStructure([['id', 'name', 'location', 'total_spaces', 'available_spaces']]);
}

    /** @test */
    public function admin_can_update_parking()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $parking = Parking::factory()->create();
    
        $this->actingAs($admin);
    
        $response = $this->putJson("/api/admin/parkings/{$parking->id}", [
            'name' => 'Parking',
            'total_spaces' => 100, 
            'available_spaces' => 30,
        ]);
    
        $response->assertStatus(200)
            ->assertJson(['message' => 'Parking updated successfully']);
    }
    
    

    /** @test */
    public function admin_can_delete_parking()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $parking = Parking::factory()->create();
    
        $this->actingAs($admin);
    
        $response = $this->deleteJson("/api/admin/parkings/{$parking->id}");
    
        $response->assertStatus(200)
            ->assertJson(['message' => 'Parking deleted successfully']);
        $this->assertDatabaseMissing('parkings', ['id' => $parking->id]);
    }
    
}
