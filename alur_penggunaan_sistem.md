# 📋 ALUR PENGGUNAAN SISTEM - e-CRM JASA DESAIN

**Tanggal:** 7 Desember 2025  
**Versi:** 1.0  
**Sistem:** e-CRM Jasa Desain

---

## DESKRIPSI SISTEM

Sistem e-CRM Jasa Desain adalah aplikasi manajemen pesanan desain yang memungkinkan Client membuat dan mengelola pesanan, Admin mengelola pesanan dan mengupload hasil desain, serta Customer Service (CS) membantu mengelola status pesanan dan berkomunikasi dengan Client.

**Aktor Utama:**

-   **Client:** Pengguna yang membuat dan mengelola pesanan desain mereka
-   **Admin:** Pengelola sistem yang mengelola pesanan dan mengupload hasil desain
-   **CS (Customer Service):** Staf yang membantu mengelola status pesanan dan berkomunikasi dengan Client

**Fitur Utama:**

-   Membuat dan mengelola pesanan desain
-   Upload dan download hasil desain
-   Update status pesanan
-   Chat antar aktor
-   Filter dan pencarian pesanan

---

## ALUR PENGGUNAAN OLEH CLIENT

### 1. ALUR MEMBUAT PESANAN BARU

**Tujuan:** Client membuat pesanan desain baru

**Langkah-langkah:**

1. **Client mengakses sistem**

    - Client membuka aplikasi dan login dengan kredensial
    - **Input:** Email dan password
    - **Proses:** Sistem validasi kredensial
    - **Output:** Jika valid, Client masuk ke dashboard

2. **Client mengklik "Pesan Project"**

    - Client mengklik tombol atau menu "Pesan Project"
    - **Input:** Klik tombol
    - **Proses:** Sistem cek role user
    - **Output:**
        - Jika role = 'client', tampilkan form create order
        - Jika role ≠ 'client', tampilkan error 403

3. **Client mengisi form pesanan**

    - Client mengisi form dengan data:
        - Jenis desain (required): Logo, Branding, Web Design, UI/UX, Print Design, dll
        - Deskripsi (required): Penjelasan kebutuhan desain
        - Kebutuhan khusus (optional): Detail tambahan
        - Deadline (optional): Tanggal deadline
        - Budget (optional): Anggaran yang tersedia
    - **Input:** Data form dari Client
    - **Proses:** Sistem menampilkan form dengan field yang tersedia
    - **Output:** Form siap diisi

4. **Client mengklik "Buat Pesanan"**

    - Client submit form setelah mengisi data
    - **Input:** Data form yang diisi Client
    - **Proses:** Sistem validasi input
    - **Output:**
        - Jika validasi gagal: Tampilkan error message, Client perbaiki input
        - Jika validasi berhasil: Lanjut ke step 5

5. **Sistem cek Client record**

    - Sistem mengecek apakah user memiliki Client record
    - **Input:** User ID dari session
    - **Proses:** Query database untuk cek Client record
    - **Output:**
        - Jika Client belum ada: Buat Client record baru, link user dengan client
        - Jika Client sudah ada: Gunakan Client record yang ada

6. **Sistem generate nomor order**

    - Sistem otomatis membuat nomor order
    - **Input:** Data order yang akan dibuat
    - **Proses:** Generate nomor dengan format ORD-YYYYMM-XXXX
    - **Output:** Nomor order unik (contoh: ORD-202512-0001)

7. **Sistem set default status**

    - Sistem mengatur status default untuk order baru
    - **Input:** Order baru
    - **Proses:** Set status = 'pending', produk_status = 'pending'
    - **Output:** Order dengan status default

8. **Sistem simpan ke database**

    - Sistem menyimpan order ke database
    - **Input:** Data order lengkap (nomor, jenis desain, deskripsi, status, dll)
    - **Proses:** INSERT INTO ecrm_orders
    - **Output:** Order tersimpan di database

9. **Sistem redirect ke detail order**

    - Sistem mengarahkan Client ke halaman detail order
    - **Input:** Order ID yang baru dibuat
    - **Proses:** Redirect ke route detail order
    - **Output:** Halaman detail order ditampilkan

10. **Sistem tampilkan flash message**
    - Sistem menampilkan pesan sukses
    - **Input:** Order berhasil dibuat
    - **Proses:** Set flash message session
    - **Output:** Flash message "Pesanan berhasil dibuat" ditampilkan

**Data Flow:**

```
Client Input → Validasi → Cek Client Record → Generate Nomor Order →
Set Default Status → Simpan Database → Redirect → Tampilkan Pesan
```

---

### 2. ALUR MELIHAT DAFTAR PESANAN (CLIENT)

**Tujuan:** Client melihat daftar pesanan yang telah dibuat

**Langkah-langkah:**

1. **Client mengakses halaman pesanan**

    - Client mengklik menu "Pesanan" atau mengakses `/ecrm/orders`
    - **Input:** Request GET ke route orders
    - **Proses:** Sistem cek authentication dan role
    - **Output:** Jika authorized, lanjut ke step 2

2. **Sistem load orders milik Client**

    - Sistem mengambil orders dari database
    - **Input:** User ID dari session
    - **Proses:** Query `WHERE user_id = Auth::id()`
    - **Output:** List orders milik Client

3. **Sistem apply filter (jika ada)**

    - Sistem menerapkan filter yang dipilih Client
    - **Input:** Parameter filter (status, jenis desain, search keyword)
    - **Proses:**
        - Filter by status (jika dipilih)
        - Filter by jenis desain (jika dipilih)
        - Search di nomor order, deskripsi (jika ada keyword)
    - **Output:** Orders yang sudah difilter

4. **Sistem sort dan paginate**

    - Sistem mengurutkan dan membagi hasil
    - **Input:** List orders
    - **Proses:** Sort by created_at DESC, paginate 15 items per page
    - **Output:** Orders terurut dan ter-paginate

5. **Sistem tampilkan tabel orders**
    - Sistem menampilkan orders dalam format tabel
    - **Input:** Orders yang sudah difilter dan di-paginate
    - **Proses:** Render view dengan data orders
    - **Output:** Tabel dengan kolom:
        - Nomor order
        - Jenis desain (badge biru)
        - Status (badge warna: yellow/blue/green)
        - Status produk (badge warna)
        - Budget
        - Deadline
        - Action buttons (View, Chat, Edit)

**Data Flow:**

```
Request → Load Orders (Filter by User ID) → Apply Filter → Sort & Paginate →
Render Tabel → Tampilkan ke Client
```

**Percabangan:**

-   Jika Client tidak memiliki order: Tampilkan pesan "Tidak ada data order" + tombol "Pesan Project"
-   Jika filter tidak menghasilkan hasil: Tampilkan pesan "Tidak ada orders ditemukan" + tombol "Reset"

---

### 3. ALUR MELIHAT DETAIL PESANAN (CLIENT)

**Tujuan:** Client melihat detail lengkap pesanan

**Langkah-langkah:**

1. **Client mengklik "Lihat" atau "View"**

    - Client mengklik tombol di tabel orders
    - **Input:** Klik tombol View
    - **Proses:** Sistem redirect ke route detail order
    - **Output:** Request ke halaman detail order

