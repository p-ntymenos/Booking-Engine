<?php

namespace App\Models\Admin;

use Eloquent as Model;

/**
 * Class Account
 * @package App\Models\Admin
 * @version April 2, 2017, 2:06 am UTC
 */
class Account extends Model
{

    public $table = 'account';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'FirstName',
        'LastName',
        'DateOfBirth',
        'IsChild',
        'Gender',
        'PassportNumber',
        'PassportExpiration',
        'Nationality',
        'Language',
        'MilesAndBonus',
        'MasterAccount',
        'Email',
        'Password',
        'Address',
        'Zip',
        'Country',
        'Phone',
        'Phone2'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'FirstName' => 'string',
        'LastName' => 'string',
        'DateOfBirth' => 'string',
        'Gender' => 'string',
        'PassportNumber' => 'string',
        'PassportExpiration' => 'string',
        'Nationality' => 'string',
        'Language' => 'string',
        'MilesAndBonus' => 'string',
        'Email' => 'string',
        'Password' => 'string',
        'Address' => 'string',
        'Zip' => 'string',
        'Country' => 'string',
        'Phone' => 'string',
        'Phone2' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
