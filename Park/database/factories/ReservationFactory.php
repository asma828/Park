<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Parking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition()
    {
        $startTime = Carbon::now()->addHours(rand(1, 24));
        $endTime = (clone $startTime)->addHours(rand(1, 5));
        
        return [
            'user_id' => User::factory(),
            'parking_id' => Parking::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            
        ];
    }
}