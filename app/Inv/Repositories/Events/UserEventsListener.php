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
use Carbon\Carbon;
use App\Jobs\SendMail as SendMailJob;
use App\Mail\SendEmail;

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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => NULL,
                    ]
                ];
            }else{
                $to = [
                    [
                        'email' => $user["email"], 
                        'name' => $user['name'],
                    ]
                ];
            }
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'mail_subject' => $email_content->subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => NULL,
                    ]
                ];
            }else{
                $to = [
                    [
                        'email' => $user["email"], 
                        'name' => $user['name'],
                    ]
                ];
            }
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'mail_subject' => $email_content->subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => NULL,
                    ]
                ];
            }else{
                $to = [
                    [
                        'email' => $user["email"], 
                        'name' => $user['name'],
                    ]
                ];
            }
            $funcName = $this->func_name;
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'mail_subject' => $email_content->subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];

            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => NULL,
                    ]
                ];
                $cc = \Helpers::ccOrBccEmailsArray(env('SEND_MAIL_CC'));
                $bcc = \Helpers::ccOrBccEmailsArray(env('SEND_MAIL_BCC'));
            }else{
                $to = [
                    [
                        'email' => $user["email"], 
                        'name' => $user['name'],
                    ]
                ];
                $cc = \Helpers::ccOrBccEmailsArray($email_content->cc);
                $bcc = \Helpers::ccOrBccEmailsArray($email_content->bcc);
            }
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'email_cc' => $cc ?? NULL,
                'email_bcc' => $bcc ?? NULL,
                'mail_subject' => $email_content->subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->bcc($bcc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
        }
    }

    public function onSendConsentOtp($user) {
        $this->func_name = __FUNCTION__; 
        $user = unserialize($user);
        $email_content = EmailTemplate::getEmailTemplate("OTP_CONSENT_SEND");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%otp','%url'],
                [ucwords($user['name']),$user['otp'],$user['url']],
                $email_content->message
            );
            Mail::send('email', ['baseUrl'=>env('REDIRECT_URL',''),'varContent' => $mail_body,
                ],
                function ($message) use ($user, $email_content, $mail_body) {
                $email = $user["email"];
                $emailBcc = \Helpers::ccOrBccEmailsArray($email_content->bcc);
                $emailCc = \Helpers::ccOrBccEmailsArray($email_content->cc);
                $mail_subject = str_replace(['%app_id','%biz_name'], [$user['ckyc_app_code'] ?? '',$user['ckyc_biz_name'] ?? ''], $email_content->subject);
                $message->bcc($emailBcc);
                $message->cc($emailCc);
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                $message->to($email, $user["name"])->subject($mail_subject);
                $mailContent = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_to' => $email,
                    'email_type' => $this->func_name,
                    'name' => $user['name'],
                    'subject' => $mail_subject,
                    'body' => $mail_body,
                    'email_bcc' => $emailBcc,
                    'email_cc' => $emailCc,
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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => NULL,
                    ]
                ];
                $cc = \Helpers::ccOrBccEmailsArray(env('SEND_MAIL_CC'));
                $bcc = \Helpers::ccOrBccEmailsArray(env('SEND_MAIL_BCC'));
            }else{
                $to = [
                    [
                        'email' => $user["email"], 
                        'name' => $user['name'],
                    ]
                ];
                $cc = \Helpers::ccOrBccEmailsArray($email_content->cc);
                $bcc = \Helpers::ccOrBccEmailsArray($email_content->bcc);
            }
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'email_cc' => $cc ?? NULL,
                'email_bcc' => $bcc ?? NULL,
                'mail_subject' => $email_content->subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->bcc($bcc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            $to = [
                [
                    'email' => $user["email"], 
                    'name' => $user['name'],
                ]
            ];
            $funcName = $this->func_name;
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'mail_subject' => $email_content->subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => NULL,
                    ]
                ];
                $cc = \Helpers::ccOrBccEmailsArray(env('SEND_MAIL_CC'));
                $bcc = \Helpers::ccOrBccEmailsArray(env('SEND_MAIL_BCC'));
            }else{
                $to = [
                    [
                        'email' => $user["email"], 
                        'name' => $user['name'],
                    ]
                ];
                $cc = \Helpers::ccOrBccEmailsArray($email_content->cc);
                $bcc = \Helpers::ccOrBccEmailsArray($email_content->bcc);
            }
            $funcName = $this->func_name;
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'email_cc' => $cc ?? NULL,
                'email_bcc' => $bcc ?? NULL,
                'mail_subject' => $email_content->subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->bcc($bcc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => NULL,
                    ]
                ];
            }else{
                $to = [
                    [
                        'email' => $user["email"], 
                        'name' => $user['name'],
                    ]
                ];
            }
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'mail_subject' => $email_content->subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            $to = [
                [
                    'email' => $user["email"], 
                    'name' => $user['name'],
                ]
            ];
            $cc = \Helpers::ccOrBccEmailsArray($email_content->cc);
            $bcc = \Helpers::ccOrBccEmailsArray($email_content->bcc);
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'email_cc' => $cc ?? NULL,
                'email_cc' => $bcc ?? NULL,
                'mail_subject' => $email_content->subject."//".$user['businessName']."//".$user['anchorName'],
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
    
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->bcc($bcc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => NULL,
                    ]
                ];
            }else{
                $to = [
                    [
                        'email' => $user["email"], 
                        'name' => $user['name'],
                    ]
                ];
            }
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'mail_subject' => $email_content->subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => NULL,
                    ]
                ];
            }else{
                $to = [
                    [
                        'email' => $data["email"], 
                        'name' => $data['name'],
                    ]
                ];
            }
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$data["email"]],
                'mail_subject' => $email_content->subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
                'attachments' => NULL,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $data['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
        if( env('SEND_MAIL_ACTIVE') == 1){
            $to = [
                [
                    'email' => explode(',', env('SEND_MAIL')), 
                    'name' => NULL,
                ]
            ];
        }else{
            $to = [
                [
                    'email' => $data["email"], 
                    'name' => $data['name'],
                ]
            ];
        }
        $baseUrl = env('REDIRECT_URL','');
        $attachData = [];
        if(!empty($data['attachment'])){
            $time = \Helpers::convertDateTimeFormat(now(), 'Y-m-d H:i:s', 'Y-m-d-H:i:s');
            $att_name = 'sanctionLetter-'.$time.'.pdf';
            $attachData[] =
                [
                    'file_path' =>  base64_encode($data['attachment']), // encode the binary data as base64
                    'file_name' => $att_name,
                    'isBinaryData' => true,
                ];
        }
        $mailData = [
            'email_to' => [$data["email"]],
            'mail_subject' => $data['subject'],
            'mail_body' => $data['body'],
            'base_url' => $baseUrl,
            'attachments' => $attachData,
        ];
        $mailLogData = [
            'email_from' => config('common.FRONTEND_FROM_EMAIL'),
            'email_type' => $this->func_name,
            'name' => $data['name'],
        ];
        // Serialize the data
        $mailDataSerialized = serialize($mailData);
        $mailLogDataSerialized = serialize($mailLogData);
        // Queue the email job
        Mail::to($to)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
       $baseUrl = env('REDIRECT_URL','');
       $ccMails = is_array($email_cc) ? $email_cc : explode(',', $email_cc);
       $cc = array_filter($ccMails);
       $mail_body = view('emails.reviewersummary.reviewersummarymail', [
        'limitOfferData'=> $user['limitOfferData'],
        'reviewerSummaryData'=> $user['reviewerSummaryData'],
        'offerPTPQ' => $user['offerPTPQ'],
        'preCondArr' => $user['preCondArr'],
        'postCondArr' => $user['postCondArr'],
        'leaseOfferData'=> $user['leaseOfferData'],
        'arrStaticData' => $user['arrStaticData'],
        'facilityTypeList' => $user['facilityTypeList'],
        //'receiverUserName' => $this->user['receiver_user_name'],
        'appId' => $user['appId'],
        'url' =>  $user['url'],
        'dispAppId' => $user['dispAppId'],
        'supplyOfferData' => $user['supplyOfferData'],
        'positiveRiskCmntArr' => $user['positiveRiskCmntArr'],
        'negativeRiskCmntArr' => $user['negativeRiskCmntArr'],
        'fee' => $user['fee'],
        'borrowerLimitData'=> $user['borrowerLimitData']
        ])->render();
        $mailData = [
            'mail_subject' => $user['email_subject'],
            'mail_body' => $mail_body,
            'base_url' => $baseUrl,
            'attachments' => $user['fileAttachments'],
            'email_cc' => $cc ?? NULL,
            'email_to' => $email,
        ];
        $mailLogData = [
            'email_from' => config('common.FRONTEND_FROM_EMAIL'),
            'email_type' => $this->func_name,
            'name' => "Move to Approver",
        ];
        // Serialize the data
        $mailDataSerialized = serialize($mailData);
        $mailLogDataSerialized = serialize($mailLogData);

        // Queue the email job
        $mailObj->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
       
       //$mailObj->queue(new ReviewerSummary($user,$serializeData));
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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => NULL,
                    ]
                ];
            }else{
                $to = [
                    [
                        'email' => [$user["receiver_email"],$user["sales_manager_email"]], 
                        'name' => $user['receiver_user_name'],
                    ]
                ];
            }
            $funcName = $this->func_name;
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["receiver_email"],$user["sales_manager_email"]],
                'mail_subject' => $mail_subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['receiver_user_name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => NULL,
                    ]
                ];
            }else{
                $to = [
                    [
                        'email' => $user["email"], 
                        'name' => $user['name'],
                    ]
                ];
            }
            $funcName = $this->func_name;
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'mail_subject' => $email_content->subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
                ['%receiver_user_name','%user_id' ,'%amount', '%utr_no','%disbursed_date'],
                [$user['receiver_user_name'],$user['id'],$user['amount'],$user['utr_no'],$user['disbursed_date']],
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
            $to = [
                [
                    'email' => trim($user["receiver_email"]), 
                    'name' => trim($user["receiver_user_name"]),
                ]
            ];
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["receiver_email"]],
                'email_cc' => $email_cc,
                'mail_subject' => $mail_subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['receiver_user_name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($email_cc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            $to = [
                [
                    'email' => $user["email"], 
                    'name' => $user['name'],
                ]
            ];
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => array($user["email"]),
                'mail_subject' => $mail_subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            
            $cc = explode(',', $email_content->cc);
            foreach ($user['email'] as $key => $emailVlaue) {
                array_push($cc, $emailVlaue);
            }

            if(isset($email_to) && !empty($email_to)) {
                if (($key = array_search($email_to, $cc)) !== false) {
                    unset($cc[$key]);
                }
                $check = array($email_to, $user['curr_email']);
                $check = array_merge($check, $email_cc);
                $to = [
                    [
                        'email' => $user['trigger_email'], 
                        'name' => NULL,
                    ]
                ];
                $mail_subject = $email_content->subject . \Carbon\Carbon::today();
                $baseUrl = env('REDIRECT_URL','');
                $mailData = [
                    'email_to' => [$check],
                    'mail_subject' => $mail_subject,
                    'mail_body' => $mail_body,
                    'base_url' => $baseUrl,
                ];
                $mailLogData = [
                    'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                    'email_type' => $this->func_name,
                    'name' => $user['curr_user'],
                ];
                // Serialize the data
                $mailDataSerialized = serialize($mailData);
                $mailLogDataSerialized = serialize($mailLogData);
                // Queue the email job
                Mail::to($to)->cc($cc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            $to = [
                [
                    'email' => $user["email"], 
                    'name' => $user['name'],
                ]
            ];
            $mail_subject = $email_content->subject . \Carbon\Carbon::today();
            $cc = explode(',', $email_content->cc);
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'email_cc' => $cc,
                'mail_subject' => $mail_subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            $email = explode(',', config('common.LENOVO_NEW_LEAD_CRON_MAIL_TO'));
            $to = [
                [
                    'email' => $email, 
                    'name' => NULL,
                ]
            ];
            $mail_subject = $email_content->subject . \Carbon\Carbon::today();
            $cc = explode(',', $email_content->cc);
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => $email,
                'email_cc' => $cc,
                'mail_subject' => $mail_subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => NULL,
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('CRONINVOICE_SEND_MAIL_TO')), 
                        'name' => null,
                    ]
                ];
                $email = explode(',', env('CRONINVOICE_SEND_MAIL_TO'));
                $cc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_CC_TO')));
                $bcc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_BCC_TO')));
            }else{
                $to = [
                    [
                        'email' => 'gaurav.agarwal@zuron.in', //$data["email"]
                        'name' => $data['user_name'],
                    ]
                ];
                $cc = array_filter(explode(',', $email_content->cc));
                $bcc = array_filter(explode(',', $email_content->bcc));
            }
            $funcName = $this->func_name;
            $baseUrl = env('HTTP_APPURL','');
            $mailData = [
                'email_to' => ['gaurav.agarwal@zuron.in'], //$data["email"]
                'email_bcc' => $bcc,
                'email_cc' => $cc,
                'mail_subject' => $mail_subject,
                'mail_body' => $offerData,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $data['user_name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->bcc($bcc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
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
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('CRONINVOICE_SEND_MAIL_TO')), 
                        'name' => null,
                    ]
                ];
                $email = explode(',', env('CRONINVOICE_SEND_MAIL_TO'));
                $cc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_CC_TO')));
                $bcc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_BCC_TO')));
            }else{
                $to = [
                    [
                        'email' => 'gaurav.agarwal@zuron.in', //$data["email"]
                        'name' => $data['user_name'],
                    ]
                ];
                $cc = array_filter(explode(',', $email_content->cc));
                $bcc = array_filter(explode(',', $email_content->bcc));
            }
            $funcName = $this->func_name;
            $baseUrl = env('HTTP_APPURL','');
            $mailData = [
                'email_to' => ['gaurav.agarwal@zuron.in'], //$data["email"]
                'email_bcc' => $bcc,
                'email_cc' => $cc,
                'mail_subject' => $mail_subject,
                'mail_body' => $offerData,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $data['user_name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->bcc($bcc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
        }
    }

    public function onChargeDeletionRequest($userData){
        $user = unserialize($userData);
        $this->func_name = __FUNCTION__;
        
        $to = [];
        foreach($user as $u) {
            if(!empty($u['receiver_email']) ){
                $to[] = $u['receiver_email'];
            }
        }
        $email_content = EmailTemplate::getEmailTemplate("CHARGE_DELETION_REQUEST_MAIL");
        $email_cc = explode(',', $email_content->cc);
        $cc = array_filter($email_cc);
        if ($email_content) {
            $mail_subject = str_replace(['%business_name'], [$user['business_name']],$email_content->subject);
            $mail_body = $email_content->message;
            $mail_body = str_replace(['%url'], ['https://'. config('proin.backend_uri')], $mail_body);
            $mail_body = str_replace(['%business_name'], [$user['business_name']], $mail_body);
            $baseUrl = config('lms.REDIRECT_URL');
            $mailData = [
                'email_to' => $to,
                'email_cc' => $cc,
                'mail_subject' => $mail_subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => 'Request Approval For Charge Deletion',
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
        }
    } 

    public function onNonAnchorLeadUpload($userData) {
        $this->func_name = __FUNCTION__;
        $user = unserialize($userData);
        $email_content = EmailTemplate::getEmailTemplate("NON_ANCHOR_CSV_LEAD_UPLOAD");
        if ($email_content) {
            $mail_body = str_replace(
                ['%name', '%url'],
                [ucwords($user['name']),$user['url']],
                $email_content->message
            );

            $mail_subject = str_replace(
                ['%productType'],
                [$user['productType']],
                $email_content->subject
            );
            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => null,
                    ]
                ];
                $cc = \Helpers::ccOrBccEmailsArray(env('SEND_MAIL_CC'));
                $bcc = \Helpers::ccOrBccEmailsArray(env('SEND_MAIL_BCC'));
            }else{
                $to = [
                    [
                        'email' => $user["email"], 
                        'name' => $user['name'],
                    ]
                ];
                $cc = \Helpers::ccOrBccEmailsArray($email_content->cc);
                $bcc = \Helpers::ccOrBccEmailsArray($email_content->bcc);
            }
            $funcName = $this->func_name;
            $baseUrl = env('REDIRECT_URL','');
            $mailData = [
                'email_to' => [$user["email"]],
                'email_bcc' => $bcc,
                'email_cc' => $cc,
                'mail_subject' => $mail_subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'name' => $user['name'],
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->bcc($bcc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
        }
    }      

    public function userInvoiceMail($attributes) {
        $data = unserialize($attributes); 
        $this->func_name = __FUNCTION__;
        $email_content = EmailTemplate::getEmailTemplate("USER_INVOICE_MAIL");
        if($email_content) {
            $mail_subject = str_replace(['%custName','%custId'], [ucwords($data['custName']),ucwords($data['custId'])], $email_content->subject);
            $mail_content = str_replace(['%custName','%custId'], [ucwords($data['custName']),ucwords($data['custId'])], $email_content->message);

            if( env('SEND_MAIL_ACTIVE') == 1){
                $to = [
                    [
                        'email' => explode(',', env('SEND_MAIL')), 
                        'name' => null,
                    ]
                ];
                $cc = \Helpers::ccOrBccEmailsArray(env('SEND_MAIL_CC'));
                $bcc = \Helpers::ccOrBccEmailsArray(env('SEND_MAIL_BCC'));
            }else{
                if($data['custId'] == ''){
                    $cc = \Helpers::ccOrBccEmailsArray($email_content->cc);
                    $bcc = \Helpers::ccOrBccEmailsArray($email_content->bcc);
                }else{
                    $cc = NULL;
                    $bcc = NULL;
                }
                $to = [
                    [
                        'email' => $data["email"], 
                        'name' => null,
                    ]
                ];
            }
            $baseUrl = env('REDIRECT_URL','');
            $attachData = [];
            if(!empty($data['attachment'])){
                $att_name = $data['invoice_no'].'.pdf';
                $attachData[] =
                [
                    'file_path' => $data['attachment'],
                    'file_name' => $att_name,
                    'isBinaryData' => false,
                ];
            }
            $mailData = [
                'email_to' => [$data["email"]],
                'email_cc' => $cc ?? NULL,
                'email_bcc' => $bcc ?? NULL,
                'mail_subject' => $mail_subject,
                'mail_body' => $mail_content,
                'base_url' => $baseUrl,
                'att_name' => $att_name ?? NULL,
                'attachments' => $attachData
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'attachment' => $data['attachment'] ?? NULL,
                'name' => null,
                'att_name' => $att_name ?? NULL,
                'email_type' => $this->func_name,
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->bcc($bcc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
        }
    }

    public function approverMailForPendingCases($attributes){
        $data = unserialize($attributes);
        $this->func_name = __FUNCTION__;
        $email_content = EmailTemplate::getEmailTemplate("APPROVER_MAIL_FOR_PENDING_CASES");
        if ($email_content) {
            $userData['approver_name'] = $data['approver_name'];
            $userData['email'] = $data['email'];
            $rowData = '';
            foreach($data['data'] as $key=>$val){
               $int = $val['interest_rate'] ?? 0.0;
               $requestPara = $val['app_id'].'%'.$val['biz_id'];
            $rowData .='<tr>
              <td
                style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;width: 10%;">
                '.$val['app_code'].'
              </td>
              <td
                style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top; width: 20%;">
                '. $val['customer_name'].'
              </td>
              <td
                style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top; width: 50%;">
                '. $val['biz_entity_name'].'
              </td>
              <td
                style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top; width: 15%;">
                '.\Helpers::formatCurreny($val['prgm_limit_amt']).'
              </td>
              <td
                style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top; width: 5%;">
                '. $int .' %
              </td>
            </tr>';
            }
            $mail_subject = str_replace(['%name'], [$data['approver_name']],$email_content->subject);
            $mail_body = str_replace(
                ['%name', '%allData'],
                [$userData['approver_name'], $rowData],
                $email_content->message
            );
            $cc = array_filter(explode(',', $email_content->cc));
            $bcc = array_filter(explode(',', $email_content->bcc));
            $to = [
                [
                    'email' => $data["email"], 
                    'name' => $data["approver_name"],
                ]
            ];
            $funcName = $this->func_name;
            $baseUrl = env('HTTP_APPURL','');
            $mailData = [
                'email_to' => [$data["email"]],
                'email_cc' => $cc ?? NULL,
                'email_bcc' => $bcc ?? NULL,
                'mail_subject' => $mail_subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'user_name' => $email_content->name,
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->bcc($bcc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
        }
    }



    /**
     * App Security Document Renewal Alert
     * 
     * @param Array $attributes
     */
    public function appSecurityDocRenewalAlert($attributes) {
        $data = unserialize($attributes);
        $this->func_name = __FUNCTION__;
        $email_content = EmailTemplate::getEmailTemplate("APP_SECURITY_DOCUMENT_RENEWAL_ALERT");
        if ($email_content) {
            $userData['user_name'] = $data['user_name'];
            $userData['email'] = $data['email'];
            $rowData = '';
            foreach($data['data'] as $key=>$val){
                $d = $val['due_date']?date('d/m/Y',strtotime($val['due_date'])):'N/A';
                $m = $val['maturity_date']?date('d/m/Y',strtotime($val['maturity_date'])):'N/A';
            $rowData .='<tr>
              <td
                style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                '.$val['doc_type_name'].'
              </td>
              <td
                style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                '. $val['document_number'].'
              </td>
              <td
                style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                '. $d .'
              </td>
              <td
                style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                '. $m .'
              </td>
            </tr>';
            }
            $mail_subject = str_replace(['%user_name'], [$data['user_name']],$email_content->subject);
            $mail_body = str_replace(
                ['%user_name','%email', '%allData'],
                [$userData['user_name'],$userData['email'], $rowData],
                $email_content->message
            );
            $cc = array_filter(explode(',', $email_content->cc));
            $bcc = array_filter(explode(',', $email_content->bcc));
            $to = [
                [
                    'email' => $data["email"], 
                    'name' => $data["user_name"],
                ]
            ];
            $baseUrl = env('HTTP_APPURL','');
            $mailData = [
                'email_to' => [$data["email"]],
                'email_cc' => $cc ?? NULL,
                'email_bcc' => $bcc ?? NULL,
                'mail_subject' => $mail_subject,
                'mail_body' => $mail_body,
                'base_url' => $baseUrl,
            ];
            $mailLogData = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_type' => $this->func_name,
                'user_name' => $email_content->name,
            ];
            // Serialize the data
            $mailDataSerialized = serialize($mailData);
            $mailLogDataSerialized = serialize($mailLogData);
            // Queue the email job
            Mail::to($to)->cc($cc)->bcc($bcc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
        }        
    }
        
    /**
     * EOD Checks Alert
     * 
     * @param Array $attributes
    */
    public function onEODChecksAlert($attributes) {
        $data = unserialize($attributes); 
        $this->func_name = __FUNCTION__;
        $eodCheckData = view('reports.eod_checks')->with(['tally_data' => $data['tally_data'], 'tally_error_data' => $data['tally_error_data']])->render();
                
        $email_content = EmailTemplate::getEmailTemplate("EOD_CHECKS_ALERT");
        if ($email_content) {
            Mail::send('email', ['baseUrl'=> env('HTTP_APPURL',''), 'varContent' => $eodCheckData],
                function ($message) use ($data, $email_content, $eodCheckData) {                    
                    $email = array_filter(explode(',',env('EOD_CHECK_MAIL_TO')));
                    $cc = array_filter(explode(',', $email_content->cc));
                    $bcc = array_filter(explode(',', $email_content->bcc));
                    if (!empty($bcc)) {
                        $message->bcc($bcc);
                    }
                    if (!empty($cc)) {
                        $message->cc($cc);
                    }

                    $mail_subject = $email_content->subject;
                    $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                    $message->to($email);
                    $message->subject($mail_subject);
                    $mailContent = [
                        'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                        'email_to' => $email,
                        'email_cc' => $cc ?? NULL,
                        'email_bcc' => $bcc ?? NULL,
                        'email_type' => $this->func_name,
                        'subject' => $mail_subject,
                        'body' => $eodCheckData,
                    ];
                    FinanceModel::logEmail($mailContent);
            });
        }                
    }

    /**
     * EOD Checks Alert
     * 
     * @param Array $attributes
    */
    public function onDisbPayChecksAlert($attributes) {
        $data = unserialize($attributes);
        $this->func_name = __FUNCTION__;
        $eodCheckData = view('reports.disb_pay_checks')->with(['disbursals' => $data['disbursals'], 'payments' => $data['payments'], 'actualDisbursals' => $data['actualDisbursals'],'actualPayment'=>$data['actualPayment'],'actualRefund'=>$data['actualRefund']])->render();
                
        $email_content = EmailTemplate::getEmailTemplate("EOD_CHECKS_ALERT");
        if ($email_content) {
            Mail::send('email', ['baseUrl'=> env('HTTP_APPURL',''), 'varContent' => $eodCheckData],
                function ($message) use ($data, $email_content, $eodCheckData) {                 
                    $email = array_filter(explode(',',env('EOD_CHECK_MAIL_TO')));
                    $cc = array_filter(explode(',', $email_content->cc));
                    $bcc = array_filter(explode(',', $email_content->bcc));
                    if (!empty($bcc)) {
                        $message->bcc($bcc);
                    }
                    if (!empty($cc)) {
                        $message->cc($cc);
                    }

                    $mail_subject = $email_content->subject;
                    $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                    $message->to($email);
                    $message->subject($mail_subject);
                    $mailContent = [
                        'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                        'email_to' => $email,
                        'email_cc' => $cc ?? NULL,
                        'email_bcc' => $bcc ?? NULL,
                        'email_type' => $this->func_name,
                        'subject' => $mail_subject,
                        'body' => $eodCheckData,
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
            'user.sendconsentotp',
            'App\Inv\Repositories\Events\UserEventsListener@onSendConsentOtp'
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

        $events->listen(
            'APP_SECURITY_DOCUMENT_RENEWAL_ALERT',
            'App\Inv\Repositories\Events\UserEventsListener@appSecurityDocRenewalAlert'
        );

        $events->listen(
            'NON_ANCHOR_CSV_LEAD_UPLOAD',
            'App\Inv\Repositories\Events\UserEventsListener@onNonAnchorLeadUpload'
        );
        
        $events->listen(
            'USER_INVOICE_MAIL',
            'App\Inv\Repositories\Events\UserEventsListener@userInvoiceMail'
        );
        $events->listen(
            'APPROVER_MAIL_FOR_PENDING_CASES',
            'App\Inv\Repositories\Events\UserEventsListener@approverMailForPendingCases'
        );

        $events->listen(
            'NOTIFY_EOD_CHECKS', 
            'App\Inv\Repositories\Events\UserEventsListener@onEODChecksAlert'
        );

        $events->listen(
            'NOTIFY_DISB_PAY_CHECKS', 
            'App\Inv\Repositories\Events\UserEventsListener@onDisbPayChecksAlert'
        );
    }
}
