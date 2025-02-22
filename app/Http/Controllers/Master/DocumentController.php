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


     public function index(Request $request){
        $filter['filter_product_type'] = $request->filter_product_type;
        $filter['filter_doc_type_id'] = $request->filter_doc_type_id;
        $filter['filter_search_keyword'] = $request->filter_search_keyword;
        // dd($filter);
        return view('master.documents.index', ['filter' => $filter]);
    }

    public function addDocument(){
        $products = $this->masterRepo->getActiveProducts();
    	return view('master.documents.add_documents', ['products' => $products]);
    }

    public function editDocument(Request $request){
        $document_id = preg_replace('#[^0-9]#', '', $request->get('id'));
        $document_data = $this->masterRepo->findDocumentById($document_id);
        $products = $this->masterRepo->getActiveProducts();
        $documentProductIds = [];
        foreach ($document_data->product_document as $value) {
            $documentProductIds[] = $value->product_id;
        }
        // dd($documentProductIds);
    	return view('master.documents.edit_documents',[
                'document_data' => $document_data,
                'documentProductIds' => $documentProductIds,
                'products' => $products
                ]);
    }


    public function saveDocuments(Request $request) {
        try {
            $arrDocumentsData = $request->all();
            $filter['filter_product_type'] = $request->filter_product_type;
            $filter['filter_doc_type_id'] = $request->filter_doc_type_id;
            $filter['filter_search_keyword'] = $request->filter_search_keyword;
             
            $status = false;
            $document_id = false;
            $productsIds = isset($arrDocumentsData['product_ids']) ? $arrDocumentsData['product_ids'] : [];
            if(!empty($request->get('id'))){
                $document_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $document_data = $this->masterRepo->findDocumentById($document_id);
                if (!empty($document_data)) {
                    $arrDocumentsData['updated_by'] = Auth::user()->user_id;
                    $where = [
                        'doc_name' => $arrDocumentsData['doc_name'],
                    ];
                    $id = [
                        'id' => $document_id
                    ];
                    $checkDocName = $this->masterRepo->checkDocumentExistEditCase($where, $id); 
                    if($checkDocName > 0) {
                        Session::flash('error', 'Document already present with this name!');
                        return redirect()->route('get_documents_list');
                    }                    
                    $status = $this->masterRepo->updateDocuments($arrDocumentsData, $document_id);
                    $result = $this->masterRepo->updateProductDocuments($productsIds, $document_id);
                }
            }else{
                $where = [
                    'doc_name' => $arrDocumentsData['doc_name'],
                ];
                $checkDocName = $this->masterRepo->checkDocumentExist($where); 
                if($checkDocName > 0) {
                    Session::flash('error', 'Document already present with this name!');
                    return redirect()->route('get_documents_list');
                }                
               $arrDocumentsData['created_by'] = Auth::user()->user_id;
               $status = $this->masterRepo->saveDocuments($arrDocumentsData); 
               $result = $this->masterRepo->updateProductDocuments($productsIds, $status->id);
            }
            if($result){
                Session::flash('message', $document_id ? trans('master_messages.documents_edit_success') :trans('master_messages.documents_add_success'));

                return redirect()->route('get_documents_list', ['filter_product_type' => $filter['filter_product_type'], 'filter_doc_type_id' => $filter['filter_doc_type_id'], 'filter_search_keyword' => $filter['filter_search_keyword']]);
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_documents_list', ['filter' => $filter]);
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}