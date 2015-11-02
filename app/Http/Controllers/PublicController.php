<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use App\Providers\CryptoServiceProvider as CryptoService;

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
     * the second the page is finished loading the POST function gets called
     * this is when the encrypted html, is sent to the browser
     *
     * AJAX implies the function is only loaded by scripts
     */

    public function home_GET(Request $request){
        return $this->master($request);
    }

    public function home_POST(Request $request){
        return CryptoService::loadPage($request, 'home');
    }


    public function login_GET(Request $request){
        return $this->master($request);
    }

    public function login_POST(Request $request){
        return CryptoService::loadPage($request, 'login');
    }


    public function register_GET(Request $request){
        return $this->master($request);
    }

    public function register_POST(Request $request){
        return CryptoService::loadPage($request, 'register');
    }

    public function register_AJAX(Request $request){
        //get the AJAX post input
        $input = $request->all();

        $rover = CryptoService::decryptRover( $input['rover'] );

        $response = array(
            'status' => true,
            'message' => 'Response received',
            'result' => null
        );

        return $rover;
    }
}
