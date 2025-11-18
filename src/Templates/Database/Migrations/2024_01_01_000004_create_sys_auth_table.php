<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
            $table->enum('isactive', [0, 1])->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->primary(['dmenu', 'idroles']);
            $table->foreign('idroles')->references('idroles')->on('sys_roles')->onDelete('cascade');
            $table->foreign('dmenu')->references('dmenu')->on('sys_dmenu')->onDelete('cascade');
            $table->foreign('gmenu')->references('gmenu')->on('sys_gmenu')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_auth');
    }
};
