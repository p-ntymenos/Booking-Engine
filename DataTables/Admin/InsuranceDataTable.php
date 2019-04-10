<?php

namespace App\DataTables\Admin;

use App\Models\Admin\Insurance;
use Form;
use Yajra\Datatables\Services\DataTable;

class InsuranceDataTable extends DataTable
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return $this->datatables
            ->eloquent($this->query())
            ->addColumn('action', 'admin.insurances.datatables_actions')
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $insurances = Insurance::query();

        return $this->applyScopes($insurances);
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
            'Lang' => ['name' => 'Lang', 'data' => 'Lang'],
            'Code' => ['name' => 'Code', 'data' => 'Code'],
            'Title' => ['name' => 'Title', 'data' => 'Title'],
            'SubTitle' => ['name' => 'SubTitle', 'data' => 'SubTitle'],
            'Body' => ['name' => 'Body', 'data' => 'Body'],
            'TermsAndConditions' => ['name' => 'TermsAndConditions', 'data' => 'TermsAndConditions']
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'insurances';
    }
}
