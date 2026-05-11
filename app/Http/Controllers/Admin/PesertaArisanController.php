<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaArisan;
use App\Models\SkemaArisan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PesertaArisanController extends Controller
{
    /* =========================
        LIST PESERTA
        (HANYA AKTIF & NONAKTIF)
    ========================== */
    public function index(Request $request)
    {
        $query = PesertaArisan::with(['user', 'skemaArisan', 'kelompok.ketua'])
            ->whereHas('user', function ($q) {
                $q->whereIn('status', ['aktif', 'nonaktif']);
            });

        /* =========================
            LOGIKA PENCARIAN
        ========================== */
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('alamat', 'like', "%{$search}%")
                ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        /* =========================
            LOGIKA FILTER SKEMA
        ========================== */
        $skemaId = $request->input('skema');
        if ($skemaId) {
            $query->where('id_skema', $skemaId);
        }

        /* =========================
            LOGIKA PENGURUTAN (GROUPING)
        ========================== */
        $pesertas = $query->orderByRaw('id_kelompok IS NULL, id_kelompok ASC')
                        ->orderBy('id_pesertaarisan', 'desc')
                        ->paginate(15); // Menggunakan paginasi untuk performa

        // Mengambil semua skema untuk dropdown filter di view
        $skemas = SkemaArisan::orderBy('nama_skema', 'asc')->get();

        return view('admin.peserta.index', compact('pesertas', 'skemas'));
    }


    /* =========================
        FORM TAMBAH PESERTA
    ========================== */
    public function create()
    {
        $skemas = SkemaArisan::orderBy('nama_skema')->get();
        return view('admin.peserta.create', compact('skemas'));
    }


    /* =========================
        SIMPAN PESERTA
        (ADMIN LANGSUNG AKTIF)
    ========================== */
    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255|unique:peserta_arisan,nama',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'no_hp'    => 'required|unique:peserta_arisan,no_hp',
            'alamat'   => 'required',
            'id_skema' => 'required|exists:skema_arisan,id_skema',
        ], [
            // Pesan Error Kustom
            'nama.required'     => 'Nama lengkap wajib diisi.',
            'nama.unique'       => 'Nama ini sudah digunakan oleh peserta lain.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar di sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password terlalu pendek, minimal harus 8 karakter.',
            'id_skema.required' => 'Silakan pilih paket arisan.',
            'no_hp.required'    => 'Nomor WhatsApp wajib diisi.',
            'no_hp.unique'      => 'Nomor WhatsApp sudah digunakan oleh peserta lain.',
            'alamat.required'   => 'Alamat domisili wajib diisi.',
        ]);

        // ✅ BUAT USER (STATUS AKTIF)
        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'peserta',
            'status'   => 'aktif',
        ]);

        // ✅ BUAT PESERTA
        PesertaArisan::create([
            'id_user'  => $user->id_user,
            'id_skema' => $request->id_skema,
            'nama'     => $request->nama,
            'alamat'   => $request->alamat,
            'no_hp'    => $request->no_hp,
        ]);

        return redirect()
            ->route('admin.peserta.index')
            ->with('success', 'Peserta berhasil ditambahkan');
    }


    /* =========================
        FORM EDIT
    ========================== */
    public function edit($id)
    {
        $peserta = PesertaArisan::with(['user'])->findOrFail($id);
        $skemas  = SkemaArisan::orderBy('nama_skema')->get();

        return view('admin.peserta.edit', compact('peserta', 'skemas'));
    }


    /* =========================
        UPDATE PESERTA
        (UPDATE STATUS DI USERS)
    ========================== */
    public function update(Request $request, $id)
    {
        $peserta = PesertaArisan::with('user')->findOrFail($id);

        $request->validate([
            'nama'   => 'required|string|max:255',
            'no_hp'  => 'required',
            'alamat' => 'required',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama.required'   => 'Nama lengkap wajib diisi.',
            'no_hp.required'  => 'Nomor WhatsApp wajib diisi.',
            'alamat.required' => 'Alamat domisili wajib diisi.',
            'status.required' => 'Status peserta wajib diisi.',
            'status.in'       => 'Status peserta harus berupa "aktif" atau "nonaktif".',
        ]);

        // ✅ UPDATE PESERTA
        $peserta->update([
            'nama'     => $request->nama,
            'alamat'   => $request->alamat,
            'no_hp'    => $request->no_hp,
        ]);

        // ✅ UPDATE USER (TERMASUK STATUS)
        if ($peserta->user) {
            $peserta->user->update([
                'nama'   => $request->nama,
                'status' => $request->status,
            ]);
        }

        return redirect()
            ->route('admin.peserta.index')
            ->with('success', 'Peserta berhasil diperbarui');
    }


    /* =========================
        HAPUS PESERTA
    ========================== */
    public function destroy($id)
    {
        $peserta = PesertaArisan::with('user')->findOrFail($id);

        if ($peserta->user) {
            $peserta->user->delete();
        }

        $peserta->delete();

        return redirect()
            ->route('admin.peserta.index')
            ->with('success', 'Peserta berhasil dihapus');
    }


    /* =========================
        DETAIL PESERTA
    ========================== */
    public function show($id)
    {
        $peserta = PesertaArisan::with(['user', 'skemaArisan'])->findOrFail($id);
        return view('admin.peserta.show', compact('peserta'));
    }
}
