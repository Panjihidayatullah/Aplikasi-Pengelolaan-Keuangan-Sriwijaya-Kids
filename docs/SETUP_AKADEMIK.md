# 📚 SETUP MODUL AKADEMIK - PANDUAN LENGKAP

## 🎯 Daftar Fitur yang Diimplementasikan

✅ **Kurikulum & Tahun Ajaran** - Pengaturan kurikulum dan periode akademik  
✅ **Semester Management** - Pengelolaan semester dan jadwal akademik  
✅ **Guru Wali Kelas** - Penugasan guru sebagai wali kelas  
✅ **Kartu Pelajar & NIS Otomatis** - Generate NIS dan cetak kartu  
✅ **Transkrip Nilai** - Riwayat nilai siswa per semester  
✅ **Pengumuman Akademik** - Informasi penting sekolah  
✅ **Kalender Akademik** - Jadwal kegiatan sekolah  
✅ **Manajemen Ujian** - UTS, UAS, jadwal, peserta  
✅ **Kenaikan Kelas & Kelulusan** - Sistem automatis  
✅ **Notifikasi Sistem** - Pemberitahuan untuk siswa/guru  
✅ **Role-Based Access** - Berbeda per role (Admin, Bendahara, Kepala Sekolah, Guru)  

---

## 🔧 LANGKAH SETUP

### 1. Run Migrations

```bash
php artisan migrate
```

**Tables yang dibuat:**
- `kurikulum` - Master kurikulum
- `tahun_ajaran` - Tahun akademik
- `semester` - Periode semester
- `guru_wali_kelas` - Penugasan wali kelas
- `kartu_pelajar` - Kartu identitas siswa
- `transkrip_nilai` - Riwayat nilai siswa
- `pengumuman` - Pengumuman akademik
- `kalender_akademik` - Kalender kegiatan
- `ujian` - Data ujian
- `ujian_siswa` - Peserta ujian (pivot)
- `kenaikan_kelas` - Proses kenaikan kelas
- `notifikasi` - Notifikasi sistem

### 2. Seed Permission & Roles

```bash
# Run existing seeder
php artisan db:seed --class=RoleSeeder

# Run academic permission seeder
php artisan db:seed --class=AcademicPermissionSeeder
```

### 3. Clear Caches

```bash
php artisan optimize:clear
php artisan view:clear
```

### 4. Buat Folder Views

```bash
mkdir -p resources/views/akademik/{kurikulum,tahun-ajaran,kartu-pelajar,pengumuman,notifikasi,dashboard}
```

---

## 📋 DATABASE SCHEMA OVERVIEW

### Kurikulum Table
```sql
- id (PK)
- nama (Kurikulum K-13, Kurikulum Merdeka, etc)
- deskripsi
- tahun_berlaku
- is_active
```

### Tahun Ajaran Table
```sql
- id (PK)
- kurikulum_id (FK)
- nama (2025/2026)
- tahun_mulai, tahun_selesai
- tanggal_mulai, tanggal_selesai
- is_active
```

### Semester Table
```sql
- id (PK)
- tahun_ajaran_id (FK)
- nomor_semester (1 or 2)
- tanggal_mulai, tanggal_selesai
- tanggal_uts, tanggal_uas
```

### Guru Wali Kelas Table
```sql
- id (PK)
- guru_id (FK)
- kelas_id (FK)
- tahun_ajaran_id (FK)
- tanggal_mulai, tanggal_selesai
- is_active
```

### Kartu Pelajar Table
```sql
- id (PK)
- siswa_id (FK)
- nomor_kartu (Auto-generated)
- nis_otomatis (Auto-generated)
- tanggal_terbit
- tanggal_berlaku_akhir
- status (aktif, expired, dibatalkan)
```

### Transkrip Nilai Table
```sql
- id (PK)
- siswa_id, mata_pelajaran_id (FK)
- semester_id, tahun_ajaran_id (FK)
- nilai_harian, nilai_uts, nilai_uas
- nilai_akhir (calculated)
- grade (A, B, C, D, E)
```

