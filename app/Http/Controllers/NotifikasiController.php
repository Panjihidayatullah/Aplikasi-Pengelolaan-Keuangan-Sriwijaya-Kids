<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Display a listing of the resource (for current user).
     */
    public function index()
    {
        return redirect()->route('riwayat.index');
    }

    /**
     * Get unread notifications (API endpoint)
     */
    public function getUnread()
    {
        $limit = $this->notificationLimitByRole();

        $unread = auth()->user()->notifikasi()
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'judul' => $notif->judul,
                    'isi' => $notif->isi,
                    'tipe' => $notif->tipe,
                    'at' => $notif->created_at->diffForHumans(),
                ];
            });

        return response()->json($unread);
    }

    private function notificationLimitByRole(): int
    {
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            return 12;
        }

        if ($user->hasRole('Kepala Sekolah')) {
            return 10;
        }

        if ($user->hasRole('Bendahara')) {
            return 8;
        }

        if ($user->hasRole('Guru')) {
            return 7;
        }

        if ($user->hasRole('Siswa')) {
            return 5;
        }

        return 6;
    }

    /**
     * Mark as read
     */
    public function markAsRead(Request $request, Notifikasi $notifikasi)
    {
        if ($notifikasi->user_id != auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            abort(403);
        }

        $notifikasi->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead(Request $request)
    {
        auth()->user()->notifikasi()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'dibaca_pada' => now(),
            ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    /**
     * Delete notification
     */
    public function destroy(Notifikasi $notifikasi)
    {
        if ($notifikasi->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notifikasi->delete();
        return response()->json(['success' => true]);
    }
}
