<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengeluaranArisan;
use App\Models\TransaksiPembayaran; 
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PengeluaranArisanController extends Controller
{
    public function index(Request $request)
    {
        $filterTahun = $request->get('tahun');

        // 1. Query Dasar
        $query = PengeluaranArisan::with(['undian.peserta.kelompok', 'undian.skema'])
                ->latest('tanggal_pengeluaran');

        if ($filterTahun) {
            $query->whereYear('tanggal_pengeluaran', $filterTahun);
        }

        // 2. Ambil SEMUA data tanpa pagination untuk perhitungan TOTAL di header grup
        // Agar nominal total grup tidak terpotong halaman
        $allPengeluarans = $query->get();

        // 3. Ambil data dengan pagination untuk tabel utama
        $pengeluarans = $query->paginate(15);

        // Statistik Global (Tetap sama)
        $totalMasuk = TransaksiPembayaran::where('status_pembayaran', 'sukses')->sum('nominal');
        $totalKeluarGlobal = PengeluaranArisan::sum('nominal');
        $saldoKasSaatIni = $totalMasuk - $totalKeluarGlobal;

        // 4. LOGIKA PENTING: Ambil daftar tahun yang ada di database untuk filter
        $tahunDiDatabase = PengeluaranArisan::selectRaw('YEAR(tanggal_pengeluaran) as tahun')
                            ->distinct()
                            ->orderBy('tahun', 'desc')
                            ->pluck('tahun')
                            ->toArray();
        $currentYear = (int)date('Y');
        
        // Gabungkan tahun berjalan, +1, +2 (untuk periode 3 tahun) dan tahun yang ada di DB
        $tahunMendatang = [$currentYear, $currentYear + 1];
        $daftarTahun = array_unique(array_merge($tahunMendatang, $tahunDiDatabase));
        rsort($daftarTahun);

        return view('admin.laporan.index', compact(
            'pengeluarans', 
            'allPengeluarans', // Kirim data lengkap ke view
            'totalKeluarGlobal', 
            'totalMasuk', 
            'saldoKasSaatIni',
            'daftarTahun',
            'filterTahun'
        ));
    }

    public function exportPDF(Request $request)
    {
        $tahunSelected = $request->get('tahun');

        // 1. Ambil Data (Tetap sama, sesuai filter untuk isi tabel)
        $query = PengeluaranArisan::with(['undian.peserta.kelompok', 'undian.skema'])
                ->latest('tanggal_pengeluaran');

        if ($tahunSelected) {
            $query->whereYear('tanggal_pengeluaran', $tahunSelected);
        }

        $allData = $query->get();

        // 2. LOGIKA GROUPING (Tetap sama)
        $dataGrouped = $allData->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->tanggal_pengeluaran)->format('Y');
        })->map(function($yearGroup) {
            return $yearGroup->groupBy(function($item) {
                return $item->undian->skema->nama_skema ?? 'Lainnya';
            });
        });

        // ============================================================
        // 3. PERBAIKAN STATISTIK (Agar Sesuai dengan Web)
        // ============================================================
        
        // Total Masuk Tetap Global (Semua Iuran Sukses)
        $totalMasukGlobal = TransaksiPembayaran::where('status_pembayaran', 'sukses')->sum('nominal');
        
        // Total Keluar di Laporan (Hanya yang sedang dicetak/difilter)
        $totalKeluarFilter = $allData->sum('nominal'); 

        // Total Keluar GLOBAL (Semua pengeluaran dari database tanpa filter tahun)
        // Ini kuncinya agar Sisa Saldo Kas Masjid sama dengan di Web
        $totalKeluarGlobal = PengeluaranArisan::sum('nominal'); 

        // Sisa Saldo Kas Masjid = Total Masuk Global - Total Keluar Global
        $saldoKasRiilSekarang = $totalMasukGlobal - $totalKeluarGlobal;

        // 4. Generate PDF
        $pdf = Pdf::loadView('admin.laporan.pdf', [
            'dataGrouped'   => $dataGrouped,
            'totalMasuk'    => $totalMasukGlobal,
            'totalKeluar'   => $totalKeluarFilter,   // Dana Terealisasi (Tahun Terlampir)
            'saldoAkhir'    => $saldoKasRiilSekarang, // Sisa Saldo Kas Masjid (Global)
            'tahunSelected' => $tahunSelected
        ]);

        $pdf->setPaper('a4', 'portrait');

        $fileName = $tahunSelected ? "laporan-qurban-$tahunSelected.pdf" : "laporan-qurban-semua-tahun.pdf";
        return $pdf->download($fileName);
    }
}