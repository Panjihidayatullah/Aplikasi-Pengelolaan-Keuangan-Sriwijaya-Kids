# 🎓 Sistem Pengelolaan Keuangan Sekolah Kids Sriwijaya

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap)

## 📖 Deskripsi

Sistem Pengelolaan Keuangan Sekolah Kids Sriwijaya adalah aplikasi web berbasis Laravel yang dirancang khusus untuk mengelola aspek keuangan sekolah secara efisien, transparan, dan profesional. Aplikasi ini menyediakan fitur lengkap untuk pengelolaan data siswa, pembayaran SPP dan biaya sekolah lainnya, pencatatan pengeluaran, manajemen aset, serta laporan keuangan yang komprehensif.

Sistem ini dibangun dengan arsitektur **clean code**, mengikuti **best practices Laravel**, dan menggunakan **Service Layer Pattern** untuk memisahkan business logic dari controller, sehingga mudah di-maintain dan di-scale.

---

## ✨ Fitur Utama

### 1. 📊 Dashboard Interaktif
- **Statistik Real-time**
  - Total pemasukan bulanan
  - Total pengeluaran bulanan
  - Saldo berjalan (all time)
  - Jumlah siswa aktif
- **Grafik Chart.js**
  - Grafik perbandingan pemasukan vs pengeluaran per bulan
  - Pie chart distribusi metode pembayaran
- **Recent Transactions**
  - 5 transaksi pembayaran terbaru
  - Quick overview aktivitas keuangan

### 2. 👥 Manajemen Data Master

#### a. Manajemen Kelas
- CRUD (Create, Read, Update, Delete) data kelas
- Informasi lengkap:
  - Nama kelas (contoh: TK A, TK B, SD Kelas 1)
  - Tingkat kelas
  - Wali kelas
- Filter dan pencarian data kelas
- Soft delete (data tidak terhapus permanen)

#### b. Manajemen Siswa
- CRUD data siswa lengkap
- Informasi siswa:
  - NIS (Nomor Induk Siswa)
  - Nama lengkap
  - Jenis kelamin
  - Alamat lengkap
  - Kelas
  - Status (Aktif/Tidak Aktif)
- **Filter & Pencarian:**
  - Filter berdasarkan kelas
  - Filter berdasarkan status
  - Pencarian nama/NIS
- Soft delete untuk keamanan data
- Relasi dengan kelas dan pembayaran

#### c. Jenis Pembayaran
- Master data jenis pembayaran
- Contoh: SPP, Uang Pangkal, Seragam, Buku, Kegiatan
- Pengaturan nominal default per jenis
- Tipe pembayaran (Bulanan, Tahunan, Sekali)

#### d. Jenis Pengeluaran
- Master kategori pengeluaran sekolah
- Contoh: Gaji, Operasional, Pemeliharaan, Utilitas

### 3. 💰 Transaksi Keuangan

#### a. Pembayaran Siswa (Pemasukan)
- **Pencatatan Pembayaran:**
  - Pilih siswa (dengan autocomplete)
  - Pilih jenis pembayaran
  - Input tanggal bayar
  - Bulan dan tahun periode pembayaran
  - Jumlah pembayaran
  - Metode pembayaran (Tunai, Transfer Bank, QRIS)
  - Keterangan tambahan
- **Filter Pembayaran:**
  - Filter by siswa
  - Filter by jenis pembayaran
  - Filter by bulan dan tahun
  - Filter by metode bayar
  - Filter by range tanggal
- **View Detail:**
  - Detail lengkap pembayaran
  - Informasi siswa dan kelas
  - User yang mencatat pembayaran
  - Timestamp created/updated
- **Permission-based Access:**
  - View, Create, Edit, Delete dengan permission

#### b. Pengeluaran
- **Pencatatan Pengeluaran:**
  - Pilih jenis pengeluaran
  - Input tanggal pengeluaran
  - Jumlah pengeluaran
  - Keterangan detail
  - Upload bukti pengeluaran (JPG, PNG, PDF)
