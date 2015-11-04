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
    /**
     * @param Request $request
     * @return \Illuminate\View\View
     *
     * the shell html and javascript that eventually loads the encrypted pages
     */
    public function master(Request $request){
        //returns nothing, just sets the keys for LSS
        CryptoService::setSessionKeys();

        //return the shell which will loads all the js and the pade loader
        return view('master');
    }

    /**
     * basically on GET you load one function this will do an auth check and the such or redirect or whatever
     *
     * the second the page is finished loading the POST function gets called
     * this is when the encrypted html, is sent to the browser
     *
     * AJAX implies the function is only loaded by scripts
     */

    public function dashboard_GET(Request $request){
        //login check
        if( !UserService::isLoggedIn() ){ return Redirect('/'); }

        return $this->master($request);
    }

    public function dashboard_POST(Request $request){
        //login check
        if( !UserService::isLoggedIn() ){ return Redirect('/'); }

        return CryptoService::loadPage($request, 'user.dashboard');
    }


    public function contacts_GET(Request $request){
        //login check
        if( !UserService::isLoggedIn() ){ return Redirect('/'); }

        return $this->master($request);
    }

    public function contacts_POST(Request $request){
        //login check
        if( !UserService::isLoggedIn() ){ return Redirect('/'); }

        return CryptoService::loadPage($request, 'user.contacts');
    }


    public function account_GET(Request $request){
        //login check
        if( !UserService::isLoggedIn() ){ return Redirect('/'); }

        return $this->master($request);
    }

    public function account_POST(Request $request){
        //login check
        if( !UserService::isLoggedIn() ){ return Redirect('/'); }

        return CryptoService::loadPage($request, 'user.account');
    }
}
