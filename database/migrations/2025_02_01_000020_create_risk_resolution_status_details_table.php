<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// RISK_STATUS_DETAILS — تفاصيل حالة حل الخطر
// (سجل تاريخي لكل حالة اعتمدت على الخطر المحتمل عبر الزمن)
//
// ملاحظة: الاسم القديم "risk_resolution_status_details" كان 30 حرفًا بالضبط،
// وهو ما يسبب في أوراكل تضاربًا حتميًا: درايفر yajra/laravel-oci8 يبني اسم
// الـ sequence الخاص بالـ auto-increment بإضافة لاحقة ثم يقصّه إلى 30 حرفًا
// (حد أوراكل القديم)، فتُقتَطع اللاحقة بالكامل ويصبح اسم الـ sequence مطابقًا
// تمامًا لاسم الجدول نفسه → ORA-00955 عند كل create sequence. لذلك تم تقصير
// الاسم هنا؛ يُفضّل مستقبلاً إبقاء أسماء الجداول الطويلة أقل من 25 حرفًا.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_status_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potential_risk_register_id')
                ->constrained('potential_risk_registers', 'id', 'risk_status_details_prr_id_fk')
                ->cascadeOnDelete();
            $table->foreignId('resolution_status_id')->constrained('resolution_statuses');
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->string('created_by', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_status_details');
    }
};