- **Filter Pengeluaran:**
  - Filter by jenis pengeluaran
  - Filter by range tanggal
- **Upload & Download Bukti:**
  - Maksimal ukuran file 2MB
  - Format: JPG, JPEG, PNG, PDF
  - Preview dan download bukti
- Permission-based access control
- Soft delete

### 4. 🏢 Manajemen Aset

- **Data Inventaris Aset:**
  - Nama aset
  - Kategori (Elektronik, Furniture, Kendaraan, Bangunan, dll)
  - Tanggal perolehan
  - Harga perolehan
  - Kondisi (Baik, Rusak Ringan, Rusak Berat)
  - Lokasi/Ruangan
- **Tracking:**
  - Monitoring kondisi aset
  - Total nilai aset
  - User yang mencatat
- CRUD lengkap dengan permission

### 5. 📑 Sistem Laporan Lengkap

#### a. Laporan Pemasukan
- **Filter Options:**
  - Filter berdasarkan jenis pembayaran
  - Filter berdasarkan bulan
  - Filter berdasarkan siswa
  - Range tanggal custom
- **Export:**
  - Export ke Excel (.xlsx)
  - Export ke PDF (dengan header sekolah)
- Tampilan tabel lengkap dengan detail siswa, kelas, jenis pembayaran

#### b. Laporan Pengeluaran
- **Filter Options:**
  - Filter berdasarkan jenis pengeluaran
  - Filter berdasarkan bulan
  - Range tanggal custom
- **Export:**
  - Export ke Excel (.xlsx)
  - Export ke PDF (dengan header sekolah)
- Tampilan tabel lengkap dengan bukti pengeluaran

#### c. Rekap Bulanan
- Rekap pemasukan dan pengeluaran per bulan
- Total pemasukan per jenis pembayaran
- Total pengeluaran per kategori
- Selisih (surplus/defisit)
- Export Excel dan PDF

#### d. Laporan Aset
- Daftar lengkap aset sekolah
- Nilai total per kategori
- Kondisi aset
- Export Excel dan PDF

### 6. 🔐 Keamanan & Authorization

#### a. Role-Based Access Control (RBAC)
Menggunakan **Spatie Laravel Permission** dengan 3 role utama:

**1. Admin (Full Access)**
- Semua permission
- CRUD semua data
- Manajemen users
- View activity logs

**2. Bendahara**
- View kelas dan siswa (read-only)
- Full CRUD pembayaran
- Full CRUD pengeluaran
- View dan create aset
- View dan export laporan
- Access dashboard

**3. Kepala Sekolah (View Only)**
- View semua data (read-only)
- View dan export laporan
- Access dashboard untuk monitoring

#### b. Features Security
- Authentication dengan Laravel Breeze
- Email verification
- Password hashing (bcrypt)
- CSRF protection
- SQL injection protection
- XSS protection
- Soft deletes untuk data penting

### 7. 🎨 User Interface & UX

- **Responsive Design:**
  - Bootstrap 5.3
  - Mobile-friendly
  - Tablet-friendly
  - Desktop optimized
- **Modern UI:**
  - Clean dan professional
  - Consistent design system
  - Intuitive navigation
  - User-friendly forms
- **Interactive Elements:**
  - DataTables untuk tabel interaktif
  - Chart.js untuk visualisasi data
  - SweetAlert2 untuk notifikasi
  - Select2 untuk dropdown advanced
  - Date picker untuk input tanggal

### 8. 📝 Activity Logging

- Log aktivitas user (opsional)
- Tracking perubahan data
- Audit trail untuk transparansi

---

## 🛠️ Technology Stack

### Backend
- **Framework:** Laravel 11.x
- **Language:** PHP 8.2+
- **Database:** MySQL 8.0
- **Authentication:** Laravel Breeze
- **Authorization:** Spatie Laravel Permission

