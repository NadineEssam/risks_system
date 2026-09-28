<?php

namespace App\DataTables;

use App\Models\Indicator;
use App\Models\IndicatorFollowup;
use App\Models\ThresholdLevel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * قياسات مؤشر واحد — نفس شكل متابعات الحدث.
 */
class IndicatorFollowupDataTable extends BaseDataTable
{
    protected ?Indicator $indicator = null;

    public function withIndicator(Indicator $indicator): static
    {
        $this->indicator = $indicator;

        return $this;
    }

    public function query(IndicatorFollowup $model): QueryBuilder
    {
        return $model->newQuery()
            ->with('thresholdLevel')
            ->where('indicators_id', $this->indicator?->id);
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('measurement_date', fn ($row) => $row->measurement_date?->format('Y-m-d') ?? '—')
            ->editColumn('actual_value', fn ($row) => $row->actual_value !== null ? rtrim(rtrim((string) $row->actual_value, '0'), '.') : '—')
            ->addColumn('threshold', fn ($row) => $row->thresholdLevel?->badge() ?? ThresholdLevel::emptyBadge('—'))
            ->editColumn('change_reason', fn ($row) => Str::limit($row->change_reason, 40) ?: '—')
            ->editColumn('action_taken', fn ($row) => Str::limit($row->action_taken, 40) ?: '—')
            ->editColumn('created_by', fn ($row) => $row->created_by ?? '—')
            // الكل بالصلاحية: 👁 عرض — ✏️ تعديل — 🗑 حذف
            ->addColumn('action', fn ($row) => $this->actionButtons('indicator-followups', $row->id, 'القياس'))
            ->rawColumns(['threshold', 'action'])
            ->setRowId('id');
    }

    public function html(): HtmlBuilder
    {
        // الأحدث قياساً أولاً (عمود 0 = تاريخ القياس)
        return $this->baseBuilder('indicator_followups_table', 0, 'desc');
    }

    protected function getColumns(): array
    {
        return [
            Column::make('measurement_date')->title('تاريخ القياس')->searchable(false),
            Column::make('actual_value')->title('القيمة الفعلية')->searchable(false),
            Column::computed('threshold')->title('مستوى حد الخطر'),
            Column::make('change_reason')->title('أسباب التغيّر')->orderable(false),
            Column::make('action_taken')->title('الإجراء المتخذ')->orderable(false),
            Column::make('created_by')->title('بواسطة'),
            Column::computed('action')->title('الإجراءات')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }
}