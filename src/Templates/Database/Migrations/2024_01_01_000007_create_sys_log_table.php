<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sys_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->default(now());
            $table->string('username', 20);
            $table->char('type', 1);
            $table->string('dmenu');
            $table->string('message');
            $table->enum('status', [0, 1])->nullable();
            $table->string('ipaddress')->nullable();
            $table->string('useragent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_log');
    }
};
