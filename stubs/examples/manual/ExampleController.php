<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MSJBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Example Manual Layout Controller
 * 
 * This demonstrates how to create a custom controller for "manual" layout.
 * Layout manual gives you full control over the CRUD operations.
 * 
 * Steps to use manual layout:
 * 1. Create sys_dmenu with layout='manual'
 * 2. Create this controller with name matching the URL (e.g., ExampleController for 'example' URL)
 * 3. Create views in resources/views/{gmenu}/{url}/ folder
 * 4. Create sys_auth to grant access
 */
class ExampleController extends MSJBaseController
{
    /**
     * Display a listing of the resource (List page)
     */
    public function index($data)
    {
        // Initialize common data
        $this->init($data);

        // Get data from database
        $items = DB::table($data['tabel'])
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $data['items'] = $items;

        // Log access
        $this->log_insert('V', $data['dmenu'], 'View ' . $data['title_menu'], '1');

        return view($data['url'], $data);
    }

    /**
     * Show the form for creating a new resource (Add page)
     */
    public function add($data)
    {
        $this->init($data);

        // Check authorization
        if ($data['authorize']->add != '1') {
            return $this->error('Not Authorized to Add');
        }

        return view($data['url'], $data);
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request, $data)
    {
        $this->init($data);

        // Check authorization
        if ($data['authorize']->add != '1') {
            return $this->error('Not Authorized to Add');
        }

        // Validate input
        $validated = $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:' . $data['tabel'] . ',email',
            'description' => 'nullable|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Insert data
            DB::table($data['tabel'])->insert([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'description' => $validated['description'] ?? null,
                'isactive' => '1',
                'user_create' => session('username'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Log activity
            $this->log_insert('C', $data['dmenu'], 'Create ' . $validated['name'], '1');

            return $this->success('Data created successfully', '/' . $data['url_menu']);
        } catch (\Exception $e) {
            DB::rollback();
            $this->log_insert('E', $data['dmenu'], 'Error: ' . $e->getMessage(), '0');
            return $this->error('Failed to create data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit($data)
    {
        $this->init($data);

        // Check authorization
        if ($data['authorize']->edit != '1') {
            return $this->error('Not Authorized to Edit');
        }

        // Decrypt ID
        $id = $this->decrypt($data['idencrypt']);

        // Get data
        $item = DB::table($data['tabel'])->where('id', $id)->first();

        if (!$item) {
            return $this->error('Data not found');
        }

        $data['item'] = $item;

        return view($data['url'], $data);
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, $data)
    {
        $this->init($data);

        // Check authorization
        if ($data['authorize']->edit != '1') {
            return $this->error('Not Authorized to Edit');
        }

        // Decrypt ID
        $id = $this->decrypt($data['idencrypt']);

        // Validate input
        $validated = $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:' . $data['tabel'] . ',email,' . $id,
            'description' => 'nullable|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Update data
            DB::table($data['tabel'])
                ->where('id', $id)
                ->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'description' => $validated['description'] ?? null,
                    'user_update' => session('username'),
                    'updated_at' => now(),
                ]);

            DB::commit();

            // Log activity
            $this->log_insert('U', $data['dmenu'], 'Update ' . $validated['name'], '1');

            return $this->success('Data updated successfully', '/' . $data['url_menu']);
        } catch (\Exception $e) {
            DB::rollback();
            $this->log_insert('E', $data['dmenu'], 'Error: ' . $e->getMessage(), '0');
            return $this->error('Failed to update data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource (Show page)
     */
    public function show($data)
    {
        $this->init($data);

        // Decrypt ID
        $id = $this->decrypt($data['idencrypt']);

        // Get data
        $item = DB::table($data['tabel'])->where('id', $id)->first();

        if (!$item) {
            return $this->error('Data not found');
        }

        $data['item'] = $item;

        return view($data['url'], $data);
    }

    /**
     * Remove the specified resource from storage (Soft delete)
     */
    public function destroy($data)
    {
        $this->init($data);

        // Check authorization
        if ($data['authorize']->delete != '1') {
            return $this->error('Not Authorized to Delete');
        }

        // Decrypt ID
        $id = $this->decrypt($data['idencrypt']);

        try {
            DB::beginTransaction();

            // Get item name for logging
            $item = DB::table($data['tabel'])->where('id', $id)->first();

            if (!$item) {
                return $this->error('Data not found');
            }

            // Soft delete (toggle isactive)
            $newStatus = $item->isactive == '1' ? '0' : '1';
            
            DB::table($data['tabel'])
                ->where('id', $id)
                ->update([
                    'isactive' => $newStatus,
                    'user_update' => session('username'),
                    'updated_at' => now(),
                ]);

            DB::commit();

            // Log activity
            $action = $newStatus == '0' ? 'Deactivate' : 'Activate';
            $this->log_insert('D', $data['dmenu'], $action . ' ' . $item->name, '1');

            return $this->success('Data ' . strtolower($action) . 'd successfully', '/' . $data['url_menu']);
        } catch (\Exception $e) {
            DB::rollback();
            $this->log_insert('E', $data['dmenu'], 'Error: ' . $e->getMessage(), '0');
            return $this->error('Failed to delete data: ' . $e->getMessage());
        }
    }
}
