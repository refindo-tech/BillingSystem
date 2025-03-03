<?php

namespace App\DataTables\Tables;

use App\Models\WhatsAppTemplate;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class WhatsAppTemplateDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->eloquent($query)
            ->addColumn('select', function ($template) {
                return '<input type="checkbox" name="template_ids[]" value="' . $template->id . '">';
            })
            // ->addColumn('action', function ($template) {
            //     return '<a href="' . route('setting.whatsapp.edit', $template->id) . '" class="btn btn-sm btn-primary">Edit</a>';
            // })
            ->rawColumns(['select', 'action']);
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(WhatsAppTemplate $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('whatsapp_templates')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0);
    }

    /**
     * Get the dataTable columns definition.
     */
    protected function getColumns()
    {
        return [
            Column::computed('select')
                ->title('<input type="checkbox" id="select-all">')
                ->exportable(false)
                ->printable(false)
                ->width(10)
                ->addClass('text-center'),
            Column::make('id')->title('ID'),
            Column::make('type')->title('Type'),
            Column::make('message')->title('Message'),
        ];
    }


    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'WhatsAppTemplate_' . date('YmdHis');
    }
}
