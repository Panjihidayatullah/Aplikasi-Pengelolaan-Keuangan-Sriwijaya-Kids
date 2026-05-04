# 🎯 MODUL AKADEMIK - ACTION ITEMS UNTUK FRONTEND

## STATUS CURRENT

✅ **BACKEND:** 100% Complete
- Database: 12 tables created & migrated
- Models: 11 models dengan relasi lengkap
- Controllers: 6 controllers dengan CRUD logic
- Routes: All akademik routes configured
- Permissions: 40+ permissions + Guru role created
- Sidebar: Updated dengan akademik menu items
- Cache: Cleared & optimized

❌ **FRONTEND:** 0% (Ready untuk dimulai)
- Blade templates: Belum dibuat
- PDF generation: Template belum dibuat
- Excel import/export: Endpoint belum implement

---

## 📋 VIEW FILES YANG PERLU DIBUAT

### Priority 1 - Essential Views (WAJIB DIKERJAKAN TERLEBIH DAHULU)

#### 1. **Dashboard Akademik** 
`resources/views/akademik/dashboard/dashboard.blade.php`

**Konten yang ditampilkan:**
- Tahun akademik aktif
- Jumlah total siswa, guru, kelas
- Pengumuman terbaru (3-5 items)
- Ujian mendatang (5 items)
- Quick stats cards

**Syntax:**
```blade
@extends('layouts.app')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <!-- Stats Cards -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600">Total Siswa</h3>
        <p class="text-3xl font-bold text-blue-600">{{ $totalSiswa }}</p>
    </div>
    <!-- ... more cards ... -->
</div>

<!-- Pengumuman & Ujian -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <!-- Pengumuman List -->
    <!-- Ujian List -->
</div>
@endsection
```

---

#### 2. **Kurikulum Management**

**a) `resources/views/akademik/kurikulum/index.blade.php`**
- List kurikulum dengan pagination
- Search/filter by nama, tahun_berlaku
- Action buttons: Edit, Delete, View detail
- Create button

**b) `resources/views/akademik/kurikulum/create.blade.php`**
- Form fields: nama, deskripsi, tahun_berlaku, is_active checkbox
- Validation error display
- Cancel button

**c) `resources/views/akademik/kurikulum/edit.blade.php`**
- Pre-filled form
- Same fields as create

**d) `resources/views/akademik/kurikulum/show.blade.php`**
- Display kurikulum detail
- List related tahun_ajaran
- Edit/Delete buttons

---

#### 3. **Tahun Ajaran Management**

**a) `resources/views/akademik/tahun-ajaran/index.blade.php`**
- List dengan visual indicator untuk active status
- Toggle active button
- CRUD actions
- Show related semester

**b) `resources/views/akademik/tahun-ajaran/create.blade.php`**
- Form: nama, kurikulum_id (dropdown), tahun_mulai/selesai, tanggal_mulai/selesai

**c) `resources/views/akademik/tahun-ajaran/edit.blade.php`**
- Pre-filled form

**d) `resources/views/akademik/tahun-ajaran/show.blade.php`**
- Detail view
- Related semester list
- Edit button

---

#### 4. **Kartu Pelajar Management**

**a) `resources/views/akademik/kartu-pelajar/index.blade.php`**
- Table with columns: NIS, Nomor Kartu, Nama Siswa, Status, Actions
- Search by nama/NIS
- Bulk generate button (untuk kelas)
- Print & view buttons

**b) `resources/views/akademik/kartu-pelajar/create.blade.php`**
- Dropdown: Pilih siswa (hanya yang belum punya kartu aktif)
- Auto-fill dari siswa data
- Submit button

**c) `resources/views/akademik/kartu-pelajar/show.blade.php`**
- Display NIS, nomor kartu, foto siswa, tanggal terbit
- Print button (melalui print action)

**d) `resources/views/akademik/kartu-pelajar/pdf.blade.php`** ⭐ IMPORTANT
- Card template (landscape A5 atau postcard)
- QR code (generate dari NIS)
- Siswa photo (small)
- NIS, Nomor Kartu, nama, tanggal berlaku
- School logo & stamp space

**e) `resources/views/akademik/kartu-pelajar/bulk-generate.blade.php`**
- Dropdown: Pilih kelas
- Confirm button
- Progress indicator

---

#### 5. **Pengumuman Management**

**a) `resources/views/akademik/pengumuman/index.blade.php`**
- Admin view: List semua pengumuman dengan status, kategori
- Kolom: Judul, Kategori, Tanggal Mulai, Tanggal Selesai, Published, Actions
- Create button
- Edit/Delete/Publish buttons

**b) `resources/views/akademik/pengumuman/create.blade.php`**
- Form fields: judul, isi (WYSIWYG editor), kategori (select: ujian/libur/kegiatan/pengumuman)
- tanggal_mulai, tanggal_selesai (date picker)
- is_published checkbox
- Submit button

**c) `resources/views/akademik/pengumuman/edit.blade.php`**
- Same as create but pre-filled

**d) `resources/views/akademik/pengumuman/show.blade.php`**
- Full announcement display
- Edit/Delete buttons

---

### Priority 2 - Advanced Views (SETELAH PRIORITY 1 SELESAI)

#### 6. **Transkrip Nilai Management**
`resources/views/akademik/transkrip-nilai/index.blade.php`
- Search siswa by nama/NIS
- Display: Siswa list, bisa click untuk lihat detail

