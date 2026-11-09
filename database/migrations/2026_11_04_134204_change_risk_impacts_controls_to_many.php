<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// الأثر والضابط: أكتر من اختيار لكل خطر محتمل (جداول ربط بدل أعمدة)
// السبب: اختيار واحد — بيفضل عمود cause_detail_id على الخطر
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('potential_risk_registers', 'impact_item_id')) {
            Schema::table('potential_risk_registers', function (Blueprint $t) {
                $t->dropForeign('prr_impact_item_fk');
                $t->dropForeign('prr_control_cat_fk');
                $t->dropColumn(['impact_item_id', 'control_category_id']);
            });
        }

        Schema::create('prr_impact_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('potential_risk_register_id')->constrained('potential_risk_registers', 'id', 'prr_impact_risk_fk')->cascadeOnDelete();
            $t->foreignId('impact_item_id')->constrained('impact_items', 'id', 'prr_impact_item_fk2');
            $t->timestamp('creation_date')->nullable()->useCurrent();
            $t->unique(['potential_risk_register_id', 'impact_item_id'], 'prr_impact_unique');
        });

        Schema::create('prr_control_categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('potential_risk_register_id')->constrained('potential_risk_registers', 'id', 'prr_control_risk_fk')->cascadeOnDelete();
            $t->foreignId('control_category_id')->constrained('control_categories', 'id', 'prr_control_cat_fk2');
            $t->timestamp('creation_date')->nullable()->useCurrent();
            $t->unique(['potential_risk_register_id', 'control_category_id'], 'prr_control_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prr_control_categories');
        Schema::dropIfExists('prr_impact_items');
    }
};