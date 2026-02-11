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
        LIST PESERTA DENGAN SEARCH & FILTER
    ========================== */
    public function index(Request $request)
    {
        $query = PesertaArisan::with(['user', 'skemaArisan']);

        $search = $request->input('search'); // nama/alamat/no_hp
        $skema  = $request->input('skema');  // id_skema

        // Terapkan filter hanya jika ada input
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('alamat', 'like', "%{$search}%")
                ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($skema) {
            $query->where('id_skema', $skema);
        }

        // Pagination
        $pesertas = $query->orderBy('id_pesertaarisan', 'desc')->paginate(10);

        $skemas = SkemaArisan::orderBy('nama_skema')->get();

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
    ========================== */
    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'no_hp'    => 'required',
            'alamat'   => 'required',
            'id_skema' => 'required|exists:skema_arisan,id_skema',
        ]);

        // BUAT USER
        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'peserta',
        ]);

        // BUAT PESERTA ARISAN
        PesertaArisan::create([
            'id_user'  => $user->id_user,
            'id_skema' => $request->id_skema,
            'nama'     => $request->nama,
            'alamat'   => $request->alamat,
            'no_hp'    => $request->no_hp,
            'status'   => 'aktif',
        ]);

        return redirect()
            ->route('admin.peserta.index')
            ->with('success', 'Peserta berhasil ditambahkan');
    }

    /* =========================
        FORM EDIT PESERTA
    ========================== */
    public function edit($id)
    {
        $peserta = PesertaArisan::with(['user'])->findOrFail($id);
        $skemas  = SkemaArisan::orderBy('nama_skema')->get();

        return view('admin.peserta.edit', compact('peserta', 'skemas'));
    }

    /* =========================
        UPDATE PESERTA
    ========================== */
    public function update(Request $request, $id)
    {
        $peserta = PesertaArisan::findOrFail($id);

        $request->validate([
            'nama'     => 'required|string|max:255',
            'no_hp'    => 'required',
            'alamat'   => 'required',
            'id_skema' => 'required|exists:skema_arisan,id_skema',
            'status'   => 'required|in:aktif,nonaktif',
        ]);

        $peserta->update([
            'nama'     => $request->nama,
            'alamat'   => $request->alamat,
            'no_hp'    => $request->no_hp,
            'id_skema' => $request->id_skema,
            'status'   => $request->status,
        ]);

        // UPDATE USER (NAMA SAJA)
        if ($peserta->user) {
            $peserta->user->update([
                'nama' => $request->nama,
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
        $peserta = PesertaArisan::findOrFail($id);

        // hapus user terlebih dahulu
        if ($peserta->user) {
            $peserta->user->delete();
        }

        $peserta->delete();

        return redirect()
            ->route('admin.peserta.index')
            ->with('success', 'Peserta berhasil dihapus');
    }
    /* =========================
        SHOW PESERTA
    ========================== */
    public function show($id)
    {
        $peserta = PesertaArisan::with(['user', 'skemaArisan'])->findOrFail($id);
        return view('admin.peserta.show', compact('peserta'));
    }

}
