<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// القطاعات والإدارات أصبحت تُقرأ من new_po (MySQL) مثل نظام الشكاوى،
// لذا تُحذف الـ FKs المحلية (لا يمكن FK بين Oracle وMySQL) ثم الجداول المحلية.
// الأعمدة نفسها (sector_id / department_id / sectors_sec_id / departments_dep_id) باقية.
return new class extends Migration
{
    public function up(): void
    {
        // كل الـ FKs اللي بتشاور على SECTORS أو DEPARTMENTS — بأي اسم (أوراكل
        // بيقصّ الأسماء لـ 30 حرف) — وآمنة لإعادة التشغيل لو جزء اتنفذ قبل كده.
        $fks = DB::select("
            SELECT c.table_name, c.constraint_name
            FROM user_constraints c
            JOIN user_constraints p ON p.constraint_name = c.r_constraint_name
            WHERE c.constraint_type = 'R'
              AND p.table_name IN ('SECTORS', 'DEPARTMENTS')
        ");

        foreach ($fks as $fk) {
            $fk = array_change_key_case((array) $fk, CASE_LOWER);

            DB::statement(sprintf(
                'ALTER TABLE "%s" DROP CONSTRAINT "%s"',
                $fk['table_name'],
                $fk['constraint_name']
            ));
        }

        Schema::dropIfExists('departments');
        Schema::dropIfExists('sectors');
    }

    public function down(): void
    {
        // لا رجوع — المصدر الوحيد للقطاعات والإدارات هو new_po.
    }
};