<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// REQUIRED_ACTIONS — الإجراءات المطلوبة (لكل قطاع مسؤول عن خطر محتمل)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('required_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potential_risk_register_sector_details_id')
                ->constrained('potential_risk_register_sector_details', 'id', 'required_actions_prr_sector_detail_id_foreign')
                ->cascadeOnDelete();
            $table->text('required_action');
            $table->date('expiration_date')->nullable();
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('required_actions');
    }
};
