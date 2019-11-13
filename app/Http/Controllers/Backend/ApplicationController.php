<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;


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
        return view('backend.application.business-information', compact('userArr'));
    }

    public function saveBusinessInformation(BusinessInformationRequest $request)
    {
        try {
            $arrFileData = $request->all();
            $business_info = $this->businessRepo->saveBusinessInfo($arrFileData,1);//Auth::user()->id
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
     * Show the authorized signatory form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPromoterDetail()
    {
        $userId  = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }

        return view('backend.application.promoter-detail', compact('userArr'));
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
    public function showBankDocument()
    {
        $userId  = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }

        return view('backend.application.bank-document', compact('userArr'));
    } 
    
    /**
     * Handle a Business documents for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function saveBankDocument(DocumentRequest $request)
    {
        try {
            $arrFileData = $request->all();
            $docId = 1; //  fetch document id
            $document_info = $this->docRepo->saveDocument($arrFileData,$docId);
            if ($document_info) {
                Session::flash('message',trans('success_messages.bank_document.uploaded'));
                return redirect()->route('gst-document');
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

        return view('backend.application.gst-document', compact('userArr'));
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