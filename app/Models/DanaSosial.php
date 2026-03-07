<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanaSosial extends Model
{
    use HasFactory;

    protected $table = 'dana_sosial';
    protected $primaryKey = 'id_dana';


    protected $fillable = [
        'order_id',  
        'nama_donatur',         
        'id_kegiatan',        
        'tipe_dana',         
        'nominal',            
        'metode_pembayaran', 
        'status_pembayaran',
        'keterangan_transaksi' 
    ];

    public $timestamps = false;

    public function kegiatan()
    {
        return $this->belongsTo(KegiatanSosial::class, 'id_kegiatan', 'id_kegiatan');
    }
}