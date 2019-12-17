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
        $addrType = ['Company (GST Address)', 'Company (Communication Address)', 'Company ()', 'Company (Warehouse Address)', 'Company (Factory Address)','Management Address'];
        //dd($fiLists[0]->fiAddress);
        return view('backend.fircu.fi')->with(['fiLists'=> $fiLists, 'addrType'=> $addrType]);   
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
        $rcuResult = $this->appRepo->getRcuLists($appId);
        foreach ($rcuResult as $key => $value) {
            $currentRcuDoc = $this->appRepo->getCurrentRcuDoc($appId, $value->doc_id);
            $value->current_rcu =  (isset($currentRcuDoc)) ? $currentRcuDoc : '';
            $value->current_agency =  (isset($currentRcuDoc->agency)) ? $currentRcuDoc->agency : '';
            $value->cm_status =  (isset($currentRcuDoc->cmStatus->status_name)) ? $currentRcuDoc->cmStatus->status_name : '';
            $rcuResult[$key]['documents'] = $this->appRepo->getRcuDocuments($appId, $value->doc_id);
            $rcuResult[$key]['agencies'] = $this->appRepo->getRcuAgencies($appId, $value->doc_id);
        }
//        dd($rcuResult);
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
        $requestAll = $request->all();
        $appId = $request->get('app_id');
        $documentIds = $request->get('document_ids');
        
        $docIds = explode('#', trim($documentIds, '#'));
        $rcuDocArr = [];
        $rcuStatusLogArr = [];
        if($requestAll) {
            
            $rcuDocArr['app_id']=$requestAll['app_id'];
            $rcuDocArr['agency_id']=$requestAll['agency_id'];
            $rcuDocArr['from_id']=Auth::user()->user_id;
            $rcuDocArr['to_id']=$requestAll['to_id'];
            $rcuDocArr['rcu_status_id']= 2;
            $rcuDocArr['rcu_status_updated_by']=0;
            $rcuDocArr['rcu_comment']=$requestAll['comment'];
            $rcuDocArr['cm_rcu_status_id']=1;
            $rcuDocArr['file_id']= 0;
            $rcuDocArr['is_active']= 1;
            $rcuDocArr['created_at']=\Carbon\Carbon::now();
            $rcuDocArr['created_by']=Auth::user()->user_id;
            $rcuStatusLogArr['rcu_status_id']=2;
            $rcuStatusLogArr['rcu_comment']=$requestAll['comment'];
            $rcuStatusLogArr['created_at']=\Carbon\Carbon::now();
            $rcuStatusLogArr['created_by']=Auth::user()->user_id;
        }
        
        foreach ($docIds as $key=>$value) {
            $rcuDocArr['doc_id']=$value;
            $rcuDocResponse = $this->appRepo->assignRcuDocument($rcuDocArr);
            
            if($rcuDocResponse == 'Assigned') {
                Session::flash('message',trans('success_messages.rcu.alreadyAssigned'));
                return redirect()->route('backend_rcu', ['app_id' => request()->get('app_id'), 'biz_id' => $appData->biz_id]);
                
            }
            if($rcuDocResponse) {
                $rcuStatusLogArr['rcu_doc_id'] = $rcuDocResponse->rcu_doc_id;
                $result = $this->appRepo->saveRcuStatusLog($rcuStatusLogArr);
            }
                
        }
        
        Session::flash('message',trans('success_messages.rcu.assigned'));
        return redirect()->route('backend_rcu', ['app_id' => request()->get('app_id'), 'biz_id' => $appData->biz_id]);   
    }

    /**
     * Display Rcu upload modal
     */
    public function RcuUpload(Request $request)
    {
        return view('backend.fircu.rcu_upload_file');   
    }

    /**
     * Save FI upload File
     */
    public function saveRcuUpload(Request $request)
    {
        $app_id = $request->get('app_id');
        $biz_id = $request->get('biz_id');
        $rcuDocId = $request->rcu_doc_id;
        $uploadData = Helpers::uploadAppFile($request->all(), $app_id);
        $userFile = $this->docRepo->saveFile($uploadData);

        $status = $this->appRepo->updateRcuFile($userFile, $rcuDocId);
        if($status){
            Session::flash('message',trans('success_messages.uploaded'));
        }else{
            Session::flash('message',trans('auth.oops_something_went_wrong'));
        }
        return redirect()->route('backend_rcu', ['app_id' => $app_id, 'biz_id' => $biz_id]); 
    }

}
