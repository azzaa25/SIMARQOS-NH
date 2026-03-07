<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KegiatanSosial extends Model
{
    use HasFactory;

    protected $table = 'kegiatan_sosial';
    protected $primaryKey = 'id_kegiatan';

    protected $fillable = [
        'id_kategori', 
        'nama_kegiatan', 
        'deskripsi_kegiatan', 
        'tanggal_kegiatan', 
        'lokasi', 
        'status_kegiatan', 
        'pamflet_kegiatan',
        'target_donasi' 
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriSosial::class, 'id_kategori', 'id_kategori');
    }

    public function dana()
    {
        return $this->hasMany(DanaSosial::class, 'id_kegiatan', 'id_kegiatan');
    }

    // Hitung total donasi masuk (dari Midtrans/Manual)
    public function getTotalMasukAttribute()
    {
        return $this->dana()->where('tipe_dana', 'masuk')->sum('nominal') ?? 0;
    }

    // Hitung total pengeluaran untuk agenda ini
    public function getTotalKeluarAttribute()
    {
        return $this->dana()->where('tipe_dana', 'keluar')->sum('nominal') ?? 0;
    }

    // Hitung sisa saldo khusus agenda ini
    public function getSaldoAttribute()
    {
        return $this->total_masuk - $this->total_keluar;
    }

    public function getPersentaseDonasiAttribute()
    {
        if ($this->target_donasi <= 0) return 0;
        
        $persen = ($this->total_masuk / $this->target_donasi) * 100;
        return $persen > 100 ? 100 : round($persen, 1);
    }
}