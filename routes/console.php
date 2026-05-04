<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Penggajian Otomatis - Berjalan setiap hari jam 00:00
Schedule::command('app:bayar-gaji-otomatis')->daily();
