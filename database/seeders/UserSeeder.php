<?php

namespace Database\Seeders;

use App\Models\Sector;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * مستخدم مدير للنظام + مستخدم نموذجي لكل دور، لتسهيل تجربة كل مسارات
 * العمل مباشرة بعد التثبيت. يُنصح بتغيير كلمات المرور فور أول تشغيل.
 * يعتمد على وجود قطاعات نموذجية مُعدَّة مسبقاً عبر OrganizationSeeder.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = Sector::query()->orderBy('sec_id')->limit(2)->pluck('sec_id');
        $sectorId = $sectors->get(0);
        $secondSectorId = $sectors->get(1, $sectorId);

        $users = [
            [
                'name' => 'مدير النظام',
                'domain_username' => 'nadine.essam',
                'email' => 'admin@msmeda.org.eg',
                'role' => 'super-admin',
                'sector_id' => $sectorId,
                'department_id' => null,
                'job_title' => 'مدير النظام',
            ],
            [
                'name' => 'مسئول المخاطر المركزي',
                'domain_username' => 'risk.officer',
                'email' => 'risk.officer@msmeda.org.eg',
                'role' => 'central-risk-officer',
                'sector_id' => $sectorId,
                'department_id' => null,
                'job_title' => 'مسئول مخاطر بالقطاع المركزي للمخاطر',
            ],
            [
                'name' => 'رئيس قطاع المخاطر الشاملة',
                'domain_username' => 'risk.chief',
                'email' => 'risk.chief@msmeda.org.eg',
                'role' => 'risk-chief',
                'sector_id' => $sectorId,
                'department_id' => null,
                'job_title' => 'رئيس قطاع المخاطر الشاملة',
            ],
            [
                'name' => 'مسئول قطاع تكنولوجيا المعلومات',
                'domain_username' => 'it.officer',
                'email' => 'it.officer@msmeda.org.eg',
                'role' => 'incident-officer',
                'sector_id' => $secondSectorId,
                'department_id' => null,
                'job_title' => 'مسئول مخاطر تشغيلية بقطاع تكنولوجيا المعلومات',
            ],
            [
                'name' => 'مسئول متابعة قطاع تكنولوجيا المعلومات',
                'domain_username' => 'it.followup',
                'email' => 'it.followup@msmeda.org.eg',
                'role' => 'sector-followup-officer',
                'sector_id' => $secondSectorId,
                'department_id' => null,
                'job_title' => 'مسئول القطاع المعني بالمتابعة',
            ],
            [
                'name' => 'مسئول متابعة مؤشرات تكنولوجيا المعلومات',
                'domain_username' => 'it.kri',
                'email' => 'it.kri@msmeda.org.eg',
                'role' => 'indicator-followup-officer',
                'sector_id' => $secondSectorId,
                'department_id' => null,
                'job_title' => 'مسئول مخاطر بالقطاعات المعنية',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password' => Hash::make('Password@123'),
                    'is_active' => true,
                ])
            );

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}