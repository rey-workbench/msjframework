<?php

namespace MSJFramework\Console\Commands\Traits;

use Illuminate\Support\Facades\DB;

trait HandlesValidation
{
    protected function validateGmenuId($value): ?string
    {
        if (!preg_match('/^[a-z0-9]{6}$/', $value)) {
            return 'ID Menu Grup harus 6 karakter, huruf kecil atau angka';
        }

        if (DB::table('sys_gmenu')->where('gmenu', $value)->exists()) {
            return 'ID Menu Grup sudah digunakan';
        }

        return null;
    }

    protected function validateDmenuId($value): ?string
    {
        if (!preg_match('/^[a-z0-9]{6}$/', $value)) {
            return 'ID Menu Detail harus 6 karakter, huruf kecil atau angka';
        }

        if (DB::table('sys_dmenu')->where('dmenu', $value)->exists()) {
            return 'ID Menu Detail sudah digunakan';
        }

        return null;
    }

    protected function validateUrl($value): ?string
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $value)) {
            return 'URL harus huruf kecil dengan angka dan strip (-)';
        }

        if (DB::table('sys_dmenu')->where('url', $value)->exists()) {
            return 'URL sudah digunakan';
        }

        return null;
    }
}
