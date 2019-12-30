<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryManagementRequest;
use Illuminate\Http\Request;
use Auth;
use Session;
use Helpers;
use App\Inv\Repositories\Contracts\QmsInterface as InvQmsRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;




class QmsController extends Controller {

    protected $qmsRepo;
    protected $docRepo;
    protected $userRepo;



    public function __construct(InvQmsRepoInterface $qms_repo, InvDocumentRepoInterface $doc_repo, InvUserRepoInterface $user_repo)
    {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->qmsRepo = $qms_repo;
        $this->docRepo = $doc_repo;
        $this->userRepo = $user_repo;

    }

    public function index(Request $request)
    {
        $app_id = $request->get('app_id');
        $arrData = [];
        $arrRole = $this->userRepo->getAllRole();
        $arrData = $this->qmsRepo->showQueryList($app_id);
        return view('backend.qms.queryList', compact('arrData', 'app_id', 'arrRole'));
    }


    public function showQueryForm(Request $request)
    {
        $app_id = $request->get('app_id');
        $arrRole = $this->userRepo->getRolesByType(2);
        return view('backend.qms.queryForm', compact('arrRole', 'app_id'));
    }
    

    public function saveQueryManagement(QueryManagementRequest $request)
    {
        try {
            $app_id = $request->get('app_id');
            $assignRoleId = $request->get('assignRoleId');
            $qms_cmnt = $request->get('qms_cmnt');
            $arrData = $request->all();
            $arrFileId  = array();
            foreach ($arrData['doc_file'] as $key => $arr) {
                $arrFileData['doc_file'] = $arr;
                $uploadData = Helpers::uploadAppFile($arrFileData, $app_id);
                $userFile = $this->docRepo->saveFile($uploadData);
                $arrFileId[$key] = $userFile->file_id;                 
            };
            $fileId =  implode(',', $arrFileId);
            $dom = new \DomDocument();
            $dom->loadHtml($qms_cmnt, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD); 
            $qms_cmnt = $dom->saveHTML();
            $this->qmsRepo->saveQuery([
            'app_id' => $app_id,
            'assign_role_id' => $assignRoleId,
            'file_id' => $fileId,
            'qms_cmnt' => $qms_cmnt,
            'created_by' => Auth::user()->user_id,
            'created_at' => \Carbon\Carbon::now(),
            ]);
            Session::flash('message', trans('success_messages.query_management_saved'));
            Session::flash('operation_status', 1); 
            return redirect()->back();
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }


    public function showQmsDetails(Request $request)
    {
        try {
            
            $qms_req_id = $request->get('qms_req_id');
            $arrData = $this->qmsRepo->getQueryData($qms_req_id);
            $arrFileId = explode(',', $arrData->file_id);
            
            $arrFileData = $this->docRepo->getMultipleFileByFileId($arrFileId);


            return view('backend.qms.queryDetails', compact('arrData','arrFileData'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}
