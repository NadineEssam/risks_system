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
use App\Models\Department;
use App\Models\ResolutionStatus;

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
            // ===== البحث الموسّع (كل فلتر بيتضاف للبحث العام بـ OR) =====
            // وصف الحدث + وصف الخطر المحتمل المرتبط
            ->filterColumn('description', function ($q, $keyword) {
                $kw = '%'.mb_strtolower($keyword).'%';
                $q->where(fn ($w) => $w
                    ->whereRaw('LOWER(description) LIKE ?', [$kw])
                    ->orWhereHas('potentialRiskRegister', fn ($r) => $r->whereRaw('LOWER(risk_description) LIKE ?', [$kw]))
                );
            })
            // الإدارة: من new_po (MySQL) — ندوّر هناك ونفلتر بالـ ids في Oracle
            ->filterColumn('department', function ($q, $keyword) {
                $ids = Department::where('depname_ar', 'like', '%'.$keyword.'%')->pluck('dep_id');
                $q->whereIn('departments_dep_id', $ids->isEmpty() ? [-1] : $ids->all());
            })
            // حالة الحل
            ->filterColumn('status', function ($q, $keyword) {
                $ids = ResolutionStatus::where('status_name', 'like', '%'.$keyword.'%')->pluck('id');
                $q->whereIn('resolution_status_id', $ids->isEmpty() ? [-1] : $ids->all());
            })
            // تاريخ الاكتشاف (كامل أو جزء: 2026-09)
            ->filterColumn('discovery_date', function ($q, $keyword) {
                $q->whereRaw("TO_CHAR(discovery_date, 'YYYY-MM-DD') LIKE ?", ['%'.$keyword.'%']);
            })
            // درجة الخطر: رقم بالظبط
            ->filterColumn('risk_degree', function ($q, $keyword) {
                is_numeric($keyword) ? $q->where('risk_degree', (int) $keyword) : $q->whereRaw('1 = 0');
            })
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
            Column::make('discovery_date')->title('تاريخ الاكتشاف'),
            Column::make('description')->title('وصف الحدث')->orderable(false),
            Column::make('department')->title('الإدارة')->orderable(false),
            Column::make('frequency_score')->title('التكرار')->searchable(false),
            Column::make('impact_score')->title('الأثر')->searchable(false),
            Column::make('risk_degree')->title('درجة الخطر'),
            Column::make('status')->title('الحالة')->orderable(false),
            Column::computed('followup_status')->title('آخر متابعة'),
            Column::computed('action')->title('الإجراءات')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }
}