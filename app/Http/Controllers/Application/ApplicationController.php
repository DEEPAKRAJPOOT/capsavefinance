<?php

namespace App\Http\Controllers\Application;

use Auth;
use Helpers;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\PartnerFormRequest;
use App\Http\Requests\DocumentRequest;
use Eastwest\Json\Facades\Json;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Models\Master\State;

class ApplicationController extends Controller
{
    protected $appRepo;
    protected $userRepo;
    protected $docRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo){
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
    }
    
    /**
     * Show the business information form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showBusinessInformationForm()
    {
        $userId  = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        $states = State::getStateList()->get();

        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }
        return view('frontend.application.business_information', compact(['userArr','states']));
    }

    public function saveBusinessInformation(BusinessInformationRequest $request)
    {
        try {
            $arrFileData = $request->all();
            $business_info = $this->appRepo->saveBusinessInfo($arrFileData, Auth::user()->user_id);
            //$appId  = Session::put('appId', $business_info['app_id']);
            
            //Add application workflow stages
            Helpers::updateWfStage('new_case', $business_info['app_id'], $wf_status = 1);
            
                        
            if ($business_info) {
                //Add application workflow stages
                Helpers::updateWfStage('biz_info', $business_info['app_id'], $wf_status = 1);
                
                Session::flash('message',trans('success_messages.basic_saved_successfully'));
                return redirect()->route('promoter-detail',['app_id'=>$business_info['app_id'], 'biz_id'=>$business_info['biz_id']]);
            } else {
                //Add application workflow stages
                Helpers::updateWfStage('biz_info', $business_info['app_id'], $wf_status = 2);
                
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {                
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Show the Promoter Details form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPromoterDetail(Request $request)
    {
        $biz_id = $request->get('biz_id');
        $userId = Auth::user()->user_id;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }
       $attribute['biz_id'] = $biz_id;
       $ownerDetail = $this->userRepo->getOwnerDetail($attribute); 
       $getCin = $this->userRepo->getCinByUserId($biz_id);
       if($getCin==false)
       {
           return redirect()->route('business_information_open');
       }
       return view('frontend.application.promoter-detail')->with(['userArr' => $userArr,
           'cin_no' => $getCin->cin,
           'ownerDetails' => $ownerDetail,
           'biz_id' => $biz_id
        ]);
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
                $appId = $arrFileData['app_id']; 

                $appId = $appData ? $appData->app_id : null; 

                Helpers::updateWfStage('promo_detail', $appId, $wf_status = 1);
                 


                Helpers::updateWfStage('promo_detail', $appId, $wf_status = 1);
                
                $toUserId = $this->userRepo->getLeadSalesManager(Auth::user()->id);
                if ($toUserId) {
                    Helpers::assignAppToUser($toUserId, $appId);
                }
                return response()->json(['message' =>trans('success_messages.basic_saved_successfully'),'status' => 1]);
            }
            else {
               //Add application workflow stages 
               Helpers::updateWfStage('promo_detail', $request->get('app_id'), $wf_status = 2);
               return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
            }
        } catch (Exception $ex) {
            //Add application workflow stages
            Helpers::updateWfStage('promo_detail', $request->get('app_id'), $wf_status = 2);
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
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
     * Show the Business documents form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showDocument(Request $request)
    {
        $appId = $request->get('app_id');
        $userId = Auth::user()->user_id;

        if ($appId > 0) {
            $requiredDocs = $this->docRepo->findRequiredDocs($userId, $appId);
            if(!empty($requiredDocs)){
                $docData = $this->docRepo->appDocuments($requiredDocs, $appId);
            }
        }
        else {
            return redirect()->back()->withErrors(trans('error_messages.noAppDoucment'));
        }

        return view('frontend.application.document')->with([
            'requiredDocs' => $requiredDocs,
            'documentData' => $docData
        ]);
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
            $docId = 1; //  fetch document id
            $document_info = $this->docRepo->saveDocument($arrFileData, $docId);
            $userId = Auth::user()->user_id;
            if ($document_info) {
                
                //Add/Update application workflow stages
                $appId = $arrFileData['appId'];       
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
    
    public function documentDelete($appDocFileId)
    {
        try {
            $response = $this->docRepo->deleteDocument($appDocFileId);
            
            if ($response) {
                Session::flash('message',trans('success_messages.deleted'));
                return redirect()->back();
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
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
            $appId  = $request->get('app_id');
            $userId = Auth::user()->user_id;
            $response = $this->docRepo->isUploadedCheck($userId, $appId);
            
            if ($response->count() < 1) {
                
                $this->appRepo->updateAppData($appId, ['status' => 1]);
                
                //Add application workflow stages                
                Helpers::updateWfStage('app_submitted', $appId, $wf_status = 1);
                
                return redirect()->route('front_dashboard')->with('message', trans('success_messages.app.completed'));
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
    
}