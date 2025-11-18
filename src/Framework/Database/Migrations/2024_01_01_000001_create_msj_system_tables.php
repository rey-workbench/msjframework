<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // sys_app - Application configuration
        Schema::create('sys_app', function (Blueprint $table) {
            $table->id();
            $table->string('appid', 200);
            $table->string('appname', 200);
            $table->text('description');
            $table->string('company', 100);
            $table->text('address');
            $table->string('city', 50)->nullable();
            $table->string('province', 50)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('telephone', 50)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('logo_small')->default('logo_small.png');
            $table->string('logo_large')->default('logo_large.png');
            $table->string('cover_out')->default('cover_out.png');
            $table->string('cover_in')->default('cover_in.png');
            $table->string('icon')->default('icon.png');
            $table->enum('isactive', [0, 1])->default(1); // 1=active,0=not active
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
        });

        // sys_roles - User roles
        Schema::create('sys_roles', function (Blueprint $table) {
            $table->char('idroles', 6);
            $table->string('name', 20);
            $table->string('description', 100);
            $table->enum('isactive', [0, 1])->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->primary('idroles');
        });

        // sys_gmenu - Group menu
        Schema::create('sys_gmenu', function (Blueprint $table) {
            $table->char('gmenu', 6);
            $table->smallInteger('urut')->nullable();
            $table->string('name', 25)->nullable();
            $table->string('icon', 50)->nullable();
            $table->enum('isactive', [0, 1])->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->primary(['gmenu']);
        });

        // sys_dmenu - Detail menu
        Schema::create('sys_dmenu', function (Blueprint $table) {
            $table->char('dmenu', 6);
            $table->char('gmenu', 6);
            $table->integer('urut');
            $table->string('name', 25)->nullable();
            $table->string('icon', 50)->nullable();
            $table->longText('url')->nullable();
            $table->string('tabel', 50)->nullable();
            $table->string('where')->nullable();
            $table->char('layout', 6)->default('master'); // set template on gmenu
            $table->char('sub', 6)->nullable(); // set submenu
            $table->enum('show', [0, 1])->default(1); // 1=active,0=not active
            $table->enum('js', [0, 1])->default(0); // 1=active,0=not active
            $table->string('notif')->nullable();
            $table->enum('isactive', [0, 1])->default(1); // 1=active,0=not active
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->foreign('gmenu')->references('gmenu')->on('sys_gmenu');
            $table->primary(['dmenu']);
        });

        // sys_auth - Authorization
        Schema::create('sys_auth', function (Blueprint $table) {
            $table->char('idroles', 6);
            $table->char('dmenu', 6);
            $table->char('gmenu', 6);
            $table->enum('add', [0, 1])->default(0);
            $table->enum('edit', [0, 1])->default(0);
            $table->enum('delete', [0, 1])->default(0);
            $table->enum('approval', [0, 1])->default(0);
            $table->enum('value', [0, 1])->default(0);
            $table->enum('print', [0, 1])->default(1);
            $table->enum('excel', [0, 1])->default(1);
            $table->enum('pdf', [0, 1])->default(1);
            $table->enum('rules', [0, 1])->default(0);
            $table->enum('isactive', [0, 1])->default(1); // 1=active,0=not active
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->foreign('idroles')->references('idroles')->on('sys_roles');
            $table->foreign('dmenu')->references('dmenu')->on('sys_dmenu');
            $table->foreign('gmenu')->references('gmenu')->on('sys_gmenu');
            $table->primary(['dmenu', 'idroles']);
        });

        // sys_table - Table configuration
        Schema::create('sys_table', function (Blueprint $table) {
            $table->char('gmenu', 6);
            $table->char('dmenu', 6);
            $table->integer('urut');
            $table->string('field', 25)->nullable();
            $table->string('alias', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->bigInteger('length')->nullable();
            $table->enum('decimals', [0, 1, 2, 3])->default(0);
            $table->string('default', 20)->nullable();
            $table->string('validate', 100)->nullable();
            $table->enum('primary', [0, 1, 2])->default(0); // 1=active,0=not active
            $table->string('generateid', 25)->nullable(); // generate id
            $table->enum('filter', [0, 1])->default(0); // 1=active,0=not active
            $table->enum('list', [0, 1])->default(1); // 1=active,0=not active
            $table->enum('show', [0, 1])->default(1); // 1=active,0=not active
            $table->longText('query')->nullable(); // query report
            $table->string('class', 255)->nullable();
            $table->string('sub', 255)->nullable();
            $table->string('link', 50)->nullable();
            $table->string('note', 255)->nullable();
            $table->enum('position', [0, 1, 2, 3, 4])->default(0); // 0=standard,1=header,2=detail,3=left,4=right
            $table->enum('isactive', [0, 1])->default(1); // 1=active,0=not active
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->foreign('gmenu')->references('gmenu')->on('sys_gmenu');
            $table->foreign('dmenu')->references('dmenu')->on('sys_dmenu');
            $table->primary(['gmenu', 'dmenu', 'urut']);
        });

        // sys_log - Activity log
        Schema::create('sys_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->default(now());
            $table->string('username', 20);
            $table->char('tipe', 1);
            $table->string('dmenu');
            $table->string('description');
            $table->enum('status', [0, 1])->nullable(); // 0=gagal,1=sukses
            $table->string('ipaddress');
            $table->string('useragent');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // sys_number - Number generation
        Schema::create('sys_number', function (Blueprint $table) {
            $table->char('periode', 4);
            $table->char('tipe', 3);
            $table->char('lastid', 10);
            $table->char('lastx', 3);
            $table->enum('isactive', [0, 1])->default(1); // 1=active,0=not active
            $table->primary(['periode', 'tipe']);
        });

        // sys_enum - Enumeration values
        Schema::create('sys_enum', function (Blueprint $table) {
            $table->string('idenum', 25);
            $table->string('value', 10);
            $table->string('name', 255);
            $table->enum('isactive', [0, 1])->default(1); // 1=active,0=not active
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->primary(['idenum', 'value']);
        });

        // sys_id - ID generation configuration
        Schema::create('sys_id', function (Blueprint $table) {
            $table->char('dmenu', 6);
            $table->enum('source', ['int', 'ext', 'cnt', 'th2', 'th4', 'bln', 'tgl'])->default('ext');
            $table->string('internal', 255)->default('-');
            $table->string('external', 255)->default('0');
            $table->integer('urut');
            $table->integer('length');
            $table->enum('isactive', [0, 1])->default(1); // 1=active,0=not active
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->primary(['dmenu', 'source', 'internal', 'external']);
        });

        // sys_counter - Counter for ID generation
        Schema::create('sys_counter', function (Blueprint $table) {
            $table->string('character');
            $table->integer('counter');
            $table->string('lastid');
            $table->enum('isactive', [0, 1])->default(1); // 1=active,0=not active
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->primary('character');
        });

        // transaction_list - Transaction tracking (example table for transc layout)
        Schema::create('transaction_list', function (Blueprint $table) {
            $table->id();
            $table->string('idtrans', 50);
            $table->date('posting');
            $table->string('sloc', 4)->nullable();
            $table->integer('item')->nullable();
            $table->string('material', 100);
            $table->string('batch', 20);
            $table->float('length', 10, 2);
            $table->float('width', 10, 2);
            $table->float('gsm', 10, 2);
            $table->float('weight', 10, 2);
            $table->float('qty', 10, 2);
            $table->string('uom', 5);
            $table->string('color', 20);
            $table->enum('tipe', ['I', 'O'])->default('I'); // I=Input, O=Output
            $table->timestamp('created_at')->useCurrent();
            $table->string('user_create')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_list');
        Schema::dropIfExists('sys_counter');
        Schema::dropIfExists('sys_id');
        Schema::dropIfExists('sys_enum');
        Schema::dropIfExists('sys_number');
        Schema::dropIfExists('sys_log');
        Schema::dropIfExists('sys_table');
        Schema::dropIfExists('sys_auth');
        Schema::dropIfExists('sys_dmenu');
        Schema::dropIfExists('sys_gmenu');
        Schema::dropIfExists('sys_roles');
        Schema::dropIfExists('sys_app');
    }
};
