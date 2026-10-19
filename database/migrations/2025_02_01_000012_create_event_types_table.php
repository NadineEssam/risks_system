<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// EVENT_TYPE — تصنيف بازل العام
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 255);
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_types');
    }
};
