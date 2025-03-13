<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Parking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $reservations = $user->reservations;
    
        return response()->json(['reservations' => $reservations]);
    }
    

    public function store(Request $request)
{
    $request->validate([
        'parking_id' => 'required|exists:parkings,id',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after:start_time',
    ]);

    // Get the authenticated user
    $user = Auth::user();
    
    // Check parking availability
    $parking = Parking::find($request->parking_id);
    
    
    if ($this->isParkingAvailable($parking, $request->start_time, $request->end_time)) {
        $reservation = Reservation::create([
            'user_id' => $user->id,  
            'parking_id' => $request->parking_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);
        
        return response()->json(['reservation' => $reservation], 201);
    } else {
        return response()->json(['message' => 'Parking spot is not available for the selected time range.'], 400);
    }
}  

private function isParkingAvailable($parking, $start_time, $end_time)
{
    
    $existingReservations = Reservation::where('parking_id', $parking->id)
        ->where(function ($query) use ($start_time, $end_time) {
            $query->whereBetween('start_time', [$start_time, $end_time])
                ->orWhereBetween('end_time', [$start_time, $end_time])
                ->orWhere(function ($query) use ($start_time, $end_time) {
                    $query->where('start_time', '<', $start_time)
                        ->where('end_time', '>', $end_time);
                });
        })->exists();

    return !$existingReservations;
}

public function update(Request $request, $id)
{
    $request->validate([
        'start_time' => 'required|date',
        'end_time' => 'required|date|after:start_time',
    ]);

    $reservation = Reservation::findOrFail($id);
    
    // Check if the authenticated user owns the reservation
    if ($reservation->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Check parking availability for the new time 
    $parking = $reservation->parking;
    if ($this->isParkingAvailable($parking, $request->start_time, $request->end_time)) {
        // Update the reservation
        $reservation->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);
        
        return response()->json(['reservation' => $reservation], 200);
    } else {
        return response()->json(['message' => 'Parking spot is not available for the new time range.'], 400);
    }
}

public function cancel($id)
{
    $reservation = Reservation::findOrFail($id);
    
    // Check if the authenticated user owns the reservation
    if ($reservation->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Delete the reservation and update the parking's available space
    $reservation->delete();

    // Update parking availability
    $parking = $reservation->parking;
    $parking->increment('available_spaces');
    
    return response()->json(['message' => 'Reservation canceled successfully.'], 200);
}

public function history()
{
    $user = Auth::user();

    $pastReservations = Reservation::where('user_id', $user->id)
        ->where('end_time', '<', now())
        ->get();

    $currentReservations = Reservation::where('user_id', $user->id)
        ->where('end_time', '>=', now())
        ->get();

    return response()->json([
        'current_reservations' => $currentReservations,
        'past_reservations' => $pastReservations,
    ]);
}

}

