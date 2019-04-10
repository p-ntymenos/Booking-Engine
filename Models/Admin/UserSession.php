<?php

namespace App\Models\Admin;

use Eloquent as Model;

/**
 * Class UserSession
 * @package App\Models\Admin
 * @version April 2, 2017, 2:05 am UTC
 */
class UserSession extends Model
{

    public $table = 'user_sessions';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'reservation_session',
        'laravel_session'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'reservation_session' => 'string',
        'laravel_session' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
