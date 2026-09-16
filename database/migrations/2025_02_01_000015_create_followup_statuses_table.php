<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FOLLOWUP_STATUS — حالة المتابعة (تم الحل / إغلاق / جارى المتابعة / قبول الخطر)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followup_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_name', 255);
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followup_statuses');
    }
};
