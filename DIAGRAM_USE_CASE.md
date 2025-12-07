# 📊 DIAGRAM USE CASE - e-CRM JASA DESAIN

**Tanggal:** 7 Desember 2025  
**Versi:** 1.0  
**Sistem:** e-CRM Jasa Desain

---

## DAFTAR DIAGRAM

1. [Use Case Diagram - Overview](#1-use-case-diagram---overview)
2. [Activity Diagram - Membuat Pesanan](#2-activity-diagram---membuat-pesanan)
3. [Activity Diagram - Upload Desain](#3-activity-diagram---upload-desain)
4. [Sequence Diagram - Membuat Pesanan](#4-sequence-diagram---membuat-pesanan)
5. [Sequence Diagram - Upload & Download Desain](#5-sequence-diagram---upload--download-desain)
6. [Data Flow Diagram - Sistem Pesanan](#6-data-flow-diagram---sistem-pesanan)
7. [State Diagram - Status Pesanan](#7-state-diagram---status-pesanan)

---

## 1. USE CASE DIAGRAM - OVERVIEW

Diagram ini menggambarkan semua use case dan aktor yang terlibat dalam sistem.

```mermaid
graph TB
    subgraph Actors["👥 AKTOR"]
        Admin[Admin]
        CS[Customer Service]
        Client[Client]
        System[System]
    end

    subgraph UseCases["📋 USE CASE"]
        UC1[Membuat Pesanan Baru]
        UC2[Melihat Daftar Pesanan]
        UC3[Melihat Detail Pesanan]
        UC4[Update Status Pesanan]
        UC5[Upload Hasil Desain]
        UC6[Download Hasil Desain]
        UC7[Edit Pesanan]
        UC8[Hapus Pesanan]
        UC9[Chat dengan Client/Admin]
        UC10[Filter & Search Pesanan]
    end

    %% Admin connections
    Admin --> UC2
    Admin --> UC3
    Admin --> UC4
    Admin --> UC5
    Admin --> UC6
    Admin --> UC7
    Admin --> UC8
    Admin --> UC9
    Admin --> UC10

    %% CS connections
    CS --> UC2
    CS --> UC3
    CS --> UC4
    CS --> UC9
    CS --> UC10

    %% Client connections
    Client --> UC1
    Client --> UC2
    Client --> UC3
    Client --> UC6
    Client --> UC7
    Client --> UC9
    Client --> UC10

    %% System connections
    System -.-> UC1
    System -.-> UC5

    style Admin fill:#e1f5ff
    style CS fill:#fff4e1
    style Client fill:#e1ffe1
    style System fill:#f5f5f5
```

---

## 2. ACTIVITY DIAGRAM - MEMBUAT PESANAN

Diagram ini menggambarkan alur aktivitas saat client membuat pesanan baru.

```mermaid
flowchart TD
    Start([Client klik 'Pesan Project']) --> Login{User sudah login?}
    Login -->|Tidak| LoginPage[Halaman Login]
    LoginPage --> Login
    Login -->|Ya| CheckRole{Role = 'client'?}
    CheckRole -->|Tidak| Error403[Error 403: Hanya client]
    CheckRole -->|Ya| ShowForm[System tampilkan form create order]
    ShowForm --> FillForm[User mengisi form:<br/>- Jenis desain<br/>- Deskripsi<br/>- Kebutuhan<br/>- Deadline<br/>- Budget]
    FillForm --> Submit[User klik 'Buat Pesanan']
    Submit --> Validate{Validasi input}
    Validate -->|Gagal| ShowError[Tampilkan error message]
    ShowError --> FillForm
    Validate -->|Berhasil| CheckClient{User punya Client record?}
    CheckClient -->|Tidak| CreateClient[Buat Client record baru<br/>Link user dengan client]
    CheckClient -->|Ya| GenerateOrder[Generate nomor order<br/>ORD-YYYYMM-XXXX]
    CreateClient --> GenerateOrder
    GenerateOrder --> SetDefault[Set default status:<br/>- status: 'pending'<br/>- produk_status: 'pending']
    SetDefault --> SaveDB[(Simpan ke database)]
    SaveDB --> Redirect[Redirect ke detail order]
    Redirect --> ShowSuccess[Tampilkan flash message:<br/>'Pesanan berhasil dibuat']
    ShowSuccess --> End([Selesai])
    Error403 --> End

    style Start fill:#e1ffe1
    style End fill:#ffe1e1
    style SaveDB fill:#fff4e1
    style Error403 fill:#ffcccc
```

---

## 3. ACTIVITY DIAGRAM - UPLOAD DESAIN

Diagram ini menggambarkan alur aktivitas saat admin mengupload hasil desain.

```mermaid
flowchart TD
    Start([Admin klik 'Upload Desain']) --> CheckRole{Role = 'admin'?}
    CheckRole -->|Tidak| Error403[Error 403: Hanya admin]
    CheckRole -->|Ya| ShowForm[System tampilkan form upload]
    ShowForm --> SelectFile[Admin pilih file:<br/>JPG, PNG, PDF, ZIP, RAR<br/>Max 10MB]
    SelectFile --> ClickUpload[Admin klik 'Upload']
    ClickUpload --> ValidateFile{Validasi file}
    ValidateFile -->|Gagal| ShowError[Tampilkan error:<br/>- Format tidak didukung<br/>- Ukuran terlalu besar]
    ShowError --> SelectFile
    ValidateFile -->|Berhasil| CheckOldFile{Ada file lama?}
    CheckOldFile -->|Ya| DeleteOld[Hapus file lama dari storage]
    CheckOldFile -->|Tidak| GenerateName[Generate nama file:<br/>timestamp_nomor_order_filename]
    DeleteOld --> GenerateName
    GenerateName --> CheckDir{Directory ada?}
    CheckDir -->|Tidak| CreateDir[Buat directory<br/>public/storage/desain/]
    CheckDir -->|Ya| MoveFile[Move file ke storage]
    CreateDir --> MoveFile
    MoveFile --> UpdateDB[(Update database:<br/>desain_file = filename)]
    UpdateDB --> Redirect[Redirect ke detail order]
    Redirect --> ShowSuccess[Tampilkan flash message:<br/>'Desain berhasil diupload']
    ShowSuccess --> End([Selesai])
    Error403 --> End

    style Start fill:#e1f5ff
    style End fill:#ffe1e1
    style UpdateDB fill:#fff4e1
    style MoveFile fill:#e1ffe1
    style Error403 fill:#ffcccc
```

---

## 4. SEQUENCE DIAGRAM - MEMBUAT PESANAN

Diagram ini menggambarkan interaksi antar komponen saat membuat pesanan.

```mermaid
sequenceDiagram
    participant Client as 👤 Client
    participant View as 📄 View (Form)
    participant Controller as 🎮 OrderController
    participant Model as 💾 Order Model
    participant ClientModel as 💾 Client Model
    participant DB as 🗄️ Database

    Client->>View: Klik 'Pesan Project'
    View->>Client: Tampilkan form create order
    Client->>View: Isi form & submit
    View->>Controller: POST /ecrm/orders/store
    
    Controller->>Controller: Validasi role = 'client'
    Controller->>ClientModel: Cek user memiliki Client?
    
    alt Client belum ada
        ClientModel->>DB: Query client by email
        DB-->>ClientModel: Tidak ditemukan
        ClientModel->>DB: INSERT INTO ecrm_clients
        DB-->>ClientModel: Client created
        ClientModel->>DB: UPDATE users SET client_id
        DB-->>ClientModel: User updated
    end
    
    ClientModel-->>Controller: Client record ready
    Controller->>Controller: Validasi input form
    Controller->>Model: Order::create(data)
    Model->>Model: Boot: Generate nomor order
    Model->>DB: INSERT INTO ecrm_orders
    DB-->>Model: Order created
    Model-->>Controller: Order object
    Controller->>View: Redirect to show order
    View->>Client: Tampilkan detail order + flash message
```

---

## 5. SEQUENCE DIAGRAM - UPLOAD & DOWNLOAD DESAIN

Diagram ini menggambarkan interaksi saat upload dan download desain.

```mermaid
sequenceDiagram
    participant Admin as 👨‍💼 Admin
    participant View as 📄 View
    participant Controller as 🎮 OrderController
    participant Model as 💾 Order Model
    participant Storage as 📁 File Storage
    participant DB as 🗄️ Database
    participant Client as 👤 Client

    Note over Admin,Storage: UPLOAD DESAIN
    Admin->>View: Klik 'Upload Desain'
    View->>Admin: Tampilkan form upload
    Admin->>View: Pilih file & submit
    View->>Controller: POST /ecrm/orders/{id}/upload-desain
    
    Controller->>Controller: Validasi role = 'admin'
    Controller->>Controller: Validasi file (type, size)
    Controller->>Model: Load order
    Model->>DB: SELECT * FROM ecrm_orders
    DB-->>Model: Order data
    Model-->>Controller: Order object
    
    alt File lama ada
        Controller->>Storage: Hapus file lama
        Storage-->>Controller: File deleted
    end
    
    Controller->>Controller: Generate filename
    Controller->>Storage: Move file ke public/storage/desain/
    Storage-->>Controller: File saved
    Controller->>Model: Update desain_file
    Model->>DB: UPDATE ecrm_orders SET desain_file
    DB-->>Model: Updated
    Model-->>Controller: Success
    Controller->>View: Redirect + flash message
    View->>Admin: Tampilkan hasil desain

    Note over Client,Storage: DOWNLOAD DESAIN
    Client->>View: Klik 'Download Hasil Desain'
    View->>Controller: GET /storage/desain/{filename}
    Controller->>Controller: Validasi akses order
    Controller->>Model: Load order
    Model->>DB: SELECT * FROM ecrm_orders
    DB-->>Model: Order data
    Model-->>Controller: Order object
    Controller->>Storage: Cek file exists
    Storage-->>Controller: File found
    Controller->>View: Return file download response
    View->>Client: Download file ke komputer
```

---

## 6. DATA FLOW DIAGRAM - SISTEM PESANAN

Diagram ini menggambarkan alur data dalam sistem pesanan.

```mermaid
flowchart LR
    subgraph External["🌐 EXTERNAL"]
        Client[👤 Client]
        Admin[👨‍💼 Admin]
        CS[👨‍💷 CS]
    end

    subgraph Process["⚙️ PROCESS"]
        P1[1.0<br/>Create Order]
        P2[2.0<br/>List Orders]
        P3[3.0<br/>Update Status]
        P4[4.0<br/>Upload Desain]
        P5[5.0<br/>Download Desain]
    end

    subgraph Storage["💾 DATA STORE"]
        D1[(D1: Orders)]
        D2[(D2: Clients)]
        D3[(D3: Users)]
        D4[(D4: Files)]
    end

    Client -->|Order Data| P1
    P1 -->|Save| D1
    P1 -->|Create/Link| D2
    D2 -->|Client Info| P1
    
    Client -->|Request| P2
    Admin -->|Request| P2
    CS -->|Request| P2
    P2 -->|Read| D1
    D1 -->|Orders| P2
    P2 -->|Display| Client
    P2 -->|Display| Admin
    P2 -->|Display| CS
    
    Admin -->|Status Update| P3
    CS -->|Status Update| P3
    P3 -->|Update| D1
    D1 -->|Updated Order| P3
    
    Admin -->|File Upload| P4
    P4 -->|Save| D4
    P4 -->|Update| D1
    D1 -->|File Reference| P4
    
    Client -->|Request| P5
    Admin -->|Request| P5
    P5 -->|Read| D1
    D1 -->|File Reference| P5
    P5 -->|Read| D4
    D4 -->|File Data| P5
    P5 -->|Download| Client
    P5 -->|Download| Admin

    style D1 fill:#fff4e1
    style D2 fill:#e1ffe1
    style D3 fill:#e1f5ff
    style D4 fill:#ffe1e1
```

---

## 7. STATE DIAGRAM - STATUS PESANAN

Diagram ini menggambarkan transisi status pesanan.

```mermaid
stateDiagram-v2
    [*] --> Pending: Client membuat pesanan
    
    Pending --> Approved: Admin/CS approve
    Pending --> Cancelled: Admin cancel
    Pending --> Pending: Client edit
    
    Approved --> InProgress: Admin/CS mulai kerja
    Approved --> Cancelled: Admin cancel
    
    InProgress --> Review: Admin/CS selesai kerja
    InProgress --> Cancelled: Admin cancel
    
    Review --> Completed: Client approve / Admin finalize
    Review --> InProgress: Perlu revisi
    
    Completed --> [*]
    Cancelled --> [*]
    
    note right of Pending
        Status awal
        Client bisa edit
    end note
    
    note right of InProgress
        Sedang dikerjakan
        Bisa upload desain
    end note
    
    note right of Completed
        Final state
        File desain tersedia
    end note
```

---

## 8. ENTITY RELATIONSHIP DIAGRAM (ERD) - PESANAN

Diagram ini menggambarkan relasi antar entitas dalam sistem pesanan.

```mermaid
erDiagram
    USERS ||--o{ ORDERS : creates
    CLIENTS ||--o{ ORDERS : has
    ORDERS ||--o{ CHAT_MESSAGES : has
    ORDERS ||--o{ INVOICES : generates
    ORDERS ||--o| DESAIN_FILES : has
    
    USERS {
        int id PK
        string name
        string email
        string role
        int client_id FK
    }
    
    CLIENTS {
        int id PK
        string nama
        string email
        string tipe
        string status
    }
    
    ORDERS {
        int id PK
        int client_id FK
        int user_id FK
        string nomor_order
        string jenis_desain
        text deskripsi
        text kebutuhan
        string status
        string produk_status
        decimal budget
        date deadline
        text catatan_admin
        string desain_file
        datetime created_at
        datetime updated_at
    }
    
    CHAT_MESSAGES {
        int id PK
        int order_id FK
        int user_id FK
        text message
        boolean is_read
        datetime created_at
    }
    
    INVOICES {
        int id PK
        int order_id FK
        int client_id FK
        string nomor_invoice
        decimal total
        string status
        date tanggal_jatuh_tempo
    }
    
    DESAIN_FILES {
        string filename PK
        int order_id FK
        string file_path
        int file_size
        string mime_type
    }
```

---

## 9. COMPONENT DIAGRAM - ARSITEKTUR SISTEM

Diagram ini menggambarkan komponen-komponen dalam sistem.

```mermaid
graph TB
    subgraph Frontend["🖥️ FRONTEND"]
        Views[Blade Views]
        Assets[CSS/JS Assets]
    end

    subgraph Middleware["🛡️ MIDDLEWARE"]
        Auth[Authentication]
        Role[Role Middleware]
    end

    subgraph Controllers["🎮 CONTROLLERS"]
        OrderCtrl[OrderController]
        ChatCtrl[ChatController]
        InvoiceCtrl[InvoiceController]
    end

    subgraph Models["💾 MODELS"]
        OrderModel[Order Model]
        ClientModel[Client Model]
        ChatModel[ChatMessage Model]
    end

    subgraph Database["🗄️ DATABASE"]
        MySQL[(MySQL Database)]
    end

    subgraph Storage["📁 STORAGE"]
        FileStorage[File Storage<br/>public/storage/desain/]
    end

    Views --> Auth
    Auth --> Role
    Role --> OrderCtrl
    Role --> ChatCtrl
    Role --> InvoiceCtrl
    
    OrderCtrl --> OrderModel
    OrderCtrl --> ClientModel
    ChatCtrl --> ChatModel
    
    OrderModel --> MySQL
    ClientModel --> MySQL
    ChatModel --> MySQL
    
    OrderCtrl --> FileStorage

    style Frontend fill:#e1f5ff
    style Middleware fill:#fff4e1
    style Controllers fill:#e1ffe1
    style Models fill:#ffe1e1
    style Database fill:#f5f5f5
    style Storage fill:#f0f0f0
```

---

## 10. ACTIVITY DIAGRAM - UPDATE STATUS PESANAN

Diagram ini menggambarkan alur update status pesanan oleh Admin/CS.

```mermaid
flowchart TD
    Start([Admin/CS buka detail order]) --> CheckRole{Role = admin/cs?}
    CheckRole -->|Tidak| Error403[Error 403]
    CheckRole -->|Ya| ShowForm[Tampilkan form update status]
    ShowForm --> FillForm[Pilih status baru:<br/>pending, approved, in_progress,<br/>review, completed, cancelled]
    FillForm --> FillProduk[Pilih produk status:<br/>pending, proses, selesai]
    FillProduk --> FillNote[Masukkan catatan admin]
    FillNote --> Submit[Klik 'Update Status']
    Submit --> Validate{Validasi input}
    Validate -->|Gagal| ShowError[Tampilkan error]
    ShowError --> FillForm
    Validate -->|Berhasil| UpdateDB[(Update database:<br/>- status<br/>- produk_status<br/>- catatan_admin)]
    UpdateDB --> Redirect[Redirect ke detail order]
    Redirect --> ShowSuccess[Tampilkan flash message:<br/>'Status berhasil diperbarui']
    ShowSuccess --> UpdateBadge[Update badge status di UI]
    UpdateBadge --> End([Selesai])
    Error403 --> End

    style Start fill:#e1f5ff
    style End fill:#ffe1e1
    style UpdateDB fill:#fff4e1
    style Error403 fill:#ffcccc
```

---

## CATATAN PENGGUNAAN DIAGRAM

### Cara Menampilkan Diagram

1. **GitHub/GitLab**: Diagram Mermaid akan otomatis dirender di markdown
2. **VS Code**: Install extension "Markdown Preview Mermaid Support"
3. **Online**: Copy kode Mermaid ke https://mermaid.live
4. **Documentation Tools**: 
   - MkDocs dengan plugin mermaid
   - Docusaurus dengan plugin mermaid
   - GitBook

### Format Diagram

Semua diagram menggunakan **Mermaid syntax** yang merupakan standar untuk diagram dalam markdown. Diagram dapat dirender di berbagai platform yang mendukung Mermaid.

### Update Diagram

Jika ada perubahan pada use case atau alur data, update diagram yang relevan di file ini untuk menjaga konsistensi dokumentasi.

---

**End of Document**

