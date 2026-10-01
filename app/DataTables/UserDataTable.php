<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('user_details', function ($row) {
                $name = trim($row->firstname . ' ' . $row->lastname);
                return '
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                            <i class="bi bi-person text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-medium">' . e($name) . '</div>
                            <div class="text-muted small">' . e($row->email) . '</div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('role_badge', function ($row) {
                $roleName = $row->role ? $row->role->name : 'No Role';
                return '<span class="badge bg-primary">' . e($roleName) . '</span>';
            })
            ->addColumn('status_badge', function ($row) {
                return $row->status === 'active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('last_login_formatted', function ($row) {
                return $row->last_login
                    ? $row->last_login->format('M d, Y')
                    : '<span class="text-muted">Never</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary edit-user" data-id="' . $row->id . '">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-user" data-id="' . $row->id . '">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['user_details', 'role_badge', 'status_badge', 'last_login_formatted', 'action'])
            ->setRowId('id');
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['role'])
            ->select('users.*');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->ajax(route('users.data'))
            ->dom('Bfrtip')
            ->orderBy(0)
            ->pageLength(10)
            ->responsive(true)
            ->parameters([
                'buttons' => [
                    [
                        'extend' => 'excel',
                        'className' => 'btn btn-success btn-sm',
                        'text' => '<i class="bi bi-file-earmark-excel"></i> Excel'
                    ],
                    [
                        'extend' => 'csv',
                        'className' => 'btn btn-info btn-sm',
                        'text' => '<i class="bi bi-filetype-csv"></i> CSV'
                    ],
                    [
                        'extend' => 'print',
                        'className' => 'btn btn-primary btn-sm',
                        'text' => '<i class="bi bi-printer"></i> Print'
                    ]
                ],
                'processing' => true,
                'serverSide' => true,
                'language' => [
                    'processing' => '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    'emptyTable' => 'No users found',
                    'zeroRecords' => 'No matching users found'
                ]
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('user_details')
                ->title('User')
                ->orderable(false)
                ->searchable(false)
                ->width(250),
            Column::make('role_badge')
                ->title('Role')
                ->orderable(false)
                ->searchable(false)
                ->width(100),
            Column::make('status_badge')
                ->title('Status')
                ->orderable(false)
                ->searchable(false)
                ->width(100),
            Column::make('last_login_formatted')
                ->title('Last Login')
                ->orderable(false)
                ->searchable(false)
                ->width(120),
            Column::computed('action')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}