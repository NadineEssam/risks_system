<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// القطاع اللي أدخل المؤشر (من PO) — والقديم بيتملى من اسم اللي أضافه
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indicators', fn (Blueprint $t) => $t->unsignedBigInteger('created_sector_id')->nullable());

        foreach (DB::table('indicators')->whereNull('created_sector_id')->get(['id', 'created_by']) as $row) {
            $row = array_change_key_case((array) $row, CASE_LOWER);
            $user = User::where('name', $row['created_by'])->first();
            $sectorId = $user?->department?->sector?->sec_id ?? $user?->sector_id;

            if ($sectorId) {
                DB::table('indicators')->where('id', $row['id'])->update(['created_sector_id' => $sectorId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('indicators', fn (Blueprint $t) => $t->dropColumn('created_sector_id'));
    }
};