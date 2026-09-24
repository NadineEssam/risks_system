<?php

namespace App\DataTables;

use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

/**
 * أساس مشترك لكل جداول النظام — نفس أيقونات وأزرار نظام الشكاوى
 * (bx-show / bx-edit-alt / bx-trash) وكل زر يظهر حسب PerUser().
 */
abstract class BaseDataTable extends DataTable
{
    public const ARABIC = [
        'processing'   => 'جارٍ التحميل...',
        'search'       => 'بحث:',
        'lengthMenu'   => 'عرض _MENU_ سجلات',
        'info'         => 'عرض _START_ إلى _END_ من أصل _TOTAL_ سجل',
        'infoEmpty'    => 'لا توجد سجلات',
        'infoFiltered' => '(منتقاة من مجموع _MAX_ سجل)',
        'zeroRecords'  => 'لا توجد نتائج مطابقة',
        'emptyTable'   => 'لا توجد بيانات',
        'paginate'     => ['first' => 'الأول', 'previous' => 'السابق', 'next' => 'التالي', 'last' => 'الأخير'],
    ];

    abstract protected function getColumns(): array;

    /**
     * $orderColumn = null → الجدول مش بيرتب، ويُستخدم ترتيب الـ query نفسها
     * (مهم لأعمدة CLOB في Oracle — مينفعش ORDER BY عليها)
     */
    protected function baseBuilder(string $tableId, ?int $orderColumn = 0, string $direction = 'desc'): HtmlBuilder
    {
        return $this->builder()
            ->setTableId($tableId)
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->pageLength(10)
            ->lengthMenu([10, 25, 50, 100])
            ->parameters([
                'order'        => $orderColumn === null ? [] : [[$orderColumn, $direction]],
                'language'     => self::ARABIC,
                'drawCallback' => 'function () { document.querySelectorAll(\'[data-bs-toggle="tooltip"]\').forEach(function (el) { bootstrap.Tooltip.getOrCreateInstance(el); }); }',
            ]);
    }

    /**
     * أزرار الإجراءات بنفس شكل نظام الشكاوى.
     * $prefix = بادئة اسم المسار (مثلاً 'risks' أو 'admin.users')
     */
    protected function actionButtons(string $prefix, $key, string $label = '', array $only = ['show', 'edit', 'destroy']): string
    {
        if ($key === null || $key === '') {
            return '—';
        }

        $buttons = [
            'show'    => ['btn-outline-info',    'bx bx-show',     'عرض'],
            'edit'    => ['btn-outline-primary', 'bx bx-edit-alt', 'تعديل'],
            'destroy' => ['btn-outline-danger',  'bx bx-trash',    'حذف'],
        ];

        $html = '<div class="d-flex align-items-center gap-2 justify-content-center">';

        foreach ($only as $action) {
            $route = "{$prefix}.{$action}";

            if (! isset($buttons[$action]) || ! Route::has($route) || ! PerUser($route)) {
                continue;
            }

            [$class, $icon, $verb] = $buttons[$action];
            $title = e(trim("{$verb} {$label}"));
            $url   = route($route, $key);

            $html .= $action === 'destroy'
                ? "<button type=\"button\" class=\"btn btn-sm {$class} action-btn delete-this\" data-url=\"{$url}\" data-bs-toggle=\"tooltip\" title=\"{$title}\"><i class=\"{$icon}\"></i></button>"
                : "<a href=\"{$url}\" class=\"btn btn-sm {$class} action-btn\" data-bs-toggle=\"tooltip\" title=\"{$title}\"><i class=\"{$icon}\"></i></a>";
        }

        return $html.'</div>';
    }

    protected function validityBadge($value): string
    {
        return $value
            ? '<span class="badge bg-success">فعّال</span>'
            : '<span class="badge bg-danger">غير فعّال</span>';
    }
}