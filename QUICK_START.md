# Quick Start Guide

Panduan cepat untuk menggunakan MSJ Framework Laravel Generator.

## 📦 Installation

```bash
composer require msj-framework/laravel-generator
```

## 🚀 Quick Start

### 1. Generate CRUD dengan Wizard Interaktif

```bash
php artisan msj:make
```

Pilih "Generate Menu Baru" dari menu, lalu ikuti wizard.

### 2. Generate CRUD secara Cepat

```bash
php artisan msj:make crud mst_example
```

Ini akan generate:
- Model: `App\Models\MstExample`
- Controller: `App\Http\Controllers\MstExampleController`
- Views: `resources/views/{gmenu}/mstexample/*.blade.php`
- JavaScript: `resources/views/js/{dmenu}.blade.php`
- Database configuration: `sys_dmenu`, `sys_table`, `sys_auth`

### 3. Generate Controller Saja

```bash
php artisan msj:make controller ExampleController --table=mst_example
```

### 4. Generate Model Saja

```bash
php artisan msj:make model mst_example
```

### 5. Generate Views Saja

```bash
php artisan msj:make views --gmenu=KOP001 --url=example --table=mst_example
```

## 📋 Database Requirements

Pastikan tabel berikut ada di database:

- `sys_gmenu` - Group menu
- `sys_dmenu` - Detail menu  
- `sys_table` - Table configuration
- `sys_auth` - Authorization
- `sys_roles` - Roles

## 🎯 Example Workflow

### Step 1: Buat Tabel Database

```sql
CREATE TABLE mst_example (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    isactive CHAR(1) DEFAULT '1',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Step 2: Generate CRUD

```bash
php artisan msj:make crud mst_example --gmenu=KOP001 --dmenu=KOP001
```

### Step 3: Akses Menu

Akses menu di browser: `http://your-app.com/mstexample`

## ⚙️ Configuration

Publish config file (optional):

```bash
php artisan vendor:publish --tag=msj-generator-config
```

Edit `config/msj-generator.php` untuk mengubah default values.

## 🎨 Layout Types

- **manual** - Full control, custom controller & views
- **standr** - Standard CRUD, simple form
- **transc** - Transaction (header-detail structure)
- **system** - System configuration (master-detail)
- **report** - Report menu (filter & results page)

## 📚 More Information

Lihat [README.md](README.md) untuk dokumentasi lengkap.

## 🆘 Troubleshooting

### Error: Table not found
Pastikan tabel sudah dibuat di database sebelum generate.

### Error: Command not found
Pastikan package sudah terinstall dan service provider sudah terdaftar:

```bash
composer dump-autoload
php artisan package:discover
```

### Error: Permission denied
Pastikan folder `app/Models`, `app/Http/Controllers`, dan `resources/views` memiliki permission yang benar.

## 💡 Tips

1. Gunakan `msj:make crud` untuk prototyping cepat
2. Gunakan `msj:make menu` untuk kontrol lebih detail
3. Selalu backup database sebelum generate
4. Review generated code sebelum digunakan di production

