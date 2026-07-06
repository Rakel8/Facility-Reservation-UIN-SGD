<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\AdminController;

Route::prefix('v1')->group(function () {
    // Public Routes - Authentication
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::get('/rooms', [RoomController::class, 'index']); // Public read
    Route::get('/rooms/image-proxy', [RoomController::class, 'imageProxy']); // Image proxy to bypass CORS/ORB
    Route::get('/reservations/schedule', [ReservationController::class, 'schedule']); // Get all active reservations schedule (Public view)
    Route::get('/faculties', function () {
        return response()->json([
            'success' => true,
            'data' => \App\Models\Faculty::all()
        ]);
    });

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth Routes
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Admin Management - Only Superadmin can manage admins
        Route::apiResource('/admins', AdminController::class)->middleware('role:super_admin,superadmin');

        // Rooms Routes - Only Superadmin can create/update/delete
        Route::post('/rooms', [RoomController::class, 'store'])->middleware('role:super_admin,superadmin');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->middleware('role:super_admin,superadmin');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->middleware('role:super_admin,superadmin');

        // Reservations Routes - Peminjam can submit reservations
        Route::get('/reservations/available', [ReservationController::class, 'index']); // Get available rooms
        Route::get('/reservations/history', [ReservationController::class, 'history']); // User's reservation history (MUST be before {id} routes)
        Route::get('/reservations/{reservation}/letter/pdf', [ReservationController::class, 'downloadLetterPDF']); // Download permission letter PDF
        Route::get('/reservations/{reservation}/proposal', [ReservationController::class, 'downloadProposal']); // Download proposal PDF
        Route::post('/reservations', [ReservationController::class, 'store'])->middleware('role:peminjam'); // Submit reservation

        // Approval Routes - Admins (Fakultas, Universitas, Bisnis, Kemahasiswaan) can approve/reject
        Route::get('/approvals/pending', [ApprovalController::class, 'pending'])->middleware('role:admin_fakultas,admin_universitas,admin_bisnis,admin_kemahasiswaan');
        Route::put('/approvals/{reservation}/approve', [ApprovalController::class, 'approve'])->middleware('role:admin_fakultas,admin_universitas,admin_bisnis,admin_kemahasiswaan');
        Route::put('/approvals/{reservation}/reject', [ApprovalController::class, 'reject'])->middleware('role:admin_fakultas,admin_universitas,admin_bisnis,admin_kemahasiswaan');
    });
});