### Frontend
- **CSS Framework:** Bootstrap 5.3
- **JavaScript:** Vanilla JS + jQuery
- **Charts:** Chart.js 4.x
- **Tables:** DataTables
- **Icons:** Font Awesome 6
- **Notifications:** SweetAlert2

### Libraries & Packages
- **maatwebsite/excel** - Excel export/import
- **barryvdh/laravel-dompdf** - PDF generation
- **spatie/laravel-permission** - Role & Permission management
- **laravel/tinker** - Artisan REPL

### Architecture Pattern
- **Service Layer Pattern** - Memisahkan business logic dari controller
- **Form Request Validation** - Validasi input terstruktur
- **Resource Controller** - RESTful pattern
- **Eloquent ORM** - Database abstraction
- **Repository Pattern** (Optional untuk scale up)

---

## 📋 Persyaratan Sistem

### Minimum Requirements
- PHP >= 8.2
- MySQL >= 8.0 atau MariaDB >= 10.3
- Composer 2.x
- Node.js >= 18.x (untuk development)
- Web Server (Apache/Nginx)

### Recommended (Development)
- XAMPP atau Laragon (Windows)
- MAMP (macOS)
- Docker dengan Laravel Sail

### PHP Extensions Required
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- Tokenizer
- XML
- GD atau Imagick

---

## 🚀 Instalasi

### 1. Clone atau Download Project
```bash
cd "c:\laragon\www\Sriwijaya Kids"
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Setup Environment
```bash
# Copy file .env.example ke .env (Windows)
copy .env.example .env

# Atau untuk Linux/Mac
cp .env.example .env
```

### 4. Konfigurasi Database
Edit file `.env` dan sesuaikan:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sriwijaya_kids_finance
DB_USERNAME=root
DB_PASSWORD=
```

**Buat database di MySQL:**
```sql
CREATE DATABASE sriwijaya_kids_finance;
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Migrasi Database & Seeder
```bash
# Jalankan migrasi
php artisan migrate

