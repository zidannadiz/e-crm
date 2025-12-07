# 📊 ALUR DATA SISTEM e-CRM JASA DESAIN

**Tanggal:** 7 Desember 2025  
**Versi:** 1.0

---

## 🎯 OVERVIEW

Dokumen ini menjelaskan alur data (data flow / data lifecycle) untuk sistem e-CRM Jasa Desain, mulai dari input user, proses backend, penyimpanan database, hingga output ke user.

---

## 1. ALUR DATA: MEMBUAT PESANAN (CREATE ORDER)

### 1.1 Input User (Client)
- **Aktor:** Client
- **Input:**
  - Jenis desain (logo, branding, web_design, ui_ux, print_design, dll)
  - Deskripsi pesanan
  - Kebutuhan khusus (optional)
  - Deadline (optional)
  - Budget (optional)

### 1.2 Proses Frontend
- User mengisi form di `resources/views/ecrm/orders/create.blade.php`
- Form validation di frontend (HTML5 validation)
- Submit form via POST ke route `ecrm.orders.store`

### 1.3 Proses Backend (Controller)
- **File:** `app/Http/Controllers/Ecrm/OrderController.php`
- **Method:** `store(Request $request)`
- **Proses:**
  1. Validasi role user (harus 'client')
  2. Cek atau buat record Client jika belum ada
  3. Link user dengan client (update `users.client_id`)
  4. Validasi input data:
     - `jenis_desain`: required, enum
     - `deskripsi`: required, string
     - `kebutuhan`: nullable, string
     - `deadline`: nullable, date
     - `budget`: nullable, numeric
  5. Generate nomor order otomatis via Model boot method
  6. Set default status: `pending`
  7. Set default produk_status: `pending`
  8. Set `user_id` dari authenticated user
  9. Set `client_id` dari user's client

### 1.4 Penyimpanan Database
- **Table:** `ecrm_orders`
- **Fields yang disimpan:**
  - `client_id` (FK ke ecrm_clients)
  - `user_id` (FK ke users)
  - `nomor_order` (auto-generated: ORD-YYYYMM-XXXX)
  - `jenis_desain`
  - `deskripsi`
  - `kebutuhan`
  - `status` (default: 'pending')
  - `produk_status` (default: 'pending')
  - `budget`
  - `deadline`
  - `created_at`, `updated_at`

### 1.5 Output ke User
- Redirect ke halaman detail pesanan (`ecrm.orders.show`)
- Flash message: "Pesanan berhasil dibuat"
- Tampilkan detail pesanan dengan nomor order yang sudah ter-generate

---

## 2. ALUR DATA: MELIHAT DAFTAR PESANAN (LIST ORDERS)

### 2.1 Input User
- **Aktor:** Admin, CS, atau Client
- **Input:**
  - Filter status (optional)
  - Filter jenis desain (optional)
  - Search keyword (optional)
  - Pagination page number

### 2.2 Proses Frontend
- User mengakses route `ecrm.orders.index`
- Form filter dan search di halaman list
- Submit filter via GET request

### 2.3 Proses Backend (Controller)
- **File:** `app/Http/Controllers/Ecrm/OrderController.php`
- **Method:** `index(Request $request)`
- **Proses:**
  1. Cek role user:
     - **Client:** Filter hanya order milik user (`where('user_id', Auth::id())`)
     - **Admin/CS:** Tampilkan semua order
  2. Load relasi: `with(['client', 'user'])`
  3. Apply filter search (jika ada):
     - Search di `nomor_order`
     - Search di `deskripsi`
     - Search di `client.nama`
  4. Apply filter status (jika ada)
  5. Apply filter jenis_desain (jika ada)
  6. Sort by `latest()` (created_at DESC)
  7. Paginate (15 items per page)
  8. **Khusus CS:** Hitung statistics (pending, in_progress, completed, total)

### 2.4 Query Database
- **Query Builder:** Eloquent ORM
- **Tables:** `ecrm_orders`, `ecrm_clients`, `users`
- **Joins:** Automatic via Eloquent relationships
- **Result:** Collection of Order models dengan eager loaded relations

### 2.5 Output ke User
- **Admin/Client:** View `ecrm.orders.index`
- **CS:** View `ecrm.orders.cs-index` (dengan statistics cards)
- Tampilkan:
  - Tabel orders dengan pagination
  - Filter controls
  - Search input
  - Badge status dengan warna (pending=yellow, in_progress=blue, completed=green)
  - Badge jenis desain (blue)
  - Badge status produk (green/blue/yellow)
  - Action buttons (View, Chat, Edit, Delete)

