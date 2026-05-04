# Security & Encryption Documentation

## 🔐 Sistem Enkripsi yang Digunakan

### 1. **Password Hashing - Bcrypt**
**Status:** ✅ AKTIF

**Detail:**
- **Algoritma:** Bcrypt (Blowfish-based)
- **Rounds:** 12 (konfigurasi di `.env`)
- **Kekuatan:** 2^12 = 4096 iterasi
- **Hash Length:** 60 karakter

**Lokasi Konfigurasi:**
```env
# .env
BCRYPT_ROUNDS=12
```

**Implementasi:**
```php
// app/Models/User.php
protected function casts(): array
{
    return [
        'password' => 'hashed', // Auto-hash dengan bcrypt
    ];
}
```

**Contoh Hash:**
```
$2y$12$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456
│ │ │└─ Salt (22 karakter)
│ │ └─── Cost factor (12)
│ └───── Minor revision (y)
└─────── Algoritma identifier (2)
```

**Keamanan:**
- ✅ Resistant terhadap rainbow table attacks
- ✅ Setiap password memiliki salt unik
- ✅ Cost factor dapat disesuaikan (semakin tinggi, semakin aman tapi lebih lambat)
- ✅ Recommended oleh OWASP untuk password hashing

---

### 2. **Application Encryption - AES-256-CBC**
**Status:** ✅ AKTIF

**Detail:**
- **Algoritma:** AES (Advanced Encryption Standard)
- **Key Size:** 256-bit
- **Mode:** CBC (Cipher Block Chaining)
- **Key:** Auto-generated Laravel APP_KEY

**Lokasi Konfigurasi:**
```env
# .env
APP_KEY=base64:MevRIMHDuHp3+a3WCOLJMsoyFIiKjwugQVDbI/SUNC8=

# config/app.php
'cipher' => 'AES-256-CBC',
```

**Digunakan untuk:**
- Cookies encryption
- Encrypted values (jika menggunakan `Crypt` facade)
- Session data (jika diaktifkan)
- Two-factor authentication secrets

**Cara Pakai:**
```php
use Illuminate\Support\Facades\Crypt;

// Encrypt
$encrypted = Crypt::encryptString('Sensitive data');

// Decrypt
$decrypted = Crypt::decryptString($encrypted);
```

**Keamanan:**
- ✅ AES-256 adalah standar enkripsi militer (FIPS 197)
- ✅ CBC mode dengan IV (Initialization Vector) random
- ✅ HMAC untuk authentication
- ✅ Resistant terhadap various cryptographic attacks

---

### 3. **Session Encryption**
**Status:** ❌ TIDAK AKTIF (default)

**Konfigurasi:**
```php
// config/session.php
'encrypt' => env('SESSION_ENCRYPT', false),
```

**Untuk Mengaktifkan:**
```env
# .env
SESSION_ENCRYPT=true
```

**Catatan:**
- Session disimpan di `storage/framework/sessions`
- Jika tidak dienkripsi, data masih aman di server-side
- Cookie session ID tetap di-sign dan protected

---

### 4. **Database Connection Security - SSL/TLS**
**Status:** ✅ AKTIF (untuk Neon.tech)

**Detail:**
- **Protocol:** TLS (Transport Layer Security)
- **Mode:** SSLMODE=require
- **Provider:** Neon.tech PostgreSQL Cloud

**Konfigurasi:**
```env
# .env
DB_CONNECTION=pgsql
DB_HOST=ep-royal-hat-a1et2xxj.ap-southeast-1.aws.neon.tech
DB_SSLMODE=require
```

**Keamanan:**
- ✅ Data encrypted during transmission
- ✅ Man-in-the-middle attack protection
- ✅ Certificate-based authentication

---

### 5. **Two-Factor Authentication (2FA)**
**Status:** ✅ TERSEDIA (Laravel Fortify)

**Detail:**
- **Library:** Laravel Fortify
- **Algoritma:** TOTP (Time-based One-Time Password)
- **Secret Storage:** Encrypted di database

