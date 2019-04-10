<?php

namespace App\Repositories\Admin;

use App\Models\Admin\Role;
use InfyOm\Generator\Common\BaseRepository;

class RoleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'display_name',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Role::class;
    }
}
