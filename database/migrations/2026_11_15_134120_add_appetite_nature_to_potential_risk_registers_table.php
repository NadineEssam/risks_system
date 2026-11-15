<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// طبيعة شهية المخاطر: متزايد / متناقص (نفس قائمة طبيعة المؤشر) — فاضية = متزايد
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('potential_risk_registers', function (Blueprint $t) {
            $t->foreignId('appetite_nature_id')->nullable()->constrained('indicator_natures', 'id', 'prr_appetite_nature_fk');
        });
    }

    public function down(): void
    {
        Schema::table('potential_risk_registers', function (Blueprint $t) {
            $t->dropForeign('prr_appetite_nature_fk');
            $t->dropColumn('appetite_nature_id');
        });
    }
};