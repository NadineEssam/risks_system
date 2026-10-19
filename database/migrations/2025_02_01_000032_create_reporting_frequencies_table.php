<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// REPORTING_FREQUENCY — دورية الإبلاغ (شهري / ربع سنوي / نصف سنوي / سنوي)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporting_frequencies', function (Blueprint $table) {
            $table->id();
            $table->string('frequency_name', 255);
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporting_frequencies');
    }
};
