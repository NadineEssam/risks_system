<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class UsersDataTable extends BaseDataTable
{
    public function query(User $model): QueryBuilder
    {
        // sector/department من new_po — with() استعلام منفصل
        return $model->newQuery()->with(['roles', 'sector', 'department']);
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('domain_username', fn ($row) => '<code>'.e($row->domain_username).'</code>')
            ->addColumn('org', fn ($row) => e($row->sector?->sector_ar ?? '—')
                .'<br><small class="text-muted">'.e($row->department?->depname_ar ?? '').'</small>')
            ->addColumn('roles_list', fn ($row) => $row->roles
                ->map(fn ($role) => '<span class="badge bg-info me-1">'.e($role->name).'</span>')
                ->implode('') ?: '—')
            ->editColumn('is_active', fn ($row) => $this->validityBadge($row->is_active))
            ->addColumn('action', fn ($row) => $this->actionButtons('admin.users', $row->id, 'المستخدم', ['edit']))
            ->rawColumns(['domain_username', 'org', 'roles_list', 'is_active', 'action'])
            ->setRowId('id');
    }

    public function html(): HtmlBuilder
    {
        return $this->baseBuilder('users_table', 0, 'asc');
    }

    protected function getColumns(): array
    {
        return [
            Column::make('name')->title('الاسم'),
            Column::make('domain_username')->title('اسم مستخدم الدومين'),
            Column::make('email')->title('البريد الإلكتروني'),
            Column::computed('org')->title('القطاع / الإدارة'),
            Column::computed('roles_list')->title('الأدوار'),
            Column::make('is_active')->title('الحالة')->searchable(false),
            Column::computed('action')->title('الإجراءات')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }
}