---

## 3. ALUR DATA: UPDATE STATUS PESANAN

### 3.1 Input User
- **Aktor:** Admin atau CS
- **Input:**
  - Order ID
  - Status baru (pending, approved, in_progress, review, completed, cancelled)
  - Produk status (pending, proses, selesai) - optional
  - Catatan admin - optional

### 3.2 Proses Frontend
- Form di halaman detail order (`ecrm.orders.show`)
- Submit via PATCH ke route `ecrm.orders.update-status`

### 3.3 Proses Backend (Controller)
- **File:** `app/Http/Controllers/Ecrm/OrderController.php`
- **Method:** `updateStatus(Request $request, Order $order)`
- **Proses:**
  1. Validasi role (harus admin atau CS)
  2. Validasi input:
     - `status`: required, enum
     - `produk_status`: nullable, enum
     - `catatan_admin`: nullable, string
  3. Update order dengan data baru
  4. Trigger event (jika ada notification system)

### 3.4 Penyimpanan Database
- **Table:** `ecrm_orders`
- **Update fields:**
  - `status`
  - `produk_status` (jika diisi)
  - `catatan_admin` (jika diisi)
  - `updated_at` (auto)

### 3.5 Output ke User
- Redirect back ke halaman detail order
- Flash message: "Status pesanan berhasil diperbarui"
- Tampilkan status baru dengan badge warna yang sesuai

---

## 4. ALUR DATA: UPLOAD DESAIN (UPLOAD DESIGN FILE)

### 4.1 Input User
- **Aktor:** Admin
- **Input:**
  - Order ID
  - File desain (JPG, PNG, PDF, ZIP, RAR)
  - Max size: 10MB

### 4.2 Proses Frontend
- Form upload di halaman detail order (`ecrm.orders.show`)
- File input dengan validation
- Submit via POST ke route `ecrm.orders.upload-desain`

### 4.3 Proses Backend (Controller)
- **File:** `app/Http/Controllers/Ecrm/OrderController.php`
- **Method:** `uploadDesain(Request $request, Order $order)`
- **Proses:**
  1. Validasi role (harus admin)
  2. Validasi file:
     - Required
     - Mime types: jpg, jpeg, png, pdf, zip, rar
     - Max size: 10240 KB (10MB)
  3. Hapus file lama jika ada:
     - Path: `public/storage/desain/{old_filename}`
     - Delete file dari filesystem
  4. Generate nama file baru:
     - Format: `{timestamp}_{nomor_order}_{original_filename}`
  5. Buat directory jika belum ada:
     - Path: `public/storage/desain/`
     - Permission: 0755
  6. Move file ke storage:
     - `$file->move($uploadPath, $fileName)`
  7. Update database dengan nama file

### 4.4 Penyimpanan
- **Filesystem:**
  - Path: `public/storage/desain/`
  - Filename: `{timestamp}_{nomor_order}_{original_filename}`
- **Database:**
  - **Table:** `ecrm_orders`
  - **Field:** `desain_file` (string, nama file)
  - Update `updated_at`

### 4.5 Output ke User
- Redirect back ke halaman detail order
- Flash message: "Desain berhasil diupload"
- Tampilkan section "Hasil Desain":
  - Preview gambar (jika JPG/PNG)
  - Icon file + nama (jika PDF/ZIP/RAR)
  - Tombol download

---

## 5. ALUR DATA: MELIHAT HASIL DESAIN (VIEW DESIGN RESULT)

### 5.1 Input User
- **Aktor:** Client atau Admin
- **Input:**
  - Order ID

### 5.2 Proses Frontend
- User mengakses halaman detail order (`ecrm.orders.show`)
- Section "Hasil Desain" ditampilkan jika `desain_file` tidak null

### 5.3 Proses Backend (Controller)
- **File:** `app/Http/Controllers/Ecrm/OrderController.php`
- **Method:** `show(Order $order)`
- **Proses:**
  1. Load order dengan relasi (client, user, invoices)
  2. Cek akses user:
     - Client: hanya order miliknya
     - Admin/CS: semua order
  3. Pass data ke view:
     - Order object
     - File path: `storage/desain/{desain_file}`
     - File extension untuk menentukan preview type

### 4.4 Query Database
- **Table:** `ecrm_orders`
- **Load:** Order dengan eager loaded relations
- **Check:** Field `desain_file` (nullable)

### 5.5 Output ke User
- **Jika file ada:**
  - Preview gambar (untuk JPG/PNG)
  - Icon + nama file (untuk PDF/ZIP/RAR)
  - Tombol download dengan link ke file
