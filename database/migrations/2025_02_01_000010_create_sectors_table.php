<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// SECTORS — القطاعات الإدارية
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sectors', function (Blueprint $table) {
            $table->increments('sec_id');
            $table->string('sector_code', 50)->unique();
            $table->string('sector_ar');
            $table->string('sector_en')->nullable();
            $table->string('fk_govt_code', 10)->nullable();
            $table->string('fk_off_code', 10)->nullable();
            $table->string('create_user_id', 100)->nullable();
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->string('update_user_id', 100)->nullable();
            $table->timestamp('update_date')->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sectors');
    }
};
