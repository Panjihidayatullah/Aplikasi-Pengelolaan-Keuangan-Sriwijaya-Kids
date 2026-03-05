# Struktur Direktori Project

Project ini menggunakan Laravel sebagai fullstack framework (backend dan frontend dalam satu aplikasi).

Struktur direktori disusun agar:
- Mudah dipahami
- Terorganisir
- Tidak over-engineering
- Cocok untuk pengembangan jangka panjang
- Tetap mengikuti best practice Laravel

---

## 📁 Struktur Direktori Utama

```
app/
│
├── Http/
│   ├── Controllers/        # Mengatur request dan response
│   │   ├── Auth/
│   │   ├── DashboardController.php
│   │   ├── UserController.php
│   │   ├── StudentController.php
│   │   ├── IncomeController.php
│   │   ├── ExpenseController.php
│   │   └── ReportController.php
│   │
│   ├── Requests/           # Validasi form (Form Request)
│   │   ├── User/
│   │   ├── Student/
│   │   └── Finance/
│   │
│   └── Middleware/         # Middleware custom
│
├── Services/               # Business Logic Layer
│   ├── UserService.php
│   ├── StudentService.php
│   ├── IncomeService.php
│   ├── ExpenseService.php
│   └── ReportService.php
│
├── Repositories/           # Query & akses database
│   ├── UserRepository.php
│   ├── StudentRepository.php
│   ├── IncomeRepository.php
│   └── ExpenseRepository.php
│
├── Models/                 # Eloquent Model
│
└── Policies/               # Authorization Policy
```

---

## 📁 Struktur Frontend (Blade)

```
resources/
│
├── views/
│   ├── layouts/            # Template utama (master layout)
│   │   ├── app.blade.php
│   │   └── guest.blade.php
│   │
│   ├── components/         # Blade Components (button, card, dll)
│   │
│   ├── dashboard/
│   │   └── index.blade.php
│   │
│   ├── users/
│   ├── students/
│   ├── finance/
│   │   ├── income/
│   │   ├── expense/
│   │   └── reports/
│   │
│   └── auth/
│
├── js/                     # File JavaScript
└── css/                    # File CSS / Tailwind
```

---

## 📁 Struktur Routes

```
routes/
├── web.php                 # Route utama aplikasi
└── auth.php                # Route autentikasi
```

---

## 🔄 Pola Arsitektur

Aplikasi menggunakan pola:

Controller → Service → Repository → Model → Database

Tujuan:
- Controller tetap bersih
- Logic bisnis dipisahkan
- Query database terstruktur
- Mudah dikembangkan dan diuji

---

## 🎯 Prinsip Struktur

- Separation of Concern
- Clean Code
- Tidak menaruh query di Controller
- Validasi menggunakan Form Request
- Menggunakan Resource Controller
- Layout terpusat di folder layouts

---

Struktur ini dirancang untuk aplikasi fullstack Laravel (frontend + backend dalam satu project) agar tetap rapi, scalable, dan mudah dipahami.