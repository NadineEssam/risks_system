<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ACTIVITY_UNIT — وحدة النشاط
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_name', 255);
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_units');
    }
};
