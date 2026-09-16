<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// INDICATORS — المؤشرات
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potential_risk_register_id')->constrained('potential_risk_registers');
            $table->foreignId('indicator_nature_id')->constrained('indicator_natures');
            $table->foreignId('measurement_unit_id')->constrained('measurement_units');
            $table->foreignId('reporting_frequency_id')->constrained('reporting_frequencies');
            $table->foreignId('activity_unit_id')->constrained('activity_units');
            $table->text('indicator_name');
            $table->text('related_actions')->nullable();
            $table->text('data_sources')->nullable();
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            // "يُحفظ المؤشر بالحالة (مفعل) تلقائياً" — نستخدم validity كحقل التفعيل.
            $table->boolean('validity')->default(true)->comment('مفعل تلقائياً عند الحفظ');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicators');
    }
};
