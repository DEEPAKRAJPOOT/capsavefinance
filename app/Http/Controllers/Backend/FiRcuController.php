<?php

namespace App\Http\Controllers\Backend;

use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocRepoInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Helpers;
use Auth;

class FiRcuController extends Controller
{
    protected $appRepo;
    protected $userRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocRepoInterface $doc_repo){
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->middleware('checkBackendLeadAccess');
    }
    
    /**
     * Display a listing of FI
     */
    public function listFI(Request $request)
    {
        $biz_id = $request->get('biz_id');
        $fiLists = $this->appRepo->getAddressforFI($biz_id);
        //dd($fiLists[0]->fiAddress);
        return view('backend.fircu.fi')->with('fiLists', $fiLists);   
    }

    /**
     * Display FI upload modal
     */
    public function FiUpload(Request $request)
    {
        return view('backend.fircu.fi_upload_file');   
    }

    /**
     * Save FI upload File
     */
    public function saveFiUpload(Request $request)
    {
        $biz_id = $request->get('biz_id');
        $app_id = $request->get('app_id');
        $fi_addr_id = $request->fiaid;
        $uploadData = Helpers::uploadAppFile($request->all(), $app_id);
        $userFile = $this->docRepo->saveFile($uploadData);

        $status = $this->appRepo->updateFiFile($userFile, $fi_addr_id);
        if($status){
            Session::flash('message',trans('success_messages.uploaded'));
        }else{
            Session::flash('message',trans('auth.oops_something_went_wrong'));
        }
        return redirect()->route('backend_fi', ['app_id' => $app_id, 'biz_id' => $biz_id]);  
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
