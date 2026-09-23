<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// CAUSE_DETAIL — تصنيف الأسباب L4 (السبب الجذري)، وهو المستوى الذي يُربط
// به سجل المخاطر المحتملة فعلياً عبر potential_risk_registers.cause_detail_id
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cause_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cause_subcategory_id')
                ->constrained('cause_subcategories', 'id', 'cause_detail_subcat_id_fk');
            $table->string('detail_code', 50)->nullable();
            $table->string('detail_name', 255);
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cause_details');
    }
};
