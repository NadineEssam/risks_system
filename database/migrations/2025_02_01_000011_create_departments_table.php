<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// DEPARTMENTS — الإدارات
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('dep_id');
            $table->string('sector_code', 50);
            $table->string('dep_code', 50)->unique();
            $table->string('depname_en')->nullable();
            $table->string('depname_ar');
            $table->string('create_user_id', 100)->nullable();
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->string('update_user_id', 100)->nullable();
            $table->timestamp('update_date')->nullable();
            $table->string('fk_govt_code', 10)->nullable();
            $table->string('fk_off_code', 10)->nullable();
            $table->boolean('validity')->default(true);

            $table->foreign('sector_code', 'departments_sector_code_foreign')
                ->references('sector_code')->on('sectors');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
