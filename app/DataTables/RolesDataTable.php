<?php

namespace App\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class RolesDataTable extends BaseDataTable
{
    public function query(Role $model): QueryBuilder
    {
        return $model->newQuery()->withCount(['permissions', 'users']);
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('perms', fn ($row) => '<span class="badge bg-info">'.$row->permissions_count.'</span>')
            ->addColumn('users', fn ($row) => '<span class="badge bg-secondary">'.$row->users_count.'</span>')
            // دور مدير النظام: تعديل فقط بدون حذف
            ->addColumn('action', fn ($row) => $this->actionButtons(
                'admin.roles', $row->id, 'الدور',
                $row->name === 'super-admin' ? ['edit'] : ['edit', 'destroy']
            ))
            ->rawColumns(['perms', 'users', 'action'])
            ->setRowId('id');
    }

    public function html(): HtmlBuilder
    {
        return $this->baseBuilder('roles_table', 0, 'asc');
    }

    protected function getColumns(): array
    {
        return [
            Column::make('name')->title('اسم الدور'),
            Column::computed('perms')->title('عدد الصلاحيات'),
            Column::computed('users')->title('عدد المستخدمين'),
            Column::computed('action')->title('الإجراءات')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }
}