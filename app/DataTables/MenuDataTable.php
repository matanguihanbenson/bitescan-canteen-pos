<?php

namespace App\DataTables;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MenuDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function($row) {
                return view('components.table.checkbox', ['id' => $row->id])->render();
            })
            ->addColumn('menu_details', function($row) {
                return view('components.menu.menu-details', compact('row'))->render();
            })
            ->addColumn('category_details', function($row) {
                return view('components.menu.category-details', compact('row'))->render();
            })
            ->addColumn('status_badge', function($row) {
                return view('components.menu.status-badge', compact('row'))->render();
            })
            ->addColumn('price_details', function($row) {
                return view('components.menu.pricing', compact('row'))->render();
            })
            ->addColumn('action', function($row) {
                return view('menu.action', compact('row'))->render();
            })
            ->editColumn('updated_at', function($row) {
                return $row->updated_at->format('M d, Y');
            })
            ->rawColumns(['checkbox', 'menu_details', 'category_details', 'status_badge', 'price_details', 'action'])
            ->setRowId('id');
    }

    public function query(Menu $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['category', 'subcategory'])
            ->select('menus.*');

        if (request()->has('status_filter') && request()->status_filter != '') {
            if (request()->status_filter === 'archived') {
                $query->where('is_archived', "true");
            } else {
                $query->where('status', request()->status_filter)
                      ->where('is_archived', "false");
            }
        } else {
            $query->where('is_archived', "false");
        }

        if (request()->has('category_id') && request()->category_id != '') {
            $query->where('menus.menucategory_id', request()->category_id);
        }

        if (request()->has('subcategory_id') && request()->subcategory_id != '') {
            $query->where('menus.menusubcategory_id', request()->subcategory_id);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('menu-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax('', null, [
                        'status_filter' => 'function() { return $("#status-filter").val(); }',
                        'category_id' => 'function() { return $("#category-filter").val(); }',
                        'subcategory_id' => 'function() { return $("#subcategory-filter").val(); }'
                    ])
                    ->dom("<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" .
                         "<'row'<'col-sm-12 col-md-6'B><'col-sm-12'tr>>" .
                         "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>")
                    ->orderBy(1)
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
                            ],
                            [
                                'extend' => 'reset',
                                'className' => 'btn btn-warning btn-sm',
                                'text' => '<i class="bi bi-arrow-counterclockwise"></i> Reset'
                            ],
                            [
                                'extend' => 'reload',
                                'className' => 'btn btn-info btn-sm',
                                'text' => '<i class="bi bi-arrow-clockwise"></i> Reload'
                            ],
                        ],
                        'select' => false,
                        'rowCallback' => "function(row, data) {
                            if ($(row).find('.dt-checkboxes').prop('checked')) {
                                $(row).addClass('selected-row');
                            }
                            $(row).addClass('align-middle');
                        }",
                        'drawCallback' => "function() {
                            $('.dt-checkboxes').prop('checked', false);
                            $('.check-all').prop('checked', false);
                            $('tr').removeClass('selected-row');
                            $('.bulk-actions').addClass('d-none');
                            $('.card-tools').removeClass('d-none');
                        }",
                        'initComplete' => "function() {
                            var filters = $('#table-filters');
                            this.api().columns().every(function() {
                                filters.on('change', 'select', function() {
                                    window.LaravelDataTables['menu-table'].draw();
                                });
                            });
                        }"
                    ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('checkbox')
                  ->title('<input type="checkbox" class="check-all">')
                  ->addClass('text-center dt-checkboxes-cell')
                  ->width(30)
                  ->orderable(false)
                  ->searchable(false)
                  ->exportable(false)
                  ->printable(false),
            Column::make('menu_details')
                  ->title('Menu Item')
                  ->orderable(true)
                  ->searchable(true)
                  ->width(150),
            Column::make('category_details')
                  ->title('Categories')
                  ->orderable(true)
                  ->searchable(true)
                  ->width(150),
            Column::make('price_details')
                  ->title('Price')
                  ->orderable(true)
                  ->searchable(false)
                  ->width(80),
            Column::make('status_badge')
                  ->title('Status')
                  ->orderable(true)
                  ->searchable(false)
                  ->width(80),
            Column::make('updated_at')
                  ->title('Last Updated')
                  ->width(100),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(80)
                  ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Menu_' . now()->timezone('Asia/Manila')->format('YmdHis');
    }
}
