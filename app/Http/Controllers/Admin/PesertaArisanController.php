<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaArisan;
use App\Models\SkemaArisan;
use App\Models\KelompokArisan;
use App\Models\TransaksiPembayaran; // 🌟 SEKARANG SUDAH DI-IMPORT AGAR AMAN
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PesertaArisanController extends Controller
{
    /* ====================================================
        LIST PESERTA
    ======================================================= */
    public function index(Request $request)
    {
        $query = PesertaArisan::with(['user', 'skemaArisan', 'kelompok.ketua'])
            ->whereHas('user', function ($q) {
                $q->whereIn('status', ['aktif', 'nonaktif']);
            });

        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('peserta_arisan.nama', 'like', "%{$search}%")
                ->orWhere('peserta_arisan.alamat', 'like', "%{$search}%")
                ->orWhere('peserta_arisan.no_hp', 'like', "%{$search}%");
            });
        }

        $skemaId = $request->input('skema');
        if ($skemaId) {
            $query->where('id_skema', $skemaId);
        }

        $pesertas = $query
            ->join('users', 'peserta_arisan.id_user', '=', 'users.id_user')
            ->orderByRaw("FIELD(users.status, 'aktif', 'nonaktif') ASC")
            ->orderByRaw('id_kelompok IS NULL, id_kelompok ASC')
            ->orderByRaw('id_pesertaarisan = (select id_ketua_peserta from kelompok_arisan where kelompok_arisan.id_kelompok = peserta_arisan.id_kelompok) DESC')
            ->select('peserta_arisan.*')
            ->paginate(15);

        $skemas = SkemaArisan::orderBy('nama_skema', 'asc')->get();

        return view('admin.peserta.index', compact('pesertas', 'skemas'));
    }

    public function create()
    {
        $skemas = SkemaArisan::orderBy('nama_skema')->get();
        return view('admin.peserta.create', compact('skemas'));
    }

    /* ====================================================
        STORE DATA PESERTA BARU & AUTO GENERATE TAGIHAN
    ======================================================= */
    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255|unique:peserta_arisan,nama',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:8',
            'no_hp'         => 'required|unique:peserta_arisan,no_hp',
            'alamat'        => 'required',
            'id_skema'      => 'required|exists:skema_arisan,id_skema',
            'tahun_periode' => 'required|numeric|digits:4',
        ], [
            'nama.unique'           => 'Nama lengkap sudah terdaftar.',
            'email.unique'          => 'Email sudah digunakan akun lain.',
            'no_hp.unique'          => 'Nomor HP sudah terdaftar.',
            'tahun_periode.required' => 'Tahun periode wajib ditentukan!',
        ]);

        $skemaPilihan = SkemaArisan::findOrFail($request->id_skema);
        $durasiBulan = (int)$skemaPilihan->durasi_bulan;
        $tahunTarget = (int)$request->tahun_periode; 

        $tahunSekarang = (int)date('Y'); 
        $bulanSekarang = (int)date('n'); 

        // Peta Bulan Idul Adha Resmi Masjid
        $petaIdulAdha = [
            2026 => 5, 2027 => 5, 2028 => 4, 2029 => 4, 2030 => 3
        ];
        $bulanIdulAdha = $petaIdulAdha[$tahunTarget] ?? 5;
        $bulanLunas = $bulanIdulAdha - 1;

        // ── 1. VALIDASI SIKLUS (3 TAHUN & 1 TAHUN) ──
        if ($durasiBulan == 36) {
            if ($tahunTarget < 2028) {
                return back()->withInput()->withErrors([
                    'tahun_periode' => "Gagal! Pendaftaran skema 3 tahun untuk target pelaksanaan tahun {$tahunTarget} sudah ditutup rapat karena angkatan tersebut saat ini sedang berjalan."
                ]);
            }
            if (($tahunTarget - 2028) % 3 !== 0) {
                return back()->withInput()->withErrors([
                    'tahun_periode' => "Gagal! Tahun pelaksanaan {$tahunTarget} tidak valid untuk skema 3 tahun."
                ]);
            }
        } else {
            if ($tahunTarget < $tahunSekarang || ($tahunTarget === $tahunSekarang && $bulanSekarang > $bulanIdulAdha)) {
                return back()->withInput()->withErrors([
                    'tahun_periode' => "Gagal! Pendaftaran skema 1 tahun untuk periode pelaksanaan qurban tahun {$tahunTarget} sudah resmi ditutup."
                ]);
            }
        }
        
        DB::beginTransaction();
        try {
            // 1. Simpan Akun User
            $user = User::create([
                'nama'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'peserta',
                'status'   => 'aktif',
            ]);

            // 2. Simpan Data Profil Peserta
            $peserta = PesertaArisan::create([
                'id_user'       => $user->id_user,
                'id_skema'      => $request->id_skema,
                'nama'          => $request->nama,
                'alamat'        => $request->alamat,
                'no_hp'         => $request->no_hp,
                'tahun_periode' => $tahunTarget
            ]);

            // 3. Kalkulasi Bulan Iuran Pertama (Sama persis dengan logika Approval)
            $tanggalJatuhTempoAkhir = Carbon::create($tahunTarget, $bulanLunas, 10);
            $tanggalMulaiTagihan = $tanggalJatuhTempoAkhir->subMonths($durasiBulan - 1);
            $bulanIuranFormat = $tanggalMulaiTagihan->locale('en')->format('F Y'); // Contoh: "May 2026"

            // ── STRATEGI GENERATE TAGIHAN BERDASARKAN TIPE SKEMA ──
            if ($skemaPilihan->tipe_skema === 'kelompok') {
                // A. JIKA SKEMA KELOMPOK (Otomatis Jadi Ketua Kelompok Baru)
                $kodeBaru = 'KLP-' . strtoupper(Str::random(5)) . rand(10, 99);

                $kelompokBaru = KelompokArisan::create([
                    'nama_kelompok'    => 'Kelompok ' . $peserta->nama,
                    'id_ketua_peserta' => $peserta->id_pesertaarisan, 
                    'kode_kelompok'    => $kodeBaru,
                    'status_kelompok'  => 'proses' // Menunggu sampai anggota lengkap (7 orang)
                ]);

                $peserta->update([
                    'id_kelompok' => $kelompokBaru->id_kelompok
                ]);

                // Hitung nominal jika dibagi rata 7 orang anggota
                $nominalBagiTujuh = ceil($skemaPilihan->nominal_iuran / 7);

                // Buatkan Invoice Perdana Khusus untuk si Ketua Kelompok
                TransaksiPembayaran::create([
                    'id_pesertaarisan'  => $peserta->id_pesertaarisan,
                    'order_id'          => 'INV-' . strtoupper(bin2hex(random_bytes(3))) . '-' . $peserta->id_pesertaarisan,
                    'nominal'           => $nominalBagiTujuh,
                    'bulan_iuran'       => $bulanIuranFormat,
                    'status_pembayaran' => 'pending',
                ]);

            } else {
                // B. JIKA SKEMA PERORANGAN (Langsung Generate Full Nominal)
                TransaksiPembayaran::create([
                    'id_pesertaarisan'  => $peserta->id_pesertaarisan,
                    'order_id'          => 'INV-' . strtoupper(bin2hex(random_bytes(3))) . '-' . $peserta->id_pesertaarisan,
                    'nominal'           => $skemaPilihan->nominal_iuran,
                    'bulan_iuran'       => $bulanIuranFormat,
                    'status_pembayaran' => 'pending',
                ]);
            }
            
            DB::commit();
            
            if ($skemaPilihan->tipe_skema === 'kelompok') {
                return redirect()->route('admin.peserta.index')->with('success', 'Peserta Kelompok berhasil didaftarkan, otomatis di-set sebagai Ketua, dan tagihan iuran bulan pertama berhasil diterbitkan.');
            }
            return redirect()->route('admin.peserta.index')->with('success', 'Peserta Perorangan berhasil didaftarkan dan tagihan iuran bulan pertama berhasil diterbitkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan peserta: ' . $e->getMessage());
        }
    }

    /* ====================================================
        API AJAX: OPSI SELECTION TAHUN PERIODE (SINKRON SIKLUS REAL)
    ======================================================= */
    public function checkSkemaPeriode(Request $request)
    {
        $skema = SkemaArisan::find($request->id_skema);
        if (!$skema) return response()->json([]);

        $durasiBulan = (int)$skema->durasi_bulan;
        $tahunSekarang = (int)date('Y'); // 2026
        $optionsTahun = [];

        $petaIdulAdha = [
            2026 => 5, 2027 => 5, 2028 => 4, 2029 => 4, 2030 => 3
        ];

        // ── 🛠️ FIX JALUR SKEMA 3 TAHUN: OTOMATIS LOMPAT SIKLUS GENERASI BARU ──
        if ($durasiBulan == 36) {
            // Karena angkatan tua (2025-2027) sedang jalan, pembukaan angkatan baru murni dimulai dari 2028
            $tahunMulaiOpsi = 2028; 

            // Lakukan looping melompat cantik setiap 3 tahun sekali ($i += 3)
            for ($i = $tahunMulaiOpsi; $i <= $tahunSekarang + 8; $i += 3) {
                $optionsTahun[] = $i;
            }

            return response()->json($optionsTahun);
        }

        // ── JALUR STANDAR SKEMA 1 TAHUN ──
        for ($i = $tahunSekarang; $i <= $tahunSekarang + 5; $i++) {
            $bulanIdulAdha = $petaIdulAdha[$i] ?? 5;
            $bulanLunas = $bulanIdulAdha - 1;

            $tanggalJatuhTempoAkhir = Carbon::create($i, $bulanLunas, 10);
            $tanggalMulaiTagihan = $tanggalJatuhTempoAkhir->copy()->subMonths($durasiBulan - 1)->startOfMonth();
            $batasTutupPendaftaran = $tanggalMulaiTagihan->copy()->addMonth()->startOfMonth();

            if (Carbon::now()->startOfMonth()->lessThan($batasTutupPendaftaran)) {
                $optionsTahun[] = $i;
            }
        }

        return response()->json($optionsTahun);
    }

    public function edit($id)
    {
        $peserta = PesertaArisan::with(['user'])->findOrFail($id);
        $skemas  = SkemaArisan::orderBy('nama_skema')->get();

        return view('admin.peserta.edit', compact('peserta', 'skemas'));
    }

    /* ====================================================
        UPDATE PESERTA (STATUS & PASSWORD)
    ======================================================= */
    public function update(Request $request, $id)
    {
        $peserta = PesertaArisan::with('user')->findOrFail($id);

        $request->validate([
            'status'   => 'required|in:aktif,nonaktif',
            'password' => 'nullable|min:8',
        ], [
            'status.required' => 'Status peserta wajib diisi.',
            'status.in'       => 'Status peserta harus berupa "aktif" atau "nonaktif".',
            'password.min'    => 'Password baru minimal harus 8 karakter.',
        ]);

        if ($peserta->user) {
            $userData = [
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $peserta->user->update($userData);
        }

        return redirect()
            ->route('admin.peserta.index')
            ->with('success', 'Status dan Password peserta berhasil diperbarui');
    }

    /* ====================================================
        FITUR: TAMBAH ANGGOTA KELOMPOK OLEH ADMIN
    ======================================================= */
    public function createAnggotaKelompok($id_kelompok)
    {
        $kelompok = KelompokArisan::findOrFail($id_kelompok);
        $ketua = PesertaArisan::findOrFail($kelompok->id_ketua_peserta); 

        return view('admin.peserta.create_anggota', compact('kelompok', 'ketua'));
    }

    public function storeAnggotaKelompok(Request $request, $id_kelompok)
    {
        $kelompok = KelompokArisan::findOrFail($id_kelompok);
        $ketua = PesertaArisan::findOrFail($kelompok->id_ketua_peserta);

        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|numeric|unique:peserta_arisan,no_hp',
        ], [
            'nama.required' => 'Nama anggota wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar.',
        ]);

        $jumlahAnggota = PesertaArisan::where('id_kelompok', $kelompok->id_kelompok)->count();
        if ($jumlahAnggota >= 7) {
            return redirect()->route('admin.peserta.index')->with('error', 'Kuota anggota kelompok ini sudah penuh.');
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'nama' => $request->nama,
                'email' => Str::slug($request->nama) . rand(100, 999) . '@nurulhuda.com',
                'password' => Hash::make('12345678'), 
                'role' => 'peserta',
                'status' => 'aktif'
            ]);

            PesertaArisan::create([
                'id_user'       => $user->id_user,
                'id_skema'      => $ketua->id_skema,
                'id_kelompok'   => $kelompok->id_kelompok,
                'nama'          => $request->nama,
                'no_hp'         => $request->no_hp,
                'alamat'        => $ketua->alamat,
                'tahun_periode' => $ketua->tahun_periode // 🌟 FIX: MEWARISI TAHUN PERIODE KETUA AGAR REKAP MATCH
            ]);

            $jumlahFinal = PesertaArisan::where('id_kelompok', $kelompok->id_kelompok)->count();
            if ($jumlahFinal >= 7) {
                $kelompok->update(['status_kelompok' => 'lengkap']);

                $tahunTarget = (int)$ketua->tahun_periode;
                $durasiBulan = (int)$ketua->skemaArisan->durasi_bulan;

                $petaIdulAdha = [2026 => 5, 2027 => 5, 2028 => 4, 2029 => 4, 2030 => 3];
                $bulanIdulAdha = $petaIdulAdha[$tahunTarget] ?? 5;
                $bulanLunas = $bulanIdulAdha - 1;

                $tanggalJatuhTempoAkhir = Carbon::create($tahunTarget, $bulanLunas, 10);
                $tanggalMulaiTagihan = $tanggalJatuhTempoAkhir->subMonths($durasiBulan - 1);
                $bulanIuranFormat = $tanggalMulaiTagihan->locale('en')->format('F Y');

                $nominalBagiTujuh = ceil($ketua->skemaArisan->nominal_iuran / 7);

                $semuaAnggota = PesertaArisan::where('id_kelompok', $kelompok->id_kelompok)->get();

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
                return redirect()->route('admin.peserta.index')->with('success', 'Anggota ke-7 berhasil bergabung! Kelompok kini resmi LENGKAP dan tagihan bulan pertama untuk seluruh anggota kelompok berhasil diterbitkan.');
            }
            return redirect()->route('admin.peserta.index')->with('success', 'Anggota kelompok berhasil ditambahkan oleh Admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $peserta = PesertaArisan::with('user')->findOrFail($id);
        if ($peserta->user) {
            $peserta->user->delete();
        }
        $peserta->delete();

        return redirect()->route('admin.peserta.index')->with('success', 'Peserta berhasil dihapus');
    }

    public function show($id)
    {
        $peserta = PesertaArisan::with(['user', 'skemaArisan'])
            ->withCount(['transaksi as total_iuran' => function($query) {
                $query->where('status_pembayaran', 'sukses');
            }])
            ->findOrFail($id);

        return view('admin.peserta.show', compact('peserta'));
    }
}