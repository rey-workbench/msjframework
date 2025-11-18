<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
            $table->enum('primary', [0, 1, 2])->default(0);
            $table->string('generateid', 25)->nullable();
            $table->enum('filter', [0, 1])->default(0);
            $table->enum('list', [0, 1])->default(1);
            $table->enum('show', [0, 1])->default(1);
            $table->longText('query')->nullable();
            $table->string('class', 255)->nullable();
            $table->string('sub', 255)->nullable();
            $table->string('link', 50)->nullable();
            $table->string('note', 255)->nullable();
            $table->enum('position', [0, 1, 2, 3, 4])->default(0);
            $table->enum('isactive', [0, 1])->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->primary(['gmenu', 'dmenu', 'urut']);
            $table->foreign('gmenu')->references('gmenu')->on('sys_gmenu')->onDelete('cascade');
            $table->foreign('dmenu')->references('dmenu')->on('sys_dmenu')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_table');
    }
};