# Jalankan seeder (data dummy + roles)
php artisan db:seed
```

Seeder akan membuat:
- 3 role (Admin, Bendahara, Kepala Sekolah)
- Semua permissions
- 3 user default
- 10 kelas
- 50 siswa dummy
- 20 jenis pembayaran
- 100 pembayaran dummy
- 80 pengeluaran dummy
- 30 aset dummy

### 7. Create Storage Link
```bash
php artisan storage:link
```

### 8. Clear Cache (Optional)
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 9. Jalankan Development Server
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

---

## 👤 Default User Account

Setelah seeder dijalankan, gunakan akun berikut:

### 👨‍💼 Admin
```
Email: admin@sriwijayakids.com
Password: password
```
**Access:** Full Control

### 💰 Bendahara
```
Email: bendahara@sriwijayakids.com
Password: password
```
**Access:** Finance Operations

### 🎓 Kepala Sekolah
```
Email: kepsek@sriwijayakids.com
Password: password
```
**Access:** View Only & Reports

> ⚠️ **Penting:** Ganti password default setelah login pertama kali!

---

## 📁 Struktur Project

```
Sriwijaya Kids/
├── app/
│   ├── Exports/
│   │   ├── PemasukanExport.php       # Export pemasukan ke Excel
│   │   └── PengeluaranExport.php     # Export pengeluaran ke Excel
│   ├── Helpers/
│   │   └── helpers.php               # Helper functions (format_rupiah, dll)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AsetController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── KelasController.php
│   │   │   ├── LaporanController.php
│   │   │   ├── PembayaranController.php
│   │   │   ├── PengeluaranController.php
│   │   │   ├── ProfileController.php
│   │   │   └── SiswaController.php
│   │   ├── Middleware/              # Custom middleware
│   │   └── Requests/                # Form validation requests
│   ├── Models/
│   │   ├── Aset.php
│   │   ├── JenisPembayaran.php
│   │   ├── JenisPengeluaran.php
│   │   ├── Kelas.php
│   │   ├── Pembayaran.php
│   │   ├── Pengeluaran.php
│   │   ├── Siswa.php
│   │   └── User.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Services/
│       ├── DashboardService.php     # Business logic dashboard
│       ├── LaporanService.php       # Business logic laporan
│       ├── PembayaranService.php    # Business logic pembayaran
│       └── PengeluaranService.php   # Business logic pengeluaran
├── config/
│   ├── app.php
│   ├── database.php
│   ├── finance.php                  # Konfigurasi finance & sekolah
│   └── permission.php               # Konfigurasi Spatie Permission
├── database/
│   ├── factories/                   # Model factories untuk testing
│   ├── migrations/                  # Database migrations
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── RolePermissionSeeder.php # Seeder roles & permissions
├── public/
│   ├── css/
│   ├── js/
│   └── storage/                     # Symbolic link ke storage
├── resources/
│   └── views/
│       ├── auth/                    # Login, register pages
│       ├── layouts/
│       │   └── app.blade.php        # Main layout
│       ├── aset/                    # Views aset
│       ├── dashboard.blade.php
│       ├── kelas/                   # Views kelas
│       ├── laporan/                 # Views laporan
│       ├── pembayaran/              # Views pembayaran
│       ├── pengeluaran/             # Views pengeluaran
│       ├── profile/                 # Views profile
│       └── siswa/                   # Views siswa
├── routes/
│   ├── auth.php                     # Authentication routes
│   ├── console.php
│   └── web.php                      # Web routes
├── storage/
│   ├── app/
│   │   ├── public/
│   │   │   ├── pengeluaran/bukti/  # Upload bukti pengeluaran
│   │   │   └── siswa/photos/       # Upload foto siswa (future)
│   │   └── private/
│   ├── framework/
│   └── logs/
├── tests/
│   └── Feature/                     # Feature tests
├── .env                             # Environment configuration
├── .env.example                     # Environment template
├── composer.json
├── package.json
├── phpunit.xml
├── INSTALL.md                       # Installation guide
├── QUICKSTART.md                    # Quick start guide
└── README.md                        # This file
```

---

## 🔧 Konfigurasi

### Konfigurasi Finance & Sekolah

Edit `config/finance.php`:

```php
'school' => [
    'name' => 'Sekolah Kids Sriwijaya',
    'address' => 'Jl. Contoh No. 123, Palembang',
    'phone' => '(0711) 1234567',
    'email' => 'info@sriwijayakids.com',
    'logo' => 'images/logo.png', // Place logo in public/images/
],

'finance' => [
    'currency' => 'IDR',
    'currency_symbol' => 'Rp',
    'decimal_places' => 0,
    'thousand_separator' => '.',
    'decimal_separator' => ',',
],
```

### Upload Configuration

Edit `config/finance.php`:

```php
'upload' => [
    'max_file_size' => 2048, // KB
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'pdf'],
    'siswa_photo_path' => 'siswa/photos',
    'pengeluaran_bukti_path' => 'pengeluaran/bukti',
],
```

### Tahun Ajaran & Semester

Edit `config/finance.php`:

```php
'defaults' => [
    'academic_year' => '2024/2025',
    'semester' => 'Ganjil', // Ganjil atau Genap
],
```

---

## 🎯 Use Cases

### 1. Pencatatan Pembayaran SPP
1. Login sebagai **Bendahara**
2. Menu **Pembayaran** → **Tambah Pembayaran**
3. Pilih siswa, jenis pembayaran "SPP"
4. Input bulan, tahun, dan nominal
5. Pilih metode bayar (Tunai/Transfer/QRIS)
6. **Simpan**

### 2. Pencatatan Pengeluaran Operasional
1. Login sebagai **Bendahara** atau **Admin**
2. Menu **Pengeluaran** → **Tambah Pengeluaran**
3. Pilih jenis pengeluaran (contoh: Gaji Guru)
4. Input tanggal dan nominal
5. Isi keterangan detail
6. Upload bukti (nota/kuitansi)
7. **Simpan**

### 3. Export Laporan Bulanan
1. Login sebagai **Kepala Sekolah**, **Bendahara**, atau **Admin**
2. Menu **Laporan** → **Rekap Bulanan**
3. Pilih bulan dan tahun
4. Klik **Export to Excel** atau **Export to PDF**
5. File otomatis terdownload

### 4. Monitoring Dashboard
1. Login dengan role apapun
2. Otomatis masuk ke **Dashboard**
3. Lihat:
   - Total pemasukan bulan ini
   - Total pengeluaran bulan ini
   - Saldo berjalan
   - Grafik bulanan
   - Transaksi terbaru

### 5. Manajemen Aset Sekolah
1. Login sebagai **Admin** atau **Bendahara**
2. Menu **Aset** → **Tambah Aset**
3. Input: nama aset, kategori, tanggal & harga perolehan
4. Pilih kondisi dan lokasi
5. **Simpan**

---

## 📊 Helper Functions

Aplikasi dilengkapi dengan helper functions di `app/Helpers/helpers.php`:

```php
// Format angka ke Rupiah
format_rupiah(150000); // Output: Rp 150.000

