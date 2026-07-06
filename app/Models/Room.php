<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama_ruangan', 'kapasitas', 'fasilitas', 'status_aktif', 'tingkat', 'fakultas_id', 'deskripsi', 'gambar', 'pic_nama', 'pic_telepon', 'eksternal_diizinkan'])]
class Room extends Model
{
    use HasFactory;

    /**
     * Get all reservations for this room.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the faculty this room belongs to.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'fakultas_id');
    }

    /**
     * Get the admins assigned to this room (for university tingkat).
     */
    public function admins()
    {
        return $this->belongsToMany(User::class, 'room_user', 'room_id', 'user_id');
    }
}
