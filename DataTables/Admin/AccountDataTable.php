<?php

namespace App\DataTables\Admin;

use App\Models\Admin\Account;
use Form;
use Yajra\Datatables\Services\DataTable;

class AccountDataTable extends DataTable
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return $this->datatables
            ->eloquent($this->query())
            ->addColumn('action', 'admin.accounts.datatables_actions')
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $accounts = Account::query();

        return $this->applyScopes($accounts);
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
            'FirstName' => ['name' => 'FirstName', 'data' => 'FirstName'],
            'LastName' => ['name' => 'LastName', 'data' => 'LastName']//,
            // 'DateOfBirth' => ['name' => 'DateOfBirth', 'data' => 'DateOfBirth'],
            // 'IsChild' => ['name' => 'IsChild', 'data' => 'IsChild'],
            // 'Gender' => ['name' => 'Gender', 'data' => 'Gender'],
            // 'PassportNumber' => ['name' => 'PassportNumber', 'data' => 'PassportNumber'],
            // 'PassportExpiration' => ['name' => 'PassportExpiration', 'data' => 'PassportExpiration'],
            // 'Nationality' => ['name' => 'Nationality', 'data' => 'Nationality'],
            // 'Language' => ['name' => 'Language', 'data' => 'Language'],
            // 'MilesAndBonus' => ['name' => 'MilesAndBonus', 'data' => 'MilesAndBonus'],
            // 'MasterAccount' => ['name' => 'MasterAccount', 'data' => 'MasterAccount'],
            // 'Email' => ['name' => 'Email', 'data' => 'Email'],
            // 'Password' => ['name' => 'Password', 'data' => 'Password'],
            // 'Address' => ['name' => 'Address', 'data' => 'Address'],
            // 'Zip' => ['name' => 'Zip', 'data' => 'Zip'],
            // 'Country' => ['name' => 'Country', 'data' => 'Country'],
            // 'Phone' => ['name' => 'Phone', 'data' => 'Phone'],
            // 'Phone2' => ['name' => 'Phone2', 'data' => 'Phone2']
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'accounts';
    }
}
