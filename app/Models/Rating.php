<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'rating';

    protected $fillable = [
        'dokter_id',
        'pasien_id',
        'rating',
        'komentar',
        'tanggal'
    ];

    /**
     * Relationship to dokter
     */
    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    /**
     * Relationship to datapasien
     */
    public function pasien()
    {
        return $this->belongsTo(Datapasien::class, 'pasien_id');
    }
}
