<?php

namespace App\DataTables;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InventoryDataTable extends DataTable
{
    
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function($row) {
                return '<input type="checkbox" class="dt-checkboxes" value="'.$row->id.'">';
            })
            ->addColumn('product_details', function($row) {
                return view('components.inventory.product-details', compact('row'))->render();
            })
            ->addColumn('stock_status', function($row) {
                return view('components.inventory.stock-status', compact('row'))->render();
            })
            ->addColumn('pricing_details', function($row) {
                return view('components.inventory.pricing', compact('row'))->render();
            })
            ->addColumn('action', function($row) {
                return view('inventory.action', compact('row'))->render();
            })
            ->editColumn('exp_date', function($row) {
                return $row->exp_date ? $row->exp_date->format('M d, Y') : 'N/A';
            })
            ->rawColumns(['checkbox', 'product_details', 'stock_status', 'pricing_details', 'action'])
            ->setRowId('id');
    }

    
    public function query(Inventory $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['product', 'product.category'])
            ->select('inventories.*');

        if (request()->has('stock_filter')) {
            switch (request()->stock_filter) {
                case 'out_of_stock':
                    $query->where('current_stock', 0);
                    break;
                case 'low_stock':
                    $query->whereColumn('current_stock', '<=', 'min_stock')
                          ->where('current_stock', '>', 0);
                    break;
                case 'in_stock':
                    $query->where('current_stock', '>', 0);
                    break;
            }
        }

        if (request()->has('category_id') && request()->category_id != '') {
            $query->whereHas('product', function($q) {
                $q->where('category_id', request()->category_id);
            });
        }

        if (request()->has('exp_date_filter')) {
            switch (request()->exp_date_filter) {
                case 'expired':
                    $query->where('exp_date', '<', now());
                    break;
                case 'expiring_soon':
                    $query->whereBetween('exp_date', [now(), now()->addDays(30)]);
                    break;
                case 'valid':
                    $query->where('exp_date', '>', now());
                    break;
            }
        }

        return $query;
    }

    
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('inventory-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax('', null, [
                        'stock_filter' => 'function() { return $("#stock-filter").val(); }',
                        'category_id' => 'function() { return $("#category-filter").val(); }',
                        'exp_date_filter' => 'function() { return $("#expiry-filter").val(); }'
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
                                'text' => '<i class="bi bi-file-earmark-pdf"></i> PDF',
                                'className' => 'btn btn-danger btn-sm',
                                'action' => 'function(e, dt, node, config) {
                                    $("#exportFilterModal").modal("show");
                                }'
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
                                    window.LaravelDataTables['inventory-table'].draw();
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
                  ->width(20)
                  ->orderable(false)
                  ->searchable(false)
                  ->exportable(false)
                  ->printable(false),
            Column::make('product_details')
                  ->title('Product Details')
                  ->orderable(false)
                  ->searchable(false)
                  ->width(250),
            Column::make('stock_status')
                  ->title('Stock Status')
                  ->orderable(false)
                  ->searchable(false)
                  ->width(150),
            Column::make('exp_date')
                  ->title('Expiry Date')
                  ->width(100),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(100)
                  ->addClass('text-center'),
        ];
    }

    
    protected function filename(): string
    {
        return 'Inventory_' . now()->timezone('Asia/Manila')->format('YmdHis');
    }
}
