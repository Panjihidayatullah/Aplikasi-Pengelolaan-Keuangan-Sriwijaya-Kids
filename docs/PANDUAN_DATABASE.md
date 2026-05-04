# 📚 Panduan Membuat Tabel & Data Baru

Panduan praktis untuk membuat tabel baru, menambahkan data, dan mengelola database di Laravel.

---

## 📋 Workflow Standar

```
1. Buat Migration (struktur tabel)
2. Buat Model (representasi tabel)
3. Jalankan Migration (buat tabel di database)
4. Buat Seeder (optional - untuk data dummy)
5. Buat Factory (optional - untuk testing)
```

---

## 🆕 A. Membuat Tabel Baru

### 1. Buat Migration

**Command:**
```bash
php artisan make:migration create_students_table
```

**Hasil:** File baru di `database/migrations/2026_02_27_xxxxxx_create_students_table.php`

**Edit Migration:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique(); // NIS
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('class'); // Kelas
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at (optional)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
```

**Tipe Kolom Umum:**
```php
$table->id();                          // Primary key auto increment
$table->string('name');                // VARCHAR(255)
$table->string('name', 100);           // VARCHAR(100)
$table->text('description');           // TEXT
$table->integer('age');                // INTEGER
$table->bigInteger('amount');          // BIGINT
$table->decimal('price', 10, 2);      // DECIMAL(10,2)
$table->boolean('is_active');          // BOOLEAN
$table->date('birth_date');            // DATE
$table->dateTime('published_at');      // DATETIME
$table->timestamp('created_at');       // TIMESTAMP
$table->enum('status', ['active', 'inactive']); // ENUM
$table->json('metadata');              // JSON

// Modifiers
->nullable()                           // NULL allowed
->default('value')                     // Default value
->unique()                             // Unique constraint
->index()                              // Add index
->unsigned()                           // Unsigned (positive only)
->after('column')                      // Place after column
->comment('Column description')        // Add comment
```

**Foreign Key:**
```php
// Cara 1 (Recommended)
$table->foreignId('user_id')->constrained()->cascadeOnDelete();

// Cara 2 (Manual)
$table->unsignedBigInteger('user_id');
$table->foreign('user_id')
      ->references('id')
      ->on('users')
      ->onDelete('cascade');
```

### 2. Buat Model

**Command:**
```bash
php artisan make:model Student
```

**Atau sekaligus dengan migration:**
```bash
php artisan make:model Student -m
```

**Edit Model:** `app/Models/Student.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes; // Jika pakai soft deletes

    // Nama tabel (optional jika mengikuti konvensi)
    protected $table = 'students';

    // Primary key (optional jika 'id')
    protected $primaryKey = 'id';

    // Kolom yang boleh diisi mass assignment
    protected $fillable = [
        'student_id',
        'name',
        'email',
        'phone',
        'address',
        'class',
        'gender',
        'birth_date',
        'parent_name',
        'parent_phone',
        'is_active',
    ];

    // Kolom yang tidak boleh diisi
    protected $guarded = ['id'];

    // Cast tipe data
    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor (get)
    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->city;
    }

    // Mutator (set)
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }

    // Scope
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

### 3. Jalankan Migration

```bash
# Jalankan migration
php artisan migrate

# Lihat status migration
php artisan migrate:status

# Rollback last batch
php artisan migrate:rollback

# Rollback semua & jalankan lagi
php artisan migrate:refresh

# Reset semua (hapus data)
php artisan migrate:fresh
```

---

## 📊 B. Menambahkan Data

### 1. Via Seeder (Recommended for Testing)

**Buat Seeder:**
```bash
php artisan make:seeder StudentSeeder
```

**Edit Seeder:** `database/seeders/StudentSeeder.php`
```php
<?php

namespace Database\Seeders;


use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Cara 1: Manual satu-satu
        Student::create([
            'student_id' => 'S001',
            'name' => 'Ahmad Rizki',
            'email' => 'ahmad@example.com',
            'phone' => '081234567890',
            'class' => '5A',
            'gender' => 'male',
            'birth_date' => '2015-05-10',
            'is_active' => true,
        ]);

        Student::create([
            'student_id' => 'S002',
            'name' => 'Siti Fatimah',
            'email' => 'siti@example.com',
            'phone' => '081234567891',
            'class' => '5A',
            'gender' => 'female',
            'birth_date' => '2015-03-15',
            'is_active' => true,
        ]);

        // Cara 2: Bulk insert
        $students = [
            [
                'student_id' => 'S003',
                'name' => 'Budi Santoso',
                'class' => '5B',
                'gender' => 'male',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 'S004',
                'name' => 'Dewi Lestari',
                'class' => '5B',
                'gender' => 'female',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Student::insert($students);

        // Cara 3: Via Factory (jika ada)
        // Student::factory(50)->create();
    }
}
```

