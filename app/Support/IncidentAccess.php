<?php

namespace App\Support;

use App\Models\Department;
use App\Models\Incident;
use App\Models\IncidentFollowup;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * مين يشوف الحدث ومتابعاته (نفس فكرة الشكاوى):
 * - مدير النظام + القطاع المركزي للمخاطر (كود 15): كل الأحداث
 * - القطاع المنشئ للحدث (قطاع إدارة الحدث): أحداثه
 * - القطاع المسؤول عن المتابعة: الأحداث المسؤول عنها
 */
class IncidentAccess
{
    /** حالات المتابعة اللي بتقفل الحدث (زي حالات 2 و4 في الشكاوى) */
    public const CLOSING_STATUSES = ['إغلاق', 'قبول الخطر'];

    /** حالات ممكن تتكرر على نفس الحدث (زي "تحت الدراسة" في الشكاوى) */
    public const REPEATABLE_STATUSES = ['جارى المتابعة'];

    public static function sector(User $user): ?Sector
    {
        return $user->sector ?? $user->department?->sector;
    }

    public static function isCentral(User $user): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $code = config('app.central_risk_sector_code');

        return $code && (string) self::sector($user)?->sector_code === (string) $code;
    }

    /** فلترة الأحداث حسب قطاع المستخدم */
    public static function scopeVisible(Builder $query, User $user): Builder
    {
        if (self::isCentral($user)) {
            return $query;
        }

        $sector = self::sector($user);

        if (! $sector) {
            return $query->whereRaw('1 = 0');
        }

        // إدارات القطاع من new_po (MySQL) — مينفعش join مع Oracle
        $depIds = Department::where('sector_code', $sector->sector_code)->pluck('dep_id');

        return $query->where(function ($q) use ($sector, $depIds) {
            $q->whereIn('departments_dep_id', $depIds)
              ->orWhereHas('sectorResponsibilities', fn ($r) => $r->where('sectors_sec_id', $sector->sec_id));
        });
    }

    public static function canView(User $user, Incident $incident): bool
    {
        return self::scopeVisible(Incident::query()->whereKey($incident->getKey()), $user)->exists();
    }

    /** تعديل/حذف متابعة: القطاع اللي كتبها + القطاع المركزي + مدير النظام */
    public static function canModify(User $user, IncidentFollowup $followup): bool
    {
        if (self::isCentral($user)) {
            return true;
        }

        return (int) $followup->incidentSectorResponsibility?->sectors_sec_id === (int) self::sector($user)?->sec_id;
    }
        /** لون حالة المتابعة (زي ألوان حالات البيان في الشكاوى) */
    public static function statusBadge(?string $name): string
    {
        $style = match ($name) {
            'جارى المتابعة' => 'background:#f0ad4e;color:#fff;',
            'تم الحل'        => 'background:#17a2b8;color:#fff;',
            'إغلاق'          => 'background:#28a745;color:#fff;',
            'قبول الخطر'     => 'background:#dc3545;color:#fff;',
            default          => 'background:#6c757d;color:#fff;',
        };

        return '<span class="badge" style="'.$style.'font-size:12px;padding:6px 10px;">'.e($name ?? 'جديد').'</span>';
    }

        /** تعديل الحدث: القطاع المنشئ (قطاع إدارة الحدث) + القطاع المركزي + مدير النظام */
    public static function canEdit(User $user, Incident $incident): bool
    {
        if (self::isCentral($user)) {
            return true;
        }

        $sector = self::sector($user);

        return $sector && (string) $incident->department?->sector_code === (string) $sector->sector_code;
    }
}