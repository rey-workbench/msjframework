<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MSJ Generator Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk MSJ Framework Laravel Generator
    |
    */

    'default_layout' => 'manual',

    'default_gmenu' => 'KOP001',

    'default_dmenu' => 'KOP999',

    'default_authorization' => [
        'add' => '1',
        'edit' => '1',
        'delete' => '1',
        'approval' => '0',
        'value' => '1',
        'print' => '1',
        'excel' => '1',
        'pdf' => '1',
        'rules' => '0',
        'isactive' => '1',
    ],

    'table_config' => [
        'default_list' => '1',
        'default_show' => '1',
        'default_filter' => '1',
        'default_position_left' => '3',
        'default_position_right' => '4',
    ],

    'paths' => [
        'models' => app_path('Models'),
        'controllers' => app_path('Http/Controllers'),
        'views' => resource_path('views'),
        'javascript' => resource_path('views/js'),
    ],
];

