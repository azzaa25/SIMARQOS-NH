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
        $validated = $request->validate([
            'nama_skema'    => 'required|string|max:100',
            'durasi_bulan'  => 'required|in:12,36',
            'tipe_skema'    => 'required|in:perorangan,kelompok',
            'nominal_iuran' => 'required|numeric|min:1000',
            'deskripsi'     => 'nullable|string'
        ], [
            'nama_skema.required'    => 'Nama paket arisan wajib diisi.',
            'nominal_iuran.required' => 'Nominal iuran tidak boleh kosong.',
            'nominal_iuran.min'      => 'Minimal iuran adalah Rp 1.000.',
            'durasi_bulan.required'  => 'Silakan pilih durasi arisan.',
            'tipe_skema.required'    => 'Silakan pilih tipe skema.',
        ]);

        try {
            SkemaArisan::create($validated);

            return redirect()->route('admin.skema.index')
                ->with('success', 'Skema "' . $request->nama_skema . '" berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal simpan skema: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }
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
        $skema = SkemaArisan::findOrFail($id);

        $validated = $request->validate([
            'nama_skema'    => 'required|string|max:100',
            'durasi_bulan'  => 'required|in:12,36',
            'tipe_skema'    => 'required|in:perorangan,kelompok',
            'nominal_iuran' => 'required|numeric|min:1000',
            'deskripsi'     => 'nullable|string'
        ], [
            'nama_skema.required' => 'Nama paket wajib diisi.',
            'nominal_iuran.min'   => 'Nominal iuran minimal Rp 1.000.',
        ]);

        try {
            $skema->update($validated);

            return redirect()->route('admin.skema.index')
                ->with('success', 'Perubahan skema berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal update skema ID ' . $id . ': ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    // HAPUS DATA
    public function destroy($id)
    {
        SkemaArisan::findOrFail($id)->delete();

        return redirect()->route('admin.skema.index')
            ->with('success', 'Skema berhasil dihapus');
    }
}
