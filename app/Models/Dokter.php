<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang digunakan
    protected $table = 'dokter';

    // Menentukan atribut yang dapat diisi (mass assignment)
    protected $fillable = ['nama_dokter', 'poliklinik_id', 'foto_dokter'];

    // Relasi: Dokter milik satu Poliklinik (Many to One)
    public function poliklinik()
    {
        return $this->belongsTo(Poliklinik::class, 'poliklinik_id');
    }
}

