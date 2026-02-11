<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkemaArisan extends Model
{
    protected $table = 'skema_arisan';

    protected $primaryKey = 'id_skema';

    protected $fillable = [
        'nama_skema',
        'durasi_bulan',
        'tipe_skema',
        'nominal_iuran',
        'deskripsi'
    ];
}