2. **Sistem load order dengan relasi**

    - Sistem mengambil data order lengkap
    - **Input:** Order ID dari URL
    - **Proses:** Query order dengan relasi (Client, User, Invoices, Chat Messages)
    - **Output:** Order object dengan relasi

3. **Sistem cek akses Client**

    - Sistem memastikan Client hanya bisa melihat order miliknya
    - **Input:** Order ID dan User ID
    - **Proses:** Cek apakah `order.user_id === Auth::id()`
    - **Output:**
        - Jika akses valid: Lanjut ke step 4
        - Jika tidak valid: Error 403, redirect ke list orders

4. **Sistem tampilkan detail order**

    - Sistem menampilkan informasi lengkap order
    - **Input:** Order object dengan relasi
    - **Proses:** Render view detail order
    - **Output:** Halaman dengan informasi:
        - Informasi pesanan (nomor, jenis desain, deskripsi, kebutuhan)
        - Status order (badge warna)
        - Status produk (badge warna)
        - Budget dan deadline
        - Catatan admin (jika ada)
        - Section Hasil Desain (jika ada file)
        - Section Chat (jika ada messages)

5. **Sistem tampilkan hasil desain (jika ada)**
    - Sistem menampilkan file desain yang sudah diupload
    - **Input:** Order dengan desain_file
    - **Proses:** Cek apakah desain_file tidak null
    - **Output:**
        - Jika ada file: Tampilkan preview/download button
        - Jika belum ada: Tampilkan pesan "Belum ada hasil desain"

**Data Flow:**

```
Request Detail → Load Order + Relasi → Cek Akses → Render Detail →
Tampilkan Hasil Desain (jika ada) → Tampilkan ke Client
```

---

### 4. ALUR DOWNLOAD HASIL DESAIN (CLIENT)

**Tujuan:** Client mengunduh file hasil desain

**Langkah-langkah:**

1. **Client mengklik "Download Hasil Desain"**

    - Client mengklik tombol download di section Hasil Desain
    - **Input:** Klik tombol Download
    - **Proses:** Request ke route download
    - **Output:** Request GET ke `/storage/desain/{filename}`

2. **Sistem validasi akses**

    - Sistem memastikan Client memiliki akses ke order
    - **Input:** Order ID dan User ID
    - **Proses:** Cek apakah `order.user_id === Auth::id()`
    - **Output:**
        - Jika valid: Lanjut ke step 3
        - Jika tidak valid: Error 403

3. **Sistem cek file exists**

    - Sistem memastikan file ada di storage
    - **Input:** Filename dari database
    - **Proses:** Cek file di `public/storage/desain/{filename}`
    - **Output:**
        - Jika file ada: Lanjut ke step 4
        - Jika tidak ada: Error 404

4. **Sistem return file download**
    - Sistem mengirim file sebagai download response
    - **Input:** File path
    - **Proses:** Return file dengan Content-Disposition: attachment
    - **Output:** Browser download file ke komputer Client

**Data Flow:**

```
Request Download → Validasi Akses → Cek File Exists → Return File →
Download ke Komputer Client
```

---

### 5. ALUR EDIT PESANAN (CLIENT)

**Tujuan:** Client mengedit pesanan yang masih pending

**Langkah-langkah:**

1. **Client mengklik "Edit"**

    - Client mengklik tombol Edit di halaman detail order
    - **Input:** Klik tombol Edit
    - **Proses:** Sistem redirect ke route edit order
    - **Output:** Request ke halaman edit order

2. **Sistem load order**

    - Sistem mengambil data order untuk diedit
    - **Input:** Order ID
    - **Proses:** Query order dengan relasi client
    - **Output:** Order object

3. **Sistem cek akses dan status**

    - Sistem memastikan Client bisa edit order
    - **Input:** Order dan User ID
    - **Proses:**
        - Cek `order.user_id === Auth::id()`
        - Cek `order.status === 'pending'`
    - **Output:**
        - Jika valid: Lanjut ke step 4
        - Jika tidak valid: Error "Pesanan hanya dapat diedit jika status masih pending"

4. **Sistem tampilkan form edit**

    - Sistem menampilkan form dengan data order saat ini
    - **Input:** Order object
    - **Proses:** Render form edit dengan field yang bisa diedit Client
    - **Output:** Form dengan field:
        - Jenis desain
        - Deskripsi
        - Kebutuhan
        - Deadline
        - (Budget tidak bisa diedit oleh Client)

5. **Client edit data dan submit**

    - Client mengubah data dan klik "Simpan Perubahan"
    - **Input:** Data form yang diubah
    - **Proses:** Sistem validasi input
    - **Output:**
        - Jika validasi gagal: Tampilkan error, Client perbaiki
        - Jika validasi berhasil: Lanjut ke step 6

6. **Sistem update database**

    - Sistem menyimpan perubahan ke database
    - **Input:** Data order yang diubah
    - **Proses:** UPDATE ecrm_orders SET ...
    - **Output:** Order ter-update di database

7. **Sistem redirect dan tampilkan pesan**
    - Sistem mengarahkan ke detail order dan menampilkan pesan sukses
    - **Input:** Order ID
    - **Proses:** Redirect + set flash message
    - **Output:** Halaman detail order + flash message "Pesanan berhasil diperbarui"

**Data Flow:**

```
Request Edit → Load Order → Cek Akses & Status → Tampilkan Form →
Client Edit & Submit → Validasi → Update Database → Redirect → Tampilkan Pesan
```

**Percabangan:**

-   Jika status bukan 'pending': Error, tidak bisa edit
-   Jika validasi gagal: Tampilkan error, kembali ke form

---

### 6. ALUR CHAT DENGAN ADMIN/CS (CLIENT)

**Tujuan:** Client berkomunikasi dengan Admin/CS terkait pesanan

**Langkah-langkah:**

1. **Client mengklik "Chat" atau "Pesan"**

    - Client mengklik tombol Chat di halaman list/detail order
    - **Input:** Klik tombol Chat
    - **Proses:** Sistem redirect ke halaman chat
    - **Output:** Request ke route chat

2. **Sistem load order dan messages**

    - Sistem mengambil data order dan chat messages
    - **Input:** Order ID
    - **Proses:** Query order + chat messages (sort by created_at ASC)
    - **Output:** Order dan list messages

3. **Sistem cek akses order**

    - Sistem memastikan Client memiliki akses
    - **Input:** Order ID dan User ID
    - **Proses:** Cek `order.user_id === Auth::id()`
    - **Output:**
        - Jika valid: Lanjut ke step 4
        - Jika tidak valid: Error 403

4. **Sistem tampilkan halaman chat**

    - Sistem menampilkan interface chat
    - **Input:** Order dan messages
    - **Proses:** Render view chat
    - **Output:** Halaman dengan:
        - Header: Nomor order dan nama client
        - List messages (scrollable)
        - Form input pesan
        - Tombol "Kirim"

5. **Client ketik pesan dan kirim**

    - Client mengetik pesan dan klik "Kirim"
    - **Input:** Pesan dari Client
    - **Proses:** Sistem validasi (message tidak boleh kosong)
    - **Output:**
        - Jika kosong: Error "Pesan tidak boleh kosong"
        - Jika valid: Lanjut ke step 6

