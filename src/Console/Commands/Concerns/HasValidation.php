<?php

namespace MSJFramework\LaravelGenerator\Console\Commands\Concerns;

use Illuminate\Support\Facades\DB;

trait HasValidation
{
    use HasDatabaseOperations;

    /**
     * Validate gmenu code
     */
    protected function validateGmenuCode(string $value): ?string
    {
        if (empty($value)) {
            return 'Kode gmenu harus diisi';
        }

        if (strlen($value) > 10) {
            return 'Kode gmenu maksimal 10 karakter';
        }

        if ($this->gmenuExists($value)) {
            return "Kode gmenu '{$value}' sudah ada";
        }

        return null;
    }

    /**
     * Validate dmenu code
     */
    protected function validateDmenuCode(string $value): ?string
    {
        if (empty($value)) {
            return 'Kode dmenu harus diisi';
        }

        if (strlen($value) > 10) {
            return 'Kode dmenu maksimal 10 karakter';
        }

        if ($this->dmenuExists($value)) {
            return "Kode dmenu '{$value}' sudah ada";
        }

        return null;
    }

    /**
     * Validate role ID
     */
    protected function validateRoleId(string $value): ?string
    {
        if (empty($value)) {
            return 'ID Role harus diisi';
        }

        if (strlen($value) !== 6) {
            return 'ID Role harus 6 karakter';
        }

        if (!preg_match('/^[a-z0-9]+$/', $value)) {
            return 'ID Role hanya boleh huruf kecil dan angka';
        }

        if ($this->roleExists($value)) {
            return "ID Role '{$value}' sudah ada";
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
}
