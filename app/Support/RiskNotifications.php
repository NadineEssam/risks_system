<?php

namespace App\Support;

use App\Models\Incident;
use App\Models\IncidentFollowup;
use App\Models\IndicatorFollowup;
use App\Models\PotentialRiskRegister;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Str;

/**
 * أحداث الإشعارات: مين يتبعتله وإيه النص — كل حدث في مكان واحد.
 */
class RiskNotifications
{
    /** حدث جديد → القطاعات المسؤولة (وفيها القطاع المنشئ وقطاع المخاطر) */
    public static function incidentCreated(Incident $incident): void
    {
        $incident->loadMissing(['potentialRiskRegister', 'department', 'sectorResponsibilities']);

        $users = Notifier::sectorUsers($incident->sectorResponsibilities->pluck('sectors_sec_id')->all());

        Notifier::send($users, new SystemNotification(
            'حدث جديد #'.$incident->id,
            'تم تسجيل حدث بإدارة '.($incident->department?->depname_ar ?? '—')
                .' — درجة الخطر '.$incident->risk_degree.': '
                .Str::limit($incident->description ?: $incident->potentialRiskRegister?->risk_description, 100),
            route('incidents.show', $incident),
            'bi-exclamation-triangle',
            'warning',
        ));
    }

    /** متابعة جديدة (أو إغلاق) → القطاع المنشئ + القطاعات المسؤولة + قطاع المخاطر */
    public static function followupAdded(IncidentFollowup $followup): void
    {
        $followup->loadMissing([
            'followupStatus',
            'incidentSectorResponsibility.sector',
            'incidentSectorResponsibility.incident.department.sector',
            'incidentSectorResponsibility.incident.sectorResponsibilities',
        ]);

        $incident  = $followup->incidentSectorResponsibility->incident;
        $sectorIds = $incident->sectorResponsibilities->pluck('sectors_sec_id')
            ->push($incident->department?->sector?->sec_id)
            ->all();

        $users   = Notifier::sectorUsers($sectorIds)->merge(Notifier::centralUsers());
        $status  = $followup->followupStatus?->status_name ?? '—';
        $closing = in_array($status, IncidentAccess::CLOSING_STATUSES, true);

        Notifier::send($users, new SystemNotification(
            $closing ? 'تم إغلاق الحدث #'.$incident->id : 'متابعة جديدة على الحدث #'.$incident->id,
            ($followup->incidentSectorResponsibility?->sector?->sector_ar ?? 'قطاع')
                .' سجّل متابعة بحالة "'.$status.'": '.Str::limit($followup->entry_text, 100),
            route('incident-followups.index', $incident),
            $closing ? 'bi-lock' : 'bi-chat-left-text',
            $closing ? 'success' : 'info',
        ));
    }

    /** قياس مؤشر متوسط/مرتفع → قطاعات الخطر المرتبط + قطاع المخاطر */
    public static function measurementAlert(IndicatorFollowup $followup): void
    {
        $followup->loadMissing(['thresholdLevel', 'indicator.potentialRiskRegister.sectorDetails']);

        $level = $followup->thresholdLevel;
        if (! $level || (int) $level->sort_order < 2) {
            return; // مقبول — مفيش إشعار
        }

        $indicator = $followup->indicator;
        $sectorIds = $indicator->potentialRiskRegister?->sectorDetails->pluck('sectors_sec_id')->all() ?? [];
        $users     = Notifier::sectorUsers($sectorIds)->merge(Notifier::centralUsers());
        $high      = (int) $level->sort_order >= 3;

        Notifier::send($users, new SystemNotification(
            ($high ? 'تصعيد: ' : 'إنذار: ').'مؤشر في '.$level->level_name,
            'القيمة الفعلية '.rtrim(rtrim((string) $followup->actual_value, '0'), '.')
                .' للمؤشر: '.Str::limit($indicator->indicator_name, 100),
            route('indicator-followups.index', $indicator),
            'bi-graph-up-arrow',
            $high ? 'danger' : 'warning',
        ));
    }

    /** اعتماد حالة خطر → رئيس قطاع المخاطر + قطاعات الخطر */
    public static function riskStatusApproved(PotentialRiskRegister $risk, string $statusName): void
    {
        $risk->loadMissing('sectorDetails');

        $chiefs = User::where('is_active', 1)
            ->whereHas('roles', fn ($q) => $q->where('name', 'risk-chief'))
            ->get();

        $users = $chiefs->merge(Notifier::sectorUsers($risk->sectorDetails->pluck('sectors_sec_id')->all()));

        Notifier::send($users, new SystemNotification(
            'اعتماد حالة خطر: '.$statusName,
            'تم اعتماد الحالة "'.$statusName.'" للخطر: '.Str::limit($risk->risk_description, 100),
            route('risks.show', $risk),
            'bi-shield-check',
            'primary',
        ));
    }
}