6. **Sistem simpan message ke database**

    - Sistem menyimpan pesan ke database
    - **Input:** order_id, user_id, message
    - **Proses:** INSERT INTO chat_messages (order_id, user_id, message, is_read=false)
    - **Output:** Message tersimpan

7. **Sistem update tampilan chat**
    - Sistem menampilkan pesan baru di chat
    - **Input:** Message baru
    - **Proses:** Update view dengan message baru
    - **Output:** Chat ter-update dengan pesan baru

**Data Flow:**

```
Request Chat → Load Order & Messages → Cek Akses → Tampilkan Chat →
Client Kirim Pesan → Validasi → Simpan Database → Update Tampilan
```

---

### 7. ALUR FILTER & SEARCH PESANAN (CLIENT)

**Tujuan:** Client mencari dan memfilter pesanan

**Langkah-langkah:**

1. **Client pilih filter status (optional)**

    - Client memilih status dari dropdown
    - **Input:** Pilihan status (Pending, Approved, In Progress, dll)
    - **Proses:** Sistem menyimpan pilihan di form
    - **Output:** Filter status terpilih

2. **Client pilih filter jenis desain (optional)**

    - Client memilih jenis desain dari dropdown
    - **Input:** Pilihan jenis desain
    - **Proses:** Sistem menyimpan pilihan di form
    - **Output:** Filter jenis desain terpilih

3. **Client ketik keyword search (optional)**

    - Client mengetik keyword di search box
    - **Input:** Keyword (nomor order, deskripsi, dll)
    - **Proses:** Sistem menyimpan keyword
    - **Output:** Keyword tersimpan

4. **Client klik "Cari"**

    - Client submit form filter/search
    - **Input:** Parameter filter dan search
    - **Proses:** Sistem terima GET request dengan parameter
    - **Output:** Request dengan parameter filter

5. **Sistem apply filter ke query**

    - Sistem menerapkan filter ke query database
    - **Input:** Parameter filter (status, jenis_desain, search)
    - **Proses:**
        - Filter by status (jika ada)
        - Filter by jenis desain (jika ada)
        - Search di nomor_order, deskripsi (jika ada)
    - **Output:** Query dengan filter

6. **Sistem load dan tampilkan hasil**

    - Sistem mengambil orders dengan filter
    - **Input:** Query dengan filter
    - **Proses:** Execute query, load orders
    - **Output:** Orders yang sesuai filter

7. **Sistem tampilkan hasil di tabel**
    - Sistem menampilkan orders di tabel
    - **Input:** Orders hasil filter
    - **Proses:** Render tabel dengan data
    - **Output:** Tabel dengan orders yang sudah difilter

**Data Flow:**

```
Client Pilih Filter → Submit Form → Terima Parameter → Apply Filter ke Query →
Load Orders → Tampilkan di Tabel
```

**Percabangan:**

-   Jika tidak ada hasil: Tampilkan "Tidak ada orders ditemukan" + tombol "Reset"
-   Jika Client klik "Reset": Clear semua filter, tampilkan semua orders

---

## ALUR PENGGUNAAN OLEH ADMIN

### 1. ALUR MELIHAT DAFTAR PESANAN (ADMIN)

**Tujuan:** Admin melihat semua pesanan di sistem

**Langkah-langkah:**

1. **Admin mengakses halaman pesanan**

    - Admin mengklik menu "Pesanan" atau mengakses `/ecrm/orders`
    - **Input:** Request GET ke route orders
    - **Proses:** Sistem cek authentication dan role
    - **Output:** Jika authorized, lanjut ke step 2

2. **Sistem load semua orders**

    - Sistem mengambil semua orders dari database
    - **Input:** Request dari Admin
    - **Proses:** Query semua orders (tanpa filter user_id)
    - **Output:** List semua orders

3. **Sistem load relasi**

    - Sistem mengambil data relasi (client, user)
    - **Input:** List orders
    - **Proses:** Load relasi client dan user untuk setiap order
    - **Output:** Orders dengan relasi

4. **Sistem apply filter (jika ada)**

    - Sistem menerapkan filter yang dipilih Admin
    - **Input:** Parameter filter (status, jenis desain, search keyword)
    - **Proses:**
        - Filter by status (jika dipilih)
        - Filter by jenis desain (jika dipilih)
        - Search di nomor order, deskripsi, nama client (jika ada keyword)
    - **Output:** Orders yang sudah difilter

5. **Sistem sort dan paginate**

    - Sistem mengurutkan dan membagi hasil
    - **Input:** List orders
    - **Proses:** Sort by created_at DESC, paginate 15 items per page
    - **Output:** Orders terurut dan ter-paginate

6. **Sistem tampilkan tabel orders**
    - Sistem menampilkan orders dalam format tabel
    - **Input:** Orders yang sudah difilter dan di-paginate
    - **Proses:** Render view dengan data orders
    - **Output:** Tabel dengan kolom:
        - Nomor order
        - Client name
        - Jenis desain (badge biru)
        - Status (badge warna)
        - Status produk (badge warna)
        - Budget
        - Deadline
        - Action buttons (View, Chat, Edit, Delete)

**Data Flow:**

```
Request → Load Semua Orders + Relasi → Apply Filter → Sort & Paginate →
Render Tabel → Tampilkan ke Admin
```

---

### 2. ALUR MELIHAT DETAIL PESANAN (ADMIN)

**Tujuan:** Admin melihat detail lengkap pesanan

**Langkah-langkah:**

1. **Admin mengklik "Lihat" atau "View"**

    - Admin mengklik tombol di tabel orders
    - **Input:** Klik tombol View
    - **Proses:** Sistem redirect ke route detail order
    - **Output:** Request ke halaman detail order

2. **Sistem load order dengan relasi**

    - Sistem mengambil data order lengkap
    - **Input:** Order ID dari URL
    - **Proses:** Query order dengan relasi (Client, User, Invoices, Chat Messages)
    - **Output:** Order object dengan relasi

3. **Sistem tampilkan detail order**
    - Sistem menampilkan informasi lengkap order
    - **Input:** Order object dengan relasi
    - **Proses:** Render view detail order
    - **Output:** Halaman dengan informasi:
        - Informasi pesanan (nomor, jenis desain, deskripsi, kebutuhan)
        - Status order (badge warna)
        - Status produk (badge warna)
        - Budget dan deadline
        - Catatan admin
        - Section Hasil Desain
        - Section Chat
        - Section Invoices
        - **Form Upload Desain** (khusus Admin)
        - **Form Update Status** (khusus Admin)

**Data Flow:**

```
Request Detail → Load Order + Relasi → Render Detail → Tampilkan ke Admin
```

---

### 3. ALUR UPDATE STATUS PESANAN (ADMIN)

**Tujuan:** Admin mengubah status pesanan

**Langkah-langkah:**

1. **Admin buka detail order**

    - Admin mengakses halaman detail order
    - **Input:** Order ID
    - **Proses:** Load order
    - **Output:** Halaman detail order dengan form update status

2. **Admin pilih status baru**

    - Admin memilih status dari dropdown
    - **Input:** Pilihan status (pending, approved, in_progress, review, completed, cancelled)
    - **Proses:** Sistem menyimpan pilihan
    - **Output:** Status terpilih