`resources/views/akademik/transkrip-nilai/show.blade.php`
- Grid: Mata Pelajaran | Harian | UTS | UAS | Nilai Akhir | Grade
- Semester tabs
- Rata-rata keseluruhan

---

#### 7. **Ujian Management**
`resources/views/akademik/ujian/index.blade.php`
- Table: Mata Pelajaran, Kelas, Jenis Ujian, Tanggal, Ruang
- Create button
- Edit/Delete/Manage Peserta buttons

`resources/views/akademik/ujian/create.blade.php`
- Form: mata_pelajaran_id, kelas_id, semester_id, jenis_ujian, tanggal, jam, ruang

`resources/views/akademik/ujian/show.blade.php`
- Detail + manage peserta (list siswa hadir/nilai)

---

#### 8. **Kenaikan Kelas Management**
`resources/views/akademik/kenaikan-kelas/index.blade.php`
- Filter by tahun_ajaran & status
- Table: Nama Siswa, Kelas, Status, Rata-rata Nilai, Actions
- Process button
- Report button

---

### Priority 3 - Components & Utilities

#### **Notifikasi Dropdown** (untuk navbar)
`resources/views/akademik/notifikasi/_dropdown.blade.php`
- Bell icon dengan unread count badge
- Dropdown list: Last 5 notifications
- "View All" link
- Mark as read action

#### **Reusable Components**
- `_stats-card.blade.php` - Card untuk stats
- `_table-header.blade.php` - Untuk list views
- `_form-errors.blade.php` - Error display
- `_pagination.blade.php` - Custom pagination

---

## 🎨 DESIGN REFERENCE

Gunakan existing pattern dari project:
- **Color Scheme:** From sidebar gradient (blue → cyan)
- **Card Style:** `bg-white rounded-lg shadow-md p-6`
- **Button Style:** Tailwind classes dari existing buttons
- **Form Style:** Dari existing forms (siswa, pembayaran, etc)
- **Table Style:** Dari existing tables (pembayaran, pengeluaran, etc)

---

## 🔧 IMPLEMENTATION TIPS

### 1. Reuse Existing Components
```blade
<!-- From existing views -->
<x-form-error field="nama" />
<x-button href="..." class="primary">Create</x-button>
@include('components.pagination', ['items' => $items])
```

### 2. Use Existing Helpers
```blade
@can('view akademik-dashboard') <!-- Auto from seeded permissions -->
@endcan

@if(is_admin() || auth()->user()->hasRole('Guru'))
<!-- Show to specific roles -->
@endif
```

### 3. Styling Pattern
```blade
<!-- Consistent with sidebar -->
<div class="bg-gradient-to-r from-purple-500 to-pink-400">
    Dashboard Akademik
</div>
```

### 4. Form Handling
```blade
<!-- Use Laravel form helpers -->
<form method="POST" action="{{ route('akademik.kurikulum.store') }}">
    @csrf
    <input type="text" name="nama" value="{{ old('nama') }}">
    @error('nama')
        <span class="text-red-500">{{ $message }}</span>
    @enderror
</form>
```

### 5. Pagination
```blade
{{ $items->links() }} <!-- Will use Tailwind view -->
```

---

## 📊 DATA FLOW PER PAGE

### Create Nilai Flow
1. Guru open `/akademik/transkrip-nilai`
2. Click siswa
3. Form: input nilai_harian, nilai_uts, nilai_uas
4. Submit POST to store()
5. Controller: calculate + grade
6. Save + create notifikasi
7. Redirect back with success

### Bulk Generate Kartu Flow
1. Admin open `/akademik/kartu-pelajar`
2. Click "Bulk Generate"
3. Select kelas
4. Confirm
5. Controller: loop siswa tanpa kartu
6. Generate NIS via static method
7. Create kartu_pelajar records
8. Redirect with count result

---

## 🧪 TESTING CHECKLIST

After creating each view:
- [ ] Can access page (permission check)
- [ ] Form validation works
- [ ] CRUD operations successful
- [ ] Pagination works
- [ ] Search/filter functional
- [ ] Mobile responsive
- [ ] Error messages display correctly
- [ ] Success messages display

---

## 📄 TEMPLATE STARTING POINT

Insert ke setiap view file baru:

```blade
@extends('layouts.app')

@section('title', 'Akademik - [Modul Name]')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">[Title]</h1>
            <p class="text-gray-600 mt-1">[Description]</p>
        </div>
        @can('create [permission]')
        <a href="{{ route('[route].create') }}" class="btn btn-primary">
            Create New
        </a>
        @endcan
    </div>

    <!-- Content here -->
    
</div>
@endsection
```

---

## ✅ NEXT IMMEDIATE STEPS

1. **Start dengan Priority 1 views** (Dashboard, Kurikulum, Tahun Ajaran)
2. **Test each view** dengan accessing via browser
3. **Test permission checks** - pastikan role-based access working
4. **Create PDF template** untuk kartu pelajar
5. **Move to Priority 2** setelah Priority 1 stable

---

**Status:** Backend ready, waiting for frontend views  
**Estimated Time:** 
- Priority 1: ~4-6 hours
- Priority 2: ~3-4 hours  
- Priority 3: ~2 hours
- **Total:** ~9-12 hours untuk complete implementation

**Next:** Start with Dashboard Akademik view
