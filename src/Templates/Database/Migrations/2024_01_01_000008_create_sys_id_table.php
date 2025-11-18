<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sys_id', function (Blueprint $table) {
            $table->char('dmenu', 6);
            $table->enum('source', ['int', 'ext', 'cnt', 'th2', 'th4', 'bln', 'tgl'])->default('ext');
            $table->string('internal', 255)->default('-');
            $table->string('external', 255)->default('0');
            $table->integer('urut');
            $table->integer('length');
            $table->enum('isactive', [0, 1])->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->primary(['dmenu', 'source', 'internal', 'external']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_id');
    }
};
