<?php

namespace App\Models\Admin;

use Eloquent as Model;

/**
 * Class Permission
 * @package App\Models\Admin
 * @version April 2, 2017, 2:04 am UTC
 */
class Permission extends Model
{

    public $table = 'permissions';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'name',
        'display_name',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'display_name' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function permissionRoles()
    {
        return $this->hasMany(\App\Models\Admin\PermissionRole::class);
    }
}
