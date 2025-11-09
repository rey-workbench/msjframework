# ⚡ Quick Start: Push ke Packagist

Panduan cepat untuk push package `reysilvaa12/msjframework` ke Packagist.

## 🎯 Langkah Cepat (5 Menit)

### 1. Setup Git & Push ke GitHub

```bash
# Inisialisasi git
git init
git add .
git commit -m "Initial commit: MSJ Framework v1.0.0"

# Tambahkan remote (ganti dengan URL repository Anda)
git remote add origin https://github.com/rey-workbench/msjframework.git
git branch -M main
git push -u origin main

# Buat tag release (PENTING!)
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

**Atau gunakan script helper:**
```bash
./setup-git.sh
```

### 2. Submit ke Packagist

1. Buka https://packagist.org
2. Login dengan GitHub (recommended)
3. Klik "Submit" di navbar
4. Masukkan URL: `https://github.com/rey-workbench/msjframework`
5. Klik "Check" → "Submit"

### 3. Setup Auto-Update (Recommended)

**Cara 1: Login via GitHub (Paling Mudah) ✅**

1. Pastikan login via GitHub di Packagist
2. Pastikan Packagist app punya akses ke organization `rey-workbench`:
   - Buka: https://github.com/settings/installations
   - Cari "Packagist" → Configure
   - Centang organization `rey-workbench` → Save
3. Di Packagist, buka package → Settings → "Trigger manual account sync"

**Cara 2: Manual Webhook**

1. Dapatkan API Token dari Packagist: https://packagist.org/profile/
2. Di GitHub repository → Settings → Webhooks → Add webhook:
   - **Payload URL**: `https://packagist.org/api/github?username=reysilvaa12`
   - **Content type**: `application/json`
   - **Secret**: (API Token dari Packagist)
   - **Events**: Just the push event
3. Save

### 4. Verifikasi

```bash
# Test install
composer require reysilvaa12/msjframework
```

## ✅ Checklist

- [ ] Git repository diinisialisasi
- [ ] Code di-push ke GitHub
- [ ] Tag v1.0.0 dibuat dan di-push
- [ ] Package di-submit ke Packagist
- [ ] Webhook/auto-sync disetup
- [ ] Package bisa di-install via Composer

## 🔄 Update Package (Release Baru)

```bash
# Update code
git add .
git commit -m "Fix: Description"
git push origin main

# Buat tag baru
git tag -a v1.0.1 -m "Release v1.0.1"
git push origin v1.0.1

# Packagist akan otomatis update jika webhook sudah disetup
# Atau update manual: klik "Update" di halaman package di Packagist
```

## 📚 Dokumentasi Lengkap

Lihat `PUSH_TO_PACKAGIST.md` untuk panduan lengkap dengan troubleshooting.

## 🆘 Troubleshooting

**Error: "Not automatically synced"**
- Pastikan login via GitHub di Packagist
- Pastikan Packagist app punya akses ke organization
- Trigger manual account sync di Packagist

**Webhook tidak berfungsi**
- Setup manual webhook (lihat Cara 2)
- Atau gunakan manual update via API (lihat PUSH_TO_PACKAGIST.md bagian 5.1)

**Package tidak update otomatis**
- Check webhook status di GitHub (harus Active/hijau)
- Trigger manual update di Packagist
- Atau gunakan API untuk update manual

## 📞 Support

- GitHub: https://github.com/rey-workbench/msjframework/issues
- Packagist: https://packagist.org/packages/reysilvaa12/msjframework

