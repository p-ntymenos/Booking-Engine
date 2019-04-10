<?php

namespace App\DataTables\Admin;

use App\Models\Admin\ManageCart;
use Form;
use Yajra\Datatables\Services\DataTable;

class ManageCartDataTable extends DataTable
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return $this->datatables
            ->eloquent($this->query())
            ->addColumn('action', 'admin.manage_carts.datatables_actions')
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $manageCarts = ManageCart::query();

        return $this->applyScopes($manageCarts);
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
            'SessionId' => ['name' => 'SessionId', 'data' => 'SessionId'],
            'BookingNo' => ['name' => 'BookingNo', 'data' => 'BookingNo'],
            'Excursions' => ['name' => 'Excursions', 'data' => 'Excursions'],
            'DrinkPackage' => ['name' => 'DrinkPackage', 'data' => 'DrinkPackage'],
            'Services' => ['name' => 'Services', 'data' => 'Services']
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'manageCarts';
    }
}
