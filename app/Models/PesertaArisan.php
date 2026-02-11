<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaArisan extends Model
{
    protected $table = 'peserta_arisan';
    protected $primaryKey = 'id_pesertaarisan';

    protected $fillable = [
        'id_user',
        'id_skema',
        'nama',
        'alamat',
        'no_hp',
        'status'
    ];

    // RELASI KE USERS
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // RELASI KE SKEMA ARISAN
    public function skemaArisan()
    {
        return $this->belongsTo(SkemaArisan::class, 'id_skema');
    }
}