**Daftarkan di DatabaseSeeder:** `database/seeders/DatabaseSeeder.php`
```php
public function run(): void
{
    $this->call([
        StudentSeeder::class,
        // Tambah seeder lain...
    ]);
}
```

**Jalankan Seeder:**
```bash
# Jalankan semua seeder
php artisan db:seed

# Jalankan seeder tertentu
php artisan db:seed --class=StudentSeeder

# Migration + Seeder sekaligus
php artisan migrate:fresh --seed
```

### 2. Via Tinker (Manual Testing)

```bash
php artisan tinker
```

**Insert:**
```php
// Cara 1
$student = new App\Models\Student;
$student->student_id = 'S005';
$student->name = 'Test Student';
$student->class = '5C';
$student->gender = 'male';
$student->is_active = true;
$student->save();

// Cara 2 (Mass Assignment)
App\Models\Student::create([
    'student_id' => 'S006',
    'name' => 'Test Student 2',
    'class' => '5C',
    'gender' => 'female',
    'is_active' => true,
]);
```

### 3. Via Controller (Production)

**Contoh:** `app/Http/Controllers/StudentController.php`
```php
use App\Models\Student;
use Illuminate\Http\Request;

public function store(Request $request)
{
    // Validasi
    $validated = $request->validate([
        'student_id' => 'required|unique:students',
        'name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'class' => 'required|string',
        'gender' => 'required|in:male,female',
    ]);

    // Simpan
    $student = Student::create($validated);

    return redirect()->route('students.index')
                    ->with('success', 'Student created successfully');
}
```

---

## 🔄 C. Mengubah Tabel yang Sudah Ada

### 1. Buat Migration Baru

```bash
php artisan make:migration add_photo_to_students_table
```

**Edit Migration:**
```php
public function up(): void
{
    Schema::table('students', function (Blueprint $table) {
        $table->string('photo')->nullable()->after('name');
        $table->string('blood_type')->nullable()->after('gender');
    });
}

public function down(): void
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropColumn(['photo', 'blood_type']);
    });
}
```

**Atau ubah kolom:**
```php
public function up(): void
{
    Schema::table('students', function (Blueprint $table) {
        // Ubah tipe kolom
        $table->string('phone', 20)->change();
        
        // Rename kolom (install doctrine/dbal dulu)
        $table->renameColumn('class', 'grade');
    });
}
```

### 2. Jalankan Migration

```bash
php artisan migrate
```

---

## 🏭 D. Factory (untuk Testing)

### 1. Buat Factory

```bash
php artisan make:factory StudentFactory
```

**Edit Factory:** `database/factories/StudentFactory.php`
```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => 'S' . fake()->unique()->numberBetween(1000, 9999),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'class' => fake()->randomElement(['5A', '5B', '5C', '6A', '6B']),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_date' => fake()->date(),
            'parent_name' => fake()->name(),
            'parent_phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
```

### 2. Gunakan Factory

**Di Seeder:**
```php
Student::factory(50)->create();
```

**Di Tinker:**
```php
App\Models\Student::factory(10)->create();
```

---

## 🔍 E. Query Data

### Basic Query

```php
// Get all
$students = Student::all();

// Get with condition
$students = Student::where('class', '5A')->get();

// Get one
$student = Student::find(1);
$student = Student::where('student_id', 'S001')->first();

// Count
$count = Student::count();
$activeCount = Student::where('is_active', true)->count();

// Paginate
$students = Student::paginate(15);

// Order
$students = Student::orderBy('name', 'asc')->get();

// Select specific columns
$students = Student::select('id', 'name', 'class')->get();

// With relationships
$students = Student::with('user')->get();
```

### Advanced Query

```php
// Where conditions
Student::where('class', '5A')
       ->where('is_active', true)
       ->get();

// OrWhere
Student::where('class', '5A')
       ->orWhere('class', '5B')
       ->get();

// WhereIn
Student::whereIn('class', ['5A', '5B', '5C'])->get();

// WhereBetween
Student::whereBetween('birth_date', ['2015-01-01', '2015-12-31'])->get();

// Like
Student::where('name', 'like', '%ahmad%')->get();

// Using scope
Student::active()->get();
```

