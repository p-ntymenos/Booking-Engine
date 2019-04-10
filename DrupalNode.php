<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrupalNode extends Model
{
    //
    protected $connection = 'mysql_drupal';
    public $table = 'node';
}
