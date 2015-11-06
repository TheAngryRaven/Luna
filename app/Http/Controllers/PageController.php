<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Session;
use Redirect;
use App\Providers\CryptoServiceProvider as CryptoService;

use App\Http\Controllers\PublicController as PublicController;

class PageController extends Controller
{
    /**
     * basically on GET you load one function this will do an auth check and the such or redirect or whatever
     *
     * When the page is finished loading the POST function gets called
     * this is when the encrypted html, is sent to the browser
     *
     * AJAX implies the function is only loaded by scripts
     */

    /**
     * @param Request $request
     * @param $pageName
     * @return array
     *
     * fancy function to capture all the ajax calls.....
     */
    public function pageLoad_AJAX(Request $request, $pageName){
        //todo: checks
        //$input = $request->all();
        //$input = $input['pageName'];

        //return the

        $page = str_replace('-', '.', $pageName);

        return CryptoService::loadPage($request, $page);
    }
}
