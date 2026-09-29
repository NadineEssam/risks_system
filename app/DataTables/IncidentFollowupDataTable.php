<?php

namespace App\DataTables;

use App\Models\Incident;
use App\Models\IncidentFollowup;
use App\Support\IncidentAccess;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * متابعات حدث واحد — نفس ComplaintResponseDataTable في الشكاوى.
 */
class IncidentFollowupDataTable extends BaseDataTable
{
    protected ?Incident $incident = null;

    public function withIncident(Incident $incident): static
    {
        $this->incident = $incident;

        return $this;
    }

    public function query(IncidentFollowup $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['incidentSectorResponsibility.sector', 'followupStatus', 'followupEntryType'])
            ->whereHas('incidentSectorResponsibility', fn ($q) => $q->where('incident_id', $this->incident?->id));
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // بنحسبهم مرة واحدة للجدول كله
        $user     = Auth::user();
        $isClosed = $this->incident?->isFollowupClosed() ?? false;
        $lastId   = (int) $this->incident?->lastFollowup()?->id;

        return (new EloquentDataTable($query))
            ->editColumn('followup_date', fn ($row) => $row->followup_date?->format('Y-m-d') ?? '—')
            ->addColumn('sector', fn ($row) => $row->incidentSectorResponsibility?->sector?->sector_ar ?? '—')
            ->addColumn('entry_type', fn ($row) => '<span class="badge bg-info">'.e($row->followupEntryType?->type_name ?? '—').'</span>')
            ->addColumn('status', fn ($row) => IncidentAccess::statusBadge($row->followupStatus?->status_name))
            ->editColumn('entry_text', fn ($row) => Str::limit($row->entry_text, 60))
            ->editColumn('created_by', fn ($row) => $row->created_by ?? '—')
            // نفس قواعد الشكاوى:
            // 👁 دايماً — ✏️ لو الحدث مفتوح أو دي آخر متابعة — 🗑 لو الحدث مفتوح
            // (✏️/🗑 للقطاع اللي كتب المتابعة أو المركزي/مدير النظام)
            ->addColumn('action', function ($row) use ($user, $isClosed, $lastId) {
                $canModify = IncidentAccess::canModify($user, $row);
                $isLast    = (int) $row->id === $lastId;

                $only = ['show'];
                if ($canModify && (! $isClosed || $isLast)) {
                    $only[] = 'edit';
                }
                if ($canModify && ! $isClosed) {
                    $only[] = 'destroy';
                }

                return $this->actionButtons('incident-followups', $row->id, 'المتابعة', $only);
            })
            ->rawColumns(['entry_type', 'status', 'action'])
            ->setRowId('id');
    }

    public function html(): HtmlBuilder
    {
        // الأحدث أولاً (عمود 0 = تاريخ المتابعة)
        return $this->baseBuilder('incident_followups_table', 0, 'desc');
    }

    protected function getColumns(): array
    {
        return [
            Column::make('followup_date')->title('تاريخ المتابعة')->searchable(false),
            Column::computed('sector')->title('القطاع'),
            Column::computed('entry_type')->title('نوع الإدخال'),
            Column::computed('status')->title('الحالة'),
            Column::make('entry_text')->title('نص المتابعة')->orderable(false),
            Column::make('created_by')->title('بواسطة'),
            Column::computed('action')->title('الإجراءات')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }
}