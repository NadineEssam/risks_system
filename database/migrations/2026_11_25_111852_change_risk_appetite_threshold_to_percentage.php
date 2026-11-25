<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// حدود شهية المخاطر: نسبة مئوية (0 - 100) بكسرين عشريين بدل درجة 1-25
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE RISK_APPETITE_DETAILS MODIFY (THRESHOLD_VALUE NUMBER(5,2))');
    }

    public function down(): void
    {
        // تكبير الدقة مش بيترجع بأمان لو فيه كسور — بنسيبه زي ما هو
    }
};