<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->reservations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'parking_id' => 'required|exists:parkings,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'parking_id' => $request->parking_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return response()->json($reservation, 201);
    }
}

