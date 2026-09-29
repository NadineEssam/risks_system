<?php

namespace App\Exports;

use App\Reports\Contracts\ReportInterface;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/** تصدير أي تقرير لـ Excel/CSV (نفس الشكاوى) + الشيت من اليمين لليسار */
class ReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithCustomCsvSettings, WithEvents
{
    protected Collection $data;

    public function __construct(protected ReportInterface $report, array $filters)
    {
        $this->data = $report->generate($filters);
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->report->headings();
    }

    public function map($row): array
    {
        return $this->report->map($row);
    }

    public function title(): string
    {
        // اسم الشيت بحد أقصى 31 حرف (قيد Excel)
        return mb_substr($this->report->label(), 0, 31);
    }

    public function getCsvSettings(): array
    {
        return ['use_bom' => true];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E3A5F']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => fn (AfterSheet $event) => $event->sheet->getDelegate()->setRightToLeft(true),
        ];
    }
}