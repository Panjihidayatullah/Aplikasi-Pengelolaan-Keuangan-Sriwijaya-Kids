<?php

namespace App\Providers;

use App\Models\Notifikasi;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register custom Neon PostgreSQL connector
        $this->app->bind('db.connector.pgsql', function () {
            return new \App\Database\NeonPostgresConnector;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set PostgreSQL options for Neon.tech endpoint ID
        if (config('database.connections.pgsql.endpoint')) {
            putenv('PGOPTIONS=endpoint=' . config('database.connections.pgsql.endpoint'));
        }
        
        // Register observers to clear notification cache
        \App\Models\Pembayaran::observe(\App\Observers\NotificationCacheObserver::class);
        \App\Models\Siswa::observe(\App\Observers\NotificationCacheObserver::class);
        \App\Models\Pengeluaran::observe(\App\Observers\NotificationCacheObserver::class);
        
        $this->configureDefaults();
        $this->shareNotificationsData();
    }

    /**
     * Share notifications data with topbar layout
     */
    protected function shareNotificationsData(): void
    {
        view()->composer('layouts.topbar', function ($view) {
            if (!auth()->check()) {
                $view->with('recentNotifications', collect());
                $view->with('notificationCount', 0);
                return;
            }

            try {
                /** @var User $user */
                $user = auth()->user();
                $limit = $this->notificationLimitByRole($user);

                $notifications = Notifikasi::query()
                    ->where('user_id', $user->id)
                    ->latest('created_at')
                    ->limit($limit)
                    ->get()
                    ->map(fn (Notifikasi $notifikasi) => $this->transformTopbarNotification($notifikasi));

                $notificationCount = Notifikasi::query()
                    ->where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                $view->with('recentNotifications', $notifications);
                $view->with('notificationCount', $notificationCount);
            } catch (\Exception $e) {
                $view->with('recentNotifications', collect());
                $view->with('notificationCount', 0);
            }
        });
    }

    private function notificationLimitByRole(User $user): int
    {
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

    private function transformTopbarNotification(Notifikasi $notifikasi): array
    {
        [$iconBg, $iconColor, $iconPath] = $this->iconForNotificationType((string) $notifikasi->tipe);

        return [
            'type' => $notifikasi->tipe ?: 'umum',
            'title' => $notifikasi->judul,
            'description' => $notifikasi->isi,
            'amount' => $notifikasi->is_read ? 'Sudah dibaca' : 'Belum dibaca',
            'time' => $notifikasi->created_at,
            'url' => $this->resolveNotificationUrl($notifikasi),
            'icon_bg' => $iconBg,
            'icon_color' => $iconColor,
            'icon' => $iconPath,
        ];
    }

    private function iconForNotificationType(string $type): array
    {
        return match ($type) {
            'tugas' => ['bg-cyan-100', 'text-cyan-600', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586A2 2 0 0114 3.586L18.414 8A2 2 0 0119 9.414V19a2 2 0 01-2 2z'],
            'nilai' => ['bg-green-100', 'text-green-600', 'M9 17v-2m3 2v-4m3 4V9m3 10H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2z'],
            'pengumuman' => ['bg-amber-100', 'text-amber-600', 'M11 5.882V19.5M18 8.5v6a1 1 0 01-1.447.894L12 13H8v-4h4l4.553-2.394A1 1 0 0118 8.5z'],
            default => ['bg-gray-100', 'text-gray-600', 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
        };
    }

    private function resolveNotificationUrl(Notifikasi $notifikasi): string
    {
        $type = $notifikasi->tipe ?: 'all';

        return route('riwayat.index', [
            'type' => $type,
        ]);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
