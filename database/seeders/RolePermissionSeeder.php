<?php

namespace Database\Seeders;

use App\Support\PermissionRegistry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * الصلاحيات تُبنى تلقائياً من أسماء المسارات (نفس نظام الشكاوى) مع
 * group / group_ar / ar_name — شغّليه تاني بعد إضافة أي مسارات جديدة.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = PermissionRegistry::all();

        foreach ($permissions as $name => $meta) {
            Permission::updateOrCreate(['name' => $name, 'guard_name' => 'web'], $meta);
        }

        // حذف الصلاحيات القديمة (risks.index, risks.create, ...) اللي مبقتش مسارات
        Permission::where('guard_name', 'web')
            ->whereNotIn('name', array_keys($permissions))
            ->get()
            ->each->delete();

        $all  = array_keys($permissions);
        $pick = fn (array $patterns) => array_values(array_filter($all, fn ($p) => Str::is($patterns, $p)));

        $roles = [
            'super-admin' => ['*'],

            // مسئولو المخاطر بالقطاع المركزي للمخاطر
            'central-risk-officer' => [
                'risks.index', 'risks.show', 'risks.create', 'risks.edit',
                'indicators.*', 'reports.index',
            ],

            // رئيس قطاع المخاطر الشاملة
            'risk-chief' => [
                'risks.index', 'risks.show', 'risks.status.update',
                'incident-followups.index', 'incident-followups.decide',
                'reports.index',
            ],

            // مسئولو القطاعات المختلفة
            'sector-officer' => ['risks.index', 'risks.show', 'reports.index'],

            // مسئولو المخاطر التشغيلية بالقطاعات
            'incident-officer' => ['risks.index', 'risks.show', 'incidents.*', 'reports.index'],

            // مسئول القطاع المعني (متابعة الحدث)
            'sector-followup-officer' => [
                'incidents.index', 'incidents.show',
                'incident-followups.index', 'incident-followups.create',
                'reports.index',
            ],

            // مسئولو المخاطر بالقطاعات المعنية (متابعة المؤشرات)
            'indicator-followup-officer' => [
                'indicators.index', 'indicators.show', 'indicator-followups.*', 'reports.index',
            ],
        ];

        foreach ($roles as $roleName => $patterns) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web'])
                ->syncPermissions($pick($patterns));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}