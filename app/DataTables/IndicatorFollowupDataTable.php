<?php

namespace App\DataTables;

use App\Models\IndicatorFollowup;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class IndicatorFollowupDataTable extends BaseDataTable
{
    public function query(IndicatorFollowup $model): QueryBuilder
    {
        return $model->newQuery()->with(['indicator', 'thresholdLevel']);
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('indicator', fn ($row) => Str::limit($row->indicator?->indicator_name, 40) ?: '—')
            // البحث في اسم المؤشر (Oracle)
            ->filterColumn('indicator', function ($q, $keyword) {
                $q->whereHas('indicator', fn ($r) => $r->whereRaw('LOWER(indicator_name) LIKE ?', ['%'.mb_strtolower($keyword).'%']));
            })
            ->editColumn('measurement_date', fn ($row) => $row->measurement_date?->format('Y-m-d') ?? '—')
            ->editColumn('actual_value', fn ($row) => $row->actual_value !== null ? rtrim(rtrim((string) $row->actual_value, '0'), '.') : '—')
            ->addColumn('threshold', function ($row) {
                if (! $row->thresholdLevel) {
                    return '—';
                }
                $class = $row->thresholdLevel->isAcceptable() ? 'bg-success' : 'bg-warning text-dark';

                return '<span class="badge '.$class.'">'.e($row->thresholdLevel->level_name).'</span>';
            })
            ->editColumn('change_reason', fn ($row) => Str::limit($row->change_reason, 40) ?: '—')
            // 👁 يفتح المؤشر نفسه
            ->addColumn('action', fn ($row) => $this->actionButtons('indicators', $row->indicators_id, 'المؤشر', ['show']))
            ->rawColumns(['threshold', 'action'])
            ->setRowId('id');
    }

    public function html(): HtmlBuilder
    {
        // الأحدث قياساً أولاً (عمود رقم 1 = تاريخ القياس)
        return $this->baseBuilder('indicator_followups_table', 1, 'desc');
    }

    protected function getColumns(): array
    {
        return [
            Column::make('indicator')->title('المؤشر')->orderable(false),
            Column::make('measurement_date')->title('تاريخ القياس')->searchable(false),
            Column::make('actual_value')->title('القيمة الفعلية')->searchable(false),
            Column::computed('threshold')->title('مستوى حد الخطر'),
            Column::make('change_reason')->title('أسباب التغيّر')->orderable(false),
            Column::computed('action')->title('الإجراءات')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }
}