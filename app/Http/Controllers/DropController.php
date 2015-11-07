<?php

namespace App\Http\Controllers;

use App\Providers\CryptoServiceProvider;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use App\Providers\CryptoServiceProvider as CryptoService;
use App\Providers\EmailServiceProvider as EmailService;
use URL;
use DB;
use Redirect;

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
     * @return \Illuminate\View\View
     *
     * simply returns the shell
     */
    public function home_GET(Request $request){
        return $this->master($request);
    }

    /**
     * @param Request $request
     * @return array
     *
     * Loads up the initial drop form
     */
    public function home_POST(Request $request){
        return CryptoService::loadPage($request, 'drop.home');
    }

    /**
     * @param Request $request
     * @return null|string
     *
     * ajax sends the encrypted data to the page (function doesn't technically encrypt messages)
     * On success returns the url of the mesasge, or an error message on fail
     * todo: make return array object? for post too
     */
    public function encrypt_AJAX(Request $request){
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

    /**
     * @param Request $request
     * @param $messageID
     * @return \Illuminate\View\View
     *
     * Makes sure messages exists then loads shell
     */
    public function message_GET(Request $request, $messageID){
        $lookup = DB::select("SELECT message FROM t_drop WHERE dropID = :id", ['id' => $messageID]);

        if( $lookup == null ) {
            //does not exit
            return Redirect::to('/');
        } else {
            //yay the message exists!

            //use the regular home function so that session keys are reset and prep to the post function
            return $this->master($request);
        }
    }

    /**
     * @param Request $request
     * @param $messageID
     * @return array|string
     *
     * returns the encrypted page and message data, or the password box if encrypted
     * then loaded again to retreive the encrypted message
     */
    public function message_POST(Request $request, $messageID){
        //this gets triggered if a user as attempted a password check
        if ($request->has('hash'))
        {
            $input = $request->all();

            $serverAES = Session::get( 'serverAES' );
            $passwordHash = CryptoService::decryptAES( $input['hash'], $serverAES );

            $lookup = DB::select("SELECT message, messageType FROM t_drop WHERE dropID = :id AND passwordHash = :hash", ['hash' => $passwordHash, 'id' => $messageID]);

            if ($lookup == null) {
                //does not exit
                return [ 'status' => 302, 'location' => '#drop' ];
            } else {
                $message = $lookup[0]->message;
                $messageType = $lookup[0]->messageType;
                //flash message to session (this is still not sent to the client in plaintext)
                Session::flash('encMessage', $message);
                Session::flash('encMessageType', $messageType);

                //now delete the message from the server
                DB::delete('DELETE FROM t_drop WHERE dropID = :id', ['id' => $messageID]);

                //load the encrypted message
                return CryptoService::loadPage($request, 'drop.message', $messageType);
            }
        } else {
            //attempt to find message
            $lookup = DB::select("SELECT message, messageType, passwordHash FROM t_drop WHERE dropID = :id", ['id' => $messageID]);

            if ($lookup == null) {
                //does not exit
                return [ 'status' => 302, 'location' => '#drop' ];
            } else {
                $message = $lookup[0]->message;
                $messageType = $lookup[0]->messageType;
                $password = $lookup[0]->passwordHash;
                if ($password != null) {
                    //load page with more fancy javascript
                    return CryptoService::loadPage($request, 'drop.password');
                } else {
                    //flash message to session (this is still not sent to the client in plaintext)
                    Session::flash('encMessage', $message);
                    Session::flash('encMessageType', $messageType);

                    //now delete the message from the server
                    DB::delete('DELETE FROM t_drop WHERE dropID = :id', ['id' => $messageID]);

                    //load regular message page
                    return CryptoService::loadPage($request, 'drop.message', $messageType);
                }
            }
        }
    }
}
