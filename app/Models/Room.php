<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama_ruangan', 'kapasitas', 'fasilitas', 'status_aktif'])]
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
}
