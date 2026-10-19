<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// تاريخ الاعتماد: تاريخ الاتفاق مع القطاع على توثيق المؤشر — بيتدخل مرة واحدة
// وعليه بتتحسب تواريخ الاستحقاق حسب دورية الإبلاغ (تقرير الانتظام)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indicators', function (Blueprint $table) {
            $table->date('approval_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('indicators', function (Blueprint $table) {
            $table->dropColumn('approval_date');
        });
    }
};