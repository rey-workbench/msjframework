<?php

namespace MSJFramework\LaravelGenerator\Console\Commands\Concerns;

use Illuminate\Support\Facades\DB;

trait HasValidation
{
    use HasDatabaseOperations;

    /**
     * Validate gmenu code
     */
    protected function validateGmenuCode(string $value, int $maxLength = 6): ?string
    {
        if (empty($value)) {
            return 'Kode gmenu harus diisi';
        }

        if (strlen($value) > $maxLength) {
            return "Kode gmenu maksimal {$maxLength} karakter (input: " . strlen($value) . " karakter)";
        }

        // Allow letters and numbers, case insensitive
        if (!preg_match('/^[A-Za-z0-9]+$/', $value)) {
            return 'Kode gmenu hanya boleh huruf dan angka';
        }

        // Check existence case insensitive (check both original and uppercase)
        if ($this->gmenuExists($value) || $this->gmenuExists(strtoupper($value)) || $this->gmenuExists(strtolower($value))) {
            return "Kode gmenu '{$value}' sudah ada";
        }

        return null;
    }

    /**
     * Validate dmenu code
     */
    protected function validateDmenuCode(string $value, int $maxLength = 6): ?string
    {
        if (empty($value)) {
            return 'Kode dmenu harus diisi';
        }

        if (strlen($value) > $maxLength) {
            return "Kode dmenu maksimal {$maxLength} karakter (input: " . strlen($value) . " karakter)";
        }

        // Allow letters and numbers, case insensitive
        if (!preg_match('/^[A-Za-z0-9]+$/', $value)) {
            return 'Kode dmenu hanya boleh huruf dan angka';
        }

        // Check existence case insensitive (check both original and uppercase)
        if ($this->dmenuExists($value) || $this->dmenuExists(strtoupper($value)) || $this->dmenuExists(strtolower($value))) {
            return "Kode dmenu '{$value}' sudah ada";
        }

        return null;
    }

    /**
     * Validate role ID
     */
    protected function validateRoleId(string $value, int $maxLength = 6): ?string
    {
        if (empty($value)) {
            return 'ID Role harus diisi';
        }

        if (strlen($value) > $maxLength) {
            return "ID Role maksimal {$maxLength} karakter";
        }

        if ($this->roleExists($value)) {
            return "ID Role '{$value}' sudah ada";
        }

        return null;
    }

    /**
     * Validate role name
     */
    protected function validateRoleName(string $value, int $maxLength = 20): ?string
    {
        if (empty($value)) {
            return 'Nama role harus diisi';
        }

        if (strlen($value) > $maxLength) {
            return "Nama role maksimal {$maxLength} karakter";
        }

        return null;
    }

    /**
     * Validate role description
     */
    protected function validateRoleDescription(string $value, int $maxLength = 100): ?string
    {
        if (strlen($value) > $maxLength) {
            return "Deskripsi role maksimal {$maxLength} karakter";
        }

        return null;
    }

    /**
     * Validate name field
     */
    protected function validateName(string $value, int $minLength = 2, int $maxLength = 100): ?string
    {
        if (strlen($value) < $minLength) {
            return "Nama minimal {$minLength} karakter";
        }

        if (strlen($value) > $maxLength) {
            return "Nama maksimal {$maxLength} karakter";
        }

        return null;
    }

    /**
     * Validate username
     */
    protected function validateUsername(string $value): ?string
    {
        if (strlen($value) < 3) {
            return 'Username minimal 3 karakter';
        }

        if (strlen($value) > 20) {
            return 'Username maksimal 20 karakter';
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $value)) {
            return 'Username hanya boleh huruf, angka, dan underscore';
        }

        if ($this->usernameExists($value)) {
            return 'Username sudah ada';
        }

        return null;
    }

    /**
     * Validate email
     */
    protected function validateEmail(string $value): ?string
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'Format email tidak valid';
        }

        if ($this->emailExists($value)) {
            return 'Email sudah ada';
        }

        return null;
    }

    /**
     * Validate password
     */
    protected function validatePassword(string $value, int $minLength = 8): ?string
    {
        if (strlen($value) < $minLength) {
            return "Password minimal {$minLength} karakter";
        }

        return null;
    }

    /**
     * Validate numeric field
     */
    protected function validateNumeric(string $value): ?string
    {
        if (!is_numeric($value)) {
            return 'Harus berupa angka';
        }

        return null;
    }

    /**
     * Check if username exists
     */
    protected function usernameExists(string $username): bool
    {
        return DB::table('users')->where('username', $username)->exists();
    }

    /**
     * Check if email exists
     */
    protected function emailExists(string $email): bool
    {
        return DB::table('users')->where('email', $email)->exists();
    }

    /**
     * Check if role exists
     */
    protected function roleExists(string $roleId): bool
    {
        return DB::table('sys_roles')->where('idroles', $roleId)->exists();
    }
}
