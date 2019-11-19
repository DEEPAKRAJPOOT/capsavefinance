<?php

namespace App\Helpers;

use Mail;
use Exception;
use Carbon\Carbon;
use App\Helpers\PaypalHelper;
use App\Inv\Repositories\Models\Patent;
use DB;
class Helper extends PaypalHelper
{

    /**
     * Send exception emails
     *
     * @param Exception $exception
     * @param string    $exMessage
     * @param boolean   $handler
     */
    public static function shootDebugEmail($exception, $handler = false)
    {
        $request                 = request();
        $data['page_url']        = $request->url();
        $data['loggedin_userid'] = (auth()->guest() ? 0 : auth()->user()->id);
        $data['ip_address']      = $request->getClientIp();
        $data['method']          = $request->method();
        $data['message']         = $exception->getMessage();
        $data['class']           = get_class($exception);

        if (app()->envrionment('live') === false) {
            $data['request'] = $request->except('password');
        }

        $data['file']  = $exception->getFile();
        $data['line']  = $exception->getLine();
        $data['trace'] = $exception->getTraceAsString();

        $subject = 'Inventrust ('.app()->environment().') '.($handler ?
            '' : 'EXCEPTION').' Error at '.date('Y-m-d D H:i:s T');

        config(['mail.driver' => 'mail']);
        Mail::raw(
            print_r($data, true),
            function ($message) use ($subject) {
            $message->to(config('errorgroup.error_notification_group'))
                ->from(
                    config('errorgroup.error_notification_email'),
                    config('errorgroup.error_notification_from')
                )
                ->subject($subject);
        });
    }

    /**
     * Get exception message w.r.t. application environment
     *
     * @param  Exception $exception
     * @return string
     */
    public static function getExceptionMessage($exception)
    {
        $exMessage = trans('error_messages.generic.failure');

        $actualException = 'Error: '.$exception->getMessage().
            ' . File: '.$exception->getFile().' . Line#: '.$exception->getLine();

        if (config('app.debug') === false) {
            self::shootDebugEmail($exception);
            return $exMessage;
        } else {
            return $actualException;
        }
    }

    
    /*
     * make model popup with Iframe
     * 
     */
    public static function makeIframePopup($modelId, $title){

     //return \App\Inv\Repositories\Models\CorpStatus::all(); 
    
        return "<div  class=\"modal\" id=\"$modelId\">
        <div class=\"modal-dialog modal-lg\">
          <div class=\"modal-content\">
              <div class=\"modal-header\">
              <h4 class=\"modal-title\">$title</h4>
              <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
            </div>
              <div class=\"modal-body\">
              <iframe frameborder=\"0\"></iframe>
            </div>
          </div>
        </div>
    </div>";
    }

    public static function customIsset($obj, $key){
        if(is_null($obj)){
            return '';
        }else if(isset($obj->$key)){
            return $obj->$key;
        }else{
            return '';
        }
    }

}