### Pengumuman Table
```sql
- id (PK)
- user_id (FK)
- judul, isi
- kategori (ujian, libur, kegiatan, pengumuman)
- tanggal_mulai, tanggal_selesai
- is_published
```

### Kalender Akademik Table
```sql
- id (PK)
- tahun_ajaran_id (FK)
- nama_kegiatan, deskripsi
- tipe (libur, ujian, kegiatan)
- tanggal_mulai, tanggal_selesai
- warna (untuk visual calendar)
```

### Ujian Table
```sql
- id (PK)
- mata_pelajaran_id, kelas_id, semester_id (FK)
- jenis_ujian (UTS, UAS, Quiz)
- tanggal_ujian, jam_mulai, jam_selesai
- ruang
```

### Ujian Siswa (Pivot)
```sql
- id (PK)
- ujian_id, siswa_id (FK)
- hadir (boolean)
- nilai (decimal)
```

### Kenaikan Kelas Table
```sql
- id (PK)
- siswa_id, kelas_sekarang_id, kelas_tujuan_id (FK)
- tahun_ajaran_id (FK)
- status (naik, tinggal, lulus)
- rata_rata_nilai
```

### Notifikasi Table
```sql
- id (PK)
- user_id (FK)
- judul, isi
- tipe (jadwal, tugas, nilai, pengumuman)
- is_read, dibaca_pada
```

---

## 🔐 ROLE-BASED PERMISSIONS

### Admin (82+ permissions)
✅ Akses penuh semua fitur akademik  
✅ Manage kurikulum, tahun ajaran, semester  
✅ Assign wali kelas  
✅ Generate kartu pelajar bulk  
✅ Manage ujian & nilai  
✅ Approve kenaikan kelas & kelulusan  
✅ Kelola pengumuman  

### Bendahara (Limited Academic Permissions)
✅ View kurikulum & tahun ajaran  
✅ Lihat kartu pelajar (untuk administrasi keuangan)  
✅ Lihat nilai (untuk perhitungan biaya)  
✅ View pengumuman  

### Kepala Sekolah (Read-Only Academic)
✅ View dashboard akademik  
✅ Lihat semua nilai & transcript  
✅ Lihat ujian & jadwal  
✅ Approve kenaikan kelas  
✅ Export rekap nilai  

### Guru (New Role)
✅ View siswa di kelasnya  
✅ Input nilai harian, UTS, UAS  
✅ Manage peserta ujian  
✅ Lihat transkrip nilai siswa  
✅ View pengumuman  
✅ Input & buat pengumuman untuk kelas  

---

## 🛣️ ROUTES STRUCTURE

### Dashboard
- `GET /akademik/dashboard` - Dashboard akademik

### Kurikulum
- `GET /akademik/kurikulum` - List kurikulum
- `POST /akademik/kurikulum` - Create kurikulum
- `GET /akademik/kurikulum/{id}/edit` - Edit form
- `PUT /akademik/kurikulum/{id}` - Update
- `DELETE /akademik/kurikulum/{id}` - Delete

### Tahun Ajaran
- `GET /akademik/tahun-ajaran` - List
- `POST /akademik/tahun-ajaran` - Create
- `POST /akademik/tahun-ajaran/{id}/set-active` - Set as active year

### Kartu Pelajar (Student Cards)
- `GET /akademik/kartu-pelajar` - List
- `POST /akademik/kartu-pelajar` - Create new card
- `GET /akademik/kartu-pelajar/{id}` - View card
- `GET /akademik/kartu-pelajar/{id}/print` - Print as PDF
- `POST /akademik/kartu-pelajar/bulk-generate` - Bulk generate for class

### Pengumuman (Announcements)
- `GET /akademik/pengumuman` - List all
- `POST /akademik/pengumuman` - Create
- `GET /pengumuman` - Public view (unauthenticated)

