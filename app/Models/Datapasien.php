<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Datapasien extends Model
{
    use HasFactory;

    protected $table = 'datapasien';

    protected $fillable = [
        'user_id', // This can now be null
        'nama_pasien', 
        'email', 
        'no_telp', 
        'nik', 
        'tempat_lahir', 
        'tanggal_lahir', 
        'jenis_kelamin', 
        'alamat', 
        'no_kberobat',
        'scan_kberobat',
        'no_bpjs', 
        'scan_bpjs',
        'scan_ktp',
        'scan_asuransi',
    ];

    // Modify the relationship to reflect the nullable user_id
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'nama_user' => 'No User',
            'username' => '-'
        ]);
    }

    // Relationship with Pendaftaran
    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'id_pasien');
    }
}
