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


    /**
     * Copy application
     * 
     * @param Request $request
     * @return type mixed
     */
    public function renewApplication(Request $request)
    {
        try {
            //$userId = 568;
            $userId = $request->get('user_id');   //510;
            $where=[];
            $where['user_id'] = $userId;
            $appData = $this->appRepo->getAppDataByOrder($where , $orderBy = 'DESC');
            if (!$appData) {
                Session::flash('message', 'No application found for renewal');
                return redirect()->back(); 
            }
            
                       
                        
            //$appId = 435;
            //$bizId = 439;  

            //$appId = 391;
            //$bizId = 392;  
            
            $appId = $appData->app_id;
            $bizId = $appData->biz_id;
            
            $this->copyApplication($userId, $appId, $bizId);
            Session::flash('message', 'Application is copied successfully');
            return redirect()->route('application_list');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

}
