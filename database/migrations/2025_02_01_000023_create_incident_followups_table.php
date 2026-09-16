<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// INCIDENT_FOLLOWUP — متابعة الحدث
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_sectors_responsibilities_id')
                ->constrained('incident_sectors_responsibilities')
                ->cascadeOnDelete();
            $table->foreignId('followup_status_id')->constrained('followup_statuses');
            $table->foreignId('followup_entry_type_id')->constrained('followup_entry_types');
            $table->date('followup_date');
            $table->text('entry_text');
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_followups');
    }
};
