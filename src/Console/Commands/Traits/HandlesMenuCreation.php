<?php

namespace MSJFramework\Console\Commands\Traits;

use Illuminate\Support\Facades\DB;

trait HandlesMenuCreation
{
    /**
     * Create menu and all related records
     */
    protected function createMenu(): void
    {
        DB::beginTransaction();

        try {
            $this->createGroupMenuIfNeeded();
            $this->createSublinkParentIfNeeded();
            $this->createDetailMenu();
            $this->createAuthorizationRecords();
            $this->insertTableConfiguration();
            $this->createIdGenerationRulesIfNeeded();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create group menu if new
     */
    protected function createGroupMenuIfNeeded(): void
    {
        if (!isset($this->menuData['create_new_gmenu'])) {
            return;
        }

        DB::table('sys_gmenu')->insert([
            'gmenu' => $this->menuData['gmenu'],
            'name' => $this->menuData['gmenu_name'],
            'icon' => $this->menuData['gmenu_icon'],
            'urut' => $this->menuData['gmenu_urut'],
            'isactive' => '1',
            'user_create' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Create parent sublink container if needed
     * Note: sys_table for parent query is handled by HandlesTableConfiguration trait
     */
    protected function createSublinkParentIfNeeded(): void
    {
        if (!isset($this->menuData['create_parent'])) {
            return;
        }

        // Check if parent menu already exists
        $exists = DB::table('sys_dmenu')
            ->where('dmenu', $this->menuData['parent_dmenu'])
            ->exists();
        
        // Only insert if not exists
        if (!$exists) {
            DB::table('sys_dmenu')->insert([
                'dmenu' => $this->menuData['parent_dmenu'],
                'gmenu' => $this->menuData['gmenu'],
                'name' => $this->menuData['parent_name'],
                'url' => strtolower($this->menuData['parent_dmenu']),
                'tabel' => '-',
                'layout' => 'sublnk',
                'sub' => null,  // Parent tidak punya sub (bukan self-reference)
                'show' => '0',
                'urut' => $this->menuData['dmenu_urut'] - 1,
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Create detail menu
     */
    protected function createDetailMenu(): void
    {
        // Untuk layout sublnk, menu yang dibuat adalah menu DATA yang akan muncul di list sublink
        // Layout aktual untuk menu data ini adalah 'standr' atau 'master', bukan 'sublnk'
        $actualLayout = $this->menuData['layout'];
        if ($actualLayout === 'sublnk') {
            // Default ke standr untuk menu data di sublink
            $actualLayout = 'standr';
        }

        DB::table('sys_dmenu')->insert([
            'dmenu' => $this->menuData['dmenu'],
            'gmenu' => $this->menuData['gmenu'],
            'name' => $this->menuData['dmenu_name'],
            'url' => $this->menuData['url'],
            'icon' => $this->menuData['icon'] ?? 'fas fa-file',
            'tabel' => $this->menuData['table'],
            'layout' => $actualLayout,
            'where' => $this->menuData['where_clause'],
            'sub' => $this->menuData['parent_link'] ?? null,
            'show' => $this->menuData['show'] ?? '1',
            'js' => $this->menuData['js_menu'],
            'notif' => $this->menuData['notif'] ?? null,
            'urut' => $this->menuData['dmenu_urut'],
            'isactive' => '1',
            'user_create' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Create authorization records for all roles
     */
    protected function createAuthorizationRecords(): void
    {
        if (empty($this->menuData['auth_roles'])) {
            return;
        }

        // Authorization untuk menu data
        foreach ($this->menuData['auth_roles'] as $roleId => $permissions) {
            DB::table('sys_auth')->insert([
                'gmenu' => $this->menuData['gmenu'],
                'dmenu' => $this->menuData['dmenu'],
                'idroles' => $roleId,
                'value' => $permissions['value'],
                'add' => $permissions['add'],
                'edit' => $permissions['edit'],
                'delete' => $permissions['delete'],
                'approval' => $permissions['approval'],
                'print' => $permissions['print'],
                'excel' => $permissions['excel'],
                'pdf' => $permissions['pdf'],
                'rules' => $permissions['rules'],
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Authorization untuk parent sublink (jika create_parent = true atau add_parent_auth = true)
        if ((isset($this->menuData['create_parent']) && $this->menuData['create_parent']) || 
            (isset($this->menuData['add_parent_auth']) && $this->menuData['add_parent_auth'])) {
            
            foreach ($this->menuData['auth_roles'] as $roleId => $permissions) {
                // Check if authorization already exists for this role
                $existingAuth = DB::table('sys_auth')
                    ->where('gmenu', $this->menuData['gmenu'])
                    ->where('dmenu', $this->menuData['parent_dmenu'])
                    ->where('idroles', $roleId)
                    ->first();
                
                // Only insert if not exists
                if (!$existingAuth) {
                    DB::table('sys_auth')->insert([
                        'gmenu' => $this->menuData['gmenu'],
                        'dmenu' => $this->menuData['parent_dmenu'],
                        'idroles' => $roleId,
                        'value' => $permissions['value'],
                        'add' => $permissions['add'],
                        'edit' => $permissions['edit'],
                        'delete' => $permissions['delete'],
                        'approval' => $permissions['approval'],
                        'print' => $permissions['print'],
                        'excel' => $permissions['excel'],
                        'pdf' => $permissions['pdf'],
                        'rules' => $permissions['rules'],
                        'isactive' => '1',
                        'user_create' => 'system',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Create ID generation rules if configured
     */
    protected function createIdGenerationRulesIfNeeded(): void
    {
        if (empty($this->menuData['id_rules'])) {
            return;
        }

        foreach ($this->menuData['id_rules'] as $rule) {
            DB::table('sys_id')->insert([
                'dmenu' => $this->menuData['dmenu'],
                'source' => $rule['source'],
                'internal' => $rule['internal'],
                'external' => $rule['external'],
                'length' => $rule['length'],
                'urut' => $rule['urut'],
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
