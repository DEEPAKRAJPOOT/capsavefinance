<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\PasswordBroker;
use App\Http\Requests\ResetPasswordFormRequest;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Session;
use Event;

class ForgotPasswordController extends Controller
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
        return view('auth.forgot');
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
            $userMailArr['email'] = $user->email;
            $userMailArr['name'] = $user->f_name;
            $reset_link = url(config('app.url').route('password.reset', $this->broker()->createToken($user), false));
            $userMailArr['reset_link'] = $reset_link;
            
            Event::dispatch("forgot_password", serialize($userMailArr));
            $response = PasswordBroker::RESET_LINK_SENT;
            Session::flash('messages',trans('success_messages.fogot_password_successfully'));
            return redirect()->back()->with('status', trans($response));
            
        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }
    }
}