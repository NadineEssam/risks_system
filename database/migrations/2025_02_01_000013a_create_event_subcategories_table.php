<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// EVENT_SUBCATEGORY — تصنيف بازل الفرعي L3 (بين "تصنيف بازل التفصيلي" وسجل
// المخاطر المحتملة، لدعم تصنيف أدق لنوع الحدث)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('events_id')->constrained('events');
            $table->string('subcategory_code', 50)->nullable();
            $table->string('subcategory_name', 255);
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_subcategories');
    }
};