3. **Admin pilih produk status (optional)**

    - Admin memilih produk status dari dropdown
    - **Input:** Pilihan produk status (pending, proses, selesai)
    - **Proses:** Sistem menyimpan pilihan
    - **Output:** Produk status terpilih

4. **Admin masukkan catatan admin (optional)**

    - Admin mengetik catatan di textarea
    - **Input:** Catatan admin (text)
    - **Proses:** Sistem menyimpan catatan
    - **Output:** Catatan tersimpan

5. **Admin klik "Update Status"**

    - Admin submit form update status
    - **Input:** Status, produk status, catatan admin
    - **Proses:** Sistem validasi input
    - **Output:**
        - Jika validasi gagal: Tampilkan error, Admin perbaiki
        - Jika validasi berhasil: Lanjut ke step 6

6. **Sistem validasi role**

    - Sistem memastikan user adalah admin
    - **Input:** User role dari session
    - **Proses:** Cek role = 'admin'
    - **Output:**
        - Jika valid: Lanjut ke step 7
        - Jika tidak valid: Error 403

7. **Sistem update database**

    - Sistem menyimpan perubahan status ke database
    - **Input:** Status, produk status, catatan admin
    - **Proses:** UPDATE ecrm_orders SET status, produk_status, catatan_admin
    - **Output:** Order ter-update di database

8. **Sistem redirect dan tampilkan pesan**

    - Sistem mengarahkan ke detail order dan menampilkan pesan sukses
    - **Input:** Order ID
    - **Proses:** Redirect + set flash message
    - **Output:** Halaman detail order + flash message "Status pesanan berhasil diperbarui"

9. **Sistem update badge status**
    - Sistem memperbarui tampilan badge status di UI
    - **Input:** Status baru
    - **Proses:** Update badge dengan warna sesuai status
    - **Output:** Badge status ter-update

**Data Flow:**

```
Buka Detail → Pilih Status → Submit Form → Validasi Role → Update Database →
Redirect → Tampilkan Pesan → Update Badge
```

**Percabangan:**

-   Jika validasi gagal: Tampilkan error, kembali ke form
-   Jika role bukan admin: Error 403

---

### 4. ALUR UPLOAD HASIL DESAIN (ADMIN)

**Tujuan:** Admin mengupload file hasil desain

**Langkah-langkah:**

1. **Admin klik "Upload Desain"**

    - Admin mengklik tombol Upload Desain di halaman detail order
    - **Input:** Klik tombol Upload Desain
    - **Proses:** Sistem tampilkan form upload
    - **Output:** Form upload file ditampilkan

2. **Admin pilih file desain**

    - Admin memilih file dari komputer
    - **Input:** File (JPG, PNG, PDF, ZIP, RAR, max 10MB)
    - **Proses:** Sistem menyimpan file sementara
    - **Output:** File terpilih

3. **Admin klik "Upload"**

    - Admin submit form upload
    - **Input:** File yang dipilih
    - **Proses:** Sistem validasi file
    - **Output:**
        - Jika validasi gagal: Tampilkan error (format tidak didukung / ukuran terlalu besar)
        - Jika validasi berhasil: Lanjut ke step 4

4. **Sistem cek file lama**

    - Sistem mengecek apakah ada file desain lama
    - **Input:** Order dengan desain_file
    - **Proses:** Cek apakah desain_file tidak null
    - **Output:**
        - Jika ada file lama: Lanjut ke step 5
        - Jika tidak ada: Lanjut ke step 6

5. **Sistem hapus file lama**

    - Sistem menghapus file lama dari storage
    - **Input:** Filename lama
    - **Proses:** Delete file dari `public/storage/desain/{filename}`
    - **Output:** File lama terhapus

6. **Sistem generate nama file baru**

    - Sistem membuat nama file unik
    - **Input:** File yang diupload dan nomor order
    - **Proses:** Generate nama: `{timestamp}_{nomor_order}_{original_filename}`
    - **Output:** Nama file unik

7. **Sistem cek directory**

    - Sistem memastikan directory storage ada
    - **Input:** Path directory
    - **Proses:** Cek apakah `public/storage/desain/` ada
    - **Output:**
        - Jika tidak ada: Buat directory dengan permission 0755
        - Jika ada: Lanjut ke step 8

8. **Sistem move file ke storage**

    - Sistem memindahkan file ke storage
    - **Input:** File dan nama file baru
    - **Proses:** Move file ke `public/storage/desain/{filename}`
    - **Output:** File tersimpan di storage

9. **Sistem update database**

    - Sistem menyimpan nama file ke database
    - **Input:** Nama file baru
    - **Proses:** UPDATE ecrm_orders SET desain_file = filename
    - **Output:** Database ter-update

10. **Sistem redirect dan tampilkan pesan**
    - Sistem mengarahkan ke detail order dan menampilkan pesan sukses
    - **Input:** Order ID
    - **Proses:** Redirect + set flash message
    - **Output:** Halaman detail order + flash message "Desain berhasil diupload"

**Data Flow:**

```
Klik Upload → Pilih File → Submit → Validasi File → Cek File Lama →
Hapus File Lama (jika ada) → Generate Nama File → Cek Directory →
Move File ke Storage → Update Database → Redirect → Tampilkan Pesan
```

**Percabangan:**

-   Jika validasi file gagal: Tampilkan error, Admin pilih file lain
-   Jika gagal hapus file lama: Log error, lanjutkan proses (file lama tetap ada)
-   Jika gagal move file: Error "Gagal mengupload file", tidak update database

---

### 5. ALUR DOWNLOAD HASIL DESAIN (ADMIN)

**Tujuan:** Admin mengunduh file hasil desain

**Langkah-langkah:**

1. **Admin mengklik "Download Hasil Desain"**

    - Admin mengklik tombol download di section Hasil Desain
    - **Input:** Klik tombol Download
    - **Proses:** Request ke route download
    - **Output:** Request GET ke `/storage/desain/{filename}`

2. **Sistem cek file exists**

    - Sistem memastikan file ada di storage
    - **Input:** Filename dari database
    - **Proses:** Cek file di `public/storage/desain/{filename}`
    - **Output:**
        - Jika file ada: Lanjut ke step 3
        - Jika tidak ada: Error 404

3. **Sistem return file download**
    - Sistem mengirim file sebagai download response
    - **Input:** File path
    - **Proses:** Return file dengan Content-Disposition: attachment
    - **Output:** Browser download file ke komputer Admin

**Data Flow:**

```
Request Download → Cek File Exists → Return File → Download ke Komputer Admin
```

---

### 6. ALUR EDIT PESANAN (ADMIN)

**Tujuan:** Admin mengedit pesanan (bisa edit semua field)

**Langkah-langkah:**

1. **Admin mengklik "Edit"**

    - Admin mengklik tombol Edit di halaman detail order
    - **Input:** Klik tombol Edit
    - **Proses:** Sistem redirect ke route edit order
    - **Output:** Request ke halaman edit order

2. **Sistem load order**

    - Sistem mengambil data order untuk diedit
    - **Input:** Order ID
    - **Proses:** Query order dengan relasi client
    - **Output:** Order object

