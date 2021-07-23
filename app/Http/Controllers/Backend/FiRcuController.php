<?php

namespace App\Http\Controllers\Backend;

use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocRepoInterface;
use App\Inv\Repositories\Models\Master\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Helpers;
use Auth;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;

class FiRcuController extends Controller
{
    protected $appRepo;
    protected $userRepo;
    use ActivityLogTrait;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocRepoInterface $doc_repo, InvMasterRepoInterface $mstRepo){
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->middleware('checkBackendLeadAccess');
        $this->mstRepo = $mstRepo;
    }
    
    /**
     * Display a application for Fi and Rcu
     */
    public function appList()
    {
        return view('backend.fircu.app_list');   
    }
    
    /**
     * Display a listing of FI
     */
    public function listFI(Request $request)
    {
        $biz_id = $request->get('biz_id');
        if(Auth::user()->agency_id != null)
            $fiLists = $this->appRepo->getAddressforAgencyFI($biz_id);
        else
            $fiLists = $this->appRepo->getAddressforFI($biz_id);

        $status_lists = Status::where(['status_type'=>3, 'is_active'=>1])->pluck('status_name', 'id');

        $addrType = ['Company (GST Address)', 'Company (Communication Address)', 'Company ()', 'Company (Warehouse Address)', 'Company (Factory Address)', 'Management Address', 'Additional Address'];
        //dd($fiLists[0]->fiAddress);
        return view('backend.fircu.fi')->with(['fiLists'=> $fiLists, 'addrType'=> $addrType, 'status_lists'=> $status_lists]);   
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
        $agencies = $this->appRepo->getAllAgency('fi');
        $agency_users = $this->userRepo->getAllAgencyUsers();
        //dd($agency_users->toArray());
        return view('backend.fircu.fi_trigger')->with(['agencies'=>$agencies, 'agency_users'=>$agency_users]);   
    }

    /**
     * Save assign FI 
     */
    public function saveAssignFi(Request $request)
    {
        $roleData = \Auth::user()->user_id;
        $userId = $request->all('to_id');
        $appData = $this->appRepo->getAppDataByAppId($request->get('app_id'));
        if((int)$userId['to_id'] == $roleData) {
            Session::flash('error',trans('You can not assign to same user'));
           return redirect()->route('backend_fi', ['app_id' => request()->get('app_id'), 'biz_id' => $appData->biz_id]);  
        }
        $this->appRepo->insertFIAddress($request->all());  

        $whereActivi['activity_code'] = 'save_assign_fi';
        $activity = $this->mstRepo->getActivity($whereActivi);
        if(!empty($activity)) {
            $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
            $activity_desc = 'Trigger for FI in FI Residence AppID ' . $request->get('app_id');
            $arrActivity['app_id'] = $request->get('app_id');
            $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
        }                        
        
        return redirect()->route('backend_fi', ['app_id' => request()->get('app_id'), 'biz_id' => $appData->biz_id]);   
    }


    /**
     * Display a listing of Inspection
     */
    public function listInspection(Request $request)
    {
        $biz_id = $request->get('biz_id');
        if(Auth::user()->agency_id != null)
            $fiLists = $this->appRepo->getAddressforAgencyFI($biz_id);
        else
            $fiLists = $this->appRepo->getAddressforFI($biz_id);

        $status_lists = Status::where(['status_type'=>3, 'is_active'=>1])->pluck('status_name', 'id');

        $addrType = ['Company (GST Address)', 'Company (Communication Address)', 'Company ()', 'Company (Warehouse Address)', 'Company (Factory Address)', 'Management Address', 'Additional Address'];
        //dd($fiLists[0]->fiAddress);
        return view('backend.fircu.inspection')->with(['fiLists'=> $fiLists, 'addrType'=> $addrType, 'status_lists'=> $status_lists]);   
    }

    /**
     * Display Inspection upload modal
     */
    public function InspectionUpload(Request $request)
    {
        return view('backend.fircu.inspection_upload_file');   
    }

    /**
     * Save Inspection upload File
     */
    public function saveInspectionUpload(Request $request)
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
        return redirect()->route('backend_inspection', ['app_id' => $app_id, 'biz_id' => $biz_id]);  
    }

    /**
     * Show assign Inspection
     */
    public function showAssignInspection(Request $request)
    {
        $agencies = $this->appRepo->getAllAgency('inspection');
        $agency_users = $this->userRepo->getAllAgencyUsers();
        //dd($agency_users->toArray());
        return view('backend.fircu.inspection_trigger')->with(['agencies'=>$agencies, 'agency_users'=>$agency_users]);   
    }

    /**
     * Save assign Inspection 
     */
    public function saveAssignInspection(Request $request)
    {
        //dd($request->all());
        $appData = $this->appRepo->getAppDataByAppId($request->get('app_id'));
        $this->appRepo->insertFIAddress($request->all());  
        return redirect()->route('backend_inspection', ['app_id' => request()->get('app_id'), 'biz_id' => $appData->biz_id]);   
    }


    /**
     * Display a listing of RCU
     */
    public function listRCU(Request $request)
    {
        $appId = $request->get('app_id');
        if(Auth::user()->agency_id != null)
            $rcuResult = $this->appRepo->getRcuActiveLists($appId);
        else
            $rcuResult = $this->appRepo->getRcuLists($appId);

        foreach ($rcuResult as $key => $value) {
            $currentRcuDoc = $this->appRepo->getCurrentRcuDoc($appId, $value->doc_id);
            $value->current_rcu =  (isset($currentRcuDoc)) ? $currentRcuDoc : '';
            $value->current_agency =  (isset($currentRcuDoc->agency)) ? $currentRcuDoc->agency : '';
            $value->cm_status =  (isset($currentRcuDoc->cmStatus->status_name)) ? $currentRcuDoc->cmStatus->status_name : '';
            $rcuResult[$key]['documents'] = $this->appRepo->getRcuDocuments($appId, $value->doc_id);

            if(Auth::user()->agency_id != null)
                $rcuResult[$key]['agencies'] = $this->appRepo->getRcuActiveAgencies($appId, $value->doc_id);
            else
                $rcuResult[$key]['agencies'] = $this->appRepo->getRcuAgencies($appId, $value->doc_id);        
        }
        // dd($rcuResult);
        return view('backend.fircu.rcu', [
                    'data' => $rcuResult
                ]);   
    }
    
    
    /**
     * Show assign RCU 
     */
    public function showAssignRcu(Request $request)
    {
        $agencies = $this->appRepo->getAllAgency('rcu');
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