### Notifikasi (Notifications)
- `GET /akademik/notifikasi` - List user notifications
- `GET /akademik/notifikasi/unread` - Get unread count (API)
- `POST /akademik/notifikasi/{id}/read` - Mark as read
- `POST /akademik/notifikasi/mark-all-read` - Mark all as read

---

## 🎬 USAGE EXAMPLES

### 1. Generate NIS Otomatis
```php
// Di KartuPelajarController@store
$nis = KartuPelajar::generateNIS();
// Output: 310326001 (tanggal + bulan + tahun + sequence)
```

### 2. Calculate Grade
```php
// Di TranskripsNilai model
public function calculateNilaiAkhir()
{
    // Harian: 30%, UTS: 30%, UAS: 40%
    $this->nilai_akhir = 
        ($this->nilai_harian * 0.3) + 
        ($this->nilai_uts * 0.3) + 
        ($this->nilai_uas * 0.4);
    $this->updateGrade();
}

// Results: A (85+), B (70-84), C (60-69), D (50-59), E (<50)
```

### 3. Determine Promotion Status
```php
// Di KenaikanKelas model
$status = KenaikanKelas::determineStatus($rataRataNilai);
// Returns: 'naik' (>= 70), 'tinggal' (< 70), atau 'lulus'
```

### 4. Get Active Announcements
```php
// Di Pengumuman model
$announcements = Pengumuman::active()->get();
// Filters: is_published = true, tanggal_mulai <= today, tanggal_selesai >= today
```

### 5. Send Notification
```php
// Create notification untuk user
Notifikasi::create([
    'user_id' => $user->id,
    'judul' => 'Hasil UTS Telah Tersedia',
    'isi' => 'Lihat hasil UTS mata pelajaran Matematika',
    'tipe' => 'nilai',
    'terkait_dengan' => 'Ujian',
    'terkait_id' => $ujian->id,
]);
```

### 6. Get Wali Kelas
```php
// Di Kelas model
$waliKelasAktif = $kelas->waliKelasAktif();
// Returns active teacher as class advisor for current year
```

---

## 📊 VIEWS TO CREATE

### Dashboard (`akademik/dashboard.blade.php`)
- Academic year status
- Total students & teachers count
- Recent announcements
- Upcoming exams
- Quick links to modules

### Kurikulum (`akademik/kurikulum/`)
- `index.blade.php` - List kurikulum dengan status
- `create.blade.php` - Create form
- `edit.blade.php` - Edit form
- `show.blade.php` - Detail kurikulum + linked tahun ajaran

### Tahun Ajaran (`akademik/tahun-ajaran/`)
- `index.blade.php` - List dengan toggle active status
- `create.blade.php` - Create form
- `edit.blade.php` - Edit form
- `show.blade.php` - Detail + linked semester

### Kartu Pelajar (`akademik/kartu-pelajar/`)
- `index.blade.php` - List kartu
- `create.blade.php` - Create form
- `show.blade.php` - View card detail
- `pdf.blade.php` - PDF template untuk cetak
- Bulk generate view

### Pengumuman (`akademik/pengumuman/`)
- `index.blade.php` - Admin list
- `create.blade.php` - Create form
- `edit.blade.php` - Edit form
- `public.blade.php` - Public display (unauthenticated)

### Notifikasi (`akademik/notifikasi/`)
- `index.blade.php` - User notifications list with filter
- Dropdown component untuk navbar

### Ujian (`akademik/ujian/`)
- `index.blade.php` - List exam schedule
- `create.blade.php` - Create exam
- `edit.blade.php` - Edit exam
- `peserta.blade.php` - Manage participants & input nilai

### Transkrip Nilai (`akademik/transkrip-nilai/`)
- `index.blade.php` - Student searchable list
- `show.blade.php` - Student full transcript

### Kenaikan Kelas (`akademik/kenaikan-kelas/`)
- `index.blade.php` - List promoted/retained students
- `process.blade.php` - Process form (bulk action)
- `report.blade.php` - Graduation report

