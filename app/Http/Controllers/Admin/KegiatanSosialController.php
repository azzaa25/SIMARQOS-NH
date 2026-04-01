<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriSosial;
use App\Models\KegiatanSosial;
use App\Models\DanaSosial;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class KegiatanSosialController extends Controller
{

    /*
    =============================
    AUTO UPDATE STATUS KEGIATAN
    =============================
    */
    private function updateStatusKegiatan()
    {
        // kegiatan yang sudah lewat → selesai
        KegiatanSosial::where('tanggal_kegiatan','<',now())
            ->where('status_kegiatan','!=','selesai')
            ->update([
                'status_kegiatan' => 'selesai'
            ]);

        // kegiatan hari ini → berlangsung
        KegiatanSosial::whereDate('tanggal_kegiatan', now())
            ->update([
                'status_kegiatan' => 'berlangsung'
            ]);

        // kegiatan yang akan datang → rencana
        KegiatanSosial::where('tanggal_kegiatan','>',now())
            ->update([
                'status_kegiatan' => 'rencana'
            ]);
    }


    public function index(Request $request)
    {
        // UPDATE STATUS OTOMATIS
        $this->updateStatusKegiatan();

        $kategori = KategoriSosial::all();
        
        $totalKegiatanSemua = KegiatanSosial::count();

        $query = KegiatanSosial::with('kategori');

        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('id_kategori', $request->kategori);
        }

        $kegiatan = $query->orderBy('tanggal_kegiatan', 'desc')->get();
        
        $totalMasuk = DanaSosial::where('tipe_dana', 'masuk')
                        ->where('status_pembayaran', 'success')
                        ->sum('nominal');

        $totalKeluar = DanaSosial::where('tipe_dana', 'keluar')
                        ->sum('nominal');

        $saldoSosial = $totalMasuk - $totalKeluar;

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

        // 1. Hapus file Pamflet dari storage
        if ($kegiatan->pamflet_kegiatan) {
            Storage::disk('public')->delete($kegiatan->pamflet_kegiatan);
        }

        // 2. Hapus semua file Dokumentasi dari storage
        if ($kegiatan->dokumentasi && is_array($kegiatan->dokumentasi)) {
            foreach ($kegiatan->dokumentasi as $foto) {
                Storage::disk('public')->delete($foto);
            }
        }

        // 3. Hapus data dari database
        $kegiatan->delete();

        return redirect()->back()->with('success', 'Agenda dan seluruh berkas berhasil dihapus');
    }

    /*
    =============================
    UPLOAD DOKUMENTASI (NEW)
    =============================
    */
    public function uploadDokumentasi(Request $request, $id)
    {
        $request->validate([
            'dokumentasi.*' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $kegiatan = KegiatanSosial::findOrFail($id);
        
        // Pastikan $images selalu array, ambil dari DB atau buat array kosong
        $images = is_array($kegiatan->dokumentasi) ? $kegiatan->dokumentasi : []; 

        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $path = $file->store('dokumentasi_kegiatan', 'public');
                $images[] = $path;
            }
        }

        $kegiatan->update([
            'dokumentasi' => $images
        ]);

        return redirect()->back()->with('success', 'Foto dokumentasi berhasil ditambahkan!');
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
            'nama_donatur' => 'Admin',
            'tipe_dana' => 'keluar',
            'nominal' => $request->nominal,
            'metode_pembayaran' => 'manual_transfer',
            'status_pembayaran' => 'success',
            'keterangan_transaksi' => $request->keterangan
        ]);

        return redirect()->back()
            ->with('success', 'Pencairan dana berhasil dicatat!');
    }


    public function listPublik(Request $request)
    {
        // UPDATE STATUS OTOMATIS
        $this->updateStatusKegiatan();

        $categories = KategoriSosial::all();

        $query = KegiatanSosial::with('kategori');

        if ($request->has('kategori')) {
            $query->whereHas('kategori', function($q) use ($request) {
                $q->where('slug', $request->kategori);
            });
        }

        $kegiatan = $query->latest()->paginate(9);

        return view('sosial.publik', compact('kegiatan', 'categories'));
    }


    /*
    =============================
    LAPORAN KEGIATAN SOSIAL
    =============================
    */
    public function laporan()
    {
        $laporanKategori = KategoriSosial::withCount('kegiatan')
            ->get();

        $kegiatanSelesai = KegiatanSosial::with('kategori')
            ->where('tanggal_kegiatan', '<', now())
            ->orderBy('tanggal_kegiatan', 'desc')
            ->get();

        $totalDanaMasuk = DanaSosial::where('tipe_dana', 'masuk')
            ->where('status_pembayaran', 'success')
            ->sum('nominal');

        $totalDanaKeluar = DanaSosial::where('tipe_dana', 'keluar')
            ->sum('nominal');

        return view('admin.sosial.laporan', compact(
            'laporanKategori',
            'kegiatanSelesai',
            'totalDanaMasuk',
            'totalDanaKeluar'
        ));
    }


    /*
    =============================
    EXPORT PDF LAPORAN SPESIFIK
    =============================
    */
    public function exportPdf($id)
    {
        // 1. Ambil data kegiatan tunggal berdasarkan ID
        $kegiatan = KegiatanSosial::with('kategori')->findOrFail($id);

        // 2. Ambil rincian mutasi dana khusus untuk kegiatan ini saja
        $rincianDana = DanaSosial::where('id_kegiatan', $id)
                        ->orderBy('tanggal_input', 'desc')
                        ->get();

        // 3. Hitung total masuk & keluar khusus kegiatan ini
        // Menggunakan accessor yang sudah ada di Model KegiatanSosial
        $totalDanaMasuk = $kegiatan->total_masuk; 
        $totalDanaKeluar = $kegiatan->total_keluar;

        // 4. Generate PDF menggunakan view tunggal
        // Catatan: Pastikan nama view sesuai, di sini saya gunakan 'admin.sosial.laporan_pdf'
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'admin.sosial.laporan_pdf',
            compact(
                'kegiatan',
                'rincianDana',
                'totalDanaMasuk',
                'totalDanaKeluar'
            )
        );

        $pdf->setPaper('a4', 'portrait');

        // Penamaan file otomatis berdasarkan nama kegiatan
        $fileName = 'Laporan-' . \Str::slug($kegiatan->nama_kegiatan) . '-' . date('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }

}