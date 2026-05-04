# 🚀 Panduan Implementasi Sistem Keuangan Sekolah

Project ini adalah implementasi lengkap Sistem Keuangan Sekolah Kids Sriwijaya berdasarkan README.md. Karena ini adalah project yang sangat besar, implementasi dilakukan secara bertahap.

---

## ✅ Yang Sudah Diimplementasikan

### 1. Database Structure
✅ **7 Migrations terbuat:**
- `create_kelas_table.php` - Tabel kelas
- `create_siswa_table.php` - Tabel siswa
- `create_jenis_pembayaran_table.php` - Master jenis pembayaran
- `create_pembayaran_table.php` - Transaksi pembayaran  
- `create_jenis_pengeluaran_table.php` - Master jenis pengeluaran
- `create_pengeluaran_table.php` - Transaksi pengeluaran
- `create_aset_table.php` - Inventaris aset sekolah

### 2. Packages
✅ Di composer.json sudah ditambahkan:
- `spatie/laravel-permission` - RBAC
- Perlu tambahkan: `maatwebsite/excel`, `barryvdh/laravel-dompdf`

---

## 📋 Langkah-Langkah Implementasi Selanjutnya

### STEP 1: Install & Setup Packages

```bash
# 1. Install packages yang di-require
composer require maatwebsite/excel barryvdh/laravel-dompdf --no-interaction

# 2. Publish config Spatie Permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# 3. Jalankan migration Spatie (akan membuat tabel roles & permissions)
php artisan migrate

# 4. Clear cache
php artisan config:clear
php artisan cache:clear
```

### STEP 2: Buat Models

Buat models untuk setiap tabel dengan relationships:

**a. Model Kelas**
```bash
php artisan make:model Kelas
```

Edit `app/Models/Kelas.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'wali_kelas',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    public function siswaAktif()
    {
        return $this->hasMany(Siswa::class)->where('is_active', true);
    }
}
```

**b. Model Siswa**
```bash
php artisan make:model Siswa
```

Edit `app/Models/Siswa.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nama',
        'kelas_id',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'telepon',
        'email',
        'nama_ayah',
        'telepon_ayah',
        'nama_ibu',
        'telepon_ibu',
        'foto',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    // Accessors
    public function getJenisKelaminLengkapAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

**Lanjutkan untuk:**
- `JenisPembayaran.php`
- `Pembayaran.php`
- `JenisPengeluaran.php`
- `Pengeluaran.php`
- `Aset.php`

### STEP 3: Setup Spatie Permission

**Buat Seeder untuk Roles & Permissions:**
```bash
php artisan make:seeder RolePermissionSeeder
```

Edit `database/seeders/RolePermissionSeeder.php`:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            // Dashboard
            'view-dashboard',
            
            // Kelas
            'view-kelas',
            'create-kelas',
            'edit-kelas',
            'delete-kelas',
            
            // Siswa
            'view-siswa',
            'create-siswa',
            'edit-siswa',
            'delete-siswa',
            
            // Pembayaran
            'view-pembayaran',
            'create-pembayaran',
            'edit-pembayaran',
            'delete-pembayaran',
            
            // Pengeluaran
            'view-pengeluaran',
            'create-pengeluaran',
            'edit-pengeluaran',
            'delete-pengeluaran',
            
            // Aset
            'view-aset',
            'create-aset',
            'edit-aset',
            'delete-aset',
            
            // Laporan
            'view-laporan',
            'export-laporan',
            
            // Users
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles
        $adminRole = Role::create(['name' => 'Admin']);
        $bendaharaRole = Role::create(['name' => 'Bendahara']);
        $kepsekRole = Role::create(['name' => 'Kepala Sekolah']);

        // Admin - All permissions
        $adminRole->givePermissionTo(Permission::all());

        // Bendahara - Finance operations
        $bendaharaRole->givePermissionTo([
            'view-dashboard',
            'view-kelas',
            'view-siswa',
            'view-pembayaran',
            'create-pembayaran',
            'edit-pembayaran',
            'delete-pembayaran',
            'view-pengeluaran',
            'create-pengeluaran',
            'edit-pengeluaran',
            'delete-pengeluaran',
            'view-aset',
            'create-aset',
            'view-laporan',
            'export-laporan',
        ]);

        // Kepala Sekolah - View only
        $kepsekRole->givePermissionTo([
            'view-dashboard',
            'view-kelas',
            'view-siswa',
            'view-pembayaran',
            'view-pengeluaran',
            'view-aset',
            'view-laporan',
            'export-laporan',
        ]);

        // Create default users
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@sriwijayakidss.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('Admin');

        $bendahara = User::create([
            'name' => 'Bendahara',
            'email' => 'bendahara@sriwijayakidss.com',
            'password' => bcrypt('password'),
        ]);
        $bendahara->assignRole('Bendahara');

        $kepsek = User::create([
            'name' => 'Kepala Sekolah',
            'email' => 'kepsek@sriwijayakidss.com',
            'password' => bcrypt('password'),
        ]);
        $kepsek->assignRole('Kepala Sekolah');
    }
}
```

