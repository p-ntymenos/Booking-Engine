<?php

namespace App\Models\Admin;

use Eloquent as Model;

/**
 * Class Logs
 * @package App\Models\Admin
 * @version April 7, 2017, 9:32 am UTC
 */
class Logs extends Model
{

    public $table = 'logs';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'Message',
        'MessageResponse',
        'SessionId',
        'Cron_code',
        'Cron_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'Message' => 'string',
        'MessageResponse' => 'string',
        'SessionId' => 'string',
        'Cron_code' => 'string',
        'Cron_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
