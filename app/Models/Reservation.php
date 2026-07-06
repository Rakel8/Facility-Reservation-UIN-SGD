<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'room_id', 'tanggal_mulai', 'tanggal_selesai', 'tujuan', 'file_surat', 'status_approval', 'alasan_penolakan', 'nomor_surat', 'tanggal_persetujuan', 'nama_penyetuju', 'jabatan_penyetuju', 'file_surat_pdf', 'instansi', 'deskripsi_acara', 'tipe_peminjam', 'approver_role', 'proposal_file'])]
class Reservation extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'tanggal_persetujuan' => 'datetime',
    ];

    /**
     * Boot the model and set auto-increment for nomor_surat
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($reservation) {
            if (!$reservation->nomor_surat && $reservation->status_approval === 'approved') {
                $nomor = 'IZIN-' . str_pad($reservation->id, 3, '0', STR_PAD_LEFT) . '-' . date('Y');
                $reservation->update(['nomor_surat' => $nomor]);
            }
        });
    }

    /**
     * Get the user who made this reservation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room being reserved.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
