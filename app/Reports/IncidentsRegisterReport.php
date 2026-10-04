<?php

namespace App\Reports;

use App\Models\Incident;
use App\Support\IncidentAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\FollowupStatus;
use App\Models\ResolutionStatus;

/** تقرير 1: جميع مدخلات القطاعات والفروع بحسب تاريخ التسجيل */
class IncidentsRegisterReport extends BaseReport
{
    public function key(): string { return 'incidents-register'; }
    public function label(): string { return 'تقرير جميع المدخلات بحسب تاريخ التسجيل'; }
    public function description(): string { return 'كل الأحداث المسجلة بالقطاعات والفروع مع تصنيف الخطر ودرجته والإجراءات وحالة الحدث.'; }
    public function icon(): string { return 'bx bx-list-ul'; }

    /** الفلاتر المشتركة + حالة الحدث + آخر متابعة */
    public function filters(): array
    {
        return array_merge(parent::filters(), [
            ['name' => 'resolution_status_id', 'label' => 'حالة الحدث', 'type' => 'select', 'required' => false,
                'options' => ResolutionStatus::active()->pluck('status_name', 'id')->all()],
            ['name' => 'followup_status', 'label' => 'آخر متابعة', 'type' => 'select', 'required' => false,
                'options' => ['new' => 'جديد (بدون متابعة)'] + FollowupStatus::active()->pluck('status_name', 'status_name')->all()],
        ]);
    }

    public function generate(array $filters): Collection
    {
        $query = Incident::active()->with([
            'potentialRiskRegister.eventDetail.eventSubcategory.event.eventType',
            'potentialRiskRegister.sectorDetails.sector',
            'department.sector',
            'resolutionStatus',
            'sectorResponsibilities.sector',
        ]);

        IncidentAccess::scopeVisible($query, Auth::user());
        $this->applyDateRange($query, 'creation_date', $filters);
        $this->filterIncidentsBySector($query, $filters['sector_id'] ?? null);

        $query->when($filters['resolution_status_id'] ?? null, fn ($q, $id) => $q->where('resolution_status_id', $id));

        // آخر متابعة لكل حدث (نفس عمود "آخر متابعة" في قائمة الأحداث)
        $rows = $query->orderByDesc('id')->get()
            ->each(fn ($incident) => $incident->setAttribute(
                'last_followup_status', $incident->lastFollowup()?->followupStatus?->status_name
            ));

        return match ($filters['followup_status'] ?? null) {
            null    => $rows,
            'new'   => $rows->whereNull('last_followup_status')->values(),
            default => $rows->where('last_followup_status', $filters['followup_status'])->values(),
        };
    }

    public function headings(): array
    {
        return ['التاريخ', 'قطاع الحدث', 'الفرع / الإدارة', 'القطاعات المسؤولة', 'تصنيف الخطر', 'درجة الخطر',
            'الجهة المسئولة', 'وصف الحدث', 'الأثر الفعلي للمشكلة', 'الإجراء الحالي', 'الإجراء المقترح', 'حالة الحدث', 'آخر متابعة'];
    }

    public function map(mixed $row): array
    {
        return [
            $this->date($row->creation_date ?? $row->discovery_date),
            $row->department?->sector?->sector_ar ?? '—',
            $row->department?->depname_ar ?? '—',
            $this->responsibleSectors($row->potentialRiskRegister),
            $row->potentialRiskRegister?->classification_label ?? '—',
            $row->risk_degree ?? '—',
            $row->sectorResponsibilities->map(fn ($r) => $r->sector?->sector_ar)->filter()->unique()->implode('، ') ?: '—',
            $row->description ?? '—',
            $row->actual_impact_problem ?? '—',
            $row->current_procedure ?? '—',
            $row->proposed_procedure ?? '—',
            $row->resolutionStatus?->status_name ?? '—',
            $row->last_followup_status ?? 'جديد',
        ];
    }
}