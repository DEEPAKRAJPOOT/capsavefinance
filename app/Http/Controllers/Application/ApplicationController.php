<?php

namespace App\Http\Controllers\Application;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\PartnerFormRequest;
use App\Http\Requests\DocumentRequest;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use Session;

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
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }
        return view('frontend.application.business_information', compact('userArr'));
    }

    public function saveBusinessInformation(BusinessInformationRequest $request)
    {
        try {
            $arrFileData = $request->all();
            $business_info = $this->appRepo->saveBusinessInfo($arrFileData, Auth::user()->user_id);
            $appId  = Session::put('appId', $business_info['app_id']);
            
            //Add application workflow stages            
            Helpers::addWfAppStage('biz_info', $business_info['app_id'], $wf_status = 2);
            
            
            if ($business_info) {
                //Add application workflow stages
                Helpers::updateWfStage('biz_info', $business_info['app_id'], $wf_status = 1);
                
                Session::flash('message',trans('success_messages.basic_saved_successfully'));
                return redirect()->route('promoter-detail');
            } else {                
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
    public function showPromoterDetail()
    {
        $userId = Auth::user()->user_id;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }
       
       $getCin = $this->userRepo->getCinByUserId($userId);
       $cinNo  =  $getCin;       
       
       return view('frontend.application.promoter-detail')->with(array('userArr' => $userArr,'cin_no' =>$cinNo));
    } 

    /**
     * Show the business information form.
     *
     * @return \Illuminate\Http\Response
     */
    //////////////////Save Promoter Multiple Details///////////////////////// 
    public function savePromoterDetail(Request $request) {
       try {
            $arrFileData = $request->all();
            $owner_info = $this->userRepo->saveOwnerInfo($arrFileData); //Auth::user()->id
          if ($owner_info) {
                //Add application workflow stages
                $appData = $this->appRepo->getAppDataByBizId($arrFileData['biz_id']);
                $appId = $appData ? $appData->app_id : null; 
                Helpers::updateWfStage('promo_detail', $appId, $wf_status = 1);
                 
                return response()->json(['message' =>trans('success_messages.basic_saved_successfully'),'status' => 1]);
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
    public function showDocument()
    {
        $appId  = Session::has('appId') ? Session::get('appId') : 1;
        $userId = Auth::user()->user_id;
        
        $userArr = [];
        if ($appId > 0) {
            // dd($appId);
            $requiredDocs = $this->docRepo->findRequiredDocs($userId, $appId);
            if(!empty($requiredDocs)){
                $docData = $this->docRepo->appDocuments($requiredDocs, $appId);
            }
        }
        else {
            return redirect()->back()->withErrors(trans('error_messages.noAppDoucment'));
        }
//        dd($docData);   
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
            
            if ($document_info) {
                
                //Add application workflow stages
                $appData = $this->appRepo->getAppDataByBizId($arrFileData['biz_id']);
                $appId = $appData ? $appData->app_id : null; 
                Helpers::updateWfStage('promo_detail', $appId, $wf_status = 1);
                
                Session::flash('message',trans('success_messages.uploaded'));
                return redirect()->back();
            } else {
                return redirect()->back();
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
            $appId  = Session::has('appId') ? Session::get('appId') : 1;
            $userId = Auth::user()->user_id;
            $response = $this->docRepo->isUploadedCheck($userId, $appId);
            
            if ($response->count() < 1) {
                
                $this->appRepo->updateAppData($appId, ['status' => 1]);
                
                //Add application workflow stages                
                Helpers::updateWfStage('app_submitted', $appId, $wf_status = 1);
                
                return redirect()->route('front_dashboard')->with('message', trans('success_messages.app.completed'));
            } else {
                return redirect()->back()->withErrors(trans('error_messages.app.incomplete'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
}