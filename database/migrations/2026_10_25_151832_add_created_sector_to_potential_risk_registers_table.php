<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// قطاع وإدارة من سجّل الخطر — عشان الخطر يظهر لقطاعه في فورم الحدث فوراً
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('potential_risk_registers', function (Blueprint $table) {
            $table->unsignedBigInteger('created_sector_id')->nullable();
            $table->unsignedBigInteger('created_department_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('potential_risk_registers', function (Blueprint $table) {
            $table->dropColumn(['created_sector_id', 'created_department_id']);
        });
    }
};