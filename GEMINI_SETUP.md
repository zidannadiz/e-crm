# Setup Gemini API Key untuk Fitur AI Chat

## Status Fitur AI
✅ **Fitur AI sudah diimplementasikan** di `ChatController::aiAnswer()` dan `generateAIResponse()`

Fitur AI akan berfungsi setelah Anda setup Gemini API Key.

---

## Cara Setup Gemini API Key

### 1. Dapatkan API Key dari Google

1. **Buka Google AI Studio**
   - Kunjungi: https://makersuite.google.com/app/apikey
   - Atau: https://aistudio.google.com/app/apikey

2. **Login dengan Google Account**
   - Gunakan akun Google Anda untuk login

3. **Buat API Key Baru**
   - Klik tombol "Create API Key" atau "Get API Key"
   - Pilih project Google Cloud (atau buat baru)
   - Copy API Key yang diberikan

### 2. Tambahkan ke File .env

1. **Buka file `.env`** di root project:
   ```
   C:\laragon\www\ecrm-jasa-desain\ecrm-jasa-desain-temp\.env
   ```

2. **Tambahkan baris berikut** (atau update jika sudah ada):
   ```env
   GEMINI_API_KEY=your_api_key_here
   ```
   
   Contoh:
   ```env
   GEMINI_API_KEY=AIzaSyBxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   ```

3. **Simpan file `.env`**

### 3. Clear Cache (Penting!)

Setelah menambahkan API key, jalankan command berikut untuk clear cache:

```bash
php artisan config:clear
php artisan cache:clear
```

Atau jika menggunakan Laravel Sail:
```bash
php artisan optimize:clear
```

### 4. Test Fitur AI

1. **Login sebagai Client**
2. **Buka salah satu Order**
3. **Klik tab "Chat"**
4. **Di bagian bawah ada form "Tanya AI"**
5. **Ketik pertanyaan**, contoh:
   - "Berapa lama proses desain logo?"
   - "Format file apa saja yang akan saya dapatkan?"
   - "Apakah bisa revisi?"
6. **Klik tombol "🤖 Tanya AI"**
7. **Tunggu beberapa detik** - AI akan menjawab berdasarkan konteks order Anda

---

## Troubleshooting

### ❌ Error: "Maaf, fitur AI belum dikonfigurasi"
**Penyebab:** API Key belum di-set atau tidak terbaca

**Solusi:**
1. Pastikan API Key sudah ditambahkan di `.env`
2. Jalankan `php artisan config:clear`
3. Restart web server (Laragon)
4. Pastikan tidak ada spasi di awal/akhir API Key

### ❌ Error: "Maaf, terjadi kesalahan saat menghubungi AI"
**Penyebab:** 
- API Key tidak valid
- API Key sudah expired
- Quota API sudah habis
- Koneksi internet bermasalah

**Solusi:**
1. Cek API Key di Google AI Studio apakah masih aktif
2. Pastikan API Key tidak expired
3. Cek quota di Google Cloud Console
4. Cek koneksi internet
5. Cek log error di `storage/logs/laravel.log`

### ❌ Error: 403 Forbidden atau 401 Unauthorized
**Penyebab:** API Key tidak valid atau tidak memiliki permission

**Solusi:**
1. Buat API Key baru di Google AI Studio
2. Pastikan API Key memiliki akses ke Gemini API
3. Enable Gemini API di Google Cloud Console

---

## Catatan Penting

1. **API Key adalah rahasia** - Jangan commit ke Git!
   - File `.env` sudah ada di `.gitignore`
   - Jangan share API Key ke publik

2. **Quota & Limit**
   - Google memberikan free tier untuk Gemini API
   - Ada batas request per menit/jam
   - Cek quota di: https://console.cloud.google.com/apis/api/generativelanguage.googleapis.com/quotas

3. **Model yang digunakan**
   - Saat ini menggunakan: `gemini-pro`
   - Bisa diubah ke `gemini-pro-vision` jika perlu

4. **Context yang dikirim ke AI**
   - Jenis desain yang dipesan
   - Deskripsi order
   - Kebutuhan tambahan
   - Semua dalam bahasa Indonesia

---

## Testing Manual

Untuk test apakah API Key sudah bekerja, bisa test langsung via terminal:

```bash
php artisan tinker
```

Kemudian jalankan:
```php
$apiKey = env('GEMINI_API_KEY');
echo $apiKey; // Pastikan tidak null

// Test API call
use Illuminate\Support\Facades\Http;
$response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
    'contents' => [
        [
            'parts' => [
                ['text' => 'Halo, apa kabar?']
            ]
        ]
    ]
]);
dd($response->json());
```

Jika berhasil, akan return response dari Gemini API.

---

## Update Kode (Opsional)

Jika ingin menggunakan model Gemini yang lebih baru, edit file:
`app/Http/Controllers/Ecrm/ChatController.php`

Ubah URL di method `generateAIResponse()`:
```php
// Dari:
'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}'

// Ke (contoh untuk gemini-1.5-pro):
'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key={$apiKey}'
```

---

## Support

Jika masih ada masalah, cek:
1. Log error: `storage/logs/laravel.log`
2. Google AI Studio: https://aistudio.google.com/
3. Gemini API Documentation: https://ai.google.dev/docs

