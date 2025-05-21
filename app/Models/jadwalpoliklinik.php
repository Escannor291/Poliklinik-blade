<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class jadwalpoliklinik extends Model
{
    protected $table = 'jadwalpoliklinik';
    protected $fillable = ['kode', 'dokter_id', 'poliklinik_id', 'tanggal_praktek', 'jam_mulai', 'jam_selesai', 'jumlah', 'kuota'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode)) {
                // Generate a unique code using timestamp and random string
                $timestamp = now()->format('YmdHis');
                $randomStr = Str::random(4);
                $model->kode = 'JP-' . $timestamp . $randomStr;
            }
        });
    }

    public function dokter()
    {
        return $this->belongsTo(dokter::class, 'dokter_id');
    }

    public function poliklinik()
    {
        return $this->belongsTo(poliklinik::class, 'poliklinik_id');
    }
    
    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'jadwalpoliklinik_id');
    }
}
