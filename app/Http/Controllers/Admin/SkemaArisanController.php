<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkemaArisan;
use Illuminate\Http\Request;

class SkemaArisanController extends Controller
{
    // TAMPILKAN DATA
    public function index()
    {
        $skemas = SkemaArisan::latest()->get();
        return view('admin.skema.index', compact('skemas'));
    }

    // FORM TAMBAH
    public function create()
    {
        return view('admin.skema.create');
    }

    // SIMPAN DATA
    public function store(Request $request)
    {
        $request->validate([
            'nama_skema'     => 'required|string|max:100',
            'durasi_bulan'   => 'required|in:12,36',
            'tipe_skema'     => 'required|in:perorangan,kelompok',
            'nominal_iuran'  => 'required|numeric|min:0',
            'deskripsi'      => 'nullable|string'
        ]);

        SkemaArisan::create([
            'nama_skema'    => $request->nama_skema,
            'durasi_bulan'  => $request->durasi_bulan,
            'tipe_skema'    => $request->tipe_skema,
            'nominal_iuran' => $request->nominal_iuran,
            'deskripsi'     => $request->deskripsi,
        ]);

        return redirect()->route('admin.skema.index')
            ->with('success', 'Skema berhasil ditambahkan');
    }

    // FORM EDIT
    public function edit($id)
    {
        $skema = SkemaArisan::findOrFail($id);
        return view('admin.skema.edit', compact('skema'));
    }

    // UPDATE DATA
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_skema'     => 'required|string|max:100',
            'durasi_bulan'   => 'required|in:12,36',
            'tipe_skema'     => 'required|in:perorangan,kelompok',
            'nominal_iuran'  => 'required|numeric|min:0',
            'deskripsi'      => 'nullable|string'
        ]);

        $skema = SkemaArisan::findOrFail($id);

        $skema->update([
            'nama_skema'    => $request->nama_skema,
            'durasi_bulan'  => $request->durasi_bulan,
            'tipe_skema'    => $request->tipe_skema,
            'nominal_iuran' => $request->nominal_iuran,
            'deskripsi'     => $request->deskripsi,
        ]);

        return redirect()->route('admin.skema.index')
            ->with('success', 'Skema berhasil diperbarui');
    }

    // HAPUS DATA
    public function destroy($id)
    {
        SkemaArisan::findOrFail($id)->delete();

        return redirect()->route('admin.skema.index')
            ->with('success', 'Skema berhasil dihapus');
    }
}
