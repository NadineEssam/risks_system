<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IndicatorFollowup;
use App\Models\PotentialRiskRegister;
use App\Models\ThresholdLevel;
use Illuminate\View\View;
use App\Support\IncidentAccess;
use App\Exports\ReportExport;
use App\Reports\ReportRegistry;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
        /** صفحة التقارير: كروت التقارير الخمسة بس (حسب صلاحية المستخدم) */
    public function index()
    {
        return view('reports.index', [
            'reports' => app(ReportRegistry::class)->available(),
        ]);
    }

    /** تقرير واحد: الفلاتر + النتائج */
    public function show(Request $request, string $key)
    {
        $report  = app(ReportRegistry::class)->find($key);
        $filters = $this->validateFilters($request, $report);
        $ran     = $request->has('run');

        return view('reports.show', [
            'report'  => $report,
            'filters' => $filters,
            'results' => $ran ? $report->generate($filters) : null,
        ]);
    }

    /** تصدير: xlsx / csv / pdf (نفس الشكاوى) */
    public function export(Request $request, string $key)
    {
        $report   = app(ReportRegistry::class)->find($key);
        $filters  = $this->validateFilters($request, $report);
        $filename = preg_replace('/[\/:*?"<>|]/u', '', $report->label()).' - '.now()->format('Y-m-d');

        return match ($request->input('format', 'xlsx')) {
            'xlsx'  => Excel::download(new ReportExport($report, $filters), "{$filename}.xlsx"),
            'csv'   => Excel::download(new ReportExport($report, $filters), "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV,
                ['Content-Type' => 'text/csv; charset=UTF-8']),
            'pdf'   => $this->exportPdf($report, $filters, $filename),
            default => abort(422, 'صيغة التصدير غير مدعومة.'),
        };
    }

    /** فلاتر التقرير: تواريخ + قوائم (القيم لازم تكون من الاختيارات) */
    private function validateFilters(Request $request, $report): array
    {
        $rules = [];
        $attributes = [];

        foreach ($report->filters() as $filter) {
            $rules[$filter['name']] = match ($filter['type']) {
                'date'   => ['nullable', 'date'],
                'select' => ['nullable', 'in:'.implode(',', array_keys($filter['options'] ?? []))],
                default  => ['nullable'],
            };
            $attributes[$filter['name']] = $filter['label'];
        }

        if (isset($rules['date_to'])) {
            $rules['date_to'][] = 'after_or_equal:date_from';
        }

        return array_filter(
            $request->validate($rules, [], $attributes),
            fn ($value) => $value !== null && $value !== ''
        );
    }

    /** PDF عربي من اليمين لليسار — A4 بالعرض (mPDF زي الشكاوى) */
    private function exportPdf($report, array $filters, string $filename)
    {
        $mpdf = new \Mpdf\Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4-L',
            'directionality'   => 'rtl',
            'default_font'     => 'dejavusans',
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
            'tempDir'          => storage_path('app/mpdf'),
        ]);

        $html = view('reports.pdf', [
            'report'  => $report,
            'filters' => $filters,
            'results' => $report->generate($filters),
        ])->render();

        ini_set('pcre.backtrack_limit', '5000000');

        foreach (str_split($html, 50000) as $part) {
            $mpdf->WriteHTML($part);
        }

        return response()->streamDownload(
            fn () => print($mpdf->Output('', 'S')),
            "{$filename}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }
}
