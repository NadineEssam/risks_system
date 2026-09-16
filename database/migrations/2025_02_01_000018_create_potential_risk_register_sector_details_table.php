<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// POTENTIAL_RISK_REGISTER_SECTOR_DETAILS — تفاصيل قطاعات الخطر
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('potential_risk_register_sector_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potential_risk_register_id')
                ->constrained('potential_risk_registers', 'id', 'prr_sector_details_prr_id_foreign')
                ->cascadeOnDelete();
            $table->unsignedInteger('sectors_sec_id');
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->string('created_by', 100)->nullable();

            $table->foreign('sectors_sec_id', 'prr_sector_sec_id_foreign')
                ->references('sec_id')->on('sectors');
            $table->unique(['potential_risk_register_id', 'sectors_sec_id'], 'prr_sector_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potential_risk_register_sector_details');
    }
};
