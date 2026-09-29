<?php

namespace App\Reports;

use App\Models\IndicatorFollowup;
use Illuminate\Support\Collection;

/** تقرير 3: مؤشرات الخطر في مرحلة الإنذار أو التصعيد والإجراء المتخذ */
class IndicatorAlertsReport extends BaseReport
{
    public function key(): string { return 'indicator-alerts'; }
    public function label(): string { return 'تقرير مؤشرات الخطر في مرحلة الإنذار أو التصعيد'; }
    public function description(): string { return 'القياسات اللي مستواها متوسط أو مرتفع والإجراء المتخذ من القطاع.'; }
    public function icon(): string { return 'bx bx-error'; }

    public function generate(array $filters): Collection
    {
        // غير "مقبول" = متوسط (2) أو مرتفع (3)
        $query = IndicatorFollowup::with(['indicator.potentialRiskRegister.sectorDetails.sector', 'thresholdLevel'])
            ->whereHas('thresholdLevel', fn ($q) => $q->where('sort_order', '>', 1))
            ->when($filters['sector_id'] ?? null, fn ($q, $sectorId) => $q->whereHas('indicator', fn ($i) => $i->forSector((int) $sectorId)));

        $this->applyDateRange($query, 'measurement_date', $filters);

        return $query->orderByDesc('measurement_date')->orderByDesc('id')->get();
    }

    public function headings(): array
    {
        return ['تاريخ القياس', 'القطاعات المسؤولة', 'وصف المؤشر', 'القيمة الفعلية', 'مستوى حد الخطر', 'الإجراء المتخذ'];
    }

    public function map(mixed $row): array
    {
        return [
            $this->date($row->measurement_date),
            $this->responsibleSectors($row->indicator?->potentialRiskRegister),
            $row->indicator?->indicator_name ?? '—',
            $this->number($row->actual_value),
            $row->thresholdLevel?->level_name ?? '—',
            $row->action_taken ?: '—',
        ];
    }
}