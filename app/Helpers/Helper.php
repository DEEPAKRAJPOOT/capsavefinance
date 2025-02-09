<?php

namespace App\Helpers;

use DB;
use Mail;
use Auth;
use Exception;
use Carbon\Carbon;
use App\Helpers\PaypalHelper;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\Patent;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\WfStage;
use App\Inv\Repositories\Models\WfAppStage;
use App\Inv\Repositories\Models\AppAssignment;
use App\Inv\Repositories\Models\Master\Permission;
use App\Inv\Repositories\Models\Master\PermissionRole;
use App\Inv\Repositories\Models\Master\RoleUser;
use App\Inv\Repositories\Models\Master\Role;
use App\Inv\Repositories\Models\AppApprover;
use App\Inv\Repositories\Models\Master\Equipment;
use App\Inv\Repositories\Models\LeadAssign;
use App\Inv\Repositories\Models\UserBankAccount;
use App\Inv\Repositories\Models\CamReviewerSummary;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\AppOfferAdhocLimit;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\Lms\CronLog;
use Illuminate\Http\File;
use App\Inv\Repositories\Models\Lms\ApprovalRequest;
use Illuminate\Contracts\Support\Renderable;
use Session;
use Zip;
use App\Inv\Repositories\Models\AnchorUser;
use App\Inv\Repositories\Models\Anchor;
use App\Inv\Repositories\Models\AppSanctionLetter;
use App\Inv\Repositories\Models\UserFile;
use App\Inv\Repositories\Models\Program;
use App\Inv\Repositories\Models\ColenderShare;
use App\Inv\Repositories\Models\LmsUsersLog;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursedDetail;
use App\Inv\Repositories\Models\AppSecurityDoc;
use App\Inv\Repositories\Models\TallyFactVoucher;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\TransFactVoucher;
use App\Inv\Repositories\Models\AppGroupDetail;
use App\Inv\Repositories\Models\UcicUser;
use App\Inv\Repositories\Models\UcicUserUcic;
use App\Inv\Repositories\Models\AppDocumentFile;
use App\Inv\Repositories\Models\OfferPTPQ;
use App\Inv\Repositories\Models\UserAppDoc;
use App\Inv\Repositories\Models\CamReviewSummRiskCmnt;
use App\Inv\Repositories\Contracts\Traits\CommonTrait;

class Helper extends PaypalHelper
{
    use CommonTrait;
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

        if (config('app.env') == "production") {
            $data['request'] = $request->except('password');
        }

        $data['file']  = $exception->getFile();
        $data['line']  = $exception->getLine();
        $data['trace'] = $exception->getTraceAsString();

        $subject = 'RentAlpha (' . app()->environment() . ') ' . ($handler ?
            '' : 'EXCEPTION') . ' Error at ' . date('Y-m-d D H:i:s T');

        //config(['mail.driver' => 'mail']);                
        Mail::raw(
            print_r($data, true),
            function ($message) use ($subject) {
                $message->to(config('errorgroup.error_notification_group'))
                    ->from(
                        config('errorgroup.error_notification_email'),
                        config('errorgroup.error_notification_from')
                    )
                    ->subject($subject);
            }
        );
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

