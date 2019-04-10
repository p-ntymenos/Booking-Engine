<?php

namespace App\Repositories\Admin;

use App\Models\Admin\ManageCart;
use InfyOm\Generator\Common\BaseRepository;

class ManageCartRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'SessionId',
        'BookingNo',
        'Excursions',
        'DrinkPackage',
        'Services'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ManageCart::class;
    }
}