---

## ⚙️ CONFIGURATION

### NIS Generation Pattern
Current pattern: `DDMMYY + Sequence`
- Example: `310326001` = 31st March 2026, student #001 that day

To customize, edit `KartuPelajar::generateNIS()` method.

### Grade Calculation Weights
- Daily Evaluation: 30%
- Mid-term Exam (UTS): 30%
- Final Exam (UAS): 40%

To change, edit `TranskripsNilai::calculateNilaiAkhir()` method.

### Promotion Criteria
- Naik (Promoted): Average grade >= 70
- Tinggal (Retained): Average grade < 70
- Lulus (Graduated): Last grade year

To customize, edit `KenaikanKelas::determineStatus()` method.

---

## 🔄 WORKFLOW EXAMPLES

### Workflow 1: Setup Tahun Akademik Baru
1. Admin buat Kurikulum baru (jika ganti kurikulum)
2. Admin buat Tahun Ajaran (2025/2026)
3. Admin buat 2 Semester (Ganjil & Genap) dengan tanggal
4. Admin set Tahun Ajaran sebagai active
5. Admin assign Wali Kelas untuk setiap kelas
6. Sistem auto-create Notifikasi untuk semua guru

### Workflow 2: Generate Kartu Pelajar
1. Admin pilih class
2. Click "Bulk Generate Kartu Pelajar"
3. Sistem auto-generate NIS + nomor kartu untuk semua siswa
4. Admin print kartu (PDF)
5. Guru bisa lihat NIS siswa di aplikasi

### Workflow 3: Input & Calculate Nilai
1. Guru login
2. Akses module "Ujian & Nilai"
3. Input nilai_harian, nilai_uts, nilai_uas untuk setiap siswa
4. Sistem auto-calculate nilai_akhir & grade
5. Notifikasi otomatis ke siswa: "Hasil nilai Anda tersedia"
6. Kepala Sekolah bisa lihat rekap nilai per class/subject

### Workflow 4: Kenaikan Kelas
1. Admin pilih Tahun Ajaran yang sudah selesai
2. Sistem calculate rata-rata nilai per siswa
3. Determine status (naik/tinggal/lulus) otomatis
4. Admin bisa override jika perlu
5. Approve kenaikan kelas
6. Notifikasi ke siswa & guru
7. Generate laporan kelulusan/kenaikan untuk kepala sekolah

---

## 🚀 NEXT STEPS

### To Complete Implementation:

1. **Create Blade Views** - Use same template as existing views (Tailwind CSS)
2. **Add Remaining Controllers:**
   - `GuruWaliKelasController` - Manage wali kelas assignment
   - `TranskripsNilaiController` - View & input nilai
   - `SemesterController` - Manage semester
   - `UjianController` - Manage exam schedule & participants
   - `KenaikanKelasController` - Process promotion/graduation
   - `KalenderAkademikController` - Manage academic calendar

3. **Create Services** (following existing pattern):
   - `AcademicService` - Calculate averages, promotions
   - `NotificationService` - Send notifications on events
   - `ExportService` - Export student data

4. **Add API endpoints** (if needed):
   - GET /api/akademik/notifikasi/unread-count
   - POST /api/akademik/nilai/bulk-import
   - GET /api/akademik/rekap-nilai/export

5. **Add Events & Listeners**:
   - `NilaiInputted` -> Send notification to parent
   - `KenaikanKelasProcessed` -> Send notification to students
   - `PengumumanCreated` -> Send to all relevant users

6. **Update Sidebar** - Add academic menu items (done in previous step)

7. **Run Tests** - Create tests for critical academic functions

---

## 📞 HELP & SUPPORT

Review this documentation alongside:
- `PERMISSIONS.md` - Role & permission details
- `ANALISIS_WEBSITE.md` - System architecture
- `README.md` - Project overview

---

**Status:** ✅ Core academic system ready for view creation  
**Last Updated:** 31 Maret 2026  
**Version:** 1.0
