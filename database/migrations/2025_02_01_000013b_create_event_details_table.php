<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// EVENT_DETAIL — تصنيف بازل الدقيق L4 (أكثر مستوى تفصيلاً في تصنيف بازل،
// وهو المستوى الذي يُربط به سجل المخاطر المحتملة فعلياً)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_subcategory_id')->constrained('event_subcategories');
            $table->string('detail_code', 50)->nullable();
            $table->string('detail_name', 255);
            $table->text('bank_example')->nullable()->comment('مثال مصرفي توضيحي لهذا التصنيف');
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_details');
    }
};
