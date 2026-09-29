<?php

namespace App\DataTables;

use App\Models\Incident;
use App\Support\RiskDegreeHelper;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use App\Support\IncidentAccess;
use Illuminate\Support\Facades\Auth;

class IncidentDataTable extends BaseDataTable
{
    public function query(Incident $model): QueryBuilder
    {
        // department من new_po (MySQL) — with() بيعمل استعلام منفصل فمفيش مشكلة
        $query = $model->newQuery()->with(['potentialRiskRegister', 'department', 'resolutionStatus']);

        // كل قطاع يشوف أحداثه (المنشئ + المسؤول) — المركزي ومدير النظام يشوفوا الكل
        return IncidentAccess::scopeVisible($query, Auth::user());
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('discovery_date', fn ($row) => $row->discovery_date?->format('Y-m-d') ?? '—')
            // وصف الحدث نفسه (بدل وصف الخطر المحتمل)
            ->editColumn('description', fn ($row) => Str::limit($row->description, 60) ?: '—')
            ->addColumn('department', fn ($row) => $row->department?->depname_ar ?? '—')
            ->editColumn('risk_degree', fn ($row) => RiskDegreeHelper::badge($row->risk_degree))
            ->addColumn('status', fn ($row) => $row->resolutionStatus?->status_name ?? '—')
            // آخر حالة متابعة للحدث (زي عمود الحالة في الشكاوى)
            ->addColumn('followup_status', fn ($row) => IncidentAccess::statusBadge($row->lastFollowup()?->followupStatus?->status_name))
            // 👁 عرض الحدث + 💬 متابعات الحدث (نفس أيقونة "الرد على البيان")
            // ✏️ للقطاع المنشئ/المركزي، والحدث مش مقفول
            ->addColumn('action', fn ($row) => $this->actionButtons('incidents', $row->id, 'الحدث',
                IncidentAccess::canEdit(Auth::user(), $row) && ! $row->isFollowupClosed() ? ['show', 'edit'] : ['show'],
                $this->iconButton('incident-followups.index', $row->id, 'bx bx-message-square-detail', 'متابعات الحدث')
            ))
            ->rawColumns(['risk_degree', 'followup_status', 'action'])
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
            Column::make('description')->title('وصف الحدث')->orderable(false),
            Column::computed('department')->title('الإدارة'),
            Column::make('frequency_score')->title('التكرار')->searchable(false),
            Column::make('impact_score')->title('الأثر')->searchable(false),
            Column::make('risk_degree')->title('درجة الخطر')->searchable(false),
            Column::computed('status')->title('الحالة'),
            Column::computed('followup_status')->title('آخر متابعة'),
            Column::computed('action')->title('الإجراءات')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }
}