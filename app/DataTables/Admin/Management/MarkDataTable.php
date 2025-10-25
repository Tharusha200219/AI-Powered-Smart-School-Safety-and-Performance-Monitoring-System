<?php

namespace App\DataTables\Admin\Management;

use App\Models\Mark;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MarkDataTable extends DataTable
{
    protected $model = 'marks';

    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('mark_id', function ($row) {
                return $row->mark_id;
            })
            ->addColumn('student_code', function ($row) {
                return $row->student ? $row->student->student_code : 'N/A';
            })
            ->addColumn('student_name', function ($row) {
                return $row->student ? $row->student->full_name : 'N/A';
            })
            ->addColumn('subject_name', function ($row) {
                return $row->subject ? $row->subject->subject_name : 'N/A';
            })
            ->addColumn('marks_display', function ($row) {
                return number_format($row->marks, 2) . '/' . number_format($row->total_marks, 2);
            })
            ->addColumn('percentage_display', function ($row) {
                return number_format($row->percentage ?? 0, 2) . '%';
            })
            ->addColumn('grade_display', function ($row) {
                return $row->grade ?? '-';
            })
            ->addColumn('action', function ($row) {
                $items = [];

                if (checkPermission('admin.management.marks.show')) {
                    $items[] = view('admin.layouts.actions.show', [
                        'url' => route('admin.management.marks.show', ['mark' => $row->mark_id]),
                        'id' => $row->mark_id,
                    ])->render();
                }

                if (checkPermission('admin.management.marks.edit')) {
                    $items[] = view('admin.layouts.actions.edit', [
                        'url' => route('admin.management.marks.edit', ['mark' => $row->mark_id]),
                        'id' => $row->mark_id,
                    ])->render();
                }

                if (checkPermission('admin.management.marks.delete')) {
                    $items[] = view('admin.layouts.actions.delete', [
                        'url' => route('admin.management.marks.destroy', ['mark' => $row->mark_id]),
                        'id' => $row->mark_id,
                    ])->render();
                }

                if (empty($items)) {
                    return '<span class="text-muted">No actions</span>';
                }

                $content = implode('<li><hr class="dropdown-divider"></li>', $items);

                return '<div class="dropdown text-end">
                    <button class="btn btn-icon border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="material-symbols-rounded text-lg">more_vert</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow rounded-3 p-2 w-100">'
                    . $content .
                    '</ul>
                </div>';
            })
            ->rawColumns(['action'])
            ->orderColumn('percentage_display', function ($query, $order) {
                $query->orderBy('percentage', $order);
            });
    }

    public function query(Mark $model): QueryBuilder
    {
        return $model->with(['student', 'subject'])->select('marks.*')->orderBy('created_at', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('mark-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->parameters([
                'scrollX' => true,
                'autoWidth' => false,
                'rowCallback' => 'function(row, data, index) { if (index % 2 === 0) { $(row).css("background-color", "rgba(0, 0, 0, 0.02)"); } }',
            ]);
    }

    protected function getColumns(): array
    {
        $columns = [
            Column::make('mark_id')->title('#')->addClass('text-start align-middle text-xs'),
            Column::make('student_code')->title('STUDENT ID')->addClass('text-start align-middle text-xs'),
            Column::make('student_name')->title('STUDENT')->addClass('text-start align-middle text-xs'),
            Column::make('grade_level')->title('GRADE')->addClass('text-start align-middle text-xs'),
            Column::make('subject_name')->title('SUBJECT')->addClass('text-start align-middle text-xs'),
            Column::make('academic_year')->title('ACADEMIC YEAR')->addClass('text-start align-middle text-xs'),
            Column::make('term')->title('TERM')->addClass('text-start align-middle text-xs'),
            Column::make('marks_display')->title('MARKS')->addClass('text-start align-middle text-xs')->searchable(false),
            Column::make('percentage_display')->title('PERCENTAGE')->addClass('text-start align-middle text-xs')->searchable(false),
            Column::make('grade_display')->title('GRADE')->addClass('text-start align-middle text-xs')->searchable(false),
        ];

        if (
            checkPermission('admin.management.marks.show') ||
            checkPermission('admin.management.marks.edit') ||
            checkPermission('admin.management.marks.delete')
        ) {
            $columns[] = Column::computed('action')->title('ACTIONS')->addClass('text-end align-middle py=2 text-xs')->exportable(false)->printable(false)->orderable(false)->searchable(false);
        }

        return $columns;
    }

    protected function filename(): string
    {
        return 'Marks_' . date('YmdHis');
    }
}
