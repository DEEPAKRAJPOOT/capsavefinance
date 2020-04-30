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


        return view('backend.app.copy_app_confirmbox')
                        ->with('userId', $userId)
                        ->with('appId', $appId)           
                        ->with('bizId', $bizId); 
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
            
            $this->copyApplication($userId, $appId, $bizId);
            //Session::flash('message', 'Application is copied successfully');
            Session::flash('is_accept', 1);
            //echo '<script>$(document).ready(function(){ parent.jQuery("#confirmCopyApp").modal("hide"); });</script>';
            return redirect()->route('company_details', ['user_id' => $userId,'app_id' => $appId, 'biz_id' => $bizId]);
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
            $salesUser = $this->userRepo->getfullUserDetail($toUserId);
            
            $endDate = $app->end_date;
            $date = \Carbon\Carbon::parse($endDate);
            $now  = \Carbon\Carbon::now();
            $diffInDays = $date->diffInDays($now);

            //if ($diffInDays == 7) {
                $emailData['app_id']  = \Helpers::formatIdWithPrefix($appId, 'APP');
                $emailData['lead_id'] = \Helpers::formatIdWithPrefix($userId, 'LEADID');
                $emailData['entity_name'] = 
                $emailData['receiver_user_name'] = $user->f_name .' '. $user->m_name .' '. $user->l_name;
                $emailData['receiver_email'] = $user->email;
                $emailData['sales_manager_name'] = $salesUser ? $salesUser->f_name .' '. $user->m_name .' '. $user->l_name : '';
                $emailData['sales_manager_email'] = $salesUser ? $salesUser->email : '';

                \Event::dispatch("APPLICATION_RENEWAL_MAIL", serialize($emailData));
            //}
            
            $result .= $result == "" ? "Applications are ready for renewal : " . $appId : ', ' . $appId;
        }
        
        echo $result . "<br>Finished ...";
    }    
}
