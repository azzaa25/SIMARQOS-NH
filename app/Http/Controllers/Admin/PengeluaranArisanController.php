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
        $filterBulan = $request->get('bulan');
        $filterSkema = $request->get('skema');
        $filterStatus = $request->get('status');

        // 1. Query Utama dengan filter status user (Aktif OR (Nonaktif AND Sudah Menang))
        $query = \App\Models\PesertaArisan::with(['skemaArisan', 'kelompok', 'undian', 'pengeluaranArisan', 'user', 'transaksi' => function($q) {
            $q->where('status_pembayaran', 'sukses');
        }])
        ->where(function($q) {
            // Tampilkan peserta yang USER-nya aktif
            $q->whereHas('user', function($sq) {
                $sq->where('status', 'aktif');
            })
            // ATAU yang USER-nya nonaktif tapi PESERTA-nya sudah menang
            ->orWhere(function($sq) {
                $sq->whereHas('user', function($ssq) {
                    $ssq->where('status', 'nonaktif');
                })->whereHas('pengeluaranArisan');
            });
        });

        // 2. Terapkan Filter Tambahan (Skema, Status Undian, Tahun)
        if ($filterSkema) {
            $query->where('id_skema', $filterSkema);
        }

        if ($filterStatus == 'pemenang') {
            $query->whereHas('pengeluaranArisan');
        } elseif ($filterStatus == 'belum') {
            $query->whereDoesntHave('pengeluaranArisan');
        }

        if ($filterTahun) {
            $query->whereHas('undian', function($q) use ($filterTahun) {
                $q->where('tahun_pelaksanaan', $filterTahun);
            });
        }

        // 3. LOGIKA DINAMIS CARD & DETAIL STATUS
        $statsQuery = clone $query;
        $pesertaTerfilter = $statsQuery->get();

        $totalPesertaFilter = $pesertaTerfilter->count();
        $totalMasukFilter = 0;
        $totalKeluarFilter = 0;
        
        // Variabel detail untuk Card Statistik
        $countAktif = 0;
        $countNonaktif = 0;

        foreach($pesertaTerfilter as $p) {
            // Hitung Keuangan
            $totalMasukFilter += $p->transaksi->sum('nominal');
            if($p->pengeluaranArisan) {
                $totalKeluarFilter += $p->pengeluaranArisan->nominal;
            }

            // Hitung Detail Status (Aktif vs Selesai)
            if($p->user && $p->user->status == 'aktif') {
                $countAktif++;
            } elseif($p->user && $p->user->status == 'nonaktif') {
                $countNonaktif++;
            }
        }

        $saldoKasDinamis = $totalMasukFilter - $totalKeluarFilter;

        // 4. Statistik Global (Sebagai referensi saldo riil kas masjid)
        $totalMasuk = \App\Models\TransaksiPembayaran::where('status_pembayaran', 'sukses')->sum('nominal');
        $totalKeluarGlobal = \App\Models\PengeluaranArisan::sum('nominal');
        $saldoKasSaatIni = $totalMasuk - $totalKeluarGlobal;
        
        // 5. Eksekusi Pagination dan Data Dropdown
        $pesertas = $query->paginate(20)->withQueryString();
        $skemas = \App\Models\SkemaArisan::all();
        $daftarTahun = range(date('Y'), 2023);

        return view('admin.laporan.index', compact(
            'pesertas', 'totalMasuk', 'totalKeluarGlobal', 'totalMasukFilter', 'totalKeluarFilter',
            'saldoKasSaatIni', 'saldoKasDinamis', 'totalPesertaFilter', 'countAktif', 'countNonaktif',
            'skemas', 'daftarTahun', 'filterTahun', 'filterBulan', 'filterSkema', 'filterStatus'
        ));
    }
    public function exportPDF(Request $request)
    {
        $filterTahun = $request->get('tahun');
        $filterSkema = $request->get('skema');
        $filterStatus = $request->get('status');

        // 1. Ambil Query Dasar (SAMA PERSIS DENGAN INDEX)
        $query = \App\Models\PesertaArisan::with(['skemaArisan', 'kelompok', 'undian', 'pengeluaranArisan', 'user', 'transaksi' => function($q) {
            $q->where('status_pembayaran', 'sukses');
        }])
        ->where(function($q) {
            $q->whereHas('user', function($sq) {
                $sq->where('status', 'aktif');
            })
            ->orWhere(function($sq) {
                $sq->whereHas('user', function($ssq) {
                    $ssq->where('status', 'nonaktif');
                })->whereHas('pengeluaranArisan');
            });
        });

        // 2. Terapkan Filter Dinamis
        if ($filterSkema) {
            $query->where('id_skema', $filterSkema);
        }

        if ($filterStatus == 'pemenang') {
            $query->whereHas('pengeluaranArisan');
        } elseif ($filterStatus == 'belum') {
            $query->whereDoesntHave('pengeluaranArisan');
        }

        if ($filterTahun) {
            $query->whereHas('undian', function($q) use ($filterTahun) {
                $q->where('tahun_pelaksanaan', $filterTahun);
            });
        }

        $allData = $query->get();

        // 3. LOGIKA GROUPING UNTUK TABEL PDF
        // Kita kelompokkan berdasarkan Tahun Pelaksanaan (dari undian) dan Nama Skema
        $dataGrouped = $allData->groupBy(function($p) {
            return $p->undian->tahun_pelaksanaan ?? 'Belum Undian';
        })->map(function($yearGroup) {
            return $yearGroup->groupBy(function($p) {
                return $p->skemaArisan->nama_skema ?? 'Umum';
            });
        });

        // 4. STATISTIK GLOBAL (Untuk Saldo Kas Riil)
        $totalMasukGlobal = \App\Models\TransaksiPembayaran::where('status_pembayaran', 'sukses')->sum('nominal');
        $totalKeluarGlobal = \App\Models\PengeluaranArisan::sum('nominal');
        $saldoKasRiilSekarang = $totalMasukGlobal - $totalKeluarGlobal;

        // 5. STATISTIK TERFILTER (Dinamis sesuai yang dicetak)
        $totalMasukFilter = 0;
        $totalKeluarFilter = 0;
        foreach($allData as $p) {
            $totalMasukFilter += $p->transaksi->sum('nominal');
            if($p->pengeluaranArisan) {
                $totalKeluarFilter += $p->pengeluaranArisan->nominal;
            }
        }

        // 6. GENERATE PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.pdf', [
            'dataGrouped'   => $dataGrouped,
            'totalMasuk'    => $totalMasukFilter, // Iuran masuk dari list peserta ini
            'totalKeluar'   => $totalKeluarFilter, // Realisasi dari list peserta ini
            'saldoAkhir'    => $saldoKasRiilSekarang, // Tetap tampilkan saldo kas masjid asli
            'tahunSelected' => $filterTahun,
            'filterStatus'  => $filterStatus
        ]);

        $pdf->setPaper('a4', 'portrait');
        $fileName = "laporan-arisan-" . ($filterTahun ?? 'semua') . ".pdf";
        
        return $pdf->download($fileName);
    }
}