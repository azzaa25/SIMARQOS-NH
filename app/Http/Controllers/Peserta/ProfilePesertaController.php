<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfilePesertaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $peserta = $user->peserta; // Pastikan relasi 'peserta' sudah ada di model User
        return view('peserta.profile.index', compact('user', 'peserta'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $peserta = $user->peserta;

        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id_user . ',id_user',
            'no_hp' => 'required|numeric',
            'alamat' => 'required|string',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Update Tabel Users
        $user->nama = $request->nama;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Update Tabel Peserta Arisan
        $peserta->update([
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}