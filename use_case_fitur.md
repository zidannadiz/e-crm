# 📋 USE CASE FITUR - e-CRM JASA DESAIN

**Tanggal:** 7 Desember 2025  
**Versi:** 1.0  
**Sistem:** e-CRM Jasa Desain

---

## DAFTAR ISI

1. [Use Case 1: Membuat Pesanan Baru](#use-case-1-membuat-pesanan-baru)
2. [Use Case 2: Melihat Daftar Pesanan](#use-case-2-melihat-daftar-pesanan)
3. [Use Case 3: Melihat Detail Pesanan](#use-case-3-melihat-detail-pesanan)
4. [Use Case 4: Update Status Pesanan](#use-case-4-update-status-pesanan)
5. [Use Case 5: Upload Hasil Desain](#use-case-5-upload-hasil-desain)
6. [Use Case 6: Download Hasil Desain](#use-case-6-download-hasil-desain)
7. [Use Case 7: Edit Pesanan](#use-case-7-edit-pesanan)
8. [Use Case 8: Hapus Pesanan](#use-case-8-hapus-pesanan)
9. [Use Case 9: Chat dengan Client/Admin](#use-case-9-chat-dengan-clientadmin)
10. [Use Case 10: Filter & Search Pesanan](#use-case-10-filter--search-pesanan)

---

## USE CASE 1: MEMBUAT PESANAN BARU

### Nama Aktor

-   **Primary Actor:** Client
-   **Secondary Actor:** System (auto-generate nomor order)

### Precondition

1. User sudah login dengan role 'client'
2. User memiliki atau terhubung dengan Client record
3. User memiliki akses ke route `ecrm.orders.create`

### Trigger

User mengklik tombol "Pesan Project" atau mengakses halaman `/ecrm/orders/create`

### Main Flow

1. System menampilkan form create order
2. User mengisi form:
    - Pilih jenis desain (required)
    - Masukkan deskripsi (required)
    - Masukkan kebutuhan khusus (optional)
    - Pilih deadline (optional)
    - Masukkan budget (optional)
3. User klik tombol "Buat Pesanan"
4. System validasi input:
    - Jenis desain harus dipilih
    - Deskripsi tidak boleh kosong
    - Deadline harus format date valid (jika diisi)
    - Budget harus numeric (jika diisi)
5. System cek apakah user memiliki Client record:
    - Jika tidak ada, buat Client record baru dengan data dari user
    - Link user dengan client (update `users.client_id`)
6. System generate nomor order otomatis (format: ORD-YYYYMM-XXXX)
7. System set default status: 'pending'
8. System set default produk_status: 'pending'
9. System simpan order ke database
10. System redirect ke halaman detail order
11. System tampilkan flash message: "Pesanan berhasil dibuat"

### Alternate Flow

#### 3a. Validasi gagal

-   System tampilkan error message di form
-   User perbaiki input dan submit lagi
-   Kembali ke step 3

#### 5a. User tidak memiliki role 'client'

-   System tampilkan error 403: "Hanya client yang dapat membuat pesanan"
-   Use case berakhir

### Postcondition

1. Order baru tersimpan di database dengan status 'pending'
2. Nomor order sudah ter-generate
3. User di-redirect ke halaman detail order
4. Flash message sukses ditampilkan

### Catatan Tambahan

-   Nomor order auto-generate via Model boot method
-   Format: ORD-{YYYY}{MM}-{XXXX} (contoh: ORD-202512-0001)
-   Client record dibuat otomatis jika belum ada

---

## USE CASE 2: MELIHAT DAFTAR PESANAN

### Nama Aktor

-   **Primary Actor:** Admin, CS, atau Client

### Precondition

1. User sudah login
2. User memiliki role yang sesuai (admin, cs, atau client)
3. User memiliki akses ke route `ecrm.orders.index`

### Trigger

User mengakses halaman `/ecrm/orders` atau klik menu "Pesanan"

### Main Flow

1. System load orders berdasarkan role:
    - **Client:** Hanya order milik user (`where('user_id', Auth::id())`)
    - **Admin/CS:** Semua order
2. System load relasi: client dan user
3. System apply filter (jika ada):
    - Filter by status
    - Filter by jenis desain
    - Search by nomor order, deskripsi, atau nama client
4. System sort orders by latest (created_at DESC)
5. System paginate hasil (15 items per page)
6. **Khusus CS:** System hitung statistics:
    - Pending orders count
    - In progress orders count
    - Completed orders count
    - Total orders count
7. System tampilkan view sesuai role:
    - **Admin/Client:** `ecrm.orders.index`
    - **CS:** `ecrm.orders.cs-index` (dengan statistics cards)
8. System render tabel dengan:
    - Nomor order
    - Client name
    - Jenis desain (badge biru)
    - Status (badge warna: yellow/blue/green)
    - Status produk (badge warna: yellow/blue/green)
    - Budget
    - Deadline
    - Action buttons (View, Chat, Edit, Delete)

### Alternate Flow

#### 1a. Client tidak memiliki order

-   System tampilkan pesan: "Tidak ada data order"
-   Tampilkan tombol "Pesan Project"

#### 3a. Filter tidak menghasilkan hasil

-   System tampilkan pesan: "Tidak ada orders ditemukan"
-   Tampilkan tombol "Reset" untuk clear filter

### Postcondition

1. Daftar orders ditampilkan sesuai filter
2. Pagination berfungsi dengan benar
3. Statistics cards ditampilkan (untuk CS)
4. Filter controls tersedia dan berfungsi

### Catatan Tambahan

-   Pagination menggunakan Laravel paginator
-   Search menggunakan LIKE query
-   Badge styling: px-5 py-1 rounded-full untuk konsistensi

---

## USE CASE 3: MELIHAT DETAIL PESANAN

### Nama Aktor

-   **Primary Actor:** Admin, CS, atau Client

### Precondition

1. User sudah login
2. Order ID valid dan ada di database
3. User memiliki akses ke order:
    - **Client:** Hanya order miliknya
    - **Admin/CS:** Semua order

### Trigger

User mengklik tombol "Lihat" atau "View" di halaman list orders

### Main Flow

1. System load order dengan relasi:
    - Client
    - User (creator)
    - Invoices (jika ada)
    - Chat messages (jika ada)
2. System cek akses user:
    - **Client:** Cek apakah `order.user_id === Auth::id()`
    - **Admin/CS:** Allow semua
3. System tampilkan halaman detail order dengan:
    - Informasi pesanan (nomor, jenis desain, deskripsi, kebutuhan)
    - Status order (badge warna)
    - Status produk (badge warna)
    - Budget dan deadline
    - Catatan admin (jika ada)
    - **Section Hasil Desain:**
        - Jika `desain_file` ada: Tampilkan preview/download
        - Jika belum ada: Tampilkan pesan "Belum ada hasil desain"
    - **Section Chat:** (jika ada messages)
    - **Section Invoices:** (jika ada invoices)
4. **Khusus Admin:** Tampilkan form upload desain
5. **Khusus Admin/CS:** Tampilkan form update status

### Alternate Flow

#### 2a. Client tidak memiliki akses

-   System tampilkan error 403: "Anda tidak memiliki akses ke order ini"
-   Redirect ke halaman list orders

#### 3a. Order tidak ditemukan

-   System tampilkan error 404: "Order tidak ditemukan"
-   Redirect ke halaman list orders

### Postcondition

1. Detail order ditampilkan dengan lengkap
2. File desain dapat di-download (jika ada)
3. Form update status tersedia (untuk Admin/CS)
4. Form upload desain tersedia (untuk Admin)

### Catatan Tambahan

-   Preview gambar untuk JPG/PNG
-   Icon file untuk PDF/ZIP/RAR
-   Download link: `/storage/desain/{filename}`

---

## USE CASE 4: UPDATE STATUS PESANAN

### Nama Aktor

-   **Primary Actor:** Admin atau CS

### Precondition

1. User sudah login dengan role 'admin' atau 'cs'
2. Order ID valid dan ada di database
3. User memiliki akses ke route `ecrm.orders.update-status`

### Trigger

User mengisi form update status di halaman detail order dan klik "Update Status"

### Main Flow

1. User pilih status baru dari dropdown:
    - pending, approved, in_progress, review, completed, cancelled
2. User pilih produk status (optional):
    - pending, proses, selesai
3. User masukkan catatan admin (optional)
4. User klik tombol "Update Status"
5. System validasi input:
    - Status: required, enum valid
    - Produk status: nullable, enum valid
    - Catatan admin: nullable, string
6. System validasi role user (harus admin atau CS)
7. System update order di database:
    - Update field `status`
    - Update field `produk_status` (jika diisi)
    - Update field `catatan_admin` (jika diisi)
    - Update `updated_at`
8. System redirect ke halaman detail order
9. System tampilkan flash message: "Status pesanan berhasil diperbarui"

### Alternate Flow

#### 5a. Validasi gagal

-   System tampilkan error message
-   User perbaiki input dan submit lagi
-   Kembali ke step 4

#### 6a. User bukan admin atau CS

-   System tampilkan error 403: "Anda tidak memiliki akses"
-   Use case berakhir

### Postcondition

1. Status order ter-update di database
2. Badge status di halaman detail order ter-update
3. Flash message sukses ditampilkan

### Catatan Tambahan

-   Status flow: pending → approved → in_progress → review → completed
-   Produk status: pending → proses → selesai
-   Catatan admin hanya bisa diisi oleh admin/CS

---

## USE CASE 5: UPLOAD HASIL DESAIN

### Nama Aktor

-   **Primary Actor:** Admin

### Precondition

1. User sudah login dengan role 'admin'
2. Order ID valid dan ada di database
3. Order sudah memiliki status 'completed' atau 'in_progress' (best practice)
4. User memiliki akses ke route `ecrm.orders.upload-desain`

### Trigger

Admin mengklik tombol "Upload Desain" di halaman detail order dan memilih file

### Main Flow

1. Admin klik tombol "Upload Desain"
2. System tampilkan form upload file
3. Admin pilih file desain:
    - Format: JPG, PNG, PDF, ZIP, RAR
    - Max size: 10MB
4. Admin klik tombol "Upload"
5. System validasi file:
    - File required
    - Mime type: jpg, jpeg, png, pdf, zip, rar
    - Max size: 10240 KB (10MB)
6. System cek apakah ada file lama:
    - Jika ada, hapus file lama dari `public/storage/desain/`
7. System generate nama file baru:
    - Format: `{timestamp}_{nomor_order}_{original_filename}`
    - Contoh: `1701936000_ORD-202512-0001_logo-final.png`
8. System cek directory `public/storage/desain/`:
    - Jika tidak ada, buat directory dengan permission 0755
9. System move file ke storage:
    - Path: `public/storage/desain/{filename}`
10. System update database:
    - Update field `desain_file` dengan nama file
    - Update `updated_at`
11. System redirect ke halaman detail order
12. System tampilkan flash message: "Desain berhasil diupload"

### Alternate Flow

#### 5a. Validasi file gagal

-   System tampilkan error message:
    -   "File harus diisi"
    -   "Format file tidak didukung (hanya JPG, PNG, PDF, ZIP, RAR)"
    -   "Ukuran file maksimal 10MB"
-   User pilih file lain dan upload lagi
-   Kembali ke step 4

#### 6a. Gagal hapus file lama

-   System log error (jika ada logging)
-   Lanjutkan proses upload (file baru tetap diupload)
-   File lama tetap ada di storage (manual cleanup diperlukan)

#### 9a. Gagal move file

-   System tampilkan error: "Gagal mengupload file"
-   System tidak update database
-   Use case berakhir dengan error

### Postcondition

1. File desain tersimpan di `public/storage/desain/`
2. Database ter-update dengan nama file
3. Section "Hasil Desain" di halaman detail order menampilkan file baru
4. Flash message sukses ditampilkan

### Catatan Tambahan

-   File lama otomatis dihapus saat upload baru
-   Nama file unik dengan timestamp untuk menghindari conflict
-   Directory dibuat otomatis jika belum ada
-   Client dapat melihat dan download file setelah diupload

---

## USE CASE 6: DOWNLOAD HASIL DESAIN

### Nama Aktor

-   **Primary Actor:** Client atau Admin

### Precondition

1. User sudah login
2. Order ID valid dan ada di database
3. Order memiliki `desain_file` (tidak null)
4. File ada di storage `public/storage/desain/{filename}`
5. User memiliki akses ke order:
    - **Client:** Hanya order miliknya
    - **Admin:** Semua order

### Trigger

User mengklik tombol "Download" di section "Hasil Desain"

### Main Flow

1. User klik tombol "Download Hasil Desain"
2. System validasi akses:
    - **Client:** Cek apakah `order.user_id === Auth::id()`
    - **Admin:** Allow semua
3. System cek apakah file ada:
    - Path: `public/storage/desain/{desain_file}`
4. System return file sebagai download response:
    - Content-Type sesuai mime type
    - Content-Disposition: attachment
    - Filename: original filename atau `desain-{nomor_order}.{ext}`
5. Browser download file ke komputer user

### Alternate Flow

#### 2a. Client tidak memiliki akses

-   System tampilkan error 403: "Anda tidak memiliki akses"
-   Use case berakhir

#### 3a. File tidak ditemukan

-   System tampilkan error 404: "File tidak ditemukan"
-   System log error untuk admin
-   Use case berakhir

### Postcondition

1. File berhasil di-download ke komputer user
2. File tetap ada di storage (tidak dihapus)

### Catatan Tambahan

-   Download menggunakan Laravel response()->download()
-   File tetap tersimpan di server
-   Original filename dipertahankan jika memungkinkan

---

## USE CASE 7: EDIT PESANAN

### Nama Aktor

-   **Primary Actor:** Client atau Admin

### Precondition

1. User sudah login
2. Order ID valid dan ada di database
3. User memiliki akses ke order
4. **Khusus Client:** Order status harus 'pending'

### Trigger

User mengklik tombol "Edit" di halaman detail order

### Main Flow

1. System load order dengan relasi client
2. System cek akses:
    - **Client:** Cek `order.user_id === Auth::id()` dan `order.status === 'pending'`
    - **Admin:** Allow semua
3. System tampilkan form edit dengan data order saat ini
4. User edit data:
    - **Client:** Hanya bisa edit jenis desain, deskripsi, kebutuhan, deadline
    - **Admin:** Bisa edit semua field termasuk status dan budget
5. User klik tombol "Simpan Perubahan"
6. System validasi input sesuai role
7. System update order di database
8. System redirect ke halaman detail order
9. System tampilkan flash message: "Pesanan berhasil diperbarui"

### Alternate Flow

#### 2a. Client tidak memiliki akses

-   System tampilkan error 403
-   Redirect ke halaman list orders

#### 2b. Client mencoba edit order yang status bukan 'pending'

-   System tampilkan error: "Pesanan hanya dapat diedit jika status masih pending"
-   Redirect ke halaman detail order

#### 6a. Validasi gagal

-   System tampilkan error message di form
-   User perbaiki input dan submit lagi
-   Kembali ke step 5

### Postcondition

1. Data order ter-update di database
2. Halaman detail order menampilkan data baru
3. Flash message sukses ditampilkan

### Catatan Tambahan

-   Client hanya bisa edit order dengan status 'pending'
-   Admin bisa edit order dengan status apapun
-   Nomor order tidak bisa diubah (auto-generated)

---

## USE CASE 8: HAPUS PESANAN

### Nama Aktor

-   **Primary Actor:** Admin

### Precondition

1. User sudah login dengan role 'admin'
2. Order ID valid dan ada di database
3. User memiliki akses ke route `ecrm.orders.destroy`

### Trigger

Admin mengklik tombol "Hapus" dan konfirmasi di modal

### Main Flow

1. Admin klik tombol "Hapus" di halaman detail order
2. System tampilkan modal konfirmasi:
    - Pesan: "Apakah Anda yakin ingin menghapus pesanan {nomor_order}?"
    - Tombol "Batal" dan "Ya, Hapus"
3. Admin klik "Ya, Hapus"
4. System cek apakah ada relasi:
    - Cek apakah ada invoices terkait
    - Cek apakah ada chat messages terkait
5. System hapus file desain jika ada:
    - Path: `public/storage/desain/{desain_file}`
6. System hapus order dari database
7. System redirect ke halaman list orders
8. System tampilkan flash message: "Pesanan berhasil dihapus"

### Alternate Flow

#### 4a. Ada invoices terkait

-   System tampilkan error: "Tidak dapat menghapus pesanan yang sudah memiliki invoice"
-   System tidak hapus order
-   Use case berakhir

#### 5a. Gagal hapus file

-   System log error (jika ada logging)
-   Lanjutkan proses hapus order (file tetap ada di storage)
-   Manual cleanup diperlukan

### Postcondition

1. Order terhapus dari database
2. File desain terhapus dari storage (jika ada)
3. User di-redirect ke halaman list orders
4. Flash message sukses ditampilkan

### Catatan Tambahan

-   Hanya admin yang bisa hapus order
-   Order dengan invoice tidak bisa dihapus (data integrity)
-   File desain otomatis dihapus saat order dihapus

---

## USE CASE 9: CHAT DENGAN CLIENT/ADMIN

### Nama Aktor

-   **Primary Actor:** Admin, CS, atau Client

### Precondition

1. User sudah login
2. Order ID valid dan ada di database
3. User memiliki akses ke order
4. User memiliki akses ke route `ecrm.chat.index`

### Trigger

User mengklik tombol "Chat" atau "Pesan" di halaman list/detail order

### Main Flow

1. User klik tombol "Chat" atau "Pesan"
2. System load order dengan relasi client dan user
3. System cek akses order
4. System load chat messages terkait order:
    - Sort by `created_at` ASC
    - Load relasi user (sender)
5. System tampilkan halaman chat dengan:
    - Header: Nomor order dan nama client
    - List messages (scrollable)
    - Form input pesan
    - Tombol "Quick Reply" (untuk Admin/CS)
6. User ketik pesan di form
7. User klik tombol "Kirim"
8. System validasi: message tidak boleh kosong
9. System create ChatMessage:
    - `order_id`
    - `user_id` (sender)
    - `message`
    - `is_read` = false
10. System simpan ke database
11. System update tampilan chat dengan pesan baru
12. System mark message sebagai read untuk sender

### Alternate Flow

#### 8a. Message kosong

-   System tampilkan error: "Pesan tidak boleh kosong"
-   User ketik pesan lagi
-   Kembali ke step 7

#### 9a. Quick Reply digunakan

-   Admin/CS pilih quick reply template
-   System load template dari QuickReply model
-   System isi form dengan template
-   Lanjutkan ke step 7

### Postcondition

1. Chat message tersimpan di database
2. Tampilan chat ter-update dengan pesan baru
3. Message status: `is_read` = false (untuk recipient)

### Catatan Tambahan

-   Chat real-time bisa ditambahkan dengan WebSocket/Pusher
-   Quick Reply hanya untuk Admin/CS
-   Unread count ter-update di dashboard

---

## USE CASE 10: FILTER & SEARCH PESANAN

### Nama Aktor

-   **Primary Actor:** Admin, CS, atau Client

### Precondition

1. User sudah login
2. User berada di halaman list orders
3. User memiliki akses ke route `ecrm.orders.index`

### Trigger

User menggunakan filter atau search di halaman list orders

### Main Flow

1. User pilih filter status (optional):
    - Semua Status, Pending, Approved, In Progress, Review, Completed, Cancelled
2. User pilih filter jenis desain (optional):
    - Semua Jenis Desain, Logo, Branding, Web Design, UI/UX, Print Design, dll
3. User ketik keyword di search box (optional):
    - Search di nomor order, deskripsi, atau nama client
4. User klik tombol "Cari"
5. System terima GET request dengan parameter:
    - `status` (jika dipilih)
    - `jenis_desain` (jika dipilih)
    - `search` (jika diisi)
6. System apply filter ke query:
    - Filter by status (jika ada)
    - Filter by jenis desain (jika ada)
    - Search di nomor_order, deskripsi, client.nama (jika ada)
7. System load orders dengan filter
8. System tampilkan hasil di tabel
9. System tampilkan filter yang aktif di form

### Alternate Flow

#### 4a. User klik "Reset"

-   System clear semua filter
-   System redirect ke halaman list orders tanpa parameter
-   System tampilkan semua orders (tanpa filter)

#### 7a. Tidak ada hasil

-   System tampilkan pesan: "Tidak ada orders ditemukan"
-   System tampilkan tombol "Reset" untuk clear filter

### Postcondition

1. Orders ditampilkan sesuai filter yang dipilih
2. Filter controls menampilkan nilai yang aktif
3. Pagination tetap berfungsi dengan filter

### Catatan Tambahan

-   Filter menggunakan GET request (bisa di-bookmark)
-   Search menggunakan LIKE query (case-insensitive)
-   Filter bisa dikombinasikan (status + jenis desain + search)

---

## RINGKASAN USE CASE

| No  | Use Case                 | Aktor Utama     | Precondition         | Kompleksitas |
| --- | ------------------------ | --------------- | -------------------- | ------------ |
| 1   | Membuat Pesanan Baru     | Client          | Login, role client   | Medium       |
| 2   | Melihat Daftar Pesanan   | Admin/CS/Client | Login                | Low          |
| 3   | Melihat Detail Pesanan   | Admin/CS/Client | Login, akses order   | Low          |
| 4   | Update Status Pesanan    | Admin/CS        | Login, role admin/cs | Low          |
| 5   | Upload Hasil Desain      | Admin           | Login, role admin    | Medium       |
| 6   | Download Hasil Desain    | Client/Admin    | Login, file ada      | Low          |
| 7   | Edit Pesanan             | Client/Admin    | Login, akses order   | Medium       |
| 8   | Hapus Pesanan            | Admin           | Login, role admin    | Medium       |
| 9   | Chat dengan Client/Admin | Admin/CS/Client | Login, akses order   | Medium       |
| 10  | Filter & Search Pesanan  | Admin/CS/Client | Login                | Low          |

---

**End of Document**
