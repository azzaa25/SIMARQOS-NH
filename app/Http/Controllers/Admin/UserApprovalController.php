<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TransaksiPembayaran;
use App\Models\KelompokArisan;
use App\Models\PesertaArisan;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserApprovalController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'peserta')
                    ->where('status', 'pending')
                    ->with(['peserta.skemaArisan'])
                    ->latest()
                    ->get();

        return view('admin.pending.index', compact('users'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'tahun_periode' => 'required|integer|min:2025'
        ], [
            'tahun_periode.required' => 'Tahun periode pelaksanaan wajib ditentukan!'
        ]);

        $user = User::with('peserta.skemaArisan')->findOrFail($id);
        $peserta = $user->peserta;

        if (!$peserta || !$peserta->skemaArisan) {
            return back()->with('error', 'Data profil atau skema arisan peserta tidak ditemukan.');
        }

        $tahunTarget = (int)$request->tahun_periode;
        $durasiBulan = (int)$peserta->skemaArisan->durasi_bulan;

        // Peta Bulan Idul Adha
        $petaIdulAdha = [
            2026 => 5, 2027 => 5, 2028 => 4, 2029 => 4, 2030 => 3
        ];
        
        $bulanIdulAdha = $petaIdulAdha[$tahunTarget] ?? 5;
        $bulanLunas = $bulanIdulAdha - 1;

        // Hitung rentang waktu iuran (Back-Counting)
        $tanggalJatuhTempoAkhir = Carbon::create($tahunTarget, $bulanLunas, 10);
        $tanggalMulaiTagihan = $tanggalJatuhTempoAkhir->copy()->subMonths($durasiBulan - 1);

        // Aturan Keamanan Siklus
        if ($durasiBulan == 12 && $tahunTarget <= (int)date('Y')) {
            return back()->with('error', "Gagal! Pelaksanaan Qurban tahun {$tahunTarget} untuk skema 1 tahun sudah/sedang berjalan.");
        }

        if (Carbon::now()->startOfMonth()->greaterThan($tanggalMulaiTagihan->copy()->startOfMonth())) {
            return back()->with('error', "Gagal! Siklus arisan periode {$tahunTarget} sudah berjalan sejak bulan " . $tanggalMulaiTagihan->translatedFormat('F Y') . ".");
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Update status user & simpan tahun periode
            $user->update(['status' => 'aktif']);
            $peserta->update(['tahun_periode' => $tahunTarget]);

            $tipeSkema = $peserta->skemaArisan->tipe_skema;

            if ($tipeSkema === 'perorangan') {
                $bulanIuranFormat = $tanggalMulaiTagihan->locale('en')->format('F Y');

                TransaksiPembayaran::create([
                    'id_pesertaarisan' => $peserta->id_pesertaarisan,
                    'order_id'         => 'INV-' . strtoupper(bin2hex(random_bytes(3))) . '-' . $peserta->id_pesertaarisan,
                    'nominal'          => $peserta->skemaArisan->nominal_iuran,
                    'bulan_iuran'      => $bulanIuranFormat,
                    'status_pembayaran'=> 'pending',
                ]);

                \Illuminate\Support\Facades\DB::commit();
                return back()->with('success', "Akun {$user->nama} aktif! Tagihan bulan pertama (" . $tanggalMulaiTagihan->translatedFormat('F Y') . ") berhasil diterbitkan.");
            }
            if ($tipeSkema === 'kelompok') {
                $kodeBaru = 'KLP-' . strtoupper(Str::random(5)) . rand(10, 99);

                $kelompokBaru = KelompokArisan::create([
                    'nama_kelompok'    => 'Kelompok ' . $peserta->nama,
                    'id_ketua_peserta' => $peserta->id_pesertaarisan, 
                    'kode_kelompok'    => $kodeBaru,
                    'status_kelompok'  => 'proses'
                ]);

                // Langsung pasang id_kelompok baru ini ke profile milik ketua
                $peserta->update([
                    'id_kelompok' => $kelompokBaru->id_kelompok
                ]);
            }
            // Jika kelompok, cukup aktifkan akun ketua tanpa membuatkan tagihan dulu
            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', "Akun Ketua Kelompok {$user->nama} berhasil diaktifkan! Silakan instruksikan ketua untuk melengkapi anggota kelompok di aplikasi.");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal memproses verifikasi: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        $user = User::findOrFail($id);
        $namaUser = $user->nama;
        $user->delete();

        return back()->with('success', 'Pendaftaran ' . $namaUser . ' telah ditolak dan dihapus dari sistem');
    }
}