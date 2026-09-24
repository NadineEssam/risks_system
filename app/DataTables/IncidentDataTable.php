<?php

namespace App\DataTables;

use App\Models\Incident;
use App\Support\RiskDegreeHelper;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class IncidentDataTable extends BaseDataTable
{
    public function query(Incident $model): QueryBuilder
    {
        // department من new_po (MySQL) — with() بيعمل استعلام منفصل فمفيش مشكلة
        return $model->newQuery()->with(['potentialRiskRegister', 'department', 'resolutionStatus']);
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('discovery_date', fn ($row) => $row->discovery_date?->format('Y-m-d') ?? '—')
            ->addColumn('risk', fn ($row) => Str::limit($row->potentialRiskRegister?->risk_description, 50) ?: '—')
            // البحث في وصف الخطر (نفس Oracle)
            ->filterColumn('risk', function ($q, $keyword) {
                $q->whereHas('potentialRiskRegister', fn ($r) => $r->whereRaw('LOWER(risk_description) LIKE ?', ['%'.mb_strtolower($keyword).'%']));
            })
            ->addColumn('department', fn ($row) => $row->department?->depname_ar ?? '—')
            ->editColumn('risk_degree', fn ($row) => RiskDegreeHelper::badge($row->risk_degree))
            ->addColumn('status', fn ($row) => $row->resolutionStatus?->status_name ?? '—')
            ->addColumn('action', fn ($row) => $this->actionButtons('incidents', $row->id, 'الحدث', ['show']))
            ->rawColumns(['risk_degree', 'action'])
            ->setRowId('id');
    }

    public function html(): HtmlBuilder
    {
        return $this->baseBuilder('incidents_table');
    }

    protected function getColumns(): array
    {
        return [
            Column::make('discovery_date')->title('تاريخ الاكتشاف')->searchable(false),
            Column::make('risk')->title('الخطر المحتمل المرتبط')->orderable(false),
            Column::computed('department')->title('الإدارة'),
            Column::make('frequency_score')->title('التكرار')->searchable(false),
            Column::make('impact_score')->title('الأثر')->searchable(false),
            Column::make('risk_degree')->title('درجة الخطر')->searchable(false),
            Column::computed('status')->title('الحالة'),
            Column::computed('action')->title('الإجراءات')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }
}