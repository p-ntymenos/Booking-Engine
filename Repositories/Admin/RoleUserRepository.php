<?php

namespace App\Repositories\Admin;

use App\Models\Admin\RoleUser;
use InfyOm\Generator\Common\BaseRepository;

class RoleUserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'role_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RoleUser::class;
    }
}
