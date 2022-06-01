<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
 
class SecurityDocumentController extends Controller {

    public function __construct(InvMasterRepoInterface $master){
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }


     public function index(){
        return view('master.securitydocument.index');
    }

    public function addSecurityDoc(){
    	return view('master.securitydocument.add_securitydoc');
    }

    public function editSecurityDoc(Request $request){
        $securityDocId = preg_replace('#[^0-9]#', '', $request->get('security_doc_id'));
        $securityDoc_data = $this->masterRepo->findSecurityDocumentById($securityDocId);
    	return view('master.securitydocument.edit_securitydoc',['securityDoc_data' => $securityDoc_data]);
    }


    public function saveSecurityDoc(Request $request) {
        try {
            $arrSecurityDocData = $request->all();
            $status = false;
            $securityDocId = false;
            if(!empty($request->get('security_doc_id'))){
                $securityDocId = preg_replace('#[^0-9]#', '', $request->get('security_doc_id'));
                $security_doc_data = $this->masterRepo->findSecurityDocumentById($securityDocId);
                if (!empty($security_doc_data)) {
                    $arrSecurityDocData['updated_by'] = Auth::user()->user_id;
                    $status = $this->masterRepo->updateSecurityDocument($arrSecurityDocData, $securityDocId);
                }
            }else{
               $arrSecurityDocData['created_by'] = Auth::user()->user_id;
               $status = $this->masterRepo->saveSecurityDocument($arrSecurityDocData); 
            }
            if($status){
                Session::flash('message', $securityDocId ? trans('master_messages.security_document_edit_success') :trans('master_messages.security_document_add_success'));
                return redirect()->route('list_security_document');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('list_security_document');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}