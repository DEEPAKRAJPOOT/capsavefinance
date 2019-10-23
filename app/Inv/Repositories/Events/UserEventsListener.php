<?php

namespace App\Inv\Repositories\Events;

use Mail;
use Illuminate\Queue\SerializesModels;
use App\Inv\Repositories\Factory\Events\BaseEvent;
use App\Inv\Repositories\Models\Master\EmailTemplate;

class UserEventsListener extends BaseEvent
{

    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Event that would be fired on a user login
     *
     * @param object $user Logged in user object
     *  
     */
    public function onLoginSuccess($user)
    {
        $user = unserialize($user);
        self::addActivityLog(1, trans('activity_messages.login_sucessfully'),
            $user);
    }

    /**
     * Event that would be fired on a failed login attempt
     *
     * @param array $user email data
     *
     * @since 0.1
     */
    public function onFailedLogin($user)
    {
        $user = unserialize($user);
        self::addActivityLog(2, trans('activity_messages.login_failed'), $user);
    }

    /**
     * Event that would be fired on a user logout
     *
     * @param object $user Logged in user object
     *
     * @since 0.1
     */
    public function onLogoutSuccess($user)
    {
        $user = unserialize($user);
        self::addActivityLog(3, trans('activity_messages.logout_sucessfully'),
            $user);
    }

    /**
     * Event that would be fired on a user verification
     *
     * @param object $user user data
     */
    public function onVerifyUser_old($user)
    {
        $user = unserialize($user);

        //Send mail to Case Manager
        $email_content = EmailTemplate::getEmailTemplate("VERIFYUSEREMAIL");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%otp'],
                [ucwords($user['name']),$user['otp']],
                $email_content->message
            );
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content) {
                $message->from(config('common.FRONTEND_FROM_EMAIL'),
                    config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($user["email"], $user["name"])->subject($email_content->subject);
            });
        }
    }

    public function onVerifyUser($user)
    {
        $user = unserialize($user);
        //Send mail to Case Manager
        $email_content1 = EmailTemplate::getEmailTemplate("VERIFYUSEREMAIL");
        
//echo $email_content;

        if ($email_content1) {
            $mail_body = str_replace(
                ['%name', '%link'],
                [ucwords($user['name']),
                link_to($user['vlink'], 'here')],
                $email_content1->message
            );
            

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content1) {
                $message->from(config('common.FRONTEND_FROM_EMAIL'),
                    config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($user["email"], $user["name"])->subject($email_content1->subject);
            });
          // dd($email_content1, $mail_body);

        }

    }

    /**
     * Event that would be fired on a user verification
     *
     * @param object $user user data
     */
    public function onUserRegistration($user)
    {
        $user = unserialize($user);

       

        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("USER_REGISTERED");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%email','%password'],
                [ucwords($user['name']),$user['email'],$user['password']],
                $email_content->message
            );

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content) {
                $message->from('admin@dexter.com',
                    'Rent Alpha');
                $message->to($user["email"], $user["name"])->subject($email_content->subject);
            });
        }
    }

     /**
     * Event that would be fired on a user verification email
     *
     * @param object $user user data
     */

    public function onSendOtp($user)
    {
        $user = unserialize($user);
        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("OTP_SEND");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%otp'],
                [ucwords($user['name']),$user['otp']],
                $email_content->message
            );

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content) {
                $message->from('admin@dexter.com','Rent Alpha');
                $message->to($user["email"], $user["name"])->subject($email_content->subject);
            });
        }
    }

    
    
    public function onForgotPassword($user) {
        $user = unserialize($user);

        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("FORGOT_PASSWORD");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%reset_link'],
                [ucwords($user['name']),$user['reset_link']],
                $email_content->message
            );

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content) {
                $message->from(config('common.FRONTEND_FROM_EMAIL'),
                    config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($user["email"], $user["name"])->subject($email_content->subject);
            });
        }
    }
    
    public function onResetPasswordSuccess($user) {
        $user = unserialize($user);

        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("RESET_PASSWORD_SUCCESSS");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name'],
                [ucwords($user['name'])],
                $email_content->message
            );

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content) {
                $message->from(config('common.FRONTEND_FROM_EMAIL'),
                    config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($user["email"], $user["name"])->subject($email_content->subject);
            });
        }
    }

    
    /**
     * Event subscribers
     *
     * @param mixed $events
     */
    public function subscribe($events)
    {

        $events->listen(
            'user.login.success',
            'App\Inv\Repositories\Events\UserEventsListener@onLoginSuccess'
        );

        $events->listen(
            'user.logout.success',
            'App\Inv\Repositories\Events\UserEventsListener@onLogoutSuccess'
        );

        $events->listen(
            'user.login.failed',
            'App\Inv\Repositories\Events\UserEventsListener@onFailedLogin'
        );
        $events->listen(
            'user.email.verify',
            'App\Inv\Repositories\Events\UserEventsListener@onVerifyUser'
        );
        $events->listen(
            'user.registered',
            'App\Inv\Repositories\Events\UserEventsListener@onUserRegistration'
        );
        
       $events->listen(
            'user.sendotp',
            'App\Inv\Repositories\Events\UserEventsListener@onSendOtp'
        );
        
        $events->listen(
            'admin.disapproved',
            'App\Inv\Repositories\Events\UserEventsListener@onDisApprovedAdmin'
        );
        
       
        
        
        
        
        $events->listen(
            'forgot_password',
            'App\Inv\Repositories\Events\UserEventsListener@onForgotPassword'
        );
        $events->listen(
            'RESET_PASSWORD_SUCCESSS',
            'App\Inv\Repositories\Events\UserEventsListener@onResetPasswordSuccess'
        );
        
        //
    }
}
