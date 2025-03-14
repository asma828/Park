<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Parking",
 *     title="Parking",
 *     description="Modèle de parking",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Parking Central"),
 *     @OA\Property(property="location", type="string", example="Downtown"),
 *     @OA\Property(property="available_spaces", type="integer", example=20),
 *     @OA\Property(property="total_spaces", type="integer", example=100),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Parking extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'location', 'total_spaces', 'available_spaces'];
}
