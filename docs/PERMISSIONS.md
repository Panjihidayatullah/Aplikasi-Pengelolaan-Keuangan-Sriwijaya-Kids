# Role & Permissions Structure

## Roles

### 1. Admin (42 permissions)
**Full Access** - Dapat mengakses semua fitur sistem

**Permissions:**
- Dashboard: view
- Siswa: view, create, edit, delete, import, export
- Kelas: view, create, edit, delete
- Pembayaran: view, create, edit, delete, approve, export
- Pengeluaran: view, create, edit, delete, approve, export
- Aset: view, create, edit, delete
- Laporan: view cashflow, view pemasukan, view pengeluaran, export all
- User Management: view, create, edit, delete
- Role & Permission: view, create, edit, delete
- Riwayat: view all

---

### 2. Bendahara (28 permissions)
**Keuangan & Administrasi** - Mengelola keuangan sekolah

**Permissions:**
- Dashboard: view
- Siswa: view, create, edit
- Pembayaran: view, create, edit, export
- Pengeluaran: view, create, edit
- Aset: view, create, edit
- Laporan: view cashflow, view pemasukan, view pengeluaran, export
- Riwayat: view own

**TIDAK DAPAT:**
- Delete siswa, pembayaran, pengeluaran, aset
- Approve pembayaran/pengeluaran
- Akses user management
- Akses role & permission

---

### 3. Kepala Sekolah (18 permissions)
**Monitoring & Reporting** - Melihat laporan dan statistik

**Permissions:**
- Dashboard: view (full access untuk monitoring)
- Siswa: view only
- Pembayaran: view only
- Pengeluaran: view only
- Aset: view only
- Laporan: view all reports, export reports
- Riwayat: view all

**TIDAK DAPAT:**
- Create, edit, delete data apapun
- Akses user management
- Akses role & permission
- Manage keuangan

---

## Permission Mapping

### Navigation Menu Access

| Menu Item | Admin | Bendahara | Kepala Sekolah |
|-----------|-------|-----------|----------------|
| Dashboard | ✅ Full | ✅ Full | ✅ Full |
| Riwayat Aktivitas | ✅ All | ✅ Own | ✅ All |
| **Master Data** | | | |
| - Siswa | ✅ CRUD | ✅ View, Create, Edit | ✅ View Only |
| - Kelas | ✅ CRUD | ❌ | ❌ |
| **Keuangan** | | | |
| - Pembayaran | ✅ CRUD + Approve | ✅ View, Create, Edit | ✅ View Only |
| - Pengeluaran | ✅ CRUD + Approve | ✅ View, Create, Edit | ✅ View Only |
| - Aset Sekolah | ✅ CRUD | ✅ View, Create, Edit | ✅ View Only |
| **Laporan** | | | |
| - Cashflow | ✅ Full | ✅ Full | ✅ View + Export |
| - Pemasukan | ✅ Full | ✅ Full | ✅ View + Export |
| - Pengeluaran | ✅ Full | ✅ Full | ✅ View + Export |
| **Pengaturan** | | | |
| - Manajemen User | ✅ CRUD | ❌ | ❌ |
| - Role & Permission | ✅ CRUD | ❌ | ❌ |

---

## Usage Examples

### In Controllers
```php
// Check if user can create siswa
if (!auth()->user()->can('create siswa')) {
    abort(403, 'Unauthorized');
}

// OR using middleware
Route::middleware(['permission:create siswa'])->group(function () {
    // routes here
});
```

### In Blade Views
```blade
@can('create siswa')
    <a href="{{ route('siswa.create') }}">Add Siswa</a>
@endcan

@if(is_admin() || is_bendahara())
    <!-- Show financial menu -->
@endif
```

### Helper Functions
```php
// Check role
is_admin()
is_bendahara()
is_kepala_sekolah()

// Check permission
can_access('create siswa')
has_role('Admin')
has_any_role(['Admin', 'Bendahara'])
```
