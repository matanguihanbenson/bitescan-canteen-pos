<?php

namespace App\DataTables;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProductsDataTable extends DataTable
{
    
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function($product) {
                return '<input type="checkbox" name="product_checkbox[]" class="product-checkbox" value="'.$product->id.'">';
            })
            ->addColumn('product_details', function($row) {
                return view('components.products.product-details', compact('row'))->render();
            })
            ->addColumn('category_info', function($row) {
                return view('components.products.category-info', ['row' => $row])->render();
            })
            ->addColumn('pricing_details', function($row) {
                return view('components.products.pricing', compact('row'))->render();
            })
            ->addColumn('status_badge', function($row) {
                return $row->status === Product::STATUS_ACTIVE 
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Deactivated</span>';
            })
            ->addColumn('action', function($row) {
                return view('components.products.action', compact('row'))->render();
            })
            ->editColumn('product_description', function($row) {
                return $row->product_description ?? '<span class="text-muted">No description</span>';
            })
            ->rawColumns(['checkbox', 'product_details', 'category_info', 'pricing_details', 'status_badge', 'action', 'product_description'])
            ->setRowId('id');
    }

    
    public function query(Product $model): QueryBuilder
    {
        return $model->newQuery()
            ->with([
                'category',
                'product_subcategories' => function($query) {
                    $query->with('subcategory');
                },
                'inventory'
            ]);
    }

    
    public function html(): HtmlBuilder
    {
        $baseUrl = url('/');
        
        return $this->builder()
            ->setTableId('products-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons([
                [
                    'extend' => 'excel',
                    'className' => 'btn btn-success btn-sm',
                    'text' => '<i class="bi bi-file-earmark-excel"></i> Excel'
                ],
                [
                    'text' => '<i class="bi bi-filetype-csv"></i> CSV',
                    'className' => 'btn btn-info btn-sm',
                    'action' => "function(e, dt, node, config) {
                        window.location.href = '{$baseUrl}/inventory/products/export-csv';
                    }"
                ],
                [
                    'text' => '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    'className' => 'btn btn-danger btn-sm',
                    'action' => "function(e, dt, node, config) {
                        window.location.href = '{$baseUrl}/inventory/products/export-pdf';
                    }"
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
            ]);
    }

    
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')
                  ->exportable(false)
                  ->printable(false)
                  ->width(20)
                  ->addClass('text-center align-middle')
                  ->title('<input type="checkbox" id="check-all-products">'),
            Column::make('product_details')
                  ->title('Product Information')
                  ->orderable(false)
                  ->searchable(true)
                  ->width(250)
                  ->addClass('align-middle'),
            Column::make('category_info')
                  ->title('Category')
                  ->orderable(false)
                  ->searchable(false)
                  ->width(150)
                  ->addClass('align-middle'),
            Column::make('pricing_details')
                  ->title('Pricing')
                  ->orderable(false)
                  ->searchable(false)
                  ->width(200)
                  ->addClass('align-middle'),
            Column::make('product_description')
                  ->title('Description')
                  ->width(150)
                  ->addClass('align-middle'),
            Column::computed('status_badge')
                  ->title('Status')
                  ->addClass('text-center align-middle'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(120)
                  ->addClass('text-center align-middle'),
        ];
    }

    
    protected function filename(): string
    {
        return 'Products_' . date('YmdHis');
    }
}
