<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// الأسباب: أكتر من سبب لكل خطر محتمل + نسبة مساهمة كل سبب
// السبب القديم (اختيار واحد) بيتنقل بنسبة 100%
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prr_cause_details', function (Blueprint $t) {
            $t->id();
            $t->foreignId('potential_risk_register_id')->constrained('potential_risk_registers', 'id', 'prr_cause_risk_fk')->cascadeOnDelete();
            $t->foreignId('cause_detail_id')->constrained('cause_details', 'id', 'prr_cause_detail_fk2');
            $t->decimal('percentage', 5, 2);
            $t->timestamp('creation_date')->nullable()->useCurrent();
            $t->unique(['potential_risk_register_id', 'cause_detail_id'], 'prr_cause_unique');
        });

        if (Schema::hasColumn('potential_risk_registers', 'cause_detail_id')) {
            // نقل السبب الحالي لكل خطر بنسبة 100%
            DB::table('potential_risk_registers')->whereNotNull('cause_detail_id')->get(['id', 'cause_detail_id'])
                ->each(function ($row) {
                    $row = array_change_key_case((array) $row, CASE_LOWER);
                    DB::table('prr_cause_details')->insert([
                        'potential_risk_register_id' => $row['id'],
                        'cause_detail_id'            => $row['cause_detail_id'],
                        'percentage'                 => 100,
                    ]);
                });

            Schema::table('potential_risk_registers', function (Blueprint $t) {
                $t->dropForeign('prr_cause_detail_fk');
                $t->dropColumn('cause_detail_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('prr_cause_details');
    }
};