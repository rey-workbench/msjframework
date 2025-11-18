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
            $table->string('appid', 50)->primary();
            $table->string('appname', 100);
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('cover_in')->nullable();
            $table->string('cover_out')->nullable();
            $table->string('version', 20)->default('1.0.0');
            $table->string('isactive', 1)->default('1');
            $table->timestamps();
        });

        // sys_roles - User roles
        Schema::create('sys_roles', function (Blueprint $table) {
            $table->string('idroles', 50)->primary();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('isactive', 1)->default('1');
            $table->string('user_create', 50)->nullable();
            $table->string('user_update', 50)->nullable();
            $table->timestamps();
        });

        // sys_gmenu - Group menu
        Schema::create('sys_gmenu', function (Blueprint $table) {
            $table->string('gmenu', 50)->primary();
            $table->string('name', 100);
            $table->string('icon', 50)->nullable();
            $table->integer('urut')->default(0);
            $table->string('isactive', 1)->default('1');
            $table->string('user_create', 50)->nullable();
            $table->string('user_update', 50)->nullable();
            $table->timestamps();
        });

        // sys_dmenu - Detail menu
        Schema::create('sys_dmenu', function (Blueprint $table) {
            $table->string('dmenu', 50)->primary();
            $table->string('gmenu', 50);
            $table->string('name', 100);
            $table->string('url', 100);
            $table->string('tabel', 100)->nullable();
            $table->string('layout', 20)->default('manual');
            $table->text('where')->nullable();
            $table->string('js', 1)->default('0');
            $table->integer('urut')->default(0);
            $table->string('isactive', 1)->default('1');
            $table->string('user_create', 50)->nullable();
            $table->string('user_update', 50)->nullable();
            $table->timestamps();

            $table->foreign('gmenu')->references('gmenu')->on('sys_gmenu')->onDelete('cascade');
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
            $table->id();
            $table->string('gmenu', 50);
            $table->string('dmenu', 50);
            $table->string('field', 100);
            $table->string('alias', 100);
            $table->string('type', 20);
            $table->integer('length')->default(0);
            $table->integer('decimals')->default(0);
            $table->text('query')->nullable();
            $table->string('default')->nullable();
            $table->text('validate')->nullable();
            $table->string('class')->nullable();
            $table->text('note')->nullable();
            $table->string('primary', 1)->default('0');
            $table->string('generateid', 1)->default('0');
            $table->string('list', 1)->default('0');
            $table->string('show', 1)->default('0');
            $table->string('filter', 1)->default('0');
            $table->string('position', 1)->default('3'); // 3=left, 4=right
            $table->string('link')->nullable();
            $table->string('sub', 100)->nullable();
            $table->integer('urut')->default(0);
            $table->string('isactive', 1)->default('1');
            $table->timestamps();

            $table->foreign('gmenu')->references('gmenu')->on('sys_gmenu')->onDelete('cascade');
            $table->foreign('dmenu')->references('dmenu')->on('sys_dmenu')->onDelete('cascade');
        });

        // sys_id - ID generation configuration
        Schema::create('sys_id', function (Blueprint $table) {
            $table->id();
            $table->string('dmenu', 50);
            $table->string('source', 20); // int, ext, th2, th4, bln, tgl, cnt
            $table->string('internal', 100)->nullable();
            $table->string('external', 100)->nullable();
            $table->integer('length')->default(0);
            $table->integer('urut')->default(0);
            $table->string('isactive', 1)->default('1');
            $table->timestamps();

            $table->foreign('dmenu')->references('dmenu')->on('sys_dmenu')->onDelete('cascade');
        });

        // sys_counter - Auto-increment counter for ID generation
        Schema::create('sys_counter', function (Blueprint $table) {
            $table->string('character', 100)->primary();
            $table->integer('counter')->default(1);
            $table->string('lastid', 100)->nullable();
            $table->timestamps();
        });

        // sys_log - System logs
        Schema::create('sys_log', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50);
            $table->string('type', 1); // V, C, U, D, E
            $table->string('dmenu', 50);
            $table->text('description');
            $table->string('status', 1);
            $table->string('ip_address', 50)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        // sys_number - Number tracking (legacy support)
        Schema::create('sys_number', function (Blueprint $table) {
            $table->char('periode', 4);
            $table->char('tipe', 3);
            $table->char('lastid', 10);
            $table->char('lastx', 3);
            $table->enum('isactive', ['0', '1'])->default('1');
            $table->primary(['periode', 'tipe']);
        });

        // sys_enum - Enum values for dropdown/select
        Schema::create('sys_enum', function (Blueprint $table) {
            $table->string('idenum', 25);
            $table->string('value', 10);
            $table->string('name', 255);
            $table->enum('isactive', ['0', '1'])->default('1');
            $table->string('user_create', 50)->nullable();
            $table->string('user_update', 50)->nullable();
            $table->timestamps();
            $table->primary(['idenum', 'value']);
        });

        // transaction_list - Transaction tracking (optional for transc layout)
        Schema::create('transaction_list', function (Blueprint $table) {
            $table->id();
            $table->string('idtrans', 50);
            $table->date('posting');
            $table->string('sloc', 4)->nullable();
            $table->integer('item')->nullable();
            $table->string('material', 100);
            $table->string('batch', 20);
            $table->float('length', 10, 2)->nullable();
            $table->float('width', 10, 2)->nullable();
            $table->float('gsm', 10, 2)->nullable();
            $table->float('weight', 10, 2)->nullable();
            $table->float('qty', 10, 2)->nullable();
            $table->string('uom', 5)->nullable();
            $table->string('color', 20)->nullable();
            $table->enum('tipe', ['I', 'O'])->default('I'); // I=Input, O=Output
            $table->string('user_create', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_list');
        Schema::dropIfExists('sys_enum');
        Schema::dropIfExists('sys_number');
        Schema::dropIfExists('sys_log');
        Schema::dropIfExists('sys_counter');
        Schema::dropIfExists('sys_id');
        Schema::dropIfExists('sys_table');
        Schema::dropIfExists('sys_auth');
        Schema::dropIfExists('sys_dmenu');
        Schema::dropIfExists('sys_gmenu');
        Schema::dropIfExists('sys_roles');
        Schema::dropIfExists('sys_app');
    }
};
