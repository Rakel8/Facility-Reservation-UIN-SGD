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
            'status_aktif' => 'nullable',
            'tingkat' => 'required|string|in:fakultas,universitas,kemahasiswaan',
            'fakultas_id' => 'nullable|exists:faculties,id|required_if:tingkat,fakultas',
            'deskripsi' => 'nullable|string',
            'pic_nama' => 'nullable|string|max:255',
            'pic_telepon' => 'nullable|string|max:255',
            'eksternal_diizinkan' => 'nullable',
        ]);

        $images = [];
        for ($i = 1; $i <= 3; $i++) {
            $fieldName = "gambar_{$i}";
            if ($request->hasFile($fieldName)) {
                $request->validate([
                    $fieldName => 'file|image|max:2048'
                ]);
                $path = $request->file($fieldName)->store('rooms', 'public');
                $images[] = '/storage/' . $path;
            } else {
                $val = $request->input($fieldName);
                if ($val && is_string($val) && $val !== 'null' && $val !== 'undefined') {
                    $images[] = $this->convertGoogleDriveLink($val);
                }
            }
        }

        if (!empty($images)) {
            $validated['gambar'] = json_encode($images);
        } else {
            $validated['gambar'] = null;
        }

        $validated['status_aktif'] = filter_var($request->input('status_aktif', true), FILTER_VALIDATE_BOOLEAN);
        $validated['eksternal_diizinkan'] = filter_var($request->input('eksternal_diizinkan', true), FILTER_VALIDATE_BOOLEAN);

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
            'status_aktif' => 'nullable',
            'tingkat' => 'string|in:fakultas,universitas,kemahasiswaan',
            'fakultas_id' => 'nullable|exists:faculties,id|required_if:tingkat,fakultas',
            'deskripsi' => 'nullable|string',
            'pic_nama' => 'nullable|string|max:255',
            'pic_telepon' => 'nullable|string|max:255',
            'eksternal_diizinkan' => 'nullable',
        ]);

        $images = [];
        $hasNewImages = false;

        for ($i = 1; $i <= 3; $i++) {
            $fieldName = "gambar_{$i}";
            if ($request->hasFile($fieldName)) {
                $request->validate([
                    $fieldName => 'file|image|max:2048'
                ]);
                $path = $request->file($fieldName)->store('rooms', 'public');
                $images[] = '/storage/' . $path;
                $hasNewImages = true;
            } else {
                $existing = $request->input($fieldName);
                if ($existing && is_string($existing) && $existing !== 'null' && $existing !== 'undefined') {
                    $images[] = $this->convertGoogleDriveLink($existing);
                }
            }
        }

        // Only update 'gambar' field if user sent any image updates (either new files or existing urls)
        // or if they explicitly cleared images. If none of the fields were present, keep existing.
        $hasAnyImageField = $request->has('gambar_1') || $request->has('gambar_2') || $request->has('gambar_3') ||
                            $request->hasFile('gambar_1') || $request->hasFile('gambar_2') || $request->hasFile('gambar_3');

        if ($hasAnyImageField) {
            if (!empty($images)) {
                $validated['gambar'] = json_encode($images);
            } else {
                $validated['gambar'] = null;
            }
        }

        if ($request->has('status_aktif')) {
            $validated['status_aktif'] = filter_var($request->input('status_aktif'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('eksternal_diizinkan')) {
            $validated['eksternal_diizinkan'] = filter_var($request->input('eksternal_diizinkan'), FILTER_VALIDATE_BOOLEAN);
        }

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

    /**
     * Convert Google Drive sharing link to a direct rendering URL.
     *
     * @param string $url
     * @return string
     */
    private function convertGoogleDriveLink(string $url): string
    {
        if (preg_match('/drive\.google\.com/', $url)) {
            // Pattern 1: https://drive.google.com/file/d/{FILE_ID}/view...
            if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return "https://lh3.googleusercontent.com/d/" . $matches[1];
            }
            // Pattern 2: https://drive.google.com/open?id={FILE_ID} or /uc?id={FILE_ID}
            if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return "https://lh3.googleusercontent.com/d/" . $matches[1];
            }
        }
        return $url;
    }

    /**
     * Image Proxy to bypass CORS / ORB blocks on Google Drive content
     */
    public function imageProxy(Request $request)
    {
        $id = $request->query('id');
        if (!$id || !preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
            return response('Invalid Image ID', 400);
        }

        $url = "https://lh3.googleusercontent.com/d/" . $id;

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
            $data = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            if ($statusCode === 200 && str_starts_with($contentType, 'image/')) {
                return response($data)->header('Content-Type', $contentType);
            }
        } catch (\Exception $e) {}

        // Fallback to placeholder image
        return redirect('https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=600&q=80');
    }
}
