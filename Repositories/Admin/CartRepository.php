<?php

namespace App\Repositories\Admin;

use App\Models\Admin\Cart;
use InfyOm\Generator\Common\BaseRepository;

class CartRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'Step',
        'SessionId',
        'CruiseCode',
        'IterCode',
        'ReservationInfo',
        'CruiseInfo',
        'Account',
        'Adults',
        'Children',
        'Staterooms',
        'Excursions',
        'Services',
        'DrinkPackage',
        'Insurance',
        'Submited'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Cart::class;
    }
}