// Parse Rupiah ke float
parse_rupiah('Rp 150.000'); // Output: 150000

// Get status badge class
get_status_badge('lunas'); // Output: 'success'
get_status_badge('pending'); // Output: 'warning'

// Format tanggal Indonesia
format_date_indonesia('2024-03-15'); // Output: 15 Maret 2024

// Daftar tahun ajaran
academic_years(); // Output: ['2021/2022', '2022/2023', ...]

// Daftar bulan Indonesia
bulan_indonesia(); // Output: ['Januari', 'Februari', ...]

// Format NIS dengan leading zeros
format_nis(123); // Output: '000123'
```

---

## 🧪 Testing

### Menjalankan Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=PembayaranTest

# Run with coverage
php artisan test --coverage
```

### Test Coverage

- ✅ Feature Test untuk Pembayaran (CRUD)
- ✅ Feature Test untuk Pengeluaran (CRUD)
- ✅ Factory untuk semua models
- ✅ Seeder dengan data dummy lengkap

---

## 📦 Deployment

### Production Checklist

1. **Set Environment (.env)**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   ```

2. **Optimize Performance**
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

3. **Set File Permissions (Linux)**
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

4. **Setup SSL Certificate**
   - Gunakan Let's Encrypt atau SSL provider lainnya

5. **Setup Cron Job (untuk scheduled tasks)**
   ```bash
   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
   ```

6. **Setup Backup Database**
   - Automated daily backup ke cloud storage

---

## 🐛 Troubleshooting

### Error: SQLSTATE[HY000] [2002]
**Solusi:** Pastikan MySQL service sudah running

### Error: Class 'Spatie\Permission\...' not found
**Solusi:** 
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### Storage Link Error
**Solusi:**
```bash
# Windows
rmdir public\storage
php artisan storage:link

