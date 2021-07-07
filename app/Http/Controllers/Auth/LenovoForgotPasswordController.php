<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\PasswordBroker;
use App\Http\Requests\ResetPasswordFormRequest;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Inv\Repositories\Models\ResetPasswordToken;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Session;
use Event;

class LenovoForgotPasswordController extends Controller
{
    /*
      |--------------------------------------------------------------------------
      | Password Reset Controller
      |--------------------------------------------------------------------------
      |
      | This controller is responsible for handling password reset emails and
      | includes a trait which assists in sending these notifications from
      | your application to your users. Feel free to explore this trait.
      |
     */

use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetLinkEmail() {
        return view('auth.partner_auth.forgot');
    }
    
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(ResetPasswordFormRequest $request)
    {   
        try {
            Session::put('token-email',$request->get('email'));

          
            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.
            /*$response = $this->broker()->sendResetLink(
                $request->only('email')
            );*/
            $user = $this->broker()->getUser($request->only('email'));

                        
            if (is_null($user)) {
                $response = PasswordBroker::INVALID_USER;
                return redirect()->back()->withErrors(['email' => trans($response)]);
            }

            $utype = $user['user_type'];
            $user_type = $request->get('user_type') ?? "2";

            if ($utype !=  $user_type) {
                $response = PasswordBroker::INVALID_USER;
                return redirect()->back()->withErrors(['email' => trans($response)]);
            }
            
            $userMailArr = [];
            $userMailArr['anchor_id'] = $user->anchor_id;
            $userMailArr['email'] = $user->email;
            $userMailArr['name'] = $user->f_name;
            $reset_link = url(config('app.url').route('password.lenevo-reset', $this->broker()->createToken($user), false));
            $userMailArr['reset_link'] = $reset_link;
            
            Event::dispatch("forgot_password", serialize($userMailArr));
            $response = PasswordBroker::RESET_LINK_SENT;
            Session::flash('messages',trans('success_messages.fogot_password_successfully'));
            return redirect()->back()->with('status', trans($response));
            
        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }
    }
    
    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
       // $token = $request;
       $token = key($request->query()); 
       $token_res = ResetPasswordToken::getUserTokenDetails($token)->toArray();
       
     
       if(count($token_res) > 0) {
           $token_res =(object) $token_res[0];
         //  dd($token_res);
           
        if(isset($token_res->is_reset) && $token_res->is_reset == 0) {
            return view('auth.partner_auth.reset')->with(
                ['token' => $token, 'email' => Session::get('token-email')]
            );
        } else {
             die('Token Expired');
        }
       }else if(count($token_res) == 0) {
        return view('auth.partner_auth.reset')->with(
            ['token' => $token, 'email' => Session::get('token-email')]
        );
       } else {         
         die('Token Expired');  
       }
    }
}