<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * أدوار وصلاحيات النظام، مبنية على مجموعات المستخدمين الواردة في
 * وثيقة المتطلبات لكل مرحلة من مراحل مسارات العمل الخمس.
 *
 * كل صلاحية لها تجميع (group/group_ar) واسم عربي (ar_name) لعرضها في شاشة
 * "الأدوار والصلاحيات" كمصفوفة اختيارات مجمّعة بنفس أسلوب "نظام خدمة
 * العملاء" — راجع resources/views/admin/roles/_permissions_table.blade.php.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // المرحلة الأولى - سجل المخاطر المحتملة
            'view-risks' => ['group' => 'risks', 'group_ar' => 'سجل المخاطر المحتملة', 'ar_name' => 'عرض المخاطر المحتملة'],
            'create-risks' => ['group' => 'risks', 'group_ar' => 'سجل المخاطر المحتملة', 'ar_name' => 'إضافة خطر محتمل'],
            'edit-risks' => ['group' => 'risks', 'group_ar' => 'سجل المخاطر المحتملة', 'ar_name' => 'تعديل خطر محتمل'],
            'approve-risks' => ['group' => 'risks', 'group_ar' => 'سجل المخاطر المحتملة', 'ar_name' => 'اعتماد حالة حل الخطر'],

            // المرحلة الثانية - الحدث
            'view-incidents' => ['group' => 'incidents', 'group_ar' => 'الأحداث التشغيلية', 'ar_name' => 'عرض الأحداث'],
            'create-incidents' => ['group' => 'incidents', 'group_ar' => 'الأحداث التشغيلية', 'ar_name' => 'تسجيل حدث جديد'],
            'edit-incidents' => ['group' => 'incidents', 'group_ar' => 'الأحداث التشغيلية', 'ar_name' => 'تعديل حدث'],

            // المرحلة الثالثة - متابعة الحدث
            'view-incident-followups' => ['group' => 'incident-followups', 'group_ar' => 'متابعة الأحداث', 'ar_name' => 'عرض متابعات الأحداث'],
            'create-incident-followups' => ['group' => 'incident-followups', 'group_ar' => 'متابعة الأحداث', 'ar_name' => 'تسجيل متابعة حدث'],
            'decide-incident-followups' => ['group' => 'incident-followups', 'group_ar' => 'متابعة الأحداث', 'ar_name' => 'اعتماد قرار متابعة الحدث'],

            // المرحلة الرابعة - المؤشر (KRI)
            'view-indicators' => ['group' => 'indicators', 'group_ar' => 'مؤشرات قياس المخاطر (KRI)', 'ar_name' => 'عرض المؤشرات'],
            'create-indicators' => ['group' => 'indicators', 'group_ar' => 'مؤشرات قياس المخاطر (KRI)', 'ar_name' => 'إضافة مؤشر جديد'],
            'edit-indicators' => ['group' => 'indicators', 'group_ar' => 'مؤشرات قياس المخاطر (KRI)', 'ar_name' => 'تعديل مؤشر'],

            // المرحلة الخامسة - متابعة المؤشر
            'view-indicator-followups' => ['group' => 'indicator-followups', 'group_ar' => 'متابعة المؤشرات', 'ar_name' => 'عرض متابعات المؤشرات'],
            'create-indicator-followups' => ['group' => 'indicator-followups', 'group_ar' => 'متابعة المؤشرات', 'ar_name' => 'تسجيل متابعة مؤشر'],

            // عام
            'view-reports' => ['group' => 'general', 'group_ar' => 'عام', 'ar_name' => 'عرض التقارير'],
            'manage-lookups' => ['group' => 'general', 'group_ar' => 'عام', 'ar_name' => 'إدارة البيانات المرجعية'],
            'manage-users' => ['group' => 'general', 'group_ar' => 'عام', 'ar_name' => 'إدارة المستخدمين والأدوار'],
        ];

        foreach ($permissions as $name => $meta) {
            Permission::updateOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                $meta
            );
        }

        $permissionNames = array_keys($permissions);

        $roles = [
            // مدير النظام: كل الصلاحيات (ويُمنح تجاوزاً كاملاً أيضاً عبر Gate::before)
            'super-admin' => $permissionNames,

            // مسئولو المخاطر بالقطاع المركزي للمخاطر
            'central-risk-officer' => [
                'view-risks', 'create-risks', 'edit-risks',
                'view-indicators', 'create-indicators', 'edit-indicators',
                'view-reports',
            ],

            // رئيس قطاع المخاطر الشاملة
            'risk-chief' => [
                'view-risks', 'approve-risks',
                'view-incident-followups', 'decide-incident-followups',
                'view-reports',
            ],

            // مسئولو القطاعات المختلفة (استعراض الإجراءات المطلوبة)
            'sector-officer' => [
                'view-risks', 'view-reports',
            ],

            // مسئولو المخاطر التشغيلية بالقطاعات
            'incident-officer' => [
                'view-risks', 'view-incidents', 'create-incidents', 'edit-incidents', 'view-reports',
            ],

            // مسئول القطاع المعني (متابعة الحدث)
            'sector-followup-officer' => [
                'view-incidents', 'view-incident-followups', 'create-incident-followups', 'view-reports',
            ],

            // مسئولو المخاطر بالقطاعات المعنية (متابعة المؤشرات)
            'indicator-followup-officer' => [
                'view-indicators', 'view-indicator-followups', 'create-indicator-followups', 'view-reports',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
