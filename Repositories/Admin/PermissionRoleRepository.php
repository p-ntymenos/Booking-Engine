<?php

namespace App\Repositories\Admin;

use App\Models\Admin\PermissionRole;
use InfyOm\Generator\Common\BaseRepository;

class PermissionRoleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'permission_id',
        'role_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PermissionRole::class;
    }
}
