<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Providers\CryptoServiceProvider as CryptoService;
use App\Providers\ValidationServiceProvider as ValidatorService;
use App\Providers\UserServiceProvider as UserService;
use App\Providers\EmailServiceProvider as EmailService;
use Session;
use URL;
use DB;

class AjaxController extends Controller
{
    /**
     * @param Request $request
     * @return array
     *
     * function called when someone attempts to login
     */
    public function login_AJAX(Request $request){
        //get the AJAX post input
        $input = $request->all();

        //decrypts json and turns it into an array
        $rover = CryptoService::decryptRover( $input['rover'] );

        //attempt login
        $auth = UserService::login( $rover['UserName'], $rover['PasswordHash'], $rover['PassphraseHash'] );

        //what did it do
        $response = null;
        if( $auth == true ) {
            //yay logged in
            $response = array(
                'status' => true,
                'message' => 'User Authenticated',
                'result' => null
            );
        } else {
            //lol nope
            $response = array(
                'status' => false,
                'message' => 'Failed to Authenticate',
                'result' => null
            );
        }

        //prepare and encrypt the ajax response
        $encryptedResponse = json_encode( $response );
        $encryptedResponse = CryptoService::encryptResponse( $input['handshake'], $encryptedResponse );

        return [ 'cipherText' => $encryptedResponse ];
    }

    /**
     * @param Request $request
     * @return array
     *
     * function called whenever someone attempts to create an account
     */
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

        //prepare and encrypt the ajax response
        $encryptedResponse = json_encode( $response );
        $encryptedResponse = CryptoService::encryptResponse( $input['handshake'], $encryptedResponse );

        return [ 'cipherText' => $encryptedResponse ];
    }

    /**
     * @param Request $request
     * @return null|string
     *
     * ajax sends the encrypted data to the page (function doesn't technically encrypt messages)
     * On success returns the url of the mesasge, or an error message on fail
     * todo: make return array object? for post too
     */
    public function encryptDrop_AJAX(Request $request){
        //get the AJAX post input
        $input = $request->all();

        //generateMessageID todo: make more complex
        $buffer = rand( 1, 30000 );
        $buffer = md5( $buffer );
        $id = substr( $buffer, 5, 16 );

        //get server AES key
        $serverAES = Session::get( 'serverAES' );
        $encryptedMessage = $input['message'];
        $encryptedEmail = $input['email'];
        $encryptedHash = $input['password'];

        //image or text yo
        $messageType = $input['type'];
        if( $messageType == 'text' ){
            $messageType = 0;
        } else {
            $messageType = 1;
        }

        //decrypt client message
        //$message = cryptAES::dec( $encryptedMessage, $serverAES );
        $message = CryptoService::decryptAES( $encryptedMessage, $serverAES );

        //checks encrypted data size
        if( strlen($message) < 2000000 ) {
            $email = null;
            $hash = null;
            $url = null;

            if ($encryptedEmail != false) {
                $email = CryptoService::decryptAES( $encryptedEmail, $serverAES );

                //this is what we return, quick fix to tell them its been emailed
                $url = 'sent';

                //send notification email
                //EmailService::message($email, URL::to('message/' . $id));
                EmailService::message($email, URL::to('/').'#message/'.$id);
            } else {
                $url = URL::to('/').'#message/'.$id;
            }

            if ($encryptedHash != false) {
                $hash = CryptoService::decryptAES( $encryptedHash, $serverAES );
            } else {
                //check plaintext size
                if (strlen($message) > 1024) {
                    return 'Message To Large';
                }
            }

            //save message to database
            $dbInput = [
                'id' => $id,
                'message' => $message,
                'hash' => $hash,
                'type' => $messageType,
                'uploadDate' => date('Y-m-d G:i:s')
            ];
            DB::insert('INSERT INTO t_drop (dropID, creationDate, passwordhash, messageType, message) VALUES (:id, :uploadDate, :hash, :type, :message )', $dbInput);

            //return resulting link
            return $url;
        } else {
            return [ 'status' => 500, 'message' => 'Image too large too upload' ];
        }
    }
}
