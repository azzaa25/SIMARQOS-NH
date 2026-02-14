<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokArisan extends Model
{
    use HasFactory;

    protected $table = 'kelompok_arisan';
    protected $primaryKey = 'id_kelompok';
    
    protected $fillable = [
        'nama_kelompok',
        'id_ketua_peserta',
        'kode_kelompok',
        'status_kelompok'
    ];

    // Relasi: Satu kelompok memiliki banyak peserta
    public function anggota()
    {
        return $this->hasMany(PesertaArisan::class, 'id_kelompok');
    }

    // Relasi: Mengetahui siapa ketua kelompoknya
    public function ketua()
    {
        return $this->belongsTo(PesertaArisan::class, 'id_ketua_peserta');
    }
}