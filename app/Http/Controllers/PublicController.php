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

        //decrypts json and turns it into an array
        $rover = CryptoService::decryptRover( $input['rover'] );

        //checks against db and such
        $validate = ValidatorService::validateRegister( $rover );

        //return buffer
        $response = null;

        if($validate['fails'] == true){
            //if any validation errors print them to the form but let the user keep previous data
            $errors = $validate['errors']->toArray();

            $response = array(
                'status' => false,
                'message' => 'Errors validating',
                'result' => $errors
            );
        } else {
            $attempt = UserService::create( $rover );

            $response = array(
                'status' => true,
                'message' => 'User has been created, go to login page',
                'result' => null
            );
        }

        $encryptedResponse = json_encode( $response );
        $encryptedResponse = CryptoService::encryptResponse( $input['handshake'], $encryptedResponse );

        return [ 'cipherText' => $encryptedResponse ];
    }
}
