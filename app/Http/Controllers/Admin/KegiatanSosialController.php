<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriSosial;
use App\Models\KegiatanSosial;
use App\Models\DanaSosial;
use Illuminate\Support\Facades\DB;

class KegiatanSosialController extends Controller
{
    public function index(Request $request)
    {
        $kategori = KategoriSosial::all();
        
        // 1. TAMBAHKAN INI: Ambil total semua kegiatan tanpa filter untuk Info Card
        $totalKegiatanSemua = KegiatanSosial::count();

        // 2. Query untuk tabel (Bisa difilter)
        $query = KegiatanSosial::with('kategori');

        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('id_kategori', $request->kategori);
        }

        $kegiatan = $query->orderBy('tanggal_kegiatan', 'desc')->get();
        
        // Statistik Keuangan (Tetap Global)
        $totalMasuk = DanaSosial::where('tipe_dana', 'masuk')
                        ->where('status_pembayaran', 'success')
                        ->sum('nominal');

        $totalKeluar = DanaSosial::where('tipe_dana', 'keluar')
                        ->sum('nominal');

        $saldoSosial = $totalMasuk - $totalKeluar;

        // 3. Masukkan 'totalKegiatanSemua' ke dalam compact
        return view('admin.sosial.index', compact(
            'kategori',
            'kegiatan',
            'saldoSosial',
            'totalKeluar',
            'totalKegiatanSemua'
        ));
    }

    /*
    =============================
    SIMPAN KATEGORI SOSIAL
    =============================
    */
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100'
        ]);

        KategoriSosial::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    /*
    =============================
    SIMPAN AGENDA KEGIATAN
    =============================
    */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan'    => 'required|string|max:255',
            'id_kategori'      => 'required|exists:kategori_sosial,id_kategori',
            'tanggal_kegiatan' => 'required|date',
            'lokasi'           => 'required|string|max:255',
            'target_donasi'    => 'required|numeric|min:0',
            'pamflet_kegiatan' => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',
            'deskripsi_kegiatan' => 'nullable|string'
        ]);

        try {

            $data = $request->only([
                'nama_kegiatan',
                'id_kategori',
                'tanggal_kegiatan',
                'lokasi',
                'target_donasi',
                'deskripsi_kegiatan'
            ]);

            $data['status_kegiatan'] = 'rencana';

            if ($request->hasFile('pamflet_kegiatan')) {
                $data['pamflet_kegiatan'] = $request->file('pamflet_kegiatan')
                    ->store('pamflet_kegiatan', 'public');
            }

            KegiatanSosial::create($data);

            return redirect()->back()
                ->with('success', 'Agenda berhasil dipublikasikan!');
        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'Gagal menyimpan agenda: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string',
            'id_kategori' => 'required',
            'tanggal_kegiatan' => 'required|date',
            'lokasi' => 'required|string',
            'target_donasi' => 'required|numeric'
        ]);

        $kegiatan = KegiatanSosial::findOrFail($id);

        $data = $request->all();

        if ($request->hasFile('pamflet_kegiatan')) {
            $data['pamflet_kegiatan'] = $request->file('pamflet_kegiatan')
                ->store('pamflet_kegiatan','public');
        }

        $kegiatan->update($data);

        return redirect()->back()->with('success','Agenda berhasil diupdate');
    }
    public function destroy($id)
    {
        $kegiatan = KegiatanSosial::findOrFail($id);

        $kegiatan->delete();

        return redirect()->back()->with('success','Agenda berhasil dihapus');
    }

    /*
    =============================
    INPUT DONASI MANUAL
    =============================
    */
    public function storeDanaMasuk(Request $request)
    {
        $request->validate([
            'id_kegiatan' => 'required|exists:kegiatan_sosial,id_kegiatan',
            'nominal' => 'required|numeric|min:1000',
            'keterangan' => 'nullable|string'
        ]);

        DanaSosial::create([
            'id_kegiatan' => $request->id_kegiatan,
            'tipe_dana' => 'masuk',
            'nominal' => $request->nominal,
            'status_pembayaran' => 'success',
            'keterangan_transaksi' => $request->keterangan
        ]);

        return redirect()->back()->with('success', 'Donasi berhasil dicatat!');
    }

    /*
    =============================
    PENCAIRAN DANA
    =============================
    */
    public function cairkanDana(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string'
        ]);

        $kegiatan = KegiatanSosial::findOrFail($id);

        $saldoTersedia = $kegiatan->total_masuk - $kegiatan->total_keluar;

        if ($request->nominal > $saldoTersedia) {
            return redirect()->back()
                ->with('error', 'Dana yang dicairkan melebihi saldo kegiatan!');
        }

        DanaSosial::create([
            'id_kegiatan' => $id,
            'tipe_dana' => 'keluar',
            'nominal' => $request->nominal,
            'status_pembayaran' => 'success',
            'keterangan_transaksi' => $request->keterangan
        ]);

        return redirect()->back()
            ->with('success', 'Pencairan dana berhasil dicatat!');
    }
    public function listPublik(Request $request)
    {
        // Ambil semua kategori untuk filter di sidebar/atas
        $categories = KategoriSosial::all();

        // Ambil kegiatan yang sedang aktif (bisa difilter berdasarkan kategori jika ada request)
        $query = KegiatanSosial::with('kategori');

        if ($request->has('kategori')) {
            $query->whereHas('kategori', function($q) use ($request) {
                $q->where('slug', $request->kategori); // pastikan tabel kategori punya kolom slug
            });
        }

        $kegiatan = $query->latest()->paginate(9);

        return view('sosial.publik', compact('kegiatan', 'categories'));
    }
}