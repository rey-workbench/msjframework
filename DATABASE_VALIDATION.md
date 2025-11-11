# Database Schema Validation

Commands telah diupdate untuk menggunakan validasi yang sesuai dengan database schema.

## Field Length Validation

### sys_gmenu Table
- `gmenu`: **char(6)** - Maksimal 6 karakter
- `name`: **string(25)** - Maksimal 25 karakter

### sys_dmenu Table  
- `dmenu`: **char(6)** - Maksimal 6 karakter
- `gmenu`: **char(6)** - Maksimal 6 karakter (foreign key)
- `name`: **string(25)** - Maksimal 25 karakter
- `icon`: **string(50)** - Maksimal 50 karakter
- `url`: **string(50)** - Maksimal 50 karakter
- `tabel`: **string(50)** - Maksimal 50 karakter
- `layout`: **char(6)** - Maksimal 6 karakter
- `sub`: **char(6)** - Maksimal 6 karakter

### sys_roles Table
- `idroles`: **char(6)** - Maksimal 6 karakter
- `name`: **string(20)** - Maksimal 20 karakter
- `description`: **string(100)** - Maksimal 100 karakter

### sys_auth Table
- `idroles`: **char(6)** - Foreign key to sys_roles
- `dmenu`: **char(6)** - Foreign key to sys_dmenu
- `gmenu`: **char(6)** - Foreign key to sys_gmenu
- Permission fields: `add`, `edit`, `delete`, `approval`, `value`, `print`, `excel`, `pdf`, `rules` (enum 0,1)

## Updated Validation Methods

### HasValidation Trait
```php
// Gmenu validation with max length
protected function validateGmenuCode(string $value, int $maxLength = 6): ?string

// Dmenu validation with max length  
protected function validateDmenuCode(string $value, int $maxLength = 6): ?string

// Role validation with max length
protected function validateRoleId(string $value, int $maxLength = 6): ?string
protected function validateRoleName(string $value, int $maxLength = 20): ?string
protected function validateRoleDescription(string $value, int $maxLength = 100): ?string

// Name validation with min/max length
protected function validateName(string $value, int $minLength = 2, int $maxLength = 100): ?string

// Existence checks
protected function gmenuExists(string $gmenu): bool
protected function dmenuExists(string $dmenu): bool
protected function roleExists(string $roleId): bool
```

## Command Updates

### MakeMSJGmenu
```php
// Validates gmenu code max 6 chars
validate: fn($value) => $this->validateGmenuCode($value, 6)

// Validates name max 25 chars
validate: fn($value) => $this->validateName($value, 2, 25)
```

### MakeMSJDmenu
```php
// Validates dmenu code max 6 chars
validate: fn($value) => $this->validateDmenuCode($value, 6)

// Validates name max 25 chars
validate: fn($value) => $this->validateName($value, 2, 25)
```

## Benefits

1. **Prevents Database Errors**: Validasi sebelum insert mencegah error "Data too long for column"
2. **Consistent Validation**: Semua commands menggunakan validasi yang sama
3. **User-Friendly**: Error messages yang jelas tentang batas karakter
4. **Duplicate Prevention**: Check existence sebelum insert

## Example Validation Messages

```
❌ Kode gmenu maksimal 6 karakter
❌ Nama maksimal 25 karakter  
❌ Kode gmenu 'KOP001' sudah ada
❌ Kode dmenu 'TEST01' sudah ada
```

## Testing

```bash
# Test max length validation
php artisan msj:make:gmenu TOOLONG123 "This name is way too long for database"

# Test duplicate validation  
php artisan msj:make:gmenu KOP001 "Test" # If already exists

# Valid input
php artisan msj:make:gmenu KOP002 "Master Data"
php artisan msj:make:dmenu TEST01 "Test Menu" --gmenu=KOP002
```
