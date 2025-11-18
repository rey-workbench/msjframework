<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MSJ Framework Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for MSJ Framework metadata-driven
    | architecture.
    |
    */

    /**
     * Database tables for metadata
     */
    'tables' => [
        'gmenu' => 'sys_gmenu',
        'dmenu' => 'sys_dmenu',
        'table' => 'sys_table',
        'auth' => 'sys_auth',
        'roles' => 'sys_roles',
        'id' => 'sys_id',
        'counter' => 'sys_counter',
        'log' => 'sys_log',
        'app' => 'sys_app',
    ],

    /**
     * Default layout types
     */
    'layouts' => [
        'manual' => 'Manual - Custom controller & views (full control)',
        'master' => 'Master - Master data with auto-generated CRUD',
        'transc' => 'Transaction - Header-detail transaction',
        'system' => 'System - System configuration',
        'standr' => 'Standard - Simple CRUD with single primary key',
        'sublnk' => 'Sub-Linking - Linking between tables',
        'report' => 'Report - Report menu with filter & result',
    ],

    /**
     * Default values
     */
    'defaults' => [
        'layout' => 'master',
        'gmenu' => 'KOP001',
        'per_page' => 10,
        'date_format' => 'd/m/Y H:i',
        'decimal_places' => 0,
        'currency_prefix' => 'Rp.',
    ],

    /**
     * Authorization configuration
     */
    'auth' => [
        'default_role' => 'user',
        'super_admin_role' => 'msjit',
        'check_rules' => true,
    ],

    /**
     * ID Generation configuration
     */
    'id_generation' => [
        'enabled' => true,
        'sources' => [
            'int' => 'Internal field from request',
            'ext' => 'External/static string',
            'th2' => 'Year 2 digits (YY)',
            'th4' => 'Year 4 digits (YYYY)',
            'bln' => 'Month (MM)',
            'tgl' => 'Date (DD)',
            'cnt' => 'Auto-increment counter',
        ],
    ],

    /**
     * Field types for sys_table
     */
    'field_types' => [
        'char' => 'Text field (short)',
        'string' => 'Text field (long)',
        'text' => 'Textarea',
        'number' => 'Number input',
        'currency' => 'Currency input',
        'date' => 'Date picker',
        'email' => 'Email input',
        'password' => 'Password input',
        'file' => 'File upload',
        'image' => 'Image upload',
        'enum' => 'Select dropdown',
        'search' => 'Search with modal',
        'join' => 'Join from another table',
        'hidden' => 'Hidden field',
    ],

    /**
     * Logging configuration
     */
    'logging' => [
        'enabled' => true,
        'table' => 'sys_log',
        'types' => [
            'V' => 'View',
            'C' => 'Create',
            'U' => 'Update',
            'D' => 'Delete',
            'E' => 'Error',
        ],
    ],
];