# Linux/Mac
rm public/storage
php artisan storage:link
```

### Migration Error: Foreign Key Constraint
**Solusi:**
```bash
php artisan migrate:fresh --seed
```

### Permission Denied Error
**Solusi (Linux):**
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

---

## 🔮 Future Features (Roadmap)

- [ ] **Notifikasi & Reminder**
  - Email reminder tunggakan
  - WhatsApp notification (via API)
  - Push notification
  
- [ ] **Dashboard Enhancement**
  - More advanced analytics
  - Prediksi cash flow
  - Comparison year-over-year
  
- [ ] **Siswa Portal**
  - Parent login
  - View tagihan
  - History pembayaran
  - Download receipt
  
- [ ] **Advanced Reporting**
  - Custom report builder
  - Scheduled email reports
  - Data visualization dashboard
  
- [ ] **API Integration**
  - RESTful API
  - Mobile app support
  - Third-party integration
  
- [ ] **Accounting Module**
  - Double-entry bookkeeping
  - Journal entries
  - Balance sheet & Income statement
  
- [ ] **Backup & Recovery**
  - Automated database backup
  - Cloud storage integration
  - Point-in-time recovery

- [ ] **Multi-tenant Support**
  - Support untuk multiple schools
  - Centralized management

---

## 🤝 Contributing

Kontribusi sangat diterima! Silakan:
1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buka Pull Request

---

## 📝 Changelog

### Version 1.0.0 (2024-01-15)
- ✅ Initial release
- ✅ Dashboard dengan statistik dan grafik interaktif
- ✅ CRUD lengkap: Kelas, Siswa, Pembayaran, Pengeluaran, Aset
- ✅ Sistem laporan lengkap dengan export Excel & PDF
- ✅ Role-based access control (Admin, Bendahara, Kepala Sekolah)
- ✅ Responsive design dengan Bootstrap 5
- ✅ Upload bukti pengeluaran
- ✅ Helper functions untuk format Rupiah dan tanggal
- ✅ Service Layer Pattern untuk business logic
- ✅ Soft deletes pada data penting

---

## 📞 Support & Contact

Jika ada pertanyaan atau butuh bantuan:

- **Email:** admin@sriwijayakids.com
- **Phone:** (0711) 1234567
- **Website:** https://sriwijayakids.com

---

## 📄 License

Project ini dilisensikan di bawah [MIT License](LICENSE).

Anda bebas untuk:
- ✅ Menggunakan untuk keperluan pribadi
- ✅ Menggunakan untuk keperluan komersial
- ✅ Memodifikasi source code
- ✅ Mendistribusikan ulang

---

## 🙏 Acknowledgments

- Laravel Framework Team
- Spatie  Laravel Permission Package
- Bootstrap Team
- Chart.js Team
- DomPDF Team
- Maatwebsite Excel Package
- Semua open source contributors

---

## 👨‍💻 Author

**Sistem Pengelolaan Keuangan Sekolah Kids Sriwijaya**

Dikembangkan dengan ❤️ untuk Sekolah Kids Sriwijaya

**Version:** 1.0.0  
**Last Updated:** February 27, 2026

---

## 📚 Dokumentasi Tambahan

- [INSTALL.md](INSTALL.md) - Panduan instalasi lengkap
- [QUICKSTART.md](QUICKSTART.md) - Quick start guide
- [PROJECT_STATUS.md](PROJECT_STATUS.md) - Status pengembangan project
- [SUMMARY.md](SUMMARY.md) - Technical summary
- [CHANGELOG.md](CHANGELOG.md) - Riwayat perubahan

---

## 🎓 Untuk Tugas Akhir / Skripsi

Sistem ini **siap dipresentasikan** sebagai Tugas Akhir dengan fitur:

✅ **Complete CRUD Operations** - Semua modul lengkap  
✅ **Professional UI/UX** - Design modern dan responsive  
✅ **Role-Based Authorization** - 3 level user dengan permissions  
✅ **Interactive Dashboard** - Real-time statistics dengan Chart.js  
✅ **Export to Excel & PDF** - Fitur export laporan lengkap  
✅ **Audit Trail System** - Activity logging  
✅ **Comprehensive Documentation** - Dokumentasi lengkap  
✅ **Unit & Feature Tests** - Testing coverage  
✅ **Clean Code Architecture** - Service Layer Pattern  
✅ **Production Ready** - Siap deploy ke production  

---

## 📖 Referensi

- [Laravel Documentation](https://laravel.com/docs/11.x)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/introduction)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)
- [Chart.js Documentation](https://www.chartjs.org/docs/latest/)
- [Laravel Excel Documentation](https://docs.laravel-excel.com/)
- [DomPDF Documentation](https://github.com/barryvdh/laravel-dompdf)

---

<div align="center">

### 🌟 **Jangan lupa beri star jika project ini bermanfaat!** 🌟

**🎉 Happy Coding! 🎉**

*Sistem ini dibuat dengan standar profesional dan siap digunakan untuk production*

</div>