---

## ✏️ F. Update & Delete Data

### Update

```php
// Find & Update
$student = Student::find(1);
$student->name = 'New Name';
$student->save();

// Update directly
Student::where('id', 1)->update(['name' => 'New Name']);

// Update or Create
Student::updateOrCreate(
    ['student_id' => 'S001'], // Search condition
    ['name' => 'Updated Name', 'class' => '6A'] // Data to update
);
```

### Delete

```php
// Soft Delete (if using SoftDeletes)
$student = Student::find(1);
$student->delete();

// Force Delete (permanent)
$student->forceDelete();

// Restore soft deleted
$student->restore();

// Delete multiple
Student::whereIn('id', [1, 2, 3])->delete();
```

---

## 📝 G. Contoh Lengkap: Tabel Income & Expense

### 1. Buat Migration

```bash
php artisan make:migration create_incomes_table
php artisan make:migration create_expenses_table
```

**Income Migration:**
```php
Schema::create('incomes', function (Blueprint $table) {
    $table->id();
    $table->string('transaction_code')->unique();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->decimal('amount', 12, 2);
    $table->string('category');
    $table->text('description')->nullable();
    $table->date('date');
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->timestamps();
});
```

**Expense Migration:**
```php
Schema::create('expenses', function (Blueprint $table) {
    $table->id();
    $table->string('transaction_code')->unique();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->decimal('amount', 12, 2);
    $table->string('category');
    $table->text('description')->nullable();
    $table->date('date');
    $table->string('proof_file')->nullable();
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->timestamps();
});
```

### 2. Buat Model

```bash
php artisan make:model Income
php artisan make:model Expense
```

### 3. Buat Seeder

```bash
php artisan make:seeder FinanceSeeder
```

```php
public function run(): void
{
    // Income data
    Income::create([
        'transaction_code' => 'INC-001',
        'user_id' => 1,
        'amount' => 5000000,
        'category' => 'SPP',
        'description' => 'Pembayaran SPP Bulan Januari',
        'date' => now(),
        'status' => 'approved',
    ]);

    // Expense data
    Expense::create([
        'transaction_code' => 'EXP-001',
        'user_id' => 1,
        'amount' => 1000000,
        'category' => 'Operational',
        'description' => 'Pembelian ATK',
        'date' => now(),
        'status' => 'approved',
    ]);
}
```

### 4. Jalankan

```bash
php artisan migrate
php artisan db:seed --class=FinanceSeeder
```

---

## ⚡ H. Tips & Best Practices

### 1. Naming Convention

- **Migration**: `create_table_name_table`, `add_column_to_table_name_table`
- **Model**: Singular (Student, User, Income)
- **Table**: Plural (students, users, incomes)
- **Foreign Key**: `model_id` (user_id, student_id)

### 2. Gunakan Transactions

```php
use Illuminate\Support\Facades\DB;

DB::beginTransaction();
try {
    Student::create([...]);
    Income::create([...]);
    
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    // Handle error
}
```

### 3. Mass Assignment Protection

Selalu define `$fillable` atau `$guarded` di Model.

### 4. Use Seeders for Development

Jangan input data manual. Buat seeder untuk testing.

### 5. Backup Before Migrate

Sebelum `migrate:fresh` di production, backup database dulu!

---

## 🎯 Quick Reference Commands

```bash
# Migration
php artisan make:migration create_table_name          # Buat migration
php artisan migrate                                     # Jalankan migration
php artisan migrate:rollback                           # Rollback
php artisan migrate:fresh                              # Reset semua

# Model
php artisan make:model ModelName                       # Buat model
php artisan make:model ModelName -m                    # Model + migration
php artisan make:model ModelName -mfs                  # Model + migration + factory + seeder

# Seeder
php artisan make:seeder TableSeeder                    # Buat seeder
php artisan db:seed                                    # Jalankan seeder
php artisan db:seed --class=TableSeeder               # Seeder tertentu

# Factory
php artisan make:factory ModelFactory                  # Buat factory

# Database
php artisan db:show                                    # Info database
php artisan db:table table_name                        # Info tabel
php artisan tinker                                     # Interactive shell
```

---

**Happy Coding! 🚀**
