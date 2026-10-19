<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// ربط جدول users بالقطاعات والإدارات المحلية — يعمل بعد إنشاء جدولي
// sectors وdepartments (الهجرتان 000010 و000011).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('sector_id', 'users_sector_id_foreign')
                ->references('sec_id')->on('sectors')->nullOnDelete();
            $table->foreign('department_id', 'users_department_id_foreign')
                ->references('dep_id')->on('departments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_sector_id_foreign');
            $table->dropForeign('users_department_id_foreign');
        });
    }
};
