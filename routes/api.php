<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', function (Request $request) {
    // Validate the request data
    $validatedData = $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
    ]);

    // Create a new user
    $user = \App\Models\User::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'password' => bcrypt($validatedData['password']),
    ]);

    // Generate a token for the user
    $token = $user->createToken('auth_token')->plainTextToken;

    // Return the token as the API response
    return response()->json(['token' => $token], 201);
});

Route::post('/login', function (Request $request) {
    // Validate the request data
    $validatedData = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Check if the credentials are valid
    if (!auth()->attempt($validatedData)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Get the authenticated user
    $user = auth()->user();

    // Generate a new token for the user
    $token = $user->createToken('auth_token')->plainTextToken;

    // Return the token as the API response
    return response()->json(['token' => $token]);
});

