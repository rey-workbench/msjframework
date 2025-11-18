<?php

namespace MSJFramework\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MSJFramework\Services\DatabaseIntrospectionService;
use MSJFramework\Services\FileGeneratorService;
use function Laravel\Prompts\text;
use function Laravel\Prompts\select;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\error;
use function Laravel\Prompts\note;
use function Laravel\Prompts\table;

class MSJMakeMenuCommand extends Command
{
    protected $signature = 'msj:make menu';
    protected $description = 'Create a new menu with interactive wizard';

    protected DatabaseIntrospectionService $db;
    protected FileGeneratorService $generator;
    
    protected array $menuData = [];
    protected array $tableFields = [];

    public function __construct()
    {
        parent::__construct();
        $this->db = new DatabaseIntrospectionService();
        $this->generator = new FileGeneratorService($this->db);
    }

    public function handle(): int
    {
        $this->displayBanner();

        try {
            $this->selectMenuType();
            $this->configureGroupMenu();
            $this->configureDetailMenu();
            $this->configureAuthorization();

            if ($this->menuData['layout'] !== 'manual') {
                $this->configureTableMetadata();
            }

            if (confirm('Configure ID auto-generation?', false)) {
                $this->configureIDGeneration();
            }

            $this->reviewConfiguration();

            if (confirm('Proceed with menu creation?', true)) {
                $this->createMenu();
                $this->displaySuccess();
                return Command::SUCCESS;
            }

            warning('Menu creation cancelled.');
            return Command::FAILURE;

        } catch (\Exception $e) {
            error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function displayBanner(): void
    {
        $this->newLine();
        info('╔═══════════════════════════════════════════╗');
        info('║   MSJ Framework - Menu Creation Wizard   ║');
        info('╚═══════════════════════════════════════════╝');
        $this->newLine();
    }

    protected function selectMenuType(): void
    {
        note('Step 1: Select Menu Layout Type');

        $this->menuData['layout'] = select(
            label: 'Choose layout type',
            options: [
                'master' => 'Master - Simple CRUD (auto-generated)',
                'transc' => 'Transaction - Header-Detail (auto-generated)',
                'system' => 'System - Configuration forms (auto-generated)',
                'standr' => 'Standard - Standard CRUD (auto-generated)',
                'sublnk' => 'Sub-Linking - Link between tables (auto-generated)',
                'report' => 'Report - Filter and result (auto-generated)',
                'manual' => 'Manual - Custom implementation (full control)',
            ],
            hint: 'Auto layouts generate UI from metadata, Manual gives full control'
        );

        info("Selected: {$this->menuData['layout']} layout");
        $this->newLine();
    }

    protected function configureGroupMenu(): void
    {
        note('Step 2: Group Menu Configuration');

        $existingGmenus = DB::table('sys_gmenu')
            ->where('isactive', '1')
            ->orderBy('urut')
            ->get(['gmenu', 'name'])
            ->mapWithKeys(fn($item) => [$item->gmenu => "{$item->gmenu} - {$item->name}"])
            ->toArray();

        $useExisting = confirm('Use existing group menu?', false);

        if ($useExisting && !empty($existingGmenus)) {
            $this->menuData['gmenu'] = select(
                label: 'Select group menu',
                options: $existingGmenus,
            );
        } else {
            $this->menuData['gmenu'] = text(
                label: 'Group Menu ID (e.g., MSJ001)',
                placeholder: 'MSJ001',
                required: true,
                validate: fn($value) => $this->validateGmenuId($value)
            );

            $this->menuData['gmenu_name'] = text(
                label: 'Group Menu Name',
                placeholder: 'Master Data',
                required: true
            );

            $this->menuData['gmenu_icon'] = text(
                label: 'Group Menu Icon (optional)',
                placeholder: 'fas fa-database',
                default: 'fas fa-folder'
            );

            $lastUrut = DB::table('sys_gmenu')->max('urut') ?? 0;
            $this->menuData['gmenu_urut'] = (int) text(
                label: 'Display Order',
                default: (string)($lastUrut + 1),
                required: true
            );

            $this->menuData['create_new_gmenu'] = true;
        }

        $this->newLine();
    }

    protected function configureDetailMenu(): void
    {
        note('Step 3: Detail Menu Configuration');

        $this->menuData['dmenu'] = text(
            label: 'Detail Menu ID (e.g., MSJ001)',
            placeholder: 'MSJ001',
            required: true,
            validate: fn($value) => $this->validateDmenuId($value)
        );

        $this->menuData['dmenu_name'] = text(
            label: 'Menu Name',
            placeholder: 'Employee Master',
            required: true
        );

        $this->menuData['url'] = text(
            label: 'URL/Route (e.g., employee-master)',
            placeholder: 'employee-master',
            required: true,
            validate: fn($value) => $this->validateUrl($value)
        );

        // Get available tables from service
        $availableTables = $this->db->getAvailableTables();
        
        if (!empty($availableTables)) {
            $this->menuData['table'] = select(
                label: 'Select Database Table',
                options: $availableTables,
                scroll: 15,
                hint: 'Choose from available tables in database'
            );
        } else {
            $this->menuData['table'] = text(
                label: 'Database Table Name',
                placeholder: 'mst_employee',
                required: true,
                validate: fn($value) => $this->db->validateTableName($value)
            );
        }

        $this->menuData['where_clause'] = text(
            label: 'WHERE Clause (optional)',
            placeholder: "isactive='1'",
            default: ''
        );

        $generateJs = confirm('Generate custom JavaScript file?', false);
        $this->menuData['js_menu'] = $generateJs ? '1' : '0';

        $lastUrut = DB::table('sys_dmenu')
            ->where('gmenu', $this->menuData['gmenu'])
            ->max('urut') ?? 0;

        $this->menuData['dmenu_urut'] = (int) text(
            label: 'Display Order',
            default: (string)($lastUrut + 1),
            required: true
        );

        $this->newLine();
    }

    protected function configureAuthorization(): void
    {
        note('Step 4: Authorization Configuration');

        $roles = DB::table('sys_roles')
            ->where('isactive', '1')
            ->pluck('name', 'idroles')
            ->toArray();

        if (empty($roles)) {
            warning('No roles found! Please create roles first.');
            $this->menuData['roles'] = [];
            return;
        }

        $selectedRoles = multiselect(
            label: 'Select roles with access',
            options: $roles,
            required: true,
            hint: 'Use space to select, enter to confirm'
        );

        $this->menuData['auth_roles'] = [];

        foreach ($selectedRoles as $roleId) {
            $this->newLine();
            info("Configuring permissions for: {$roles[$roleId]}");

            $this->menuData['auth_roles'][$roleId] = [
                'value' => '1',
                'add' => confirm('Allow ADD?', true) ? '1' : '0',
                'edit' => confirm('Allow EDIT?', true) ? '1' : '0',
                'delete' => confirm('Allow DELETE?', true) ? '1' : '0',
                'approval' => confirm('Allow APPROVAL?', false) ? '1' : '0',
                'print' => confirm('Allow PRINT?', true) ? '1' : '0',
                'excel' => confirm('Allow EXCEL?', true) ? '1' : '0',
                'pdf' => confirm('Allow PDF?', true) ? '1' : '0',
                'rules' => confirm('Allow RULES?', true) ? '1' : '0',
            ];
        }

        $this->newLine();
    }

    protected function configureTableMetadata(): void
    {
        note('Step 5: Table Metadata Configuration (for auto-generated forms)');

        // Auto-detect fields using service
        $detectedFields = $this->db->detectTableFields($this->menuData['table']);
        
        if (!empty($detectedFields)) {
            info("✓ Detected " . count($detectedFields) . " fields from table '{$this->menuData['table']}'");
            $this->newLine();
            
            $useAutoDetect = confirm('Use auto-detected fields?', true);
            
            if ($useAutoDetect) {
                table(
                    ['Field', 'Type', 'Nullable', 'Default'],
                    collect($detectedFields)->map(fn($f) => [
                        $f['field'],
                        $f['db_type'],
                        $f['nullable'] ? 'Yes' : 'No',
                        $f['default'] ?? '-'
                    ])->toArray()
                );
                $this->newLine();
                
                $selectedFields = multiselect(
                    label: 'Select fields to include in form',
                    options: collect($detectedFields)->mapWithKeys(fn($f) => [
                        $f['field'] => "{$f['field']} ({$f['type']})"
                    ])->toArray(),
                    required: true,
                    hint: 'Space to select, Enter to confirm'
                );
                
                foreach ($selectedFields as $fieldName) {
                    $detected = collect($detectedFields)->firstWhere('field', $fieldName);
                    if ($detected) {
                        $this->tableFields[] = $detected;
                    }
                }
                
                info("✓ " . count($this->tableFields) . " fields configured");
                $this->newLine();
                
                if (confirm('Customize field settings?', false)) {
                    $this->customizeFields();
                }
                
                return;
            }
        } else {
            warning("Could not auto-detect fields from table '{$this->menuData['table']}'");
            info('You will need to configure fields manually');
            $this->newLine();
        }

        // Manual configuration fallback
        $this->configureFieldsManually();
    }

    protected function customizeFields(): void
    {
        $this->newLine();
        info('Customize field settings:');
        $this->newLine();
        
        foreach ($this->tableFields as $index => &$field) {
            info("Field: {$field['label']} ({$field['field']})");
            
            if (confirm("Customize this field?", false)) {
                $field['label'] = text(
                    label: 'Label',
                    default: $field['label'],
                    required: true
                );
                
                $field['type'] = select(
                    label: 'Field Type',
                    options: [
                        'char' => 'Text (short)',
                        'string' => 'Text (long)',
                        'text' => 'Textarea',
                        'number' => 'Number',
                        'currency' => 'Currency',
                        'date' => 'Date',
                        'email' => 'Email',
                        'password' => 'Password',
                        'file' => 'File Upload',
                        'image' => 'Image Upload',
                        'enum' => 'Select/Dropdown',
                        'search' => 'Search Modal',
                        'hidden' => 'Hidden Field',
                    ],
                    default: $field['type']
                );
                
                $field['position'] = select(
                    label: 'Position',
                    options: ['L' => 'Left', 'R' => 'Right', 'F' => 'Full Width'],
                    default: $field['position']
                );
                
                $field['required'] = confirm('Required?', $field['required'] === '1') ? '1' : '0';
                $field['readonly'] = confirm('Read-only?', $field['readonly'] === '1') ? '1' : '0';
                
                if ($field['type'] === 'enum') {
                    $field['idenum'] = text(
                        label: 'Enum ID',
                        placeholder: 'STATUS',
                        required: true
                    );
                }
                
                $this->newLine();
            }
        }
        
        info('✓ Field customization complete');
        $this->newLine();
    }

    protected function configureFieldsManually(): void
    {
        info('Configure fields that will be displayed in the form');
        $this->newLine();

        $urut = 1;
        do {
            $field = [
                'field' => text(label: "Field #{$urut} - Column Name", placeholder: 'emp_name', required: true),
                'label' => text(label: 'Label', placeholder: 'Employee Name', required: true),
                'type' => select(label: 'Field Type', options: [
                    'char' => 'Text (short)', 'string' => 'Text (long)', 'text' => 'Textarea',
                    'number' => 'Number', 'currency' => 'Currency', 'date' => 'Date',
                    'email' => 'Email', 'password' => 'Password', 'file' => 'File Upload',
                    'image' => 'Image Upload', 'enum' => 'Select/Dropdown',
                    'search' => 'Search Modal', 'hidden' => 'Hidden Field',
                ]),
                'length' => (int) text(label: 'Max Length', default: '100', required: true),
                'position' => select(label: 'Position', options: ['L' => 'Left Column', 'R' => 'Right Column', 'F' => 'Full Width']),
                'required' => confirm('Required field?', true) ? '1' : '0',
                'readonly' => confirm('Read-only?', false) ? '1' : '0',
                'idenum' => '',
                'urut' => $urut,
            ];

            if ($field['type'] === 'enum') {
                $field['idenum'] = text(label: 'Enum ID (for dropdown values)', placeholder: 'STATUS', required: true);
            }

            $this->tableFields[] = $field;
            info("✓ Field '{$field['label']}' added");
            $this->newLine();
            $urut++;
        } while (confirm('Add another field?', true));

        info("Total {$urut} fields configured");
        $this->newLine();
    }

    protected function configureIDGeneration(): void
    {
        note('Step 6: ID Generation Configuration');
        info('Configure automatic ID generation pattern (e.g., EMP-2024-0001)');
        $this->newLine();

        $this->menuData['id_rules'] = [];
        $urut = 1;

        do {
            $rule = ['urut' => $urut];
            
            $rule['source'] = select(
                label: "Segment #{$urut} - Source",
                options: [
                    'ext' => 'External String (fixed text)',
                    'int' => 'Internal Field (from form)',
                    'th2' => 'Year (2 digits)',
                    'th4' => 'Year (4 digits)',
                    'bln' => 'Month (01-12)',
                    'tgl' => 'Date (01-31)',
                    'cnt' => 'Counter (auto-increment)',
                ]
            );

            if ($rule['source'] === 'ext') {
                $rule['external'] = text(label: 'Fixed Text', placeholder: 'EMP', required: true);
                $rule['internal'] = '';
            } elseif ($rule['source'] === 'int') {
                $rule['internal'] = text(label: 'Field Name', placeholder: 'dept_code', required: true);
                $rule['external'] = '';
            } else {
                $rule['external'] = '';
                $rule['internal'] = '';
            }

            $rule['length'] = (int) text(
                label: 'Length',
                default: $rule['source'] === 'cnt' ? '4' : '2',
                required: true
            );

            $this->menuData['id_rules'][] = $rule;
            info("✓ Segment added: {$rule['source']} (length: {$rule['length']})");
            $this->newLine();
            $urut++;
        } while (confirm('Add another segment?', $urut < 5));

        $this->newLine();
    }

    protected function reviewConfiguration(): void
    {
        $this->newLine();
        note('═══════════════════════════════════════════');
        note('Configuration Review');
        note('═══════════════════════════════════════════');
        $this->newLine();

        table(
            ['Setting', 'Value'],
            [
                ['Layout Type', $this->menuData['layout']],
                ['Group Menu', $this->menuData['gmenu']],
                ['Detail Menu', $this->menuData['dmenu']],
                ['Menu Name', $this->menuData['dmenu_name']],
                ['URL', $this->menuData['url']],
                ['Table', $this->menuData['table']],
                ['JavaScript File', $this->menuData['js_menu'] === '1' ? 'Yes' : 'No'],
                ['Authorized Roles', count($this->menuData['auth_roles'])],
                ['Table Fields', count($this->tableFields)],
                ['ID Generation', !empty($this->menuData['id_rules']) ? 'Yes' : 'No'],
            ]
        );

        $this->newLine();
    }

    protected function createMenu(): void
    {
        DB::beginTransaction();

        try {
            if (isset($this->menuData['create_new_gmenu'])) {
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

            DB::table('sys_dmenu')->insert([
                'dmenu' => $this->menuData['dmenu'],
                'gmenu' => $this->menuData['gmenu'],
                'name' => $this->menuData['dmenu_name'],
                'url' => $this->menuData['url'],
                'tabel' => $this->menuData['table'],
                'layout' => $this->menuData['layout'],
                'where' => $this->menuData['where_clause'],
                'js' => $this->menuData['js_menu'],
                'urut' => $this->menuData['dmenu_urut'],
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($this->menuData['auth_roles'] as $roleId => $permissions) {
                DB::table('sys_auth')->insert([
                    'gmenu' => $this->menuData['gmenu'],
                    'dmenu' => $this->menuData['dmenu'],
                    'idroles' => $roleId,
                    'value' => $permissions['value'],
                    'add' => $permissions['add'],
                    'edit' => $permissions['edit'],
                    'delete' => $permissions['delete'],
                    'export' => $permissions['export'],
                    'isactive' => '1',
                    'user_create' => 'system',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (!empty($this->tableFields)) {
                foreach ($this->tableFields as $field) {
                    DB::table('sys_table')->insert([
                        'dmenu' => $this->menuData['dmenu'],
                        'field' => $field['field'],
                        'label' => $field['label'],
                        'type' => $field['type'],
                        'length' => $field['length'],
                        'position' => $field['position'],
                        'required' => $field['required'],
                        'readonly' => $field['readonly'],
                        'idenum' => $field['idenum'],
                        'urut' => $field['urut'],
                        'isactive' => '1',
                        'user_create' => 'system',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            if (!empty($this->menuData['id_rules'])) {
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function displaySuccess(): void
    {
        $this->newLine();
        info('╔═══════════════════════════════════════════╗');
        info('║          Menu Created Successfully!       ║');
        info('╚═══════════════════════════════════════════╝');
        $this->newLine();

        note('Access your new menu at:');
        info("URL: /{$this->menuData['url']}");
        $this->newLine();

        if ($this->menuData['layout'] === 'manual') {
            info('✨ Manual Layout - Auto-Generating Files...');
            $this->newLine();
            
            try {
                // Use generator service
                $model = $this->generator->generateModel($this->menuData['table']);
                info("✓ Model: {$model['name']}");
                
                $controller = $this->generator->generateController(
                    $this->menuData['url'],
                    $this->menuData['table']
                );
                info("✓ Controller: {$controller['name']}");
                
                $this->generator->generateViews(
                    $this->menuData['gmenu'],
                    $this->menuData['url'],
                    $this->menuData['table']
                );
                info('✓ Views: list.blade.php, add.blade.php, edit.blade.php, show.blade.php');
                
                $this->newLine();
                info('📁 Generated files:');
                info("   app/Models/{$model['name']}.php");
                info("   app/Http/Controllers/{$controller['name']}.php");
                info("   resources/views/{$this->menuData['gmenu']}/{$this->menuData['url']}/");
            } catch (\Exception $e) {
                warning('⚠️  Auto-generation failed: ' . $e->getMessage());
                info('You may need to create files manually.');
                info('See: MANUAL_LAYOUT_GUIDE.md for examples');
            }
        } else {
            info('✓ Controller: Auto-handled by ' . ucfirst($this->menuData['layout']) . 'Controller');
            info('✓ Views: Auto-generated from sys_table metadata');
            info('✓ CRUD Operations: Ready to use!');
        }

        // Generate JavaScript file if requested
        if ($this->menuData['js_menu'] === '1') {
            $this->newLine();
            info('✨ Generating JavaScript file...');
            
            try {
                $js = $this->generator->generateJavaScriptFile($this->menuData['dmenu']);
                info("✓ JavaScript: {$js['name']}");
                info("   {$js['path']}");
            } catch (\Exception $e) {
                warning('⚠️  JavaScript generation failed: ' . $e->getMessage());
            }
        }

        $this->newLine();
        note('Next steps:');
        info('1. Login to http://127.0.0.1/login');
        info('2. Navigate to the new menu');
        info('3. Test CRUD operations');
        $this->newLine();
    }

    protected function validateGmenuId($value): ?string
    {
        if (!preg_match('/^[A-Z0-9]{3,10}$/', $value)) {
            return 'Group Menu ID must be 3-10 uppercase alphanumeric characters';
        }
        return null;
    }

    protected function validateDmenuId($value): ?string
    {
        if (!preg_match('/^[A-Z0-9]{3,10}$/', $value)) {
            return 'Detail Menu ID must be 3-10 uppercase alphanumeric characters';
        }

        if (DB::table('sys_dmenu')->where('dmenu', $value)->exists()) {
            return 'Detail Menu ID already exists';
        }

        return null;
    }

    protected function validateUrl($value): ?string
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $value)) {
            return 'URL must be lowercase alphanumeric with hyphens only';
        }

        if (DB::table('sys_dmenu')->where('url', $value)->exists()) {
            return 'URL already exists';
        }

        return null;
    }
}
