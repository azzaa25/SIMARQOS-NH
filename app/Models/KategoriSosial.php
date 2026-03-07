<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriSosial extends Model
{
    protected $table = 'kategori_sosial';
    protected $primaryKey = 'id_kategori';
    
    public $timestamps = false; 

    protected $fillable = ['nama_kategori'];

    public function kegiatan()
    {
        return $this->hasMany(KegiatanSosial::class, 'id_kategori', 'id_kategori');
    }
}