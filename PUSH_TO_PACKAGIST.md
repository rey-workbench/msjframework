# 🚀 Panduan Push ke Packagist

Panduan lengkap untuk mempublish package `reysilvaa12/msjframework` ke Packagist tanpa error.

## ✅ Checklist Sebelum Push

- [x] `composer.json` sudah valid
- [x] Package name: `reysilvaa12/msjframework`
- [x] Repository URL sudah diset di composer.json
- [x] README.md sudah lengkap
- [x] LICENSE file ada
- [ ] Git repository sudah diinisialisasi
- [ ] Code sudah di-push ke GitHub
- [ ] Tag release sudah dibuat
- [ ] Package sudah di-submit ke Packagist

## 📋 Langkah-langkah

### 1. Pastikan Repository GitHub Sudah Dibuat

1. Login ke GitHub (https://github.com)
2. Buat repository baru dengan nama: `msjframework`
3. **JANGAN** initialize dengan README, .gitignore, atau license (karena sudah ada)
4. Copy URL repository (contoh: `https://github.com/rey-workbench/msjframework.git`)

### 2. Inisialisasi Git Repository

```bash
# Masuk ke direktori package
cd /home/rey/projects/personal/composer-package/msjframework

# Inisialisasi git repository
git init

# Tambahkan semua file
git add .

# Buat initial commit
git commit -m "Initial commit: MSJ Framework Laravel Generator v1.0.0"

# Tambahkan remote repository
git remote add origin https://github.com/rey-workbench/msjframework.git

# Rename branch ke main (jika perlu)
git branch -M main

# Push ke GitHub
git push -u origin main
```

### 3. Buat Tag Release (Penting!)

Packagist memerlukan tag untuk release version. Buat tag untuk version pertama:

```bash
# Buat tag v1.0.0
git tag -a v1.0.0 -m "Release version 1.0.0"

# Push tag ke GitHub
git push origin v1.0.0

# Atau push semua tags
git push origin --tags
```

### 4. Submit ke Packagist

1. **Login ke Packagist**

   - Buka https://packagist.org
   - Login dengan akun GitHub (recommended) atau buat akun baru

2. **Submit Package**

   - Klik menu "Submit" di navbar
   - Masukkan URL repository: `https://github.com/rey-workbench/msjframework`
   - Klik "Check" untuk validasi
   - Jika semua OK, klik "Submit"

3. **Tunggu Processing**
   - Packagist akan memproses package Anda
   - Biasanya butuh beberapa detik sampai beberapa menit
   - Refresh halaman untuk melihat status

### 5. Setup GitHub Webhook (Auto-Update) ⚡

Untuk auto-update di Packagist ketika ada commit atau tag baru, ada 2 cara:

#### Cara 1: Login via GitHub (Recommended - Paling Mudah) ✅

Ini adalah cara termudah dan otomatis:

1. **Pastikan Login via GitHub di Packagist:**

   - Jika Anda sudah punya akun Packagist tapi belum connect dengan GitHub:
     - Logout dari Packagist
     - Login lagi menggunakan "Login with GitHub"
     - Pastikan Anda grant permission yang diperlukan
   - Jika sudah login via GitHub, pastikan akun GitHub Anda terhubung

2. **Pastikan Packagist App punya akses ke Organization:**

   - Jika repository Anda di organization `rey-workbench`, pastikan Packagist application punya akses
   - Buka: https://github.com/settings/installations
   - Cari "Packagist" application
   - Klik "Configure" dan pastikan organization `rey-workbench` sudah dicentang
   - Save changes

3. **Trigger Manual Sync (jika perlu):**

   - Di Packagist, buka package Anda
   - Klik "Settings" di sidebar
   - Klik "Trigger manual account sync" untuk setup hook otomatis
   - Packagist akan mencoba setup hook untuk semua repository Anda

4. **Verifikasi:**
   - Di halaman package di Packagist, pastikan tidak ada warning tentang "not being automatically synced"
   - Jika ada warning, coba trigger manual sync lagi

**Note:** Archived repositories tidak bisa di-setup karena readonly di GitHub API.

#### Cara 2: Manual Setup GitHub Webhook 🔧

Jika tidak ingin login via GitHub atau perlu setup manual:

1. **Dapatkan API Token dari Packagist:**

   - Login ke Packagist: https://packagist.org
   - Buka Profile page: https://packagist.org/profile/
   - Copy "API Token" Anda

2. **Setup Webhook di GitHub:**

   - Buka repository: https://github.com/rey-workbench/msjframework
   - Pergi ke **Settings > Webhooks**
   - Klik **"Add webhook"**
   - Isi form dengan:
     - **Payload URL**: `https://packagist.org/api/github?username=reysilvaa12`
     - **Content type**: `application/json`
     - **Secret**: (masukkan API Token dari Packagist)
     - **Which events?**: Pilih **"Just the push event"** (cukup push event)
   - Klik **"Add webhook"**

3. **Verifikasi:**
   - Setelah webhook dibuat, GitHub akan mengirim test delivery
   - Pastikan status webhook adalah "Active" (hijau)
   - Jika ada error, check log di webhook untuk detail error

### 5.1. Manual Update via API (Alternatif) 🔄

Jika webhook tidak berfungsi atau Anda ingin update manual:

1. **Dapatkan API Token:**

   - Login ke Packagist: https://packagist.org
   - Buka Profile: https://packagist.org/profile/
   - Copy "API Token" Anda

2. **Trigger Update via API:**

   ```bash
   # Ganti YOUR_API_TOKEN dengan API token dari Packagist
   curl -XPOST -H'content-type:application/json' \
     'https://packagist.org/api/update-package?username=reysilvaa12&apiToken=YOUR_API_TOKEN' \
     -d'{"repository":{"url":"https://github.com/rey-workbench/msjframework"}}'
   ```

   Atau dengan format yang lebih sederhana:

   ```bash
   curl -X POST \
     'https://packagist.org/api/update-package?username=reysilvaa12&apiToken=YOUR_API_TOKEN' \
     -d '{"repository":{"url":"https://github.com/rey-workbench/msjframework"}}'
   ```

3. **Verifikasi:**
   - Setelah API call berhasil, refresh halaman package di Packagist
   - Package akan otomatis di-update dengan versi terbaru

**Note:** Manual update via API berguna jika:

- Webhook tidak berfungsi
- Anda tidak ingin setup webhook
- Anda perlu update manual tanpa push ke GitHub

### 6. Verifikasi Package

Setelah di-submit, verifikasi package bisa di-install:

```bash
# Test install package
composer require reysilvaa12/msjframework

# Atau test di project Laravel baru
composer create-project laravel/laravel test-project
cd test-project
composer require reysilvaa12/msjframework
```

## 🔄 Update Package (Release Baru)

Setiap kali ada update, ikuti langkah berikut:

1. **Update Code**

   ```bash
   # Buat perubahan di code
   # Commit perubahan
   git add .
   git commit -m "Fix: Description of changes"
   git push origin main
   ```

2. **Buat Tag Baru**

   ```bash
   # Untuk patch (bug fix): v1.0.1
   git tag -a v1.0.1 -m "Release version 1.0.1"

   # Untuk minor (new feature): v1.1.0
   git tag -a v1.1.0 -m "Release version 1.1.0"

   # Untuk major (breaking change): v2.0.0
   git tag -a v2.0.0 -m "Release version 2.0.0"

   # Push tag
   git push origin v1.0.1
   # atau push semua tags
   git push origin --tags
   ```

3. **Update di Packagist**
   - Jika webhook sudah disetup, Packagist akan otomatis update
   - Jika tidak, klik "Update" di halaman package di Packagist
   - Atau trigger manual via API (lihat bagian 5.1 di bawah)

## 📝 Versioning (Semantic Versioning)

Gunakan Semantic Versioning: `MAJOR.MINOR.PATCH`

- **MAJOR** (1.0.0): Breaking changes - perubahan yang tidak backward compatible
- **MINOR** (0.1.0): New features - fitur baru yang backward compatible
- **PATCH** (0.0.1): Bug fixes - perbaikan bug yang backward compatible

Contoh:

- `v1.0.0` - Initial release
- `v1.0.1` - Bug fix
- `v1.1.0` - New feature
- `v2.0.0` - Breaking change

## ⚠️ Common Errors & Solutions

### Error: "Package not found"

- **Solution**: Pastikan repository URL benar di composer.json
- **Solution**: Pastikan repository sudah di-push ke GitHub
- **Solution**: Pastikan tag sudah dibuat dan di-push

### Error: "No valid composer.json found"

- **Solution**: Pastikan composer.json ada di root repository
- **Solution**: Validasi dengan `composer validate`

### Error: "Repository not found"

- **Solution**: Pastikan repository GitHub sudah dibuat
- **Solution**: Pastikan repository adalah public (atau setup access token untuk private)

### Error: "No tags found"

- **Solution**: Buat tag release: `git tag -a v1.0.0 -m "Release v1.0.0"`
- **Solution**: Push tag: `git push origin v1.0.0`

### Error: "Package name already taken"

- **Solution**: Package name `reysilvaa12/msjframework` sudah digunakan
- **Solution**: Ganti package name di composer.json atau hubungi owner package yang ada

### Error: "Not automatically synced" atau Webhook tidak berfungsi

- **Solution**: Pastikan Anda login via GitHub di Packagist
- **Solution**: Pastikan Packagist application punya akses ke organization `rey-workbench`
- **Solution**: Coba trigger manual account sync di Packagist
- **Solution**: Setup webhook manual (lihat Cara 2 di bagian 5)
- **Solution**: Gunakan manual update via API (lihat bagian 5.1)

## ✅ Checklist Final

Setelah semua langkah selesai, pastikan:

- [ ] Git repository sudah diinisialisasi
- [ ] Code sudah di-push ke GitHub
- [ ] Tag v1.0.0 sudah dibuat dan di-push
- [ ] Package sudah di-submit ke Packagist
- [ ] Package bisa di-install via `composer require reysilvaa12/msjframework`
- [ ] Webhook sudah disetup (optional tapi recommended)

## 🎉 Selesai!

Package Anda sekarang sudah tersedia di Packagist dan bisa di-install oleh siapa saja dengan:

```bash
composer require reysilvaa12/msjframework
```

## 📞 Support

Jika ada masalah, buka issue di:

- GitHub: https://github.com/rey-workbench/msjframework/issues
- Packagist: https://packagist.org/packages/reysilvaa12/msjframework
