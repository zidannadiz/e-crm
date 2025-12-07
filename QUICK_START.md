# 🚀 Quick Start - e-CRM Jasa Desain Mandiri

## Langkah Cepat Setup

### 1. Install Authentication (PENTING!)

Routes menggunakan middleware `auth`, jadi Anda HARUS install authentication terlebih dahulu:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install
npm run build
```

### 2. Setup Database

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecrm_jasa_desain
DB_USERNAME=root
DB_PASSWORD=
```

Buat database:
```sql
CREATE DATABASE ecrm_jasa_desain;
```

### 3. Generate Key & Migrate

```bash
php artisan key:generate
php artisan migrate
```

### 4. Buat User Admin

Daftar via: http://localhost:8000/register

Atau via tinker:
```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
]);
```

### 5. Jalankan Server

```bash
php artisan serve
```

### 6. Akses Aplikasi

- Login: http://localhost:8000/login
- Dashboard: http://localhost:8000/ecrm/dashboard

## ✅ Yang Sudah Tersedia

- ✅ Dashboard dengan statistik
- ✅ CRUD Clients
- ✅ CRUD Projects  
- ✅ CRUD Leads (dengan convert to client)
- ✅ History Kontak
- ✅ Filter & Search
- ✅ Responsive UI dengan Tailwind CSS

## 📝 Catatan

- Semua routes menggunakan prefix `/ecrm`
- Pastikan sudah install Laravel Breeze untuk authentication
- File sudah lengkap dan siap digunakan!

