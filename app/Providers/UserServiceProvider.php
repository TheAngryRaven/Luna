<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use Session;

class UserServiceProvider extends ServiceProvider
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
     * @param $rover
     * @return bool
     *
     * creates the user account
     */
    static function create( $rover ){
        //user info to stick into db
        $user = array(
            'userID'            => md5( rand(100,9000).$rover['UserName'].rand(545,12322) ),
            'userName'          => $rover['UserName'],
            'email'             => $rover['Email'],
            'passwordHash'      => $rover['PasswordHash'],
            'encryptionHash'    => $rover['PassphraseHash'],
        );

        $setting_email = array(
            'userID'    => $user['userID'],
            'type'      => 1 //0: no | 1: per message | 2: daily | TODO: set this up
        );

        $result = DB::transaction(function () use ($user, $setting_email) {
            //insert account info
            DB::insert('INSERT INTO t_user (userID, userName, email, passwordHash, encryptionHash) VALUES (:userID, :userName, :email, :passwordHash, :encryptionHash)', $user);

            //create settings objects
            DB::insert('INSERT INTO t_setting_email (userID, type) VALUES (:userID, :type)', $setting_email);
        });

        return $result;
    }

    /**
     * @param $username
     * @param $password
     * @param $passphrase
     * @return bool
     *
     * Check the DB to see if the user is good to go
     *
     * and setup a session token
     */
    static function login( $username, $password, $passphrase ){
        $auth = array(
            'user' => $username,
            'pass' => $password,
            'phrase' => $passphrase
        );

        $dbCall = DB::select("SELECT userID FROM t_user WHERE userName = :user AND passwordHash = :pass AND encryptionHash = :phrase LIMIT 1", $auth);

        if($dbCall == null){
            /**
             * auth failed
             */
            return false;
        } else {
            /**
             * Correct Password
             * This is when we create our cookie
             */

            $userData = array();
            $userData['uid'] = $dbCall[0]->userID;
            $userData['loggedin'] = true;

            //creates cookie with this array
            Session::set('user', $userData);

            return true;
        }
    }

    static function logout(){
        Session::forget('user');
    }

    /**
     * @return bool
     *
     * Simply checks to see if the user is logged in
     */
    static function isLoggedIn(){
        $user = Session::get( 'user', null );

        if($user == null){
            return false;
        } else {
            return true;
        }
    }
}
