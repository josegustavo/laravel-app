<?php

namespace App\Http\Controllers;

use App\Traits\AuthenticatesUsers;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('guest')->except('logout');
    }


}
