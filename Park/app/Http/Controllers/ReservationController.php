<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Parking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReservationController extends Controller
{

  /**
 * @OA\Get(
 *     path="/api/reservations",
 *     summary="Liste des réservations de l'utilisateur",
 *     tags={"Reservations"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des réservations",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Reservation")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé"
 *     )
 * )
 */
    public function index()
    {
        $user = Auth::user();
        $reservations = $user->reservations;
    
        return response()->json(['reservations' => $reservations]);
    }
    

    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     summary="Créer une réservation",
     *     description="Permet de réserver une place de parking si elle est disponible.",
     *     security={{ "bearerAuth":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"parking_id", "start_time", "end_time"},
     *             @OA\Property(property="parking_id", type="integer", example=1),
     *             @OA\Property(property="start_time", type="string", format="date-time", example="2025-03-15 10:00:00"),
     *             @OA\Property(property="end_time", type="string", format="date-time", example="2025-03-15 12:00:00")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Réservation créée avec succès"),
     *     @OA\Response(response=400, description="La place de parking n'est pas disponible"),
     *     @OA\Response(response=422, description="Erreur de validation des données")
     * )
     */
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

/**
     * @OA\Put(
     *     path="/api/reservations/{id}",
     *     summary="Modifier une réservation",
     *     description="Permet de modifier les horaires d'une réservation existante si la place est disponible.",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la réservation à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"start_time", "end_time"},
     *             @OA\Property(property="start_time", type="string", format="date-time", example="2025-03-15 10:00:00"),
     *             @OA\Property(property="end_time", type="string", format="date-time", example="2025-03-15 12:00:00")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Réservation mise à jour avec succès"),
     *     @OA\Response(response=400, description="La place de parking n'est pas disponible"),
     *     @OA\Response(response=403, description="Accès non autorisé"),
     *     @OA\Response(response=404, description="Réservation non trouvée")
     * )
     */
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
 /**
     * @OA\Delete(
     *     path="/api/reservations/{id}",
     *     summary="Annuler une réservation",
     *     description="Permet à l'utilisateur d'annuler une réservation existante.",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la réservation à annuler",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Réservation annulée avec succès"),
     *     @OA\Response(response=403, description="Accès non autorisé"),
     *     @OA\Response(response=404, description="Réservation non trouvée")
     * )
     */
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

 /**
     * @OA\Get(
     *     path="/api/reservations/history",
     *     summary="Historique des réservations",
     *     description="Récupère les réservations passées et actuelles de l'utilisateur.",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="Historique des réservations récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_reservations", type="array", @OA\Items(ref="#/components/schemas/Reservation")),
     *             @OA\Property(property="past_reservations", type="array", @OA\Items(ref="#/components/schemas/Reservation"))
     *         )
     *     )
     * )
     */
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