        $actualException = 'Error: ' . $exception->getMessage() .
            ' . File: ' . $exception->getFile() . ' . Line#: ' . $exception->getLine();

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
    public static function makeIframePopup($modelId, $title, $model)
    {

        //return \App\Inv\Repositories\Models\CorpStatus::all(); 
        return "<div  class=\"modal\" id=\"$modelId\" data-keyboard=\"false\" data-backdrop=\"static\">
        <div class=\"modal-dialog $model\">
          <div class=\"modal-content\">
              <div class=\"modal-header\">
              <h4 class=\"modal-title\">$title</h4>
              <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
            </div>
              <div class=\"modal-body append_in-frame\">
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
    public static function customIsset($obj, $key)
    {
        if (is_null($obj)) {
            return '';
        } else if (isset($obj->$key)) {
            return $obj->$key;
        } else {
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
    public static function updateWfStage($wf_stage_code, $app_id, $wf_status = 0, $assign_role = false, $addl_data = [], $sendEmail = true)
    {
        $wfData = WfStage::getWfDetailById($wf_stage_code);
        if ($wfData) {
            $wf_stage_id = $wfData->wf_stage_id;
            $wf_order_no = $wfData->order_no;
            $assignedRoleId = $wfData->role_id;
            $updateData = [
                'app_wf_status' => $wf_status,
                'is_complete' => $wf_status
            ];
            $appData = Application::getAppData((int) $app_id);
            $user_id = (int)$appData->user_id;
            if ($wf_stage_code == 'new_case') {
                $updateData['biz_app_id'] = $app_id;
                $result = WfAppStage::updateWfStageByUserId($wf_stage_id, $user_id, $updateData);
                self::updateAppCurrentStatus($app_id, config('common.mst_status_id.APP_INCOMPLETE'));
            } else {
                $result = WfAppStage::updateWfStage($wf_stage_id, $app_id, $updateData);
            }
            if ($wf_status == 1) {
                $nextWfData = WfStage::getNextWfStage($wf_order_no);
                $wfAppStageData = WfAppStage::getAppWfStage($nextWfData->stage_code, $user_id, $app_id);
                if (!$wfAppStageData) {
                    // $wf_disb_status = $nextWfData->stage_code == 'disbursed' ? 1 : 0;
                    $wf_disb_status = 0;
                    $insertData = [
                        'wf_stage_id' => $nextWfData->wf_stage_id,
                        'biz_app_id' => $app_id,
                        'user_id' => $user_id,
                        'app_wf_status' => $wf_disb_status,
                        'is_complete' => $wf_disb_status
                    ];
                    $result = WfAppStage::saveWfDetail($insertData);
                } else {
                    $result = $wfAppStageData;
                }

                if ($assign_role) {
                    //get role id by wf_stage_id
                    $data = WfStage::find($result->wf_stage_id);
                    AppAssignment::updateAppAssignById((int) $app_id, ['is_owner' => 0]);
                    //update assign table
                    $dataArr = [];
                    $dataArr['from_id'] = \Auth::user()->user_id;
                    if ($data->role_id == 4) {
                        //$toUserId = User::getLeadSalesManager($user_id);
                        $userData = User::getfullUserDetail($user_id);
                        /*if ($userData && !empty($userData->anchor_id)) {
                            $toUserId = User::getLeadSalesManager($user_id);
                        } else {*/
                            $toUserId = LeadAssign::getAssignedSalesManager($user_id);
                        /*}*/
                        $dataArr['to_id'] = $toUserId;
                        $dataArr['role_id'] = null;
                    } else if (isset($addl_data['to_id']) && !empty($addl_data['to_id'])) {
                        $toUserId = $addl_data['to_id'];
                        $dataArr['to_id'] = $toUserId;
                        $dataArr['role_id'] = null;
                    } else {
                        $dataArr['to_id'] = null;
                        $dataArr['role_id'] = $data->role_id;
                    }
                    $dataArr['assigned_user_id'] = $user_id;
                    $dataArr['app_id'] = $app_id;
                    $dataArr['assign_status'] = '0';
                    $dataArr['assign_type'] = '2';
                    $dataArr['sharing_comment'] = isset($addl_data['sharing_comment']) ? $addl_data['sharing_comment'] : '';
                    $dataArr['is_owner'] = 1;

                    AppAssignment::saveData($dataArr, $sendEmail);

                    return $data;
                } else {
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
    public static function getCurrentWfStage($app_id)
    {
        return WfAppStage::getCurrentWfStage($app_id);
    }

    /**
     * Add workflow stage
     * 
     * @param integer $app_id
     */
    public static function addWfAppStage($wf_stage_code, $user_id, $app_id = 0, $wf_status = 0)
    {
        $wfData = WfStage::getWfDetailById($wf_stage_code);
        if ($wfData) {
            $wfAppStageData = WfAppStage::getAppWfStage($wf_stage_code, $user_id, $app_id);
            if (!$wfAppStageData) {
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
        $userId = Application::where('app_id', $appId)->pluck('user_id')->first();
        $inputArr = [];
        if(!empty($attributes['doc_file'])) {
            if ($attributes['doc_file']) {
                if (!Storage::exists('/public/user/' . $userId . '/' . $appId)) {
                    Storage::makeDirectory('/public/user/' . $userId . '/' . $appId, 0777, true);
                }
                $path = Storage::put('public/user/' . $userId . '/' . $appId, $attributes['doc_file'], null);
                $inputArr['file_path'] = str_replace('public/', '', $path);
            }
        }
        $inputArr['file_type'] = !empty($attributes['doc_file']) ? $attributes['doc_file']->getClientMimeType() : '';
        $inputArr['file_name'] = !empty($attributes['doc_file']) ? $attributes['doc_file']->getClientOriginalName() : '';
        $inputArr['file_size'] = !empty($attributes['doc_file']) ? $attributes['doc_file']->getSize() : '';
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;

        return $inputArr;
    }
    public static function uploadUserLMSFile($attributes, $userId)
    {
        $inputArr = [];
        if ($attributes['doc_file']) {
            if (!Storage::exists('/public/Lms/' . $userId)) {
                Storage::makeDirectory('/public/Lms/' . $userId, 0777, true);
            }
            $path = Storage::put('public/Lms/' . $userId, $attributes['doc_file'], null);
            $inputArr['file_path'] = str_replace('public/', '', $path);
        }

        $inputArr['file_type'] = $attributes['doc_file']->getClientMimeType();
        $inputArr['file_name'] = $attributes['doc_file']->getClientOriginalName();
        $inputArr['file_size'] = $attributes['doc_file']->getSize();
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;

        return $inputArr;
    }
    public static function uploadInvoiceFile($attributes, $batch_id)
    {
       $userId = Auth::user()->user_id;
       $inputArr = []; 
       $attr[] = "";   
       $fp = file($attributes['file_id'], FILE_SKIP_EMPTY_LINES);
      
       if(count($fp) > 51)
       {
             $attr['status'] =0;
             $attr['message']= 'You can not upload more than 50 records in csv file.';
             return  $attr;   
       } 
      else if($attributes['file_id']->getSize() > 1000000)
       {
             $attr['status'] =0;
             $attr['message']= 'File size should be upload Only 1 Mb.';
             return  $attr;   
       }
       else if($attributes['file_id']->getClientOriginalExtension()!='csv')
       {
             $attr['status'] =0;
             $attr['message']= 'Csv file format is not correct, only csv file is allowed.';
             return  $attr;   
       }

       if ($attributes['file_id']) {
            if (!Storage::exists('/public/user/' . $userId . '/invoice/' . $batch_id)) {
                Storage::makeDirectory('/public/user/' . $userId . '/invoice/' . $batch_id, 0777, true);
            }

                $extension = $attributes['file_id']->getClientOriginalExtension();
                $name   = $attributes['file_id']->getClientOriginalName();
                $name  =  explode('.',$name);
                $filename =  $name[0].'.'.$extension;
             $path = Storage::putFileAs('public/user/' . $userId . '/invoice/' . $batch_id, $attributes['file_id'], $filename); 
             $inputArr['file_path'] = str_replace('public/', '', $path);
            }   

        $inputArr['file_type'] = $attributes['file_id']->getClientMimeType();
        $inputArr['file_name'] = $attributes['file_id']->getClientOriginalName();
        $inputArr['file_size'] = $attributes['file_id']->getSize();
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['status'] =1;
       return $inputArr;
    }

      public static function uploadZipInvoiceFile($attributes, $batch_id)
    {
     
       $userId = Auth::user()->user_id;
       $inputArr = []; 
       $attr[] = "";   
        if(!empty($attributes['file_image_id'])) {
            if($attributes['file_image_id']->getSize() > 30000000)
            {
                    $attr['status'] =0;
                    $attr['message']= 'File size should be upload Only 30 Mb.';
                    return  $attr;   
            }
            else if($attributes['file_image_id']->getClientOriginalExtension()!='zip')
            {
                    $attr['status'] =0;
                    $attr['message']= 'Zip File format is not correct, only zip file is allowed.';
                    return  $attr;   
            }
                if ($attributes['file_image_id']) {
                if (!Storage::exists('/public/user/' . $userId . '/invoice/' . $batch_id.'/zip')) {
                    Storage::makeDirectory('/public/user/' . $userId . '/invoice/' . $batch_id.'/zip', 0777, true);
                }
                    $zipExtension = $attributes['file_image_id']->getClientOriginalExtension();
                    $zipName   = $attributes['file_image_id']->getClientOriginalName();
                    $zipName  =  explode('.',$zipName);
                    $zipFilename =  $zipName[0].'.'.$zipExtension;
                    $path = Storage::putFileAs('public/user/' . $userId . '/invoice/' . $batch_id.'/zip', $attributes['file_image_id'], $zipFilename); 
                    $extract_path =  'public/user/' . $userId . '/invoice/' . $batch_id.'/zip';
                    
                    $zip =  Zip::open($attributes['file_image_id']);
                    if(count($zip->listFiles()) > 50)
                        {
                            $attr['status'] =0;
                            $attr['message']= 'You can not archive more than 50 file.';
                            return  $attr;   
                        } 
                        $tempPath = Session::getId().'/'.$extract_path;
                        $resExtract  =  $zip->extract(Storage::disk('temp')->path($tempPath));
                        $files = Storage::disk('temp')->allFiles($tempPath);
                        foreach ($files as $value) {
                           Storage::put($extract_path.'/'.basename($value), Storage::disk('temp')->get($value));
                           Storage::disk('temp')->delete($value);
                        }
                        $inputArr['file_path'] = str_replace('public/', '', $path);
                    }   
            $inputArr['file_type'] = $attributes['file_image_id']->getClientMimeType();
            $inputArr['file_name'] = $attributes['file_image_id']->getClientOriginalName();
            $inputArr['file_size'] = $attributes['file_image_id']->getSize();
            $inputArr['file_encp_key'] =  md5('2');
            $inputArr['created_by'] = 1;
            $inputArr['updated_by'] = 1;
            $inputArr['status'] =1;
            return $inputArr;
        }
    }
    
     public static function ImageChk($file_name,$batch_id)
    {
        $userId = Auth::user()->user_id;
        $inputArr = [];
        if (Storage::exists('/public/user/' . $userId . '/invoice/' . $batch_id.'/zip/'.$file_name))
         {
            $pathToFile = Storage::path('public/user/' . $userId . '/invoice/' . $batch_id.'/zip/'.$file_name);
            $fileSize = Storage::size('public/user/' . $userId . '/invoice/' . $batch_id.'/zip/'.$file_name);
            $attributes =  pathinfo($pathToFile);
            $realPath = '/user/' . $userId . '/invoice/' . $batch_id.'/zip/'.$attributes['basename'];
            if($fileSize > 1000000)
            {
                      unlink($pathToFile);
                      $inputArr['status'] =0;
                      $inputArr['message']= 'Following files ('.$file_name.') has been auto-cancel due to file size limit, the file size should not be more than 1 MB.';
                      return  $inputArr;    
            }
            $inputArr['status'] =1;
            $inputArr['file_path'] = $realPath;
            $inputArr['file_type'] = $attributes['extension'];
            $inputArr['file_name'] = $attributes['basename'];
            $inputArr['file_size'] = $fileSize;
            $inputArr['file_encp_key'] =  md5('2');
            return $inputArr;
         }
         else
         {
            $inputArr['status'] =0;
            return  $inputArr; 
         }
        
    }
    
   
     
    /**
     * uploading document data
     *
     * @param Exception $exception
     * @param string    $exMessage
     * @param boolean   $handler
     */
    public static function uploadAnchorFile($attributes, $anchorId)
    {
        $inputArr = [];
        if ($attributes['doc_file']) {
            if (!Storage::exists('/public/anchor/' . $anchorId)) {
                Storage::makeDirectory('/public/anchor/' . $anchorId, 0777, true);
            }
            $path = Storage::put('public/anchor/' . $anchorId, $attributes['doc_file'], null);
            $inputArr['file_path'] = str_replace('public/', '', $path);
        }

        $inputArr['file_type'] = $attributes['doc_file']->getClientMimeType();
        $inputArr['file_name'] = $attributes['doc_file']->getClientOriginalName();
        $inputArr['file_size'] = $attributes['doc_file']->getSize();
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;

        return $inputArr;
    }

    /**
     * Save cam pdf document
     *
     * @param Exception $exception
     * @param string    $exMessage
     * @param boolean   $handler
     */
    public static function generateCamPdf($appId, $bizId, $pdfContent)
    {
        $inputArr = [];
        if ($pdfContent) {
            if (!Storage::exists('/public/cam/' . $appId)) {
                Storage::makeDirectory('/public/cam/' . $appId, 0777, true);
            }
            if (!Storage::exists('/public/cam/' . $appId . "/" . config('common.PRODUCT.LEASE_LOAN'))) {
                Storage::makeDirectory('/public/cam/' . $appId . "/" . config('common.PRODUCT.LEASE_LOAN'), 0777, true);
            }
            $businessDetails = Business::find($bizId);
            $fileName ='CAM_'.$appId.'_'.$businessDetails->biz_entity_name.'.pdf'; // 'CamReport_'.$appId."_".time().".pdf";
            $path = "public/cam/" .$appId."/".config('common.PRODUCT.LEASE_LOAN')."/".$fileName;            
            $tempPath = Storage::put($path, $pdfContent);
            $dbpath = "cam/" . $appId . "/" . config('common.PRODUCT.LEASE_LOAN') . "/" . $fileName;
            $inputArr['file_path'] = str_replace('public/', '', $dbpath);
        }

        $inputArr['file_type'] = Storage::mimeType($path);
        $inputArr['file_name'] = $fileName;
        $inputArr['file_size'] = Storage::size($path);
        $inputArr['file_encp_key'] =  md5(time());
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;

        return $inputArr;
    }

    /**
     * uploading document data to s3 bucket
     *
     * @param Exception $exception
     * @param string    $exMessage
     * @param boolean   $handler
     */
    public static function uploadAwsS3Bucket($s3path, $attributes, $filename = null){
        $inputArr = [];
        // if (isset($attributes['doc_file'])) {
    
        //     if (!Storage::exists($s3path)) {
        //         Storage::makeDirectory($s3path, 0777, true);
        //     }
        //     $path = Storage::putFileAs($s3path, $attributes['doc_file'], $filename);
        //     $inputArr['file_path'] = ltrim($s3path, '/').'/'.basename($path);
        //     $inputArr['file_type'] = $attributes['doc_file']->getMimeType();
        //     $inputArr['file_name'] = $attributes['doc_file']->getClientOriginalName();
        //     $inputArr['file_size'] = $attributes['doc_file']->getSize();
            
        // }else if(isset($attributes['upload_unsettled_trans'])) {
        //     $defaultPath = Storage::path('');
        //     $s3path = str_replace($defaultPath, '', $s3path);
        //     if (!Storage::exists($s3path)) {
        //         Storage::makeDirectory($s3path, 0777, true);
        //     }
        //     $path = Storage::putFileAs($s3path, $attributes['upload_unsettled_trans'], $filename);
        //     $inputArr['file_path'] = ltrim($s3path, '/').'/'.basename($path);
        //     $inputArr['file_type'] = $attributes['upload_unsettled_trans']->getMimeType();
        //     $inputArr['file_name'] = $attributes['upload_unsettled_trans']->getClientOriginalName();
        //     $inputArr['file_size'] = $attributes['upload_unsettled_trans']->getSize();
    
        // }else if(isset($attributes['file_contents'])) {
        //     $defaultPath = Storage::path('public');
        //     $s3path = str_replace($defaultPath, '', $s3path);
        //     if (!Storage::exists(dirname($s3path))) {
        //         Storage::makeDirectory(dirname($s3path), 0777, true);
        //     }
        //     $isSaved = Storage::put($s3path, $attributes['file_contents']);
        //     if ($isSaved) {
        //         $mimetype = Storage::getMimeType($s3path);
        //         $metadata = Storage::getMetaData($s3path);
        //         $inputArr['file_path'] = ltrim($s3path, '/');
        //         $inputArr['file_type'] = $mimetype;
        //         $inputArr['file_name'] = basename($s3path);
        //         $inputArr['file_size'] = $metadata['size'];
        //     }
        // }else if(isset($attributes['file_image_id'])) {
    
        //     if (!Storage::exists($s3path)) {
        //         Storage::makeDirectory($s3path, 0777, true);
        //     }
        //     $path = Storage::putFileAs($s3path, $attributes['file_image_id'], $filename);
        //     $inputArr['file_path'] = ltrim($s3path, '/').'/'.basename($path);
        //     $inputArr['file_type'] = $attributes['file_image_id']->getMimeType();
        //     $inputArr['file_name'] = $attributes['file_image_id']->getClientOriginalName();
        //     $inputArr['file_size'] = $attributes['file_image_id']->getSize();
    
        // }else 
        if(isset($attributes['temp_file_path'])) {
            $defaultPath = Storage::path('');
            $s3path = str_replace($defaultPath, '', $s3path);
            if (!Storage::exists($s3path)) {
                Storage::makeDirectory($s3path, 0777, true);
            }
            Storage::putFileAs($s3path, $attributes['temp_file_path'], $filename);
            return ltrim($s3path, '/').'/'.$filename;            
        }
        
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;
        $inputArr['status'] =1;
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
        for ($i = 0; $i < $count; $i++) {
            if ($attributes['doc_file'][$i]) {
                if (!Storage::exists('/public/user/' . $userId . '/' . $attributes['appId'])) {
                    Storage::makeDirectory('/public/user/' . $userId . '/' . $attributes['appId'], 0775, true);
                }
                $path = Storage::put('public/user/' . $userId . '/' . $attributes['appId'], $attributes['doc_file'][$i], null);
                $inputArr[$i]['file_path'] = str_replace('public/', '', $path);
            }

            $inputArr[$i]['file_type'] = $attributes['doc_file'][$i]->getMimeType();
            $inputArr[$i]['file_name'] = $attributes['doc_file'][$i]->getClientOriginalName();
            $inputArr[$i]['file_size'] = $attributes['doc_file'][$i]->getSize();
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

        $inputArr['app_id']  = (isset($attributes['app_id'])) ? $attributes['app_id'] : 0;
        $inputArr['doc_id']  = (isset($attributes['doc_id'])) ? $attributes['doc_id'] : 0;
        $inputArr['biz_owner_id']  = (isset($attributes['owner_id'])) ? $attributes['owner_id'] : 0;
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

    /**
     * Get current workflow stage by Role id
     * 
     * @param integer $app_id
     */
    public static function getCurrentWfStagebyRole($roleId, $user_journey=2, $wf_start_order_no=0, $orderBy='ASC')
    {
        return WfStage::getCurrentWfStagebyRole($roleId, $user_journey, $wf_start_order_no, $orderBy);
    }

    /**
     * Update workflow stages to move the stages back
     * 
     * @param integer $app_id
     * @param integer $from_wf_stage_order_no
     * @param integer $to_wf_stage_order_no
     * @param integer $wf_status
     * @param integer $assign_role
     * @param array $addl_data
     * 
     * @return boolean
     */
    public static function updateWfStageManual($app_id, $from_wf_stage_order_no, $to_wf_stage_order_no, $wf_status = 2, $assign_role_uid = null, $addl_data = [])
    {
        $appData = Application::getAppData((int) $app_id);
        $user_id = $appData->user_id;
        if ($from_wf_stage_order_no < $to_wf_stage_order_no) {
            for ($wf_order_no = $from_wf_stage_order_no; $wf_order_no <= $to_wf_stage_order_no; $wf_order_no++) {
                $wfData = WfStage::getWfDetailByOrderNo($wf_order_no);
                $wf_stage_id = $wfData->wf_stage_id;
                $updateData = [
                    'app_wf_status' => $wf_status,
                    'is_complete' => $wf_status
                ];
                WfAppStage::updateWfStage($wf_stage_id, $app_id, $updateData);
            }

            if ($assign_role_uid) {
                AppAssignment::updateAppAssignById((int) $app_id, ['is_owner' => 0]);
                //update assign table
                $dataArr = [];
                $dataArr['from_id'] = \Auth::user()->user_id;
                $dataArr['to_id'] = $assign_role_uid;
                $dataArr['role_id'] = null; //$assign_role;
                $dataArr['assigned_user_id'] = $user_id;
                $dataArr['app_id'] = $app_id;
                $dataArr['assign_status'] = '0';
                $dataArr['assign_type'] = '3';
                $dataArr['sharing_comment'] = isset($addl_data['sharing_comment']) ? $addl_data['sharing_comment'] : '';;
                $dataArr['is_owner'] = 1;

                AppAssignment::saveData($dataArr);
            }
        }
        return true;
    }

    /**
     * Get permission by Role id
     * 
     * @param integer $app_id
     */
    public static function getByParent($parentId, $isDisplay)
    {
        return Permission::getByParent($parentId, $isDisplay);
    }
    /**
     * Get permission by Role id
     * 
     * @param integer $app_id
     */
    public static function checkRole($parentId, $role_id)
    {
        return PermissionRole::checkRole($parentId, $role_id);
    }

    /**
     * Redirect workflow stage next to completed stage
     * 
     * @param integer $app_id
     * @return boolean
     */
    public static function getWfRedirectRoute($user_id)
    {
        $user = \Auth::user();

        $apps = Application::getAllAppsByUserId($user_id);
        if (count($apps) > 1) {
            $appData = Application::getLatestApp($user_id);
        } else if (count($apps) == 1) {
            $appData = $apps[0];
        } else {
            $appData = null;
        }
        $wf_order_no = 0;
        $app_id = 0;
        $redirectUrl = '';
        $wf_stages = ['new_case', 'biz_info', 'promo_detail', 'doc_upload', 'app_submitted'];
        if ($appData) {
            $app_id = $appData ? $appData->app_id : 0;
            $last_completed_wf_stage = WfAppStage::getCurrentWfStage($app_id);
            $wf_order_no = $last_completed_wf_stage->order_no;
            $wf_data = WfStage::getNextWfStage($wf_order_no);
            if ($user && $user->user_type == '1' && in_array($wf_data['stage_code'], $wf_stages)) {
                $redirectUrl = route($wf_data['route_name'], ['app_id' =>  $appData->app_id, 'user_id' =>  $appData->user_id, 'biz_id' => $appData->biz_id]);
            }
        } else {
            if (count($apps) == 0) {
                $wfAppStageData = WfAppStage::getAppWfStage('biz_info', $user_id, $app_id);
                if (!$wfAppStageData) {
                    $wf_data = WfStage::getNextWfStage($wf_order_no);
                    if ($user && $user->user_type == '1' && in_array($wf_data['stage_code'], $wf_stages)) {
                        $redirectUrl = route($wf_data['route_name'], ['user_id' => $user_id]);
                    }
                }
            }
        }

        $wfRedirectData = [
            'wf_order_no' => $wf_order_no,
            'redirect_url'  => $redirectUrl
        ];

        return $wfRedirectData;
    }

    /**
     * Get Latest Application Data
     * 
     * @param integer $user_id
     * @return mixed
     */
    public static function getLatestAppData($user_id)
    {
        $appData = Application::getLatestApp($user_id);
        return $appData ? $appData : null;
    }

    /**
     * Assign Application to User
     * 
     * @param integer $to_userid
     * @param integer $app_id
     */
    public static function assignAppToUser($to_userid, $app_id, $app_user_id = null)
    {
        if (is_null($app_user_id)) {
            $appData = Application::getAppData((int) $app_id);
            $app_user_id = $appData->user_id;
        }
        AppAssignment::updateAppAssignById((int) $app_id, ['is_owner' => 0]);
        //update assign table
        $dataArr = [];
        $dataArr['from_id'] = \Auth::user()->user_id;
        $dataArr['to_id'] = $to_userid;
        $dataArr['role_id'] = null;
        $dataArr['assigned_user_id'] = $app_user_id;
        $dataArr['app_id'] = $app_id;

        $whereCondition = $dataArr;

        $dataArr['assign_status'] = '0';
        $dataArr['sharing_comment'] = "";
        $dataArr['is_owner'] = 1;

        $assignData = AppAssignment::getAppAssignmentData($whereCondition);

        if (!$assignData) {
            AppAssignment::saveData($dataArr);
        } else {
            AppAssignment::updateData(['is_owner' => 1], $assignData->app_assign_id);
        }

        $application = Application::updateAppDetails($app_id, ['is_assigned' => 1]);
    }

    /**
     * Get User Role
     * 
     * @param integer $user_id | default
     */
    public static function getUserRole($user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = \Auth::user()->user_id;
        }
        $roleData = User::getBackendUser($user_id);
        return $roleData;
    }
    /**
     * Get User Role
     * 
     * @param integer $user_id | default
     */
    public static function getUserInfo($user_id = null)
    {
        $user_id = !is_null($user_id) ? (int) $user_id : null;
        $getUserInfo = User::getfullUserDetail($user_id);
        return $getUserInfo;
    }


    /**
     * Check permission  
     *      * 
     * @param integer $user_id | default
     */
    public static function checkPermission($routePerm)
    {
        $user_id = \Auth::user()->user_id;
        $roleData = User::getBackendUser($user_id);

        if ($roleData[0]->is_superadmin == 1) {
            return true;
        }
        $role_id = $roleData[0]->id;
        $prData = PermissionRole::getPermissionByRoleID($role_id)->toArray();
        $routes = Permission::getPermissionByArr($prData)->toArray();
        $check = in_array($routePerm, $routes);
        return $check;
    }



    /**
     * 
     * @param type $wf_stage_code
     * @param type $app_id
     * @return int
     */
    public static function isWfStageCompleted($wf_stage_code, $app_id)
    {
        $isWfStageCompleted = 0;
        $wfData = WfStage::getWfDetailById($wf_stage_code);
        if ($wfData) {
            //$wf_stage_id = $wfData->wf_stage_id;
            $wf_order_no = $wfData->order_no;
            //$assignedRoleId = $wfData->role_id;

            $last_completed_wf_stage = WfAppStage::getCurrentWfStage($app_id);
            $app_wf_order_no = $last_completed_wf_stage ? $last_completed_wf_stage->order_no : 0;
            if ($app_wf_order_no >= $wf_order_no) {
                $isWfStageCompleted = 1;
            }
        }
        return $isWfStageCompleted;
    }

    /**
     * Get Next Workflow stage by workflow order no
     * 
     * @param integer $wf_order_no
     * @return mixed
     */
    public static function getNextWfStage($wf_order_no)
    {
        return WfStage::getNextWfStage($wf_order_no);
    }


    /**
     * Get aal role
     *      * 
     * @param integer $user_id | default
     */
    public static function getAllRole()
    {
        $data = Role::getAllRole();
        return $data;
    }

    /**
     * Format Currency
     * 
     * @param decimal $amount
     * @param string $locale | optional
     * @return string
     */
    public static function formatCurreny($amount, $locale = 'en_IN', $decimal = false, $prefixCurrency = true)
    {
        //setlocale(LC_MONETARY, $locale);
        //$formattedAmount = money_format('%!i', $amount);
        $currency = '&#8377;';
        $amount = !$decimal ? (int) $amount : $amount;
        $formattedAmount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $amount);
        if ($prefixCurrency) {
            $formattedAmount = "$currency $formattedAmount";
        }
        return $formattedAmount;
    }



    /**
     * Format Currency
     * 
     * @param decimal $amount
     * @param string $locale | optional
     * @return string
     */
    public static function roundFormatCurreny($amount, $locale = 'en_IN', $decimal = false, $prefixCurrency = true)
    {
        //setlocale(LC_MONETARY, $locale);
        //$formattedAmount = money_format('%!i', $amount);
        $currency = '&#8377;';
        $amount = !$decimal ? round($amount) : $amount;
        $formattedAmount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $amount);
        if ($prefixCurrency) {
            $formattedAmount = $currency . $formattedAmount;
        }
        return $formattedAmount;
    }

    /**
     * Workflow stage to process
     * 
     * @param integer $app_id
     */
    public static function getWfStageToProcess($app_id)
    {
        $currStage = self::getCurrentWfStage($app_id);
        if (!$currStage) {
            return false;
        }
        $wf_order_no = $currStage->order_no;
        $currStage = self::getNextWfStage($wf_order_no);
        return $currStage;
    }

    /**
     * Get aal role
     *      * 
     * @param integer $user_id | default
     */
    public static function getAllUsersByRoleId($role_id)
    {
        //$data = RoleUser::getAllUsersByRoleId($role_id);
        $users = RoleUser::getBackendUsersByRoleId($role_id);
        $data = [];
        foreach ($users as $user) {
            $data[$user->user_id] = $user->f_name . ' ' . $user->l_name;
        }
        return $data;
    }


    /**
     *
     * get program type
     * 
     * @param type $type int
     * @return type mixed
     */
    public static function getProgramType($type)
    {
        $out = null;
        switch ($type) {
            case 1:
                $out = 'Vendor Finance';
                break;
            case 2:
                $out = 'Channel Finance';
                break;
            default:
                $out = null;
                break;
        }
        return $out;
    }
    /**
     * Create bootstrap alert box
     *
     * @param  string $languageKey
     * @param  string $type        success | info | warning | danger
     * @return string
     */
    public static function createAlertHTML($languageKey, $type)
    {
        $allowedTypes = ['success', 'info', 'warning', 'danger'];
        $type = trim(strtolower($type));
        $type = in_array($type, $allowedTypes) ? $type : 'info';
        $html = '<div class=" alert-' . $type . ' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span>' .
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>' .
            e(trans($languageKey)) .
            '</div>';
        return $html;
    }

    /**
     * Get Application current assignee 
     * 
     * @param integer $app_id
     * @return mixed
     */
    public static function getAppCurrentAssignee($app_id)
    {
        $assigneeData = AppAssignment::getAppCurrentAssignee($app_id);
        return $assigneeData;
    }

    /**
     * Get Application current assignee all data
     * 
     * @param integer $app_id
     * @return mixed
     */
    public static function getAppCurrentAssigneedata($app_id)
    {
        $assigneeData = AppAssignment::getAppCurrentAssigneedata($app_id);
        return $assigneeData;
    }

    /**
     * Check access of application is view only or not
     * 
     * @param integer $app_id
     * @param integer $to_id optional
     * 
     * @return mixed
     */
    public static function isAccessViewOnly($app_id, $to_id = null)
    {
        try {
            $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface');        
            if (is_null($to_id)) {
                $to_id = \Auth::user()->user_id;
            }
            
            $appData = $appRepo->getAppData($app_id);
            $appStatusList = [
                config('common.mst_status_id.APP_REJECTED'),
                config('common.mst_status_id.APP_CANCEL'),
            ];
            if ($appData && in_array($appData->curr_status_id, $appStatusList)) {
                return 0;
            }            
            $roleData = self::getUserRole();
            if ((isset($roleData[0]) && $roleData[0]->is_superadmin == 1) || isset($roleData[0]) && $roleData[0]->is_allapp_access == '1') return 1;            
            
            if (isset($roleData[0]) && $roleData[0]->id == 12) {
                $where=[];
                $where['fi_addr.to_id'] = $to_id;
                $where['app.app_id'] = $app_id;
                $where['fi_addr.is_active'] = 1;
                $fiData = $appRepo->getFiAddressData($where);
                
                $where=[];
                $where['to_id'] = $to_id;
                $where['app_id'] = $app_id;
                $where['is_active'] = 1;         
                $rcuData = $appRepo->getRcuDocumentData($where);
                if (isset($fiData[0]) || isset($rcuData[0])) {
                    return 1;
                }
            }
            
            if (isset($roleData[0]) && $roleData[0]->id == 15) {
                $where=[];
                $where['app_id'] = $app_id;
                $where['co_lender_id'] = \Auth::user()->co_lender_id;
                $coLender = $appRepo->getSharedColender($where);
                if (isset($coLender[0])) {
                    return 1;
                }
            }
            
            $isWfStageCompleted = self::isWfStageCompleted('app_submitted', $app_id);
            if (!$isWfStageCompleted) {
                $isViewOnly = 1;
            } else {
                $userArr = self::getChildUsersWithParent($to_id);
                $curStage = WfAppStage::getCurrentWfStage($app_id);
                if ($curStage && $curStage->stage_code == 'approver') {                    
                    //$whereCond=[];
                    //$whereCond['to_id'] = $to_id;
                    //$whereCond['app_id'] = $app_id;
                    //$assignData = AppAssignment::getAppAssignmentData($whereCond);
                    //$isViewOnly = $assignData && isset($assignData->app_assign_id) ? 1 : 0;
                    
                      $appApprData = AppApprover::getAppApprovers($app_id);
                      $apprUsers = [];
                      if (isset($appApprData[0])) {
                          foreach($appApprData as $appr) {
                              $apprUsers[] = $appr->approver_user_id;
                          }
                      }
                      $isViewOnly = count($apprUsers) > 0 && in_array($to_id, $apprUsers) ? 1 : 0;
                      if (isset($roleData[0]) && $roleData[0]->id == config('common.user_role.REVIEWER') && request()->has('is_app_pull_back')) {
                        $isViewOnly = 1;
                      }
                      if(request()->has('uploadApprovalMailCopyViaApproverList') && request()->get('uploadApprovalMailCopyViaApproverList') === '1'){
                          $isViewOnly = 1;
                      }
                    
                } else {
                    if (isset($roleData[0]) && $roleData[0]->id == 6 && in_array(request()->route()->getName(), ['share_to_colender', 'save_share_to_colender','update_total_limit_amnt'])) {
                        $isViewOnly = 1;
                    } else if (isset($roleData[0]) && $roleData[0]->id == 11 && in_array(request()->route()->getName(), ['reject_app', 'save_app_rejection'])) {
                        $isViewOnly = 1;
                    } 
                    // get_trans_name added by Sudesh but needs to be discussed with Gaurav
                    else if (in_array(request()->route()->getName(), [  'list_lms_charges',
                                                                        'get_chrg_amount',
                                                                        'renew_application', 
                                                                        'create_enhanced_limit_app', 
                                                                        'create_reduced_limit_app',
                                                                        'get_trans_name', 
                                                                        'ajax_get_program_balance_limit',
                                                                        'save_manual_charges'
                                                                    ])) {
                        $isViewOnly = 1;
                    } else {
                        $appStatusArr = [
                            config('common.mst_status_id.APP_SANCTIONED'),
                            config('common.mst_status_id.APP_CLOSED'),
                        ];
                        if(in_array($appData->curr_status_id, $appStatusArr)) {
                            $isViewOnly = 1;
                        } else {
                            $isViewOnly = AppAssignment::isAppCurrentAssignee($app_id, $userArr, isset($roleData[0]) ? $roleData[0]->id : null);
                        }   
                    }
                }
            }
            return $isViewOnly ? 1 : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get Roles By role_type
     *      
     * @param integer $role_type
     */
    public static function getRolesByType($role_type)
    {
        $data = Role::getRolesByType($role_type);
        return $data;
    }

    /**
     * Get Child Users with Parent User
     * 
     * @param integer $parentId
     * @return array
     */
    public static function getChildUsersWithParent($parentId)
    {
        $userRepo = \App::make('App\Inv\Repositories\Contracts\UserInterface');
        $childs = $userRepo->getChildUsers($parentId);
        $childs = array_unique($childs);
        return array_merge($childs, array($parentId));
    }

    /**
     * Save Approval Authority Users against application
     * 
     * @param integer $app_id
     * @return mixed
     */
    public static function saveApprAuthorityUsers($app_id,$approver_list)
    {
        //$approvers = User::getApprAuthorityUsers();
        $approvers = Application::getDoAUsersByAppId($app_id);
       
        $data = [];
        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');

        AppApprover::updateAppApprActiveFlag($app_id); //update rows with is_active => 0
        foreach ($approvers as $approver) {
            /*******chk the approval is active or deactive behalf of checkbox check *********/
             if(in_array($approver->user_id,$approver_list))
            {
                 $is_active =1;
            }
            else
            {
                 $is_active =0;
            }
            $data[] = [
                'app_id' => $app_id,
                'approver_user_id' => $approver->user_id,
                'is_active' => $is_active,
                'created_by' => Auth::user()->user_id,
                'created_at' => $curData,
                'updated_by' => Auth::user()->user_id,
                'updated_at' => $curData,
            ];
        }
        AppApprover::insert($data);
            
        $application = Application::find($app_id);
        $reviewerSummaryData = CamReviewerSummary::where('biz_id','=',$application->business->biz_id)->where('app_id','=',$application->app_id)->first();
          foreach ($approvers as $approver) {
            if(in_array($approver->user_id,$approver_list))
            {
             
                $user = User::getfullUserDetail((int)$approver->user_id);
                $emailData['app_id'] = \Helpers::formatIdWithPrefix($application->app_id, 'APP');
                $emailData['receiver_user_name'] = $user->f_name .' '. $user->m_name .' '. $user->l_name;
                $emailData['receiver_role_name'] = '';//$user->roles[0]->name;
                $emailData['receiver_email'] = isset($user->email) ? $user->email : '';
                $emailData['cover_note'] = (isset($reviewerSummaryData->cover_note))?$reviewerSummaryData->cover_note:'';  
                $allEmailData[] = $emailData;
            }
        }
        $productsArr = $application->products->pluck('id')->toArray();
        $Array_CC = [];
        $productIdArr = [];
        if (in_array(1, $productsArr)) {
            $SCF_CC = explode(',', config('common.SEND_APPROVER_MAIL_CC_SCF'));
            $Array_CC = array_merge($Array_CC, $SCF_CC);
            $productIdArr = array_merge($productIdArr, [1]);
        } 
        if (in_array(2, $productsArr)) {
            $SCF_CC = explode(',', config('common.SEND_APPROVER_MAIL_CC_TERM'));
            $Array_CC = array_merge($Array_CC, $SCF_CC);
            $productIdArr = array_merge($productIdArr, [2]);
        } 
        if (in_array(3, $productsArr)) {
            $SCF_CC = explode(',', config('common.SEND_APPROVER_MAIL_CC_LEASE'));
            $Array_CC = array_merge($Array_CC, $SCF_CC);
            $productIdArr = array_merge($productIdArr, [3]);
        } 
        $allEmailData['cc_mails'] = implode(',', array_unique($Array_CC));
        //dd($allEmailData['cc_mails']);
        $allEmailData['cc_mails'] = array_unique($Array_CC);
        $allEmailData['product_id'] = array_unique($productIdArr);
        $allEmailData['biz_entity_name'] = $application->business->biz_entity_name;
        $helper = new Helper();
        $helper->_getReviewerSummaryData($allEmailData,$application->app_id,$application->business->biz_id);
        \Event::dispatch("APPLICATION_APPROVER_MAIL", serialize($allEmailData));
        return $approvers;
    }

    /**
     * Check Approval Authority Users against application
     * 
     * @param integer $app_id
     * @return mixed
     */
    public static function isAppApprByAuthority($app_id)
    {
        $appApprData = AppApprover::getAppApprovers($app_id);
        $totalApprover = count($appApprData);
        $apprCount = 0;
        foreach ($appApprData as $data) {
            if ($data->status == 1) {
                $apprCount++;
            }
        }
        if ($totalApprover == $apprCount) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Assign application user
     * 
     * @param array $data
     */
    public static function assignAppUser($data)
    {
        AppAssignment::updateAppAssignById((int) $data['app_id'], ['is_owner' => 0]);
        //update assign table
        $dataArr = [];
        $dataArr['from_id'] = \Auth::user()->user_id;
        $dataArr['to_id'] = $data['to_id'];
        $dataArr['role_id'] = null;
        $dataArr['assigned_user_id'] = $data['assigned_user_id'];
        $dataArr['app_id'] = $data['app_id'];
        $dataArr['assign_status'] = '0';
        $dataArr['sharing_comment'] = isset($data['sharing_comment']) ? $data['sharing_comment'] : '';
        $dataArr['is_owner'] = 1;
        AppAssignment::saveData($dataArr);
    }



    /**
     * get Doa Level
     * 
     * @param mixed $request
     * @return mixed
     */
    public static  function getDoaLevelCity($request)
    {

        $city_name =    $request->doaLevelStates->map(function ($elem) {
            return $elem->name;
        });

        return implode(',', $city_name->toArray());
    }


    /**
     * check permission 
     * 
     * @param int $permission_id
     * @param int $role_id
     * @return mixed
     */
    public static function checkPermissionAssigntoRole($permission_id, $role_id)
    {
        return PermissionRole::checkPermissionAssigntoRole($permission_id, $role_id);
    }

    /**
     * Get equipment type
     *      
     * @param integer $id
     */
    public static function getEquipmentTypeById($id)
    {
        return Equipment::getEquipmentTypeById($id);
    }


    public static function getBankAccListByCompId($id)
    {
        //        dd($id);
        $bank_acc = UserBankAccount::getAllCompanyBankAcc($id);

        return  $bank_acc;
    }

    public static function getAllCompBankAccList($id)
    {
        //        dd($id);
        $bank_acc = UserBankAccount::getAllCompBankAccList($id);

        return  $bank_acc;
    }

    /**
     * Get User detail by user_id
     *      
     * @param integer $user_id
     */
    public static function getUserName($user_id)
    {
        $user =  User::findOrFail($user_id);
        return ucwords($user->f_name . ' ' . $user->l_name);
    }

    /**
     * Update current workflow app stage
     * 
     * @param string $wf_stage_code
     * @param integer $app_id
     * @param integer $wf_status
     * @return boolean
     */
    public static function updateCurrentWfStage($wf_stage_code, $app_id, $wf_status)
    {
        $wfData = WfStage::getWfDetailById($wf_stage_code);
        if ($wfData) {
            $wf_stage_id = $wfData->wf_stage_id;
            $wf_order_no = $wfData->order_no;
            $updateData = [
                'app_wf_status' => $wf_status,
                'is_complete' => $wf_status
            ];
            $appData = Application::getAppData((int) $app_id);
            $user_id = $appData->user_id;
            if ($wf_stage_code == 'new_case') {
                $updateData['biz_app_id'] = $app_id;
                $result = WfAppStage::updateWfStageByUserId($wf_stage_id, $user_id, $updateData);
            } else {
                $result = WfAppStage::updateWfStage($wf_stage_id, $app_id, $updateData);
            }
        }
        return $wfData;
    }

    /** 
     * @Author: Rent Alpha
     * @Date: 2020-02-18 10:51:28 
     * @Desc:  comman function for get table record.
     */    
    public static function getTableVal($tableName, $whereField, $colVal)
    {
        $result = DB::table($tableName)->where($whereField, '=', $colVal)->first();
        return  $result ? $result : false;
    }
    
    /**
     * Convert Datetime Format
     * 
     * @param string $dateTime
     * @param string $fromDateFormat
     * @param string $toDateFormat
     * @return string
     */
    public static function convertDateTimeFormat($dateTime, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i:s') 
    {
        $convertedDateTime = \Carbon\Carbon::createFromFormat($fromDateFormat, $dateTime, config('app.timezone'))
                ->setTimezone(config('common.timezone'))->format($toDateFormat);
        return $convertedDateTime;
    }

    /**
     * Convert Datetime Format
     * 
     * @param string $dateTime
     * @param string $fromDateFormat
     * @param string $toDateFormat
     * @return string
     */
    public static function istToUtc($dateTime, $inputDateFormat='Y-m-d H:i:s', $outputDateFormat='Y-m-d H:i:s') 
    {
        $convertedDateTime = \Carbon\Carbon::createFromFormat($inputDateFormat, $dateTime, config('common.timezone'))
                ->setTimezone(config('app.timezone'))->format($outputDateFormat);
        return $convertedDateTime;
    }

    /**
     * Convert Datetime Format
     * 
     * @param string $dateTime
     * @param string $fromDateFormat
     * @param string $toDateFormat
     * @return string
     */
    public static function utcToIst($dateTime, $inputDateFormat='Y-m-d H:i:s', $outputDateFormat='Y-m-d H:i:s') 
    {
        $convertedDateTime = \Carbon\Carbon::createFromFormat($inputDateFormat, $dateTime, config('app.timezone'))
                ->setTimezone(config('common.timezone'))->format($outputDateFormat);
        return $convertedDateTime;
    }

    
    /**
     * Format Id with Prefix
     * 
     * @param string $idValue
     * @param string $type
     * @return string
     */
    public static function formatIdWithPrefix($idValue, $type='APP') 
    {
        $prefix = config('common.idprefix.'.$type);
        $formatedId = null;
        
        if ($type == 'APP') {            
            $formatedId = $prefix . $idValue; /* sprintf('%08d', $idValue); */
        } else if ($type == 'VA') {
            $prefix = config('common.idprefix.'.$type);
            $formatedId = $prefix . sprintf('%08d', $idValue);            
        } else if ($type == 'CUSTID') {
            $prefix = config('common.idprefix.'.$type);
            $formatedId = $prefix . sprintf('%08d', $idValue);            
        } else if ($type == 'REFUND') {
            $prefix = config('common.idprefix.'.$type);
            $formatedId = $prefix . sprintf('%08d', $idValue);            
        } else if ($type == 'LEADID') {
            $prefix = config('common.idprefix.'.$type);
            $formatedId = $prefix . sprintf('%09d', $idValue);            
        } else if ($type == 'PAYMENTID') {
            $prefix = config('common.idprefix.'.$type);
            $formatedId = $prefix . sprintf('%09d', $idValue);            
        }else if ($type == 'UCIC') {
            $prefix = config('common.idprefix.'.$type);
            $formatedId = $prefix . sprintf('%07d', $idValue);
        }
        return $formatedId;
    }    
    
    public static function isReqInLastWfStage($reqId)
    {
        $lmsRepo = \App::make('App\Inv\Repositories\Contracts\LmsInterface');
        
        $apprReqData = $lmsRepo->getApprRequestData($reqId);
        if(!$apprReqData) return true;
                
        $wf_stage_type = $apprReqData->req_type;
        
        //Get Current workflow stage
        $curWfStage = $lmsRepo->getCurrentWfStage($reqId);
        if (!$curWfStage) return true;
                
        $cur_wf_stage_code = $curWfStage ? $curWfStage->stage_code : '';
        $cur_wf_stage_id = $curWfStage ? $curWfStage->wf_stage_id : '';
        $cur_wf_order_no = $curWfStage ? $curWfStage->order_no : '';        
        
        //Get Next workflow stage
        $nextWfStage = $lmsRepo->getNextWfStage($wf_stage_type, $cur_wf_order_no);
        if (!$nextWfStage) return true;

        return false;
    }
    
    public static function getRequestStatusList($reqId)
    {
        $lmsRepo = \App::make('App\Inv\Repositories\Contracts\LmsInterface');
        
        $reqData = $lmsRepo->getApprRequestData($reqId);
        $reqType = $reqData ? $reqData->req_type : '';
        $reqStatus = $reqData ? $reqData->status : '';

        //Get Current workflow stage
        $wfStage = $lmsRepo->getCurrentWfStage($reqId);
        $wf_stage_code = $wfStage ? $wfStage->stage_code : '';
        $wf_stage_id = $wfStage ? $wfStage->wf_stage_id : '';
        $statusArr = $wfStage && !empty($wfStage->status) ? explode(',', $wfStage->status) : [];
        
        $statusList = [];
        
        /*
        if ($reqType == config('lms.REQUEST_TYPE.REFUND')) {
            
            if ($wf_stage_code == 'refund_approval') {
                if ($reqStatus == config('lms.REQUEST_STATUS.APPROVED')) {
                    $statusList[config('lms.REQUEST_STATUS.APPROVED')] = 'Refund';        
                } else {
                    $statusList[config('lms.REQUEST_STATUS.REJECTED')] = 'Reject';
                    $statusList[config('lms.REQUEST_STATUS.APPROVED')] = 'Approve';                    
                }
            }
        } 
         * 
         */
        if (count($statusArr) > 0) {            
            foreach($statusArr as $key => $status) {
                $statusList[$status] = config('lms.REQUEST_STATUS_DISP.'.$status . '.USER');
            }
        }
        return $statusList;
    }
    
    public static function isRequestOwner($reqId, $assignedUserId)
    {
        $lmsRepo = \App::make('App\Inv\Repositories\Contracts\LmsInterface');
        return $lmsRepo->isRequestOwner($reqId, $assignedUserId);    
    }
    
    /**
     * Get Approval Request Log Data
     * 
     * @param array $whereCond
     * 
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getApprRequestStatus($reqId, $assignedUserId)
    {
        $lmsRepo = \App::make('App\Inv\Repositories\Contracts\LmsInterface');
        
        //Get Current workflow stage
        //$wfStage = $lmsRepo->getCurrentWfStage($reqId);        
        //$wfStageId = $wfStage ? $wfStage->wf_stage_id : '';
        
        $whereCond=[];
        $whereCond['req_id'] = $reqId;
        $whereCond['assigned_user_id'] = $assignedUserId;
        //$whereCond['wf_stage_id'] = $wfStageId;
        $apprLogData = $lmsRepo->getApprRequestLogData($whereCond);
        $apprStatus = isset($apprLogData[0]) ? config('lms.REQUEST_STATUS_DISP.'.$apprLogData[0]->status . '.SYSTEM') : '';
        return $apprStatus;
    }
    
    public static function getRequestCurrentStage($reqId)
    {
        $lmsRepo = \App::make('App\Inv\Repositories\Contracts\LmsInterface');
        //Get Current workflow stage
        $curWfStage = $lmsRepo->getCurrentWfStage($reqId);
        if (!$curWfStage) return false;
        
        return $curWfStage;
    }
    
    public static function getReqCurrentAssignee($reqId)
    {
        $lmsRepo = \App::make('App\Inv\Repositories\Contracts\LmsInterface');    
        $assignData = $lmsRepo->getReqCurrentAssignee($reqId);
        return $assignData;
    }

    public static function getEntityNameByUserId($userId)
    {
       $entityName = ApprovalRequest::getEntityNameByUserId($userId);
       return $entityName;
    }  

    public static function hasSupplyChainOffer($appId)
    {
        $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface');    
        $offerData = $appRepo->getPrgmLimitByAppId($appId);
        return $offerData && isset($offerData->offer);
    }  
    
    
    public static function checkEodProcess()
    {
        $lmsRepo = \App::make('App\Inv\Repositories\Contracts\LmsInterface');
        $whereCond=[];
        $whereCond['status'] =  [config('lms.EOD_PROCESS_STATUS.RUNNING')];
        //$whereCond['eod_process_start_date_eq'] = \Carbon\Carbon::now()->toDateString();
        //$whereCond['eod_process_start_date_tz_eq'] = \Carbon\Carbon::now()->toDateString();
        $whereCond['sys_start_date_lte'] = \Carbon\Carbon::now()->toDateTimeString();
        $whereCond['is_active'] = 1;
        $eodProcess = $lmsRepo->getEodProcess($whereCond);
        if ($eodProcess) {            
            return false;
        } else {
            return true;
        }
    }
    

    public static function updateEodProcess($eodProcessCheckType, $status, $eod_process_id)
    {
        $eodProcessCheckTypeList = config('lms.EOD_PROCESS_CHECK_TYPE');
       
        $lmsRepo = \App::make('App\Inv\Repositories\Contracts\LmsInterface');
        $data = [];
        $data[$eodProcessCheckType] = $status;
        
        $today = \Carbon\Carbon::now();
        
        $sys_start_date_eq = $today->format('Y-m-d');
        
        $whereCond=[];
        $whereCond['eod_process_id'] = $eod_process_id;
        $eodProcess = $lmsRepo->getEodProcess($whereCond);
        if ($eodProcess) {
            $eod_process_id = $eodProcess->eod_process_id;
            $sys_start_date = $eodProcess->sys_start_date;
            $lmsRepo->saveEodProcessLog($data, $eod_process_id);
            
            $eod_status = '';
            if ($status == config('lms.EOD_FAIL_STATUS')) {
                $eod_status = $status;
            } else {
                $whereCond=[];
                $whereCond['eod_process_id'] = $eod_process_id;
                $eodLog = $lmsRepo->getEodProcessLog($whereCond);
                $statusArr=[];
                if ($eodLog) {
                    $statusArr[] = $eodLog->tally_status;
                    $statusArr[] = $eodLog->int_accrual_status;
                    $statusArr[] = $eodLog->repayment_status;
                    $statusArr[] = $eodLog->disbursal_status;
                    $statusArr[] = $eodLog->charge_post_status;
                    $statusArr[] = $eodLog->overdue_int_accrual_status;
                    $statusArr[] = $eodLog->disbursal_block_status;
                    $statusArr[] = $eodLog->is_running_trans_settled;
                    $eod_status = in_array(2, $statusArr) ? config('lms.EOD_PROCESS_STATUS.FAILED') : (in_array(0, $statusArr) ? '' : config('lms.EOD_PROCESS_STATUS.COMPLETED'));
                }
            }
            
            if ($eod_status) {
                $eodData = [];
                $eodData['status'] = $eod_status;
                $eodData['eod_process_end'] = $today->format('Y-m-d H:i:s');
                $lmsRepo->saveEodProcess($eodData, $eod_process_id);
            }
           
        }
        
    } 

    public static function getDateTimeFieldInTz($fieldName)
    {           
        $tz = '+5:30';        //'timezone' => 'Asia/Kolkata',
        $field = "CONVERT_TZ("+$fieldName+", '+00:00', '" . $tz . "')";
        return $field;
    }
    
    public static function invoiceAnchorLimitApprove($attr){

        $prgmData = Program::where('prgm_id', $attr['prgm_id'])->first();
        if (isset($prgmData->parent_prgm_id)) {
            $prgm_ids = Program::where('parent_prgm_id', $prgmData->parent_prgm_id)->pluck('prgm_id')->toArray();
        }else{
            $prgm_ids = [$attr['prgm_id']];
        }
        $is_enhance = Application::whereIn('app_type',[1,2,3])->where(['app_id' => $attr['app_id']])->whereIn('status',[2,3])->count();

        if($is_enhance == 1)
        {
            $marginApprAmt = InvoiceDisbursed::getDisbursedAmountForSupplier($attr['user_id'], $attr['prgm_offer_id'],$attr['anchor_id'],$attr['app_id']);
            $marginApprAmt = $marginApprAmt ?? 0;
            $marginApprAmt += BizInvoice::whereIn('program_id', $prgm_ids)
            ->where('prgm_offer_id',$attr['prgm_offer_id'])
            ->whereIn('status_id',[8,9,10])
            ->where(['is_adhoc' => 0,'supplier_id' => $attr['user_id'],'anchor_id' => $attr['anchor_id']])
            ->where('app_id' , '<=', $attr['app_id'])
            ->sum('invoice_margin_amount');
            // ->sum('invoice_approve_amount');

            $marginReypayAmt =  BizInvoice::whereIn('program_id', $prgm_ids)
            ->where('prgm_offer_id',$attr['prgm_offer_id'])
            ->whereIn('status_id',[8,9,10,12,13,15])
            ->where(['is_adhoc' => 0,'supplier_id' => $attr['user_id'],'anchor_id' => $attr['anchor_id']])
            ->where('app_id' , '<=', $attr['app_id'])
            ->sum('principal_repayment_amt');
            
            return $marginApprAmt - $marginReypayAmt;
        }
        else
        {            
            $marginApprAmt = InvoiceDisbursed::getDisbursedAmountForSupplierIsEnhance($attr['user_id'], $attr['prgm_offer_id'],$attr['anchor_id'], $attr['app_id']);
            $marginApprAmt = $marginApprAmt ?? 0;
            $marginApprAmt   +=  BizInvoice::whereIn('program_id', $prgm_ids)
            ->where('prgm_offer_id',$attr['prgm_offer_id'])
            ->whereIn('status_id',[8,9,10])                    
            ->where(['is_adhoc' =>0,'app_id' =>$attr['app_id'],'supplier_id' =>$attr['user_id'],'anchor_id' =>$attr['anchor_id']])
            ->sum('invoice_margin_amount');
            // ->sum('invoice_approve_amount');
                
            $marginReypayAmt =  BizInvoice::whereIn('program_id', $prgm_ids)
            ->where('prgm_offer_id',$attr['prgm_offer_id'])
            ->whereIn('status_id',[8,9,10,12,13,15])
            ->where(['is_adhoc' => 0,'app_id' => $attr['app_id'],'supplier_id' => $attr['user_id'],'anchor_id' => $attr['anchor_id']])
            ->sum('principal_repayment_amt');
            return $marginApprAmt - $marginReypayAmt;
        }
    }      
        
    public function ProgramProductLimit($limit_id)
    {
        return  AppProgramLimit::where(['status'=> 1,'app_limit_id' =>$limit_id])->sum('limit_amt');
    } 
    
    public function getAdhoc($attr)
    {
        return  AppOfferAdhocLimit::with('prgm_offer')->where(['prgm_offer_id' =>$attr['prgm_offer_id']])->orderBy('created_at', 'DESC')->get();
    } 
         
    public static function checkLimitAmount($appId, $productId, $inputLimitAmt=0, $excludeId=[])
    {
        $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface');
        
        //Validate Enchancement Limit                        
        $appData = $appRepo->getAppData($appId);        
        $result = [
            'status' => false,
            'app_type' => $appData ? $appData->app_type : 0,
            'message' => '',            
        ];
        if ($appData && in_array($appData->app_type, [2,3]) ) {
            $parentAppId = $appData->parent_app_id;
            $parentUserId = $appData->user_id;
            
            // $appLimitData = $appRepo->getAppLimitData(['app_id' => $parentAppId, 'status' => 2]);
            $appLimitData = $appRepo->getAppLimitData(['app_id' => $parentAppId, 'status' => 1]);
            $result['tot_limit_amt'] = isset($appLimitData[0]) ? $appLimitData[0]->tot_limit_amt : 0;            
            $result['parent_inv_utilized_amt'] = 0;
            
            if ($productId == 1) {
                $pTotalCunsumeLimit = 0;
                $invUtilizedAmt = 0;
                $invSettledAmt  = 0;
                $pAppPrgmLimit = $appRepo->getUtilizeLimit($parentAppId, $productId);
                foreach ($pAppPrgmLimit as $value) {
                    $pTotalCunsumeLimit += $value->utilize_limit;

                    $attr=[];
                    $attr['user_id'] = $parentUserId;
                    $attr['app_id'] = $parentAppId;
                    $attr['anchor_id'] = $value->anchor_id;
                    $attr['prgm_id'] = $value->prgm_id;              
                    $attr['prgm_offer_id'] = $value->prgm_offer_id;
                    $invUtilizedAmt += self::anchorSupplierPrgmUtilizedLimitByInvoice($attr);
                    // $invUtilizedAmt += $appRepo->getInvoiceUtilizedAmount($attr);      
                }
                $result['parent_inv_utilized_amt'] = $invUtilizedAmt;

                $totalCunsumeLimit = $inputLimitAmt > 0 ? str_replace(',', '', $inputLimitAmt) : 0;
                $appPrgmLimit = $appRepo->getUtilizeLimit($appId, 1, $checkApprLimit=false);
                foreach ($appPrgmLimit as $value) {
                    if (count($excludeId) > 0) {
                        if (isset($excludeId['app_prgm_limit_id']) && !empty($excludeId['app_prgm_limit_id']) && $excludeId['app_prgm_limit_id'] != $value->app_prgm_limit_id) {                                            
                            $totalCunsumeLimit += $value->utilize_limit;
                        } else if (isset($excludeId['prgm_offer_id']) && !empty($excludeId['prgm_offer_id']) && $excludeId['prgm_offer_id'] != $value->prgm_offer_id) {
                            $totalCunsumeLimit += $value->utilize_limit;
                        } else {
                            $totalCunsumeLimit += $value->utilize_limit;
                        }
                    } else {
                        $totalCunsumeLimit += $value->utilize_limit;
                    }                                
                }

                if ($appData->app_type == 2) {
                    $result['status'] = $totalCunsumeLimit <= $pTotalCunsumeLimit;    
                    $result['message'] = trans('backend_messages.validate_limit_enhance_amt');
                    $result['parent_consumed_limit'] = $pTotalCunsumeLimit; 
                } else if ($appData->app_type == 3) {
                    $result['status'] = $invUtilizedAmt >= $totalCunsumeLimit;    
                    $result['message'] = trans('backend_messages.validate_reduce_limit_amt');
                    $result['parent_consumed_limit'] = $pTotalCunsumeLimit; 
                }
            }
        }
        
        return $result;
     }
  /**
     * Get 'yes' or NO
     *
     * @return string
     */

     public static function getYesFlag($value , $returnValues = [ '0'=>'No', '1'=>'Yes', '2'=>'N/A' ] )
  {
       if (is_null($value) || $value === '')
        {
            return '';
        }
        else
        {
           if( array_key_exists( $value, $returnValues ) )
           {
               return $returnValues[ (int) $value ];
           }
           else
           {
               return '';
           }
        }
    } 
    
    /**
     * Get System Start Date
     * 
     * @return timestamp
     */
    public static function getSysStartDate()
    {
        /*
        $lmsRepo = \App::make('App\Inv\Repositories\Contracts\LmsInterface');
        $eodDetails = $lmsRepo->getEodProcess(['is_active'=>1]);
        if($eodDetails){
            if($eodDetails->status == config('lms.EOD_PROCESS_STATUS.RUNNING')){
                $sys_start_date = Carbon::parse($eodDetails->sys_start_date);
                //$sys_start_date = \Carbon\Carbon::now()->toDateTimeString();
            }
            elseif($eodDetails->status == config('lms.EOD_PROCESS_STATUS.WATING')){
                $sys_start_date = Carbon::parse($eodDetails->sys_start_date);
            }
            elseif($eodDetails->status == config('lms.EOD_PROCESS_STATUS.COMPLETED')){
                $sys_start_date = Carbon::parse($eodDetails->sys_end_date);
            }
            elseif($eodDetails->status == config('lms.EOD_PROCESS_STATUS.STOPPED')){
                $sys_start_date = Carbon::parse($eodDetails->sys_end_date);
            }
            elseif($eodDetails->status == config('lms.EOD_PROCESS_STATUS.FAILED')){
                $sys_start_date = Carbon::parse($eodDetails->sys_end_date);
            }
        }else{
            $sys_start_date = \Carbon\Carbon::now()->toDateTimeString();
        }
        */

        $sys_start_date = \Carbon\Carbon::now()->toDateTimeString();
        return $sys_start_date;
    }     

     /**
      * Get Server Protocol
      * 
      * @return string
      */
     public static function getServerProtocol()
     {
        if(config('app.env') == "production"){
            $protocol = 'https://';
        } else {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";         
        }
        return $protocol;
     }
     
      public static function getProductWiseDoAUsersByAppId($app_id)
     {
        return  Application::getDoAUsersByAppId($app_id);
     }

    public static function getInterestAccrualCronStatus(){
        $currentTimestamp = Carbon::now()->format('Y-m-d');
        $cronLogDetails = CronLog::where('cron_id','1')->whereDate('exec_start_at',$currentTimestamp)
        ->orderBy('cron_log_id','DESC')->first();
        if($cronLogDetails){
            return true;
        } 
        return false;
    }

    public static function getEodProcessCronStatus(){
        $currentTimestamp = Carbon::now()->format('Y-m-d');
        $cronLogDetails = CronLog::where('cron_id','2')->whereDate('exec_start_at',$currentTimestamp)
        ->orderBy('cron_log_id','DESC')->first();

        if($cronLogDetails){
            return true;
        } 
        return false;
    }

    public static function cronLogBegin(int $cronId){
        $cLog = [];
        $cLog['cron_id'] = $cronId;
        $cLog['exec_start_at'] = \Carbon\Carbon::now()->toDateTimeString();
        return CronLog::createCronLog($cLog);
    }

    public static function cronLogEnd(int $status, int $cronLogId){
        $cLog = [];
        $cLog['exec_end_at'] = \Carbon\Carbon::now()->toDateTimeString();
        $cLog['status'] = $status;
        return CronLog::updateCronLog($cLog,$cronLogId);
    }

    
    /**
     * Get Associated Anchors By User Id
     * 
     * @param int $userId
     * @return string
     */
    public static function getAnchorsByUserId($userId) 
    {
        $anchors = AnchorUser::getAnchorsByUserId($userId);        
        $anchorsInfo = '';
        if (count($anchors) == 1) {            
            foreach($anchors as $anchor) {                
                $anchorsInfo = ucwords($anchor->comp_name);                
            }
        } else if (count($anchors) > 1) {
            $anchorsInfo .= '<ul class="anchor-list" style="list-style-type: disc;padding: 10px;">';
            foreach($anchors as $anchor) {
                $anchorsInfo .= '<li>';
                $anchorsInfo .= ucwords($anchor->comp_name);
                $anchorsInfo .= '</li>';
            }
            $anchorsInfo .= '</ul>';
        } else {
            $anchorsInfo = 'NA';
        }
        
        return $anchorsInfo;
    }
    
    /**
     * Get Anchor Detail By Anchor Id
     * 
     * @param int $anchorId
     * @return string
     */
    public static function getAnchorById($anchorId) 
    {
        $anchor = Anchor::getAnchorById($anchorId);
        $anchorInfo = '';
        if ($anchor) {
            $anchorInfo = ucwords($anchor->comp_name);
        } else {
            $anchorInfo = 'NA';
        }
        
        return $anchorInfo;
    }    

    public static function uploadOrUpdateFileWithContent($active_filename_fullpath, $fileContents, $appendFlag = false) {
        $defaultPath = Storage::path('public');
        $realPath = str_replace($defaultPath, '', $active_filename_fullpath);
        if (Storage::exists($active_filename_fullpath)) {
            $fileContents = (is_array($fileContents)) ? json_encode($fileContents): $fileContents;
            $isUpdated = Storage::append($realPath, $fileContents);
        } else {
            $isSaved = Storage::put($realPath, $fileContents);
        }
        
        $mimetype = Storage::mimeType($realPath);
        $size = Storage::size($realPath);
        $inputArr['file_path'] = $realPath;
        $inputArr['file_type'] = $mimetype;
        $inputArr['file_name'] = basename($realPath);
        $inputArr['file_size'] = $size;
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = \Auth::user()->user_id ?? 0;
        $inputArr['updated_by'] = \Auth::user()->user_id ?? 0;

        if (isset($isSaved)) {
            return $inputArr;
        }
        if (isset($isUpdated) && $appendFlag) {
            $fileData = UserFile::where('file_path', $realPath)->first();

            if($fileData->count()){
                $fileData = $fileData->toArray();
            }else{
                $fileData = UserFile::create($inputArr);
            }
            return $fileData ?? false;
        }
        return $isUpdated ? $isUpdated : $isSaved;
    }
     
     public static function replaceImagePath($variable)
     {
        $currentRoute = \Request::route()->getName();
        if ($currentRoute == 'generate_cam_report') {
            $backendUri = self::getServerProtocol() . config('proin.backend_uri');             
            $ckUploadImgPath = !empty(config('common.ck_upload_img_path')) ? config('common.ck_upload_img_path') : $_SERVER["DOCUMENT_ROOT"];            
            return str_replace($backendUri, $ckUploadImgPath, $variable);
        } else {
            return $variable;
        }
     }     

     public static function checkApprPrgm($prgmId, $isOfferAcceptedOrRejected=true)
     {
        $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface');
        $offerCond=[];
        $offerCond['prgm_id'] = $prgmId;        
        $offerCond['is_active'] = 1;
        if ($isOfferAcceptedOrRejected) {
            $offerCond['status_is_not_null'] = 1;
        } else {
            $offerCond['status_is_null'] = 1;
        }
        $appPrgmOffer = $appRepo->getOfferData($offerCond);
        $res = false;
        
        if ($appPrgmOffer && $appPrgmOffer->prgm_offer_id) {            
            $res = true;
        }
        
        return $res;
     }
     
     public static function getPrgmBalLimit($programId)
     {
        $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface');  
        
        $programs = $appRepo->getParentsPrograms($programId);        
        $utilizedLimit = 0;
        foreach($programs as $prgmId) {
            $utilizedLimit += $appRepo->getPrgmBalLimit($prgmId);
        }
        return $utilizedLimit;
     }
     
     public static function getAnchorUtilizedLimit($anchorPrgmId)
     {
        $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface');
        $anchorSubLimitTotal = $appRepo->getSelectedProgramData(['parent_prgm_id' => $anchorPrgmId, 'status' => 1], ['anchor_sub_limit'])->sum('anchor_sub_limit');
        //$subPrgms = $appRepo->getSelectedProgramData(['parent_prgm_id' => $anchorPrgmId], ['prgm_id'])->pluck('prgm_id');
        //$prgmIds = $subPrgms ? $subPrgms->toArray() : [];
        //$utilizedLimit = count($prgmIds) > 0 ? $appRepo->getPrgmBalLimit($prgmIds) : 0; 
        $utilizedLimit = 0;
        $totalUtilizedAmount = $anchorSubLimitTotal + $utilizedLimit;
        return $totalUtilizedAmount;
     }
     
     public static function isProgamEditAllowed($anchorPrgmId)
     {
        $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface');
        $subPrgms = $appRepo->getSelectedProgramData(['parent_prgm_id' => $anchorPrgmId], ['parent_prgm_id','is_edit_allow', 'prgm_id']);
        $isProgamEditAllowed = 0;
        
        $prgmIds =   $subPrgms  ? $subPrgms->pluck('prgm_id')->toArray() : [];
        $appPrgmOffer = $appRepo->checkProgramOffers($prgmIds);        
                     
        if (count($subPrgms) == 0 || $appPrgmOffer == 0) {
            $isProgamEditAllowed = 1;
        } else {            
            foreach($subPrgms as $prgm) {   
                if ($prgm->is_edit_allow && !self::checkApprPrgm($prgm->prgm_id, $isOfferAcceptedOrRejected=false) )  {
                    $isProgamEditAllowed = 2;               
                    break;
                }
            }       
        }
        return $isProgamEditAllowed;
     }
     
     public static function getParentsPrograms($prgmId)
     {
        $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface');        
        $programs = $appRepo->getParentsPrograms($prgmId);
        return $programs;
     }

    public static function updateAppCurrentStatus($appId, $curStatus, $data=[])
    {
        $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface');
        $curDate = \Carbon\Carbon::now();
        $appData = $appRepo->getAppData($appId);
        
        if ($appData && $appData->curr_status_id != $curStatus) {
            $userId = $appData->user_id;
            if (isset($data['note_data']) && !empty($data['note_data'])) {
                $noteData = [
                    'app_id'     => $appId, 
                    'note_data'  => $data['note_data'],
                    'created_at' => $curDate,
                    'created_by' => \Auth::user()->user_id
                ];
                $result = $appRepo->saveAppNote($noteData)->latest()->first()->toArray();
            }
        
            $appStatusData = [
                'app_id'    => $appId,
                'user_id'   => $userId,
                'note_id'   => isset($result['note_id']) ? $result['note_id'] : null,
                'status_id' => (int) $curStatus,
                'created_at'=> $curDate,
                'created_by'=> \Auth::user()->user_id
            ];
            $appRepo->saveAppStatusLog($appStatusData);

            $arrUpdateApp=[
                'curr_status_id' => (int) $curStatus,
                'curr_status_updated_at' => $curDate
            ];

            $appStatusList = self::getAppStatusList();
            $arrActivity = [];
            $arrActivity['activity_code'] = 'app_status_changed';
            $arrActivity['activity_desc'] = 'Application status is modified from ' . (isset($appStatusList[$appData->curr_status_id]) ? $appStatusList[$appData->curr_status_id] : '' ) . ' to ' . (isset($appStatusList[$curStatus]) ? $appStatusList[$curStatus] : '' );
            $arrActivity['user_id'] = $userId;
            $arrActivity['app_id'] = $appId;
            \Event::dispatch("ADD_ACTIVITY_LOG", serialize($arrActivity));

            return $appRepo->updateAppDetails($appId, $arrUpdateApp);
        }

    }
    
    public static function isChangeAppStatusAllowed ($curStatusId) 
    {
        $appStatusList = [
            config('common.mst_status_id.APP_REJECTED'),
            config('common.mst_status_id.APP_CANCEL'),
            //config('common.mst_status_id.OFFER_LIMIT_APPROVED'),
            //config('common.mst_status_id.OFFER_LIMIT_REJECTED'),            
            //config('common.mst_status_id.OFFER_GENERATED'),
            //config('common.mst_status_id.OFFER_ACCEPTED'),
            //config('common.mst_status_id.OFFER_REJECTED'),
            //config('common.mst_status_id.SANCTION_LETTER_GENERATED'),
            config('common.mst_status_id.APP_SANCTIONED'),
            config('common.mst_status_id.APP_CLOSED'),
            config('common.mst_status_id.DISBURSED'),
            config('common.mst_status_id.NPA'),
        ];
        $isChangeAppStatusAllowed = !in_array($curStatusId, $appStatusList);
        return $isChangeAppStatusAllowed;
    }
    
    public static function getAppStatusList()
    {
        return \App\Inv\Repositories\Models\Master\Status::getStatusList($status_type=1);
    }

    /**
     * Get workflow deatail by workflow id
     * 
     * @param string $wf_stage_code
     * @param int $user_id
     * @param int $app_id
     * 
     * @return object
     */
    public static function getWfDetailById($wf_stage_code, $user_id, $app_id)
    {        
        $wfData = WfAppStage::getAppWfStage($wf_stage_code, $user_id, $app_id);
        return $wfData;
    }
    
    /**
     * Number to Word formation
     * 
     * @param type $number
     * @return type
     */
    public static function numberToWord($number) {
        $no = floor($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array('0' => '', '1' => 'one', '2' => 'two',
         '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
         '7' => 'seven', '8' => 'eight', '9' => 'nine',
         '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
         '13' => 'thirteen', '14' => 'fourteen',
         '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
         '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
         '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
         '60' => 'sixty', '70' => 'seventy',
         '80' => 'eighty', '90' => 'ninety');
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_1) {
          $divider = ($i == 2) ? 10 : 100;
          $number = floor($no % $divider);
          $no = floor($no / $divider);
          $i += ($divider == 10) ? 1 : 2;
          if ($number) {
             $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
             $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
             $str [] = ($number < 21) ? $words[$number] .
                 " " . $digits[$counter] . $plural . " " . $hundred
                 :
                 $words[floor($number / 10) * 10]
                 . " " . $words[$number % 10] . " "
                 . $digits[$counter] . $plural . " " . $hundred;
          } else $str[] = null;
       }
       $str = array_reverse($str);
       $result = implode('', $str);
       $points = ($point) ?
         "." . $words[$point / 10] . " " . 
               $words[$point = $point % 10] : '';
       return $result;
    }

    public static function uploadDirectoryFile($attributes, $pathDirectory = 'user')
    {
        $inputArr = [];
        if ($attributes['doc_file']) {
            if (!Storage::exists('/public/'.$pathDirectory)) {
                Storage::makeDirectory('/public/' . $pathDirectory, 0777, true);
            }
            // $extension = $attributes['doc_file']->getClientOriginalExtension();
            // $name   = $attributes['doc_file']->getClientOriginalName();
            // $name  =  explode('.',$name);
            // $filename =  $name[0].'.'.$extension;
            // dd($filename);
            $path = Storage::put('/public/'.$pathDirectory, $attributes['doc_file'], null);
            $inputArr['file_path'] = str_replace('public/', '', $path);
        }

        $inputArr['file_type'] = $attributes['doc_file']->getClientMimeType();
        $inputArr['file_name'] = $attributes['doc_file']->getClientOriginalName();
        $inputArr['file_size'] = $attributes['doc_file']->getSize();
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;

        return $inputArr;
    }

    public static function ckycUploadDocument($attributes, $userInfo){

        if(isset($attributes['imageInfo'])){
            $imageType = $attributes['type'];
            $extension = $attributes['imageInfo']['imageExtension'];
            $base64_str = $attributes['imageInfo']['image'];//substr($attributes['imageInfo']['image'], strpos($attributes['imageInfo']['image'], ",")+1);
        }else{
            $extension = $attributes['imageExtension'];
            $imageType = $attributes['imageType'];
            $base64_str = $attributes['image'];//substr($attributes['image'], strpos($attributes['image'], ",")+1);
        }
        $imageDetails['file_type'] = $imageType;
        if(isset($userInfo['biz_owner_id'])){
            $user_img_prefix = 'ckyc_'.$userInfo['user_id'].'_'.$userInfo['biz_owner_id'];
        }else{
            $user_img_prefix = 'ckyc_'.$userInfo['user_id'];
        }
        $img_name =  $user_img_prefix.'_'.time().rand(10,100).'.'.$extension;
        
        
        $image = base64_decode($base64_str);
        if (!Storage::exists('Development/ckycdoc/business/identityDetails/'.$userInfo['user_id'])) {
            Storage::makeDirectory('Development/ckycdoc/business/identityDetails/'.$userInfo['user_id'], 0777, true);
        }
        $path = Storage::put('Development/ckycdoc/business/identityDetails/'.$userInfo['user_id'] . '/'.$img_name, $image, null);
        $inputArr['file_path'] = 'Development/ckycdoc/business/identityDetails/'.$userInfo['user_id'] . '/'.$img_name;
        $inputArr['file_type'] = $imageType;
        $inputArr['file_name'] = $img_name;
        $inputArr['file_size'] = 0;
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;
        
        return $inputArr;
    }

    public static function ckycJsonResponseAsDocument($response,$userInfo){

        if(isset($userInfo['biz_owner_id'])){
            $user_img_prefix = 'ckyc_res_'.$userInfo['user_id'].'_'.$userInfo['biz_owner_id'];
        }else{
            $user_img_prefix = 'ckyc_res_'.$userInfo['user_id'];
        }
        $fileName =  $user_img_prefix.'_'.time().rand(10,100).'.json';
        if (!Storage::exists('Development/ckycdoc/ckyc-response/'.$userInfo['user_id'])) {
           Storage::makeDirectory('Development/ckycdoc/ckyc-response/'.$userInfo['user_id'], 0777, true);
        }

        $uploaded = Storage::put('Development/ckycdoc/ckyc-response/'. $userInfo['user_id'] .'/' . $fileName, $response);
        if($uploaded){
            $inputArr['file_path'] = 'Development/ckycdoc/ckyc-response/'. $userInfo['user_id'] .'/' . $fileName;
        }
        $inputArr['file_type'] = 'Json';
        $inputArr['file_name'] = $fileName;
        $inputArr['file_size'] = 0;
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;

        return $inputArr;
    }

    public static function getCompanyBankAccList()
    {
        $bank_acc = UserBankAccount::getCompanyBankAccList();
        return  $bank_acc;
    }    
        
    /**
     * uploading document data
     *
     * @param Exception $exception
     * @param string    $exMessage
     * @param boolean   $handler
     */
    public static function uploadAnchorLogo($attributes, $anchorId)
    {
        $inputArr = [];
        if ($attributes['doc_file']) {
            $anchorData = Anchor::getAnchorById($anchorId);
            $oldFileId = UserFile::deletes($anchorData->logo_file_id);
            
            if (!Storage::exists('/public/anchor/' . $anchorId)) {
                Storage::makeDirectory('/public/anchor/' . $anchorId, 0777, true);
            }
            $path = Storage::put('/public/anchor/' . $anchorId, $attributes['doc_file'], null);
            $inputArr['file_path'] = str_replace('public/', '', $path);
        }

        $inputArr['file_type'] = $attributes['doc_file']->getClientMimeType();
        $inputArr['file_name'] = $attributes['doc_file']->getClientOriginalName();
        $inputArr['file_size'] = $attributes['doc_file']->getSize();
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;

        return $inputArr;
    }

    /**
     * get authenticated anchor logo
     * 
     * @param Exception $exception
     * @param string    $exMessage
     * @param boolean   $handler
     */
    public static function getAuthenticatedAnchorLogo(){
        $fileArr = [];
        $user_id = \Auth::user()->user_id;
        $userArr = User::getUserDetail($user_id);
        $anchorData = Anchor::getAnchorById($userArr->anchor_id);
        // dd($anchorData);
        if($anchorData){
            $fileArr['path'] = $anchorData->file_path;
            $fileArr['align'] = $anchorData->logo_align;
        }
        
        return $fileArr;
    }

    public static function getProgram($prgm_id)
    {
        $prgmData = Program::getProgram($prgm_id);
        return $prgmData;
    }

    public static function getPerfiosBankById($id){
        $bankData = Bank::find($id);
        return $bankData['bank_name'];
    }

    // Check app status for Reactivate 
    public static function isChangeAppStatusReactivate ($curStatusId) 
    {
        $appStatusList = [
            config('common.mst_status_id.APP_REJECTED'),
            config('common.mst_status_id.APP_CLOSED'),
            config('common.mst_status_id.APP_HOLD'),
        ];
        $isChangeAppStatusAllowed = in_array($curStatusId, $appStatusList);
        return $isChangeAppStatusAllowed;
    }    
    /**
     * check colender
     *
     * 
     */
    public static function getColenderListByAppID($colenderwherCond)
    {
        return ColenderShare::getColenderListByAppID($colenderwherCond);
    }
    public static function formatCurrency($amount, $decimal = true, $prefixCurrency = true)
    {
        if(is_numeric($amount)){

            $currency = '₹';
            $amount = $decimal ? round($amount,2) : round($amount);
            $formattedAmount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $amount);
            if ($prefixCurrency) {
                $formattedAmount = $currency ."$formattedAmount";
            }
            return $formattedAmount;
        }
        return null;
    }
    
    public static function getInvoiceStatusByPrgmOfferId($prgmOfferId){
        $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface'); 
        $offerData = $appRepo->getOfferData(['prgm_offer_id' => $prgmOfferId]);
        $status = FALSE;
        if($offerData && isset($offerData->invoice) && $offerData->invoice->isNotEmpty()){
             foreach($offerData->invoice as $invoice){
                if($invoice->status_id > 9){
                    $status = FALSE;
                    break;
                }else if($invoice->status_id >= 7 && $invoice->status_id <= 9){
                    $status = TRUE;
                }
            }
        }
        return $status;
    }

    public static function getLatestLmsUserLogByUserId($userId){
        $lmsUserLog = LmsUsersLog::where('user_id',$userId)->orderBy('created_at','desc')->first();

        return $lmsUserLog->status_id ?? '';
    }


    public static function appStatus($app_id)
    {
       return $appData = Application::getAppData((int) $app_id)->status;       
    }

    /**
     * Format Currency without symbol
     * 
     * @param decimal $amount
     * @param string $locale | optional
     * @return string
     */
    public static function formatCurrencyNoSymbol($amount, $decimal = true, $prefixCurrency = true)
    {
        if(is_numeric($amount)){

            // $currency = '₹';
            $amount = $decimal ? round($amount,2) : round($amount);
            $formattedAmount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $amount);
            if ($prefixCurrency) {
                $formattedAmount = "$formattedAmount";
            }
            return $formattedAmount;
        }
        return null;
    }

    public static function getDailyReportsEmailData($emailTemplate, $compName = null)
    {
        $emailData['cc']       = $emailTemplate->cc ? explode(',', $emailTemplate->cc) : '';
        $emailData['bcc']      = $emailTemplate->bcc ? explode(',', $emailTemplate->bcc) : '';
        $emailData['name']     = $compName ? trim(str_replace('%compName', $compName, $emailTemplate->name)) : trim(str_replace('%compName', '', $emailTemplate->name));
        $emailData['body']     = $emailTemplate->message;
        $emailData['subject']  = $compName ? trim(str_replace('%compName', $compName, $emailTemplate->subject)) : trim(str_replace('%compName', '', $emailTemplate->subject));

        return $emailData;
    }

    public static function _decrypt($string, $key){
        $result = '';
        $string = base64_decode($string);
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }
        //return base64_decode($result);
        return $result;
    }

    public static function _encrypt($string, $key){
        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }
        return base64_encode($result);
    }

    public static function _is_base64_encoded($data){
        if(!is_array($data)){
            if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    public static function getPrgmBalLimitAmt($userId, $prgmId, $app_id = null, $offer_id = null)
    {
        $appStatusList = [
            config('common.mst_status_id.APP_REJECTED'),
            config('common.mst_status_id.APP_CANCEL'),
            config('common.mst_status_id.APP_CLOSED'),
            config('common.mst_status_id.OFFER_LIMIT_REJECTED')
        ];
        $results = AppProgramOffer::select('app_prgm_offer.anchor_id', 'app.user_id', 'app_prgm_offer.prgm_id', 'app_prgm_offer.app_id', 'app.parent_app_id', 'app_prgm_offer.prgm_offer_id', 'app_prgm_offer.prgm_limit_amt', 'app_prgm_offer.status', 'app.curr_status_id')
                ->join('prgm', 'app_prgm_offer.prgm_id', '=', 'prgm.prgm_id')
                ->join('app', 'app.app_id', '=', 'app_prgm_offer.app_id')
                ->join('app_product', 'app.app_id', '=', 'app_product.app_id')
                ->where('app_product.product_id', 1)
                // ->where('prgm.prgm_id', $prgmId)
                ->where('app.user_id', $userId)
                ->where('app_prgm_offer.is_active', 1)
                ->whereNotIn('app.curr_status_id', $appStatusList)
                ->where(function ($query) {
                    $query->whereIn('app_prgm_offer.status', [1])->orWhereNull('app_prgm_offer.status');
                })
                ->orderBy('user_id', 'asc')
                ->orderBy('prgm_id', 'asc')
                ->orderBy('app_id', 'asc')
                ->orderBy('prgm_offer_id', 'asc');
        if($app_id){
            $results->where('app.app_id', $app_id);
        }
        if($offer_id){
            $results->where('app_prgm_offer.prgm_offer_id',$offer_id);
        }
        $results = $results->get();

        $arr = [];
        foreach($results as $result)
        {
            if ($result->parent_app_id && isset($arr[$result->parent_app_id]) && $arr[$result->parent_app_id]['curr_status_id'] == 50 && $result->curr_status_id != 50) {
                $arr[$result->app_id] = [
                    'parent_app_id'  => $result->parent_app_id,
                    'prgm_limit_amt' => $result->prgm_limit_amt,
                    'status'         => $result->status,
                    'curr_status_id' => $result->curr_status_id
                ];
                unset($arr[$result->parent_app_id]);
            } else {
                $arr[$result->app_id] = [
                    'parent_app_id'  => $result->parent_app_id ?? 0,
                    'prgm_limit_amt' => $result->prgm_limit_amt,
                    'status'         => $result->status,
                    'curr_status_id' => $result->curr_status_id
                ];
            }
        }
        return array_sum(array_column($arr, 'prgm_limit_amt'));
    }

    public static function checkAnchorPrgmOfferDuplicate($anchorId, $prgmId, $appId)
    {
        // return AppProgramOffer::where('anchor_id', $anchorId)
        //                             ->where('prgm_id', $prgmId)
        //                             ->where('app_id', $appId)
        //                             ->where('is_active', 1)
        //                             ->whereNotIn('status', [2])
        //                             ->first();
        $prgmOfferData = AppProgramOffer::getData($appId,$prgmId,$anchorId)->where('status','!=',2)->first();
        return $prgmOfferData;
    }

    public static function getAppTypeName($appType)
    {
        $name = '';
        switch ($appType) {
            case 1:
                $name = 'Renewal';
                break;
            case 2:
                $name = 'Limit Enhancement';
                break;
            case 3:
                $name = 'Limit Reduction';
                break;
        }
        return $name;
    }

    public static function anchorSupplierUtilizedLimitByInvoice($userId, $anchorId)
    {
        $marginApprAmt = InvoiceDisbursed::getInvoiceDisbursedAmountForSupplier($userId, $anchorId);    

        $query             = BizInvoice::where(['is_adhoc' => 0,'supplier_id' => $userId,'anchor_id' => $anchorId]);
        $marginReypayQuery = clone $query;

        $marginApprAmt  += $query->whereIn('status_id', [8,9,10])->sum('invoice_margin_amount');
        $marginReypayAmt = $marginReypayQuery->whereIn('status_id', [8,9,10,12,13,15])->sum('principal_repayment_amt');
        return $marginApprAmt - $marginReypayAmt;
    }
    
    public static function anchorSupplierPrgmUtilizedLimitByInvoice($attr){

        $prgmData = Program::where('prgm_id', $attr['prgm_id'])->first();
        if (isset($prgmData->parent_prgm_id)) {
            $prgm_ids = Program::where('parent_prgm_id', $prgmData->parent_prgm_id)->pluck('prgm_id')->toArray();
        }else{
            $prgm_ids = [$attr['prgm_id']];
        }
        $is_enhance =  Application::whereIn('app_type', [1,2,3])->where('app_id', $attr['app_id'])->whereIn('status', [2,3])->count();
        $sum = 0;
        if ($is_enhance) {
            $appData = Application::getAppData((int) $attr['app_id']);
            if (in_array($appData->app_type, [1,2,3]) && $appData->parent_app_id) {
                $parentAppOffer = AppProgramOffer::getActiveProgramOfferByAppId($attr['anchor_id'], $appData->parent_app_id);
                if ($parentAppOffer && $parentAppOffer->prgm_offer_id && $parentAppOffer->prgm_id && $parentAppOffer->prgm_id == $attr['prgm_id']) {
                    $newAttr['prgm_id'] = $parentAppOffer->prgm_id;
                    $newAttr['app_id'] = $appData->parent_app_id;
                    $newAttr['user_id'] = $attr['user_id'];
                    $newAttr['anchor_id'] = $attr['anchor_id'];
                    $newAttr['prgm_offer_id'] = $parentAppOffer->prgm_offer_id;
                    $sum += self::anchorSupplierPrgmUtilizedLimitByInvoice($newAttr);
                }
                else {
                    if ($prgmData && $prgmData->copied_prgm_id) {
                        $parentAppOffer = AppProgramOffer::getActiveProgramOfferByAppId($attr['anchor_id'], $appData->parent_app_id, $prgmData->copied_prgm_id);
                        if ($parentAppOffer && $parentAppOffer->prgm_offer_id && $parentAppOffer->prgm_id ) {
                            $newAttr['prgm_id'] = $prgmData->copied_prgm_id;
                            $newAttr['app_id'] = $appData->parent_app_id;
                            $newAttr['user_id'] = $attr['user_id'];
                            $newAttr['anchor_id'] = $attr['anchor_id'];
                            $newAttr['prgm_offer_id'] = $parentAppOffer->prgm_offer_id;
                            $sum += self::anchorSupplierPrgmUtilizedLimitByInvoice($newAttr);
                        }
                    }
                }
            }
        }

        $marginApprAmt = InvoiceDisbursed::getDisbursedAmountForSupplierIsEnhance($attr['user_id'], $attr['prgm_offer_id'],$attr['anchor_id'], $attr['app_id']);
        $marginApprAmt += BizInvoice::whereIn('program_id', $prgm_ids)
                                ->where('prgm_offer_id',$attr['prgm_offer_id'])
                                ->whereIn('status_id', [8,9,10])                    
                                ->where(['is_adhoc' => 0,'app_id' => $attr['app_id'],'supplier_id' => $attr['user_id'],'anchor_id' => $attr['anchor_id']])
                                ->sum('invoice_margin_amount');
            
        $marginReypayAmt =  BizInvoice::whereIn('program_id', $prgm_ids)
                                ->where('prgm_offer_id',$attr['prgm_offer_id'])
                                ->whereIn('status_id',[8,9,10,12,13,15])
                                ->where(['is_adhoc' => 0,'app_id' => $attr['app_id'],'supplier_id' => $attr['user_id'],'anchor_id' => $attr['anchor_id']])
                                ->sum('principal_repayment_amt');
        $sum += $marginApprAmt - $marginReypayAmt;
        return $sum;
    }

    
    public static function anchorSupplierUtilizedLimitByInvoiceByPrgm($attr){

        $prgmData = Program::where('prgm_id', $attr['prgm_id'])->first();
        if (isset($prgmData->parent_prgm_id)) {
            $prgm_ids = Program::where('parent_prgm_id', $prgmData->parent_prgm_id)->pluck('prgm_id')->toArray();
        }else{
            $prgm_ids = [$attr['prgm_id']];
        }
        $is_enhance =  Application::whereIn('app_type', [1,2,3])->where('app_id', $attr['app_id'])->whereIn('status', [2,3])->count();
        $sum = 0;
        if ($is_enhance) {
            $appData = Application::getAppData((int) $attr['app_id']);
            if (in_array($appData->app_type, [1,2,3]) && $appData->parent_app_id) {
                $parentAppOffer = AppProgramOffer::getActiveProgramOfferByAppId($attr['anchor_id'], $appData->parent_app_id);
                if ($parentAppOffer && $parentAppOffer->prgm_offer_id && $parentAppOffer->prgm_id && $parentAppOffer->prgm_id == $attr['prgm_id']) {
                    $newAttr['prgm_id'] = $parentAppOffer->prgm_id;
                    $newAttr['app_id'] = $appData->parent_app_id;
                    $newAttr['user_id'] = $attr['user_id'];
                    $newAttr['anchor_id'] = $attr['anchor_id'];
                    $newAttr['prgm_offer_id'] = $parentAppOffer->prgm_offer_id;
                }
                else {
                    if ($prgmData && $prgmData->copied_prgm_id) {
                        $parentAppOffer = AppProgramOffer::getActiveProgramOfferByAppId($attr['anchor_id'], $appData->parent_app_id, $prgmData->copied_prgm_id);
                        if ($parentAppOffer && $parentAppOffer->prgm_offer_id && $parentAppOffer->prgm_id ) {
                            $newAttr['prgm_id'] = $prgmData->copied_prgm_id;
                            $newAttr['app_id'] = $appData->parent_app_id;
                            $newAttr['user_id'] = $attr['user_id'];
                            $newAttr['anchor_id'] = $attr['anchor_id'];
                            $newAttr['prgm_offer_id'] = $parentAppOffer->prgm_offer_id;
                        }
                    }
                }
            }
        }

        $marginApprAmt = InvoiceDisbursed::getDisbursedAmountForSupplierIsEnhance($attr['user_id'], $attr['prgm_offer_id'],$attr['anchor_id'], $attr['app_id']);
        $marginApprAmt += BizInvoice::whereIn('program_id', $prgm_ids)
                                ->where('prgm_offer_id',$attr['prgm_offer_id'])
                                ->whereIn('status_id', [8,9,10])                    
                                ->where(['is_adhoc' => 0,'app_id' => $attr['app_id'],'supplier_id' => $attr['user_id'],'anchor_id' => $attr['anchor_id']])
                                ->sum('invoice_margin_amount');
            
        $marginReypayAmt =  BizInvoice::whereIn('program_id', $prgm_ids)
                                ->where('prgm_offer_id',$attr['prgm_offer_id'])
                                ->whereIn('status_id',[8,9,10,12,13,15])
                                ->where(['is_adhoc' => 0,'app_id' => $attr['app_id'],'supplier_id' => $attr['user_id'],'anchor_id' => $attr['anchor_id']])
                                ->sum('principal_repayment_amt');
        $sum += $marginApprAmt - $marginReypayAmt;
        return $sum;
    }

    
    
    public static function getTotalProductLimit($appId, $productId)
    {
        $totalProductLimit = 0;
        if (isset($appId) && isset($productId)) {
            $appPrgmLimit = AppProgramLimit::getProductLimit($appId, $productId);
            foreach ($appPrgmLimit as $value) {
                $totalProductLimit += $value->product_limit;
            }
        }
        return $totalProductLimit;
    }
    
    public static function getOfferMarginAmtOfInvoiceAmt($prgmOfferId, $invoiceAmount)
    {
        $offer = AppProgramOffer::getAppPrgmOfferById($prgmOfferId);
        $sum   = $invoiceAmount;
        if ($offer && $offer->margin) {
            $sum -= ($invoiceAmount * $offer->margin) / 100;
        }
        return $sum;
    }

    public static function uploadSecurityDocFile($attributes, $appId, $app_security_doc_id=null)
    {
        $inputArr = [];
        if ($attributes['doc_file']) {
            if($app_security_doc_id){
                $appSecDocData = AppSecurityDoc::where('app_security_doc_id', $app_security_doc_id)->first();
                if($appSecDocData){
                    $oldFileId = UserFile::deletes($appSecDocData->file_id);
                }
            }
            $file_dir_path = '/app_security_doc/'.$appId;
            if (!Storage::exists($file_dir_path)) {
                Storage::makeDirectory($file_dir_path, 0777, true);
            }
            // $path = Storage::put($active_filename_fullpath, $content);
            // if (!Storage::exists('/public/app_security_doc/' . $appId)) {
            //     Storage::makeDirectory('/public/app_security_doc/' . $appId, 0777, true);
            // }
            $path = Storage::put('/app_security_doc/' . $appId, $attributes['doc_file'], null);
            $inputArr['file_path'] = $path;
        }

        $inputArr['file_type'] = $attributes['doc_file']->getClientMimeType();
        $inputArr['file_name'] = $attributes['doc_file']->getClientOriginalName();
        $inputArr['file_size'] = $attributes['doc_file']->getSize();
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;
        return $inputArr;
    }
    
    /**
     * Separated cc or bcc emails and return array
     *
     * @var array
     */
    public static function ccOrBccEmailsArray($cc_bcc_email)
    {
        $emails = [];
        $separator = ',';

        if ($cc_bcc_email) {
            $emails = array_filter(explode($separator, $cc_bcc_email));
        }
        return $emails;
    }
    
    public static function checkActiveAdhocLimit($adhocLimits)
    {
        $activeArray = [];
        if ($adhocLimits && count($adhocLimits)) {
            foreach($adhocLimits as $adhocLimit) {
                $curDate = strtotime(now()->format('Y-m-d'));
                $adhocExpirDate = strtotime($adhocLimit->end_date);
                if ($curDate > $adhocExpirDate) {
                    $activeArray[] = false;
                }elseif(in_array($adhocLimit->status, [2])) {
                    $activeArray[] = false;
                }
            }
        }        
        return count($activeArray) == count($adhocLimits) ? false : true;
    }

     public static function uploadUserApprovalFile($attributes, $userId, $appId)
    {
        $inputArr = [];
        if (isset($attributes['approval_doc_file'])) {
            if (!Storage::exists('/public/user/' . $userId . '/'. $appId)) {
                Storage::makeDirectory('/public/user/' . $userId . '/'. $appId , 0777, true);
            }
            $path = Storage::put('public/user/' . $userId . '/'. $appId . '/', $attributes['approval_doc_file'], null);
            $inputArr['file_path'] = str_replace('public/', '', $path);
        }

        $inputArr['file_type'] = $attributes['approval_doc_file']->getClientMimeType();
        $inputArr['file_name'] = $attributes['approval_doc_file']->getClientOriginalName();
        $inputArr['file_size'] = $attributes['approval_doc_file']->getSize();
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = \Auth::user()->user_id;
        $inputArr['updated_by'] = \Auth::user()->user_id;

        return $inputArr;
    }

    public static function getlastSacntionedApplication(int $userId){
       return Application::where('user_id',$userId)->whereIn('curr_status_id',[config('common.mst_status_id.APP_SANCTIONED'),config('common.mst_status_id.APP_CLOSED')])->orderBy('app_id', 'DESC')->first();
    }

    public static function getCustomerSanctionedAmt(int $userId){
        $appData = self::getlastSacntionedApplication($userId);
        $offerData = null;
        if($appData){
            $offerData = AppProgramOffer::where('app_id',$appData->app_id)
            ->where('is_approve',1)  
            ->where('status',1)  
            ->where('is_active',1)
            ->sum('prgm_limit_amt'); 
        }
        return $offerData;
    }
    
    public static function getCustomerUtilizedAmt(int $userId){
            $marginApprAmt = BizInvoice::whereIn('status_id', [8,9,10]) 
                            ->where('is_adhoc', 0)
                            ->where('supplier_id', $userId) 
                            ->sum('invoice_margin_amount');
            
            $invoiceDisbursedDetails = InvoiceDisbursedDetail::whereHas('invoice',function($q) use($userId){
                                $q->where('supplier_id',$userId);
                            })->get();

            $marginApprAmt += round((($invoiceDisbursedDetails->sum('total_outstanding_amount') - $invoiceDisbursedDetails->sum('margin_amount')) - ($invoiceDisbursedDetails->sum('total_repayment_amount') -  ( $invoiceDisbursedDetails->sum('margin_repayment') + $invoiceDisbursedDetails->sum('margin_waived_off') + $invoiceDisbursedDetails->sum('margin_tds') + $invoiceDisbursedDetails->sum('margin_write_off')))),2);
            
            return $marginApprAmt;   
             }
    /**
     * uploading adhoc limit document data
     *
     * @param Exception $exception
     * @param string    $exMessage
     * @param boolean   $handler
     */
    public static function uploadAppAdhocDocFile($docFile, $userId, $appId, $adhocLimit)
    {
        $inputArr = [];
        if(!empty($docFile)) {
            if ($docFile) {
                $targetDir = '/public/user/' . $userId . '/' . $appId . '/adhoc-limit' . '/'. $adhocLimit->app_offer_adhoc_limit_id . '/docs';
                if (!Storage::exists($targetDir)) {
                    Storage::makeDirectory($targetDir, 0777, true);
                }
                $path = Storage::put($targetDir, $docFile, null);
                $inputArr['file_path'] = str_replace('public/', '', $path);
            }
        }
        $inputArr['file_type'] = !empty($docFile) ? $docFile->getClientMimeType() : '';
        $inputArr['file_name'] = !empty($docFile) ? $docFile->getClientOriginalName() : '';
        $inputArr['file_size'] = !empty($docFile) ? $docFile->getSize() : '';
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = Auth::user()->user_id;
        $inputArr['updated_by'] = Auth::user()->user_id;
        return $inputArr;
    }
    public static function uploadAppLimitReviewApprovalFile($attributes, $userId, $appId)
    {
        $inputArr = [];
        if (isset($attributes['doc_file'])) {
            if (!Storage::exists('/public/Lms/app_limit_review/' . $userId . '/'. $appId)) {
                Storage::makeDirectory('/public/Lms/app_limit_review/' . $userId . '/'. $appId , 0777, true);
            }
            $path = Storage::put('public/app_limit_review/' . $userId . '/'. $appId . '/', $attributes['doc_file'], null);
            $inputArr['file_path'] = str_replace('public/', '', $path);
        }

        $inputArr['file_type'] = $attributes['doc_file']->getClientMimeType();
        $inputArr['file_name'] = $attributes['doc_file']->getClientOriginalName();
        $inputArr['file_size'] = $attributes['doc_file']->getSize();
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = \Auth::user()->user_id;
        $inputArr['updated_by'] = \Auth::user()->user_id;

        return $inputArr;
    }
    public static function getSecurityDoc($offerId, $appId,$docType){
        $appData = self::appDataCurrent($appId);
        $securityDataQuery = AppSecurityDoc::with(['mstSecurityDocs'])->where(['prgm_offer_id'=>$offerId,'is_active'=>1,'doc_type'=>$docType]);
        if($appData && $appData->status == 1){
            $securityDataQuery->where(['status'=>3,'is_non_editable'=>0]);
        }else{
            $securityDataQuery->where(['status'=>4,'is_non_editable'=>1]);
        }
        $securityData = $securityDataQuery->get();
        return ($securityData) ?$securityData : [];
    }
    public static function appSanctionLetterStatus($app_id)
    {
       $whereCondition = [];
	   $whereCondition['app_id'] = $app_id;
       $appSanctionLettersData = AppSanctionLetter::getOfferNewSancationLetterData($whereCondition); 
        if($appSanctionLettersData){
            return false;
        }
        return true;
    }

    public static function appCurrentStatus($app_id)
    {
       $appCurrentStatusData = Application::getAppData((int) $app_id)->curr_status_id; 
        if($appCurrentStatusData == config('common.mst_status_id.SANCTION_LETTER_GENERATED') || $appCurrentStatusData == config('common.mst_status_id.APP_SANCTIONED')){
            return true;
        }
        return false;
    }

    public static function appDataCurrent($app_id)
    {
        $application = Application::find($app_id);
        return $application;
    }

    public static function appSanctionLetterGenerated($app_id)
    {
        $supplyChainFormFile = Storage::path('public/user/'.$app_id.'_supplychain.json');
        $arrFileData = false;
        if (Storage::exists($supplyChainFormFile)) {
          $arrFileData = true; 
        }
        return $arrFileData;
    }

    public static function checkInvoiceLimitExceed($sum, $limit, $po_inv_amount)
    {
        $finalsum = $sum - $po_inv_amount;
        if ($limit  >= $finalsum) {
            $remain_amount = $limit - $finalsum;
            if ($remain_amount < $po_inv_amount) {
                return true;
            }
        } else {
            return false;
        }
        return false;
    }

    public static function getfactVoucherNumber($startDate) {
        $startDate = Helper::utcToIst($startDate,'Y-m-d H:i:s', 'Y-m-d H:i:s');
        $factvoucherData = TallyFactVoucher::getfactVoucherNumber();
        $month =  Carbon::parse($startDate)->format('M');
        $fYear = explode('-',getFinancialYear($startDate));
        $year1 =  $fYear[0];
        $year2 = $fYear[1];
        $factResult = array(
            'year1'=>$year1,
            'year2'=>$year2,
            'month'=>$month,
            'fact_srp_seq_number'=>0,
            'fact_sjv_seq_number'=>0,
        );

        if($year2 <= '2023' || $year1 <= '2022'){
            $factResult['voucher_format'] = substr($year1,-2).substr($year2,-2).'/'.mb_substr($month, 0, 1);
            if($factvoucherData){
                if(!($factvoucherData->fact_month == $month)){
                    $factvoucherData->fact_srp_seq_number = 0;
                    $factvoucherData->fact_sjv_seq_number = 0;
                }
                $factResult['fact_srp_seq_number'] = $factvoucherData->fact_srp_seq_number;
                $factResult['fact_sjv_seq_number'] = $factvoucherData->fact_sjv_seq_number;
            }
        }else{
            $factResult['voucher_format'] = substr($year1,-2).substr($year2,-2).'/';
            if($factvoucherData->fact_year2 >= '2024' && $factvoucherData->fact_year1 >= '2023'){
                $factResult['fact_srp_seq_number'] = $factvoucherData->fact_srp_seq_number;
                $factResult['fact_sjv_seq_number'] = $factvoucherData->fact_sjv_seq_number;
            }
        }
        return $factResult;
    }

    public static function validateInvoiceTypes($transIds, $specificMsg = false, $noteType = 'debit') {
        $invTypes = Transactions::whereIn('trans_id', $transIds)
                                            ->distinct()
                                            ->pluck('gst')
                                            ->toArray();
        if (count($invTypes) > 1) {
            $msg = "Please select same type of charges (Either GST Or NON GST applicable)";
            if ($specificMsg) {
                $msg .= " for generating $noteType note";
            }
            $msg .= ".";
            return ['status' => false, 'message' => $msg];
        }
        return ['status' => true];
    }

    public static function getTransfactVoucherNumber($trans_id) {
        return TransFactVoucher::getTransFactVoucher($trans_id);
    }

    public static function uploadUCICFile($attributes, $ucicId)
    {
        $inputArr = [];
        if(!empty($attributes['doc_file'])) {
            if ($attributes['doc_file']) {
                if (!Storage::exists('/public/ucic/' . $ucicId)) {
                    Storage::makeDirectory('/public/ucic/' . $ucicId, 0777, true);
                }
                $path = Storage::put('public/ucic/' . $ucicId, $attributes['doc_file'], null);
                $inputArr['file_path'] = str_replace('public/', '', $path);
            }
        }
        $inputArr['file_type'] = !empty($attributes['doc_file']) ? $attributes['doc_file']->getClientMimeType() : '';
        $inputArr['file_name'] = !empty($attributes['doc_file']) ? $attributes['doc_file']->getClientOriginalName() : '';
        $inputArr['file_size'] = !empty($attributes['doc_file']) ? $attributes['doc_file']->getClientSize() : '';
        $inputArr['file_encp_key'] =  md5('2');
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;

        return $inputArr;
    }
    
    public static function generateGroupCode($group_id)
    {
        $groupId = sprintf('%07d', $group_id);
        $groupCode = 'GRP'.$groupId;
        return $groupCode;
    }
    
    public static function isAppApprByAuthorityForGroup($app_id)
    {
        $app = Application::find($app_id);
        $isAppApproved = false;
        //After Application Approval
        if(in_array($app->curr_status_id, [ 21,22,25,50,51,48 ])){
            $isAppApproved = true;
        }

        //Before Application Approval
        elseif(in_array($app->curr_status_id, [ 49,20,56,55,23 ])){
            $isAppApproved = false;
        }

        //Exception Case
        elseif(in_array($app->curr_status_id, [ 43,44,45,46 ])){
            $isAppApproved = true;
        }

        return $isAppApproved;
    }

    public static function saveAppGroupDetailData($arrData)
    {
        if(isset($arrData['group']))
        {
            $appId = (int) $arrData['app_id'];
            $status = 0;
            $curAppProducts = [];
            foreach($arrData['group'] as $key => $groupData) {
                $biz = Business::find($groupData['borrower']);
                $inputArr = array(
                    'app_id' => $appId,
                    'biz_app_id' => $groupData['biz_app_id'],
                    'biz_id' => $groupData['borrower'],
                    'borrower' => $biz->biz_entity_name,
                    'sanction' => str_replace(',', '',$groupData['sanction']) ?? 0,
                    'outstanding' => str_replace(',', '',$groupData['outstanding']) ?? 0,
                    'proposed' => str_replace(',', '',$groupData['proposed']) ?? 0,
                    'group_id' => $arrData['group_id'],
                    'status' => $status,
                    'product_id ' => $groupData['product_type'],
                    'ucic_id' => $groupData['ucic_id'],
                );
                $inputArr['final_sanction'] = $inputArr['sanction'];
                $inputArr['final_outstanding'] = $inputArr['outstanding'];
                if($appId == $groupData['biz_app_id']){
                    $curAppProducts[] = $groupData['product_type'];
                }
                UcicUserUcic::where('app_id',$appId)->update(['group_id' => $arrData['group_id']]);
                AppGroupDetail::updateOrcreate(['app_id' => $appId, 'ucic_id' => $groupData['ucic_id'], 'product_id' => $groupData['product_type']], $inputArr);
            }
            //Delete removed product from application  
            AppGroupDetail::where(['app_id' => $appId, 'biz_app_id' => $appId])
            ->whereNotIn('product_id',array_unique($curAppProducts))
            ->delete();
            AppGroupDetail::where('app_id', $appId)->whereNotIn('group_id', [$arrData['group_id']])->delete();
        }
    }

    public static function saveGroupDetailsToUcic($userId, $appId, $groupId)
    {
        $ucicData = UcicUser::where(['user_id' => $userId])->first();
        if ($ucicData && $groupId) {
            $ucicData->update([
                'group_id' => $groupId,
                'updated_info_src' => 1
            ]);
        }
    }
        
    public static function updateIdNoToUcic($ucicData, $ownerId, $key, $data, $appId)
    {
        $ucicManagementData = $ucicData && $ucicData->management_info ? json_decode($ucicData->management_info, true) : [];
        if (!$ucicManagementData) {
            $ucicManagementData['document_upload'][$ownerId][$key]['id_no'] = $data['id_no'];
            $ucicManagementData['document_upload'][$ownerId][$key]['is_verify'] = true;
            $ucicManagementData['document_upload'][$ownerId][$key]['biz_api_id'] = $data['biz_api_id'];
            $ucicManagementData['document_upload'][$ownerId][$key]['biz_api_log_id'] = $data['biz_api_log_id'] ?? null;
        }else {
            $documents = $ucicManagementData['document_upload'] ?? [];
            $documents = isset($documents[$ownerId]) ? $documents[$ownerId] : [];
            $documentData = isset($documents[$key]) ? $documents[$key] : [];
            $documentData['id_no'] = $data['id_no'];
            $documentData['is_verify'] = true;
            $documentData['biz_api_id'] = $data['biz_api_id'];
            $documents[$key] = $documentData;
            $ucicManagementData['document_upload'][$ownerId] = $documents;
        }

        if ($ucicData) {
            $ucicData->update([
                'management_info' => json_encode($ucicManagementData)
            ]);
        }
    }
    
    public static function updateOrDeleteDocFileToUcic($ucicData, $ownerId, $key, $fileId, $filePath, $appId, $source, $update = true, $delete = false)
    {
        $ucicManagementData = json_decode($ucicData->management_info, true);
        if (!$ucicManagementData) {
            $ucicManagementData['document_upload'][$ownerId][$key]['is_ovd_enabled'] = $update && !$delete ? 1 : ((!$update && $delete) ? 0 : 0);
            $ucicManagementData['document_upload'][$ownerId][$key]['file']['id'] = $update && !$delete ? $fileId : ((!$update && $delete) ? null : null);
            $ucicManagementData['document_upload'][$ownerId][$key]['file']['path'] = $update && !$delete ? $filePath : ((!$update && $delete) ? null : null);
        }else{
            $doc_id_no = [
                'pan_card' => 2, 
                'photo' => 22, 
                'voter_id' => 30, 
                'driving_license' => 31, 
                'passport' => 32, 
                'aadhar_card' => 34, 
                'electricity_bill' => 37,
                'telephone_bill' => 38,
                'other_documents_pre_offer' => 77 
            ];
            $documents = $ucicManagementData['document_upload'] ?? [];
            $documents = $documents[$ownerId] ?? [];
            $documentData = $documents[$key] ?? [];
            $documentData['is_ovd_enabled'] = $update && !$delete ? 1 : ((!$update && $delete) ? 0 : 0);
            $documentData['file']['id'] = $update && !$delete ? $fileId : ((!$update && $delete) ? null : null);
            $documentData['file']['path'] = $update && !$delete ? $filePath : ((!$update && $delete) ? null : null);
            // $documentData['id_no'] = null;
            $documentData['doc_id'] = $doc_id_no[$key];
            $documents[$key] = $documentData;
            $ucicManagementData['document_upload'][$ownerId] = $documents;
        }
        $ucicData->update([
            'management_info' => json_encode($ucicManagementData)
        ]);
    }

    public static function makeManagementInfoDocumentArrayData($ownersData)
    {
        $ownersArrayData = [];        
        foreach($ownersData as $ownerData)
        {
            $ownersArrayData["document_upload"][$ownerData->biz_owner_id] = self::setManagementInfoDocumentData($ownerData);
        }
        return $ownersArrayData;
    }

    public static function setManagementInfoDocumentData($owner)
    {
        $docData = [];
        if ($owner->pan_card && !isset($docData['pan_card']['id_no'])) {
            $docData['pan_card']['id_no'] = $owner->pan_card;
            $docData['pan_card']['is_verify'] = false;
        }
        if ($owner->voter_id && !isset($docData['voter_id']['id_no'])) {
            $docData['voter_id']['id_no'] = $owner->voter_id;
            $docData['voter_id']['is_verify'] = false;
        }
        if ($owner->driving_license && !isset($docData['driving_license']['id_no'])) {
            $docData['driving_license']['id_no'] = $owner->driving_license;
            $docData['driving_license']['is_verify'] = false;
        }
        if ($owner->passport && !isset($docData['passport']['id_no'])) {
            $docData['passport']['id_no'] = $owner->passport;
            $docData['passport']['is_verify'] = false;
        }

        foreach ($owner->document as $document) {
            switch ($document->doc_id) {
                case 2:
                    $docData['pan_card'] = self::setDocFileData($document);
                    $docData['pan_card']['id_no'] = $docData['pan_card']['id_no'] ?? null;
                    $docData['pan_card']['doc_id'] = 2;
                break;
                case 31:
                    $docData['driving_license'] = self::setDocFileData($document);
                    $docData['driving_license']['id_no'] = $docData['driving_license']['id_no'] ?? null;
                    $docData['driving_license']['doc_id'] = 31;
                break;
                case 30:
                    $docData['voter_id'] = self::setDocFileData($document);
                    $docData['voter_id']['id_no'] = $docData['voter_id']['id_no'] ?? null;
                    $docData['voter_id']['doc_id'] = 30;
                break;
                case 32:
                    $docData['passport'] = self::setDocFileData($document);
                    $docData['passport']['id_no'] = $docData['passport']['id_no'] ?? null;
                    $docData['passport']['doc_id'] = 32;
                break;
                case 22:
                    $docData['photo'] = self::setDocFileData($document);
                    $docData['photo']['doc_id'] = 22;
                break;
                case 34:
                    $docData['aadhar_card'] = self::setDocFileData($document);
                    $docData['aadhar_card']['doc_id'] = 34;
                break;
                case 38:
                    $docData['telephone_bill'] = self::setDocFileData($document);
                    $docData['telephone_bill']['doc_id'] = 38;
                break;
                case 37:
                    $docData['electricity_bill'] = self::setDocFileData($document);
                    $docData['electricity_bill']['doc_id'] = 37;
                break;

                default:
                    break;
            }
        }

        foreach ($owner->businessApi as $bizApi) {
            $docIdNo = $bizApi->karza ? (json_decode($bizApi->karza->req_file, true)['requestId'] ?? "") : "";
            switch ($bizApi->type) {
                case 3:
                    $docData['pan_card']['biz_api_id'] = $bizApi->biz_api_id ?? null; 
                    $docData['pan_card']['id_no'] = isset($docData['pan_card']['id_no']) && $docIdNo == $docData['pan_card']['id_no'] ? $docData['pan_card']['id_no'] : $docIdNo;
                    $docData['pan_card']['is_verify'] = isset($docData['pan_card']['id_no']) && $docIdNo == $docData['pan_card']['id_no'] ? true : true;
                    break;
                case 4:
                    $docData['voter_id']['biz_api_id'] = $bizApi->biz_api_id ?? null;
                    $docData['voter_id']['id_no'] = isset($docData['voter_id']['id_no']) && $docIdNo == $docData['voter_id']['id_no'] ? $docData['voter_id']['id_no'] : $docIdNo;
                    $docData['voter_id']['is_verify'] = isset($docData['voter_id']['id_no']) && $docIdNo == $docData['voter_id']['id_no'] ? true : true;
                    break;
                case 5:
                    $docData['driving_license']['biz_api_id'] = $bizApi->biz_api_id ?? null;
                    $docData['driving_license']['id_no'] = isset($docData['driving_license']['id_no']) && $docIdNo == $docData['driving_license']['id_no'] ? $docData['driving_license']['id_no'] : $docIdNo;
                    $docData['driving_license']['is_verify'] = isset($docData['driving_license']['id_no']) && $docIdNo == $docData['driving_license']['id_no'] ? true : true;
                    break;
                case 6:
                    $docData['passport']['biz_api_id'] = $bizApi->biz_api_id ?? null;
                    $docData['passport']['id_no'] = isset($docData['passport']['id_no']) && $docIdNo == $docData['passport']['id_no'] ? $docData['passport']['id_no'] : $docIdNo;
                    $docData['passport']['is_verify'] = isset($docData['passport']['id_no']) && $docIdNo == $docData['passport']['id_no'] ? true : true;
                    break;    
                default:
                    break;
            }
        }
        
        return $docData;
    }

    public static function setDocFileData($document)
    {
        $docFileData["file"]['id'] = $document->userFile->file_id && $document->is_ovd_enabled == 1 ? $document->userFile->file_id : null;
        $docFileData["file"]['path'] = $document->userFile->file_path && $document->is_ovd_enabled == 1 ? Storage::url($document->userFile->file_path) : null;
        $docFileData["is_ovd_enabled"] = $document->is_ovd_enabled ?? 0;
        return $docFileData;
    }

    public static function approvalStatusOfAppForGroupExpoInArray()
    {
        $approvedStatus = [
                            config('common.mst_status_id.OFFER_LIMIT_APPROVED'),
                            config('common.mst_status_id.OFFER_ACCEPTED'),
                            config('common.mst_status_id.SANCTION_LETTER_GENERATED'),
                            config('common.mst_status_id.APP_SANCTIONED')
                        ];
        return $approvedStatus;
    }

    public static function getGroupBorrowers($groupId, $appId, $isAppApprovedBy = true)
    {
        $appData = Application::getAppData($appId);

        if($isAppApprovedBy){
            $results = (array) DB::select('SELECT app_group_detail_id, borrower, product_id, biz_id, biz_app_id, sanction, outstanding, proposed, ucic_id FROM rta_app_group_detail where app_id = ?',[$appId]);
        }else {
            $results = (array) DB::select('call get_group_borrower_details(?)',array($groupId));
        }

        $resultData = self::modifyResultData($results, $appId, $groupId, $isAppApprovedBy);
        
        $totalExposureAmt = $resultData['totalExposureAmt'];
        $groupSaveStatus = $resultData['groupSaveStatus'];

        unset($resultData['totalExposureAmt']);
        unset($resultData['groupSaveStatus']);
        
        return ['results' => $results, 'totalExposureAmt' => $totalExposureAmt, 'groupSaveStatus' => $groupSaveStatus];
    }

    public static function modifyResultData(&$resultData, $appId, $groupId, $isAppApprovedBy)
    {
        $groupSaveStatus = true;
        $appData = Application::getAppData($appId);
        $ucicUserId = $appData->ucicUserUcic->ucic_id ?? null;
        $appGroupData = AppGroupDetail::where('app_id', $appId)->get();
        $productIds = $appData->appProducts()->pluck('product_id')->toArray() ?? [];
        
        $currentAppGroup = [];
        if(!$isAppApprovedBy){      
            // Pre-filled Data 
            if(count($resultData) > 0){
                foreach ($resultData as $key => $data) {
                    $resultData[$key]->proposed = 0;
                    $resultData[$key]->app_id = $appId;
                    $resultData[$key]->user_id = $data->user_id ?? $appData->user_id ?? NULL;
                    $resultData[$key]->ucic_id = $data->ucic_id ?? $ucicUserId;
                    $resultData[$key]->editable = false;
                    /**
                     * 1 => Supply Chain
                     * 2 => Term Loan
                     * 3 => Leasing
                     */
                    if(in_array($data->product_id, [2,3])){
                        $resultData[$key]->outstanding = 0;
                        // Get User filled data for current application
                        $apgDataOutstanding = $appGroupData->where('product_id',$data->product_id)->where('ucic_id',$data->ucic_id);
                        
                        if($apgDataOutstanding->count() > 0){
                            $resultData[$key]->outstanding = $apgDataOutstanding->sum('outstanding');;
                        }else{
                            // Get auto filled data baised on latest application data
                            $prefillOutstanding = AppGroupDetail::where('product_id',$data->product_id)->where('ucic_id',$data->ucic_id)->where('is_latest',1)->sum('outstanding');
                            $resultData[$key]->outstanding = $prefillOutstanding;
                        }
                    }

                    if($data->ucic_id == $ucicUserId && in_array($data->product_id,$productIds)){
                        $resultData[$key]->editable = true;
                        $currentAppGroup[$data->product_id]  = $resultData[$key];
                        unset($resultData[$key]);
                        continue;
                    }
                }
            }

            // accending sort by borrower
            if(count($resultData) > 0){
                usort($resultData, function ($data1, $data2) {
                    return $data1->borrower <=> $data2->borrower;
                });
            }else{
                $resultData = [];
            }

            
            if(count($productIds)) {
                rsort($productIds);
                foreach($productIds as $key => $productId) {
                    $cagd = $appGroupData->where('product_id',$productId)->where('ucic_id',$ucicUserId)->first();
                    if(isset($currentAppGroup[$productId])){
                        $currentAppGroup[$productId]->proposed =  $cagd->proposed ?? null;
                        $object = $currentAppGroup[$productId];
                    }else{
                        $newArray = [
                            "borrower" => $cagd->borrower ?? $appData->business->biz_entity_name,
                            "product_id" => $productId,
                            "biz_app_id" => $appId,
                            "biz_id" => $cagd->biz_id ?? $appData->biz_id,
                            "sanction" => $cagd->sanction ?? null,
                            "outstanding" => $cagd->outstanding ?? null,
                            "proposed" => $cagd->proposed ?? null,
                            "app_id" => $appId,
                            "group_id" => $groupId,
                            "user_id" => $appData->user_id,
                            "ucic_id" => $ucicUserId,
                            "editable" => true
                        ];
                        $object = (object) $newArray;
                    }
                    array_unshift($resultData, $object);
                }
            }
        }

        $totalExposureAmt = 0;
        foreach($resultData as $key => $result)
        {
            if($isAppApprovedBy){ 
                $resultData[$key]->editable = false;
            }
            switch ($result->product_id) {
                case 1: // supply chain
                    $totalExposureAmt += ($result->sanction ?? 0) + ($result->proposed ?? 0);
                    break;
                case 2: // term loan
                    $totalExposureAmt += ($result->outstanding ?? 0) + ($result->proposed ?? 0);
                    break;
                case 3: // leasing
                    $totalExposureAmt += ($result->outstanding ?? 0) + ($result->proposed ?? 0);
                    break;
            }
        }

        return ['groupSaveStatus' => $groupSaveStatus, 'totalExposureAmt' => $totalExposureAmt];
    }

    public static function approveAppGroupDetails($groupId, $appId)
    {
        $productIds = AppProgramLimit::whereHas('offer',function($query){
            $query->where('is_approve',1)->where('is_active','1');
        })->where('app_id',$appId)->pluck('product_id')->toArray();

        AppGroupDetail::where('group_id',$groupId)->where('app_id',$appId)->whereColumn('app_id','biz_app_id')->whereNotIn('product_id',$productIds)->delete();
        AppGroupDetail::where('group_id',$groupId)->where('app_id',$appId)->update(['is_latest' => 1,'status' => 1, 'freezed_at' => Carbon::now()->format('Y-m-d H:i:s')]);
        AppGroupDetail::where('group_id',$groupId)->where('app_id','<>',$appId)->update(['is_latest' => 0]);
    }

    public static function changeGroupLatestApp($appId,$groupId = NULL){
        try {
            if(is_null($groupId)){
                $groupId = AppGroupDetail::where('app_id', $appId)->limit(1)->value('group_id'); 
            }
            $previousAppId = AppGroupDetail::where('group_id',$groupId)->where('app_id','<>',$appId)->whereHas('app',function($query){
                $query->whereIn('curr_status_id',[21,22,25,48,50,51]);
            })->orderBy('freezed_at','desc')->limit(1)->value('app_id');
            if($previousAppId){
                AppGroupDetail::where('app_id', $previousAppId)->update(['is_latest'=>1]);
                AppGroupDetail::where('app_id', $appId)->update(['is_latest'=>0]);
            }
            return 1;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function unApproveAppGroupDetails($appId)
    {
        self::changeGroupLatestApp($appId);
        AppGroupDetail::where('app_id', $appId)->delete();
    }
    
    public static function getGroupAppList($groupId)
    {
        $groupUcicData = (array) \DB::select('call get_group_borrower_details(?)',array($groupId));
        foreach ($groupUcicData as $key => $data) {
            /**
             * 1 => Supply Chain
             * 2 => Term Loan
             * 3 => Leasing
             */
            if(in_array($data->product_id, [2,3])){
                $groupUcicData[$key]->outstanding = 0;
                $apgDataOutstanding = AppGroupDetail::where('product_id',$data->product_id)->where('ucic_id',$data->ucic_id)->where('is_latest',1)->sum('outstanding');
                // Get auto filled data baised on latest application data
                $groupUcicData[$key]->outstanding = $apgDataOutstanding;
            }
        }
        
        return $groupUcicData;
    }    

    public static function getuciciCode($appidId)
    {
        $UcicCodeData = \DB::table('user_ucic')
                    ->select('user_ucic.ucic_code','user_ucic.user_ucic_id','user_ucic.app_id','user_ucic.business_info')
                    ->from('user_ucic')
                    ->join('user_ucic_user', 'user_ucic_user.ucic_id', '=', 'user_ucic.user_ucic_id')
                    ->where('user_ucic_user.app_id', $appidId)
                    ->first();
        return $UcicCodeData;
    }

    public static function getucicCode($MaxUcicnumber)
    {
        $export = explode('UCC',$MaxUcicnumber);
        return $export[1]+1;
    }

    public static function convertToMillions($amount) {
        if(is_numeric($amount)) {
            $result = $amount / 1000000;
            $result = (floor($result * 100) / 100);    
        }else{
            $result = 0;
        }
        return sprintf("%.2f", $result);
    }

    public static function getUcicApp($ucicCode)
    {
        $ucicData = UcicUser::getUcicApp($ucicCode);
        return $ucicData;
    }

    public static function getuciciCodeByPan($panNo)
    {
        $UcicCodeData = \DB::table('user_ucic')
                    ->select('user_ucic.ucic_code','user_ucic.user_ucic_id','user_ucic.app_id')
                    ->from('user_ucic')
                    ->where('user_ucic.pan_no', $panNo)
                    ->first();
        return $UcicCodeData;
    }
   
    public static function getAnchorDetails($userId){
        $anchId = Anchor::where('sales_user_id',$userId)->get('anchor_id');
        return $anchId;
    }
        
    private function _getReviewerSummaryData(&$allEmailData, $appId, $bizId) {
        $mstRepo = \App::make('App\Inv\Repositories\Contracts\MasterInterface');
        $preCondArr = [];
        $postCondArr = [];
        $offerPTPQ = '';
        $businessDetails = Business::find($bizId);
        $preCondArr = $postCondArr = array();
        $limitOfferData = AppProgramLimit::getLimitWithOffer($appId, $bizId, config('common.PRODUCT.LEASE_LOAN'));
        $reviewerSummaryData = CamReviewerSummary::where('biz_id','=',$bizId)->where('app_id','=',$appId)->first();        
        if(isset($limitOfferData->prgm_offer_id) && $limitOfferData->prgm_offer_id) {
            $offerPTPQ = OfferPTPQ::getOfferPTPQR($limitOfferData->prgm_offer_id);
        }
        $preCondArr=[];
        $postCondArr=[];
        if(isset($reviewerSummaryData['cam_reviewer_summary_id'])) {
            $dataPrePostCond = AppSecurityDoc::with(['mstSecurityDocs'])->where('cam_reviewer_summary_id', $reviewerSummaryData['cam_reviewer_summary_id'])->where('app_id', $appId)->where('is_active', 1)->whereIn('status', [1,2])->whereIn('is_non_editable', [0,1])->get();
            $dataPrePostCond = $dataPrePostCond ? $dataPrePostCond->toArray() : [];
            if(!empty($dataPrePostCond)) {
              $preCondArr = array_filter($dataPrePostCond, array($this, "filterPreCondSecurity"));
              $postCondArr = array_filter($dataPrePostCond, array($this, "filterPostCondSecurity"));
            }
        } 
    
        $positiveRiskCmntArr=[];
        $negativeRiskCmntArr=[];
        if(isset($reviewerSummaryData['cam_reviewer_summary_id'])) {
            $dataRiskComments = CamReviewSummRiskCmnt::where('cam_reviewer_summary_id', $reviewerSummaryData['cam_reviewer_summary_id'])
                            ->where('is_active', 1)->get();
            $dataRiskComments = $dataRiskComments ? $dataRiskComments->toArray() : [];
            if(!empty($dataRiskComments)) {
              $positiveRiskCmntArr = array_filter($dataRiskComments, array($this, "filterRiskCommentPositive"));
              $negativeRiskCmntArr = array_filter($dataRiskComments, array($this, "filterRiskCommentNegative"));
            }
        }
        //Get PreOffer Docs
        $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface');  
        $appProductIds = [];
        $appProducts = $appRepo->getApplicationProduct($appId);
        foreach($appProducts->products as $product){
            array_push($appProductIds, $product->pivot->product_id);
        }        
        $preOfferDocs=[];        
        $prgmDocs = $appRepo->getRequiredDocs(['doc_type_id' => 4], $appProductIds);
        foreach ($prgmDocs as $key => $value) {
            $preOfferDocs[] = $value->doc_id;
        }
        //config('common.review_summ_mail_docs_id') + 
        $fileArray = AppDocumentFile::getReviewerSummaryPreDocs($appId, $preOfferDocs);
        $leaseOfferData = $facilityTypeList = array();
        $leaseOfferData = AppProgramOffer::getAllOffers($appId, '3');
        $facilityTypeList= $mstRepo->getFacilityTypeList()->toarray();
        $arrStaticData = array();
        $arrStaticData['rentalFrequency'] = array('1'=>'Yearly','2'=>'Bi-Yearly','3'=>'Quarterly','4'=>'Monthly');
        $arrStaticData['rentalFrequencyForPTPQ'] = array('1'=>'Year','2'=>'Bi-Yearly','3'=>'Quarter','4'=>'Months');
        $arrStaticData['securityDepositType'] = array('1'=>'INR','2'=>'%');
        $arrStaticData['securityDepositOf'] = array('1'=>'Loan Amount','2'=>'Asset Value','3'=>'Asset Base Value','4'=>'Sanction');
        $arrStaticData['rentalFrequencyType'] = array('1'=>'Advance','2'=>'Arrears');  
        $dispAppId = Helper::formatIdWithPrefix($appId, 'APP');
        $supplyOfferData = $appRepo->getAllOffers($appId, 1);//for supply chain 
        $fee = [];
        foreach($supplyOfferData as $key=>$val){
            $offerCharges = $val->offerCharges;
            $chrg_id0 = isset($offerCharges[0]) ? $offerCharges[0]->charge_id : '';
            $fee[$chrg_id0]['chrg_type'] = isset($offerCharges[0]) ? $offerCharges[0]->chrg_type : '';
            $fee[$chrg_id0]['chrg_name'] = isset($offerCharges[0]) ? $offerCharges[0]->chargeName->chrg_name : '';
            $fee[$chrg_id0]['chrg_value'] = isset($offerCharges[0]) ? $offerCharges[0]->chrg_value : '';
            $chrg_id1 = isset($offerCharges[1]) ? $offerCharges[1]->charge_id : '';
            $fee[$chrg_id1]['chrg_type'] = isset($offerCharges[1]) ? $offerCharges[1]->chrg_type : '';
            $fee[$chrg_id1]['chrg_name'] = isset($offerCharges[1]) ? $offerCharges[1]->chargeName->chrg_name : '';
            $fee[$chrg_id1]['chrg_value'] = isset($offerCharges[1]) ? $offerCharges[1]->chrg_value : '';
        }
    
        $is_shown = $appRepo->getOfferStatus([['app_id', $appId], ['is_approve', 1], ['status', 1],['is_active', 1]]);
        $borrowerLimitData['single_limit'] = config('common.DEFAULT_BORROWER_LIMIT.Single_limit');
        $borrowerLimitData['multiple_limit'] = config('common.DEFAULT_BORROWER_LIMIT.multiple_limit');
        
        if($is_shown){
        $Limitdata =  $appRepo->getAppBorrowerLimit($appId);
        if($Limitdata){
            $borrowerLimitData['single_limit'] = $Limitdata['single_limit'];
            $borrowerLimitData['multiple_limit'] = $Limitdata['multiple_limit'];
        }
        }else{
            $Limitdata = $mstRepo->getCurrentBorrowerLimitData();
            if($Limitdata){
            $borrowerLimitData['single_limit'] = $Limitdata['single_limit'];
            $borrowerLimitData['multiple_limit'] = $Limitdata['multiple_limit'];
            }
        }
        $email_subject = 'New Application is waiting for your approval ' . $businessDetails->biz_entity_name;
        $fileAttachments = [];
        if($fileArray) {
            foreach($fileArray as $key => $val) {
                if(Storage::exists('public/'.$val['file_path'])) {
                    $fileAttachments[] = [
                        'file_path' => Storage::url('public/'.$val['file_path']),
                        'file_name' => $val['file_name'],
                        'isBinaryData' => false,
                    ];
                }
            }
        }
        //Cam report files
        $camFile = UserAppDoc::getLatestDoc($appId, config('common.PRODUCT.LEASE_LOAN'), '2');
        if($camFile) {
            if(Storage::exists('public/'.$camFile['file_path'])) {
                $fileAttachments[] = [
                    'file_path' => Storage::url('public/'.$camFile['file_path']),
                    'file_name' => $camFile['file_name'],
                    'isBinaryData' => false,
                ];
            }
        }
        // Add elements one by one
        $allEmailData['limitOfferData'] = $limitOfferData;
        $allEmailData['reviewerSummaryData'] = $reviewerSummaryData;
        $allEmailData['offerPTPQ'] = $offerPTPQ;
        $allEmailData['preCondArr'] = $preCondArr;
        $allEmailData['postCondArr'] = $postCondArr;
        $allEmailData['leaseOfferData'] = $leaseOfferData;
        $allEmailData['arrStaticData'] = $arrStaticData;
        $allEmailData['facilityTypeList'] = $facilityTypeList;
        $allEmailData['appId'] = $appId;
        $allEmailData['url'] = 'https://'. config('proin.backend_uri');
        $allEmailData['dispAppId'] = $dispAppId;
        $allEmailData['supplyOfferData'] = $supplyOfferData;
        $allEmailData['positiveRiskCmntArr'] = $positiveRiskCmntArr;
        $allEmailData['negativeRiskCmntArr'] = $negativeRiskCmntArr;
        $allEmailData['fee'] = $fee;
        $allEmailData['borrowerLimitData'] = $borrowerLimitData;
        $allEmailData['email_subject'] = $email_subject;
        $allEmailData['fileAttachments'] = $fileAttachments;            
    }
}