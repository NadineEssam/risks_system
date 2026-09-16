<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// THRESHOLD_LEVEL — مستوى الحد (مقبول / متوسط / مرتفع)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threshold_levels', function (Blueprint $table) {
            $table->id();
            $table->string('level_name', 255);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threshold_levels');
    }
};
