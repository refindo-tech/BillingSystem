<?php

namespace App\DataTables;

use App\Models\Ticket;
use App\Support\Lang;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CustomerTicketDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', fn ($row) => view('datatable.action.customer-ticket-action', $row))
            ->editColumn('created_at', fn ($row) => Lang::dateTimeFormat($row->created_at))
            ->editColumn('priority', fn ($priority) => view('datatable.column.ticket-priority-label', $priority))
            ->editColumn('status', fn ($status) => view('datatable.column.ticket-status-label', $status))
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Ticket $model): QueryBuilder
    {
        
        // return $model->newQuery()->with('customer');
        return $model->newQuery()->with('customer')->where('customer_id', auth()->id());
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->dom('Bfrtip')
            ->orderBy(0)
            ->selectStyleSingle();
        // ->buttons([
        //     Button::make('excel'),
        //     Button::make('csv'),
        //     Button::make('pdf'),
        //     Button::make('print'),
        // ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('status')->title('Status'),
            Column::make('ticket_number')->title('Ticket Number'),
            Column::make('customer.fullname')->title('Customer'),
            Column::make('customer.phonenumber')->title('Contact'),
            Column::make('subject')->title('Subject'),
            Column::make('message')->title('Message'),
            Column::make('priority')->title('Priority'),
            Column::make('created_at')->title('Created On')
                ->addClass('whitespace-nowrap'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Ticket_'.date('YmdHis');
    }
}
