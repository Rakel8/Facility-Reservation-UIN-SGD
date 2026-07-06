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
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $email = $request->email;
        $password = $request->password;
        $user = null;

        // 1. Dapatkan Base URL API Kampus dari .env / Config (bersihkan trailing slash)
        $campusApiUrl = rtrim(config('services.campus.base_url'), '/');

        // 2. Coba autentikasi via API Kampus jika URL API disetel dan bukan placeholder
        if ($campusApiUrl && !str_contains($campusApiUrl, 'xxxxxxxxxx')) {
            try {
                // Setup headers & config
                $headers = [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'application/json',
                    'Referer' => $campusApiUrl,
                    'Origin' => $campusApiUrl,
                ];

                // Coba endpoint Mahasiswa (Form URL-Encoded + Bypass SSL)
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                    ->asForm()
                    ->withHeaders($headers)
                    ->post($campusApiUrl . '/auth/mahasiswa/login', [
                        'nim' => $email,
                        'username' => $email,
                        'email' => $email,
                        'password' => $password,
                    ]);

                // Jika gagal, coba Mahasiswa (JSON + Bypass SSL)
                if (!$response->successful()) {
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                        ->withHeaders($headers)
                        ->post($campusApiUrl . '/auth/mahasiswa/login', [
                            'nim' => $email,
                            'username' => $email,
                            'email' => $email,
                            'password' => $password,
                        ]);
                }

                // Jika masih gagal, coba Dosen (Form URL-Encoded + Bypass SSL)
                if (!$response->successful()) {
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                        ->asForm()
                        ->withHeaders($headers)
                        ->post($campusApiUrl . '/auth/dosen/login', [
                            'nip' => $email,
                            'username' => $email,
                            'email' => $email,
                            'password' => $password,
                        ]);
                }

                // Jika masih gagal, coba Dosen (JSON + Bypass SSL)
                if (!$response->successful()) {
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                        ->withHeaders($headers)
                        ->post($campusApiUrl . '/auth/dosen/login', [
                            'nip' => $email,
                            'username' => $email,
                            'email' => $email,
                            'password' => $password,
                        ]);
                }

                if ($response->successful()) {
                    $apiData = $response->json();
                    
                    // Mendukung struktur data dibungkus 'user' maupun datar (tanpa bungkus)
                    $userDataFromApi = null;
                    if (isset($apiData['user'])) {
                        $userDataFromApi = $apiData['user'];
                    } elseif (isset($apiData['nim']) || isset($apiData['email']) || isset($apiData['name'])) {
                        $userDataFromApi = $apiData;
                    }

                    if ($userDataFromApi) {
                        // Jika API tidak memberikan email, kita buat dari NIM/NIP
                        $userEmail = $userDataFromApi['email'] ?? null;
                        if (!$userEmail) {
                            $userEmail = ($userDataFromApi['nim'] ?? $userDataFromApi['nip'] ?? $email) . '@uinsgd.ac.id';
                        }

                        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                            $userEmail = $userEmail . '@uinsgd.ac.id';
                        }

                        // Cari user lokal berdasarkan email atau NIM/NIP
                        $user = User::where('email', $userEmail)
                            ->orWhere('nim_nip', $userDataFromApi['nim'] ?? $userDataFromApi['nip'] ?? $email)
                            ->first();

                        if (!$user) {
                            // Auto-register user lokal
                            $user = User::create([
                                'name' => $userDataFromApi['name'] ?? $userDataFromApi['nama'] ?? 'Pengguna Kampus',
                                'email' => $userEmail,
                                'nim_nip' => $userDataFromApi['nim'] ?? $userDataFromApi['nip'] ?? $email,
                                'role' => 'peminjam',
                                'tipe_user' => 'internal',
                                'no_telepon' => $userDataFromApi['no_telepon'] ?? $userDataFromApi['phone'] ?? '-',
                                'password' => Hash::make(\Illuminate\Support\Str::random(16)),
                            ]);
                        }
                    }
                } else {
                    \Illuminate\Support\Facades\Log::warning('Login API Kampus gagal. Status: ' . $response->status() . ' Response: ' . $response->body());
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Koneksi API Kampus Gagal: ' . $e->getMessage());
            }
        }

        // 3. Cadangan (Fallback): Jika API gagal / URL kosong, verifikasi via DB lokal (Bisa menggunakan NIM atau Email)
        if (!$user) {
            $user = User::where('email', $email)->orWhere('nim_nip', $email)->first();

            if (!$user || !Hash::check($password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIM/Email atau Password Anda salah.',
                    'data' => null,
                ], 401);
            }
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
     * Register
     *
     * Create a new user account with role peminjam.
     *
     * @bodyParam name string required The user's full name. Example: Muhammad Rizki
     * @bodyParam email string required The user email (must be unique). Example: rizki@gmail.com
     * @bodyParam password string required The password. Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Example: password123
     * @bodyParam no_telepon string required The phone number. Example: 081234567890
     * @bodyParam tipe_user string required User type (internal or eksternal). Example: internal
     * @bodyParam nim_nip string NIM or NIP (only for internal users). Example: 1234567890
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Registration successful",
     *   "data": {
     *     "user": {
     *       "id": 4,
     *       "name": "Muhammad Rizki",
     *       "email": "rizki@gmail.com",
     *       "role": "peminjam",
     *       "no_telepon": "081234567890",
     *       "tipe_user": "internal",
     *       "nim_nip": "1234567890",
     *       "created_at": "2026-07-03T14:40:00.000000Z",
     *       "updated_at": "2026-07-03T14:40:00.000000Z"
     *     },
     *     "token": "2|abcdefghijklmnopqrstuvwxyz"
     *   }
     * }
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'no_telepon' => 'required|string|max:20',
            'tipe_user' => 'required|string|in:internal,eksternal',
            'nim_nip' => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'peminjam',
            'no_telepon' => $validated['no_telepon'],
            'tipe_user' => $validated['tipe_user'],
            'nim_nip' => $validated['nim_nip'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
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
