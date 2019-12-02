<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\PartnerFormRequest;
use App\Http\Requests\DocumentRequest;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\User;
use Session;
use Helpers;
use App\Libraries\Pdf;

class ApplicationController extends Controller
{
    protected $appRepo;
    protected $userRepo;
    protected $docRepo;

    /**
     * The pdf instance.
     *
     * @var App\Libraries\Pdf
     */
    protected $pdf;
    
    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, Pdf $pdf){
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->pdf = $pdf;
        $this->middleware('checkBackendLeadAccess');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//       if($currStage){
//        Helpers::updateWfStage($currStage->stage_code, 1, $wf_status = 1);
//      }
       return view('backend.app.index');   
              
    }

    /**
     * Render view for company detail page according to biz id
     * 
     * @param Request $request
     * @return view
     */
    public function showCompanyDetails(Request $request){
        try {
            $arrFileData = $request->all();
            
            $appId = $request->get('app_id');
            $bizId = $request->get('biz_id');
            $userId = $request->get('user_id');
            
           
            $business_info = $this->appRepo->getApplicationById($request->biz_id);
            $states = State::getStateList()->get();
            //dd($business_info->gst->pan_gst_hash);

            if ($business_info) {
                return view('backend.app.company_details')
                        ->with(['business_info'=>$business_info, 'states'=>$states])
                        ->with('user_id',$userId)
                        ->with('app_id',$appId)
                        ->with('biz_id',$bizId);
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Update company detail page according to biz id
     * 
     * @param Request $request
     * @return to promoter detail page
     */
    public function updateCompanyDetail(BusinessInformationRequest $request){
        try {
            $arrFileData = $request->all();
            $appId = $request->app_id;
            $bizId = $request->biz_id;
            
            $business_info = $this->appRepo->updateCompanyDetail($arrFileData, $bizId, Auth::user()->user_id);

            if ($business_info) {
                Session::flash('message',trans('success_messages.update_company_detail_successfully'));
                return redirect()->route('promoter_details',['app_id' =>  $appId, 'biz_id' => $bizId]);
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }


     /* Show promoter details page  */
     public function showPromoterDetails(Request $request){
        $id = Auth::user()->user_id;
        $appId = $request->get('app_id');  
        $bizId = $request->get('biz_id'); 
        $attribute['biz_id'] = $bizId;
        $attribute['app_id'] = $appId;
        $getCin = $this->userRepo->getCinByUserId($bizId);
       if($getCin==false)
       {
          return redirect()->back();
       }
        $OwnerPanApi = $this->userRepo->getOwnerApiDetail($attribute);
       
        return view('backend.app.promoter-details')->with([
            'ownerDetails' => $OwnerPanApi, 
              'cin_no' => $getCin->cin,
             'appId' => $appId, 
            'bizId' => $bizId
            ]);
    }
     /**
     * Save Promoter details form.
     *
     * @return \Illuminate\Http\Response
     */
    //////////////////Save Promoter Multiple Details///////////////////////// 
    public function savePromoter(Request $request) {
       try {
          $arrFileData = json_decode($request->getContent(), true);
          $owner_info = $this->userRepo->saveOwner($arrFileData); //Auth::user()->id
         
          if ($owner_info) {
                return response()->json(['message' =>trans('success_messages.basic_saved_successfully'),'status' => 1, 'data' => $owner_info]);
            } else {
               return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
     /**
     * Save Promoter details form.
     *
     * @return \Illuminate\Http\Response
     */
    //////////////////Save Promoter Multiple Details///////////////////////// 
    public function updatePromoterDetail(Request $request) {
       try {
            $arrFileData = $request->all();
            $owner_info = $this->userRepo->updateOwnerInfo($arrFileData); //Auth::user()->id
            if ($owner_info) {
            
                //Add application workflow stages
               /// $appId = $arrFileData['app_id']; 
                 ////Helpers::updateWfStage('promo_detail', $appId, $wf_status = 1);
                /////  $toUserId = $this->userRepo->getLeadSalesManager(Auth::user()->id);
              ///  if ($toUserId) {
                ////    Helpers::assignAppToUser($toUserId, $appId);
              ///  }
                return response()->json(['message' =>trans('success_messages.basic_saved_successfully'),'status' => 1]);
            }
            else {
               //Add application workflow stages 
              ///// Helpers::updateWfStage('promo_detail', $request->get('app_id'), $wf_status = 2);
               return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
            }
        } catch (Exception $ex) {
            //Add application workflow stages
            /////Helpers::updateWfStage('promo_detail', $request->get('app_id'), $wf_status = 2);
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    /** get karza api response     */
     public function getKarzaApiRes(Request $request)
      {
            $request  = $request->all();
            $result   = $this->userRepo->getOwnerAppRes($request);
          if( $result!='')
          {
            if($result->karza->res_file!='[]')
            {
                return response()->json(['res'=>$result->karza->res_file,'status' =>1,'type' =>$request['type']]); 
            }
            else {
               return response()->json(['status' =>0]);
            }
          }
          else
          {
              return response()->json(['status' =>2]);
              
          }
           
       }
    /**
     * Handle a Business documents for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function promoterDocumentSave(Request $request)
    {
        try {
            
            $userId = Auth::user()->user_id;
            $arrFileData = $request->all();
            $docId = $request->get('doc_id'); //  fetch document id
            $appId = $request->get('app_id'); //  fetch document id
            $OwnerId = $request->get('owner_id'); //  fetch document id
            $uploadData = Helpers::uploadAwsBucket($arrFileData, $appId);
            
            $userFile = $this->docRepo->saveFile($uploadData);
            
            if(!empty($userFile->file_id)) {
                
                $appDocData = Helpers::appDocData($arrFileData, $userFile->file_id);
                $appDocResponse = $this->docRepo->saveAppDoc($appDocData);
                $fileId = $appDocResponse->file_id;
                $response = $this->docRepo->getFileByFileId($fileId);
            }
            if ($response) {
                return response()->json([
                    'result' => $response, 
                    'status' => 1, 
                    'file_path' => $response->file_path 
                ]);
            } else {
                return response()->json([
                    'result' => '', 
                    'status' => 0 
                ]);
            }
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    
    /**
     * Render view for company detail page according to biz id
     * 
     * @param Request $request
     * @return view
     */
    public function showDocuments(Request $request){
        try {
            $arrFileData = $request->all();
            $appId = $request->get('app_id');
            $bizId = $request->get('biz_id');
            $userData = User::getUserByAppId($appId);
            
            if ($appId > 0) {
                $requiredDocs = $this->docRepo->findRequiredDocs($userData->user_id, $appId);
                if(!empty($requiredDocs)){
                    $docData = $this->docRepo->appDocuments($requiredDocs, $appId);
                }
            }
            else {
                return redirect()->back()->withErrors(trans('error_messages.noAppDoucment'));
            }
            
            if ($docData) {
                return view('backend.app.documents', [
                    'requiredDocs' => $requiredDocs,
                    'documentData' => $docData,
                    'user_id' => $userData->user_id,
                    'app_id' => $appId,
                    'biz_id' => $bizId
                ]);
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
     
    /**
     * Handle a Business documents for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function saveDocument(DocumentRequest $request)
    {
        try {
            $arrFileData = $request->all();
            // dd($arrFileData);
            $docId = (int)$request->doc_id; //  fetch document id
            $appId = (int)$request->app_id; //  fetch document id
            $userData = User::getUserByAppId($appId);
            $userId = $userData->user_id;
            $document_info = $this->docRepo->saveDocument($arrFileData, $docId, $userId);
            if ($document_info) {
                //Add/Update application workflow stages    
                $response = $this->docRepo->isUploadedCheck($userId, $appId);            
                $wf_status = $response->count() < 1 ? 1 : 2;
                Helpers::updateWfStage('doc_upload', $appId, $wf_status);
                
                Session::flash('message',trans('success_messages.uploaded'));
                return redirect()->back();
            } else {
                //Add application workflow stages
                Helpers::updateWfStage('doc_upload', $request->get('appId'), $wf_status=2);
            
                return redirect()->back();
            }
        } catch (Exception $ex) {
            //Add application workflow stages
            Helpers::updateWfStage('doc_upload', $request->get('appId'), $wf_status=2);
                
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    
    
    /**
     * Handling deleting documents file for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function applicationSave(Request $request)
    {
        try {
            $appId  = (int)$request->app_id;
            $userData = User::getUserByAppId($appId);
            $userId = $userData->user_id;
            $response = $this->docRepo->isUploadedCheck($userId, $appId);
            
            if ($response->count() < 1) {
                
                $this->appRepo->updateAppData($appId, ['status' => 1]);
                
                //Add application workflow stages                
                Helpers::updateWfStage('app_submitted', $appId, $wf_status = 1);
                
                return redirect()->route('application_list')->with('message', trans('success_messages.app.saved'));
            } else {
                //Add application workflow stages                
                Helpers::updateWfStage('app_submitted', $request->get('app_id'), $wf_status = 2);
                
                return redirect()->back()->withErrors(trans('error_messages.app.incomplete'));
            }
        } catch (Exception $ex) {
            //Add application workflow stages                
            Helpers::updateWfStage('app_submitted', $request->get('app_id'), $wf_status = 2);
                
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Render view for change application status
     * 
     * @param Request $request
     * @return view
     */
    public function changeAppStatus(Request $request) {
        $app_id = $request->get('app_id');
        $biz_id = $request->get('biz_id');
        $appStatus = [''=>'Select status', '1'=>'Completed','0'=> 'Pending', '2' => 'Onhold'];
        
        return view('backend.app.change_app_status')
                ->with('app_id', $app_id)
                ->with('biz_id', $biz_id)
                ->with('appStatus', $appStatus);
    }
 
    /**
     * Render view for assign case
     * 
     * @param Request $request
     * @return view
     */    
    public function assignCase(Request $request) {
        $app_id = $request->get('app_id');
        $biz_id = $request->get('biz_id');
        $assignee = [
            '' => 'Select assignee',
            '1' => 'credit manager 1',
            '2' => 'credit manager 2',
            '3' => 'credit manager 3',
        ];
        return view('backend.app.assign_case')
                  ->with('app_id', $app_id)
                  ->with('biz_id', $biz_id)
                  ->with('assignee', $assignee);
    }  
    
    /**
     * Update application status
     * 
     * @param Request $request
     * @return view
     */    
    public function updateAppStatus(Request $request) {
        try {
            $app_id = $request->get('app_id');
            $biz_id = $request->get('biz_id');
            $app_status = $request->get('app_status');
            $updateData = ['app_status' => $app_status];
            
            $this->appRepo->updateAppStatus($app_id, $updateData);
            Session::flash('message',trans('backend_messages.change_app_status'));
            //return redirect()->route('company_details', ['app_id' => $app_id, 'biz_id' => $biz_id]);
            return redirect()->route('application_list');
        } catch (Exception $ex ) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Save assign case
     * 
     * @param Request $request
     * @return view
     */    
    public function updateAssignee(Request $request) {
        try {
            $app_id = $request->get('app_id');
            $biz_id = $request->get('biz_id');
            $app_status = $request->get('assignee');
            $updateData = ['app_status' => $app_status];
            
            $this->appRepo->updateAssignee($app_id, $updateData);
            Session::flash('message',trans('backend_messages.update_assignee'));
            //return redirect()->route('company_details', ['app_id' => $app_id, 'biz_id' => $biz_id]);
            return redirect()->route('application_list');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Render view for add application note
     * 
     * @param Request $request
     * @return view
     */
    public function addAppNote(Request $request) {
        $app_id = $request->get('app_id');
        $biz_id = $request->get('biz_id');        
        
        return view('backend.app.add_app_note')
                ->with('app_id', $app_id)
                ->with('biz_id', $biz_id);
    } 
    
    /**
     * Save application note
     * 
     * @param Request $request
     * @return view
     */    
    public function saveAppNote(Request $request) {
        
        try {
            $app_id = $request->get('app_id');
            $biz_id = $request->get('biz_id');
            $notes = $request->get('notes');
            $noteData = [
                'app_id' => $app_id, 
                'note_data' => $notes,
                'created_at' => \Carbon\Carbon::now(),
                'created_by' => \Auth::user()->user_id
            ];
            
            $this->appRepo->saveAppNote($noteData);
            Session::flash('message',trans('backend_messages.add_note'));
            //return redirect()->route('company_details', ['app_id' => $app_id, 'biz_id' => $biz_id]);
            return redirect()->route('application_list');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    } 
    
    /**
     * Save application note
     * 
     * @param Request $request
     * @return view
     */    
    public function sendCaseConfirmbox(Request $request) {
        try{
            $user_id = $request->get('user_id');
            $app_id = $request->get('app_id');
            $currentStage = Helpers::getCurrentWfStage($app_id);
            
            //$last_completed_wf_stage = WfAppStage::getCurrentWfStage($app_id);
            $wf_order_no = $currentStage->order_no;
            $currentStage = Helpers::getNextWfStage($wf_order_no);  
            
            $e = explode(',', $currentStage->assign_role);
            $roleDropDown = $this->userRepo->getRoleByArray($e)->toArray();
            
            return view('backend.app.next_stage_confirmBox')
                ->with('app_id', $app_id)
                ->with('roles', $roleDropDown)
                ->with('user_id', $user_id);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    /**
     * Save application note
     * 
     * @param Request $request
     * @return view
     */    
    public function AcceptNextStage(Request $request) {
        try{
            
            $user_id = $request->get('user_id');
            $app_id = $request->get('app_id');
            $assign_role = $request->get('assign_role');
            $sharing_comment = $request->get('sharing_comment');
            $addl_data = [];
            $addl_data['sharing_comment'] = $sharing_comment;
            
            if ($assign_role) {                
                $currStage = Helpers::getCurrentWfStagebyRole($assign_role);
                Helpers::updateWfStageManual($currStage->stage_code, $app_id, $wf_status = 0,$assign_role, $addl_data);
            } else {
                $currStage = Helpers::getCurrentWfStage($app_id);      
                $wf_order_no = $currStage->order_no;
                $currStage = Helpers::getNextWfStage($wf_order_no);                  
                Helpers::updateWfStage($currStage->stage_code, $app_id, $wf_status = 1, $assign = true, $addl_data);
            }


            $application = $this->appRepo->updateAppDetails($app_id, ['is_assigned'=>1]); 
            Session::flash('is_accept', 1);
            return redirect()->back();
           
            //return redirect()->route('company_details', ['app_id' => $app_id, 'biz_id' => $biz_id]);
           
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Show the business information form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showBusinessInformation()
    {
        $states = State::getStateList()->get();
        return view('backend.app.business_information',compact('states'));
    }

    public function saveBusinessInformation(BusinessInformationRequest $request)
    {
        try {
            $arrFileData = $request->all();
            $user_id = $request->user_id;
            $business_info = $this->appRepo->saveBusinessInfo($arrFileData, $user_id);
            //$appId  = Session::put('appId', $business_info['app_id']);
            
            //Add application workflow stages
            Helpers::updateWfStage('new_case', $business_info['app_id'], $wf_status = 1);
            
                        
            if ($business_info) {
                //Add application workflow stages
                Helpers::updateWfStage('biz_info', $business_info['app_id'], $wf_status = 1, $assign_role = false);
                
                Session::flash('message',trans('success_messages.basic_saved_successfully'));
                return redirect()->route('promoter_details',['app_id'=>$business_info['app_id'], 'biz_id'=>$business_info['biz_id']]);
            } else {
                //Add application workflow stages
                Helpers::updateWfStage('biz_info', $business_info['app_id'], $wf_status = 2, $assign_role = false);
                
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {                
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Show the offer
     *
     * @return \Illuminate\Http\Response
     */
    public function showOffer(Request $request)
    {
        $appId = $request->get('app_id');
        $bizId = $request->get('biz_id');
        
        //$appData = $this->appRepo->getAppDataByAppId($appId);        
        //$loanAmount = $appData ? $appData->loan_amt : 0;
        
        $offerWhereCond = [];
        $offerWhereCond['app_id'] = $appId;        
        $offerData = $this->appRepo->getOfferData($offerWhereCond);
        $offerId = $offerData ? $offerData->offer_id : 0;
        $prgmId = $offerData ? $offerData->prgm_id : 0;
        $loanAmount = $offerData ? $offerData->loan_amount : 0;
        
        return view('backend.app.offer')
                ->with('appId', $appId)
                ->with('bizId', $bizId)
                ->with('loanAmount', $loanAmount)
                ->with('prgm_id', $prgmId)
                ->with('offerId', $offerId)                
                ->with('offerData', $offerData);        
    }

    /**
     * Accept Offer
     * 
     * @param Request $request
     */
    public function acceptOffer(Request $request)
    {
        $appId = $request->get('app_id');        
        $offerId = $request->get('offer_id');
        $bizId = $request->get('biz_id');        
        
        try {
            $offerData = [];
            if ($request->has('btn_accept_offer') && $request->get('btn_accept_offer') == 'Accept') {
                $offerData['status'] = 1;           
                $message = trans('backend_messages.accept_offer_success');
                
                //Update workflow stage
                Helpers::updateWfStage('sales_queue', $appId, $wf_status = 1);                
                
            } else if($request->has('btn_reject_offer') && $request->get('btn_reject_offer') == 'Reject') {
                $offerData['status'] = 2; 
                $message = trans('backend_messages.reject_offer_success');
                
                //Update workflow stage
                Helpers::updateWfStage('approver', $appId, $wf_status = 2);
                Helpers::updateWfStage('sales_queue', $appId, $wf_status = 2);
                //Helpers::updateWfStage('sanction_letter', $appId, $wf_status = 2);
                //Helpers::updateWfStage('upload_exe_doc', $appId, $wf_status = 2);
            }
            
            $savedOfferData = $this->appRepo->saveOfferData($offerData, $offerId);
            if ($savedOfferData) {
                Session::flash('message', $message);
                //return redirect()->route('gen_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId ]);
                return redirect()->route('view_offer', ['app_id' => $appId, 'biz_id' => $bizId ]);
            }
            
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }        
    }
    
    /**
     * Generate sanction letter
     * 
     * @param Request $request
     * @return View
     */
    public function genSanctionLetter(Request $request)
    {
        $appId = $request->get('app_id');
        $bizId = $request->get('biz_id');
        $offerId = $request->get('offer_id');
        
        $offerWhereCond = [];
        $offerWhereCond['offer_id'] = $offerId;        
        $offerData = $this->appRepo->getOfferData($offerWhereCond);
        
        return view('backend.app.sanction_letter')
                ->with('appId', $appId)
                ->with('bizId', $bizId)
                ->with('offerId', $offerId)
                ->with('offerData', $offerData);          
    }
    
    /**
     * Download sanction letter
     * 
     * @return \Illuminate\Http\Response
     */
    public function downloadSanctionLetter(Request $request)
    {
        
        $appId = $request->get('app_id');
        $offerId = $request->get('offer_id');
        
        try {
            $offerWhereCond = [];
            $offerWhereCond['offer_id'] = $offerId;
            $offerData = $this->appRepo->getOfferData($offerWhereCond);

            $fileName = 'sanction_letter_'. time() . '.pdf';
            $htmlContent = \View::make('backend.app.download_sanction_letter', 
                    compact('offerData')+['appId' => $appId, 'offerId' => $offerId]
                    )->render();

            //Update workflow stage
            Helpers::updateWfStage('sanction_letter', $appId, $wf_status = 1);
            
            return response($this->pdf->render($htmlContent), 200)->withHeaders([
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => ($request->has('download') ? 'attachment' : 'inline') . "; filename=" . $fileName,
            ]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Show upload sanction letter
     * 
     * @param Request $request
     * @return View
     */
    public function showUploadSanctionLetter(Request $request)
    {
        $appId = $request->get('app_id');
        $offerId = $request->get('offer_id');
        $bizId = $request->get('biz_id');
        $docId = 33;  //
               
        return view('backend.app.upload_sanction_letter')
                ->with('appId', $appId)
                ->with('bizId', $bizId)
                ->with('offerId', $offerId)               
                ->with('docId', $docId);       
    }
    
    /**
     * Upload signed sanction letter
     * 
     * @param Request $request
     * @return Response
     */
    public function uploadSanctionLetter(Request $request)
    {
        
        $arrFileData = $request->all();
                
        $docId = $request->get('doc_id');
        $appId = $request->get('app_id');
        $bizId = $request->get('biz_id');
        $offerId = $request->get('offer_id');
        
        try {
            $uploadData = Helpers::uploadAwsBucket($arrFileData, $appId);

            $userFile = $this->docRepo->saveFile($uploadData);

            if(!empty($userFile->file_id)) {
                
                $appDocData = Helpers::appDocData($arrFileData, $userFile->file_id);
                $appDocResponse = $this->docRepo->saveAppDoc($appDocData);
                
                //Update workflow stage
                Helpers::updateWfStage('upload_exe_doc', $appId, $wf_status = 1);
                
                Session::flash('message',trans('backend_messages.upload_sanction_letter_success'));
            } else {
                Session::flash('message',trans('backend_messages.upload_sanction_letter_fail'));
            }            
            return redirect()->route('gen_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId ]);
            
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }       
    }    
}