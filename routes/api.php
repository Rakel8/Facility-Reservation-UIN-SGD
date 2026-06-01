<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ApprovalController;

Route::prefix('v1')->group(function () {
    // Public Routes - Authentication
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth Routes
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Rooms Routes - Only Superadmin can create/update/delete
        Route::get('/rooms', [RoomController::class, 'index']); // Public read
        Route::post('/rooms', [RoomController::class, 'store'])->middleware('role:superadmin');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->middleware('role:superadmin');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->middleware('role:superadmin');

        // Reservations Routes - Peminjam can submit reservations
        Route::get('/reservations/available', [ReservationController::class, 'index']); // Get available rooms
        Route::get('/reservations/history', [ReservationController::class, 'history']); // User's reservation history (MUST be before {id} routes)
        Route::get('/reservations/{reservation}/letter/pdf', [ReservationController::class, 'downloadLetterPDF']); // Download permission letter PDF
        Route::post('/reservations', [ReservationController::class, 'store'])->middleware('role:peminjam'); // Submit reservation

        // Approval Routes - Only Admin_Fakultas can approve/reject
        Route::get('/approvals/pending', [ApprovalController::class, 'pending'])->middleware('role:admin_fakultas');
        Route::put('/approvals/{reservation}/approve', [ApprovalController::class, 'approve'])->middleware('role:admin_fakultas');
        Route::put('/approvals/{reservation}/reject', [ApprovalController::class, 'reject'])->middleware('role:admin_fakultas');
    });
});
