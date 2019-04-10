<?php

namespace App\Models\Admin;

use Eloquent as Model;

/**
 * Class ManageCart
 * @package App\Models\Admin
 * @version April 21, 2017, 11:16 am EEST
 */
class ManageCart extends Model
{

    public $table = 'managebookingcart';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'SessionId',
        'BookingNo',
        'ReservationInfo',
        'CruiseInfo',
        'Excursions',
        'DrinkPackage',
        'Services'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'SessionId' => 'string',
        'ReservationInfo' => 'array',
        'CruiseInfo' => 'array',
        'BookingNo' => 'string',
        'Excursions' => 'array',
        'DrinkPackage' => 'array',
        'Services' => 'array'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
