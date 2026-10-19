<?php

namespace App\Reports;

use App\Models\PotentialRiskRegister;
use App\Models\Sector;
use App\Reports\Contracts\ReportInterface;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Department;

abstract class BaseReport implements ReportInterface
{
    public function permission(): string
    {
        return 'reports.'.$this->key();
    }

    public function icon(): string
    {
        return 'bx bx-file';
    }

    /** الفلاتر المشتركة: من تاريخ — إلى تاريخ — القطاع */
    public function filters(): array
    {
        return [
            ['name' => 'date_from', 'label' => 'من تاريخ', 'type' => 'date', 'required' => false],
            ['name' => 'date_to',   'label' => 'إلى تاريخ', 'type' => 'date', 'required' => false],
            ['name' => 'sector_id', 'label' => 'القطاع', 'type' => 'select', 'required' => false,
                'options' => $this->sectorOptions()],
        ];
    }

    /** قائمة القطاعات من new_po */
    protected function sectorOptions(): array
    {
        return Sector::active()->orderBy('sector_ar')->pluck('sector_ar', 'sec_id')->all();
    }

    /** فلتر التاريخ على عمود معيّن */
    protected function applyDateRange(Builder $query, string $column, array $filters): Builder
    {
        return $query
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate($column, '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate($column, '<=', $to));
    }

    /** أسماء القطاعات المسؤولة من سجل الخطر المحتمل (مفصولة بـ ،) */
    protected function responsibleSectors(?PotentialRiskRegister $risk): string
    {
        if (! $risk) {
            return '—';
        }

        return $risk->sectorDetails
            ->map(fn ($d) => $d->sector?->sector_ar)
            ->filter()
            ->unique()
            ->implode('، ') ?: '—';
    }

    /** فلتر القطاع للأحداث: قطاع الحدث (قطاع الإدارة) أو القطاعات المسؤولة */
    protected function filterIncidentsBySector(Builder $query, $sectorId): Builder
    {
        if (! $sectorId) {
            return $query;
        }

        $sector = Sector::where('sec_id', $sectorId)->first();

        if (! $sector) {
            return $query->whereRaw('1 = 0');
        }

        // إدارات القطاع من new_po (MySQL) — مينفعش join مع Oracle
        $depIds = Department::where('sector_code', $sector->sector_code)->pluck('dep_id');

        return $query->where(fn ($q) => $q
            ->whereIn('departments_dep_id', $depIds)
            ->orWhereHas('potentialRiskRegister.sectorDetails', fn ($r) => $r->where('sectors_sec_id', $sector->sec_id))
        );
    }

    protected function date($value): string
    {
        return $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d') : '—';
    }

    protected function number($value): string
    {
        return $value === null ? '—' : rtrim(rtrim((string) $value, '0'), '.');
    }
}