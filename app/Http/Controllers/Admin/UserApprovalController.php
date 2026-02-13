<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserApprovalController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'peserta')
                    ->where('status', 'pending')
                    ->latest()
                    ->get();

        return view('admin.pending.index', compact('users'));
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'aktif']);

        return back()->with('success', 'Akun berhasil diaktifkan');
    }

    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'nonaktif']);

        return back()->with('success', 'Akun ditolak');
    }
}
