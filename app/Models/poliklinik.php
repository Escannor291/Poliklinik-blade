<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class poliklinik extends Model
{
    use HasFactory;
    protected $table = 'poliklinik';
    protected $fillable = [
            'nama_poliklinik', 
    ];

    // Add this relationship method
    public function jadwalpoliklinik()
    {
        return $this->hasMany(jadwalpoliklinik::class, 'poliklinik_id');
    }

    // Add dokter relationship
    public function dokter()
    {
        return $this->hasMany(dokter::class, 'poliklinik_id');
    }
}
