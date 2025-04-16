<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class antrian extends Model
{
    use HasFactory;

    protected $table = 'antrian';

protected $fillable = [
    'kode_jadwalpoliklinik',
    'kode_antrian',
    'no_antrian',
    'nama_pasien',
    'no_telp',
    'jadwalpoliklinik_id',
    'id_pasien',
    'nama_dokter',
    'poliklinik',
    'penjamin',
    'no_bpjs',
    'scan_bpjs',
    'scan_keaslian',
    'tanggal_berobat',
    'tanggal_reservasi',
    'scan_surat_rujukan',
    'user_id'
];

public function jadwalpoliklinik()
{
    return $this->belongsTo(Jadwalpoliklinik::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

public function datapasien()
{
    return $this->belongsTo(Datapasien::class, 'id_pasien');
}

protected $casts = [
    'tanggal_berobat' => 'date',
    'tanggal_reservasi' => 'datetime',
];
}
