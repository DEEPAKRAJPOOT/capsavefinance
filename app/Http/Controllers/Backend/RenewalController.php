<?php

namespace App\Http\Controllers\Backend;

use Auth;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

class RenewalController extends Controller {

    use ApplicationTrait;
    protected $appRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo)
    {        
        $this->middleware('checkBackendLeadAccess');
        $this->appRepo  = $app_repo;
        $this->userRepo = $user_repo;
    }

    public function copyAppConfirmbox(Request $request)
    {
        $userId = $request->get('user_id');
        $appId = $request->get('app_id');
        $bizId = $request->get('biz_id');
        $appType = $request->has('app_type') ? $request->get('app_type') : null;

        $where=[];
        $where['user_id'] = $userId;
        $where['status'] = [0,1];
        $appData = $this->appRepo->getApplicationsData($where);

        $userData = $this->userRepo->getfullUserDetail($userId);           
        $isAnchorLead = $userData && !empty($userData->anchor_id);

        $flag = 0; 
        if (isset($appData[0])) {
        //    Session::flash('message', trans('error_messages.active_app_check'));
        //    return redirect()->back();
            $flag = 1; 
        }
        if ($appType == 1) {
            $save_route = 'renew_application';
            $redirect_url = route('copy_app_confirmbox', ['user_id' => $userId,'app_id' => $appId, 'biz_id' => $bizId]);
        } else if ($appType == 2) {
            $save_route = 'create_enhanced_limit_app';
            $redirect_url = route('enhance_limit_confirmbox', ['user_id' => $userId,'app_id' => $appId, 'biz_id' => $bizId]);
        } else if ($appType == 3) {
            $save_route = 'create_reduced_limit_app';
            $redirect_url = route('reduce_limit_confirmBox', ['user_id' => $userId,'app_id' => $appId, 'biz_id' => $bizId]);
        }
        return view('backend.app.copy_app_confirmbox')
                        ->with('userId', $userId)
                        ->with('appId', $appId)           
                        ->with('bizId', $bizId)
                        ->with('appType', $appType)
                        ->with('flag', $flag)
                        ->with('save_route', $save_route)
                        ->with('redirect_url', $redirect_url);
    }    

    /**
     * Copy application
     * 
     * @param Request $request
     * @return type mixed
     */
    public function renewApplication(Request $request)
    {
        try {

            $userId = $request->get('user_id');   
            $appId = $request->get('app_id'); 
            $bizId = $request->get('biz_id'); 
            $appType = $request->get('app_type'); 
            $flag = $request->get('flag'); 
            
            if ($flag == '1') {
                Session::flash('error_code', 'active_app_found');
                return redirect()->back();            
            }
            
            /*
            $where=[];
            $where['user_id'] = $userId;
            $appData = $this->appRepo->getAppDataByOrder($where , $orderBy = 'DESC');
            if (!$appData) {
                Session::flash('message', 'No application found for renewal');
                return redirect()->back(); 
            }
            
            $appId = $appData->app_id;
            $bizId = $appData->biz_id;          
                 
            //$userId = 568;
            //$userId = 510;
                                 
            //$appId = 435;
            //$bizId = 439;  

            //$appId = 391;
            //$bizId = 392;  
            
            */
            
            $result = $this->copyApplication($userId, $appId, $bizId, $appType);            
            if (!isset($result['new_app_id'])) {
                Session::flash('error_code', 'app_data_error');
                return redirect()->back();            
            }
            
            $newAppId = $result['new_app_id'];
            $newBizId = $result['new_biz_id'];            
            $arrActivity = [];
            if ($appType == 1) {
                $arrActivity['activity_code'] = 'application_renewal';
                $arrActivity['activity_desc'] = 'Application is renewed successfully. New App ID ' . $newAppId;
                $this->appRepo->updateAppDetails($appId, ['renewal_status' => 2]); //Renewed  
                $targetModel = 'confirmCopyApp';
            } else if ($appType == 2){
                $arrActivity['activity_code'] = 'user_limit_enhancement';
                $arrActivity['activity_desc'] = 'Application is copied from for limit enhancement successfully. New App ID '. $newAppId;
                $targetModel = 'confirmEnhanceLimit';    
            } else if ($appType == 3){
                $arrActivity['activity_code'] = 'reduce_limit';
                $arrActivity['activity_desc'] = 'Application is copied for reduce limit successfully.  New App ID ' . $newAppId;
                $targetModel = 'confirmReduceLimit';
            }                        
            $arrActivity['user_id'] = $userId;
            $arrActivity['app_id'] = $appId;
                                   
            \Event::dispatch("ADD_ACTIVITY_LOG", serialize($arrActivity));
        
            //Session::flash('message', 'Application is copied successfully');
            Session::flash('is_accept', 1);
            //echo '<script>$(document).ready(function(){ parent.jQuery("#'.$targetModel.'").modal("hide"); });</script>';
            return redirect()->route('company_details', ['user_id' => $userId,'app_id' => $newAppId, 'biz_id' => $newBizId]);
            //return redirect()->back();
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    public function renewalAppList()
    {
        return view('backend.app.renewal_app_list'); 
    }

   /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function checkRenewalApps()
    {
                                               
        $appData = $this->appRepo->getRenewalApp();        
        $result = '';
        foreach($appData as $app) {
            $appId  = $app->app_id;
            $bizId  = $app->biz_id;
            $userId = $app->user_id;
            
            $this->appRepo->updateAppDetails($appId, ['renewal_status' => 1]); //Ready for Renewal
            
            $userData = $this->userRepo->getfullUserDetail($userId);


            // Get business name
            $biz_entity_name = \DB::table('biz')
            ->select('biz_id', 'biz_entity_name')
            ->where('user_id',$userId)
            ->get();

            // get customer_id from lms_user
            $customer_id = \DB::table('lms_users')
            ->select('lms_user_id', 'customer_id')
            ->where('user_id',$userId)
            ->where('app_id',$appId)
            ->get();

            // Get company name and address
            $companyDetails = \DB::table('mst_company')
            ->select('comp_addr_id', 'cmp_name', 'cmp_add')
            ->where('is_reg',1)
            ->get();
            
            // Limit Sanctioned
            $prgm_limit_amt = \DB::table('app_prgm_offer')
            ->where('is_active',1)
            ->where('status',1)
            ->where('app_id',$appId)
            ->sum('prgm_limit_amt');

            // Limit Sanctioned end date
            $app_limit = \DB::table('app_limit')
            ->select('app_limit_id', 'end_date')
            ->where('user_id',$userId)
            ->where('app_id',$appId)
            ->get();

            
            /*
            if ($userData && !empty($userData->anchor_id)) {
                $toUserId = $this->userRepo->getLeadSalesManager($userId);
            } else {
                $toUserId = $this->userRepo->getAssignedSalesManager($userId);
            }
            */
            $roles = $this->appRepo->getBackStageUsers($appId, [4]);  //Assigned Sales Manager
            $toUserId = isset($roles[0]) ? $roles[0]->user_id : null;
            
            $user = $this->userRepo->getfullUserDetail($userId);
            $salesUser = $this->userRepo->getfullSalesUserDetail($toUserId);
            
            $endDate = $app->end_date;
            $date = \Carbon\Carbon::parse($endDate);
            $now  = \Carbon\Carbon::now();
            $diffInDays = $date->diffInDays($now);

            //if ($diffInDays == 7) {
                $emailData['app_id']  = \Helpers::formatIdWithPrefix($appId, 'APP');
                $emailData['lead_id'] = \Helpers::formatIdWithPrefix($userId, 'LEADID');
                $emailData['entity_name'] = $biz_entity_name[0]->biz_entity_name ? $biz_entity_name[0]->biz_entity_name : '';
                $emailData['entity_addr'] = '';
                $emailData['customer_id'] = $customer_id[0]->customer_id ? $customer_id[0]->customer_id : '';
                $emailData['biz_type'] = 'SCF';
                $emailData['receiver_user_name'] = $user->f_name .' '. $user->m_name .' '. $user->l_name;
                $emailData['receiver_email'] = $user->email;
                $emailData['sales_manager_name'] = $salesUser ? $salesUser->f_name .' '. $salesUser->m_name .' '. $salesUser->l_name : '';
                $emailData['sales_manager_email'] = $salesUser ? $salesUser->email : '';
                $emailData['prgm_limit_amt'] = number_format($prgm_limit_amt) ? number_format($prgm_limit_amt) : '';
                $emailData['app_limit'] = $app_limit[0]->end_date ? date('d-m-Y', strtotime($app_limit[0]->end_date)) : '';
                $emailData['cmp_name'] = $companyDetails[0]->cmp_name ? $companyDetails[0]->cmp_name : '';
                $emailData['cmp_add'] =$companyDetails[0]->cmp_add ? $companyDetails[0]->cmp_add : '';
                $emailData['year'] = 2020;

                \Event::dispatch("APPLICATION_RENEWAL_MAIL", serialize($emailData));
            //}
            
            $result .= $result == "" ? "Applications are ready for renewal : " . $appId : ', ' . $appId;
        }
        
        echo $result . "<br>Finished ...";
    }    
}
