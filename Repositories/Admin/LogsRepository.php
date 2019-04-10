<?php

namespace App\Repositories\Admin;

use App\Models\Admin\Logs;
use InfyOm\Generator\Common\BaseRepository;

class LogsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'Message',
        'Percentage',
        'Cron_code',
        'Cron_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Logs::class;
    }
}
