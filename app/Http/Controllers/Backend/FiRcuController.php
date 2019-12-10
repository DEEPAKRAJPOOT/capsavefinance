<?php

namespace App\Http\Controllers\Backend;

use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Helpers;
use Auth;

class FiRcuController extends Controller
{
    protected $appRepo;
    protected $userRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo){
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->middleware('checkBackendLeadAccess');
    }
    
    /**
     * Display a listing of FI
     */
    public function listFI(Request $request)
    {
        $biz_id = $request->get('biz_id');
        $fiLists = $this->appRepo->getAddressforFI($biz_id);
        //dd($fiLists);
        return view('backend.fircu.fi')->with('fiLists', $fiLists);   
    }

    /**
     * Show assign FI 
     */
    public function showAssignFi(Request $request)
    {
        return view('backend.fircu.fi_trigger');   
    }

    /**
     * Save assign FI 
     */
    public function saveAssignFi(Request $request)
    {
        //dd($request->all());
        $appData = $this->appRepo->getAppDataByAppId($request->get('app_id'));
        $this->appRepo->insertFIAddress($request->all());  
        return redirect()->route('backend_fi', ['app_id' => request()->get('app_id'), 'biz_id' => $appData->biz_id]);   
    }

    /**
     * Display a listing of RCU
     */
    public function listRCU(Request $request)
    {
        $appId = $request->get('app_id');
        $promoterDocId = [2, 22, 30, 31, 32];
        
        $rcuResult = $this->appRepo->getRcuLists($appId);
//        $promoterDoc = $this->appRepo->getPromoterRcuLists($appId);
        foreach ($rcuResult as $key => $value) {
            $rcuResult[$key]['documents'] = $this->appRepo->getRcuDocuments($appId, $value->doc_id);
        }
        
//        dd($rcuResult);
        return view('backend.fircu.rcu', [
                    'data' => $rcuResult
                ]);   
    }


}
