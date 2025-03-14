<?php

namespace Database\Factories;

use App\Models\Parking;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParkingFactory extends Factory
{
    protected $model = Parking::class;

    public function definition()
{
    $total = $this->faker->numberBetween(50, 200);
    return [
        'name' => $this->faker->company,
        'location' => $this->faker->address,
        'total_spaces' => $total,
        'available_spaces' => $this->faker->numberBetween(10, $total),
    ];
}
}
