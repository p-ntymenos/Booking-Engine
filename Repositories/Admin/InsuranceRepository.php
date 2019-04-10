<?php

namespace App\Repositories\Admin;

use App\Models\Admin\Insurance;
use InfyOm\Generator\Common\BaseRepository;

class InsuranceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'Lang',
        'Code',
        'Title',
        'SubTitle',
        'Body',
        'TermsAndConditions'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Insurance::class;
    }
}
