<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

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
}
