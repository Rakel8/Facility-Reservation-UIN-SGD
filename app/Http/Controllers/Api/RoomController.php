<?php

namespace App\Http\Controllers\Api;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * @group Rooms Management
 * API endpoints for managing facility rooms (requires superadmin role for write operations)
 */
class RoomController extends Controller
{
    /**
     * List All Rooms
     *
     * Retrieve all available rooms with their details.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Rooms retrieved successfully",
     *   "data": [
     *     {
     *       "id": 1,
     *       "nama_ruangan": "Aula Abdjan Soelaiman",
     *       "kapasitas": 500,
     *       "fasilitas": "Proyektor, Sound System, AC",
     *       "status_aktif": true,
     *       "created_at": "2026-05-31T00:00:00.000000Z",
     *       "updated_at": "2026-05-31T00:00:00.000000Z"
     *     }
     *   ]
     * }
     */
    public function index()
    {
        $rooms = Room::all();

        return response()->json([
            'success' => true,
            'message' => 'Rooms retrieved successfully',
            'data' => $rooms,
        ]);
    }

    /**
     * Create Room
     *
     * Create a new facility room. Requires superadmin role.
     *
     * @authenticated
     * @bodyParam nama_ruangan string required The room name. Example: Aula Abdjan Soelaiman
     * @bodyParam kapasitas integer required The room capacity. Example: 500
     * @bodyParam fasilitas string required Facilities in the room. Example: Proyektor, Sound System, AC
     * @bodyParam status_aktif boolean The room active status. Example: true
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Room created successfully",
     *   "data": {
     *     "id": 1,
     *     "nama_ruangan": "Aula Abdjan Soelaiman",
     *     "kapasitas": 500,
     *     "fasilitas": "Proyektor, Sound System, AC",
     *     "status_aktif": true,
     *     "created_at": "2026-05-31T00:00:00.000000Z",
     *     "updated_at": "2026-05-31T00:00:00.000000Z"
     *   }
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'fasilitas' => 'required|string',
            'status_aktif' => 'boolean',
        ]);

        $room = Room::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Room created successfully',
            'data' => $room,
        ], 201);
    }

    /**
     * Update Room
     *
     * Update an existing room. Requires superadmin role.
     *
     * @authenticated
     * @bodyParam nama_ruangan string The room name.
     * @bodyParam kapasitas integer The room capacity.
     * @bodyParam fasilitas string Facilities in the room.
     * @bodyParam status_aktif boolean The room active status.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Room updated successfully",
     *   "data": {
     *     "id": 1,
     *     "nama_ruangan": "Aula Abdjan Soelaiman Updated",
     *     "kapasitas": 500,
     *     "fasilitas": "Proyektor, Sound System, AC",
     *     "status_aktif": true,
     *     "created_at": "2026-05-31T00:00:00.000000Z",
     *     "updated_at": "2026-05-31T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "success": false,
     *   "message": "Room not found",
     *   "data": null
     * }
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'nama_ruangan' => 'string|max:255',
            'kapasitas' => 'integer|min:1',
            'fasilitas' => 'string',
            'status_aktif' => 'boolean',
        ]);

        $room->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Room updated successfully',
            'data' => $room,
        ]);
    }

    /**
     * Delete Room
     *
     * Delete a room. Requires superadmin role.
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Room deleted successfully",
     *   "data": null
     * }
     *
     * @response 404 {
     *   "success": false,
     *   "message": "Room not found",
     *   "data": null
     * }
     */
    public function destroy(Room $room)
    {
        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully',
            'data' => null,
        ]);
    }
}
