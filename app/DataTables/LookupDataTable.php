<?php

namespace App\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

/**
 * جدول عام لأي Lookup من LookupRegistry — الأعمدة بتتبني من تعريف الحقول.
 */
class LookupDataTable extends BaseDataTable
{
    protected string $type = '';
    protected array $definition = [];

    public function forType(string $type, array $definition): static
    {
        $this->type = $type;
        $this->definition = $definition;

        return $this;
    }

    /** الحقول اللي بتظهر في الجدول */
    protected function listFields(): array
    {
        return array_values(array_filter($this->definition['fields'], fn ($f) => $f['list'] ?? true));
    }

    public function query(): QueryBuilder
    {
        $model = $this->definition['model'];

        return $model::query()->with($this->definition['with'] ?? []);
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $table = new EloquentDataTable($query);

        // حقول الـ select: نعرض اسم العنصر المرتبط بدل الـ id
        foreach ($this->listFields() as $field) {
            if ($field['type'] === 'select') {
                $table->addColumn($field['name'].'_label', fn ($row) => $row->{$field['relation']}?->{$field['display']} ?? '—');
            }
        }

        return $table
            ->editColumn('validity', fn ($row) => $this->validityBadge($row->validity))
            ->addColumn('action', fn ($row) => $this->actionButtons(
                "admin.{$this->type}", $row->getKey(), $this->definition['singular'] ?? ''
            ))
            ->rawColumns(['validity', 'action'])
            ->setRowId(fn ($row) => $row->getKey());
    }

    public function html(): HtmlBuilder
    {
        // الترتيب الافتراضي على أول عمود قابل للترتيب
        $orderIndex = null;
        foreach ($this->getColumns() as $i => $column) {
            if ($column->orderable) {
                $orderIndex = $i;
                break;
            }
        }

        return $this->baseBuilder('lookup_'.str_replace('-', '_', $this->type).'_table', $orderIndex, 'asc');
    }

    protected function getColumns(): array
    {
        $columns = [];

        foreach ($this->listFields() as $field) {
            $columns[] = match ($field['type']) {
                'select'   => Column::computed($field['name'].'_label')->title($field['label']),
                'textarea' => Column::make($field['name'])->title($field['label'])->orderable(false),
                'number'   => Column::make($field['name'])->title($field['label'])->searchable(false),
                default    => Column::make($field['name'])->title($field['label']),
            };
        }

        $columns[] = Column::make('validity')->title('الحالة')->searchable(false);
        $columns[] = Column::computed('action')->title('الإجراءات')->exportable(false)->printable(false)->addClass('text-center');

        return $columns;
    }
}