3. **Sistem tampilkan form edit**

    - Sistem menampilkan form dengan data order saat ini
    - **Input:** Order object
    - **Proses:** Render form edit dengan semua field
    - **Output:** Form dengan field:
        - Jenis desain
        - Deskripsi
        - Kebutuhan
        - Deadline
        - Budget
        - Status (Admin bisa edit)
        - Produk status (Admin bisa edit)

4. **Admin edit data dan submit**

    - Admin mengubah data dan klik "Simpan Perubahan"
    - **Input:** Data form yang diubah
    - **Proses:** Sistem validasi input
    - **Output:**
        - Jika validasi gagal: Tampilkan error, Admin perbaiki
        - Jika validasi berhasil: Lanjut ke step 5

5. **Sistem update database**

    - Sistem menyimpan perubahan ke database
    - **Input:** Data order yang diubah
    - **Proses:** UPDATE ecrm_orders SET ...
    - **Output:** Order ter-update di database

6. **Sistem redirect dan tampilkan pesan**
    - Sistem mengarahkan ke detail order dan menampilkan pesan sukses
    - **Input:** Order ID
    - **Proses:** Redirect + set flash message
    - **Output:** Halaman detail order + flash message "Pesanan berhasil diperbarui"

**Data Flow:**

```
Request Edit → Load Order → Tampilkan Form (Semua Field) → Admin Edit & Submit →
Validasi → Update Database → Redirect → Tampilkan Pesan
```

**Catatan:** Admin bisa edit order dengan status apapun, tidak seperti Client yang hanya bisa edit order dengan status 'pending'

---

### 7. ALUR HAPUS PESANAN (ADMIN)

**Tujuan:** Admin menghapus pesanan

**Langkah-langkah:**

1. **Admin mengklik "Hapus"**

    - Admin mengklik tombol Hapus di halaman detail order
    - **Input:** Klik tombol Hapus
    - **Proses:** Sistem tampilkan modal konfirmasi
    - **Output:** Modal konfirmasi ditampilkan

2. **Sistem tampilkan modal konfirmasi**

    - Sistem menampilkan dialog konfirmasi
    - **Input:** Order ID
    - **Proses:** Render modal dengan pesan konfirmasi
    - **Output:** Modal dengan:
        - Pesan: "Apakah Anda yakin ingin menghapus pesanan {nomor_order}?"
        - Tombol "Batal" dan "Ya, Hapus"

3. **Admin klik "Ya, Hapus"**

    - Admin mengkonfirmasi penghapusan
    - **Input:** Konfirmasi dari Admin
    - **Proses:** Sistem terima konfirmasi
    - **Output:** Request DELETE ke route destroy

4. **Sistem cek relasi**

    - Sistem mengecek apakah ada relasi yang menghalangi penghapusan
    - **Input:** Order ID
    - **Proses:**
        - Cek apakah ada invoices terkait
        - Cek apakah ada chat messages terkait
    - **Output:**
        - Jika ada invoices: Error "Tidak dapat menghapus pesanan yang sudah memiliki invoice"
        - Jika tidak ada: Lanjut ke step 5

5. **Sistem hapus file desain (jika ada)**

    - Sistem menghapus file desain dari storage
    - **Input:** Filename dari database
    - **Proses:** Delete file dari `public/storage/desain/{filename}`
    - **Output:**
        - Jika berhasil: File terhapus
        - Jika gagal: Log error (lanjutkan proses)

6. **Sistem hapus order dari database**

    - Sistem menghapus order dari database
    - **Input:** Order ID
    - **Proses:** DELETE FROM ecrm_orders WHERE id = order_id
    - **Output:** Order terhapus dari database

7. **Sistem redirect dan tampilkan pesan**
    - Sistem mengarahkan ke list orders dan menampilkan pesan sukses
    - **Input:** Request selesai
    - **Proses:** Redirect + set flash message
    - **Output:** Halaman list orders + flash message "Pesanan berhasil dihapus"

**Data Flow:**

```
Klik Hapus → Tampilkan Modal → Konfirmasi → Cek Relasi → Hapus File Desain →
Hapus dari Database → Redirect → Tampilkan Pesan
```

**Percabangan:**

-   Jika ada invoices terkait: Error, tidak bisa hapus
-   Jika gagal hapus file: Log error, lanjutkan proses (file tetap ada di storage)

---

### 8. ALUR CHAT DENGAN CLIENT/CS (ADMIN)

**Tujuan:** Admin berkomunikasi dengan Client atau CS terkait pesanan

**Langkah-langkah:**

1. **Admin mengklik "Chat" atau "Pesan"**

    - Admin mengklik tombol Chat di halaman list/detail order
    - **Input:** Klik tombol Chat
    - **Proses:** Sistem redirect ke halaman chat
    - **Output:** Request ke route chat

2. **Sistem load order dan messages**

    - Sistem mengambil data order dan chat messages
    - **Input:** Order ID
    - **Proses:** Query order + chat messages (sort by created_at ASC)
    - **Output:** Order dan list messages

3. **Sistem tampilkan halaman chat**

    - Sistem menampilkan interface chat
    - **Input:** Order dan messages
    - **Proses:** Render view chat
    - **Output:** Halaman dengan:
        - Header: Nomor order dan nama client
        - List messages (scrollable)
        - Form input pesan
        - Tombol "Kirim"
        - Tombol "Quick Reply" (khusus Admin)

4. **Admin ketik pesan atau pilih Quick Reply**

    - Admin mengetik pesan atau memilih template Quick Reply
    - **Input:** Pesan dari Admin atau template Quick Reply
    - **Proses:**
        - Jika Quick Reply: Load template dari database
        - Jika manual: Gunakan pesan yang diketik
    - **Output:** Pesan siap dikirim

5. **Admin klik "Kirim"**

    - Admin submit pesan
    - **Input:** Pesan
    - **Proses:** Sistem validasi (message tidak boleh kosong)
    - **Output:**
        - Jika kosong: Error "Pesan tidak boleh kosong"
        - Jika valid: Lanjut ke step 6

6. **Sistem simpan message ke database**

    - Sistem menyimpan pesan ke database
    - **Input:** order_id, user_id, message
    - **Proses:** INSERT INTO chat_messages (order_id, user_id, message, is_read=false)
    - **Output:** Message tersimpan

7. **Sistem update tampilan chat**
    - Sistem menampilkan pesan baru di chat
    - **Input:** Message baru
    - **Proses:** Update view dengan message baru
    - **Output:** Chat ter-update dengan pesan baru

**Data Flow:**

```
Request Chat → Load Order & Messages → Tampilkan Chat → Admin Kirim Pesan/Quick Reply →
Validasi → Simpan Database → Update Tampilan
```

**Percabangan:**

-   Jika Admin pilih Quick Reply: Load template, isi form, lanjutkan ke step 5
-   Jika pesan kosong: Error, Admin ketik lagi

---

### 9. ALUR FILTER & SEARCH PESANAN (ADMIN)

**Tujuan:** Admin mencari dan memfilter pesanan

**Langkah-langkah:**

1. **Admin pilih filter status (optional)**

    - Admin memilih status dari dropdown
    - **Input:** Pilihan status
    - **Proses:** Sistem menyimpan pilihan di form
    - **Output:** Filter status terpilih

