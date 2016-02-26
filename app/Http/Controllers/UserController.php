<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Providers\CryptoServiceProvider as CryptoService;
use App\Providers\UserServiceProvider as UserService;
use Redirect;

class UserController extends Controller
{
    public function dashboard_POST(Request $request){
        //login check
        if( !UserService::isLoggedIn() ){  return CryptoService::redirect('#login'); }

        return CryptoService::loadPage($request, 'user.dashboard');
    }

    public function contacts_POST(Request $request){
        //login check
        if( !UserService::isLoggedIn() ){  return CryptoService::redirect('#login'); }

        return CryptoService::loadPage($request, 'user.contacts');
    }

    public function account_POST(Request $request){
        //login check
        if( !UserService::isLoggedIn() ){  return CryptoService::redirect('#login'); }

        return CryptoService::loadPage($request, 'user.account');
    }
}
