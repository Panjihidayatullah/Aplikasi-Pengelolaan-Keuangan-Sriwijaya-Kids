<?php

return [
    /*
    |--------------------------------------------------------------------------
    | School Information
    |--------------------------------------------------------------------------
    |
    | Informasi sekolah untuk ditampilkan di aplikasi dan laporan
    |
    */
    'school' => [
        'name' => env('SCHOOL_NAME', 'Sriwijaya Kids'),
        'address' => env('SCHOOL_ADDRESS', 'Jl. Contoh No. 123, Palembang'),
        'phone' => env('SCHOOL_PHONE', '(0711) 1234567'),
        'email' => env('SCHOOL_EMAIL', 'info@sriwijayakids.com'),
        'logo' => env('SCHOOL_LOGO', 'images/Logo_SriwijayaKids.png'),
        'website' => env('SCHOOL_WEBSITE', 'https://sriwijayakids.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Finance Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan format mata uang dan angka
    |
    */
    'finance' => [
        'currency' => 'IDR',
        'currency_symbol' => 'Rp',
        'decimal_places' => 0,
        'thousand_separator' => '.',
        'decimal_separator' => ',',
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Pengaturan upload file
    |
    */
    'upload' => [
        'max_file_size' => 2048, // KB (2MB)
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'pdf'],
        'siswa_photo_path' => 'siswa/photos',
        'pengeluaran_bukti_path' => 'pengeluaran/bukti',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    |
    | Nilai default untuk tahun ajaran dan semester
    |
    */
    'defaults' => [
        'academic_year' => '2025/2026',
        'semester' => 'Ganjil', // Ganjil atau Genap
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Jumlah data per halaman
    |
    */
    'pagination' => [
        'per_page' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Prefixes
    |--------------------------------------------------------------------------
    |
    | Prefix untuk kode transaksi
    |
    */
    'transaction_prefixes' => [
        'pembayaran' => 'PMB',
        'pengeluaran' => 'PNG',
    ],

    /*
    |--------------------------------------------------------------------------
    | Report Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan untuk laporan
    |
    */
    'report' => [
        'header_height' => 80, // pixels
        'footer_text' => 'Dicetak oleh Sistem Keuangan Sriwijaya Kids',
    ],
];
