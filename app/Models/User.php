<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // NAMA TABEL
    protected $table = 'users';

    // PRIMARY KEY CUSTOM
    protected $primaryKey = 'id_user';

    // MASS ASSIGNMENT
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role'
    ];

    // DATA YANG DISEMBUNYIKAN
    protected $hidden = [
        'password',
        'remember_token'
    ];

    // CAST (OPSIONAL)
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // RELASI KE PESERTA ARISAN
    public function peserta()
    {
        return $this->hasOne(PesertaArisan::class, 'id_user');
    }
}
