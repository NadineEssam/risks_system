<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CAUSE_CATEGORY — تصنيف الأسباب L2 (المستوى الأعلى لتصنيف أسباب الخطر،
// مستقل عن تصنيف بازل للأحداث EVENT_TYPE/EVENTS، ومرتبط لاحقاً بسجل
// المخاطر المحتملة عبر cause_detail_id على مستوى L4)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cause_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_code', 50)->nullable();
            $table->string('category_name', 255);
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cause_categories');
    }
};
