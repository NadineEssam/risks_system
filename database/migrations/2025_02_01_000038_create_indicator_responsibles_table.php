<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// INDICATOR_RESPONSIBLES — مسئولو المؤشر
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicator_responsibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicators_id')->constrained('indicators')->cascadeOnDelete();
            $table->foreignId('responsible_role_id')->constrained('responsible_roles');
            $table->string('full_name', 255);
            $table->string('job_title', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicator_responsibles');
    }
};