**Update User Model:**
Edit `app/Models/User.php`, tambahkan:
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    
    // ... rest of code
}
```

**Jalankan Seeder:**
```bash
php artisan db:seed --class=RolePermissionSeeder
```

### STEP 4: Buat Config File

**a. Create `config/finance.php`:**
```php
<?php

return [
    'school' => [
        'name' => 'Sekolah Kids Sriwijaya',
        'address' => 'Jl. Contoh No. 123, Palembang',
        'phone' => '(0711) 1234567',
        'email' => 'info@sriwijayakids.com',
        'logo' => 'images/logo.png',
    ],

    'finance' => [
        'currency' => 'IDR',
        'currency_symbol' => 'Rp',
        'decimal_places' => 0,
        'thousand_separator' => '.',
        'decimal_separator' => ',',
    ],

    'upload' => [
        'max_file_size' => 2048, // KB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'pdf'],
        'siswa_photo_path' => 'siswa/photos',
        'pengeluaran_bukti_path' => 'pengeluaran/bukti',
    ],

    'defaults' => [
        'academic_year' => '2024/2025',
        'semester' => 'Ganjil',
    ],
];
```

### STEP 5: Buat Helper Functions

**Create `app/Helpers/helpers.php`:**
```php
<?php

if (!function_exists('format_rupiah')) {
    function format_rupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('parse_rupiah')) {
    function parse_rupiah($rupiah)
    {
        return (float) str_replace([' Rp', '.', ','], ['', '', '.'], $rupiah);
    }
}

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

if (!function_exists('generate_transaction_code')) {
    function generate_transaction_code($prefix = 'TRX')
    {
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
```

**Autoload helper di `composer.json`:**
```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/Helpers/helpers.php"
    ]
},
```

**Regenerate autoload:**
```bash
composer dump-autoload
```

### STEP 6: Buat Controllers dengan Service Layer

**a. Dashboard Controller:**
```bash
php artisan make:controller DashboardController
```

**b. CRUD Controllers:**
```bash
php artisan make:controller KelasController --resource
php artisan make:controller SiswaController --resource
php artisan make:controller PembayaranController --resource
php artisan make:controller PengeluaranController --resource
php artisan make:controller AsetController --resource
php artisan make:controller LaporanController
```

**c. Services:**
Buat folder `app/Services/` dan file:
- `DashboardService.php`
- `PembayaranService.php`
- `PengeluaranService.php`
- `LaporanService.php`

### STEP 7: Setup Routes

Edit `routes/web.php`:
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\AsetController;
use App\Http\Controllers\LaporanController;

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');

    // Kelas
    Route::resource('kelas', KelasController::class);
    
    // Siswa
    Route::resource('siswa', SiswaController::class);
    
    // Pembayaran
    Route::resource('pembayaran', PembayaranController::class);
    
    // Pengeluaran
    Route::resource('pengeluaran', PengeluaranController::class);
    
    // Aset
    Route::resource('aset', AsetController::class);
    
    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/pemasukan', [LaporanController::class, 'pemasukan'])->name('pemasukan');
        Route::get('/pengeluaran', [LaporanController::class, 'pengeluaran'])->name('pengeluaran');
        Route::get('/rekap-bulanan', [LaporanController::class, 'rekapBulanan'])->name('rekap-bulanan');
        Route::get('/aset', [LaporanController::class, 'aset'])->name('aset');
        
        // Export
        Route::get('/export/pemasukan', [LaporanController::class, 'exportPemasukan'])->name('export.pemasukan');
        Route::get('/export/pengeluaran', [LaporanController::class, 'exportPengeluaran'])->name('export.pengeluaran');
    });
});
```

