<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parking;
use App\Models\Reservation;

class AdminController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(rules: [
            'name' => 'required|string|unique:parkings,name',
            'location' => 'required|string',
            'total_spaces' => 'required|integer|min:1',
        ]);

        $parking = Parking::create([
            'name' => $request->name,
            'location' => $request->location,
            'total_spaces' => $request->total_spaces,
            'available_spaces' => $request->total_spaces, 
        ]);

        return response()->json(['message' => 'Parking created successfully', 'parking' => $parking], 201);
    }

    public function update(Request $request, Parking $parking)
    {
        $request->validate([
            'name' => 'sometimes|string|unique:parkings,name,' . $parking->id,
            'location' => 'sometimes|string',
            'total_spaces' => 'sometimes|integer|min:1',
        ]);

        $parking->update($request->only(['name', 'location', 'total_spaces']));

        return response()->json(['message' => 'Parking updated successfully', 'parking' => $parking]);
    }

    public function destroy(Parking $parking)
    {
        $parking->delete();
        return response()->json(['message' => 'Parking deleted successfully']);
    }

    public function statistics()
{
    $totalParkings = Parking::count();
    $totalReservations = Reservation::count();
    $activeReservations = Reservation::where('end_time', '>=', now())->count();
    $availableSpaces = Parking::sum('available_spaces');

    return response()->json([
        'total_parkings' => $totalParkings,
        'total_reservations' => $totalReservations,
        'active_reservations' => $activeReservations,
        'total_available_spaces' => $availableSpaces,
    ]);
}


}
