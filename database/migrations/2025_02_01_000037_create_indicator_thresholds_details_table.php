<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// INDICATOR_THRESHOLDS_DETAILS — تفاصيل حدود المؤشر
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicator_thresholds_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicators_id')->constrained('indicators')->cascadeOnDelete();
            $table->foreignId('threshold_level_id')->constrained('threshold_levels');
            $table->decimal('threshold_value', 18, 4);
            $table->text('required_action')->nullable()
                ->comment('إجراء إلزامي عند تجاوز المستوى (متوسط/مرتفع)');
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);

            $table->unique(['indicators_id', 'threshold_level_id'], 'indicator_threshold_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicator_thresholds_details');
    }
};
