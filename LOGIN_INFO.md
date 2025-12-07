# 🔐 Informasi Login - e-CRM Jasa Desain Mandiri

## Sistem Authentication

Aplikasi ini menggunakan **Laravel Breeze** untuk sistem authentication/login.

## 📍 URL Login & Register

- **Login**: http://localhost:8000/login
- **Register**: http://localhost:8000/register
- **Dashboard e-CRM**: http://localhost:8000/ecrm/dashboard

## 👤 Cara Membuat User

### Opsi 1: Register via Web (Recommended)
1. Buka: http://localhost:8000/register
2. Isi form:
   - Name: Nama Anda
   - Email: email@example.com
   - Password: password Anda
   - Confirm Password: ulangi password
3. Klik "Register"
4. Setelah register, Anda akan otomatis login

### Opsi 2: Buat User via Tinker
```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123'),
]);
```

### Opsi 3: Buat User via Seeder
Buat file `database/seeders/AdminSeeder.php`:
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
    }
}
```

Jalankan:
```bash
php artisan db:seed --class=AdminSeeder
```

## 🔑 Fitur Authentication Breeze

Laravel Breeze menyediakan:
- ✅ Login dengan email & password
- ✅ Register user baru
- ✅ Remember Me (Stay logged in)
- ✅ Forgot Password
- ✅ Reset Password
- ✅ Email Verification (opsional)
- ✅ Profile Management

## 🚀 Setelah Login

Setelah login berhasil, user akan di-redirect ke:
- **Dashboard e-CRM**: `/ecrm/dashboard`

Atau bisa akses langsung:
- Clients: `/ecrm/clients`
- Projects: `/ecrm/projects`
- Leads: `/ecrm/leads`
- Contacts: `/ecrm/contacts`

## 📝 Catatan

1. **Semua routes e-CRM menggunakan middleware `auth`**, jadi user harus login terlebih dahulu
2. **Home page (`/`) akan redirect ke `/ecrm/dashboard`** setelah login
3. **Jika belum login**, akan di-redirect ke halaman login
4. **Session login** akan tetap aktif sampai logout atau session expired

## 🔒 Security

- Password di-hash menggunakan bcrypt
- CSRF protection aktif
- Session management otomatis
- Password reset via email (jika email dikonfigurasi)

## 🛠️ Troubleshooting

**Tidak bisa login?**
- Pastikan user sudah dibuat (register atau via tinker)
- Pastikan email dan password benar
- Cek database users table: `SELECT * FROM users;`

**Error "Route [login] not defined"?**
- Pastikan Breeze sudah terinstall: `composer require laravel/breeze --dev`
- Pastikan sudah run: `php artisan breeze:install blade`

**Redirect loop?**
- Clear cache: `php artisan route:clear && php artisan config:clear`
- Pastikan middleware auth sudah benar di routes

## 📞 Quick Test

1. **Buat user:**
   ```bash
   php artisan tinker
   ```
   ```php
   \App\Models\User::create(['name' => 'Test', 'email' => 'test@test.com', 'password' => bcrypt('password')]);
   ```

2. **Login:**
   - Email: `test@test.com`
   - Password: `password`

3. **Akses dashboard:**
   - http://localhost:8000/ecrm/dashboard

