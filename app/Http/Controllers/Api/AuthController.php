<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @group Authentication
 * API endpoints for user authentication
 */
class AuthController extends Controller
{
    /**
     * Login
     *
     * Authenticate user with email and password to receive Sanctum token.
     *
     * @bodyParam email string required The user email. Example: mahasiswa@uin.ac.id
     * @bodyParam password string required The user password. Example: password123
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Login successful",
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "Mahasiswa Peminjam",
     *       "email": "mahasiswa@uin.ac.id",
     *       "role": "peminjam",
     *       "created_at": "2026-05-31T00:00:00.000000Z",
     *       "updated_at": "2026-05-31T00:00:00.000000Z"
     *     },
     *     "token": "1|abcdefghijklmnopqrstuvwxyz"
     *   }
     * }
     *
     * @response 401 {
     *   "success": false,
     *   "message": "Invalid credentials",
     *   "data": null
     * }
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'data' => null,
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout
     *
     * Revoke the current user's authentication token.
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Logout successful",
     *   "data": null
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
            'data' => null,
        ]);
    }

    /**
     * Get Current User
     *
     * Retrieve the authenticated user's information.
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "User retrieved successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Mahasiswa Peminjam",
     *     "email": "mahasiswa@uin.ac.id",
     *     "role": "peminjam",
     *     "created_at": "2026-05-31T00:00:00.000000Z",
     *     "updated_at": "2026-05-31T00:00:00.000000Z"
     *   }
     * }
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully',
            'data' => $request->user(),
        ]);
    }
}
