<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class AdminController extends \App\Http\Controllers\AppBaseController
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        // $user = Auth::user();
        // echo $user->hasRole('admin');
        // if ($user)
        // {
        //     echo "Hello $user->name";
        // }
        return view('admin.dashboard');
    }
}