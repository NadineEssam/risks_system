<?php

namespace App\Reports;

use App\Models\IncidentFollowup;
use App\Support\IncidentAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/** تقرير 2: متابعة الأحداث بدرجة الخطر ورد الجهة المسئولة */
class IncidentFollowupsReport extends BaseReport
{
    public function key(): string { return 'incident-followups'; }
    public function label(): string { return 'تقرير متابعة الأحداث ورد الجهة المسئولة'; }
    public function description(): string { return 'كل متابعة على الأحداث: درجة الخطر، رد القطاع المسئول، وتاريخ المتابعة.'; }
    public function icon(): string { return 'bx bx-message-square-detail'; }

    public function generate(array $filters): Collection
    {
        $user     = Auth::user();
        $sectorId = $filters['sector_id'] ?? null;

        $query = IncidentFollowup::with([
            'incidentSectorResponsibility.incident.department.sector',
            'incidentSectorResponsibility.incident.potentialRiskRegister.sectorDetails.sector',
            'incidentSectorResponsibility.sector',
            'followupStatus',
        ])->whereHas('incidentSectorResponsibility.incident', function ($q) use ($user, $sectorId) {
            IncidentAccess::scopeVisible($q, $user);
            $this->filterIncidentsBySector($q, $sectorId);
        });

        $this->applyDateRange($query, 'followup_date', $filters);

        return $query->orderByDesc('followup_date')->orderByDesc('id')->get();
    }

    public function headings(): array
    {
        return ['تاريخ الحدث', 'قطاع الحدث', 'القطاعات المسؤولة', 'وصف الحدث', 'درجة الخطر',
            'القطاع المتابع', 'رد الجهة المسئولة', 'حالة المتابعة', 'تاريخ المتابعة'];
    }

    public function map(mixed $row): array
    {
        $incident = $row->incidentSectorResponsibility?->incident;

        return [
            $this->date($incident?->creation_date ?? $incident?->discovery_date),
            $incident?->department?->sector?->sector_ar ?? '—',
            $this->responsibleSectors($incident?->potentialRiskRegister),
            $incident?->description ?? '—',
            $incident?->risk_degree ?? '—',
            $row->incidentSectorResponsibility?->sector?->sector_ar ?? '—',
            $row->entry_text ?? '—',
            $row->followupStatus?->status_name ?? '—',
            $this->date($row->followup_date),
        ];
    }
}