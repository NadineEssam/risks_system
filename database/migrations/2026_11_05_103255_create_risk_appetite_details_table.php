<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// شهية المخاطر: لكل خطر محتمل حد لكل مستوى (مقبول/متوسط/مرتفع) على درجة الخطر (1-25)
// + الإجراء المطلوب عند الوصول للمستوى
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_appetite_details', function (Blueprint $t) {
            $t->id();
            $t->foreignId('potential_risk_register_id')->constrained('potential_risk_registers', 'id', 'appetite_risk_fk')->cascadeOnDelete();
            $t->foreignId('threshold_level_id')->constrained('threshold_levels', 'id', 'appetite_level_fk');
            $t->unsignedTinyInteger('threshold_value');
            $t->text('required_action')->nullable();
            $t->timestamp('creation_date')->nullable()->useCurrent();
            $t->timestamp('update_date')->nullable();
            $t->string('created_by', 100)->nullable();
            $t->string('updated_by', 100)->nullable();
            $t->unique(['potential_risk_register_id', 'threshold_level_id'], 'appetite_risk_level_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_appetite_details');
    }
};