2. **Admin pilih filter jenis desain (optional)**

    - Admin memilih jenis desain dari dropdown
    - **Input:** Pilihan jenis desain
    - **Proses:** Sistem menyimpan pilihan di form
    - **Output:** Filter jenis desain terpilih

3. **Admin ketik keyword search (optional)**

    - Admin mengetik keyword di search box
    - **Input:** Keyword (nomor order, deskripsi, nama client)
    - **Proses:** Sistem menyimpan keyword
    - **Output:** Keyword tersimpan

4. **Admin klik "Cari"**

    - Admin submit form filter/search
    - **Input:** Parameter filter dan search
    - **Proses:** Sistem terima GET request dengan parameter
    - **Output:** Request dengan parameter filter

5. **Sistem apply filter ke query**

    - Sistem menerapkan filter ke query database
    - **Input:** Parameter filter (status, jenis_desain, search)
    - **Proses:**
        - Filter by status (jika ada)
        - Filter by jenis desain (jika ada)
        - Search di nomor_order, deskripsi, client.nama (jika ada)
    - **Output:** Query dengan filter

6. **Sistem load dan tampilkan hasil**

    - Sistem mengambil orders dengan filter
    - **Input:** Query dengan filter
    - **Proses:** Execute query, load orders
    - **Output:** Orders yang sesuai filter

7. **Sistem tampilkan hasil di tabel**
    - Sistem menampilkan orders di tabel
    - **Input:** Orders hasil filter
    - **Proses:** Render tabel dengan data
    - **Output:** Tabel dengan orders yang sudah difilter

**Data Flow:**

```
Admin Pilih Filter → Submit Form → Terima Parameter → Apply Filter ke Query →
Load Orders → Tampilkan di Tabel
```

**Percabangan:**

-   Jika tidak ada hasil: Tampilkan "Tidak ada orders ditemukan" + tombol "Reset"
-   Jika Admin klik "Reset": Clear semua filter, tampilkan semua orders

---

## ALUR PENGGUNAAN OLEH CS (CUSTOMER SERVICE)

### 1. ALUR MELIHAT DAFTAR PESANAN (CS)

**Tujuan:** CS melihat semua pesanan dengan statistics

**Langkah-langkah:**

1. **CS mengakses halaman pesanan**

    - CS mengklik menu "Pesanan" atau mengakses `/ecrm/orders`
    - **Input:** Request GET ke route orders
    - **Proses:** Sistem cek authentication dan role
    - **Output:** Jika authorized, lanjut ke step 2

2. **Sistem load semua orders**

    - Sistem mengambil semua orders dari database
    - **Input:** Request dari CS
    - **Proses:** Query semua orders (tanpa filter user_id)
    - **Output:** List semua orders

3. **Sistem load relasi**

    - Sistem mengambil data relasi (client, user)
    - **Input:** List orders
    - **Proses:** Load relasi client dan user untuk setiap order
    - **Output:** Orders dengan relasi

4. **Sistem hitung statistics**

    - Sistem menghitung statistik pesanan
    - **Input:** List semua orders
    - **Proses:** Hitung:
        - Pending orders count
        - In progress orders count
        - Completed orders count
        - Total orders count
    - **Output:** Statistics data

5. **Sistem apply filter (jika ada)**

    - Sistem menerapkan filter yang dipilih CS
    - **Input:** Parameter filter (status, jenis desain, search keyword)
    - **Proses:**
        - Filter by status (jika dipilih)
        - Filter by jenis desain (jika dipilih)
        - Search di nomor order, deskripsi, nama client (jika ada keyword)
    - **Output:** Orders yang sudah difilter

6. **Sistem sort dan paginate**

    - Sistem mengurutkan dan membagi hasil
    - **Input:** List orders
    - **Proses:** Sort by created_at DESC, paginate 15 items per page
    - **Output:** Orders terurut dan ter-paginate

7. **Sistem tampilkan halaman dengan statistics**
    - Sistem menampilkan halaman khusus CS dengan statistics cards
    - **Input:** Orders dan statistics
    - **Proses:** Render view `ecrm.orders.cs-index`
    - **Output:** Halaman dengan:
        - **Statistics Cards:**
            - Card Pending Orders (count)
            - Card In Progress Orders (count)
            - Card Completed Orders (count)
            - Card Total Orders (count)
        - Tabel orders dengan kolom:
            - Nomor order
            - Client name
            - Jenis desain (badge biru)
            - Status (badge warna)
            - Status produk (badge warna)
            - Budget
            - Deadline
            - Action buttons (View, Chat, Edit)

**Data Flow:**

```
Request → Load Semua Orders + Relasi → Hitung Statistics → Apply Filter →
Sort & Paginate → Render Halaman CS dengan Statistics → Tampilkan ke CS
```

---

### 2. ALUR MELIHAT DETAIL PESANAN (CS)

**Tujuan:** CS melihat detail lengkap pesanan

**Langkah-langkah:**

1. **CS mengklik "Lihat" atau "View"**

    - CS mengklik tombol di tabel orders
    - **Input:** Klik tombol View
    - **Proses:** Sistem redirect ke route detail order
    - **Output:** Request ke halaman detail order

2. **Sistem load order dengan relasi**

    - Sistem mengambil data order lengkap
    - **Input:** Order ID dari URL
    - **Proses:** Query order dengan relasi (Client, User, Invoices, Chat Messages)
    - **Output:** Order object dengan relasi

3. **Sistem tampilkan detail order**
    - Sistem menampilkan informasi lengkap order
    - **Input:** Order object dengan relasi
    - **Proses:** Render view detail order
    - **Output:** Halaman dengan informasi:
        - Informasi pesanan (nomor, jenis desain, deskripsi, kebutuhan)
        - Status order (badge warna)
        - Status produk (badge warna)
        - Budget dan deadline
        - Catatan admin
        - Section Hasil Desain
        - Section Chat
        - Section Invoices
        - **Form Update Status** (khusus CS)

**Catatan:** CS tidak memiliki form Upload Desain (hanya Admin yang bisa upload)

**Data Flow:**

```
Request Detail → Load Order + Relasi → Render Detail → Tampilkan ke CS
```

---

### 3. ALUR UPDATE STATUS PESANAN (CS)

**Tujuan:** CS mengubah status pesanan

**Langkah-langkah:**

1. **CS buka detail order**

    - CS mengakses halaman detail order
    - **Input:** Order ID
    - **Proses:** Load order
    - **Output:** Halaman detail order dengan form update status

2. **CS pilih status baru**

    - CS memilih status dari dropdown
    - **Input:** Pilihan status (pending, approved, in_progress, review, completed, cancelled)
    - **Proses:** Sistem menyimpan pilihan
    - **Output:** Status terpilih

3. **CS pilih produk status (optional)**

    - CS memilih produk status dari dropdown
    - **Input:** Pilihan produk status (pending, proses, selesai)
    - **Proses:** Sistem menyimpan pilihan
    - **Output:** Produk status terpilih

4. **CS masukkan catatan admin (optional)**

    - CS mengetik catatan di textarea
    - **Input:** Catatan admin (text)
    - **Proses:** Sistem menyimpan catatan
    - **Output:** Catatan tersimpan

