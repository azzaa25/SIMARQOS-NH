<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriSosial;
use App\Models\KegiatanSosial;
use App\Models\DanaSosial;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
        // 1. UPDATE STATUS OTOMATIS
        $this->updateStatusKegiatan();

        // 2. AMBIL DATA KATEGORI UNTUK FILTER
        $kategori = KategoriSosial::all();
        
        // 3. HITUNG STATISTIK RINGKAS
        $totalKegiatanSemua = KegiatanSosial::count();

        $totalMasuk = DanaSosial::where('tipe_dana', 'masuk')
                        ->where('status_pembayaran', 'success')
                        ->sum('nominal');

        $totalKeluar = DanaSosial::where('tipe_dana', 'keluar')
                        ->sum('nominal');

        $saldoSosial = $totalMasuk - $totalKeluar;

        // 4. QUERY KEGIATAN DENGAN FILTER
        $query = KegiatanSosial::with('kategori');

        // Filter Berdasarkan Kategori (Dropdown)
        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        // Filter Berdasarkan Status (Tabs: rencana, berlangsung, selesai)
        if ($request->filled('status')) {
            $query->where('status_kegiatan', $request->status);
        }

        // 5. EKSEKUSI DENGAN PAGINATION (Agar firstItem() tidak error)
        $kegiatan = $query->orderBy('tanggal_kegiatan', 'desc')->paginate(5);

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

            $kegiatan = KegiatanSosial::create($data);
            // Kirim Notifikasi ke WA Group
            $this->kirimNotifKegiatan($kegiatan);

            return redirect()->back()
                ->with('success', 'Agenda berhasil dipublikasikan!');
        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'Gagal menyimpan agenda: ' . $e->getMessage());
        }
    }

    private function kirimNotifKegiatan($kegiatan)
    {
        // Susun Tanggal Indonesia
        $hari = \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->translatedFormat('l');
        $tanggal = \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->translatedFormat('d M Y');

        // Link Ngrok kamu
        $urlWebsite = "https://refutable-supportable-sherlyn.ngrok-free.dev/#sosial";

        // Susun Pesan sesuai format gambar yang kamu kirim tadi
        $pesan = "❗ *ANNOUNCEMENT* ❗\n\n";
        $pesan .= "@cahrwdua Proudly Present 🕌😇💫\n";
        $pesan .= "--------------------------------------------------\n\n";
        
        $pesan .= "Acara : *" . strtoupper($kegiatan->nama_kegiatan) . "*\n";
        $pesan .= "Pada : " . $hari . ", " . $tanggal . "\n";
        $pesan .= "Bertempat Di : " . $kegiatan->lokasi . "\n";
        $pesan .= "Target Donasi: Rp " . number_format($kegiatan->target_donasi, 0, ',', '.') . "\n\n";
        
        
        if ($kegiatan->deskripsi_kegiatan) {
            $pesan .= "📝 *Keterangan:*\n";
            $pesan .= $kegiatan->deskripsi_kegiatan . "\n\n";
        }

        $pesan .= "🤝 *INGIN BERDONASI?*\n";
        $pesan .= "Salurkan bantuan terbaik Bapak/Ibu melalui website resmi kami:\n";
        $pesan .= "🌐 " . $urlWebsite . "\n\n";
        $pesan .= "Terima kasih atas partisipasi dan kerja samanya. Semoga kegiatan berjalan lancar. 🙏\n\n";
        $pesan .= "_Pesan otomatis dari Sistem Masjid Nurul Huda_";

        try {
            // Langsung kirim pesan teks
            $response = Http::post(env('WA_BOT_URL'), [
                'target' => env('WA_GROUP_ID'),
                'message' => $pesan
            ]);

            // Cek Log jika pengiriman ke API Bot gagal
            if ($response->failed()) {
                \Log::error("Bot WA Gagal Kirim: " . $response->body());
            }

        } catch (\Exception $e) {
            \Log::error("Koneksi ke Bot WA Bermasalah: " . $e->getMessage());
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

        // CEK APAKAH SUDAH ADA DONASI MASUK
        if ($kegiatan->total_masuk > 0) {
            return redirect()->back()->with('error', 'Gagal menghapus! Kegiatan ini sudah memiliki donasi yang masuk. Anda hanya dapat mengubah status menjadi selesai atau mengeditnya.');
        }
        // Jika belum ada donasi, proses hapus berkas dan data
        if ($kegiatan->pamflet_kegiatan) {
            Storage::disk('public')->delete($kegiatan->pamflet_kegiatan);
        }
        if ($kegiatan->dokumentasi && is_array($kegiatan->dokumentasi)) {
            foreach ($kegiatan->dokumentasi as $foto) {
                Storage::disk('public')->delete($foto);
            }
        }
        $kegiatan->delete();
        return redirect()->back()->with('success', 'Agenda berhasil dihapus karena belum ada transaksi masuk.');
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
    PENCAIRAN DANA (UPDATE LOGIC)
    =============================
    */
    public function cairkanDana(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string'
        ]);

        $kegiatan = KegiatanSosial::findOrFail($id);

        // 1. CEK STATUS KEGIATAN
        // Jika masih 'rencana', dilarang melakukan pencairan
        if ($kegiatan->status_kegiatan === 'rencana') {
            return redirect()->back()
                ->with('error', 'Gagal! Dana tidak dapat dicairkan jika status kegiatan masih RENCANA. Pencairan hanya dapat dilakukan saat kegiatan BERLANGSUNG atau SELESAI.');
        }

        // 2. CEK SALDO (Logika yang sudah ada)
        $saldoTersedia = $kegiatan->total_masuk - $kegiatan->total_keluar;
        
        if ($saldoTersedia <= 0) {
            return redirect()->back()
                ->with('error', 'Gagal! Saldo kegiatan ini sudah Rp 0 atau sudah habis ditarik.');
        }
        
        if ($request->nominal > $saldoTersedia) {
            return redirect()->back()
                ->with('error', 'Dana yang dicairkan melebihi saldo kegiatan!');
        }

        // 3. EKSEKUSI PENCAIRAN
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
    public function laporan(Request $request) 
    {
        $totalDanaMasuk = DanaSosial::where('tipe_dana', 'masuk')
                            ->where('status_pembayaran', 'success')
                            ->sum('nominal');

        $totalDanaKeluar = DanaSosial::where('tipe_dana', 'keluar')
                            ->sum('nominal');

        $query = KegiatanSosial::with('kategori');

        if ($request->filled('status')) {
            $query->where('status_kegiatan', $request->status);
        }

        $kegiatanSelesai = $query->orderBy('tanggal_kegiatan', 'desc')->paginate(4)->withQueryString();

        return view('admin.sosial.laporan', compact(
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
    public function show($id)
    {
        // Ambil data kegiatan beserta kategorinya
        $item = KegiatanSosial::with('kategori')->findOrFail($id);

        // Ambil daftar donatur yang pembayarannya sudah sukses (settlement/success)
        // Diasumsikan nama tabel dana sosial kamu adalah dana_sosials
        $donatur = DanaSosial::where('id_kegiatan', $id)
                    ->where('tipe_dana', 'masuk')
                    ->whereIn('status_pembayaran', ['success', 'settlement'])
                    ->orderBy('tanggal_input', 'desc')
                    ->get();

        return view('admin.sosial.show', compact('item', 'donatur'));
    }

}