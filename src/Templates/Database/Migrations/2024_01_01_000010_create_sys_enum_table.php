<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sys_enum', function (Blueprint $table) {
            $table->string('idenum', 25);
            $table->string('value', 10);
            $table->string('name', 255);
            $table->enum('isactive', [0, 1])->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
            $table->primary(['idenum', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_enum');
    }
};
