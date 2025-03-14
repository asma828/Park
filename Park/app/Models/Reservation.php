<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Reservation",
 *     title="Reservation",
 *     description="Modèle de réservation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=2),
 *     @OA\Property(property="parking_id", type="integer", example=5),
 *     @OA\Property(property="start_time", type="string", format="date-time", example="2025-03-14 08:00:00"),
 *     @OA\Property(property="end_time", type="string", format="date-time", example="2025-03-14 12:00:00"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Reservation extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'parking_id', 'start_time', 'end_time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parking()
    {
        return $this->belongsTo(Parking::class);
    }
}
