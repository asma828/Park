<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use Illuminate\Http\Request;

class ParkingController extends Controller
{

    /**
 * @OA\Get(
 *     path="/api/parkings",
 *     summary="Liste des parkings",
 *     tags={"Parkings"},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des parkings disponibles",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Parking")
 *         )
 *     )
 * )
 */
    public function index()
    {
        return response()->json(Parking::all());
    }

     /**
     * @OA\Get(
     *     path="/api/parkings/search",
     *     summary="Rechercher des parkings par localisation",
     *     description="Permet de rechercher des parkings disponibles en fonction de la localisation fournie.",
     *     @OA\Parameter(
     *         name="location",
     *         in="query",
     *         required=true,
     *         description="Localisation du parking à rechercher",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des parkings correspondant à la recherche",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Parking")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation des données"
     *     )
     * )
     */
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
