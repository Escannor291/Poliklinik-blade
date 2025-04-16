<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_pasien',
        'jadwalpoliklinik_id',
        'penjamin',
        'scan_surat_rujukan',
    ];
    
    public function jadwalpoliklinik()
    {
        return $this->belongsTo(Jadwalpoliklinik::class, 'jadwalpoliklinik_id');
    }
    
    public function datapasien()
    {
        return $this->belongsTo(Datapasien::class, 'id_pasien');
    }
}
