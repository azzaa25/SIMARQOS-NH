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
        // AMBIL DATA SKEMA UNTUK DROPDOWN
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
            $request->session()->regenerate();

            // REDIRECT SESUAI ROLE
            return match (Auth::user()->role) {
                'admin'   => redirect()->route('admin.dashboard'),
                'peserta' => redirect()->route('peserta.dashboard'),
                default   => redirect()->route('login'),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password salah'
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
                'id_skema.required'  => 'Skema qurban wajib dipilih',
                'id_skema.exists'    => 'Skema tidak valid',
            ]
        );

        // SIMPAN USER
        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'peserta'
        ]);

        // SIMPAN PESERTA ARISAN
        PesertaArisan::create([
            'id_user' => $user->id_user,
            'id_skema'=> $request->id_skema,
            'nama'    => $request->nama,
            'alamat'  => $request->alamat,
            'no_hp'   => $request->no_hp,
            'status'  => 'aktif'
        ]);

        // AUTO LOGIN
        Auth::login($user);

        return redirect()->route('peserta.dashboard');
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
