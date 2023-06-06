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
use Illuminate\Support\Facades\Storage;
use Exception;

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
    
    public function uploadDocument(Request $request)
    {
        return view('backend.document.upload_document');   
    }
    
    public function editUploadDocument(Request $request)
    {
        $fileId = $request->get('app_doc_file_id');
        $data = $this->docRepo->getAppDocFileById($fileId);

        return view('backend.app.edit_upload_document', [
                    'data' => $data
                ]);   
    }

    public function updateEditUploadDocument(Request $request)
    {
        $fileId = $request->get('app_doc_file_id');
        $comment = $request->get('comment');
        $appId = (int)$request->app_id; //  fetch app id
        $bizId = (int)$request->biz_id; //  fetch biz id
        $data = ['comment' => $comment ];
        $document_info = $this->docRepo->updateDocument($data, $fileId);

        Session::flash('message',trans('success_messages.documentUpdated'));
        return redirect()->route('documents', ['app_id' => $appId, 'biz_id' => $bizId]);

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
                return redirect()->route('pp_document_list', ['app_id' => $appId, 'biz_id' => $bizId,'user_id' => $userId]);
            } else {                                
                return redirect()->route('pp_document_list', ['app_id' => $appId, 'biz_id' => $bizId,'user_id' => $userId]);
            }
        } catch (Exception $ex) {                
            return redirect()->route('pp_document_list', ['app_id' => $appId, 'biz_id' => $bizId,'user_id' => $userId])->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function downloadStorageFile(Request $request)
    {
        try {
            $fileId = $request->get('file_id');
            $fileData = $this->docRepo->getFileByFileId($fileId);
            if (!empty($fileData->file_path )) {
                $file = Storage::disk('public')->exists($fileData->file_path);
                if ($file) {
                    return Storage::disk('public')->download($fileData->file_path, $fileData->file_name);
                } else {
                    return redirect()->back()->withErrors(trans('error_messages.documentNotFound'));
                }
            } else {
                return redirect()->back()->withErrors(trans('error_messages.documentNotFound'));
            }
        } catch (Exception $ex) {                
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * This method is used for see upload file in Onboarding and prepost sanction documents  
     */
    public function seeUploadFile(Request $request) {

        $fileId = $request->get('file_id');
        $fileData = $this->docRepo->getFileByFileId($fileId);
        
        if (Storage::disk(env('STORAGE_TYPE'))->exists($fileData->file_path)) {
            $s3_filepath = $fileData->file_path;
            $fileName = time().$fileData->file_name;
            $temp_filepath = tempnam(sys_get_temp_dir(), 'file');
            $file_data = Storage::disk(env('STORAGE_TYPE'))->get($s3_filepath);
            file_put_contents($temp_filepath, $file_data);

            return response()
                ->download($temp_filepath, $fileName, [], 'inline')
                ->deleteFileAfterSend();
        }else{
            exit('Requested file does not exist on our server!');
        }
    }

    public function downloadAWSS3File(Request $request)
    {
        try {
            $fileId = $request->get('file_id');
            $fileData = $this->docRepo->getFileByFileId($fileId);
            if (!empty($fileData->file_path )) {
                $file = Storage::disk(env('STORAGE_TYPE'))->exists($fileData->file_path);
                $path = $fileData->file_path;
                if ($file) {
                    return Storage::disk(env('STORAGE_TYPE'))->download($fileData->file_path, $fileData->file_name);
                } else {
                    return redirect()->back()->withErrors(trans('error_messages.documentNotFound'));
                }
            } else {
                return redirect()->back()->withErrors(trans('error_messages.documentNotFound'));
            }
        } catch (Exception $ex) {                
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

}
