<?php

namespace App\Helpers;

use Mail;
use Auth;
use Exception;
use Carbon\Carbon;
use App\Helpers\PaypalHelper;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\Patent;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\WfStage;
use App\Inv\Repositories\Models\WfAppStage;
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
    public static function makeIframePopup($modelId, $title, $model){

     //return \App\Inv\Repositories\Models\CorpStatus::all(); 
    
        return "<div  class=\"modal\" id=\"$modelId\">
        <div class=\"modal-dialog $model\">
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
  
    /**
     * customIsset
     * 
     * @param type $obj
     * @param type $key
     * @return string
     */
    public static function customIsset($obj, $key){
        if(is_null($obj)){
            return '';
        }else if(isset($obj->$key)){
            return $obj->$key;
        }else{
            return '';
        }
    }    
    
    /**
     * Update workflow app stage
     * 
     * @param string $wf_stage_code
     * @param integer $app_id
     * @param integer $wf_status
     * @return boolean
     */
    public static function updateWfStage($wf_stage_code, $app_id, $wf_status = 0)
    {
        $wfData = WfStage::getWfDetailById($wf_stage_code);
        if ($wfData) {
            $wf_stage_id = $wfData->wf_stage_id;
            $wf_order_no = $wfData->order_no;
            $updateData = [
                'app_wf_status' => $wf_status,
                'is_complete' => $wf_status
            ];
            $appData = Application::getAppData((int)$app_id);
            $user_id = $appData->user_id;
            if ($wf_stage_code == 'new_case') {
                $updateData['biz_app_id'] = $app_id;
                $result = WfAppStage::updateWfStageByUserId($wf_stage_id, $user_id, $updateData);
            } else {
                $result = WfAppStage::updateWfStage($wf_stage_id, $app_id, $updateData);
            }
            if ($wf_status == 1) {
                $nextWfData = WfStage::getNextWfStage($wf_order_no);
                $wfAppStageData = WfAppStage::getAppWfStage($nextWfData->stage_code, $user_id, $app_id);
                if ( !$wfAppStageData ) {
                    $insertData = [
                        'wf_stage_id' => $nextWfData->wf_stage_id,
                        'biz_app_id' => $app_id,
                        'user_id' => $user_id,
                        'app_wf_status' => 0,
                        'is_complete' => 0
                    ];
                    $result = WfAppStage::saveWfDetail($insertData);
                    return $result;
                }
            }
            return $result;
        } else {
            return false;
        }
    }
   
    
    /**
     * Get current workflow stage
     * 
     * @param integer $app_id
     */
    public static function getCurrentWfStage($app_id){
        return WfAppStage::getCurrentWfStage($app_id);
    }
    
    /**
     * Add workflow stage
     * 
     * @param integer $app_id
     */
    public static function addWfAppStage($wf_stage_code, $user_id, $app_id=0, $wf_status = 0){
        $wfData = WfStage::getWfDetailById($wf_stage_code);        
        if ($wfData) {
            $wfAppStageData = WfAppStage::getAppWfStage($wf_stage_code, $user_id, $app_id);
            if ( !$wfAppStageData ) {            
                $arrData = [
                    'wf_stage_id' => $wfData->wf_stage_id,
                    'user_id' => $user_id,
                    'biz_app_id' => $app_id,
                    'app_wf_status' => $wf_status,
                    'is_complete' => $wf_status
                ];        
                return WfAppStage::saveWfDetail($arrData);
            }
        } else {
            return false;
        }
    }
     /**
     * uploading document data
     *
     * @param Exception $exception
     * @param string    $exMessage
     * @param boolean   $handler
     */
    public static function uploadAppFile($attributes, $appId) 
    {
        $userId = Auth::user()->user_id;
        $inputArr = [];
        if($attributes['doc_file']) {
            if(!Storage::exists('/public/user/' .$userId. '/' .$appId)) {
                Storage::makeDirectory('/public/user/' .$userId. '/' .$appId, 0775, true);
            }
            $path = Storage::disk('public')->put('/user/' .$userId. '/' .$appId, $attributes['doc_file'], null);
            $inputArr['file_path'] = $path;
        }
             
        $inputArr['file_type'] = $attributes['doc_file']->getClientMimeType();
        $inputArr['file_name'] = $attributes['doc_file']->getClientOriginalName();
        $inputArr['file_size'] = $attributes['doc_file']->getClientSize();
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;
        
        return $inputArr;
    }
    
    /**
     * uploading document data
     *
     * @param Exception $exception
     * @param string    $exMessage
     * @param boolean   $handler
     */
    public static function uploadAppFiles($attributes) 
    {
        $userId = Auth::user()->user_id;
        $inputArr = [];
        $count = count($attributes['doc_file']);
        for ( $i=0; $i < $count; $i++) 
        {   
            if($attributes['doc_file'][$i]) {
                if(!Storage::exists('/public/user/' .$userId. '/' .$attributes['appId'])) {
                    Storage::makeDirectory('/public/user/' .$userId. '/' .$attributes['appId'], 0775, true);
                }
                $path = Storage::disk('public')->put('/user/' .$userId. '/' .$attributes['appId'], $attributes['doc_file'][$i], null);
                $inputArr[$i]['file_path'] = $path;
            }
             
            $inputArr[$i]['file_type'] = $attributes['doc_file'][$i]->getClientMimeType();
            $inputArr[$i]['file_name'] = $attributes['doc_file'][$i]->getClientOriginalName();
            $inputArr[$i]['file_size'] = $attributes['doc_file'][$i]->getClientSize();
            $inputArr[$i]['file_encp_key'] =  md5('2');
            $inputArr[$i]['created_by'] = 1;
            $inputArr[$i]['updated_by'] = 1;
        }
        
        return $inputArr;
    }
    
    /**
     * app_doc table data
     *
     * @param Exception $exception
     * @param string    $exMessage
     * @param boolean   $handler
     */
    public static function appDocData($attributes, $fileId) 
    {
        
        $inputArr = [];

        $inputArr['app_id']  = (isset($attributes['doc_id'])) ? $attributes['doc_id'] : 0;   
        $inputArr['doc_id']  = (isset($attributes['doc_id'])) ? $attributes['doc_id'] : 0   ;  
        $inputArr['doc_name']  = (isset($attributes['doc_name'])) ? $attributes['doc_name'] : ''; 
        $inputArr['finc_year']  = (isset($attributes['finc_year'])) ? $attributes['finc_year'] : ''; 
        $inputArr['gst_month']  = (isset($attributes['gst_month'])) ? $attributes['gst_month'] : ''; 
        $inputArr['gst_year']  = (isset($attributes['gst_year'])) ? $attributes['gst_year'] : ''; 
        $inputArr['doc_id_no']  = (isset($attributes['doc_id_no'])) ? $attributes['doc_id_no'] : ''; 
        $inputArr['file_id']  = $fileId; 
        $inputArr['is_upload'] = 1;
        $inputArr['created_by'] = 1;
        
        return $inputArr;
    }
}