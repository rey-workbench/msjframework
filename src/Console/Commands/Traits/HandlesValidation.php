<?php

namespace MSJFramework\Console\Commands\Traits;

use Illuminate\Support\Facades\DB;

trait HandlesValidation
{
    protected function validateGmenuId($value): ?string
    {
        if (strlen($value) !== 6) {
            return 'ID Menu Grup harus tepat 6 karakter';
        }

        if (DB::table('sys_gmenu')->where('gmenu', $value)->exists()) {
            return 'ID Menu Grup sudah digunakan';
        }

        return null;
    }

    protected function validateDmenuId($value): ?string
    {
        if (strlen($value) !== 6) {
            return 'ID Menu Detail harus tepat 6 karakter';
        }

        if (DB::table('sys_dmenu')->where('dmenu', $value)->exists()) {
            return 'ID Menu Detail sudah digunakan';
        }

        return null;
    }

    protected function validateUrl($value): ?string
    {
        // URL harus dimulai dengan huruf (untuk nama class yang valid)
        if (!preg_match('/^[a-z][a-z0-9\-]*$/', $value)) {
            return 'URL harus dimulai dengan huruf, boleh berisi angka dan strip (-)';
        }

        if (DB::table('sys_dmenu')->where('url', $value)->exists()) {
            return 'URL sudah digunakan';
        }

        return null;
    }
}
