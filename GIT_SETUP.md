# 🔗 Setup Git Repository - e-CRM Jasa Desain

## Repository GitHub

**URL:** https://github.com/zidannadiz/e-crm.git

---

## Langkah-langkah Setup

### 1. Inisialisasi Git Repository

```powershell
cd c:\laragon\www\ecrm-jasa-desain\ecrm-jasa-desain-temp
git init
```

### 2. Konfigurasi User Git

```powershell
git config user.name "zidannadiz"
git config user.email "zidannadiz@users.noreply.github.com"
```

**Catatan:** Jika Anda ingin menggunakan email yang berbeda, ganti dengan email GitHub Anda:

```powershell
git config user.email "your-email@example.com"
```

### 3. Tambahkan Remote Repository

```powershell
git remote add origin https://github.com/zidannadiz/e-crm.git
```

### 4. Verifikasi Konfigurasi

```powershell
# Cek user name
git config user.name

# Cek user email
git config user.email

# Cek remote URL
git remote -v
```

### 5. Tambahkan File ke Staging

```powershell
git add .
```

### 6. Buat Initial Commit

```powershell
git commit -m "Initial commit: e-CRM Jasa Desain project"
```

### 7. Set Branch ke Main

```powershell
git branch -M main
```

### 8. Push ke GitHub

```powershell
git push -u origin main
```

**Catatan:** Jika ini pertama kali push, GitHub mungkin akan meminta autentikasi. Gunakan:

-   **Personal Access Token** (recommended), atau
-   **GitHub CLI** untuk login

---

## Alternatif: Menggunakan Script Otomatis

Jalankan script PowerShell yang sudah disediakan:

```powershell
cd c:\laragon\www\ecrm-jasa-desain\ecrm-jasa-desain-temp
.\setup-git.ps1
```

Kemudian lanjutkan dengan langkah 5-8 di atas.

---

## Setup Personal Access Token (Jika Diperlukan)

Jika GitHub meminta autentikasi saat push:

1. Buka GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Generate new token (classic)
3. Beri nama token (contoh: "e-crm-project")
4. Pilih scope: **repo** (full control of private repositories)
5. Generate token dan **copy token** (hanya muncul sekali!)
6. Saat push, gunakan token sebagai password:
    - Username: `zidannadiz`
    - Password: `[paste-token-di-sini]`

---

## Perintah Git yang Sering Digunakan

### Melihat Status

```powershell
git status
```

### Melihat Log Commit

```powershell
git log --oneline
```

### Menambahkan File Spesifik

```powershell
git add nama-file.md
```

### Commit dengan Pesan

```powershell
git commit -m "Deskripsi perubahan"
```

### Push ke GitHub

```powershell
git push
```

### Pull dari GitHub

```powershell
git pull
```

### Melihat Branch

```powershell
git branch
```

### Membuat Branch Baru

```powershell
git checkout -b nama-branch
```

---

## Troubleshooting

### Error: "remote origin already exists"

```powershell
git remote remove origin
git remote add origin https://github.com/zidannadiz/e-crm.git
```

### Error: "Authentication failed"

-   Pastikan Personal Access Token sudah dibuat
-   Gunakan token sebagai password, bukan password GitHub

### Error: "Permission denied"

-   Pastikan repository di GitHub sudah dibuat
-   Pastikan Anda memiliki akses ke repository

### Reset Konfigurasi

```powershell
# Hapus remote
git remote remove origin

# Hapus konfigurasi user (local)
git config --unset user.name
git config --unset user.email

# Setup ulang
git config user.name "zidannadiz"
git config user.email "your-email@example.com"
git remote add origin https://github.com/zidannadiz/e-crm.git
```

---

## Status Repository

✅ **Remote URL:** https://github.com/zidannadiz/e-crm.git  
✅ **Username:** zidannadiz  
✅ **Email:** zidannadiz@users.noreply.github.com (atau email Anda)

---

**Selamat! Repository Git sudah dikonfigurasi dan siap digunakan.**
