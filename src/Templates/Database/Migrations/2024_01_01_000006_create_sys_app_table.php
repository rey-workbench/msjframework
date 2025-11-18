<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
            $table->enum('isactive', [0, 1])->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_app');
    }
};
