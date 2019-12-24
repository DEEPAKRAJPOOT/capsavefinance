<?php

namespace App\Http\Controllers\Backend;

use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocRepoInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\User;
use Session;
use Helpers;
use Auth;

class DocumentController extends Controller
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
     * Display a application for Fi and Rcu
     */
    public function list(Request $request)
    {
    	try {
            $arrFileData = $request->all();
            $appId = $request->get('app_id');
            $bizId = $request->get('biz_id');
            $userData = User::getUserByAppId($appId);
            
            if ($appId > 0) {
                $requiredDocs = $this->docRepo->findPPRequiredDocs($userData->user_id, $appId);
                if($requiredDocs->count() != 0){
                    $docData = $this->docRepo->appDocuments($requiredDocs, $appId);
                }
                else {
                    Session::flash('message',trans('error_messages.document'));
                    return redirect()->back();
                }
            }
            else {
                return redirect()->back()->withErrors(trans('error_messages.noAppDoucment'));
            }
            if ($docData) {
                return view('backend.document.list', [
                    'requiredDocs' => $requiredDocs,
                    'documentData' => $docData,
                    'user_id' => $userData->user_id,
                    'app_id' => $appId,
                    'biz_id' => $bizId,
                ]);
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }

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
