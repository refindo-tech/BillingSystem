<?php

namespace App\DataTables\Tables;

use App\Models\WhatsappMessage;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class WhatsAppMessageDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('select', function ($row) {
                return '<input type="checkbox" class="message-checkbox" name="template_ids[]" value="' . $row->id . '">';
            })
            ->rawColumns(['select']);
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(WhatsappMessage $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('whatsapp_message')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('frtip')
            ->orderBy(0);
    }


    /**
     * Get the dataTable columns definition.
     */
    public function getColumns()
    {
        return [
            Column::computed('select')
                ->title('<input type="checkbox" id="select-all">')
                ->exportable(false)
                ->printable(false)
                ->width(10)
                ->addClass('text-center'),
            Column::make('id')->title('ID'),
            Column::make('phone')->title('Phone Number'),
            Column::make('message')->title('Message'),
            Column::make('date')->title('Date'),
            Column::make('status')->title('Status'),
        ];
    }


    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'WhatsAppMessage_' . date('YmdHis');
    }
}
