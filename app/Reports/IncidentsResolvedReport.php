<?php

namespace App\Reports;

use App\Models\Incident;
use App\Support\IncidentAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/** تقرير 4: الأحداث اللي تم حلها أو مقبولة (حالة الحدث أو آخر متابعة) */
class IncidentsResolvedReport extends BaseReport
{
    public const RESOLVED_STATUSES = ['حل كلي', 'مقبول'];
    public const CLOSING_FOLLOWUPS = ['تم الحل', 'إغلاق', 'قبول الخطر'];

    public function key(): string { return 'incidents-resolved'; }
    public function label(): string { return 'تقرير الأحداث التي تم حلها أو مقبولة'; }
    public function description(): string { return 'الأحداث بحالة حل كلي/مقبول أو آخر متابعة تم الحل/إغلاق/قبول الخطر، مع الضوابط والإجراءات.'; }
    public function icon(): string { return 'bx bx-check-shield'; }

    public function generate(array $filters): Collection
    {
        $query = Incident::active()->with([
            'potentialRiskRegister.sectorDetails.sector',
            'department.sector',
            'resolutionStatus',
        ]);

        IncidentAccess::scopeVisible($query, Auth::user());
        $this->applyDateRange($query, 'creation_date', $filters);
        $this->filterIncidentsBySector($query, $filters['sector_id'] ?? null);

        // آخر متابعة لكل حدث — وبعدين نفلتر: حالة الحدث أو آخر متابعة
        return $query->orderByDesc('id')->get()
            ->each(fn ($incident) => $incident->setAttribute('last_followup_status', $incident->lastFollowup()?->followupStatus?->status_name))
            ->filter(fn ($incident) =>
                in_array($incident->resolutionStatus?->status_name, self::RESOLVED_STATUSES, true)
                || in_array($incident->last_followup_status, self::CLOSING_FOLLOWUPS, true)
            )
            ->values();
    }

    public function headings(): array
    {
        return ['التاريخ', 'قطاع الحدث', 'الفرع / الإدارة', 'القطاعات المسؤولة', 'وصف الحدث', 'درجة الخطر',
            'حالة الحدث', 'آخر متابعة', 'الضوابط والإجراءات'];
    }

    public function map(mixed $row): array
    {
        return [
            $this->date($row->creation_date ?? $row->discovery_date),
            $row->department?->sector?->sector_ar ?? '—',
            $row->department?->depname_ar ?? '—',
            $this->responsibleSectors($row->potentialRiskRegister),
            $row->description ?? '—',
            $row->risk_degree ?? '—',
            $row->resolutionStatus?->status_name ?? '—',
            $row->last_followup_status ?? '—',
            $row->potentialRiskRegister?->proposed_control ?? '—',
        ];
    }
}