<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserApprovalController extends Controller
{
    public function index()
    {
        $pendingUsers = User::where('is_approved', false)
            ->latest()
            ->paginate(15);
            
        return view('admin.approvals.index', compact('pendingUsers'));
    }

    public function approve(User $user)
    {
        $user->update(['is_approved' => true]);
        return redirect()->route('admin.approvals.index')->with('success', 'Akun ' . $user->name . ' (' . $user->role . ') berhasil disetujui.');
    }

    public function reject(User $user)
    {
        $user->delete(); // Soft delete or hard delete depending on needs. Let's hard delete so they can re-register if needed, or soft delete is safer.
        // User model doesn't use SoftDeletes natively unless added. Let's assume standard delete.
        return redirect()->route('admin.approvals.index')->with('success', 'Pendaftaran akun ' . $user->name . ' telah ditolak dan dihapus.');
    }
}
