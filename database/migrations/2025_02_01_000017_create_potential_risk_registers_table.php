<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// POTENTIAL_RISK_REGISTER — سجل المخاطر المحتملة
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('potential_risk_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_detail_id')->constrained('event_details');
            $table->text('risk_description');
            $table->text('proposed_control')->nullable();
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potential_risk_registers');
    }
};
