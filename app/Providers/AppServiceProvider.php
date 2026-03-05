<?php

namespace App\Providers;

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
            if (auth()->check()) {
                try {
                    // Cache notifications for 5 minutes to improve performance
                    $notifications = cache()->remember('topbar_notifications', 300, function () {
                        $notifications = collect();

                        // Get latest payments (last 5)
                        $latestPayments = \App\Models\Pembayaran::with(['siswa', 'jenis'])
                            ->latest()
                            ->take(5)
                            ->get()
                            ->map(function ($payment) {
                                return [
                                    'type' => 'pembayaran',
                                    'title' => 'Pembayaran Baru Diterima',
                                    'description' => ($payment->siswa->nama ?? 'Siswa') . ' - ' . ($payment->jenis->nama ?? 'Pembayaran'),
                                    'amount' => 'Rp ' . number_format($payment->jumlah, 0, ',', '.'),
                                    'time' => $payment->created_at,
                                    'url' => route('pembayaran.show', $payment->id),
                                    'icon_bg' => 'bg-green-100',
                                    'icon_color' => 'text-green-600',
                                    'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                                ];
                            });

                        // Get latest students (last 5)
                        $latestStudents = \App\Models\Siswa::with('kelas')
                            ->latest()
                            ->take(5)
                            ->get()
                            ->map(function ($student) {
                                return [
                                    'type' => 'siswa',
                                    'title' => 'Siswa Baru Terdaftar',
                                    'description' => $student->nama . ' - ' . ($student->kelas->nama_kelas ?? 'Belum ada kelas'),
                                    'amount' => 'NIS: ' . $student->nis,
                                    'time' => $student->created_at,
                                    'url' => route('siswa.show', $student->id),
                                    'icon_bg' => 'bg-blue-100',
                                    'icon_color' => 'text-blue-600',
                                    'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
                                ];
                            });

                        // Get latest expenses (last 5)
                        $latestExpenses = \App\Models\Pengeluaran::with('jenis')
                            ->latest()
                            ->take(5)
                            ->get()
                            ->map(function ($expense) {
                                return [
                                    'type' => 'pengeluaran',
                                    'title' => 'Pengeluaran Baru Dicatat',
                                    'description' => $expense->keterangan ?? 'Pengeluaran',
                                    'amount' => 'Rp ' . number_format($expense->jumlah, 0, ',', '.'),
                                    'time' => $expense->created_at,
                                    'url' => route('pengeluaran.show', $expense->id),
                                    'icon_bg' => 'bg-red-100',
                                    'icon_color' => 'text-red-600',
                                    'icon' => 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6'
                                ];
                            });

                        // Merge and sort by created_at
                        return $notifications
                            ->merge($latestPayments)
                            ->merge($latestStudents)
                            ->merge($latestExpenses)
                            ->sortByDesc('time')
                            ->take(10)
                            ->values();
                    });

                    $view->with('recentNotifications', $notifications);
                    $view->with('notificationCount', $notifications->count());
                } catch (\Exception $e) {
                    // If any error occurs, show empty notifications
                    $view->with('recentNotifications', collect());
                    $view->with('notificationCount', 0);
                }
            } else {
                $view->with('recentNotifications', collect());
                $view->with('notificationCount', 0);
            }
        });
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
