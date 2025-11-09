# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-01-XX

### Added
- Initial release of MSJ Framework Laravel Generator
- Interactive wizard using Laravel Prompts
- CRUD generator command (`msj:make:crud`)
- Module generator command (`msj:make:menu`)
- Controller generator command (`msj:make:controller`)
- Model generator command (`msj:make:model`)
- Views generator command (`msj:make:views`)
- Hub command (`msj:make`) with interactive menu
- Support for multiple layout types (manual, standr, transc, system, report)
- Auto-detection of database table structure
- Auto-generation of fillable properties
- Auto-generation of primary keys
- Auto-generation of validation rules
- Beautiful console output with ASCII art and badges
- Search and select functionality for tables and fields
- Configuration file support
- Service Provider for Laravel auto-discovery

### Features
- Generate Models with auto-detected fillable properties
- Generate Controllers with CRUD methods
- Generate Views (list, add, edit, show) with Blade templates
- Generate JavaScript files
- Register menu in database
- Register table configuration in database
- Register authorization in database
- Support for composite primary keys
- Support for auto-increment and non-auto-increment primary keys
- Support for various field types (string, text, number, date, datetime, enum, image, file, etc.)
- Support for search fields with modal
- Support for currency formatting
- Support for date formatting
- Support for image preview
- Support for file uploads

### Dependencies
- PHP >= 8.2
- Laravel >= 12.0
- Laravel Prompts >= 0.1.0

## [Unreleased]

### Planned
- Support for more field types
- Support for relationships
- Support for migrations generation
- Support for API resources
- Support for Form Requests
- Support for Tests generation
- Support for multi-language
- Support for custom templates

