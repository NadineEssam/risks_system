<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// تصنيفات ERM: أكواد الأحداث + الأثر + الضوابط + ربطها بالخطر المحتمل
// + تقسيم حالات الخطر إلى (قبول الخطر / معالجة الخطر)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_types', fn (Blueprint $t) => $t->string('type_code', 20)->nullable());
        Schema::table('events', fn (Blueprint $t) => $t->string('event_code', 20)->nullable());

        // الأثر: الفئة (L2) ← الأثر (L3) — وطبيعة الأثر (L1) خاصية على كل أثر
        Schema::create('impact_categories', function (Blueprint $t) {
            $t->id();
            $t->string('category_code', 20)->nullable();
            $t->string('category_name', 255);
            $t->timestamp('creation_date')->nullable()->useCurrent();
            $t->timestamp('update_date')->nullable();
            $t->string('created_by', 100)->nullable();
            $t->string('updated_by', 100)->nullable();
            $t->boolean('validity')->default(true);
        });

        Schema::create('impact_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('impact_category_id')->constrained('impact_categories', 'id', 'impact_item_category_fk');
            $t->string('item_code', 20)->nullable();
            $t->string('item_name', 255);
            $t->string('nature_code', 20)->nullable();
            $t->string('nature_name', 100)->nullable();
            $t->timestamp('creation_date')->nullable()->useCurrent();
            $t->timestamp('update_date')->nullable();
            $t->string('created_by', 100)->nullable();
            $t->string('updated_by', 100)->nullable();
            $t->boolean('validity')->default(true);
        });

        // الضوابط: المجال (L2) ← الفئة (L3) — و L1 دايماً "الضوابط الرقابية"
        Schema::create('control_domains', function (Blueprint $t) {
            $t->id();
            $t->string('domain_code', 20)->nullable();
            $t->string('domain_name', 255);
            $t->timestamp('creation_date')->nullable()->useCurrent();
            $t->timestamp('update_date')->nullable();
            $t->string('created_by', 100)->nullable();
            $t->string('updated_by', 100)->nullable();
            $t->boolean('validity')->default(true);
        });

        Schema::create('control_categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('control_domain_id')->constrained('control_domains', 'id', 'control_cat_domain_fk');
            $t->string('category_code', 20)->nullable();
            $t->string('category_name', 255);
            $t->timestamp('creation_date')->nullable()->useCurrent();
            $t->timestamp('update_date')->nullable();
            $t->string('created_by', 100)->nullable();
            $t->string('updated_by', 100)->nullable();
            $t->boolean('validity')->default(true);
        });

        // الخطر المحتمل: السبب + الأثر + فئة الضابط (nullable عشان المخاطر القديمة)
        Schema::table('potential_risk_registers', function (Blueprint $t) {
            $t->foreignId('cause_detail_id')->nullable()->constrained('cause_details', 'id', 'prr_cause_detail_fk');
            $t->foreignId('impact_item_id')->nullable()->constrained('impact_items', 'id', 'prr_impact_item_fk');
            $t->foreignId('control_category_id')->nullable()->constrained('control_categories', 'id', 'prr_control_cat_fk');
        });

        // تقسيم الحالات: قبول الخطر (مقبول/غير مقبول) — معالجة الخطر (حل كلي/حل جزئي)
        Schema::table('resolution_statuses', fn (Blueprint $t) => $t->string('status_group', 20)->nullable());
        DB::table('resolution_statuses')->whereIn('status_name', ['مقبول', 'غير مقبول'])->update(['status_group' => 'acceptance']);
        DB::table('resolution_statuses')->whereIn('status_name', ['حل كلي', 'حل جزئي'])->update(['status_group' => 'treatment']);
    }

    public function down(): void
    {
        Schema::table('potential_risk_registers', function (Blueprint $t) {
            $t->dropForeign('prr_cause_detail_fk');
            $t->dropForeign('prr_impact_item_fk');
            $t->dropForeign('prr_control_cat_fk');
            $t->dropColumn(['cause_detail_id', 'impact_item_id', 'control_category_id']);
        });
        Schema::dropIfExists('control_categories');
        Schema::dropIfExists('control_domains');
        Schema::dropIfExists('impact_items');
        Schema::dropIfExists('impact_categories');
        Schema::table('events', fn (Blueprint $t) => $t->dropColumn('event_code'));
        Schema::table('event_types', fn (Blueprint $t) => $t->dropColumn('type_code'));
        Schema::table('resolution_statuses', fn (Blueprint $t) => $t->dropColumn('status_group'));
    }
};