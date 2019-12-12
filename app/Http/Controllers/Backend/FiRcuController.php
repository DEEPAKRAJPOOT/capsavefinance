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
        //dd($fiLists[0]->fiAddress[0]->status);
        return view('backend.fircu.fi')->with('fiLists', $fiLists);   
    }

    /**
     * Show assign FI 
     */
    public function showAssignFi(Request $request)
    {
        $agencies = $this->appRepo->getAllAgency();
        $agency_users = $this->userRepo->getAllAgencyUsers();
        //dd($agency_users->toArray());
        return view('backend.fircu.fi_trigger')->with(['agencies'=>$agencies, 'agency_users'=>$agency_users]);   
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
        $rcuResult = $this->appRepo->getRcuLists($appId);
        foreach ($rcuResult as $key => $value) {
            $rcuResult[$key]['documents'] = $this->appRepo->getRcuDocuments($appId, $value->doc_id);
        }
        
        return view('backend.fircu.rcu', [
                    'data' => $rcuResult
                ]);   
    }
    
    
    /**
     * Show assign RCU 
     */
    public function showAssignRcu(Request $request)
    {
        $agencies = $this->appRepo->getAllAgency();
        $agency_users = $this->userRepo->getAllAgencyUsers();
        
        return view('backend.fircu.rcu_trigger')->with(['agencies'=>$agencies, 'agency_users'=>$agency_users]);   
    }

    /**
     * Save assign RCU 
     */
    public function saveAssignRcu(Request $request)
    {
        $appData = $this->appRepo->getAppDataByAppId($request->get('app_id'));
        $docIds = explode('#', trim($data['document_ids'], '#'));
        $customLogArr = [];
        $customAddArr = [];
        foreach ($docIds as $key=>$value) {
            $customLogArr[$key]['whom_id']=$value;
            $customLogArr[$key]['fi_rcu_type']=1;
            $customLogArr[$key]['fi_rcu_status']=2;
            $customLogArr[$key]['fi_rcu_comment']=$data['comment'];
            $customLogArr[$key]['created_by']=Auth::user()->user_id;

            $customAddArr[$key]['agency_id']=$data['agency_id'];
            $customAddArr[$key]['from_id']=Auth::user()->user_id;
            $customAddArr[$key]['to_id']=$data['to_id'];
            $customAddArr[$key]['biz_addr_id']=$value;
            $customAddArr[$key]['fi_comment']=$data['comment'];
            $customAddArr[$key]['is_active']=1;
            $customAddArr[$key]['created_by']=Auth::user()->user_id;
        }
        
        $this->appRepo->assignRcuDocument($request->all()); 
        
        return redirect()->route('backend_rcu', ['app_id' => request()->get('app_id'), 'biz_id' => $appData->biz_id]);   
    }


}
