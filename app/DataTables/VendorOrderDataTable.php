<?php

namespace App\DataTables;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class VendorOrderDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($query) {
                $showBtn = "<a href='" . route('vendor.orders.show', $query->id) . "' class='btn btn-primary'><i class='far fa-eye'></i></a>";

                return $showBtn;
            })
            ->addColumn('customer', function ($query) {
                return $query->user->username;
            })
            ->addColumn('date', function ($query) {
                return date('d-M-Y', strtotime($query->created_at));
            })
            ->addColumn('payment_status', function ($query) {
                if ($query->payment_status == 1) {
                    return '<span class="badge bg-success">completed</span>';
                } else {
                    return '<span class="badge bg-warning">pending</span>';
                }
            })
            ->addColumn('order_status', function ($query) {
                switch ($query->order_status) {
                    case 'pending':
                        return '<span class="badge bg-warning">Pending</span>';
                        break;
                    case 'processed_and_ready_to_ship':
                        return '<span class="badge bg-info">Processed</span>';
                        break;
                    case 'dropped_off':
                        return '<span class="badge bg-info">Dropped</span>';
                        break;
                    case 'shipped':
                        return '<span class="badge bg-primary">Shipped</span>';
                        break;
                    case 'out_for_delivery':
                        return '<span class="badge bg-primary">Out for Delivery</span>';
                        break;
                    case 'delivered':
                        return '<span class="badge bg-success">Delivered</span>';
                        break;
                    case 'canceled':
                        return '<span class="badge bg-danger">Canceled</span>';
                        break;
                    default:
                        # code...
                        break;
                }
                return '<span class="badge bg-warning">' . $query->order_status . '</span>';
            })
            ->addColumn('amount', function ($query) {
                return $query->currency_icon . $query->amount;
            })
            ->rawColumns(['action', 'order_status', 'payment_status'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Order $model): QueryBuilder
    {
        return $model::whereHas('orderProducts', function($query){
            $query->where('vendor_id', Auth::user()->vendor->id);
        })->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('vendororder-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('invoice_id'),
            Column::make('customer'),
            Column::make('date')->width(100),
            Column::make('product_qty'),
            Column::make('amount'),
            Column::make('order_status'),
            Column::make('payment_status'),
            Column::make('payment_method'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center')->width(200),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'VendorOrder_' . date('YmdHis');
    }
}
