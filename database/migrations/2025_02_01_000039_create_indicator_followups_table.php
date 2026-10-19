<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// INDICATOR_FOLLOWUP — متابعة المؤشر
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicator_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicators_id')->constrained('indicators');
            $table->foreignId('threshold_level_id')->constrained('threshold_levels');
            $table->date('measurement_date');
            $table->decimal('actual_value', 18, 4);
            $table->text('change_reason')->nullable()
                ->comment('إلزامي إذا كان مستوى الحد لا يساوي "مقبول"');
            $table->text('action_taken')->nullable()
                ->comment('إلزامي إذا كان مستوى الحد لا يساوي "مقبول"');
            $table->text('notes')->nullable();
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicator_followups');
    }
};
