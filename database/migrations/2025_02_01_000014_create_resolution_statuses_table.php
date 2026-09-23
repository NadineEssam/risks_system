<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// RESOLUTION_STATUS — حالة حل الحدث (حل كلي / حل جزئي / غير مقبول / مقبول)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resolution_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_name', 255);
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resolution_statuses');
    }
};
