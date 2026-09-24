<?php

namespace App\DataTables;

use App\Models\IncidentFollowup;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class IncidentFollowupDataTable extends BaseDataTable
{
    public function query(IncidentFollowup $model): QueryBuilder
    {
        $query = $model->newQuery()->with([
            'incidentSectorResponsibility.incident.potentialRiskRegister',
            'incidentSectorResponsibility.sector',
            'followupStatus',
            'followupEntryType',
        ]);

        // نفس منطق الـ controller القديم: كل قطاع يشوف متابعاته بس،
        // ما عدا مدير النظام ومن لديه صلاحية اعتماد القرار
        $user = Auth::user();
        $sectorId = $user->department?->sector?->sec_id;

        if ($sectorId && ! $user->hasRole('super-admin') && ! $user->can('incident-followups.decide')) {
            $query->whereHas('incidentSectorResponsibility', fn ($q) => $q->where('sectors_sec_id', $sectorId));
        }

        return $query;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('followup_date', fn ($row) => $row->followup_date?->format('Y-m-d') ?? '—')
            ->addColumn('risk', fn ($row) => Str::limit($row->incidentSectorResponsibility?->incident?->potentialRiskRegister?->risk_description, 40) ?: '—')
            ->addColumn('sector', fn ($row) => $row->incidentSectorResponsibility?->sector?->sector_ar ?? '—')
            ->addColumn('entry_type', fn ($row) => '<span class="badge bg-info">'.e($row->followupEntryType?->type_name ?? '—').'</span>')
            ->addColumn('status', fn ($row) => '<span class="badge bg-secondary">'.e($row->followupStatus?->status_name ?? '—').'</span>')
            ->editColumn('entry_text', fn ($row) => Str::limit($row->entry_text, 60))
            // 👁 يفتح الحدث نفسه
            ->addColumn('action', fn ($row) => $this->actionButtons('incidents', $row->incidentSectorResponsibility?->incident_id, 'الحدث', ['show']))
            ->rawColumns(['entry_type', 'status', 'action'])
            ->setRowId('id');
    }

    public function html(): HtmlBuilder
    {
        return $this->baseBuilder('incident_followups_table');
    }

    protected function getColumns(): array
    {
        return [
            Column::make('followup_date')->title('تاريخ المتابعة')->searchable(false),
            Column::computed('risk')->title('الخطر المحتمل'),
            Column::computed('sector')->title('القطاع'),
            Column::computed('entry_type')->title('نوع الإدخال'),
            Column::computed('status')->title('حالة المتابعة'),
            Column::make('entry_text')->title('نص المتابعة')->orderable(false),
            Column::computed('action')->title('الإجراءات')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }
}