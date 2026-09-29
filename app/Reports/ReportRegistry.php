<?php

namespace App\Reports;

use App\Reports\Contracts\ReportInterface;

/**
 * سجل التقارير (نفس الشكاوى) — كل تقرير بيتسجل في AppServiceProvider.
 */
class ReportRegistry
{
    /** @var ReportInterface[] */
    protected array $reports = [];

    public function register(ReportInterface ...$reports): void
    {
        foreach ($reports as $report) {
            $this->reports[$report->key()] = $report;
        }
    }

    /** كل التقارير (للـ PermissionRegistry) */
    public function all(): array
    {
        return $this->reports;
    }

    /** التقارير المتاحة للمستخدم الحالي */
    public function available(): array
    {
        return array_filter($this->reports, fn (ReportInterface $r) => PerUser($r->permission()));
    }

    /** تقرير واحد — 404 لو مش موجود، 403 لو المستخدم ملوش صلاحيته */
    public function find(string $key): ReportInterface
    {
        $report = $this->reports[$key] ?? abort(404);

        abort_unless(PerUser($report->permission()), 403, 'ليس لديك صلاحية لهذا التقرير.');

        return $report;
    }
}