- **Jika file belum ada:**
  - Pesan: "Belum ada hasil desain yang diupload"
- **Khusus Client:**
  - Section "Hasil Desain Tersedia" di halaman `my-orders`

---

## 6. ALUR DATA: CHAT/MESSAGES

### 6.1 Input User
- **Aktor:** Admin, CS, atau Client
- **Input:**
  - Order ID
  - Pesan text
  - Quick Reply ID (optional)

### 6.2 Proses Frontend
- Form chat di halaman chat (`ecrm.chat.index`)
- Real-time update via AJAX (jika ada)
- Submit via POST ke route `ecrm.chat.send`

### 6.3 Proses Backend (Controller)
- **File:** `app/Http/Controllers/Ecrm/ChatController.php`
- **Method:** `send(Request $request, Order $order)`
- **Proses:**
  1. Validasi input (message required)
  2. Cek akses order
  3. Create ChatMessage:
     - `order_id`
     - `user_id` (sender)
     - `message`
     - `is_read` (default: false)
  4. Jika quick reply: load template dari QuickReply model
  5. Trigger notification (jika ada)

### 6.4 Penyimpanan Database
- **Table:** `ecrm_chat_messages`
- **Fields:**
  - `order_id` (FK)
  - `user_id` (FK)
  - `message` (text)
  - `is_read` (boolean, default: false)
  - `created_at`, `updated_at`

### 6.5 Output ke User
- Response JSON (jika AJAX) atau redirect
- Tampilkan pesan baru di chat window
- Update unread count di dashboard

---

## 7. ALUR DATA: INVOICE & PAYMENT

### 7.1 Membuat Invoice
- **Input:** Order ID, client, total amount, due date
- **Proses:**
  1. Admin membuat invoice dari order
  2. Generate nomor invoice
  3. Simpan ke `ecrm_invoices`
  4. Status default: 'draft'
- **Output:** Invoice dengan nomor invoice

### 7.2 Mengirim Invoice
- **Input:** Invoice ID
- **Proses:**
  1. Update status: 'draft' → 'sent'
  2. Kirim email ke client (jika ada email service)
  3. Log pengiriman
- **Output:** Invoice status menjadi 'sent'

### 7.3 Verifikasi Pembayaran
- **Input:** Payment ID, verification status
- **Proses:**
  1. Admin verifikasi payment
  2. Update status: 'pending' → 'verified'
  3. Update invoice total_paid
  4. Jika lunas, update invoice status: 'sent' → 'paid'
- **Output:** Payment status 'verified', invoice mungkin 'paid'

---

## 8. DIAGRAM ALUR DATA SINGKAT

```
[User Input] 
    ↓
[Frontend Validation]
    ↓
[HTTP Request] → [Route] → [Middleware: auth, role]
    ↓
[Controller Method]
    ↓
[Validation] → [Business Logic]
    ↓
[Database Query/Update]
    ↓
[File Storage] (jika upload file)
    ↓
[Response] → [View Rendering]
    ↓
[User Output]
```

---

## 9. CATATAN PENTING

### 9.1 Role-Based Access Control
- Setiap request melewati middleware `role:admin|cs|client`
- Controller melakukan double-check akses
- Client hanya bisa akses data miliknya

### 9.2 File Storage
- Path: `public/storage/desain/`
- Naming: `{timestamp}_{nomor_order}_{original_filename}`
- Old file dihapus saat upload baru

### 9.3 Auto-Generated Fields
- `nomor_order`: Auto-generate via Model boot method
- Format: `ORD-YYYYMM-XXXX`
- Increment per bulan

### 9.4 Status Flow
- **Order Status:** pending → approved → in_progress → review → completed
- **Produk Status:** pending → proses → selesai
- **Invoice Status:** draft → sent → paid/overdue

---

## 10. TABEL DATABASE UTAMA

### ecrm_orders
- Primary key: `id`
- Foreign keys: `client_id`, `user_id`
- Auto-generated: `nomor_order`
- File storage: `desain_file` (filename only)

### ecrm_clients
- Primary key: `id`
- Linked to: `users.client_id`

### ecrm_chat_messages
- Primary key: `id`
- Foreign keys: `order_id`, `user_id`
- Status: `is_read`

### ecrm_invoices
- Primary key: `id`
- Foreign keys: `order_id`, `client_id`
- Status: draft, sent, paid, overdue

### ecrm_payments
- Primary key: `id`
- Foreign keys: `invoice_id`
- Status: pending, verified, rejected

---

**End of Document**

