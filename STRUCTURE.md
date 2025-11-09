# Package Structure

Struktur package MSJ Framework Laravel Generator.

```
msj-framework/
├── src/
│   ├── Console/
│   │   └── Commands/
│   │       ├── Concerns/
│   │       │   └── HasConsoleStyling.php
│   │       ├── MakeMSJController.php
│   │       ├── MakeMSJCrud.php
│   │       ├── MakeMSJModel.php
│   │       ├── MakeMSJModule.php
│   │       ├── MakeMSJViews.php
│   │       └── MSJMake.php
│   ├── Services/
│   │   ├── Templates/
│   │   │   ├── AddView.php
│   │   │   ├── ControllerTemplate.php
│   │   │   ├── EditView.php
│   │   │   ├── JavascriptTemplate.php
│   │   │   ├── JsComponent.php
│   │   │   ├── ListView.php
│   │   │   ├── ModelTemplate.php
│   │   │   ├── MSJBaseControllerTemplate.php
│   │   │   ├── ShowView.php
│   │   │   └── Helpers/
│   │   │       ├── ErrorHelperTemplate.php
│   │   │       ├── FormatHelperTemplate.php
│   │   │       ├── FunctionHelperTemplate.php
│   │   │       ├── TableExporterTemplate.php
│   │   │       └── ValidationHelperTemplate.php
│   │   └── MSJModuleGenerator.php
│   └── MSJServiceProvider.php
├── config/
│   └── msj-generator.php
├── .github/
│   └── workflows/
│       └── tests.yml
├── CHANGELOG.md
├── composer.json
├── LICENSE
├── PACKAGE_GUIDE.md
├── QUICK_START.md
├── README.md
├── STRUCTURE.md (this file)
└── .gitignore
```

## Namespace

Semua class menggunakan namespace: `MSJFramework\LaravelGenerator\`

### Console Commands
- `MSJFramework\LaravelGenerator\Console\Commands\MSJMake`
- `MSJFramework\LaravelGenerator\Console\Commands\MakeMSJModule`
- `MSJFramework\LaravelGenerator\Console\Commands\MakeMSJCrud`
- `MSJFramework\LaravelGenerator\Console\Commands\MakeMSJController`
- `MSJFramework\LaravelGenerator\Console\Commands\MakeMSJModel`
- `MSJFramework\LaravelGenerator\Console\Commands\MakeMSJViews`
- `MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling`

### Services
- `MSJFramework\LaravelGenerator\Services\MSJModuleGenerator`

### Templates
- `MSJFramework\LaravelGenerator\Services\Templates\AddView`
- `MSJFramework\LaravelGenerator\Services\Templates\ControllerTemplate`
- `MSJFramework\LaravelGenerator\Services\Templates\EditView`
- `MSJFramework\LaravelGenerator\Services\Templates\JavascriptTemplate`
- `MSJFramework\LaravelGenerator\Services\Templates\JsComponent`
- `MSJFramework\LaravelGenerator\Services\Templates\ListView`
- `MSJFramework\LaravelGenerator\Services\Templates\ModelTemplate`
- `MSJFramework\LaravelGenerator\Services\Templates\MSJBaseControllerTemplate`
- `MSJFramework\LaravelGenerator\Services\Templates\ShowView`

### Template Helpers
- `MSJFramework\LaravelGenerator\Services\Templates\Helpers\ErrorHelperTemplate`
- `MSJFramework\LaravelGenerator\Services\Templates\Helpers\FormatHelperTemplate`
- `MSJFramework\LaravelGenerator\Services\Templates\Helpers\FunctionHelperTemplate`
- `MSJFramework\LaravelGenerator\Services\Templates\Helpers\TableExporterTemplate`
- `MSJFramework\LaravelGenerator\Services\Templates\Helpers\ValidationHelperTemplate`

### Service Provider
- `MSJFramework\LaravelGenerator\MSJServiceProvider`

## Files Description

### Console Commands
- **MSJMake.php** - Hub command dengan interactive menu
- **MakeMSJModule.php** - Wizard interaktif untuk generate module
- **MakeMSJCrud.php** - Quick CRUD generator
- **MakeMSJController.php** - Controller generator
- **MakeMSJModel.php** - Model generator
- **MakeMSJViews.php** - Views generator
- **HasConsoleStyling.php** - Trait untuk console styling

### Services
- **MSJModuleGenerator.php** - Main generator service

### Templates
- **AddView.php** - Template untuk add.blade.php
- **EditView.php** - Template untuk edit.blade.php
- **ListView.php** - Template untuk list.blade.php
- **ShowView.php** - Template untuk show.blade.php
- **ControllerTemplate.php** - Template untuk Controller
- **ModelTemplate.php** - Template untuk Model
- **JavascriptTemplate.php** - Template untuk JavaScript
- **JsComponent.php** - JS component generator
- **MSJBaseControllerTemplate.php** - Base controller template

### Template Helpers
- **ErrorHelperTemplate.php** - Error helper template
- **FormatHelperTemplate.php** - Format helper template
- **FunctionHelperTemplate.php** - Function helper template
- **TableExporterTemplate.php** - Table exporter template
- **ValidationHelperTemplate.php** - Validation helper template

## Configuration

File konfigurasi: `config/msj-generator.php`

## Dependencies

- PHP >= 8.2
- Laravel >= 12.0
- Laravel Prompts >= 0.1.0

## Installation

```bash
composer require msj-framework/laravel-generator
```

## Usage

```bash
php artisan msj:make
```

