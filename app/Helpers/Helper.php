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
use Illuminate\Http\File;
use App\Inv\Repositories\Models\Lms\ApprovalRequest;

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
    public static function updateWfStage($wf_stage_code, $app_id, $wf_status = 0, $assign_role = false, $addl_data = [])
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
                        if ($userData && !empty($userData->anchor_id)) {
                            $toUserId = User::getLeadSalesManager($user_id);
                        } else {
                            $toUserId = LeadAssign::getAssignedSalesManager($user_id);
                        }
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

                    AppAssignment::saveData($dataArr);

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
        if ($attributes['doc_file']) {
            if (!Storage::exists('/public/user/' . $userId . '/' . $appId)) {
                Storage::makeDirectory('/public/user/' . $userId . '/' . $appId, 0777, true);
            }
            $path = Storage::disk('public')->put('/user/' . $userId . '/' . $appId, $attributes['doc_file'], null);
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
    public static function uploadAnchorFile($attributes, $anchorId)
    {
        $inputArr = [];
        if ($attributes['doc_file']) {
            if (!Storage::exists('/public/anchor/' . $anchorId)) {
                Storage::makeDirectory('/public/anchor/' . $anchorId, 0777, true);
            }
            $path = Storage::disk('public')->put('/anchor/' . $anchorId, $attributes['doc_file'], null);
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
            $path = "/cam/" .$appId."/".config('common.PRODUCT.LEASE_LOAN')."/".$fileName;            
            $tempPath = Storage::disk('public')->put($path, $pdfContent);
            $dbpath = "cam/" . $appId . "/" . config('common.PRODUCT.LEASE_LOAN') . "/" . $fileName;
            $inputArr['file_path'] = $dbpath;
        }

        $inputArr['file_type'] = Storage::disk('public')->mimeType($path);
        $inputArr['file_name'] = $fileName;
        $inputArr['file_size'] = Storage::disk('public')->size($path);
        $inputArr['file_encp_key'] =  md5(time());
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
    public static function uploadAwsBucket($attributes, $appId)
    {
        $userId = Auth::user()->user_id;
        $inputArr = [];

        if ($attributes['doc_file']) {
            if (!Storage::disk('s3')->exists('/Development/user/' . $userId . '/' . $appId)) {
                Storage::disk('s3')->makeDirectory('/Development/user/' . $userId . '/' . $appId, 0775, true);
            }
            $path = Storage::disk('s3')->put('/Development/user/' . $userId . '/' . $appId, $attributes['doc_file'], null);
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
        for ($i = 0; $i < $count; $i++) {
            if ($attributes['doc_file'][$i]) {
                if (!Storage::exists('/public/user/' . $userId . '/' . $attributes['appId'])) {
                    Storage::makeDirectory('/public/user/' . $userId . '/' . $attributes['appId'], 0775, true);
                }
                $path = Storage::disk('public')->put('/user/' . $userId . '/' . $attributes['appId'], $attributes['doc_file'][$i], null);
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
    public static function getCurrentWfStagebyRole($roleId)
    {
        return WfStage::getCurrentWfStagebyRole($roleId);
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
            if (is_null($to_id)) {
                $to_id = \Auth::user()->user_id;
            }
            $roleData = self::getUserRole();
            if (isset($roleData[0]) && $roleData[0]->is_superadmin == 1) return 1;
            $isWfStageCompleted = self::isWfStageCompleted('app_submitted', $app_id);
            if (!$isWfStageCompleted) {
                $isViewOnly = 1;
            } else {
                $userArr = self::getChildUsersWithParent($to_id);
                $isViewOnly = AppAssignment::isAppCurrentAssignee($app_id, $userArr);
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
    public static function saveApprAuthorityUsers($app_id = null)
    {
        //$approvers = User::getApprAuthorityUsers();
        $approvers = Application::getDoAUsersByAppId($app_id);
        $data = [];
        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');

        AppApprover::updateAppApprActiveFlag($app_id); //update rows with is_active => 0
        foreach ($approvers as $approver) {
            $data[] = [
                'app_id' => $app_id,
                'approver_user_id' => $approver->user_id,
                'created_by' => Auth::user()->user_id,
                'created_at' => $curData,
                'updated_by' => Auth::user()->user_id,
                'updated_at' => $curData,
            ];
        }
        AppApprover::insert($data);

        $application = Application::find($app_id);
        $reviewerSummaryData = CamReviewerSummary::where('biz_id','=',$application->business->biz_id)->where('app_id','=',$application->app_id)->first();
        
        $allEmailData=[];
        foreach ($approvers as $approver) {

            $user = User::getfullUserDetail((int)$approver->user_id);
            $emailData['app_id'] = \Helpers::formatIdWithPrefix($application->app_id, 'APP');
            $emailData['receiver_user_name'] = $user->f_name .' '. $user->m_name .' '. $user->l_name;
            $emailData['receiver_role_name'] = '';//$user->roles[0]->name;
            $emailData['receiver_email'] = $user->email;
            $emailData['cover_note'] = (isset($reviewerSummaryData->cover_note))?$reviewerSummaryData->cover_note:'';  
            $allEmailData[] = $emailData;
        }
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
            $formatedId = $prefix . sprintf('%08d', $idValue);
        } else if ($type == 'VA') {
            $prefix = config('common.idprefix.'.$type);
            $formatedId = $prefix . sprintf('%08d', $idValue);            
        } else if ($type == 'CUSTID') {
            $prefix = config('common.idprefix.'.$type);
            $formatedId = $prefix . sprintf('%08d', $idValue);            
        } else if ($type == 'REFUND') {
            $prefix = config('common.idprefix.'.$type);
            $formatedId = $prefix . sprintf('%08d', $idValue);            
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
                    $statusList[config('lms.REQUEST_STATUS.PROCESSED')] = 'Refund';        
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
}
