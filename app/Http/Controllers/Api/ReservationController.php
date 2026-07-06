<?php

namespace App\Http\Controllers\Api;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

/**
 * @group Reservations
 * API endpoints for managing facility reservations
 */
class ReservationController extends Controller
{
    /**
     * List Available Rooms
     *
     * Retrieve all active rooms that are available for reservation.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Available rooms retrieved successfully",
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
        $rooms = Room::where('status_aktif', true)->get();

        return response()->json([
            'success' => true,
            'message' => 'Available rooms retrieved successfully',
            'data' => $rooms,
        ]);
    }

    /**
     * Submit Reservation
     *
     * Create a new reservation request. Requires peminjam role.
     *
     * @authenticated
     * @bodyParam room_id integer required The room ID to reserve. Example: 1
     * @bodyParam tanggal_mulai string required Start date and time (ISO 8601). Example: 2026-06-05T10:00:00
     * @bodyParam tanggal_selesai string required End date and time (ISO 8601). Example: 2026-06-05T12:00:00
     * @bodyParam tujuan string required Purpose of reservation. Example: Seminar Nasional
     * @bodyParam file_surat string File attachment path (optional).
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Reservation created successfully",
     *   "data": {
     *     "id": 1,
     *     "user_id": 3,
     *     "room_id": 1,
     *     "tanggal_mulai": "2026-06-05T10:00:00.000000Z",
     *     "tanggal_selesai": "2026-06-05T12:00:00.000000Z",
     *     "tujuan": "Seminar Nasional",
     *     "file_surat": null,
     *     "status_approval": "pending",
     *     "alasan_penolakan": null,
     *     "created_at": "2026-05-31T00:00:00.000000Z",
     *     "updated_at": "2026-05-31T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "success": false,
     *   "message": "Validation failed",
     *   "data": null
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'tanggal_mulai' => 'required|date_format:Y-m-d\TH:i:s',
            'tanggal_selesai' => 'required|date_format:Y-m-d\TH:i:s|after:tanggal_mulai',
            'tujuan' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'deskripsi_acara' => 'required|string',
            'tipe_peminjam' => 'required|string|in:internal,eksternal',
            'proposal_file' => 'nullable|file|mimes:pdf|max:2048', // PDF max 2MB
        ]);

        $room = Room::findOrFail($validated['room_id']);

        // Check if internal-only room is requested by external borrower
        if (!$room->eksternal_diizinkan && $validated['tipe_peminjam'] === 'eksternal') {
            return response()->json([
                'success' => false,
                'message' => 'Aula ini hanya bisa disewa oleh internal kampus',
                'data' => null
            ], 422);
        }

        // Determine approver role based on borrower type, room level, and check-in day
        if ($validated['tipe_peminjam'] === 'eksternal') {
            $approverRole = 'admin_bisnis';
        } elseif ($room->tingkat === 'fakultas') {
            $approverRole = 'admin_fakultas';
        } elseif ($room->tingkat === 'kemahasiswaan') {
            $approverRole = 'admin_kemahasiswaan';
        } else { // universitas
            $startDate = \Carbon\Carbon::parse($validated['tanggal_mulai']);
            $endDate = \Carbon\Carbon::parse($validated['tanggal_selesai']);
            
            // If it covers any weekday, it goes to admin_universitas. If purely weekend, admin_bisnis.
            $hasWeekday = false;
            $tempDate = $startDate->copy();
            while ($tempDate->lte($endDate)) {
                if ($tempDate->isWeekday()) {
                    $hasWeekday = true;
                    break;
                }
                $tempDate->addDay();
            }

            if ($hasWeekday) {
                $approverRole = 'admin_universitas';
            } else {
                $approverRole = 'admin_bisnis';
            }
        }

        // Handle proposal file upload
        if ($request->hasFile('proposal_file')) {
            $path = $request->file('proposal_file')->store('proposals', 'public');
            $validated['proposal_file'] = '/storage/' . $path;
        }

        $validated['user_id'] = $request->user()->id;
        $validated['status_approval'] = 'pending';
        $validated['approver_role'] = $approverRole;

        $reservation = Reservation::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reservation created successfully. Waiting for ' . str_replace('_', ' ', $approverRole) . ' approval.',
            'data' => $reservation,
        ], 201);
    }

    /**
     * User Reservation History
     *
     * Retrieve the authenticated user's reservation history.
     *
     * @authenticated
     * @queryParam status string Filter by approval status (pending, approved, rejected). Optional.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "User reservations retrieved successfully",
     *   "data": [
     *     {
     *       "id": 1,
     *       "user_id": 3,
     *       "room_id": 1,
     *       "tanggal_mulai": "2026-06-05T10:00:00.000000Z",
     *       "tanggal_selesai": "2026-06-05T12:00:00.000000Z",
     *       "tujuan": "Seminar Nasional",
     *       "file_surat": null,
     *       "status_approval": "pending",
     *       "alasan_penolakan": null,
     *       "created_at": "2026-05-31T00:00:00.000000Z",
     *       "updated_at": "2026-05-31T00:00:00.000000Z",
     *       "user": {
     *         "id": 3,
     *         "name": "Mahasiswa Peminjam",
     *         "email": "mahasiswa@uin.ac.id",
     *         "role": "peminjam"
     *       },
     *       "room": {
     *         "id": 1,
     *         "nama_ruangan": "Aula Abdjan Soelaiman",
     *         "kapasitas": 500
     *       }
     *     }
     *   ]
     * }
     */
    public function history(Request $request)
    {
        $user = $request->user();

        // Retrieve only the logged-in user's reservations for privacy
        $query = Reservation::where('user_id', $user->id)->with(['user', 'room']);

        if ($request->has('status')) {
            $query->where('status_approval', $request->status);
        }

        // Order by newest reservations first
        $reservations = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'User reservations retrieved successfully',
            'data' => $reservations,
        ]);
    }

    /**
     * List Active Reservations Schedule
     *
     * Retrieve all active (approved or pending) reservations for scheduling and availability check.
     *
     * @authenticated
     * @response 200 {
     *   "success": true,
     *   "message": "All active reservations retrieved successfully",
     *   "data": [
     *     {
     *       "id": 1,
     *       "user_id": 3,
     *       "room_id": 1,
     *       "tanggal_mulai": "2026-06-05T10:00:00.000000Z",
     *       "tanggal_selesai": "2026-06-05T12:00:00.000000Z",
     *       "tujuan": "Seminar Nasional",
     *       "status_approval": "approved"
     *     }
     *   ]
     * }
     */
    public function schedule()
    {
        $reservations = Reservation::whereIn('status_approval', ['approved', 'pending'])
            ->with(['user', 'room'])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'All active reservations retrieved successfully',
            'data' => $reservations,
        ]);
    }

    /**
     * Download Permission Letter PDF
     *
     * Download the generated permission letter PDF for an approved reservation.
     * Only the reservation owner or admin can download the PDF.
     *
     * @authenticated
     * @response 200 Binary PDF file
     *
     * @response 404 {
     *   "success": false,
     *   "message": "Reservation not found or PDF not available",
     *   "data": null
     * }
     *
     * @response 403 {
     *   "success": false,
     *   "message": "Not authorized to download this file",
     *   "data": null
     * }
     */
    public function downloadLetterPDF(Request $request, Reservation $reservation)
    {
        // Check authorization: user must be the owner or an admin
        if ($request->user()->id !== $reservation->user_id && !in_array($request->user()->role, ['super_admin', 'superadmin', 'admin_fakultas', 'admin_universitas', 'admin_bisnis'])) {
            return response()->json([
                'success' => false,
                'message' => 'Not authorized to download this file',
                'data' => null,
            ], 403);
        }

        // Check if PDF exists
        if (!$reservation->file_surat_pdf || !Storage::exists($reservation->file_surat_pdf)) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found or PDF not available',
                'data' => null,
            ], 404);
        }

        // Download the file
        $fileName = basename($reservation->file_surat_pdf);
        return Storage::download($reservation->file_surat_pdf, $fileName . '_' . $reservation->nomor_surat . '.pdf');
    }

    /**
     * Download Proposal PDF
     */
    public function downloadProposal(Request $request, Reservation $reservation)
    {
        $user = $request->user();
        
        $isOwner = $user->id === $reservation->user_id;
        $isAdmin = in_array($user->role, ['super_admin', 'superadmin', 'admin_fakultas', 'admin_universitas', 'admin_bisnis']);
        
        if (!$isOwner && !$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Not authorized to download this file',
                'data' => null,
            ], 403);
        }

        if (!$reservation->proposal_file) {
            return response()->json([
                'success' => false,
                'message' => 'Proposal file not found',
                'data' => null,
            ], 404);
        }

        $filename = str_replace('/storage/proposals/', 'proposals/', $reservation->proposal_file);
        if (!Storage::disk('public')->exists($filename)) {
            return response()->json([
                'success' => false,
                'message' => 'File does not exist on disk',
                'data' => null,
            ], 404);
        }

        return Storage::disk('public')->download($filename);
    }
}
