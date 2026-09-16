<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// INCIDENT — الحدث
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potential_risk_register_id')->constrained('potential_risk_registers');
            $table->foreignId('resolution_status_id')->nullable()->constrained('resolution_statuses');
            $table->unsignedInteger('departments_dep_id');
            $table->date('start_date')->nullable();
            $table->date('discovery_date')->comment('تاريخ اكتشاف المشكلة');
            $table->unsignedTinyInteger('impact_score')->comment('1-5');
            $table->unsignedTinyInteger('frequency_score')
                ->comment('عدد مرات التكرار المحتسب على مستوى القطاع، بحد أقصى 5');
            $table->unsignedSmallInteger('risk_degree')->comment('impact_score x frequency_score');
            $table->text('description')->nullable();
            $table->text('current_procedure')->nullable();
            $table->text('proposed_procedure')->nullable();
            $table->text('actual_impact_problem')->nullable();

            // حقول Legacy موروثة من النظام القديم - غير مستخدمة فعلياً
            $table->integer('fk_central_sector_id')->nullable()->comment('legacy relationship, not used');
            $table->integer('fk_unit_id')->nullable()->comment('legacy relationship, not used');
            $table->integer('fk_internal_event')->nullable()->comment('legacy relationship, not used');
            $table->integer('fk_external_event')->nullable()->comment('legacy relationship, not used');
            $table->string('fk_govt_code', 10)->nullable()->comment('legacy relationship, not used');
            $table->string('fk_off_code', 10)->nullable()->comment('legacy relationship, not used');
            $table->integer('fk_problem_size')->nullable()->comment('legacy relationship, not used');

            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);

            $table->foreign('departments_dep_id', 'incidents_dep_id_foreign')
                ->references('dep_id')->on('departments');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
