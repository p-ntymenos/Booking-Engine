<?php

namespace App\Repositories\Admin;

use App\Models\Admin\Account;
use InfyOm\Generator\Common\BaseRepository;

class AccountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     **/
    public function model()
    {
        return Account::class;
    }
}