### STEP 8: Buat Seeders untuk Data Dummy

```bash
php artisan make:seeder KelasSeeder
php artisan make:seeder SiswaSeeder
php artisan make:seeder JenisPembayaranSeeder
php artisan make:seeder JenisPengeluaranSeeder
```

Update `DatabaseSeeder.php`:
```php
public function run(): void
{
    $this->call([
        RolePermissionSeeder::class,
        KelasSeeder::class,
        JenisPembayaranSeeder::class,
        JenisPengeluaranSeeder::class,
        SiswaSeeder::class,
    ]);
}
```

### STEP 9: Jalankan Migration & Seeder

```bash
# Jalankan semua migrasi
php artisan migrate

# Jalankan seeder
php artisan db:seed

# Atau sekaligus (hati-hati, akan reset database!)
php artisan migrate:fresh --seed
```

### STEP 10: Buat Views

Buat struktur folder views:
```
resources/views/
├── layouts/
│   └── app.blade.php (main layout)
├── dashboard.blade.php
├── kelas/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── siswa/
├── pembayaran/
├── pengeluaran/
├── aset/
└── laporan/
```

---

## 📦 Frontend Assets

### Install NPM Packages

```bash
npm install
npm install chart.js sweetalert2 select2 datatables.net datatables.net-bs5
```

### Build Assets

```bash
npm run dev        # Development
npm run build      # Production
```

---

## 🔐 Middleware untuk Permission

**Tambahkan di Controller:**
```php
public function __construct()
{
    $this->middleware('permission:view-siswa')->only(['index', 'show']);
    $this->middleware('permission:create-siswa')->only(['create', 'store']);
    $this->middleware('permission:edit-siswa')->only(['edit', 'update']);
    $this->middleware('permission:delete-siswa')->only(['destroy']);
}
```

---

## 🧪 Testing

```bash
# Test database connection
php artisan db:show

# Test roles & permissions
php artisan tinker
> User::first()->roles
> User::first()->permissions

# Test views
php artisan route:list
php artisan serve
```

---

## 📚 Dokumentasi Lengkap

Untuk detail code implementation:
1. **Models** - Lihat PANDUAN_DATABASE.md
2. **Service Layer** - Akan dibuat terpisah
3. **Controller Examples** - Akan dibuat terpisah
4. **View Components** - Perlu Blade templates lengkap
5. **Export Excel/PDF** - Gunakan maatwebsite/excel & dompdf

---

## 🚨 Important Notes

1. **Ini adalah project BESAR** (~50+ files, ~10,000+ lines of code)
2. **Implementasi penuh butuh waktu** (5-10 hari untuk 1 developer)
3. **Testing di setiap step** sangat penting
4. **Dokumentasi** setiap fitur
5. **Version control** dengan Git sangat disarankan

---

## ✅ Checklist Progress

- [x] Database migrations
- [x] Package requirements
- [ ] Models dengan relationships
- [ ] Spatie Permission setup
- [ ] Helper functions
- [ ] Config files
- [ ] Services layer
- [ ] Controllers
- [ ] Routes
- [ ] Views (Blade)
- [ ] Seeders dengan data dummy
- [ ] Excel & PDF exports
- [ ] Testing

---

## 🎯 Next Actions

Prioritas implementasi:
1. ✅ Selesaikan models
2. ✅ Setup Spatie Permission & roles
3. ✅ Buat helper functions
4. ✅ Buat config files
5. Implement 1 modul lengkap (misal: Kelas) sebagai template
6. Duplikasi untuk modul lainnya
7. Dashboard dengan Chart.js
8. Excel & PDF export functions
9. Testing & debugging
10. UI/UX polishing

---

**Estimasi waktu:** 5-10 hari full development
**Recommended:** Implementasi bertahap per modul

**Happy Coding! 🚀**
