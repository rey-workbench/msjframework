<?php

namespace MSJFramework\LaravelGenerator\Console\Commands\Concerns;

use Illuminate\Support\Facades\DB;

trait HasDatabaseOperations
{
    /**
     * Get available database tables
     */
    protected function getAvailableTables(): array
    {
        try {
            $database = DB::connection()->getDatabaseName();
            $tables = DB::select('SHOW TABLES');
            $tableKey = "Tables_in_{$database}";

            return array_map(fn ($table) => $table->$tableKey, $tables);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get active group menus
     */
    protected function getActiveGmenus(): array
    {
        return DB::table('sys_gmenu')
            ->where('isactive', '1')
            ->select('gmenu', 'name')
            ->orderBy('urut')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->gmenu => "{$item->gmenu} - {$item->name}"])
            ->toArray();
    }

    /**
     * Get active detail menus
     */
    protected function getActiveDmenus(?string $gmenu = null): array
    {
        $query = DB::table('sys_dmenu')
            ->where('isactive', '1')
            ->select('dmenu', 'name', 'gmenu');

        if ($gmenu) {
            $query->where('gmenu', $gmenu);
        }

        return $query->orderBy('urut')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->dmenu => "{$item->dmenu} - {$item->name}"])
            ->toArray();
    }

    /**
     * Get active roles
     */
    protected function getActiveRoles(): array
    {
        return DB::table('sys_roles')
            ->where('isactive', '1')
            ->select('idroles', 'name')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->idroles => "{$item->name} ({$item->idroles})"])
            ->toArray();
    }

    /**
     * Check if table exists
     */
    protected function tableExists(string $table): bool
    {
        try {
            $result = DB::select("SHOW TABLES LIKE '{$table}'");
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if gmenu exists
     */
    protected function gmenuExists(string $gmenu): bool
    {
        return DB::table('sys_gmenu')->where('gmenu', $gmenu)->exists();
    }

    /**
     * Check if dmenu exists
     */
    protected function dmenuExists(string $dmenu): bool
    {
        return DB::table('sys_dmenu')->where('dmenu', $dmenu)->exists();
    }

    /**
     * Check if role exists
     */
    protected function roleExists(string $idroles): bool
    {
        return DB::table('sys_roles')->where('idroles', $idroles)->exists();
    }

    /**
     * Get table columns
     */
    protected function getTableColumns(string $table): array
    {
        try {
            return DB::select("SHOW COLUMNS FROM {$table}");
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get latest created gmenu
     */
    protected function getLatestGmenu(): ?object
    {
        return DB::table('sys_gmenu')
            ->where('isactive', '1')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get latest created dmenu
     */
    protected function getLatestDmenu(?string $gmenu = null): ?object
    {
        $query = DB::table('sys_dmenu')
            ->where('isactive', '1');

        if ($gmenu) {
            $query->where('gmenu', $gmenu);
        }

        return $query->orderBy('created_at', 'desc')->first();
    }
}
