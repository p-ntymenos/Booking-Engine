<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\AppBaseController;

class DocumentationsController extends AppBaseController
{
    //
    public function index(){
        return view('admin.documentation.index');
    }
}
