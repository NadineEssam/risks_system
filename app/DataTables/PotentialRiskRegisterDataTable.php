<?php

namespace App\DataTables;

use App\Models\PotentialRiskRegister;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class PotentialRiskRegisterDataTable extends BaseDataTable
{
    // البيانات من Oracle (نفس الاستعلام اللي كان في الـ controller)
    public function query(PotentialRiskRegister $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['eventDetail.eventSubcategory.event.eventType', 'latestResolutionStatus.resolutionStatus'])
            ->withCount(['sectorDetails', 'incidents']);
    }

    // شكل كل عمود
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('creation_date', fn ($row) => $row->creation_date?->format('Y-m-d') ?? '—')
            ->editColumn('risk_description', fn ($row) => Str::limit($row->risk_description, 60))
            ->addColumn('classification', fn ($row) => $row->classification_label ?? '—')
            ->addColumn('sectors', fn ($row) => '<span class="badge bg-secondary">'.$row->sector_details_count.' قطاع</span>')
            ->addColumn('current_status', fn ($row) => $row->latestResolutionStatus?->resolutionStatus?->status_name ?? '—')
            ->editColumn('validity', fn ($row) => $this->validityBadge($row->validity))
            ->addColumn('action', fn ($row) => $this->actionButtons('risks', $row->id, 'الخطر', ['show', 'edit']))
            ->rawColumns(['sectors', 'validity', 'action'])
            ->setRowId('id');
    }

    public function html(): HtmlBuilder
    {
        return $this->baseBuilder('risks_table');
    }

    // عناوين الأعمدة بالعربي
    protected function getColumns(): array
    {
        return [
            Column::make('creation_date')->title('تاريخ التسجيل')->searchable(false),
            Column::make('risk_description')->title('وصف الخطر')->orderable(false),
            Column::computed('classification')->title('تصنيف بازل'),
            Column::computed('sectors')->title('القطاعات المسؤولة'),
            Column::computed('current_status')->title('الحالة الحالية'),
            Column::make('incidents_count')->title('عدد الأحداث')->searchable(false)->orderable(false),
            Column::make('validity')->title('الحالة')->searchable(false),
            Column::computed('action')->title('الإجراءات')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }
}