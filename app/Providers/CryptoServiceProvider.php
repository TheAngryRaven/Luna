<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Requests;
use Session;
use App\Libraries\GibberishAES as cryptAES;

class CryptoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() { }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() { }

    /**
     * @param Request $request
     * @param $view
     * @param array $other
     * @return array
     *
     * basically the function that serves as the "Lunar Sub Security" system, the ssl if you wi
     */
    static function loadPage(Request $request, $view, $other = []){
        //get the AJAX post input
        $input = $request->all();

        //setup encryption engine with servers keys
        $sessionKeys = Session::get("serverKeys");
        $privateKey = Session::get("serverPrivate");
        $serverAES = Session::get('serverAES');
        $rsa = new \phpseclib\Crypt\RSA();
        $rsa->setEncryptionMode( $rsa::ENCRYPTION_PKCS1 );

        //decrypt the clients AESkey from the
        $rsa->loadKey( $privateKey );
        $clientAESkey = $rsa->decrypt( base64_decode( $input['handshake'] ) );

        //gather the page to be rendered (and process the php on page)
        $pageHTML = view($view)->render();

        //use AES to encrypt the page data
        $AESEncrypted = cryptAES::enc($pageHTML, $clientAESkey);

        //send the AES encrypted page to client
        return ['cipherText' => $AESEncrypted, 'serverAES' => cryptAES::enc( $serverAES, $clientAESkey ), 'other' => $other];
    }

    /**
     * this sets up the keys for said system
     */
    static function setSessionKeys() {
        //if the user doesnt have a session create one
        if( !Session::has('serverKeys') ){
            //generate keypair for server
            $rsa = new \phpseclib\Crypt\RSA();
            $rsa->setEncryptionMode( $rsa::ENCRYPTION_PKCS1 );
            $serverKeyPair = $rsa->createKey();

            //save keys to session (encrypted on server)
            Session::put('serverKeys', $serverKeyPair );
            Session::put('serverPublic', $serverKeyPair['publickey'] );
            Session::put('serverPrivate', $serverKeyPair['privatekey'] );

            //generate random AES key
            $key = md5( (string)rand(1,20000).(string)rand(1,20000).(string)rand(1,20000) );
            Session::put('serverAES', $key); //static for now
        }
    }

    //simple functions instead of importing all the crypto libs

    static function decryptAES( $data, $password ){
        return cryptAES::dec( $data, $password );
    }

    static function decryptRover( $roverEncrypted ){
        //get server AES key
        $serverAES = Session::get( 'serverAES' );

        //decrypt rover
        $roverString = cryptAES::dec( $roverEncrypted, $serverAES );

        //turn it into an object
        $rover = json_decode( $roverString, true);

        //finally return
        return $rover;
    }

    static function encryptResponse( $handshake, $response ){
        //setup encryption engine with servers keys
        $privateKey = Session::get("serverPrivate");
        $serverAES = Session::get('serverAES');
        $rsa = new \phpseclib\Crypt\RSA();
        $rsa->setEncryptionMode( $rsa::ENCRYPTION_PKCS1 );

        //decrypt the clients AESkey from the
        $rsa->loadKey( $privateKey );
        $clientAESkey = $rsa->decrypt( base64_decode( $handshake ) );

        //use AES to encrypt the data
        $AESEncrypted = cryptAES::enc($response, $clientAESkey);

        return $AESEncrypted;

    }
}
