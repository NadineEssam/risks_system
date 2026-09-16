<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Sector;
use Illuminate\Database\Seeder;

/**
 * بيانات مرجعية نموذجية للقطاعات والإدارات، لتسهيل تجربة النظام مباشرة
 * بعد التثبيت. يمكن لمدير النظام إضافة/تعديل القطاعات والإدارات الفعلية
 * لاحقاً من شاشتي "القطاعات" و"الإدارات" تحت "البيانات المرجعية".
 */
class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [
            ['sector_code' => 'SEC-RISK', 'sector_ar' => 'قطاع المخاطر الشاملة', 'sector_en' => 'Enterprise Risk Sector'],
            ['sector_code' => 'SEC-IT', 'sector_ar' => 'قطاع تكنولوجيا المعلومات', 'sector_en' => 'IT Sector'],
        ];

        foreach ($sectors as $data) {
            Sector::firstOrCreate(['sector_code' => $data['sector_code']], $data);
        }

        $departments = [
            ['sector_code' => 'SEC-RISK', 'dep_code' => 'DEP-RISK-CENTRAL', 'depname_ar' => 'إدارة المخاطر المركزية', 'depname_en' => 'Central Risk Management'],
            ['sector_code' => 'SEC-IT', 'dep_code' => 'DEP-IT-INFRA', 'depname_ar' => 'إدارة البنية التحتية', 'depname_en' => 'IT Infrastructure'],
            ['sector_code' => 'SEC-IT', 'dep_code' => 'DEP-IT-APPS', 'depname_ar' => 'إدارة التطبيقات', 'depname_en' => 'Applications'],
        ];

        foreach ($departments as $data) {
            Department::firstOrCreate(['dep_code' => $data['dep_code']], $data);
        }
    }
}
