<?php

namespace App\Inv\Repositories\Events;

use Mail;
use Illuminate\Queue\SerializesModels;
use App\Inv\Repositories\Factory\Events\BaseEvent;
use App\Inv\Repositories\Models\Master\EmailTemplate;
use App\Inv\Repositories\Models\FinanceModel;
use Storage;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Mail\ReviewerSummary;

class UserEventsListener extends BaseEvent
{

    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(InvMasterRepoInterface $mstRepo)
    {
        $this->mstRepo = $mstRepo;
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
            $link = \Helpers::getServerProtocol() . config('proin.frontend_uri');
            $mail_body = str_replace(
                ['%name', '%email','%password','%link'],
                [ucwords($user['name']),$user['email'],$user['password'], $link],
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

        if(isset($user['anchor_id'])) {
            if ($user['anchor_id'] == config('common.LENEVO_ANCHOR_ID')) {
                $email_content = EmailTemplate::getEmailTemplate("FORGOT_PASSWORD_LENOVO");
            }
        } else {
            $email_content = EmailTemplate::getEmailTemplate("FORGOT_PASSWORD");
        }
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
        if (isset($user['anchor_id']) && ($user['anchor_id'] == config('common.LENEVO_ANCHOR_ID'))) {
            $email_content = EmailTemplate::getEmailTemplate("LENOVO_RESET_PASSWORD_SUCCESSS");
        } else {
            $email_content = EmailTemplate::getEmailTemplate("RESET_PASSWORD_SUCCESSS");
        }
        if ($email_content) {
            $mail_body = str_replace(
                ['%name'],
                [ucwords($user['name'])],
                $email_content->message
            );

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                $email = $user["email"];
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
            $link = \Helpers::getServerProtocol() . env('BACKEND_URI');
            $mail_body = str_replace(
                ['%name', '%email','%password','%link'],
                [ucwords($user['name']),$user['email'],$user['password'], $link],
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
            $link = \Helpers::getServerProtocol() . env('BACKEND_URI');
            $mail_body = str_replace(
                ['%name', '%email','%password', '%link'],
                [ucwords($user['name']),$user['email'],$user['password'], $link],
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
                    'subject' => $email_content->subject."//".$user['businessName']."//".$user['anchorName'],
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
            $link = \Helpers::getServerProtocol() . env('BACKEND_URI');
            $mail_body = str_replace(
                ['%name', '%email','%password', '%link'],
                [ucwords($user['name']),$user['email'],$user['password'], $link],
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
                        $email = $data["email"];
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
                    $email = $data["email"];
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

    public function onApplicationPickup($userData) {
        $user = unserialize($userData);
        $this->func_name = __FUNCTION__;
        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("APPLICATION_PICKUP");

        if ($email_content) {
            $mail_body = str_replace(
                ['%sender_user_name', '%sender_role_name','%receiver_user_name','%receiver_role_name','%app_id'],
                [$user['sender_user_name'],$user['sender_role_name'],$user['receiver_user_name'],$user['receiver_role_name'],$user['app_id']],
                $email_content->message
            );
            $mail_subject = str_replace(['%app_id'], $user['app_id'],$email_content->subject);

            Mail::send( 'email', [ 'baseUrl'=>env('REDIRECT_URL',''), 'varContent' => $mail_body, ], 
                function ($message) use ($user, $mail_subject, $mail_body) {
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($user["receiver_email"], $user["receiver_user_name"])->subject($mail_subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => array($user["receiver_email"]),
                    'email_type' => $this->func_name,
                    'name' => $user['receiver_user_name'],
                    'subject' => $mail_subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    } 

    public function onApplicationMoveNextUser($userData) {

        
        $user = unserialize($userData);
        $this->func_name = __FUNCTION__;
        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("APPLICATION_MOVE_NEXT_USER");
        if ($email_content) {
            $mail_body = str_replace(
                ['%sender_user_name', '%sender_role_name','%receiver_user_name','%receiver_role_name','%lead_id' ,'%app_id','%entity_name','%comment'],
                [$user['sender_user_name'],$user['sender_role_name'],$user['receiver_user_name'],$user['receiver_role_name'],$user['lead_id'],$user['app_id'],$user['entity_name'],$user['comment']],
                $email_content->message
            );
            $mail_subject = str_replace(['%app_id'], $user['app_id'],$email_content->subject);
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body, ],
                function ($message) use ($user, $mail_subject, $mail_body) {
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($user["receiver_email"], $user["receiver_user_name"])->subject($mail_subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => array($user["receiver_email"]),
                    'email_type' => $this->func_name,
                    'name' => $user['receiver_user_name'],
                    'subject' => $mail_subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    } 

    public function onApplicationMoveNextPool($userData) {
        $user = unserialize($userData);
        $this->func_name = __FUNCTION__;
        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("APPLICATION_MOVE_NEXT_POOL");
        if ($email_content) {
            $mail_body = str_replace(
                ['%sender_user_name', '%sender_role_name','%receiver_user_name','%receiver_role_name','%lead_id' ,'%app_id','%entity_name','%comment'],
                [$user['sender_user_name'],$user['sender_role_name'],$user['receiver_user_name'],$user['receiver_role_name'],$user['lead_id'],$user['app_id'],$user['entity_name'],$user['comment']],
                $email_content->message
            );
            $mail_subject = str_replace(['%app_id'], $user['app_id'],$email_content->subject);

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body, ],
                function ($message) use ($user, $mail_subject, $mail_body) {
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($user["receiver_email"], $user["receiver_user_name"])->subject($mail_subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => array($user["receiver_email"]),
                    'email_type' => $this->func_name,
                    'name' => $user['receiver_user_name'],
                    'subject' => $mail_subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }

    public function onApplicationMoveBack($userData) {
        $user = unserialize($userData);
        $this->func_name = __FUNCTION__;
        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("APPLICATION_MOVE_BACK");
        if ($email_content) {
            $mail_body = str_replace(
                ['%sender_user_name', '%sender_role_name','%receiver_user_name','%receiver_role_name','%lead_id' ,'%app_id','%entity_name','%comment'],
                [$user['sender_user_name'],$user['sender_role_name'],$user['receiver_user_name'],$user['receiver_role_name'],$user['lead_id'],$user['app_id'],$user['entity_name'],$user['comment']],
                $email_content->message
            );
            $mail_subject = str_replace(['%app_id'], $user['app_id'],$email_content->subject);

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body, ],
                function ($message) use ($user, $mail_subject, $mail_body) {
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($user["receiver_email"], $user["receiver_user_name"])->subject($mail_subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => array($user["receiver_email"]),
                    'email_type' => $this->func_name,
                    'name' => $user['receiver_user_name'],
                    'subject' => $mail_subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    } 

    public function onApplicationMoveToApprover($userData){
        $user = unserialize($userData);        
        $this->func_name = __FUNCTION__;
        //Send mail to User
        
        $email = [];
        foreach($user as $u) {
            if(!empty($u['receiver_email']) ){
                $email[] = $u['receiver_email'];
            }
        }
        if( env('SEND_MAIL_ACTIVE') == 1){
            $email_cc = $user['cc_mails'];
        }else{
            $email_content = EmailTemplate::getEmailTemplate("APPLICATION_APPROVER_MAIL");
            if(!empty($user['product_id']) && (in_array(1,$user['product_id']) || in_array(2,$user['product_id']))){
                $email_cc = explode(',', $email_content->cc);
            }else{
                $email_cc = [];
            }
        }        
       $email_cc = array_filter($email_cc);
       $mailObj = Mail::to($email, ''); //$user["receiver_user_name"]
       if (!empty($email_cc)) {
           $mailObj->cc($email_cc);
       }
       $mailObj->send(new ReviewerSummary($this->mstRepo, $user));

       $ccMails = is_array($email_cc) ? $email_cc : explode(',', $email_cc);
       $cc = array_filter($ccMails);
       $mailContent = [
        'email_from' => config('common.FRONTEND_FROM_EMAIL'),
        'email_to' => $email,
        'email_cc' => $cc ?? NULL,
        'email_type' => $this->func_name,
        'name' => "Move to Approver",
        'subject' => "Application Approver Mail",
        'body' => '',
        ];
        FinanceModel::logEmail($mailContent);
        
    }
    

    public function onAddActivityLog($arrActivity)
    {
        $arrActivity = unserialize($arrActivity);
        $whereCond=[];
        $whereCond['activity_code'] = $arrActivity['activity_code'];
        $activity = $this->mstRepo->getActivity($whereCond);        
        $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0 ;
        $activity_desc = $arrActivity['activity_desc'];
        return self::addActivityLog($activity_type_id, $activity_desc, $arrActivity);        
    }
        
    public function onRenewApplication($emailData)
    {
        $user = unserialize($emailData);
        $this->func_name = __FUNCTION__;
        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("APPLICATION_RENEWAL_MAIL");
        if ($email_content) {
            $mail_body = str_replace(
                ['%receiver_user_name', '%lead_id' ,'%app_id','%entity_name', '%entity_addr' ,'%customer_id', '%biz_type', '%sales_manager_name', '%prgm_limit_amt', '%app_limit', '%sales_manager_email', '%cmp_name', '%cmp_add', '%year'],
                [$user['receiver_user_name'],$user['lead_id'],$user['app_id'],$user['entity_name'],$user['entity_addr'],$user['customer_id'],$user['biz_type'],$user['sales_manager_name'],$user['prgm_limit_amt'],$user['app_limit'],$user['sales_manager_email'],$user['cmp_name'],$user['cmp_add'],$user['year']],
                $email_content->message
            );
            $mail_subject = str_replace(['%app_id'], $user['app_id'],$email_content->subject);

            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body, ],
                function ($message) use ($user, $mail_subject, $mail_body) {
                
                
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->subject($mail_subject);
                
                if( env('SEND_MAIL_ACTIVE') == 1){
                    $email = explode(',', env('SEND_MAIL'));
                    $message->to($email);
                    $message->bcc(explode(',', env('SEND_MAIL_BCC')));
                    $message->cc(explode(',', env('SEND_MAIL_CC')));                    
                }else{
                    //$message->to($user["receiver_email"], $user["receiver_user_name"]);
                    //$message->to($user["sales_manager_email"], $user["sales_manager_name"]);
                    $email = [$user["receiver_email"],$user["sales_manager_email"]];
                    $message->to($email);
                }
        
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $user['receiver_user_name'],
                    'subject' => $mail_subject,
                    'body' => $mail_body,
                ]; 
                FinanceModel::logEmail($mailContent);
                
            });
        }
    }
    
    public function onRegdWithSamePan($user) {
        $this->func_name = __FUNCTION__; 
        $user = unserialize($user);
        $email_content = EmailTemplate::getEmailTemplate("NOTIFY_EXISTING_USER");
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

    public function onApplicationMoveToLms($userData) {

        
        $user = unserialize($userData);
        $this->func_name = __FUNCTION__;
        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("APPLICATION_MOVE_LMS");
        if ($email_content) {
            $mail_body = str_replace(
                ['%sender_user_name', '%sender_role_name','%receiver_user_name','%receiver_role_name','%lead_id' ,'%app_id','%entity_name','%comment'],
                [$user['sender_user_name'],$user['sender_role_name'],$user['receiver_user_name'],$user['receiver_role_name'],$user['lead_id'],$user['app_id'],$user['entity_name'],$user['comment']],
                $email_content->message
            );
            $mail_subject = str_replace(['%app_id'], $user['app_id'],$email_content->subject);
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body, ],
                function ($message) use ($user, $mail_subject, $mail_body) {
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($user["receiver_email"], $user["receiver_user_name"])->subject($mail_subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => array($user["receiver_email"]),
                    'email_type' => $this->func_name,
                    'name' => $user['receiver_user_name'],
                    'subject' => $mail_subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }

    public function onDisbursedSuccess($userData) {

        
        $user = unserialize($userData);
        $this->func_name = __FUNCTION__;
        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("LMS_USER_DISBURSAL");
        if ($email_content) {
            $mail_body = str_replace(
                ['%receiver_user_name','%user_id' ,'%amount', '%utr_no','%benefi_name','%disbursed_date'],
                [$user['receiver_user_name'],$user['user_id'],$user['amount'],$user['utr_no'],$user['benefi_name'],$user['disbursed_date']],
                $email_content->message
            );
            $mail_subject = str_replace(['%user_id'], $user['user_id'],$email_content->subject);
            $email_cc = explode(',', $email_content->cc);
            if(isset($user['anchor_email'])) {
                $email_cc[] = ($user['anchor_email']);
            }
            if(isset($user['sales_email'])) {
                $email_cc[] = ($user['sales_email']);
            }
            if(isset($user['auth_email'])) {
                $email_cc[] = ($user['auth_email']);
            } 
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body, ],
                function ($message) use ($user, $mail_subject, $mail_body, $email_cc) {
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to(trim($user["receiver_email"]),trim($user["receiver_user_name"]))->subject($mail_subject);
                if(!empty($email_cc)) {
                    $message->cc($email_cc);
                }
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => array($user["receiver_email"]),
                    'email_cc' => $email_cc,
                    'email_type' => $this->func_name,
                    'name' => $user['receiver_user_name'],
                    'subject' => $mail_subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    } 

    public function onMaturityReport($mailData){
        $data = unserialize($mailData); 
        $this->func_name = __FUNCTION__;
        Mail::send('email', ['baseUrl'=> env('REDIRECT_URL',''), 'varContent' => $data['body']],
        function ($message) use ($data) {
            if(!empty($data['attachment'])){
                $att_name = 'Maturity Report.xlsx';
                $message->attach($data['attachment'] ,['as' => $att_name]);
            }
            
            $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'))
            ->to($data["to"], $data["name"])
            ->subject($data['subject']);

            if (!empty($data["cc"])) {
                $message->cc($data["cc"]);
            }

            if (!empty($data["bcc"])) {
                $message->bcc($data["bcc"]);
            }

            $mailContent = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_to' => $data["to"],
                'email_cc' => $data["cc"],
                'email_bcc' => $data["bcc"],
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

    public function onUtilizationReport($mailData){
        $data = unserialize($mailData); 
        $this->func_name = __FUNCTION__;
        Mail::send('email', ['baseUrl'=> env('REDIRECT_URL',''), 'varContent' => $data['body']],
        function ($message) use ($data) {
            if(!empty($data['attachment'])){
                $att_name = 'Utilization Report.xlsx';
                $message->attach($data['attachment'] ,['as' => $att_name]);
            }
            
            $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'))
            ->to($data["to"], $data["name"])
            ->subject($data['subject']);
            
            if (!empty($data["cc"])) {
                $message->cc($data["cc"]);
            }

            if (!empty($data["bcc"])) {
                $message->bcc($data["bcc"]);
            }

            $mailContent = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_to' => $data["to"],
                'email_cc' => $data["cc"],
                'email_bcc' => $data["bcc"],
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

    public function onDisbursalReport($mailData){
        $data = unserialize($mailData); 
        $this->func_name = __FUNCTION__;
        Mail::send('email', ['baseUrl'=> env('REDIRECT_URL',''), 'varContent' => $data['body']],
        function ($message) use ($data) {
            if(!empty($data['attachment'])){
                $att_name = 'Disbursal Report.xlsx';
                $message->attach($data['attachment'] ,['as' => $att_name]);
            }
            
            $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'))
            ->to($data["to"], $data["name"])
            ->subject($data['subject']);

            if (!empty($data["cc"])) {
                $message->cc($data["cc"]);
            }

            if (!empty($data["bcc"])) {
                $message->bcc($data["bcc"]);
            }

            $mailContent = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_to' => $data["to"],
                'email_cc' => $data["cc"],
                'email_bcc' => $data["bcc"],
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

    public function onOverdueReport($mailData){
        $data = unserialize($mailData); 
        $this->func_name = __FUNCTION__;
        Mail::send('email', ['baseUrl'=> env('REDIRECT_URL',''), 'varContent' => $data['body']],
        function ($message) use ($data) {
            if(!empty($data['attachment'])){
                $att_name = 'Overdue Report.xlsx';
                $message->attach($data['attachment'] ,['as' => $att_name]);
            }
            
            $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'))
            ->to($data["to"], $data["name"])
            ->subject($data['subject']);

            if (!empty($data["cc"])) {
                $message->cc($data["cc"]);
            }

            if (!empty($data["bcc"])) {
                $message->bcc($data["bcc"]);
            }

            $mailContent = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_to' => $data["to"],
                'email_cc' => $data["cc"],
                'email_bcc' => $data["bcc"],
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

    public function onMarginReport($mailData){
        $data = unserialize($mailData);
        $this->func_name = __FUNCTION__;
        Mail::send('email', ['baseUrl'=> env('REDIRECT_URL',''), 'varContent' => $data['body']],
        function ($message) use ($data) {
            if(!empty($data['attachment'])){
                $att_name = 'Margin Report.xlsx';
                $message->attach($data['attachment'] ,['as' => $att_name]);
            }

            $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'))
            ->to($data["to"], $data["name"])
            ->subject($data['subject']);

            if (!empty($data["cc"])) {
                $message->cc($data["cc"]);
            }

            if (!empty($data["bcc"])) {
                $message->bcc($data["bcc"]);
            }

            $mailContent = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_to' => $data["to"],
                'email_cc' => $data["cc"],
                'email_bcc' => $data["bcc"],
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

    public function onReceiptReport($mailData){
        $data = unserialize($mailData);
        $this->func_name = __FUNCTION__;
        Mail::send('email', ['baseUrl'=> env('REDIRECT_URL',''), 'varContent' => $data['body']],
        function ($message) use ($data) {
            if(!empty($data['attachment'])){
                $att_name = 'Receipt Report.xlsx';
                $message->attach($data['attachment'] ,['as' => $att_name]);
            }

            $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'))
            ->to($data["to"], $data["name"])
            ->subject($data['subject']);

            if (!empty($data["cc"])) {
                $message->cc($data["cc"]);
            }

            if (!empty($data["bcc"])) {
                $message->bcc($data["bcc"]);
            }

            $mailContent = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_to' => $data["to"],
                'email_cc' => $data["cc"],
                'email_bcc' => $data["bcc"],
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

    public function onAccountDisbursalReport($mailData){
        $data = unserialize($mailData); 
        $this->func_name = __FUNCTION__;
        Mail::send('email', ['baseUrl'=> env('REDIRECT_URL',''), 'varContent' => $data['body']],
        function ($message) use ($data) {
            if(!empty($data['attachment'])){
                $att_name = 'Account Disbursal Report.xlsx';
                $message->attach($data['attachment'] ,['as' => $att_name]);
            }
            
            $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'))
            ->to($data["to"], $data["name"])
            ->subject($data['subject']);

            if (!empty($data["cc"])) {
                $message->cc($data["cc"]);
            }

            if (!empty($data["bcc"])) {
                $message->bcc($data["bcc"]);
            }

            $mailContent = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_to' => $data["to"],
                'email_cc' => $data["cc"],
                'email_bcc' => $data["bcc"],
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

    // Inform to respective AGENCY
    public function FiFcuPdConcernMail($mailData){
        $user = unserialize($mailData);
        $this->func_name = __FUNCTION__;
        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("FI_FCU_PD_CONCERN_MAIL");
        if ($email_content) {
            $mail_body = str_replace(
                ['%agency_email','%agency_name' ,'%user', '%user_email', '%trigger_type', '%comment'],
                [$user['email'],$user['name'],$user['user'],$user['user_email'],$user['trigger_type'],$user['comment']],
                $email_content->message
            );
            $mail_subject = $user['subject'];
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body, ],
                function ($message) use ($user, $mail_subject, $mail_body) {
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($user["email"])->subject($mail_subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => array($user["email"]),
                    'email_type' => $this->func_name,
                    'name' => $user['name'],
                    'subject' => $mail_subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }

    // Inform to CPA and CR when agency Status Change
    public function AgencyUpdateToCPAandCR($mailData){
        $user = unserialize($mailData);
        $email_to;
        if(isset($user['trigger_email']) && !empty($user['trigger_email'])) {
            $email_to = $user['trigger_email'];
        }

        $this->func_name = __FUNCTION__;
        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("AGENCY_UPDATE_MAIL_TO_CPA_CR");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name','%curr_user','%curr_email','%trigger_type','%comment','%agency_name','%change_status'],
                [$user['name'], $user['curr_user'],$user['curr_email'],$user['trigger_type'],$user['comment'],$user['agency_name'],$user['change_status']],
                $email_content->message
            );
            $mail_subject = $user['subject'];
            // $emailCC = array();

            // foreach ($user['email'] as $key => $emailVal) {
            //     $emailCC[] = $emailVal;
            // };
            
            $email_cc = explode(',', $email_content->cc);
            foreach ($user['email'] as $key => $emailVlaue) {
                array_push($email_cc, $emailVlaue);
            }

            if(isset($email_to) && !empty($email_to)) {
                if (($key = array_search($email_to, $email_cc)) !== false) {
                    unset($email_cc[$key]);
                }
                Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body, ],
                    function ($message) use ($user, $mail_subject, $mail_body, $email_cc, $email_to) {
                    $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                    $message->to($email_to)->subject($mail_subject);
                    $message->cc($email_cc);
                    
                    $check = array($email_to, $user['curr_email']);
                    $check = array_merge($check, $email_cc);
                    $mailContent = [
                        'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                        'email_to' => $check,
                        'email_type' => $this->func_name,
                        'name' => $user['curr_user'],
                        'subject' => $mail_subject,
                        'body' => $mail_body,
                    ];
                    FinanceModel::logEmail($mailContent);
                });
            }
        }
    }

    
    public function onLenevoRegdSuccess($user) {
        $this->func_name = __FUNCTION__;
        $user = unserialize($user);
        $email_content = EmailTemplate::getEmailTemplate("LENEVO_USER_REGISTERED");
        if ($email_content) {
            $link = \Helpers::getServerProtocol() . config('proin.lenevo_frontend_uri').'/lenevo-login';
            $mail_body = str_replace(
                ['%name', '%email', '%login_link'],
                [ucwords($user['name']),$user['email'], $link],
                $email_content->message
            );
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                $email = $user["email"];
                $message->cc(explode(',', $email_content->cc));                    
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
    
    public function onLenevoDailyNewUser($anchorUserData) {
        $rowData = '';
        foreach ($anchorUserData as $value) {
            $rowData .= '<tr><td>'.ucwords($value->name) .' '. $value->l_name . '</td><td>'.$value->email. '</td><td>'.$value->pan_no. '</td><td>Lenovo</td></tr>';
        }
        $this->func_name = __FUNCTION__;
        $email_content = EmailTemplate::getEmailTemplate("LENEVO_LEAD_REGISTRATION_DETAILS");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%rowdata'],
                ['', $rowData],
                $email_content->message
            );
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($email_content, $mail_body) {
                $eSubj = $email_content->subject . \Carbon\Carbon::today();
                $email = explode(',', config('common.LENOVO_NEW_LEAD_CRON_MAIL_TO'));
                $message->cc(explode(',', $email_content->cc));                    
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, '')->subject($eSubj);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => '',
                    'subject' => $eSubj,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }
     
    /**
     * Supply chain limit activation
     * 
     * @param Array $attributes
     */
    public function supplyChainInvDueAlert($attributes) {
        $data = unserialize($attributes); 
        $this->func_name = __FUNCTION__;
        $email_content = EmailTemplate::getEmailTemplate("SUPPLY_CHAIN_INVOICE_DUE_ALERT");
        if ($email_content) {
            $testingAllow = true;
            if ($testingAllow == true) {
              $activeMailemail = explode(',', env('CRONINVOICE_SEND_MAIL_TO'));
              $activeMailbcc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_BCC_TO')));
              $activeMailcc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_CC_TO')));
              $envMails = ['email' => $activeMailemail, 'cc' => $activeMailcc, 'bcc' => $activeMailbcc];

              $dynamicEmail = $data["email"] ?? 'gaurav.agarwal@zuron.in';
              $dynamiccc = array_filter(explode(',', $email_content->cc));
              $dynamicbcc = array_filter(explode(',', $email_content->bcc));
              $dynamicMails = ['email' => $dynamicEmail, 'cc' => $dynamiccc, 'bcc' => $dynamicbcc];
              
              $mailIds = ['envMails' => $envMails, 'dynamicMails' => $dynamicMails, 'sendigFrom' => (env('SEND_MAIL_ACTIVE') == 1) ? 'ENV' : 'Dynamically'];
              dump($mailIds);
            }
            $userData['user_name'] = $data['user_name'];
            $userData['email'] = $data['email'];
            $offerData = view('reports.invoice_due_alrt')->with(['data' => $data['data'], 'userData' => $userData])->render();
            $mail_subject = str_replace(['%user_name'], [$data['user_name']],$email_content->subject);
            Mail::send('email', ['baseUrl'=> env('HTTP_APPURL',''), 'varContent' => $offerData],
                function ($message) use ($data, $mail_subject, $offerData, $email_content) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('CRONINVOICE_SEND_MAIL_TO'));
                        $bcc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_BCC_TO')));
                        $cc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_CC_TO')));
                    }else{
                        $email = 'gaurav.agarwal@zuron.in';//$data["email"];
                        $cc = array_filter(explode(',', $email_content->cc));
                        $bcc = array_filter(explode(',', $email_content->bcc));
                    }
                    if (!empty($bcc)) {
                        $message->bcc($bcc);
                    }
                    if (!empty($cc)) {
                        $message->cc($cc);
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, $data["user_name"]);
                $message->subject($mail_subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_cc' => $cc ?? NULL,
                    'email_bcc' => $bcc ?? NULL,
                    'email_type' => $this->func_name,
                    'user_name' => $data['user_name'],
                    'subject' => $mail_subject,
                    'body' => $offerData,
                    // 'att_name' => $att_name ?? NULL,
                    // 'attachment' => $data['attachment'] ?? NULL,
                ];
                FinanceModel::logEmail($mailContent);
            }); 
        }
    }
     
    /**
     * Supply chain limit activation
     * 
     * @param Array $attributes
     */
    public function supplyChainInvOverDueAlert($attributes) {
        $data = unserialize($attributes); 
        $this->func_name = __FUNCTION__;
        $userData['user_name'] = $data['user_name'];
        $userData['email'] = $data['email'];
        $email_content = EmailTemplate::getEmailTemplate("SUPPLY_CHAIN_INVOICE_OVERDUE_ALERT");
        if ($email_content) {
            $testingAllow = true;
            if ($testingAllow == true) {
              $activeMailemail = explode(',', env('CRONINVOICE_SEND_MAIL_TO'));
              $activeMailbcc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_BCC_TO')));
              $activeMailcc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_CC_TO')));
              $envMails = ['email' => $activeMailemail, 'cc' => $activeMailcc, 'bcc' => $activeMailbcc];

              $dynamicEmail = $data["email"] ?? 'gaurav.agarwal@zuron.in';
              $dynamiccc = array_filter(explode(',', $email_content->cc));
              $dynamicbcc = array_filter(explode(',', $email_content->bcc));
              $dynamicMails = ['email' => $dynamicEmail, 'cc' => $dynamiccc, 'bcc' => $dynamicbcc];
              
              $mailIds = ['envMails' => $envMails, 'dynamicMails' => $dynamicMails, 'sendigFrom' => (env('SEND_MAIL_ACTIVE') == 1) ? 'ENV' : 'Dynamically'];
              dump($mailIds);
            }
            $offerData = view('reports.invoice_overdue_alrt')->with(['data' => $data['data'], 'userData' => $userData])->render();
            $mail_subject = str_replace(['%user_name'], [$data['user_name']],$email_content->subject);
            Mail::send('email', ['baseUrl'=> env('HTTP_APPURL',''), 'varContent' => $offerData],
                function ($message) use ($data, $mail_subject, $offerData, $email_content) {
                    if( env('SEND_MAIL_ACTIVE') == 1){
                        $email = explode(',', env('CRONINVOICE_SEND_MAIL_TO'));
                        $bcc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_BCC_TO')));
                        $cc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_CC_TO')));
                    }else{
                        $email = 'gaurav.agarwal@zuron.in';//$data["email"];
                        $bcc = array_filter(explode(',', $email_content->bcc));
                        $cc = array_filter(explode(',', $email_content->cc));
                    }
                    if (!empty($bcc)) {
                        $message->bcc($bcc);
                    }
                    if (!empty($cc)) {
                        $message->cc($cc);
                    }
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, $data["user_name"]);
                $message->subject($mail_subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_cc' => $cc ?? NULL,
                    'email_bcc' => $bcc ?? NULL,
                    'email_type' => $this->func_name,
                    'user_name' => $data['user_name'],
                    'subject' => $mail_subject,
                    'body' => $offerData,
                    // 'att_name' => $att_name ?? NULL,
                    // 'attachment' => $data['attachment'] ?? NULL,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
    }

    public function onChargeDeletionRequest($userData){
        $user = unserialize($userData);        
        $this->func_name = __FUNCTION__;
        
        $email = [];
        foreach($user as $u) {
            if(!empty($u['receiver_email']) ){
                $email[] = $u['receiver_email'];
            }
        }

        $email_content = EmailTemplate::getEmailTemplate("CHARGE_DELETION_REQUEST_MAIL");
        $email_cc = explode(',', $email_content->cc);
        $email_cc = array_filter($email_cc);
        if ($email_content) {
            $mail_subject = str_replace(['%business_name'], [$user['business_name']],$email_content->subject);
            $mail_body = $email_content->message;
            $mail_body = str_replace(['%url'], ['https://'. config('proin.backend_uri')], $mail_body);
            $mail_body = str_replace(['%business_name'], [$user['business_name']], $mail_body);
            Mail::send('email', ['baseUrl' => config('lms.REDIRECT_URL'),'varContent' => $mail_body,
                ],
                function ($message) use ($email_content, $mail_body, $email_cc, $email, $mail_subject) {
                $message->cc($email_cc);                    
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, '')->subject($mail_subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => 'Request Approval For Charge Deletion',
                    'subject' => $mail_subject,
                    'body' => $mail_body,
                ];
                FinanceModel::logEmail($mailContent);
            });
        }
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

        $events->listen(
            'APPLICATION_PICKUP', 
            'App\Inv\Repositories\Events\UserEventsListener@onApplicationPickup'
        );

        $events->listen(
            'APPLICATION_MOVE_NEXT_USER', 
            'App\Inv\Repositories\Events\UserEventsListener@onApplicationMoveNextUser'
        );

        $events->listen(
            'APPLICATION_MOVE_NEXT_POOL', 
            'App\Inv\Repositories\Events\UserEventsListener@onApplicationMoveNextPool'
        );

        $events->listen(
            'APPLICATION_MOVE_BACK', 
            'App\Inv\Repositories\Events\UserEventsListener@onApplicationMoveBack'
        );

        $events->listen(
            'APPLICATION_APPROVER_MAIL', 
            'App\Inv\Repositories\Events\UserEventsListener@onApplicationMoveToApprover'
        );
        
        $events->listen(
            'ADD_ACTIVITY_LOG', 
            'App\Inv\Repositories\Events\UserEventsListener@onAddActivityLog'
        );
        
        $events->listen(
            'APPLICATION_RENEWAL_MAIL', 
            'App\Inv\Repositories\Events\UserEventsListener@onRenewApplication'
        );

        $events->listen(
            'APPLICATION_MOVE_LMS', 
            'App\Inv\Repositories\Events\UserEventsListener@onApplicationMoveToLms'
        );

        $events->listen(
            'LMS_USER_DISBURSAL', 
            'App\Inv\Repositories\Events\UserEventsListener@onDisbursedSuccess'
        );        

        $events->listen(
            'NOTIFY_EXISTING_USER', 
            'App\Inv\Repositories\Events\UserEventsListener@onRegdWithSamePan'
        );
        
        $events->listen(
            'NOTIFY_MATURITY_REPORT', 
            'App\Inv\Repositories\Events\UserEventsListener@onMaturityReport'
        );
        
        $events->listen(
            'NOTIFY_UTILIZATION_REPORT', 
            'App\Inv\Repositories\Events\UserEventsListener@onUtilizationReport'
        );

        $events->listen(
            'NOTIFY_DISBURSAL_REPORT', 
            'App\Inv\Repositories\Events\UserEventsListener@onDisbursalReport'
        );

        $events->listen(
            'NOTIFY_OVERDUE_REPORT', 
            'App\Inv\Repositories\Events\UserEventsListener@onOverdueReport'
        );

        $events->listen(
            'NOTIFY_ACCOUNT_DISBURSAL_REPORT', 
            'App\Inv\Repositories\Events\UserEventsListener@onAccountDisbursalReport'
        );

        $events->listen(
            'FI_FCU_PD_CONCERN_MAIL', 
            'App\Inv\Repositories\Events\UserEventsListener@FiFcuPdConcernMail'
        );

        $events->listen(
            'AGENCY_UPDATE_MAIL_TO_CPA_CR', 
            'App\Inv\Repositories\Events\UserEventsListener@AgencyUpdateToCPAandCR'
        );
        
        $events->listen(
            'user.LENEVO_REGISTERED_SUCCESS',
            'App\Inv\Repositories\Events\UserEventsListener@onLenevoRegdSuccess'
        );
        
        $events->listen(
            'user.LENEVO_DAILY_NEW_USER_CRON',
            'App\Inv\Repositories\Events\UserEventsListener@onLenevoDailyNewUser'
        );

        $events->listen(
            'SUPPLY_CHAIN_INVOICE_DUE_ALERT', 
            'App\Inv\Repositories\Events\UserEventsListener@supplyChainInvDueAlert'
        );

        $events->listen(
            'SUPPLY_CHAIN_INVOICE_OVERDUE_ALERT', 
            'App\Inv\Repositories\Events\UserEventsListener@supplyChainInvOverDueAlert'
        );

        $events->listen(
            'NOTIFY_MARGIN_REPORT',
            'App\Inv\Repositories\Events\UserEventsListener@onMarginReport'
        );

        $events->listen(
            'NOTIFY_RECEIPT_REPORT',
            'App\Inv\Repositories\Events\UserEventsListener@onReceiptReport'
        );

        $events->listen(
            'CHARGE_DELETION_REQUEST_MAIL',
            'App\Inv\Repositories\Events\UserEventsListener@onChargeDeletionRequest'
        );
    }
}
