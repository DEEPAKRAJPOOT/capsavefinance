<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\PartnerFormRequest;
use App\Http\Requests\DocumentRequest;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\OwnerInterface as InvOwnerRepoInterface;
use App\Inv\Repositories\Contracts\BusinessInterface as InvBusinessRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use Session;

class ApplicationController extends Controller
{
    protected $businessRepo;
    protected $ownerRepo;
    protected $userRepo;
    protected $docRepo;

    public function __construct(InvBusinessRepoInterface $buss_repo, InvOwnerRepoInterface $owner_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo){
        $this->middleware('auth');
        $this->businessRepo = $buss_repo;
        $this->ownerRepo = $owner_repo;
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
        return view('frontend.application.business-information', compact('userArr'));
    }

    public function saveBusinessInformation(BusinessInformationRequest $request)
    {
        try {
            $arrFileData = $request->all();
            $business_info = $this->businessRepo->saveBusinessInfo($arrFileData, Auth::user()->user_id);
            $appId  = Session::put('appId', $business_info['app_id']);

            if ($business_info) {
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
       
       $getCin = $this->ownerRepo->getCinByUserId($userId);
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
            $owner_info = $this->ownerRepo->saveOwnerInfo($arrFileData); //Auth::user()->id
          if ($owner_info) {
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
//            dd($request);
            $arrFileData = $request->all();
            $docId = 1; //  fetch document id
            $document_info = $this->docRepo->saveDocument($arrFileData, $docId);
            if ($document_info) {
                Session::flash('message',trans('success_messages.uploaded'));
                return redirect()->back();
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Show the Associate Logistics form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showGSTDocument()
    {
        $userId  = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }

        return view('frontend.application.gst-document', compact('userArr'));
    } 
     
   /**
     * Handle a Associate Logistics for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function saveGSTDocument(Request $request)
    {
        try {
            
            $data        = [];
            $arrFileData = [];
            $arrFileData = $request->all();
            dd($arrFileData);
            $user = $this->create($arrFileData);
           
            if ($user) {
                Session::flash('message',trans('success_messages.basic_saved_successfully'));
                return redirect()->route('authorized_signatory_open');
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}