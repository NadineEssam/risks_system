<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * الصلاحيات = أسماء المسارات (نفس فكرة نظام الشكاوى).
 * مصدر واحد يستخدمه: CheckRoutePermission + PerUser() + RolePermissionSeeder.
 */
class PermissionRegistry
{
    /** مسارات متاحة لأي مستخدم مسجّل دخول */
    public const EXEMPT = ['login', 'logout', 'dashboard'];

    /** مسار => الصلاحية اللي بيستخدمها */
    public const ALIASES = [
        'risks.toggle'               => 'risks.edit',
        'risks.sectors.store'        => 'risks.edit',
        'risks.sectors.destroy'      => 'risks.edit',
        'risk-sectors.actions.store' => 'risks.edit',
        'required-actions.destroy'   => 'risks.edit',
    ];

    /** آخر جزء في اسم المسار => الصلاحية المقابلة (لو المسار ده موجود) */
    public const SUFFIX_ALIASES = [
        'store'  => 'create',
        'update' => 'edit',
        'data'   => 'index',
    ];

    /** صلاحيات منطق عمل مش مسارات */
    public const EXTRA = ['incident-followups.decide'];

    public const GROUPS = [
        'risks'               => 'سجل المخاطر المحتملة',
        'incidents'           => 'الأحداث التشغيلية',
        'incident-followups'  => 'متابعة الأحداث',
        'indicators'          => 'مؤشرات قياس المخاطر (KRI)',
        'indicator-followups' => 'متابعة المؤشرات',
        'reports'             => 'التقارير',
        'users'               => 'المستخدمون',
        'roles'               => 'الأدوار والصلاحيات',
        'events'              => 'تصنيف بازل التفصيلي',
        'event-subcategories' => 'تصنيف بازل الفرعي',
        'event-details'       => 'تصنيف بازل الدقيق',
        'lookups'             => 'البيانات المرجعية العامة',
    ];

    public const ACTIONS = [
        'index'                   => 'عرض القائمة',
        'show'                    => 'عرض التفاصيل',
        'create'                  => 'إضافة',
        'store'                   => 'إضافة',
        'edit'                    => 'تعديل',
        'update'                  => 'تعديل',
        'destroy'                 => 'حذف',
        'toggle'                  => 'تفعيل / تعطيل',
        'status.update'           => 'تغيير الحالة',
        'roles.update'            => 'تعديل أدوار المستخدم',
        'sectorDepartment.update' => 'تعديل القطاع / الإدارة',
    ];

    /** أسماء عربية خاصة تغلب على ACTIONS */
    public const LABELS = [
        'risks.status.update'       => 'اعتماد حالة حل الخطر',
        'incidents.status.update'   => 'تحديث حالة الحدث',
        'incident-followups.decide' => 'اعتماد قرار متابعة الحدث',
    ];

    public static function isExempt(string $routeName): bool
    {
        return in_array($routeName, self::EXEMPT, true);
    }

    public static function resolve(string $routeName): string
    {
        if (isset(self::ALIASES[$routeName])) {
            return self::ALIASES[$routeName];
        }

        $suffix = Str::afterLast($routeName, '.');

        if (isset(self::SUFFIX_ALIASES[$suffix])) {
            $target = Str::beforeLast($routeName, '.').'.'.self::SUFFIX_ALIASES[$suffix];

            if (Route::has($target)) {
                return $target;
            }
        }

        return $routeName;
    }

    /** [name => [group, group_ar, ar_name]] بترتيب المسارات */
    public static function all(): array
    {
        $names = [];

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();

            if (! $name || self::isExempt($name) || ! in_array('auth', $route->gatherMiddleware(), true)) {
                continue;
            }

            $names[self::resolve($name)] = true;
        }

        foreach (self::EXTRA as $extra) {
            $names[$extra] = true;
        }

        $result = [];
        foreach (array_keys($names) as $name) {
            $result[$name] = self::meta($name);
        }

        return $result;
    }

    public static function meta(string $name): array
    {
        $short  = Str::startsWith($name, 'admin.') ? Str::after($name, 'admin.') : $name;
        $group  = Str::before($short, '.');
        $action = Str::after($short, '.');
        $lookups = LookupRegistry::definitions();

        return [
            'group'    => $group,
            'group_ar' => self::GROUPS[$group] ?? ($lookups[$group]['title'] ?? $group),
            'ar_name'  => self::LABELS[$name] ?? self::ACTIONS[$action] ?? $name,
        ];
    }
}