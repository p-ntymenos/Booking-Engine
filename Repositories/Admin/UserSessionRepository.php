<?php

namespace App\Repositories\Admin;

use App\Models\Admin\UserSession;
use InfyOm\Generator\Common\BaseRepository;

class UserSessionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'reservation_session',
        'laravel_session'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UserSession::class;
    }
}