5. **CS klik "Update Status"**

    - CS submit form update status
    - **Input:** Status, produk status, catatan admin
    - **Proses:** Sistem validasi input
    - **Output:**
        - Jika validasi gagal: Tampilkan error, CS perbaiki
        - Jika validasi berhasil: Lanjut ke step 6

6. **Sistem validasi role**

    - Sistem memastikan user adalah CS atau admin
    - **Input:** User role dari session
    - **Proses:** Cek role = 'cs' atau 'admin'
    - **Output:**
        - Jika valid: Lanjut ke step 7
        - Jika tidak valid: Error 403

7. **Sistem update database**

    - Sistem menyimpan perubahan status ke database
    - **Input:** Status, produk status, catatan admin
    - **Proses:** UPDATE ecrm_orders SET status, produk_status, catatan_admin
    - **Output:** Order ter-update di database

8. **Sistem redirect dan tampilkan pesan**

    - Sistem mengarahkan ke detail order dan menampilkan pesan sukses
    - **Input:** Order ID
    - **Proses:** Redirect + set flash message
    - **Output:** Halaman detail order + flash message "Status pesanan berhasil diperbarui"

9. **Sistem update badge status**
    - Sistem memperbarui tampilan badge status di UI
    - **Input:** Status baru
    - **Proses:** Update badge dengan warna sesuai status
    - **Output:** Badge status ter-update

**Data Flow:**

```
Buka Detail → Pilih Status → Submit Form → Validasi Role → Update Database →
Redirect → Tampilkan Pesan → Update Badge
```

**Percabangan:**

-   Jika validasi gagal: Tampilkan error, kembali ke form
-   Jika role bukan cs atau admin: Error 403

---

### 4. ALUR CHAT DENGAN CLIENT/ADMIN (CS)

**Tujuan:** CS berkomunikasi dengan Client atau Admin terkait pesanan

**Langkah-langkah:**

1. **CS mengklik "Chat" atau "Pesan"**

    - CS mengklik tombol Chat di halaman list/detail order
    - **Input:** Klik tombol Chat
    - **Proses:** Sistem redirect ke halaman chat
    - **Output:** Request ke route chat

2. **Sistem load order dan messages**

    - Sistem mengambil data order dan chat messages
    - **Input:** Order ID
    - **Proses:** Query order + chat messages (sort by created_at ASC)
    - **Output:** Order dan list messages

3. **Sistem tampilkan halaman chat**

    - Sistem menampilkan interface chat
    - **Input:** Order dan messages
    - **Proses:** Render view chat
    - **Output:** Halaman dengan:
        - Header: Nomor order dan nama client
        - List messages (scrollable)
        - Form input pesan
        - Tombol "Kirim"
        - Tombol "Quick Reply" (khusus CS)

4. **CS ketik pesan atau pilih Quick Reply**

    - CS mengetik pesan atau memilih template Quick Reply
    - **Input:** Pesan dari CS atau template Quick Reply
    - **Proses:**
        - Jika Quick Reply: Load template dari database
        - Jika manual: Gunakan pesan yang diketik
    - **Output:** Pesan siap dikirim

5. **CS klik "Kirim"**

    - CS submit pesan
    - **Input:** Pesan
    - **Proses:** Sistem validasi (message tidak boleh kosong)
    - **Output:**
        - Jika kosong: Error "Pesan tidak boleh kosong"
        - Jika valid: Lanjut ke step 6

6. **Sistem simpan message ke database**

    - Sistem menyimpan pesan ke database
    - **Input:** order_id, user_id, message
    - **Proses:** INSERT INTO chat_messages (order_id, user_id, message, is_read=false)
    - **Output:** Message tersimpan

7. **Sistem update tampilan chat**
    - Sistem menampilkan pesan baru di chat
    - **Input:** Message baru
    - **Proses:** Update view dengan message baru
    - **Output:** Chat ter-update dengan pesan baru

**Data Flow:**

```
Request Chat → Load Order & Messages → Tampilkan Chat → CS Kirim Pesan/Quick Reply →
Validasi → Simpan Database → Update Tampilan
```

**Percabangan:**

-   Jika CS pilih Quick Reply: Load template, isi form, lanjutkan ke step 5
-   Jika pesan kosong: Error, CS ketik lagi

---

### 5. ALUR FILTER & SEARCH PESANAN (CS)

**Tujuan:** CS mencari dan memfilter pesanan

**Langkah-langkah:**

1. **CS pilih filter status (optional)**

    - CS memilih status dari dropdown
    - **Input:** Pilihan status
    - **Proses:** Sistem menyimpan pilihan di form
    - **Output:** Filter status terpilih

2. **CS pilih filter jenis desain (optional)**

    - CS memilih jenis desain dari dropdown
    - **Input:** Pilihan jenis desain
    - **Proses:** Sistem menyimpan pilihan di form
    - **Output:** Filter jenis desain terpilih

3. **CS ketik keyword search (optional)**

    - CS mengetik keyword di search box
    - **Input:** Keyword (nomor order, deskripsi, nama client)
    - **Proses:** Sistem menyimpan keyword
    - **Output:** Keyword tersimpan

4. **CS klik "Cari"**

    - CS submit form filter/search
    - **Input:** Parameter filter dan search
    - **Proses:** Sistem terima GET request dengan parameter
    - **Output:** Request dengan parameter filter

5. **Sistem apply filter ke query**

    - Sistem menerapkan filter ke query database
    - **Input:** Parameter filter (status, jenis_desain, search)
    - **Proses:**
        - Filter by status (jika ada)
        - Filter by jenis desain (jika ada)
        - Search di nomor_order, deskripsi, client.nama (jika ada)
    - **Output:** Query dengan filter

6. **Sistem load dan tampilkan hasil**

    - Sistem mengambil orders dengan filter
    - **Input:** Query dengan filter
    - **Proses:** Execute query, load orders
    - **Output:** Orders yang sesuai filter

7. **Sistem tampilkan hasil di tabel**
    - Sistem menampilkan orders di tabel
    - **Input:** Orders hasil filter
    - **Proses:** Render tabel dengan data
    - **Output:** Tabel dengan orders yang sudah difilter

**Data Flow:**

```
CS Pilih Filter → Submit Form → Terima Parameter → Apply Filter ke Query →
Load Orders → Tampilkan di Tabel
```

**Percabangan:**

-   Jika tidak ada hasil: Tampilkan "Tidak ada orders ditemukan" + tombol "Reset"
-   Jika CS klik "Reset": Clear semua filter, tampilkan semua orders

---

## CATATAN INTERAKSI LINTAS-AKTOR

### 1. ALUR STATUS PESANAN (Lintas-Aktor)

**Tujuan:** Menjelaskan bagaimana status pesanan berubah melalui interaksi berbagai aktor

**Alur Status:**

1. **Status Awal: Pending**

    - **Dibuat oleh:** Client (saat membuat pesanan baru)
    - **Dapat diubah oleh:** Admin atau CS
    - **Aksi yang bisa dilakukan:**
        - Client: Edit pesanan, Chat
        - Admin/CS: Approve, Cancel, Chat

