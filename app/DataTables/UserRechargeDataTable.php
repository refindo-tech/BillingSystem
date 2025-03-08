<?php

namespace App\DataTables;

use App\Models\UserRecharge;
use App\Support\Lang;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserRechargeDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
{
    return (new EloquentDataTable($query))
        ->addColumn('checkbox', function ($row) {
            return '<input type="checkbox" class="row-checkbox w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 transition duration-150 ease-in-out ml-2" value="' . $row->id . '">';
        })
        ->rawColumns(['checkbox'])
        ->addColumn('action', fn ($row) => view('datatable.action.prepaid-user-action', $row))
        ->editColumn('created_at', fn ($row) => Lang::dateTimeFormat($row->created_at))
        ->editColumn('recharged_at', fn ($row) => Lang::dateTimeFormat($row->recharged_at))
        ->editColumn('expired_at', fn ($row) => Lang::dateTimeFormat($row->expired_at))
        ->editColumn('status', fn ($status) => view('datatable.column.recharge-status-label', $status))
        ->setRowId('id');
}



    /**
     * Get the query source of dataTable.
     */
    public function query(UserRecharge $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with('plan:id,name,type')
            ->with('customer:id,fullname')
            ->with('router:id,name')
            ->with('server:id,name');

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($planName = request('plan_name')) {
            $query->whereHas('plan', function ($q) use ($planName) {
                $q->where('name', $planName);
            });
        }

        if ($planType = request('plan_type')) {
            $query->whereHas('plan', function ($q) use ($planType) {
                $q->where('type', $planType);
            });
        }

        if ($validityCycle = request('validity_cycle')) {
            $query->where('validity_cycle', $validityCycle);
        }

        if ($routerName = request('router_name')) {
            $query->whereHas('router', function ($q) use ($routerName) {
                $q->where('name', $routerName);
            });
        }

        if ($serverName = request('server_name')) {
            $query->whereHas('server', function ($q) use ($serverName) {
                $q->where('name', $serverName);
            });
        }

        return $query;
    }


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->ajax([
                'url' => '',
                'data' => 'function(d) {
                d.status = $("#filter-status").val();
                d.plan_name = $("#filter-plan-name").val();
                d.plan_type = $("#filter-plan-type").val();
                d.validity_cycle = $("#filter-validity-cycle").val();
                d.router_name = $("#filter-router-name").val();
                d.server_name = $("#filter-server-name").val();
            }'
            ])
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
            ]);
    }


    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
{
    return [
        Column::computed('checkbox')
            ->title('<input type="checkbox" id="select-all" class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 transition duration-150 ease-in-out ml-2">')
            ->exportable(false)
            ->printable(false)
            ->orderable(false)
            ->searchable(false)
            ->width(20)
            ->addClass('text-center'),
        Column::make('id')->hidden(),
        Column::make('service_number')->title('Nomor Layanan'),
        Column::make('username'),
        Column::make('pppoe_password')->title('Password'),
        Column::make('customer.fullname')->title('Nama Pelanggan'),
        Column::make('status'),
        Column::make('plan.name'),
        Column::make('plan.type'),
        Column::make('validity_cycle')->title('Siklus'),
        Column::make('created_at')->title('Tanggal Registrasi'),
        Column::make('recharged_at')->title('Tanggal Isi Ulang'),
        Column::computed('expired_at')->title('Tanggal Kadaluarsa'),
        Column::make('method'),
        Column::make('router.name')->title('Router'),
        Column::make('server.name')->title('Server'),
        Column::computed('action'),
    ];
}



    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'UserRecharge_' . date('YmdHis');
    }
}
