<?php

namespace App\Http\Controllers\Api;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use TCPDF;

/**
 * @group Approval Management
 * API endpoints for managing reservation approvals (requires admin_fakultas role)
 */
class ApprovalController extends Controller
{
    /**
     * List Pending Reservations
     *
     * Retrieve all pending reservations awaiting approval. Requires admin_fakultas role.
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Pending reservations retrieved successfully",
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
    public function pending(Request $request)
    {
        $user = $request->user();
        $query = Reservation::where('status_approval', 'pending')->with(['user', 'room']);

        if ($user->role === 'admin_fakultas') {
            $query->whereHas('room', function ($q) use ($user) {
                $q->where('tingkat', 'fakultas')
                  ->where('fakultas_id', $user->fakultas_id);
            });
        } elseif (in_array($user->role, ['admin_universitas', 'admin_kemahasiswaan'])) {
            $assignedRoomIds = $user->rooms()->pluck('rooms.id')->toArray();
            $query->where('approver_role', $user->role)
                  ->whereIn('room_id', $assignedRoomIds);
        } elseif ($user->role === 'admin_bisnis') {
            $query->where('approver_role', 'admin_bisnis');
        } elseif (in_array($user->role, ['super_admin', 'superadmin'])) {
            // Super admin can see all pending
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized role to view pending approvals',
                'data' => null,
            ], 403);
        }

        $reservations = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Pending reservations retrieved successfully',
            'data' => $reservations,
        ]);
    }

    /**
     * Approve Reservation
     *
     * Approve a pending reservation and automatically generate permission letter PDF. Requires admin_fakultas role.
     *
     * @authenticated
     * @bodyParam nama_penyetuju string required Name of the approver. Example: Dr. Kadir A. Muin
     * @bodyParam jabatan_penyetuju string required Title/position of the approver. Example: Wakil Rektor Bidang Akademik
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Reservation approved successfully and letter generated",
     *   "data": {
     *     "id": 1,
     *     "user_id": 3,
     *     "room_id": 1,
     *     "tanggal_mulai": "2026-06-05T10:00:00.000000Z",
     *     "tanggal_selesai": "2026-06-05T12:00:00.000000Z",
     *     "tujuan": "Seminar Nasional",
     *     "file_surat": null,
     *     "status_approval": "approved",
     *     "nomor_surat": "IZIN-001-2026",
     *     "tanggal_persetujuan": "2026-06-01T15:30:00.000000Z",
     *     "nama_penyetuju": "Dr. Kadir A. Muin",
     *     "jabatan_penyetuju": "Wakil Rektor Bidang Akademik",
     *     "file_surat_pdf": "letters/izin-001-2026.pdf",
     *     "created_at": "2026-05-31T00:00:00.000000Z",
     *     "updated_at": "2026-06-01T15:30:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "success": false,
     *   "message": "Reservation not found or already processed",
     *   "data": null
     * }
     *
     * @response 422 {
     *   "success": false,
     *   "message": "Validation error",
     *   "errors": {
     *     "nama_penyetuju": ["The nama penyetuju field is required."]
     *   }
     * }
     */
    public function approve(Request $request, Reservation $reservation)
    {
        $user = $request->user();
        
        // Authorization check
        $isAuthorized = false;
        if (in_array($user->role, ['super_admin', 'superadmin'])) {
            $isAuthorized = true;
        } elseif (in_array($user->role, ['admin_fakultas', 'admin_universitas', 'admin_kemahasiswaan'])) {
            $assignedRoomIds = $user->rooms()->pluck('rooms.id')->toArray();
            $isAuthorized = ($reservation->approver_role === $user->role && in_array($reservation->room_id, $assignedRoomIds));
        } elseif ($user->role === 'admin_bisnis') {
            $isAuthorized = ($reservation->approver_role === 'admin_bisnis');
        }

        if (!$isAuthorized) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to approve this reservation',
                'data' => null,
            ], 403);
        }

        if ($reservation->status_approval !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Reservation is not pending',
                'data' => null,
            ], 422);
        }

        $validated = $request->validate([
            'nama_penyetuju' => 'required|string',
            'jabatan_penyetuju' => 'required|string',
        ]);

        try {
            // Generate letter number
            $nomor_surat = 'IZIN-' . str_pad($reservation->id, 3, '0', STR_PAD_LEFT) . '-' . date('Y');
            $tanggal_persetujuan = now();

            // Update reservation with approval info
            $reservation->update([
                'status_approval' => 'approved',
                'nomor_surat' => $nomor_surat,
                'tanggal_persetujuan' => $tanggal_persetujuan,
                'nama_penyetuju' => $validated['nama_penyetuju'],
                'jabatan_penyetuju' => $validated['jabatan_penyetuju'],
            ]);

            // Generate PDF
            $pdfPath = $this->generatePermissionLetterPDF($reservation);

            // Save PDF path to reservation
            $reservation->update(['file_surat_pdf' => $pdfPath]);

            return response()->json([
                'success' => true,
                'message' => 'Reservation approved successfully and letter generated',
                'data' => $reservation->fresh()->load(['user', 'room']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating letter PDF: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Generate Permission Letter PDF
     *
     * Helper method to generate permission letter as PDF using TCPDF
     */
    private function generatePermissionLetterPDF(Reservation $reservation)
    {
        // Create directory if not exists
        $dirPath = 'letters';
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }

        // Render blade template to HTML string
        $html = view('letters.permission-letter', compact('reservation'))->render();

        // Create PDF
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Sistem Reservasi Ruangan');
        $pdf->SetAuthor('UIN Sunan Gunung Djati Bandung');
        $pdf->SetTitle('Surat Izin Penggunaan Ruangan');
        
        // 1. Matikan Header dan Footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // 2. Hapus sisa ruang default header/footer bawaan TCPDF
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);

        // 3. Set Margins (Kiri: 20mm, Atas: 0mm, Kanan: 20mm)
        $pdf->SetMargins(20, 0, 20);
        
        // 4. Set Auto Page Break (True, Margin Bawah: 15mm)
        $pdf->SetAutoPageBreak(TRUE, 15);

        // Tambahkan halaman baru (akan otomatis menggunakan setting margin di atas)
        $pdf->AddPage();
        
        // Opsional: Pastikan kursor mulai tepat di titik Y=0
        $pdf->SetY(0);

        // Tulis HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Save to storage
        $fileName = strtolower(str_replace(['/', '-', ' '], '', $reservation->nomor_surat)) . '.pdf';
        $filePath = $dirPath . '/' . $fileName;
        $pdf->Output(storage_path('app/' . $filePath), 'F');

        return $filePath;
    }

    /**
     * Reject Reservation
     *
     * Reject a pending reservation with optional reason. Requires admin_fakultas role.
     *
     * @authenticated
     * @bodyParam alasan_penolakan string required The reason for rejection. Example: Ruangan sudah dipesan lebih dulu
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Reservation rejected successfully",
     *   "data": {
     *     "id": 1,
     *     "user_id": 3,
     *     "room_id": 1,
     *     "tanggal_mulai": "2026-06-05T10:00:00.000000Z",
     *     "tanggal_selesai": "2026-06-05T12:00:00.000000Z",
     *     "tujuan": "Seminar Nasional",
     *     "file_surat": null,
     *     "status_approval": "rejected",
     *     "alasan_penolakan": "Ruangan sudah dipesan lebih dulu",
     *     "created_at": "2026-05-31T00:00:00.000000Z",
     *     "updated_at": "2026-05-31T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "success": false,
     *   "message": "Reservation is not pending",
     *   "data": null
     * }
     */
    public function reject(Request $request, Reservation $reservation)
    {
        $user = $request->user();
        
        // Authorization check
        $isAuthorized = false;
        if (in_array($user->role, ['super_admin', 'superadmin'])) {
            $isAuthorized = true;
        } elseif (in_array($user->role, ['admin_fakultas', 'admin_universitas', 'admin_kemahasiswaan'])) {
            $assignedRoomIds = $user->rooms()->pluck('rooms.id')->toArray();
            $isAuthorized = ($reservation->approver_role === $user->role && in_array($reservation->room_id, $assignedRoomIds));
        } elseif ($user->role === 'admin_bisnis') {
            $isAuthorized = ($reservation->approver_role === 'admin_bisnis');
        }

        if (!$isAuthorized) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to reject this reservation',
                'data' => null,
            ], 403);
        }

        if ($reservation->status_approval !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Reservation is not pending',
                'data' => null,
            ], 422);
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string',
        ]);

        $reservation->update([
            'status_approval' => 'rejected',
            'alasan_penolakan' => $validated['alasan_penolakan'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reservation rejected successfully',
            'data' => $reservation,
        ]);
    }
}
