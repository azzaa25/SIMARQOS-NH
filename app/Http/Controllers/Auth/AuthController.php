<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\PesertaArisan;
use App\Models\SkemaArisan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /* =======================
        FORM LOGIN
    ======================== */
    public function loginForm()
    {
        return view('auth.login');
    }

    /* =======================
        FORM REGISTER
    ======================== */
    public function registerForm()
    {
        $skemas = SkemaArisan::orderBy('nama_skema')->get();
        return view('auth.register', compact('skemas'));
    }

    /* =======================
        PROSES LOGIN
    ======================== */
    public function login(Request $request)
    {
        $request->validate(
            [
                'email'    => 'required|email',
                'password' => 'required|min:8',
            ],
            [
                'email.required'    => 'Email wajib diisi',
                'email.email'       => 'Format email tidak valid',
                'password.required' => 'Password wajib diisi',
                'password.min'      => 'Password minimal 8 karakter',
            ]
        );

        if (Auth::attempt($request->only('email', 'password'))) {

            $user = Auth::user();

            // 🔥 CEK STATUS USER
            if ($user->status !== 'aktif') {

                Auth::logout();

                if ($user->status === 'pending') {
                    $message = 'Akun Anda masih menunggu konfirmasi dari Admin.';
                } elseif ($user->status === 'nonaktif') {
                    $message = 'Akun Anda telah dinonaktifkan. Silakan hubungi Admin.';
                } else {
                    $message = 'Status akun Anda tidak valid.';
                }

                return back()->withErrors(['email' => $message]);
            }

            $request->session()->regenerate();

            return match ($user->role) {
                'admin'   => redirect()->route('admin.dashboard'),
                'peserta' => redirect()->route('peserta.dashboard'),
                default   => redirect()->route('login'),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.'
        ]);
    }

    /* =======================
        PROSES REGISTER
    ======================== */
    public function register(Request $request)
    {
        $request->validate(
            [
                'nama'     => 'required',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'no_hp'    => 'required',
                'alamat'   => 'required',
                'id_skema' => 'required|exists:skema_arisan,id_skema',
            ],
            [
                'nama.required'      => 'Nama wajib diisi',
                'email.required'     => 'Email wajib diisi',
                'email.email'        => 'Format email tidak valid',
                'email.unique'       => 'Email sudah terdaftar',
                'password.required'  => 'Password wajib diisi',
                'password.min'       => 'Password minimal 8 karakter',
                'password.confirmed' => 'Konfirmasi password tidak sesuai',
                'no_hp.required'     => 'Nomor HP wajib diisi',
                'alamat.required'    => 'Alamat wajib diisi',
                'id_skema.required'  => 'Skema arisan wajib dipilih',
                'id_skema.exists'    => 'Skema tidak valid',
            ]
        );
        
        $skemaPilihan = SkemaArisan::findOrFail($request->id_skema);
        $durasiBulan = (int)$skemaPilihan->durasi_bulan;

        if ($durasiBulan == 36) {
            // Peta Bulan Idul Adha terdekat angkatan berjalan (2027)
            $tahunTargetAngkatanAktif = 2027;
            $bulanIdulAdha = 5; // Mei
            $bulanLunas = $bulanIdulAdha - 1; // April

            $tanggalJatuhTempoAkhir = Carbon::create($tahunTargetAngkatanAktif, $bulanLunas, 1);
            $tanggalMulaiTagihanSeharusnya = $tanggalJatuhTempoAkhir->copy()->subMonths($durasiBulan - 1);

            // Jika hari ini sudah melewati bulan mulai tagihan angkatan aktif, KUNCI PENDAFTARANNYA
            if (Carbon::now()->startOfMonth()->greaterThan($tanggalMulaiTagihanSeharusnya->startOfMonth())) {
                return back()->withInput()->withErrors([
                    'id_skema' => 'Pendaftaran untuk skema arisan 3 tahun angkatan saat ini sudah ditutup karena periode iuran sudah berjalan.'
                ]);
            }
        }

        // ✅ SIMPAN USER (STATUS OTOMATIS PENDING)
        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'peserta',
            'status'   => 'pending'
        ]);

        // ✅ SIMPAN DATA PESERTA (TANPA STATUS)
        PesertaArisan::create([
            'id_user'  => $user->id_user,
            'id_skema' => $request->id_skema,
            'nama'     => $request->nama,
            'alamat'   => $request->alamat,
            'no_hp'    => $request->no_hp,
        ]);

        // ❌ TIDAK AUTO LOGIN
        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Tunggu admin konfirmasi sebelum login.');
    }

    /* =======================
        LOGOUT
    ======================== */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
