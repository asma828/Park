<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
     /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Enregistrer un utilisateur",
     *     description="Cette méthode permet d'enregistrer un nouvel utilisateur en fournissant son nom, email, mot de passe et rôle.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "role"},
     *             @OA\Property(property="name", type="string", description="Nom de l'utilisateur"),
     *             @OA\Property(property="email", type="string", format="email", description="Email de l'utilisateur"),
     *             @OA\Property(property="password", type="string", format="password", description="Mot de passe de l'utilisateur"),
     *             @OA\Property(property="role", type="string", description="Rôle de l'utilisateur (user ou admin)", enum={"user", "admin"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur enregistré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", description="Token d'authentification de l'utilisateur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation des données"
     *     )
     * )
     */
    public function register(Request $request)
    {
        // Validate the register request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|string|in:user,admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // $user = User::create($validator);

        // Create a token for the newly registered user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

/**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Se connecter",
     *     description="Cette méthode permet de connecter un utilisateur existant en utilisant son email et son mot de passe.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", description="Email de l'utilisateur"),
     *             @OA\Property(property="password", type="string", format="password", description="Mot de passe de l'utilisateur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", description="Token d'authentification de l'utilisateur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Identifiants invalides"
     *     )
     * )
     */

    public function login(Request $request)
    {
        // Validate the login request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create a token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token]);
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
    
}
