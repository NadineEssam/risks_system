<?php

namespace App\Reports;

use App\Models\Indicator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * تقرير 5: مؤشرات الخطر غير المستوفاة في تاريخ الاستحقاق.
 * الفترات من دورية الإبلاغ بداية من تاريخ إدخال المؤشر — لو فيه قياس
 * جوه الفترة = منتظم، غير كده = غير منتظم. (الفترات اللي استحقت بس)
 */
class IndicatorComplianceReport extends BaseReport
{
    /** دورية الإبلاغ → عدد الشهور */
    public const FREQUENCY_MONTHS = ['شهري' => 1, 'ربع سنوي' => 3, 'نصف سنوي' => 6, 'سنوي' => 12];

    public function key(): string { return 'indicator-compliance'; }
    public function label(): string { return 'تقرير مؤشرات الخطر غير المستوفاة في تاريخ الاستحقاق'; }
    public function description(): string { return 'لكل مؤشر: تواريخ الاستحقاق من تاريخ الاعتماد حسب دورية الإبلاغ، وهل تم إدخال القياس في موعده (منتظم / غير منتظم).'; }
    public function icon(): string { return 'bx bx-calendar-exclamation'; }

    public function filters(): array
    {
        return array_merge(parent::filters(), [
            ['name' => 'status', 'label' => 'الانتظام', 'type' => 'select', 'required' => false,
                'options' => ['regular' => 'منتظم', 'irregular' => 'غير منتظم']],
        ]);
    }

    public function generate(array $filters): Collection
    {
        $indicators = Indicator::active()
            ->with(['reportingFrequency', 'potentialRiskRegister.sectorDetails.sector', 'followups'])
            ->when($filters['sector_id'] ?? null, fn ($q, $sectorId) => $q->forSector((int) $sectorId))
            ->get();

        $today = Carbon::today();
        $rows  = collect();

        foreach ($indicators as $indicator) {
            $months = self::FREQUENCY_MONTHS[trim((string) $indicator->reportingFrequency?->frequency_name)] ?? null;
            // البداية = تاريخ الاعتماد (تاريخ الاتفاق مع القطاع) — المؤشر بدونه مش بيدخل التقرير
            $start  = $indicator->approval_date;

            if (! $months || ! $start) {
                continue;
            }

            $periodStart = Carbon::parse($start)->startOfDay();
            // تاريخ إدخال القياس على النظام (ولو مش موجود: تاريخ القياس) — ده مقياس الالتزام
            $dates = $indicator->followups
                ->map(fn ($f) => $f->creation_date ?? $f->measurement_date)
                ->filter()
                ->map(fn ($d) => Carbon::parse($d));

            // كل فترة استحق موعدها (بحد أقصى 120 فترة كحماية)
            for ($i = 0; $i < 120; $i++) {
                $due = $periodStart->copy()->addMonthsNoOverflow($months);

                if ($due->gt($today)) {
                    break;
                }

                $fulfilled = $dates->contains(fn ($d) => $d->gte($periodStart) && $d->lt($due));

                $rows->push([
                    'approval'   => $indicator->approval_date,
                    'due'        => $due->copy(),
                    'from'       => $periodStart->copy(),
                    'sectors'    => $this->responsibleSectors($indicator->potentialRiskRegister),
                    'indicator'  => $indicator->indicator_name,
                    'frequency'  => $indicator->reportingFrequency?->frequency_name,
                    'regular'    => $fulfilled,
                ]);

                $periodStart = $due;
            }
        }

        return $rows
            ->when($filters['date_from'] ?? null, fn ($c, $from) => $c->filter(fn ($r) => $r['due']->gte(Carbon::parse($from))))
            ->when($filters['date_to'] ?? null, fn ($c, $to) => $c->filter(fn ($r) => $r['due']->lte(Carbon::parse($to))))
            ->when(($filters['status'] ?? null) === 'regular', fn ($c) => $c->where('regular', true))
            ->when(($filters['status'] ?? null) === 'irregular', fn ($c) => $c->where('regular', false))
            ->sortByDesc(fn ($r) => $r['due']->timestamp)
            ->values();
    }

    public function headings(): array
    {
        return ['تاريخ الاستحقاق', 'الفترة', 'القطاعات المسؤولة', 'وصف المؤشر', 'دورية الإبلاغ', 'تاريخ الاعتماد', 'منتظم / غير منتظم'];
    }

    public function map(mixed $row): array
    {
        return [
            $row['due']->format('Y-m-d'),
            $row['from']->format('Y-m-d').' → '.$row['due']->copy()->subDay()->format('Y-m-d'),
            $row['sectors'],
            $row['indicator'] ?? '—',
            $row['frequency'] ?? '—',
            $this->date($row['approval']),
            $row['regular'] ? 'منتظم' : 'غير منتظم',
        ];
    }
}