<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPembayaran extends Model
{
    protected $table = 'transaksi_pembayaran';
    protected $primaryKey = 'id_transaksi';
    
    protected $fillable = [
        'id_pesertaarisan', 
        'order_id', 
        'nominal', 
        'bulan_iuran', 
        'snap_token', 
        'status_pembayaran', 
        'metode_pembayaran', 
        'is_read'
    ];

    public function peserta()
    {
        return $this->belongsTo(PesertaArisan::class, 'id_pesertaarisan', 'id_pesertaarisan');
    }
}