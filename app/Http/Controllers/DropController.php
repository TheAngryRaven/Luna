<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use App\Providers\CryptoServiceProvider as CryptoService;

class DropController extends Controller
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

    public function home_GET(Request $request){
        return $this->master($request);
    }

    public function home_POST(Request $request){
        return CryptoService::loadPage($request, 'drop.home');
    }
}
