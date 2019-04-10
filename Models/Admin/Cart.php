<?php

namespace App\Models\Admin;

use Eloquent as Model;

/**
* Class Cart
* @package App\Models\Admin
* @version April 2, 2017, 1:43 am UTC
*/
class Cart extends Model
{
    
    public $table = 'cart';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    protected $attributes = array(
    'Step' => 0,
    'Adults' => '{"Amount":"2", "Data":[]}',
    'Children' => '{"Amount":"0", "Data": [{"DOB":""}] }',
    
    );
    
    public $fillable = [
    'Step',
    'SessionId',
    'CruiseCode',
    'IterCode',
    'ReservationInfo',
    'CruiseInfo',
    'MarketInfo',
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
    * The attributes that should be casted to native types.
    *
    * @var array
    */
    protected $casts = [
    'id' => 'integer',
    'Step' => 'integer',
    'SessionId' => 'string',
    'CruiseCode' => 'string',
    'IterCode' => 'string',
    'ReservationInfo' => 'array',
    'CruiseInfo' => 'array',
    'MarketInfo' => 'array',
    'Account' => 'array',
    'Adults' => 'array',
    'Children' => 'array',
    'Staterooms' => 'array',
    'Excursions' => 'array',
    'Services' => 'array',
    'DrinkPackage' => 'array',
    'Insurance' => 'array'
    ];
    
    /**
    * Validation rules
    *
    * @var array
    */
    public static $rules = [
    
    ];
    
    
}