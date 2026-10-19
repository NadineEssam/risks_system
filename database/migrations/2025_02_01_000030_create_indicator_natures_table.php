<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// INDICATOR_NATURE — طبيعة المؤشر (متزايد / متناقص)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicator_natures', function (Blueprint $table) {
            $table->id();
            $table->string('nature_name', 255);
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicator_natures');
    }
};
