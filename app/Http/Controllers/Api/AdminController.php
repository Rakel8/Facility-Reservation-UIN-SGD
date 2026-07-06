<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * @group Admin Management
 * API endpoints for Super Admin to manage other Admin users
 */
class AdminController extends Controller
{
    /**
     * List All Admins
     */
    public function index()
    {
        $admins = User::whereIn('role', ['admin_fakultas', 'admin_universitas', 'admin_bisnis', 'admin_kemahasiswaan'])
            ->with(['faculty', 'rooms'])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Admins retrieved successfully',
            'data' => $admins,
        ]);
    }

    /**
     * Create Admin User
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin_fakultas', 'admin_universitas', 'admin_bisnis', 'admin_kemahasiswaan'])],
            'fakultas_id' => 'nullable|exists:faculties,id|required_if:role,admin_fakultas',
            'room_ids' => 'nullable|array',
            'room_ids.*' => 'exists:rooms,id',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'fakultas_id' => $validated['role'] === 'admin_fakultas' ? $validated['fakultas_id'] : null,
        ];

        $user = User::create($userData);

        if (in_array($validated['role'], ['admin_universitas', 'admin_kemahasiswaan']) && !empty($validated['room_ids'])) {
            $user->rooms()->sync($validated['room_ids']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Admin user created successfully',
            'data' => $user->load(['faculty', 'rooms']),
        ], 201);
    }

    /**
     * Update Admin User
     */
    public function update(Request $request, User $admin)
    {
        // Make sure we only edit admins, not super_admins or standard users
        if (!in_array($admin->role, ['admin_fakultas', 'admin_universitas', 'admin_bisnis', 'admin_kemahasiswaan'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update this user via admin management',
                'data' => null,
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => ['string', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'password' => 'nullable|string|min:8',
            'role' => [Rule::in(['admin_fakultas', 'admin_universitas', 'admin_bisnis', 'admin_kemahasiswaan'])],
            'fakultas_id' => 'nullable|exists:faculties,id|required_if:role,admin_fakultas',
            'room_ids' => 'nullable|array',
            'room_ids.*' => 'exists:rooms,id',
        ]);

        $userData = [];
        if (isset($validated['name'])) $userData['name'] = $validated['name'];
        if (isset($validated['email'])) $userData['email'] = $validated['email'];
        if (!empty($validated['password'])) $userData['password'] = Hash::make($validated['password']);
        if (isset($validated['role'])) $userData['role'] = $validated['role'];

        if (isset($validated['role'])) {
            $userData['fakultas_id'] = $validated['role'] === 'admin_fakultas' ? ($validated['fakultas_id'] ?? null) : null;
        }

        $admin->update($userData);

        // Update assigned rooms for university admin
        if (in_array($admin->role, ['admin_universitas', 'admin_kemahasiswaan'])) {
            $admin->rooms()->sync($validated['room_ids'] ?? []);
        } else {
            $admin->rooms()->detach(); // Clear pivot if role changed
        }

        return response()->json([
            'success' => true,
            'message' => 'Admin user updated successfully',
            'data' => $admin->fresh()->load(['faculty', 'rooms']),
        ]);
    }

    /**
     * Delete Admin User
     */
    public function destroy(User $admin)
    {
        if (!in_array($admin->role, ['admin_fakultas', 'admin_universitas', 'admin_bisnis', 'admin_kemahasiswaan'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this user via admin management',
                'data' => null,
            ], 403);
        }

        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin user deleted successfully',
            'data' => null,
        ]);
    }
}
