<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranArisan extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_arisan';
    protected $primaryKey = 'id_pengeluaran';

    protected $fillable = [
        'id_undian',
        'order_id',
        'nominal',
        'keterangan',
        'tanggal_pengeluaran'
    ];

    // Relasi: Pengeluaran ini milik satu data undian (pemenang)
    public function undian()
    {
        return $this->belongsTo(UndianArisan::class, 'id_undian');
    }
}