<?php

namespace App\Http\Controllers;

use App\Providers\CryptoServiceProvider;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use App\Providers\CryptoServiceProvider as CryptoService;
use URL;
use DB;
use Redirect;

class DropController extends Controller
{
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
     * @param $messageID
     * @return array|string
     *
     * returns the encrypted page and message data, or the password box if encrypted
     * then loaded again to retreive the encrypted message
     */
    public function message_POST(Request $request, $messageID){
        //this gets triggered if a user as attempted a password check
        if($request->has('human')){

            //attempt to find message
            $lookup = DB::select("SELECT message, messageType, passwordHash FROM t_drop WHERE dropID = :id", ['id' => $messageID]);

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

                //load regular message page
                return CryptoService::loadPage($request, 'drop.message', (int)$messageType);
            }
        }

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
                return CryptoService::loadPage($request, 'drop.message', (int)$messageType);
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
                    //simple "human check"
                    return CryptoService::loadPage($request, 'drop.view', (int)$messageType);
                }
            }
        }
    }
}
