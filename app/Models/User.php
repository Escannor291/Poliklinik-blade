<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'user';

    protected $fillable = [
        'nama_user', 'username', 'password', 'no_telepon', 'foto_user', 'roles',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function datapasien()
{
    return $this->hasOne(Datapasien::class, 'user_id');
}

protected static function booted()
{
    static::updated(function ($user) {
        if ($user->isDirty('no_telepon')) {
            Datapasien::where('user_id', $user->id)
                ->update(['no_telp' => $user->no_telepon]);
        }
    });
}

}
