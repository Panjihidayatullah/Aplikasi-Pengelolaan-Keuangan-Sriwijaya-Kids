<?php

/**
 * Format angka ke format Rupiah
 */
if (!function_exists('format_rupiah')) {
    function format_rupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

/**
 * Parse string Rupiah ke float
 */
if (!function_exists('parse_rupiah')) {
    function parse_rupiah($rupiah)
    {
        return (float) str_replace(['Rp', ' ', '.', ','], ['', '', '', '.'], $rupiah);
    }
}

/**
 * Format tanggal ke format Indonesia
 */
if (!function_exists('format_date_indonesia')) {
    function format_date_indonesia($date)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $split = explode('-', date('Y-m-d', strtotime($date)));
        return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    }
}

/**
 * Daftar bulan dalam bahasa Indonesia
 */
if (!function_exists('bulan_indonesia')) {
    function bulan_indonesia()
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    }
}

/**
 * Generate daftar tahun ajaran
 */
if (!function_exists('academic_years')) {
    function academic_years($range = 5)
    {
        $currentYear = date('Y');
        $years = [];
        
        for ($i = 0; $i < $range; $i++) {
            $year = $currentYear - $i;
            $years[] = $year . '/' . ($year + 1);
        }
        
        return $years;
    }
}

/**
 * Generate kode transaksi unik
 */
if (!function_exists('generate_transaction_code')) {
    function generate_transaction_code($prefix = 'TRX')
    {
        return $prefix . '-' . date('Ymd') . '-' . strtoupp(substr(uniqid(), -6));
    }
}

/**
 * Get status badge class
 */
if (!function_exists('get_status_badge')) {
    function get_status_badge($status)
    {
        $badges = [
            'lunas' => 'success',
            'pending' => 'warning',
            'dibatalkan' => 'danger',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            'aktif' => 'success',
            'tidak aktif' => 'secondary',
        ];
        
        return $badges[strtolower($status)] ?? 'secondary';
    }
}

/**
 * Format NIS dengan leading zeros
 */
if (!function_exists('format_nis')) {
    function format_nis($nis, $length = 6)
    {
        return str_pad($nis, $length, '0', STR_PAD_LEFT);
    }
}

/**
 * Check if user has any of the given roles
 */
if (!function_exists('has_any_role')) {
    function has_any_role($roles)
    {
        return auth()->check() && auth()->user()->hasAnyRole($roles);
    }
}

/**
 * Check if user has any of the given permissions
 */
if (!function_exists('has_any_permission')) {
    function has_any_permission($permissions)
    {
        return auth()->check() && auth()->user()->hasAnyPermission($permissions);
    }
}
