<?php

namespace App\Http\Controllers;

use App\Providers\UserServiceProvider;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Redirect;

use App\Providers\CryptoServiceProvider as CryptoService;
use App\Providers\ValidationServiceProvider as ValidatorService;
use App\Providers\UserServiceProvider as UserService;

class PublicController extends Controller
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
     * When the page is finished loading the POST function gets called
     * this is when the encrypted html, is sent to the browser
     *
     * AJAX implies the function is only loaded by scripts
     */

    public function home_GET(Request $request){
        return $this->master($request);
    }

    public function home_POST(Request $request){
        //return CryptoService::loadPage($request, 'home');
        return [ 'status' => 302, 'location' => '#drop' ];
    }

    public function login_POST(Request $request){
        return CryptoService::loadPage($request, 'login');
    }

    public function register_POST(Request $request){
        return CryptoService::loadPage($request, 'register');
    }

    public function logoff_GET(Request $request){
        UserService::logout();
        return redirect('/');
    }
}
