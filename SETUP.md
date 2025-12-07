# Setup e-CRM Jasa Desain Mandiri

## Langkah-langkah Setup

### 1. Install Dependencies

```bash
cd C:\laragon\www\ecrm-jasa-desain
composer install
```

### 2. Setup Environment

Buat file `.env` dari `.env.example` (jika belum ada):

```bash
copy .env.example .env
```

Atau buat manual file `.env` dengan konfigurasi:

```env
APP_NAME="e-CRM Jasa Desain"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecrm_jasa_desain
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Buat Database

Buat database baru di MySQL:
- Nama: `ecrm_jasa_desain`
- Charset: `utf8mb4`
- Collation: `utf8mb4_unicode_ci`

### 5. Jalankan Migrations

```bash
php artisan migrate
```

### 6. Buat User Admin (jika menggunakan authentication)

Jika menggunakan Laravel Breeze/Jetstream, jalankan:
```bash
php artisan migrate
```

Atau buat user manual melalui tinker:
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

### 7. Jalankan Server

```bash
php artisan serve
```

Akses aplikasi di: http://localhost:8000

## Catatan

- Pastikan Laragon sudah running
- Pastikan MySQL service aktif
- Jika menggunakan authentication, pastikan sudah setup middleware auth di routes

## Struktur File

```
ecrm-jasa-desain/
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
│   ├── create_clients_table.php
│   ├── create_projects_table.php
│   ├── create_contacts_table.php
│   └── create_leads_table.php
├── resources/views/ecrm/
│   ├── dashboard/
│   ├── clients/
│   ├── projects/
│   ├── leads/
│   └── contacts/
└── routes/
    ├── web.php
    └── ecrm.php
```

