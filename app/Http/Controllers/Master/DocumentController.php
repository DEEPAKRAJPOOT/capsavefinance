<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
 
class DocumentController extends Controller {

    public function __construct(InvMasterRepoInterface $master){
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }


     public function index(){
        return view('master.documents.index');
    }

    public function addDocument(){
    	return view('master.documents.add_documents');
    }

    public function editDocument(Request $request){
        $document_id = preg_replace('#[^0-9]#', '', $request->get('id'));
        $document_data = $this->masterRepo->findDocumentById($document_id);
    	return view('master.documents.edit_documents',['document_data' => $document_data]);
    }


    public function saveDocuments(Request $request) {
        try {
            $arrDocumentsData = $request->all();
            $arrDocumentsData['created_at'] = \carbon\Carbon::now();
            $arrDocumentsData['created_by'] = Auth::user()->user_id;
            $status = false;
            $document_id = false;
            if(!empty($request->get('id'))){
                $document_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $document_data = $this->masterRepo->findDocumentById($document_id);
                if (!empty($document_data)) {
                    $status = $this->masterRepo->updateDocuments($arrDocumentsData, $document_id);
                }
            }else{
               $status = $this->masterRepo->saveDocuments($arrDocumentsData); 
            }
            if($status){
                Session::flash('message', $document_id ? trans('master_messages.documents_edit_success') :trans('master_messages.documents_add_success'));
                return redirect()->route('get_documents_list');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_documents_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}