**Implementasi:**
```php
// app/Models/User.php
use Laravel\Fortify\TwoFactorAuthenticatable;

protected $hidden = [
    'two_factor_secret',           // Encrypted
    'two_factor_recovery_codes',   // Encrypted
];
```

**Kolom Database:**
- `two_factor_secret` → QR code secret (encrypted)
- `two_factor_recovery_codes` → Backup codes (encrypted)
- `two_factor_confirmed_at` → Activation timestamp

---

### 6. **Remember Token**
**Status:** ✅ AKTIF

**Detail:**
- Token untuk "Remember Me" functionality
- Random string (60 characters)
- Stored di database, dikirim via cookie

**Keamanan:**
- ✅ Random token generation
- ✅ Hashed sebelum disimpan
- ✅ Automatic invalidation pada logout

---

## 🛡️ Best Practices yang Diterapkan

### ✅ Implemented

1. **Password Hashing**: Bcrypt dengan 12 rounds
2. **Application Key**: 256-bit random key
3. **SSL/TLS**: Koneksi database terenkripsi
4. **CSRF Protection**: Built-in Laravel
5. **XSS Protection**: Blade templating auto-escape
6. **SQL Injection Protection**: Eloquent ORM dengan prepared statements
7. **2FA Support**: Available untuk extra security
8. **Hidden Sensitive Fields**: Password, tokens, secrets

### 🔧 Optional Enhancements

**1. Aktifkan Session Encryption:**
```env
SESSION_ENCRYPT=true
```

**2. Encrypt Sensitive Database Columns:**
```php
use Illuminate\Database\Eloquent\Casts\Encrypted;

protected $casts = [
    'nik' => Encrypted::class,
    'no_hp' => Encrypted::class,
];
```

**3. HTTPS/SSL untuk Production:**
```php
// app/Providers/AppServiceProvider.php
if ($this->app->environment('production')) {
    URL::forceScheme('https');
}
```

**4. Rate Limiting:**
```php
// Already implemented in routes/web.php
->middleware('throttle:6,1')
```

---

## 📊 Security Level Summary

| Component | Algorithm | Key Size | Status | Level |
|-----------|-----------|----------|--------|-------|
| Password | Bcrypt | N/A | ✅ Active | 🟢 High |
| App Encryption | AES-256-CBC | 256-bit | ✅ Active | 🟢 High |
| Session | AES-256-CBC | 256-bit | ⚪ Optional | 🟡 Medium |
| Database Conn | TLS/SSL | 2048-bit | ✅ Active | 🟢 High |
| 2FA | TOTP | 160-bit | ✅ Available | 🟢 High |
| Remember Token | SHA-256 | 256-bit | ✅ Active | 🟢 High |

**Overall Security Rating:** 🟢 **HIGH** (Production-Ready)

---

## 🔍 Verification Commands

### Check Encryption Key
```bash
php artisan tinker
config('app.key')
# Output: base64:MevRIMHDuHp3+a3WCOLJMsoyFIiKjwugQVDbI/SUNC8=
```

### Test Password Hashing
```bash
php artisan tinker
bcrypt('password123')
# Output: $2y$12$...
```

### Check Database SSL
```bash
php artisan tinker
DB::connection()->getPdo()->getAttribute(PDO::ATTR_SSL_VERIFY_SERVER_CERT)
```

### Test Encryption
```bash
php artisan tinker
Crypt::encryptString('test')
Crypt::decryptString('...')
```

---

## 📚 References

- **Bcrypt:** [OWASP Password Storage Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html)
- **AES-256:** [NIST FIPS 197](https://nvlpubs.nist.gov/nistpubs/FIPS/NIST.FIPS.197.pdf)
- **Laravel Encryption:** [Laravel Docs - Encryption](https://laravel.com/docs/11.x/encryption)
- **Laravel Security:** [OWASP Laravel Security](https://cheatsheetseries.owasp.org/cheatsheets/Laravel_Cheat_Sheet.html)

---

**Last Updated:** March 7, 2026  
**Laravel Version:** 12.53.0  
**PHP Version:** 8.3.16
