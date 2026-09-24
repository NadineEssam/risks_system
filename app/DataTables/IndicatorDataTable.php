<?php

namespace App\DataTables;

use App\Models\Indicator;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class IndicatorDataTable extends BaseDataTable
{
    public function query(Indicator $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['potentialRiskRegister', 'nature', 'measurementUnit', 'reportingFrequency'])
            ->orderByDesc('id');
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('indicator_name', fn ($row) => Str::limit($row->indicator_name, 45))
            ->addColumn('risk', fn ($row) => Str::limit($row->potentialRiskRegister?->risk_description, 35) ?: '—')
            ->filterColumn('risk', function ($q, $keyword) {
                $q->whereHas('potentialRiskRegister', fn ($r) => $r->whereRaw('LOWER(risk_description) LIKE ?', ['%'.mb_strtolower($keyword).'%']));
            })
            ->addColumn('nature', fn ($row) => $row->nature?->nature_name ?? '—')
            ->addColumn('unit', fn ($row) => $row->measurementUnit?->unit_name ?? '—')
            ->addColumn('frequency', fn ($row) => $row->reportingFrequency?->frequency_name ?? '—')
            ->editColumn('validity', fn ($row) => $this->validityBadge($row->validity))
            ->addColumn('action', fn ($row) => $this->actionButtons('indicators', $row->getKey(), 'المؤشر', ['show']))
            ->rawColumns(['validity', 'action'])
            ->setRowId(fn ($row) => $row->getKey());
    }

    public function html(): HtmlBuilder
    {
        // الأحدث أولاً من الـ query (indicator_name نوعه CLOB)
        return $this->baseBuilder('indicators_table', null);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('indicator_name')->title('اسم المؤشر')->orderable(false),
            Column::make('risk')->title('الخطر المرتبط')->orderable(false),
            Column::computed('nature')->title('طبيعة المؤشر'),
            Column::computed('unit')->title('وحدة القياس'),
            Column::computed('frequency')->title('دورية الإبلاغ'),
            Column::make('validity')->title('الحالة')->searchable(false),
            Column::computed('action')->title('الإجراءات')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }
}