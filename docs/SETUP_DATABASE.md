# 📚 Panduan Setup Database PostgreSQL

Panduan lengkap untuk setup database PostgreSQL pada project Laravel ini.

---

## 🎯 Pilihan Database

Project ini mendukung PostgreSQL. Anda bisa menggunakan:

1. **PostgreSQL Lokal** (via Laragon/XAMPP)
2. **PostgreSQL Cloud** (Neon.tech, Supabase, Railway, dll)

---

## 🔧 A. Setup PostgreSQL di Laragon (Lokal)

### 1. Install PostgreSQL di Laragon

**Via Laragon Menu:**
- Klik kanan icon Laragon → **Menu** → **PostgreSQL** → Download versi terbaru
- Atau download manual dari: https://www.postgresql.org/download/windows/

**Install Langkah:**
1. Ekstrak PostgreSQL ke folder: `C:\laragon\bin\postgresql\`
2. Restart Laragon

### 2. Aktifkan Extension PostgreSQL di PHP

**Buka php.ini:**
- Klik kanan icon Laragon → **PHP** → **php.ini**

**Hapus tanda `;` (uncomment) pada baris:**
```ini
;extension=pdo_pgsql
;extension=pgsql
```

Menjadi:
```ini
extension=pdo_pgsql
extension=pgsql
```

**Simpan dan Restart:**
- Laragon → Stop All → Start All

**Verifikasi:**
```bash
php -m | Select-String -Pattern "pdo_pgsql"
```
Harus muncul: `pdo_pgsql` dan `pgsql`

### 3. Buat Database

**Via pgAdmin:**
1. Buka pgAdmin (biasanya include dengan PostgreSQL)
2. Login dengan password yang dibuat saat install
3. Klik kanan **Databases** → **Create** → **Database**
4. Nama: `sriwijaya_kidss` → Save

**Via Command Line:**
```bash
psql -U postgres
CREATE DATABASE sriwijaya_kidss;
\q
```

### 4. Konfigurasi `.env`

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sriwijaya_kidss
DB_USERNAME=postgres
DB_PASSWORD=your_password
DB_SSLMODE=prefer
```

---

## ☁️ B. Setup PostgreSQL Cloud (Neon.tech)

### 1. Daftar Neon.tech

1. Buka: https://neon.tech
2. Sign up (gratis) via GitHub/Google/Email
3. Create New Project

### 2. Dapat Connection Details

Setelah project dibuat, akan mendapat:
- **Host**: `ep-xxxxx.region.aws.neon.tech`
- **Database**: `neondb`
- **Username**: `neondb_owner`
- **Password**: `npg_xxxxx`
- **Port**: `5432`

### 3. Aktifkan Extension PostgreSQL di PHP

**Buka php.ini:**
```bash
# Via Laragon
Klik kanan Laragon → PHP → php.ini
```

**Uncomment extension:**
```ini
extension=pdo_pgsql
extension=pgsql
```

**Restart Laragon**

### 4. Konfigurasi `.env`

**Untuk Direct Connection (Non-Pooler):**
```env
DB_CONNECTION=pgsql
DB_HOST=ep-xxxxx.region.aws.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=npg_xxxxx
DB_SSLMODE=require
DB_ENDPOINT=ep-xxxxx
```

**Catatan:** Endpoint ID adalah bagian pertama dari hostname (sebelum tanda `.`)

### 5. Setup Custom Connector (Sudah Ada)

Project ini sudah include custom connector untuk Neon.tech:
- `app/Database/NeonPostgresConnector.php`
- Sudah di-register di `AppServiceProvider.php`

---

## 🚀 C. Menjalankan Migrasi

### 1. Test Koneksi Database

```bash
php artisan config:clear
php artisan db:show
```

Jika berhasil, akan muncul info database.

### 2. Jalankan Migrasi

**Pertama kali (buat semua tabel):**
```bash
php artisan migrate
```

**Reset dan buat ulang:**
```bash
php artisan migrate:fresh
```

**Reset + isi data dummy:**
```bash
php artisan migrate:fresh --seed
```

### 3. Rollback Migrasi

