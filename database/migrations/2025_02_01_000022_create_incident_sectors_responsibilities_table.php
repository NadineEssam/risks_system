<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// INCIDENT_SECTORS_RESPONSIBILITIES — قطاعات ومسئوليات الحدث
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_sectors_responsibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
            $table->unsignedInteger('sectors_sec_id');
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->string('created_by', 100)->nullable();

            $table->foreign('sectors_sec_id', 'incident_sector_sec_id_foreign')
                ->references('sec_id')->on('sectors');
            $table->unique(['incident_id', 'sectors_sec_id'], 'incident_sector_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_sectors_responsibilities');
    }
};
