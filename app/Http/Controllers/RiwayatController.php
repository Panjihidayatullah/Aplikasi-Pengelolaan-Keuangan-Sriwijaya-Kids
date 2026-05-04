<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RiwayatController extends Controller
{
    /**
     * Display activity history with filters
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $allowedTypes = $this->allowedActivityTypes((int) $user->id);

        $requestedType = (string) $request->input('type', 'all');
        $type = array_key_exists($requestedType, $allowedTypes) ? $requestedType : 'all';
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('search');

        $notifications = Notifikasi::query()
            ->where('user_id', $user->id)
            ->with('user')
            ->when($type !== 'all', function ($q) use ($type) {
                if ($type === 'umum') {
                    $q->where(function ($sub) {
                        $sub->whereNull('tipe')
                            ->orWhere('tipe', '');
                    });

                    return;
                }

                $q->where('tipe', $type);
            })
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('judul', 'ILIKE', "%{$search}%")
                        ->orWhere('isi', 'ILIKE', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->paginate(10)
            ->through(function (Notifikasi $notifikasi) {
                return $this->transformNotificationToActivity($notifikasi);
            });

        return view('riwayat.index', [
            'activities' => $notifications,
            'type' => $type,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
            'allowedTypes' => $allowedTypes,
            'unreadCount' => Notifikasi::query()
                ->where('user_id', $user->id)
                ->where('is_read', false)
                ->count(),
        ]);
    }

    private function allowedActivityTypes(int $userId): array
    {
        $types = [
            'all' => 'Semua Aktivitas',
        ];

        $distinctTypes = Notifikasi::query()
            ->where('user_id', $userId)
            ->selectRaw("COALESCE(NULLIF(tipe, ''), 'umum') as tipe_notif")
            ->distinct()
            ->pluck('tipe_notif')
            ->filter()
            ->values();

        foreach ($distinctTypes as $notifType) {
            $types[$notifType] = $this->typeLabel((string) $notifType);
        }

        return $types;
    }

    private function transformNotificationToActivity(Notifikasi $notifikasi): array
    {
        [$iconBg, $iconColor, $iconPath] = $this->iconForNotificationType((string) $notifikasi->tipe);

        return [
            'id' => $notifikasi->id,
            'is_read' => (bool) $notifikasi->is_read,
            'type' => $notifikasi->tipe ?: 'umum',
            'type_label' => $this->typeLabel((string) ($notifikasi->tipe ?: 'umum')),
            'title' => $notifikasi->judul,
            'description' => $notifikasi->isi,
            'amount' => $notifikasi->is_read ? 'Sudah dibaca' : 'Belum dibaca',
            'time' => $notifikasi->created_at,
            'url' => $this->resolveNotificationUrl($notifikasi),
            'icon_bg' => $iconBg,
            'icon_color' => $iconColor,
            'icon' => $iconPath,
            'user' => optional($notifikasi->user)->name ?? 'System',
        ];
    }

    private function typeLabel(string $type): string
    {
        return match ($type) {
            'tugas' => 'Tugas',
            'nilai' => 'Nilai',
            'pengumuman' => 'Pengumuman',
            'pembayaran' => 'Pemasukan',
            'pengeluaran' => 'Pengeluaran',
            'siswa' => 'Siswa',
            'kelas' => 'Kelas',
            'aset' => 'Aset',
            'umum' => 'Umum',
            default => Str::headline($type),
        };
    }

    private function iconForNotificationType(string $type): array
    {
        return match ($type) {
            'tugas' => ['bg-cyan-100', 'text-cyan-600', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586A2 2 0 0114 3.586L18.414 8A2 2 0 0119 9.414V19a2 2 0 01-2 2z'],
            'nilai' => ['bg-green-100', 'text-green-600', 'M9 17v-2m3 2v-4m3 4V9m3 10H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2z'],
            'pengumuman' => ['bg-amber-100', 'text-amber-600', 'M11 5.882V19.5M18 8.5v6a1 1 0 01-1.447.894L12 13H8v-4h4l4.553-2.394A1 1 0 0118 8.5z'],
            'pembayaran' => ['bg-green-100', 'text-green-600', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            'pengeluaran' => ['bg-red-100', 'text-red-600', 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6'],
            'siswa' => ['bg-blue-100', 'text-blue-600', 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            'kelas' => ['bg-purple-100', 'text-purple-600', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            'aset' => ['bg-yellow-100', 'text-yellow-600', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            default => ['bg-gray-100', 'text-gray-600', 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
        };
    }

    private function resolveNotificationUrl(Notifikasi $notifikasi): string
    {
        if ($notifikasi->tipe === 'pembayaran' && $notifikasi->terkait_id && can_access('view pembayaran')) {
            return route('pembayaran.show', $notifikasi->terkait_id);
        }

        if ($notifikasi->tipe === 'pengeluaran' && $notifikasi->terkait_id && can_access('view pengeluaran')) {
            return route('pengeluaran.show', $notifikasi->terkait_id);
        }

        if ($notifikasi->tipe === 'tugas' && $notifikasi->terkait_id) {
            return route('akademik.lms.tugas.show', $notifikasi->terkait_id);
        }

        if ($notifikasi->tipe === 'nilai' && $notifikasi->terkait_id) {
            $tugasId = PengumpulanTugas::query()
                ->where('id', $notifikasi->terkait_id)
                ->value('tugas_id');

            if ($tugasId) {
                return route('akademik.lms.tugas.show', $tugasId);
            }
        }

        return route('riwayat.index');
    }
}
