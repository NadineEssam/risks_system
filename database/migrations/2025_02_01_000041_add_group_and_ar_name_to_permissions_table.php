<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// إضافة تجميع/تسمية عربية للصلاحيات — بنفس فكرة "نظام خدمة العملاء" —
// لعرضها في شاشة "الأدوار والصلاحيات" كمصفوفة اختيارات مجمّعة بدلاً من
// قائمة أسماء تقنية غير مفهومة للمستخدم غير التقني.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('group', 100)->nullable()->after('guard_name');
            $table->string('group_ar', 150)->nullable()->after('group');
            $table->string('ar_name', 255)->nullable()->after('group_ar');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['group', 'group_ar', 'ar_name']);
        });
    }
};