2. **Status: Approved**

    - **Diubah oleh:** Admin atau CS
    - **Dari status:** Pending
    - **Aksi yang bisa dilakukan:**
        - Client: Chat, Download (jika ada file)
        - Admin/CS: Update ke In Progress, Cancel, Chat

3. **Status: In Progress**

    - **Diubah oleh:** Admin atau CS
    - **Dari status:** Approved
    - **Aksi yang bisa dilakukan:**
        - Client: Chat
        - Admin: Upload desain, Update ke Review, Cancel, Chat
        - CS: Update ke Review, Cancel, Chat

4. **Status: Review**

    - **Diubah oleh:** Admin atau CS
    - **Dari status:** In Progress
    - **Aksi yang bisa dilakukan:**
        - Client: Chat, Download desain
        - Admin/CS: Update ke Completed, Update kembali ke In Progress (revisi), Chat

5. **Status: Completed**

    - **Diubah oleh:** Admin atau CS (atau Client approve)
    - **Dari status:** Review
    - **Aksi yang bisa dilakukan:**
        - Client: Download desain, Chat
        - Admin/CS: Chat

6. **Status: Cancelled**
    - **Diubah oleh:** Admin atau CS
    - **Dari status:** Pending, Approved, atau In Progress
    - **Aksi yang bisa dilakukan:**
        - Client: Chat
        - Admin/CS: Chat

**Data Flow Status:**

```
Pending → Approved → In Progress → Review → Completed
  ↓         ↓            ↓
Cancelled Cancelled  Cancelled
```

---

### 2. ALUR UPLOAD & DOWNLOAD DESAIN (Lintas-Aktor)

**Tujuan:** Menjelaskan bagaimana file desain mengalir dari Admin ke Client

**Alur:**

1. **Admin Upload Desain**

    - Admin mengupload file desain (Use Case 5 - Admin)
    - **Input:** File dari Admin
    - **Proses:** Simpan ke storage, update database
    - **Output:** File tersimpan, database ter-update

2. **Sistem Notifikasi (jika ada)**

    - Sistem dapat mengirim notifikasi ke Client (opsional)
    - **Input:** Order dengan desain_file baru
    - **Proses:** Generate notifikasi
    - **Output:** Notifikasi ke Client

3. **Client Melihat Hasil Desain**

    - Client mengakses detail order
    - **Input:** Order ID
    - **Proses:** Load order dengan desain_file
    - **Output:** Section Hasil Desain menampilkan file

4. **Client Download Desain**
    - Client mengunduh file desain (Use Case 6 - Client)
    - **Input:** Request download dari Client
    - **Proses:** Validasi akses, return file
    - **Output:** File ter-download ke komputer Client

**Data Flow:**

```
Admin Upload → Simpan ke Storage → Update Database → Client Lihat Detail →
Client Download → File ke Komputer Client
```

---

### 3. ALUR CHAT (Lintas-Aktor)

**Tujuan:** Menjelaskan komunikasi antar aktor melalui chat

**Aktor yang bisa chat:**

-   Client ↔ Admin
-   Client ↔ CS
-   Admin ↔ CS

**Alur Chat:**

1. **Aktor A mengirim pesan**

    - Aktor A (Client/Admin/CS) mengirim pesan
    - **Input:** Pesan dari Aktor A
    - **Proses:** Simpan ke database dengan is_read = false
    - **Output:** Message tersimpan

2. **Sistem update unread count**

    - Sistem memperbarui jumlah pesan belum dibaca untuk Aktor B
    - **Input:** Message baru dengan is_read = false
    - **Proses:** Hitung unread messages untuk Aktor B
    - **Output:** Unread count ter-update

3. **Aktor B membuka chat**

    - Aktor B mengakses halaman chat
    - **Input:** Request dari Aktor B
    - **Proses:** Load messages, mark as read
    - **Output:** Messages ditampilkan, is_read = true

4. **Aktor B membalas**
    - Aktor B mengirim balasan
    - **Input:** Pesan dari Aktor B
    - **Proses:** Simpan ke database dengan is_read = false
    - **Output:** Message tersimpan, unread count untuk Aktor A ter-update

**Data Flow:**

```
Aktor A Kirim Pesan → Simpan (is_read=false) → Update Unread Count Aktor B →
Aktor B Buka Chat → Mark as Read → Aktor B Balas → Simpan (is_read=false) →
Update Unread Count Aktor A
```

---

### 4. ALUR EDIT PESANAN (Lintas-Aktor)

**Tujuan:** Menjelaskan perbedaan edit pesanan antara Client dan Admin

**Perbedaan:**

1. **Client Edit Pesanan**

    - **Kondisi:** Hanya bisa edit jika status = 'pending'
    - **Field yang bisa diedit:** Jenis desain, Deskripsi, Kebutuhan, Deadline
    - **Field yang tidak bisa diedit:** Budget, Status, Produk status
    - **Alur:** Use Case 7 - Client

2. **Admin Edit Pesanan**
    - **Kondisi:** Bisa edit dengan status apapun
    - **Field yang bisa diedit:** Semua field (Jenis desain, Deskripsi, Kebutuhan, Deadline, Budget, Status, Produk status)
    - **Alur:** Use Case 7 - Admin

**Data Flow:**

```
Client Edit (status=pending) → Validasi → Update Field Terbatas → Database
Admin Edit (status=apapun) → Validasi → Update Semua Field → Database
```

---

## RINGKASAN ALUR PENGGUNAAN

### Client

1. Membuat Pesanan Baru
2. Melihat Daftar Pesanan
3. Melihat Detail Pesanan
4. Download Hasil Desain
5. Edit Pesanan (hanya jika status pending)
6. Chat dengan Admin/CS
7. Filter & Search Pesanan

### Admin

1. Melihat Daftar Pesanan
2. Melihat Detail Pesanan
3. Update Status Pesanan
4. Upload Hasil Desain
5. Download Hasil Desain
6. Edit Pesanan (semua status)
7. Hapus Pesanan
8. Chat dengan Client/CS
9. Filter & Search Pesanan

### CS (Customer Service)

1. Melihat Daftar Pesanan (dengan statistics)
2. Melihat Detail Pesanan
3. Update Status Pesanan
4. Chat dengan Client/Admin
5. Filter & Search Pesanan

---

## CATATAN UNTUK DIAGRAM VISIO

### Format yang Disarankan:

1. **Flowchart untuk setiap alur utama:**

    - Gunakan shape berbeda untuk:
        - Oval: Start/End
        - Rectangle: Process/Aksi
        - Diamond: Decision/Validasi
        - Parallelogram: Input/Output
        - Cylinder: Database

2. **Swimlane untuk alur lintas-aktor:**

    - Buat swimlane untuk setiap aktor (Client, Admin, CS)
    - Tunjukkan interaksi antar swimlane

3. **Sequence Diagram untuk interaksi detail:**

    - Tunjukkan urutan langkah antar komponen
    - Tunjukkan data flow dengan panah

4. **State Diagram untuk status pesanan:**

    - Tunjukkan transisi status
    - Tunjukkan aktor yang bisa mengubah status

5. **Data Flow Diagram:**
    - Tunjukkan alur data dari input sampai output
    - Tunjukkan proses transformasi data

---

**End of Document**
