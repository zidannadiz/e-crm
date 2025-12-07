# Catatan Instalasi e-CRM

## ✅ File Sudah Dipindahkan

Semua file e-CRM sudah berhasil dipindahkan ke project Laravel:
- ✅ Models (Client, Project, Contact, Lead)
- ✅ Controllers (Dashboard, Client, Project, Lead, Contact)
- ✅ Migrations (4 migrations untuk e-CRM)
- ✅ Views (Dashboard, Clients, Projects, Leads, Contacts)
- ✅ Routes (ecrm.php dan update web.php)
- ✅ Layout (app.blade.php)

## ⚠️ Yang Perlu Dilakukan

### 1. Setup Authentication

Routes menggunakan middleware `auth`. Anda perlu memilih salah satu:

**Opsi A: Install Laravel Breeze (Recommended)**
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install && npm run build
```

**Opsi B: Install Laravel Jetstream**
```bash
composer require laravel/jetstream
php artisan jetstream:install livewire
php artisan migrate
npm install && npm run build
```

**Opsi C: Hapus Middleware Auth (untuk testing)**
Edit `routes/ecrm.php` dan hapus middleware `auth`:
```php
Route::prefix('ecrm')->name('ecrm.')->group(function () {
    // ... routes
});
```

### 2. Setup Database

Edit file `.env`:
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

### 3. Jalankan Migrations

```bash
php artisan migrate
```

Ini akan membuat tabel:
- users (dari Laravel default)
- ecrm_clients
- ecrm_projects
- ecrm_contacts
- ecrm_leads

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Buat User Admin

Jika menggunakan Breeze/Jetstream, buat user melalui register page.

Atau via tinker:
```bash
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
]);
```

### 6. Jalankan Server

```bash
php artisan serve
```

Akses: http://localhost:8000

## 📁 Struktur File

```
ecrm-jasa-desain-temp/
├── app/
│   ├── Http/Controllers/Ecrm/
│   │   ├── DashboardController.php
│   │   ├── ClientController.php
│   │   ├── ProjectController.php
│   │   ├── LeadController.php
│   │   └── ContactController.php
│   └── Models/Ecrm/
│       ├── Client.php
│       ├── Project.php
│       ├── Contact.php
│       └── Lead.php
├── database/migrations/ecrm/
│   ├── 2024_01_01_000001_create_clients_table.php
│   ├── 2024_01_01_000002_create_projects_table.php
│   ├── 2024_01_01_000003_create_contacts_table.php
│   └── 2024_01_01_000004_create_leads_table.php
├── resources/views/
│   ├── layouts/app.blade.php
│   └── ecrm/
│       ├── dashboard/
│       ├── clients/
│       ├── projects/
│       ├── leads/
│       └── contacts/
└── routes/
    ├── web.php (updated)
    └── ecrm.php (new)
```

## 🚀 Quick Start

1. **Install Breeze untuk authentication:**
   ```bash
   composer require laravel/breeze --dev
   php artisan breeze:install blade
   php artisan migrate
   npm install && npm run build
   ```

2. **Setup database di .env**

3. **Jalankan migrations:**
   ```bash
   php artisan migrate
   ```

4. **Buat user admin** (via register atau tinker)

5. **Jalankan server:**
   ```bash
   php artisan serve
   ```

6. **Akses aplikasi:**
   - Login: http://localhost:8000/login
   - Dashboard: http://localhost:8000/ecrm/dashboard

## 📝 Catatan

- Semua routes menggunakan prefix `/ecrm`
- Dashboard: `/ecrm/dashboard`
- Clients: `/ecrm/clients`
- Projects: `/ecrm/projects`
- Leads: `/ecrm/leads`
- Contacts: `/ecrm/contacts`

## 🔧 Troubleshooting

**Error: Route [login] not defined**
- Install Laravel Breeze atau setup authentication

**Error: Class 'App\Models\User' not found**
- Pastikan User model ada di `app/Models/User.php`
- Jika tidak ada, buat dengan: `php artisan make:model User`

**Error: Table doesn't exist**
- Jalankan: `php artisan migrate`

**Error: 404 Not Found**
- Pastikan routes sudah di-include di `web.php`
- Clear cache: `php artisan route:clear`

