<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parking;
use App\Models\Reservation;

class AdminController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/parkings",
     *     summary="Créer un parking",
     *     description="Cette méthode permet de créer un parking avec des informations spécifiques.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "location", "total_spaces"},
     *             @OA\Property(property="name", type="string", description="Nom du parking"),
     *             @OA\Property(property="location", type="string", description="Emplacement du parking"),
     *             @OA\Property(property="total_spaces", type="integer", description="Nombre total d'emplacements")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Parking créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Parking créé avec succès"),
     *             @OA\Property(property="parking", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="location", type="string"),
     *                 @OA\Property(property="total_spaces", type="integer"),
     *                 @OA\Property(property="available_spaces", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Requête invalide"
     *     )
     * )
     */
    
    
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

        /**
     * @OA\Put(
     *     path="/api/parkings/{id}",
     *     summary="Mettre à jour un parking",
     *     description="Cette méthode permet de mettre à jour les informations d'un parking existant.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du parking à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="Nom du parking"),
     *             @OA\Property(property="location", type="string", description="Emplacement du parking"),
     *             @OA\Property(property="total_spaces", type="integer", description="Nombre total d'emplacements")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Parking mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Parking updated successfully"),
     *             @OA\Property(property="parking", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="location", type="string"),
     *                 @OA\Property(property="total_spaces", type="integer"),
     *                 @OA\Property(property="available_spaces", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Requête invalide"
     *     )
     * )
     */

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

    
    /**
     * @OA\Delete(
     *     path="/api/parkings/{id}",
     *     summary="Supprimer un parking",
     *     description="Cette méthode permet de supprimer un parking.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du parking à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Parking supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Parking deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parking non trouvé"
     *     )
     * )
     */
    public function destroy(Parking $parking)
    {
        $parking->delete();
        return response()->json(['message' => 'Parking deleted successfully']);
    }

        /**
     * @OA\Get(
     *     path="/api/parkings/statistics",
     *     summary="Statistiques sur les parkings et réservations",
     *     description="Cette méthode permet de récupérer les statistiques sur les parkings et les réservations.",
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques des parkings",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_parkings", type="integer", description="Nombre total de parkings"),
     *             @OA\Property(property="total_reservations", type="integer", description="Nombre total de réservations"),
     *             @OA\Property(property="active_reservations", type="integer", description="Nombre de réservations actives"),
     *             @OA\Property(property="total_available_spaces", type="integer", description="Nombre total d'espaces disponibles")
     *         )
     *     )
     * )
     */
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
