<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UndianArisan extends Model
{
    use HasFactory;

    protected $table = 'undian_arisan';
    protected $primaryKey = 'id_undian';
    protected $fillable = [
        'id_skema',
        'id_pesertaarisan',
        'tahun_ke',
        'urutan_pemenang',
        'tanggal_undian',
        'status_undian'
    ];

    // Relasi ke Skema
    public function skema() {
        return $this->belongsTo(SkemaArisan::class, 'id_skema');
    }

    // Relasi ke Peserta
    public function peserta() {
        return $this->belongsTo(PesertaArisan::class, 'id_pesertaarisan');
    }
}