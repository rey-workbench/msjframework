<?php

namespace MSJFramework\LaravelGenerator\Services\Templates;

use Illuminate\Support\Str;

class ControllerTemplate
{
    public static function getTemplate(array $config): string
    {
        $controllerName = Str::studly($config['url']).'Controller';
        $modelName = Str::studly(Str::singular($config['table']));

        return <<<PHP
<?php

namespace App\Http\Controllers;

use App\Helpers\Koperasi\ValidationHelper;
use App\Models\\{$modelName};
use Illuminate\Support\Facades\DB;

class {$controllerName} extends MSJBaseController
{
    public function index(\$data)
    {
        \$data = \$this->initialize(\$data);
        \$data = array_merge(\$data, \$this->initializeTableLayout(\$data));

        \$query = {$modelName}::orderBy({$modelName}::FIELD_CREATED_AT, 'desc');
        \$collection = \$query->get();

        \$data['table_detail'] = \$this->paginate(\$collection);
        \$this->log('V', \$data['dmenu'], 'Akses Menu {$config['menu_name']}');

        return view(\$data['url'], \$data);
    }

    public function add(\$data)
    {
        \$data = \$this->initialize(\$data);

        if (! \$this->auth(\$data, 'add')) {
            return \$this->error(\$data, 'Not Authorized!', 'add');
        }

        \$data = array_merge(\$data, \$this->initializeTableLayout(\$data));
        if (! \$data['table_primary']) {
            return \$this->error(\$data, 'Table structure not found!');
        }

        \$this->log('V', \$data['dmenu'], 'Akses Form Tambah {$config['menu_name']}');

        return view(\$data['url'], \$data);
    }

    public function store(\$data)
    {
        if (! \$this->auth(\$data, 'store')) {
            return \$this->error(\$data, 'Not Authorized!', 'store');
        }

        \$validation = ValidationHelper::validate(\$data['dmenu']);
        if (! \$validation['success']) {
            \$errorMessage = \$validation['errors']->first();

            return redirect()->back()
                ->withInput()
                ->with('message', \$errorMessage)
                ->with('class', 'danger');
        }

        return \$this->transaction(function () use (\$validation) {
            \$requestData = \$validation['data'];

            if (! isset(\$requestData['isactive'])) {
                \$requestData['isactive'] = '1';
            }

            \$requestData['user_create'] = \$this->getUsername();
            {$modelName}::create(\$requestData);

            return true;
        }, \$data, 'Data berhasil ditambahkan');
    }

    public function edit(\$data)
    {
        \$data = \$this->initialize(\$data);

        if (! \$this->auth(\$data, 'edit')) {
            return \$this->error(\$data, 'Not Authorized!', 'edit');
        }

        if (empty(\$data['idencrypt']) || \$data['idencrypt'] === 'edit') {
            return \$this->error(\$data, 'ID parameter tidak ditemukan untuk edit!');
        }

        \$decrypted = \$this->decrypt(\$data['idencrypt']);
        if (! \$decrypted['success']) {
            return \$this->error(\$data, \$decrypted['error']);
        }

        \$record = {$modelName}::find(\$decrypted['id']);
        if (! \$record) {
            return \$this->error(\$data, 'Data tidak ditemukan!');
        }

        \$data = array_merge(\$data, \$this->initializeTableLayout(\$data));
        if (! \$data['table_primary']) {
            return \$this->error(\$data, 'Table structure not found!');
        }

        \$data['table_detail'] = \$record;
        \$this->log('V', \$data['dmenu'], 'Akses Form Edit {$config['menu_name']}');

        return view(\$data['url'], \$data);
    }

    public function update(\$data)
    {
        if (! \$this->auth(\$data, 'update')) {
            return \$this->error(\$data, 'Not Authorized!', 'update');
        }

        \$decrypted = \$this->decrypt(\$data['idencrypt']);
        if (! \$decrypted['success']) {
            return redirect()->back()->with('message', \$decrypted['error'])->with('class', 'danger');
        }

        \$record = {$modelName}::find(\$decrypted['id']);
        if (! \$record) {
            return redirect()->back()->with('message', 'Data tidak ditemukan!')->with('class', 'danger');
        }

        \$validation = ValidationHelper::validate(\$data['dmenu'], \$decrypted['id']);
        if (! \$validation['success']) {
            \$errorMessage = \$validation['errors']->first();

            return redirect()->back()
                ->withInput()
                ->with('message', \$errorMessage)
                ->with('class', 'danger');
        }

        return \$this->transaction(function () use (\$record, \$validation) {
            \$requestData = \$validation['data'];
            \$requestData['user_update'] = \$this->getUsername();

            \$record->update(\$requestData);

            return true;
        }, \$data, 'Data berhasil diupdate');
    }

    public function destroy(\$data)
    {
        if (! \$this->auth(\$data, 'delete')) {
            return \$this->error(\$data, 'Not Authorized!', 'delete');
        }

        \$decrypted = \$this->decrypt(\$data['idencrypt']);
        if (! \$decrypted['success']) {
            return redirect()->back()->with('message', \$decrypted['error'])->with('class', 'danger');
        }

        \$record = {$modelName}::find(\$decrypted['id']);
        if (! \$record) {
            return redirect()->back()->with('message', 'Data tidak ditemukan!')->with('class', 'danger');
        }

        return \$this->transaction(function () use (\$record) {
            \$record->isactive = \$record->isactive == '1' ? '0' : '1';
            \$record->user_update = \$this->getUsername();
            \$record->save();

            return true;
        }, \$data, 'Status data berhasil diubah');
    }

    public function show(\$data)
    {
        \$data = \$this->initialize(\$data);
        \$data = array_merge(\$data, \$this->initializeTableLayout(\$data));

        if (empty(\$data['idencrypt']) || \$data['idencrypt'] === 'show') {
            return \$this->error(\$data, 'ID parameter tidak ditemukan!');
        }

        \$decrypted = \$this->decrypt(\$data['idencrypt']);
        if (! \$decrypted['success']) {
            return \$this->error(\$data, \$decrypted['error']);
        }

        \$record = {$modelName}::find(\$decrypted['id']);
        if (! \$record) {
            return \$this->error(\$data, 'Data tidak ditemukan!');
        }

        \$data['table_detail'] = \$record;
        \$this->log('V', \$data['dmenu'], 'Akses Detail {$config['menu_name']}');

        return view(\$data['url'], \$data);
    }

    public function getExportData(array \$data): array
    {
        \$query = {$modelName}::query();
        
        // Apply authorization rules if needed
        if (\$data['authorize']->rules == '1') {
            \$roles = array_map('trim', explode(',', \$data['user_login']->idroles));
            \$query->where(function (\$q) use (\$roles) {
                foreach (\$roles as \$role) {
                    \$q->orWhereRaw("FIND_IN_SET(?, REPLACE(rules, ' ', ''))", [\$role]);
                }
            });
        }

        // Apply search if exists
        \$search = request('search');
        if (! empty(\$search)) {
            \$query->where(function (\$q) use (\$search, \$data) {
                foreach (\$data['table_header'] as \$header) {
                    \$q->orWhere(\$header->field, 'like', "%{\$search}%");
                }
            });
        }

        \$data['table_detail'] = \$query->get();

        return \$data;
    }
}

PHP;
    }
}
