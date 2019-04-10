<?php

namespace App\Models\Admin;

use Eloquent as Model;

/**
 * Class Insurance
 * @package App\Models\Admin
 * @version April 13, 2017, 10:40 am UTC
 */
class Insurance extends Model
{

    public $table = 'insurances';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'Lang',
        'Code',
        'Title',
        'SubTitle',
        'Body',
        'TermsAndConditions'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'Lang' => 'string',
        'Code' => 'string',
        'Title' => 'string',
        'SubTitle' => 'string',
        'Body' => 'string',
        'TermsAndConditions' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
