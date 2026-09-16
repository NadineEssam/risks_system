<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FOLLOWUP_ENTRY_TYPE — نوع إدخال المتابعة (توصية / رد / رأى)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followup_entry_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 255);
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followup_entry_types');
    }
};
