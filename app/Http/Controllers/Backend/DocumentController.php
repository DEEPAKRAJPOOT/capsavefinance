<?php

namespace App\Http\Controllers\Backend;

use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocRepoInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DocumentRequest;
use App\Inv\Repositories\Models\User;
use Session;
use Helpers;
use Auth;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

class DocumentController extends Controller
{
    use ApplicationTrait;
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
    public function listDocument(Request $request)
    {
    	try {            
            $arrFileData = $request->all();
            $appId = $request->get('app_id');
            $bizId = $request->get('biz_id');
            $userData = User::getUserByAppId($appId);                        
            $allProductDoc = [];
            $docData = [];
            $requiredDocs = [];
            $docFlag = 0;
            $noDocFlag = 0;

            $appProduct = $this->appRepo->getApplicationProduct($appId);
            
            $docTypes = config('common.doc_type');            
                                  
            if ($appId > 0) {
                foreach ($appProduct->products as $key => $value) {
                    $requiredDocs[$key]['productInfo'] = $value;
                    $requiredDocs[$key]['documents'] = $this->docRepo->findPPRequiredDocs($userData->user_id, $appId, $value->id);
                    // dd($requiredDocs);
                    if($requiredDocs[$key]['documents']->count() != 0){
                        $docData += $this->docRepo->appPPDocuments($requiredDocs[$key]['documents'], $appId);
                    }
                }
            }
            else {
                return redirect()->back()->withErrors(trans('error_messages.noAppDoucment'));
            }

                        
            foreach($requiredDocs as $key => $product) {
                if($product['documents']->count() > 0 ) {
                    $docFlag ++;
                }
            }                

            return view('backend.document.list', [
                'requiredDocs' => $requiredDocs,
                'documentData' => $docData,
                'user_id' => $userData->user_id,
                'app_id' => $appId,
                'biz_id' => $bizId,
                'docFlag' => $docFlag,
                'docTypes' => $docTypes
            ]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }

    }
    
    /**
     * Show assign RCU 
     */
    public function uploadDocument(Request $request)
    {
        return view('backend.document.upload_document');   
    }
/**
     * Handle a Business documents for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function saveDocument(DocumentRequest $request)
    {
        
            $arrFileData = $request->all();
            $docId = (int)$request->doc_id; //  fetch document id
            $appId = (int)$request->app_id; //  fetch app id
            $bizId = (int)$request->biz_id; //  fetch biz id
        try {
            $userData = $this->userRepo->getUserByAppId($appId);
            $userId = $userData->user_id;

            switch ($docId) {
                case '4':
                    $file_bank_id = $arrFileData['file_bank_id'];
                    $bankData = State::getBankName($file_bank_id);
                    $arrFileData['doc_name'] = $bankData['bank_name'] ?? NULL;
                    $arrFileData['finc_year'] = NULL;
                    $arrFileData['gst_month'] = $arrFileData['bank_month'];
                    $arrFileData['gst_year'] = $arrFileData['bank_year'];
                    $arrFileData['pwd_txt'] = $arrFileData['is_pwd_protected'] ? $arrFileData['pwd_txt'] :NULL;
                    break;
                case '5':
                    $arrFileData['file_bank_id'] = NULL;
                    $arrFileData['gst_month'] = NULL;
                    $arrFileData['gst_year'] = NULL;
                    $arrFileData['pwd_txt'] = $arrFileData['is_pwd_protected'] ? $arrFileData['pwd_txt'] :NULL;
                    break;

                case '6':
                    $arrFileData['file_bank_id'] = NULL;
                    $arrFileData['finc_year']    = NULL;
                    $arrFileData['is_pwd_protected'] = NULL;
                    $arrFileData['is_scanned'] = NULL;
                    $arrFileData['pwd_txt'] = NULL;
                    break;
                
                default:
                    //$arrFileData = "Invalid Doc ID";
                    $arrFileData['file_bank_id'] = NULL;
                    $arrFileData['finc_year']    = NULL;
                    $arrFileData['is_pwd_protected'] = NULL;
                    $arrFileData['is_scanned'] = NULL;
                    $arrFileData['pwd_txt'] = NULL;                                        
                    break;
            }
            $document_info = $this->docRepo->saveDocument($arrFileData, $docId, $userId);
            if ($document_info) {
                Session::flash('message',trans('success_messages.uploaded'));
                return redirect()->route('pp_document_list', ['app_id' => $appId, 'biz_id' => $bizId]);
            } else {                                
                return redirect()->route('pp_document_list', ['app_id' => $appId, 'biz_id' => $bizId]);
            }
        } catch (Exception $ex) {                
            return redirect()->route('pp_document_list', ['app_id' => $appId, 'biz_id' => $bizId])->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

}
