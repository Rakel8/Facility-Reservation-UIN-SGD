<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama_fakultas'])]
class Faculty extends Model
{
    use HasFactory;

    /**
     * Get rooms belonging to this faculty.
     */
    public function rooms()
    {
        return $this->hasMany(Room::class, 'fakultas_id');
    }

    /**
     * Get admins belonging to this faculty.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'fakultas_id');
    }
}
