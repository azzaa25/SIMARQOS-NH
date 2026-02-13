<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminManageController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        return view('admin.profile.index', compact('admin'));
    }

    public function manage(Request $request)
    {
        $admins = User::where('role', 'admin')->latest()->get();
        
        // Logika untuk Mode Edit: Jika ada ID di URL, ambil data user tersebut
        $editAdmin = null;
        if ($request->has('edit')) {
            $editAdmin = User::where('id_user', $request->edit)->where('role', 'admin')->first();
        }

        return view('admin.profile.manage', compact('admins', 'editAdmin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ], [
            'email.unique' => 'Email ini sudah terdaftar sebagai admin.',
            'password.min' => 'Password admin minimal 8 karakter.'
        ]);

        User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin',
            'status'   => 'aktif',
        ]);

        return redirect()->route('admin.manage.index')->with('success', 'Admin baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',id_user',
            'password' => 'nullable|min:8', // Password boleh kosong saat edit
        ]);

        $data = [
            'nama'  => $request->nama,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.manage.index')->with('success', 'Data admin berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Mencegah admin menghapus dirinya sendiri
        if (Auth::id() == $id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->route('admin.manage.index')->with('success', 'Admin berhasil dihapus.');
    }
}