# MSJ Framework Laravel Generator

[![Latest Version](https://img.shields.io/packagist/v/reysilvaa12/msjframework.svg?style=flat-square)](https://packagist.org/packages/reysilvaa12/msjframework)
[![Total Downloads](https://img.shields.io/packagist/dt/reysilvaa12/msjframework.svg?style=flat-square)](https://packagist.org/packages/reysilvaa12/msjframework)
[![License](https://img.shields.io/packagist/l/reysilvaa12/msjframework.svg?style=flat-square)](https://packagist.org/packages/reysilvaa12/msjframework)

MSJ Framework Laravel Generator adalah package Laravel yang memudahkan pembuatan CRUD (Create, Read, Update, Delete) dengan wizard interaktif menggunakan Laravel Prompts. Package ini secara otomatis menghasilkan Model, Controller, Views (Blade), JavaScript, dan konfigurasi database untuk modul MSJ Framework.

## ✨ Fitur

- 🎯 **Wizard Interaktif** - Menggunakan Laravel Prompts untuk pengalaman yang lebih baik
- 🚀 **CRUD Generator** - Generate lengkap Model, Controller, Views, dan JavaScript
- 📋 **Multi Layout Support** - Mendukung layout manual, standard, transaksi, system, dan report
- 🎨 **Auto Detection** - Deteksi otomatis struktur tabel database
- 🔒 **Authorization** - Generate konfigurasi authorization otomatis
- 📝 **Fillable Auto** - Generate fillable property secara otomatis dari struktur tabel
- 🎨 **Beautiful Console** - Console output yang menarik dengan ASCII art dan badges
- 🔍 **Search & Select** - Pencarian tabel dan field dengan autocomplete

## 📋 Requirements

- PHP >= 8.2
- Laravel ^11.0|^12.0
- Laravel Prompts ^0.1.0|^0.3.0

## 🚀 Installation

Install package via Composer:

```bash
composer require reysilvaa12/msjframework
```

Publish configuration (optional):

```bash
php artisan vendor:publish --tag=msj-generator-config
```

## 📖 Usage

### Interactive Menu

Jalankan command tanpa argument untuk membuka menu interaktif:

```bash
php artisan msj:make
```

### Generate Module (Wizard)

Generate module lengkap dengan wizard interaktif:

```bash
php artisan msj:make menu
# atau
php artisan msj:make module
```

### Generate CRUD (Quick)

Generate CRUD secara cepat:

```bash
php artisan msj:make crud mst_example
```

Dengan opsi:

```bash
php artisan msj:make crud mst_example --gmenu=KOP001 --dmenu=KOP999 --layout=manual
```

### Generate Controller

```bash
php artisan msj:make controller ExampleController
```

### Generate Model

```bash
php artisan msj:make model mst_example
```

### Generate Views

```bash
php artisan msj:make views
```

## 📚 Commands

### `msj:make`

Hub command untuk semua generator. Menampilkan menu interaktif jika dijalankan tanpa argument.

### `msj:make:menu`

Wizard interaktif untuk generate module lengkap dengan 4 langkah:

1. Pilih Layout Type
2. Informasi Dasar (gmenu, dmenu, menu name, URL, table)
3. Pengaturan Field
4. Ringkasan

### `msj:make:crud {table}`

Generate CRUD secara cepat dari tabel database.

**Options:**

- `--gmenu=` - Kode group menu (default: KOP001)
- `--dmenu=` - Kode detail menu (default: KOP999)
- `--layout=` - Tipe layout: manual|standr|transc|system|report (default: manual)

### `msj:make:controller {name}`

Generate controller MSJ dengan method CRUD.

**Options:**

- `--table=` - Nama tabel database
- `--gmenu=` - Kode group menu
- `--url=` - URL slug

### `msj:make:model {table}`

Generate model dari struktur tabel database.

**Options:**

- `--force` - Menimpa model yang sudah ada

### `msj:make:views`

Generate blade views (list, add, edit, show) dan JavaScript.

**Options:**

- `--gmenu=` - Kode group menu
- `--url=` - URL slug
- `--table=` - Nama tabel database
- `--dmenu=` - Kode detail menu

## 🎨 Layout Types

- **manual** - Controller & views custom (kontrol penuh)
- **standr** - CRUD standard (form sederhana, 1 primary key)
- **transc** - Transaksi (struktur header-detail)
- **system** - Konfigurasi sistem (master-detail)
- **report** - Menu laporan (halaman filter & hasil)

## 📁 Generated Files

### Model

- `app/Models/{ModelName}.php`
- Auto-detected primary key
- Auto-generated fillable properties
- Timestamps configuration

### Controller

- `app/Http/Controllers/{ControllerName}.php`
- CRUD methods: index, add, store, edit, update, destroy, show
- Authorization checks
- Validation using ValidationHelper
- Transaction support

### Views

- `resources/views/{gmenu}/{url}/list.blade.php`
- `resources/views/{gmenu}/{url}/add.blade.php`
- `resources/views/{gmenu}/{url}/edit.blade.php`
- `resources/views/{gmenu}/{url}/show.blade.php`

### JavaScript

- `resources/views/js/{dmenu}.blade.php`

### Database Configuration

- Menu registration in `sys_dmenu`
- Table configuration in `sys_table`
- Authorization in `sys_auth`

## ⚙️ Configuration

File konfigurasi: `config/msj-generator.php`

```php
return [
    'default_layout' => 'manual',
    'default_gmenu' => 'KOP001',
    'default_dmenu' => 'KOP999',
    // ...
];
```

## 🔧 Database Requirements

Package ini memerlukan tabel berikut di database:

- `sys_gmenu` - Group menu
- `sys_dmenu` - Detail menu
- `sys_table` - Table configuration
- `sys_auth` - Authorization configuration
- `sys_roles` - Roles
- `sys_log` - System logs (optional)

## 📝 Examples

### Example 1: Generate CRUD untuk tabel `mst_anggota`

```bash
php artisan msj:make crud mst_anggota --gmenu=KOP001 --dmenu=KOP001 --layout=manual
```

### Example 2: Generate menggunakan wizard

```bash
php artisan msj:make menu
```

Kemudian ikuti wizard:

1. Pilih layout: `manual`
2. Pilih gmenu: `KOP001`
3. Masukkan dmenu: `KOP001`
4. Masukkan menu name: `Data Anggota`
5. Masukkan URL: `data-anggota`
6. Pilih tabel: `mst_anggota`
7. Konfirmasi generate

## 🛠️ Development

### Run Tests

```bash
composer test
```

### Code Style

```bash
composer pint
```

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📞 Support

For support, email support@msjframework.com or open an issue on GitHub.

## 🙏 Credits

- Laravel Framework
- Laravel Prompts
- MSJ Framework Team
