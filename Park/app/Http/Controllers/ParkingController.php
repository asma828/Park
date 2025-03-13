<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use Illuminate\Http\Request;

class ParkingController extends Controller
{
    public function index()
    {
        return response()->json(Parking::all());
    }

    public function search(Request $request)
{
    $request->validate([
        'location' => 'required|string',
    ]);

    $parkings = Parking::where('location', 'LIKE', "%{$request->location}%")
        ->where('available_spaces', '>', 0)
        ->get();

    return response()->json($parkings);
}

}
