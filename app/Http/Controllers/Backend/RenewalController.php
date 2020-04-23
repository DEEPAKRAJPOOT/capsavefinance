<?php

namespace App\Http\Controllers\Backend;

use Auth;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

class RenewalController extends Controller {

    use ApplicationTrait;
    protected $appRepo;

    public function __construct(InvAppRepoInterface $app_repo)
    {        
        $this->middleware('checkBackendLeadAccess');
        $this->appRepo = $app_repo;
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
            $userId = 568;
            $appData = $this->appRepo->getRenewalApp($userId);
            
            
            if (!isset($appData[0])) {
                Session::flash('message', 'No application found for renewal');
                return redirect()->back();            
            }
            
            $appId = $appData[0]->app_id;
            $bizId = $appData[0]->biz_id;
            
            $this->copyApplication($userId, $appId, $bizId);
            Session::flash('message', 'Application is copied successfully');
            return redirect()->back();
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

}
