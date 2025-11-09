# Package Publishing Guide

Panduan untuk mempublish package ini ke Packagist.

## 📋 Prerequisites

1. Akun Packagist (daftar di https://packagist.org)
2. Repository Git (GitHub, GitLab, atau Bitbucket)
3. Composer terinstall

## 🚀 Steps to Publish

### 1. Buat Repository Git

```bash
cd packages/msj-framework
git init
git add .
git commit -m "Initial commit: MSJ Framework Laravel Generator"
```

### 2. Push ke GitHub/GitLab

```bash
# Buat repository di GitHub/GitLab terlebih dahulu
git remote add origin https://github.com/yourusername/msj-laravel-generator.git
git branch -M main
git push -u origin main
```

### 3. Update composer.json

Pastikan informasi di `composer.json` sudah benar:

```json
{
    "name": "your-vendor/msj-laravel-generator",
    "description": "MSJ Framework - Laravel CRUD Generator",
    "homepage": "https://github.com/yourusername/msj-laravel-generator",
    "support": {
        "issues": "https://github.com/yourusername/msj-laravel-generator/issues",
        "source": "https://github.com/yourusername/msj-laravel-generator"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/yourusername/msj-laravel-generator"
        }
    ]
}
```

### 4. Buat Tag Release

```bash
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

### 5. Submit ke Packagist

1. Login ke https://packagist.org
2. Klik "Submit" di menu
3. Masukkan URL repository Git Anda
4. Klik "Check" untuk validasi
5. Klik "Submit" untuk submit package

### 6. Setup GitHub Webhook (Optional)

Untuk auto-update di Packagist ketika ada commit baru:

1. Di Packagist, buka package Anda
2. Klik "Settings"
3. Copy webhook URL
4. Di GitHub repository, buka Settings > Webhooks
5. Add webhook dengan URL dari Packagist
6. Content type: `application/json`
7. Events: `Just the push event`

## 📦 Install Package

Setelah dipublish, install package:

```bash
composer require your-vendor/msj-laravel-generator
```

## 🔄 Update Package

Untuk update package:

1. Buat perubahan di code
2. Commit perubahan
3. Buat tag baru:
   ```bash
   git tag -a v1.0.1 -m "Release version 1.0.1"
   git push origin v1.0.1
   ```
4. Packagist akan otomatis update jika webhook sudah disetup

## 📝 Versioning

Gunakan Semantic Versioning:
- MAJOR version (1.0.0) - Breaking changes
- MINOR version (0.1.0) - New features (backward compatible)
- PATCH version (0.0.1) - Bug fixes (backward compatible)

## 🏷️ Tagging

```bash
# Major release
git tag -a v1.0.0 -m "Release version 1.0.0"

# Minor release
git tag -a v1.1.0 -m "Release version 1.1.0"

# Patch release
git tag -a v1.0.1 -m "Release version 1.0.1"

# Push tags
git push origin --tags
```

## 📄 Important Files

Pastikan file-file berikut ada:
- `composer.json` - Package configuration
- `README.md` - Documentation
- `LICENSE` - License file
- `.gitignore` - Git ignore rules
- `CHANGELOG.md` - Changelog

## ✅ Checklist

- [ ] Repository Git dibuat
- [ ] Code sudah di-push ke repository
- [ ] `composer.json` sudah benar
- [ ] `README.md` sudah lengkap
- [ ] `LICENSE` file ada
- [ ] Tag release dibuat
- [ ] Package sudah di-submit ke Packagist
- [ ] Webhook sudah disetup (optional)
- [ ] Package bisa di-install via Composer

## 🎉 Done!

Package Anda sekarang sudah tersedia di Packagist dan bisa di-install oleh siapa saja!