**Rollback 1 batch terakhir:**
```bash
php artisan migrate:rollback
```

**Rollback semua:**
```bash
php artisan migrate:reset
```

---

## 📊 D. Seeder (Data Dummy)

### 1. Jalankan Seeder

**Semua seeder:**
```bash
php artisan db:seed
```

**Seeder tertentu:**
```bash
php artisan db:seed --class=DatabaseSeeder
```

### 2. Buat Seeder Baru

```bash
php artisan make:seeder StudentSeeder
```

Edit file: `database/seeders/StudentSeeder.php`

### 3. Daftarkan Seeder

Edit `DatabaseSeeder.php`:
```php
public function run(): void
{
    $this->call([
        UserSeeder::class,
        StudentSeeder::class,
        // tambah seeder lain...
    ]);
}
```

---

## 🔍 E. Verifikasi Database

### 1. Check Koneksi

```bash
php artisan db:show
```

### 2. Lihat Tabel

```bash
php artisan db:table users
```

### 3. Query via Tinker

```bash
php artisan tinker
```

Kemudian:
```php
// Hitung data
User::count()

// Lihat semua
User::all()

// Lihat 5 pertama
User::take(5)->get()
```

---

## ⚙️ F. Config Database Lanjutan

### File: `config/database.php`

**Untuk Custom Options:**
```php
'pgsql' => [
    'driver' => 'pgsql',
    'url' => env('DB_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => env('DB_CHARSET', 'utf8'),
    'prefix' => '',
    'prefix_indexes' => true,
    'search_path' => 'public',
    'sslmode' => env('DB_SSLMODE', 'prefer'),
    'endpoint' => env('DB_ENDPOINT'), // Khusus Neon.tech
    'options' => [],
],
```

---

## 🐛 G. Troubleshooting

### Error: "could not find driver"

**Solusi:**
1. Pastikan extension PostgreSQL aktif di php.ini
2. Restart web server/Laragon
3. Verifikasi: `php -m | Select-String -Pattern "pgsql"`

### Error: "Endpoint ID is not specified" (Neon.tech)

**Solusi:**
1. Gunakan direct connection (non-pooler host)
2. Tambahkan `DB_ENDPOINT` di `.env`
3. Pastikan custom connector sudah aktif

### Error: "Connection timeout"

**Solusi:**
1. Check firewall/antivirus
2. Pastikan port 5432 tidak terblokir
3. Test koneksi: `telnet hostname 5432`

### Error: "Authentication failed"

**Solusi:**
1. Cek username dan password di `.env`
2. Pastikan tidak ada spasi di password
3. Untuk Neon.tech, copy ulang credential dari dashboard

### Error: "SSL connection required"

**Solusi:**
```env
DB_SSLMODE=require
```

---

## 📝 H. Best Practices

### 1. Jangan Commit `.env`

File `.env` sudah ada di `.gitignore`. Jangan di-commit!

### 2. Gunakan Migration

Jangan buat tabel manual. Selalu gunakan migration:
```bash
php artisan make:migration create_students_table
```

### 3. Backup Database Regular

**Export:**
```bash
pg_dump -U username -d database_name > backup.sql
```

**Import:**
```bash
psql -U username -d database_name < backup.sql
```

### 4. Gunakan Seeder untuk Testing

Buat data dummy dengan seeder, bukan input manual.

### 5. Clear Cache Setelah Config

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📚 Referensi

- **Laravel Database**: https://laravel.com/docs/database
- **Laravel Migrations**: https://laravel.com/docs/migrations
- **PostgreSQL Docs**: https://www.postgresql.org/docs/
- **Neon.tech Docs**: https://neon.tech/docs

---

## ✅ Checklist Setup

- [ ] Extension PostgreSQL aktif
- [ ] Database created (lokal atau cloud)
- [ ] `.env` dikonfigurasi dengan benar
- [ ] Test koneksi berhasil (`php artisan db:show`)
- [ ] Migration berhasil (`php artisan migrate`)
- [ ] Seeder berhasil (`php artisan db:seed`)
- [ ] Data dummy terlihat di database

---

**Happy Coding! 🚀**
