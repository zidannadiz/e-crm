# e-CRM Jasa Desain Mandiri

Sistem Customer Relationship Management untuk Jasa Desain Mandiri.

## Fitur Utama

- **Manajemen Client**: CRUD lengkap untuk data client
- **Manajemen Project**: Tracking proyek desain dari quotation hingga completed
- **Manajemen Lead**: Konversi lead menjadi client
- **History Kontak**: Tracking semua komunikasi dengan client
- **Dashboard Analytics**: Statistik dan laporan

## Teknologi

- Laravel 11
- MySQL/SQLite
- Tailwind CSS
- Blade Templates

## Instalasi

1. Install dependencies:
```bash
composer install
npm install
```

2. Setup environment:
```bash
cp .env.example .env
php artisan key:generate
```

3. Setup database di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecrm_jasa_desain
DB_USERNAME=root
DB_PASSWORD=
```

4. Jalankan migrations:
```bash
php artisan migrate
```

5. Jalankan server:
```bash
php artisan serve
```

Akses: http://localhost:8000

