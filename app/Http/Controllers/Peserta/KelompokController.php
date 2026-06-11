<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\PesertaArisan;
use App\Models\KelompokArisan;
use App\Models\User;
use App\Models\TransaksiPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KelompokController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = auth()->user();
        $peserta = $user->peserta;

        if (!$peserta) {
            return redirect()->route('peserta.dashboard')->with('error', 'Data peserta tidak ditemukan.');
        }

        $skema = $peserta->skemaArisan;

        if (!$skema || $skema->tipe_skema !== 'kelompok') {
            return redirect()->route('peserta.dashboard')->with('error', 'Skema Anda bukan kelompok.');
        }

        $kelompok = $peserta->kelompok;
        
        // Jika sudah punya kelompok, ambil semua anggota
        if ($kelompok) {
            $anggota = PesertaArisan::where('id_kelompok', $peserta->id_kelompok)
                ->orderBy('created_at', 'asc')
                ->get();
                
            // CEK APAKAH USER INI ADALAH KETUA
            // Kita cek apakah id_pesertaarisan user ini sama dengan id_ketua_peserta di tabel kelompok
            $isKetua = ($peserta->id_pesertaarisan == $kelompok->id_ketua_peserta);
        } else {
            $kelompok = null;
            $anggota = collect([$peserta]);
            $isKetua = true; // Jika belum ada kelompok, dia dianggap calon ketua
        }

        $maxKuota = 7;
        $sisaKuota = max(0, $maxKuota - $anggota->count());

        return view('peserta.kelompok.index', compact(
            'kelompok',
            'anggota',
            'sisaKuota',
            'skema',
            'peserta',
            'isKetua' // Kirim variabel ini ke view
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE ANGGOTA
    |--------------------------------------------------------------------------
    */
    public function storeAnggota(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|numeric|unique:peserta_arisan,no_hp',
        ]);

        $ketua = auth()->user()->peserta;

        DB::beginTransaction();
        try {
            // 1. BUAT KELOMPOK JIKA BELUM ADA
            if (!$ketua->id_kelompok) {
                $kodeBaru = 'KLP-' . strtoupper(Str::random(5)) . rand(10, 99);

                $kelompokBaru = KelompokArisan::create([
                    'nama_kelompok' => 'Kelompok ' . $ketua->nama,
                    'id_ketua_peserta' => $ketua->id_pesertaarisan,
                    'kode_kelompok' => $kodeBaru,
                    'status_kelompok' => 'proses'
                ]);

                // PAKSA UPDATE DI DATABASE DAN DI VARIABEL
                $ketua->id_kelompok = $kelompokBaru->id_kelompok;
                $ketua->save(); 
            }

            // 2. CEK KUOTA (Gunakan $ketua->id_kelompok yang sudah terisi)
            $jumlahAnggota = PesertaArisan::where('id_kelompok', $ketua->id_kelompok)->count();
            if ($jumlahAnggota >= 7) {
                return back()->with('error', 'Kuota anggota sudah penuh.');
            }

            // 3. BUAT USER BARU
            $user = User::create([
                'nama' => $request->nama,
                'email' => Str::slug($request->nama) . rand(100, 999) . '@nurulhuda.com',
                'password' => Hash::make('12345678'),
                'role' => 'peserta',
                'status' => 'aktif'
            ]);

            // 4. SIMPAN PESERTA (Gunakan ID User yang baru dibuat)
            PesertaArisan::create([
                'id_user' => $user->id_user, // Pastikan kolom di tabel user adalah id_user
                'id_skema' => $ketua->id_skema,
                'id_kelompok' => $ketua->id_kelompok,
                'nama' => $request->nama,
                'no_hp' => $request->no_hp,
                'alamat' => $ketua->alamat
            ]);

            // 5. UPDATE STATUS KELOMPOK
            $jumlahFinal = PesertaArisan::where('id_kelompok', $ketua->id_kelompok)->count();
            if ($jumlahFinal >= 7) {
                // Kunci status kelompok menjadi lengkap
                KelompokArisan::where('id_kelompok', $ketua->id_kelompok)
                    ->update(['status_kelompok' => 'lengkap']);

                // Ambil parameter periode dari data ketua kelompok
                $tahunTarget = (int)$ketua->tahun_periode;
                $durasiBulan = (int)$ketua->skemaArisan->durasi_bulan; // 12 atau 36

                $petaIdulAdha = [2026 => 5, 2027 => 5, 2028 => 4, 2029 => 4, 2030 => 3];
                $bulanIdulAdha = $petaIdulAdha[$tahunTarget] ?? 5;
                $bulanLunas = $bulanIdulAdha - 1;

                // Hitung mundur awal mula siklus iuran wajib
                $tanggalJatuhTempoAkhir = Carbon::create($tahunTarget, $bulanLunas, 10);
                $tanggalMulaiTagihan = $tanggalJatuhTempoAkhir->subMonths($durasiBulan - 1);
                $bulanIuranFormat = $tanggalMulaiTagihan->locale('en')->format('F Y');

                // Hitung nominal final (Total iuran hewan qurban paket dibagi rata 7 orang)
                $nominalBagiTujuh = ceil($ketua->skemaArisan->nominal_iuran / 7);

                // Ambil data ke-7 anggota kelompok tersebut
                $semuaAnggota = PesertaArisan::where('id_kelompok', $ketua->id_kelompok)->get();

                // Buatkan Invoice tagihan pertama secara bersamaan untuk ke-7 orang
                foreach ($semuaAnggota as $anggotaKelompok) {
                    $cekTagihan = TransaksiPembayaran::where('id_pesertaarisan', $anggotaKelompok->id_pesertaarisan)
                        ->where('bulan_iuran', $bulanIuranFormat)
                        ->exists();

                    if (!$cekTagihan) {
                        TransaksiPembayaran::create([
                            'id_pesertaarisan' => $anggotaKelompok->id_pesertaarisan,
                            'order_id'         => 'INV-' . strtoupper(bin2hex(random_bytes(3))) . '-' . $anggotaKelompok->id_pesertaarisan,
                            'nominal'          => $nominalBagiTujuh,
                            'bulan_iuran'      => $bulanIuranFormat,
                            'status_pembayaran'=> 'pending',
                        ]);
                    }
                }
            }

            DB::commit();
            if ($jumlahFinal >= 7) {
                return back()->with('success', 'Anggota ke-7 berhasil bergabung! Kelompok kini resmi LENGKAP dan tagihan bulan pertama untuk seluruh anggota kelompok berhasil diterbitkan.');
            }
            return back()->with('success', 'Anggota berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Tampilkan pesan error asli agar tahu letak salahnya
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE ANGGOTA
    |--------------------------------------------------------------------------
    */
    public function updateAnggota(Request $request, $id)
    {
        $ketua = auth()->user()->peserta;
        $peserta = PesertaArisan::findOrFail($id);

        // Pastikan satu kelompok
        if ($peserta->id_kelompok != $ketua->id_kelompok) {
            return back()->with('error', 'Tidak diizinkan.');
        }

        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|numeric|unique:peserta_arisan,no_hp,' 
                        . $peserta->id_pesertaarisan . ',id_pesertaarisan',
        ]);

        $peserta->update([
            'nama' => $request->nama,
            'no_hp' => $request->no_hp
        ]);

        $peserta->user->update([
            'nama' => $request->nama
        ]);

        return back()->with('success', 'Data anggota berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE ANGGOTA
    |--------------------------------------------------------------------------
    */
    public function destroyAnggota($id)
    {
        $ketua = auth()->user()->peserta;
        $peserta = PesertaArisan::findOrFail($id);

        // Jangan hapus diri sendiri (ketua)
        if ($peserta->id_user == auth()->id()) {
            return back()->with('error', 'Ketua kelompok tidak dapat dihapus.');
        }

        // Pastikan satu kelompok
        if ($peserta->id_kelompok != $ketua->id_kelompok) {
            return back()->with('error', 'Tidak diizinkan.');
        }

        DB::beginTransaction();

        try {

            $user = $peserta->user;

            $peserta->delete();
            $user->delete();

            // Update status kelompok jika anggota < 7
            $jumlahSisa = PesertaArisan::where('id_kelompok', $ketua->id_kelompok)->count();

            if ($jumlahSisa < 7) {
                KelompokArisan::where('id_kelompok', $ketua->id_kelompok)
                    ->update(['status_kelompok' => 'proses']);
            }

            DB::commit();

            return back()->with('success', 'Anggota berhasil dihapus.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Gagal menghapus anggota.');
        }
    }
}
