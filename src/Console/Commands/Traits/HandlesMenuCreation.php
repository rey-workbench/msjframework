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
     */
    protected function createSublinkParentIfNeeded(): void
    {
        if (!isset($this->menuData['create_parent'])) {
            return;
        }

        DB::table('sys_dmenu')->insert([
            'dmenu' => $this->menuData['parent_dmenu'],
            'gmenu' => $this->menuData['gmenu'],
            'name' => $this->menuData['parent_name'],
            'url' => strtolower($this->menuData['parent_dmenu']),
            'tabel' => '-',
            'layout' => 'sublnk',
            'sub' => $this->menuData['parent_link'],
            'show' => '0',
            'urut' => $this->menuData['dmenu_urut'] - 1,
            'isactive' => '1',
            'user_create' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create sys_table for parent query
        DB::table('sys_table')->insert([
            'gmenu' => $this->menuData['gmenu'],
            'dmenu' => $this->menuData['parent_dmenu'],
            'urut' => 1,
            'field' => 'query',
            'alias' => 'Parent Query',
            'type' => 'report',
            'length' => 0,
            'decimals' => '0',
            'default' => '',
            'validate' => '',
            'primary' => '0',
            'generateid' => '',
            'filter' => '0',
            'list' => '1',
            'show' => '1',
            'query' => "SELECT gmenu, dmenu, icon, tabel, name AS Detail FROM sys_dmenu WHERE sub = '{$this->menuData['parent_link']}'",
            'class' => '',
            'sub' => '',
            'link' => '',
            'note' => '',
            'position' => '1',
            'isactive' => '1',
            'user_create' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Create detail menu
     */
    protected function createDetailMenu(): void
    {
        DB::table('sys_dmenu')->insert([
            'dmenu' => $this->menuData['dmenu'],
            'gmenu' => $this->menuData['gmenu'],
            'name' => $this->menuData['dmenu_name'],
            'url' => $this->menuData['url'],
            'tabel' => $this->menuData['table'],
            'layout' => $this->menuData['layout'],
            'where' => $this->menuData['where_clause'],
            'sub' => $this->menuData['parent_link'] ?? null,
            'js' => $this->menuData['js_menu'],
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
