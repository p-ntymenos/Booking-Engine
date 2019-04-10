<?php

namespace App\DataTables\Admin;

use App\Models\Admin\Cart;
use Form;
use Yajra\Datatables\Services\DataTable;

class CartDataTable extends DataTable
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return $this->datatables
            ->eloquent($this->query())
            ->addColumn('action', 'admin.carts.datatables_actions')
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $carts = Cart::query();

        return $this->applyScopes($carts);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->addAction(['width' => '10%'])
            ->ajax('')
            ->parameters([
                'dom' => 'Bfrtip',
                'scrollX' => false,
                'buttons' => [
                    'print',
                    'reset',
                    'reload',
                    [
                         'extend'  => 'collection',
                         'text'    => '<i class="fa fa-download"></i> Export',
                         'buttons' => [
                             'csv',
                             'excel',
                             'pdf',
                         ],
                    ],
                    'colvis'
                ]
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    private function getColumns()
    {
        return [
            'created_at' => ['name' => 'created_at', 'data' => 'created_at'],
            'Step' => ['name' => 'Step', 'data' => 'Step'],
            'SessionId' => ['name' => 'SessionId', 'data' => 'SessionId'],
            //'CruiseCode' => ['name' => 'CruiseCode', 'data' => 'CruiseCode'],
            //'IterCode' => ['name' => 'IterCode', 'data' => 'IterCode'],
            'ReservationInfo' => ['name' => 'ReservationInfo', 'data' => 'ReservationInfo'],
            'CruiseInfo' => ['name' => 'CruiseInfo', 'data' => 'CruiseInfo'],
            // 'Account' => ['name' => 'Account', 'data' => 'Account'],
            // 'Adults' => ['name' => 'Adults', 'data' => 'Adults'],
            // 'Children' => ['name' => 'Children', 'data' => 'Children'],
            // 'Staterooms' => ['name' => 'Staterooms', 'data' => 'Staterooms'],
            // 'Excursions' => ['name' => 'Excursions', 'data' => 'Excursions'],
            // 'Services' => ['name' => 'Services', 'data' => 'Services'],
            // 'DrinkPackage' => ['name' => 'DrinkPackage', 'data' => 'DrinkPackage'],
            // 'Insurance' => ['name' => 'Insurance', 'data' => 'Insurance'],
            'Submited' => ['name' => 'Submited', 'data' => 'Submited']
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'carts';
    }
}
