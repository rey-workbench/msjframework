<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sys_dmenu', function (Blueprint $table) {
            $table->char('dmenu', 6)->primary();
            $table->char('gmenu', 6);
            $table->integer('urut');
            $table->string('name', 25)->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('url', 50)->nullable();
            $table->string('tabel', 50)->nullable();
            $table->char('layout', 6)->default('master');
            $table->char('sub', 6)->nullable();
            $table->enum('show', [0, 1])->default(1);
            $table->enum('js', [0, 1])->default(0);
            $table->text('where')->nullable();
            $table->enum('isactive', [0, 1])->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->foreign('gmenu')->references('gmenu')->on('sys_gmenu')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_dmenu');
    }
};
