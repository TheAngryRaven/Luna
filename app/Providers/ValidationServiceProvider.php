<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class ValidationServiceProvider extends ServiceProvider
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

    static function validateRegister( $userData ){
        $validate = Validator::make($userData, [
            'Email'      => 'email|max:255|unique:mysql.t_user,email',
            'UserName'      => 'required|max:255|unique:mysql.t_user,userName',
            'PasswordHash'   => 'required|min:60',
            'PassphraseHash'   => 'required|min:60',
        ]);

        //just debug stuff can remove later
        $out = array();
        $out['errors'] = $validate->errors();
        $out['fails'] = $validate->fails();

        return $out;
    }
}
