<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// RESPONSIBLE_ROLE — دور المسئول (متابعة وإبلاغ / اعتماد المؤشر)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responsible_roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 255);
            $table->timestamp('creation_date')->nullable()->useCurrent();
            $table->timestamp('update_date')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->boolean('validity')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responsible_roles');
    }
};
