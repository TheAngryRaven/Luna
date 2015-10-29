<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EmailServiceProvider extends ServiceProvider
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
     * @param $template
     * @param $to
     * @param $subject
     * @param null $vars
     * @return mixed
     *
     * basic email function returns mail status code
     */
    static function send( $template, $to, $subject, $vars = null){
        $mail = Mail::send('email.'.$template, $vars, function($message) use( $to, $subject ) {
            $message->to($to)->subject( $subject );
        });
        return $mail;
    }

    /**
     * @param $email
     * @param $fullname
     * @return mixed
     *
     * simple function to send an email a link
     */
    static function message( $email, $messageURL ){
        $result = EmailServiceProvider::send(
            'message',
            $email,
            'New Lunar Message',
            [
                'messageURL'      => $messageURL
            ]
        );

        return $result;
    }
}
