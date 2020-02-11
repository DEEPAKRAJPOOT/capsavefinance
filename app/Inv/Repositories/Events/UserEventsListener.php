<?php

namespace App\Inv\Repositories\Events;

use Mail;
use Illuminate\Queue\SerializesModels;
use App\Inv\Repositories\Factory\Events\BaseEvent;
use App\Inv\Repositories\Models\Master\EmailTemplate;
use App\Inv\Repositories\Models\FinanceModel;
use Storage;

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
    public function onVerifyUser_old($user) {
        $this->func_name = __FUNCTION__;
        $user = unserialize($user);
        $email_content = EmailTemplate::getEmailTemplate("VERIFYUSEREMAIL");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%otp'],
                [ucwords($user['name']),$user['otp']],
                $email_content->message
            );
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('SEND_MAIL'));
                        $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                        $message->cc(explode(',', env('SEND_MAIL_CC')));
                    }else{
                        $email = $user["email"];
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'),config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, $user["name"])->subject($email_content->subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $user['name'],
                    'subject' => $email_content->subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }

    public function onVerifyUser($user) {
        $this->func_name = __FUNCTION__;
        $user = unserialize($user);
        $email_content = EmailTemplate::getEmailTemplate("VERIFYUSEREMAIL");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%link'],
                [ucwords($user['name']),
                link_to($user['vlink'], 'here')],
                $email_content->message
            );
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('SEND_MAIL'));
                        $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                        $message->cc(explode(',', env('SEND_MAIL_CC')));
                    }else{
                        $email = $user["email"];
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'),config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, $user["name"])->subject($email_content->subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $user['name'],
                    'subject' => $email_content->subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }

    }

    /**
     * Event that would be fired on a user verification
     *
     * @param object $user user data
     */
    public function onUserRegistration($user) {
        $this->func_name = __FUNCTION__;
        $user = unserialize($user);
        $email_content = EmailTemplate::getEmailTemplate("USER_REGISTERED");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%email','%password'],
                [ucwords($user['name']),$user['email'],$user['password']],
                $email_content->message
            );
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('SEND_MAIL'));
                        $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                        $message->cc(explode(',', env('SEND_MAIL_CC')));
                    }else{
                        $email = $user["email"];
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, $user["name"])->subject($email_content->subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $user['name'],
                    'subject' => $email_content->subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }

     /**
     * Event that would be fired on a user verification email
     *
     * @param object $user user data
     */

    public function onSendOtp($user) {
        $this->func_name = __FUNCTION__; 
        $user = unserialize($user);
        $email_content = EmailTemplate::getEmailTemplate("OTP_SEND");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%otp'],
                [ucwords($user['name']),$user['otp']],
                $email_content->message
            );
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('SEND_MAIL'));
                        $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                        $message->cc(explode(',', env('SEND_MAIL_CC')));
                    }else{
                        $email = $user["email"];
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, $user["name"])->subject($email_content->subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $user['name'],
                    'subject' => $email_content->subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }

    
    
    public function onForgotPassword($user) {
        $this->func_name = __FUNCTION__;
        $user = unserialize($user);
        $email_content = EmailTemplate::getEmailTemplate("FORGOT_PASSWORD");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%reset_link'],
                [ucwords($user['name']),$user['reset_link']],
                $email_content->message
            );

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('SEND_MAIL'));
                        $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                        $message->cc(explode(',', env('SEND_MAIL_CC')));
                    }else{
                        $email = $user["email"];
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, $user["name"])->subject($email_content->subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $user['name'],
                    'subject' => $email_content->subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }
    
    public function onResetPasswordSuccess($user) {
        $this->func_name = __FUNCTION__;
        $user = unserialize($user);
        $email_content = EmailTemplate::getEmailTemplate("RESET_PASSWORD_SUCCESSS");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name'],
                [ucwords($user['name'])],
                $email_content->message
            );

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('SEND_MAIL'));
                        $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                        $message->cc(explode(',', env('SEND_MAIL_CC')));
                    }else{
                        $email = $user["email"];
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, $user["name"])->subject($email_content->subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $user['name'],
                    'subject' => $email_content->subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }

    public function onAnchorRegistUserSuccess($userData) {
        $this->func_name = __FUNCTION__;
        $user = unserialize($userData);
        $email_content = EmailTemplate::getEmailTemplate("ANCHOR_REGISTER_USER_MAIL");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%email','%password'],
                [ucwords($user['name']),$user['email'],$user['password']],
                $email_content->message
            );
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('SEND_MAIL'));
                        $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                        $message->cc(explode(',', env('SEND_MAIL_CC')));
                    }else{
                        $email = $user["email"];
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'),config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, $user["name"])->subject($email_content->subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $user['name'],
                    'subject' => $email_content->subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }

    public function onAgencyUserRegisterSuccess($userData) {
        $this->func_name = __FUNCTION__;
        $user = unserialize($userData);
        $email_content = EmailTemplate::getEmailTemplate("AGENCY_USER_REGISTER_MAIL");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%email','%password'],
                [ucwords($user['name']),$user['email'],$user['password']],
                $email_content->message
            );

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('SEND_MAIL'));
                        $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                        $message->cc(explode(',', env('SEND_MAIL_CC')));
                    }else{
                        $email = $user["email"];
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, $user["name"])->subject($email_content->subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $user['name'],
                    'subject' => $email_content->subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }

    
     public function onAnchorLeadUpload($userData) {
        $this->func_name = __FUNCTION__;
        $user = unserialize($userData);
        $email_content = EmailTemplate::getEmailTemplate("ANCHOR_CSV_LEAD_UPLOAD");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%url'],
                [ucwords($user['name']),$user['url']],
                $email_content->message
            );

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('SEND_MAIL'));
                        $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                        $message->cc(explode(',', env('SEND_MAIL_CC')));
                    }else{
                        $email = $user["email"];
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to( $email, $user["name"])->subject($email_content->subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $user['name'],
                    'subject' => $email_content->subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }  

    public function onCreateUserRoleSuccess($userData) {
        $this->func_name = __FUNCTION__;
        $user = unserialize($userData);
        $email_content = EmailTemplate::getEmailTemplate("CREATE_BACKEND_USER_MAIL");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%email','%password'],
                [ucwords($user['name']),$user['email'],$user['password']],
                $email_content->message
            );
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('SEND_MAIL'));
                        $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                        $message->cc(explode(',', env('SEND_MAIL_CC')));
                    }else{
                        $email = $user["email"];
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to( $email, $user["name"])->subject($email_content->subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $user['name'],
                    'subject' => $email_content->subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    } 
    
    
    
    /**
     * co lender user mail
     * 
     * @param Array $attributes
     */
    public function coLenderUserRegMail($attributes) {
        $this->func_name = __FUNCTION__;
        $data = unserialize($attributes); 
        $email_content = EmailTemplate::getEmailTemplate("CO_Lender_REGISTER_USER_MAIL");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%email','%password'],
                [ucwords($data['name']),$data['email'],$data['password']],
                $email_content->message
            );

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($data, $email_content, $mail_body) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('SEND_MAIL'));
                        $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                        $message->cc(explode(',', env('SEND_MAIL_CC')));
                    }else{
                        $email = $user["email"];
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to( $email, $data["name"])->subject($email_content->subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $data['name'],
                    'subject' => $email_content->subject,
                    'body' => $mail_body,
               ];
               FinanceModel::logEmail($mailContent);
            });
        }
    }

    /**
     * Sanction Letter
     * 
     * @param Array $attributes
     */
    public function sactionLetterMail($attributes) {
        $data = unserialize($attributes); 
        $this->func_name = __FUNCTION__;
        Mail::send('email', ['baseUrl'=> env('REDIRECT_URL',''), 'varContent' => $data['body']],
            function ($message) use ($data) {
                if( env('SEND_MAIL_ACTIVE') == 1){
                    $email = explode(',', env('SEND_MAIL'));
                    $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                    $message->cc(explode(',', env('SEND_MAIL_CC')));
                }else{
                    $email = $user["email"];
                }
            $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
            if(!empty($data['attachment'])){
                $att_name = 'sanction.pdf';
                $message->attachData($data['attachment'], $att_name);
            }

            $message->to($email, $data["name"])->subject($data['subject']);
            $mailContent = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_to' => $email,
                'email_type' => $this->func_name,
                'name' => $data['name'],
                'subject' => $data['subject'],
                'body' => $data['body'],
                'att_name' => $att_name ?? NULL,
                'attachment' => $data['attachment'] ?? NULL,
            ];
            FinanceModel::logEmail($mailContent);
        }); 
    }

    /**
     * Event subscribers
     *
     * @param mixed $events
     */
    public function subscribe($events) {

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
        $events->listen(
            'ANCHOR_REGISTER_USER_MAIL',
            'App\Inv\Repositories\Events\UserEventsListener@onAnchorRegistUserSuccess'
        );
        $events->listen(
            'ANCHOR_CSV_LEAD_UPLOAD',
            'App\Inv\Repositories\Events\UserEventsListener@onAnchorLeadUpload'
        );
        $events->listen(
            'CREATE_BACKEND_USER_MAIL',
            'App\Inv\Repositories\Events\UserEventsListener@onCreateUserRoleSuccess'
        );
        
        $events->listen(
            'AGENCY_USER_REGISTER_MAIL',
            'App\Inv\Repositories\Events\UserEventsListener@onAgencyUserRegisterSuccess'
        );
        
        
        $events->listen(
            'CO_LENDER_USER_REGISTER_MAIL',
            'App\Inv\Repositories\Events\UserEventsListener@coLenderUserRegMail'
        );
        
        $events->listen(
            'SANCTION_LETTER_MAIL',
            'App\Inv\Repositories\Events\UserEventsListener@sactionLetterMail'
        );
        
        //
    }
}
