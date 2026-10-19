<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// CAUSE_SUBCATEGORY — تصنيف الأسباب L3
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cause_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cause_category_id')
                ->constrained('cause_categories', 'id', 'cause_subcat_category_id_fk');
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
        Schema::dropIfExists('cause_subcategories');
    }
};
