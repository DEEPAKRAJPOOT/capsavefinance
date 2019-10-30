<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\BusinessInterface as InvBusinessRepoInterface;
use App\Http\Requests\BusinessInformationRequest;
use Session;

class BusinessController extends Controller
{
    protected $businessRepo;

    public function __construct(InvBusinessRepoInterface $buss_repo){
    	$this->businessRepo = $buss_repo;
    }

        /**
     * Show the business information form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showBusinessInformationForm()
    {
        return view('auth.business-information');
    }

    public function saveBusinessInformation(BusinessInformationRequest $request)
    {
        try {
            $arrFileData = $request->all();
            $business_info = $this->businessRepo->saveBusinessInfo($arrFileData,1);//Auth::user()->id
            if ($business_info) {
                Session::flash('message',trans('success_messages.basic_saved_successfully'));
                return redirect()->route('authorized_signatory_open');
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
    public function showAuthorizedSignatoryForm()
    {
        $userId  = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }

        return view('auth.authorized-signatory', compact('userArr'));
    } 
    
    public function saveAuthorizedSignatoryForm(PartnerFormRequest $request)
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
    
    /**
     * Show the Business documents form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showBusinessDocument()
    {
        $userId  = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }

        return view('auth.business-document', compact('userArr'));
    } 
    
    /**
     * Handle a Business documents for the application.
     *
     * @param  \Illuminate\Http\BusinessDocumentRequest  $request
     * @return \Illuminate\Http\Response
     */
    
    public function saveBusinessDocument(BusinessDocumentRequest $request)
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
    
    /**
     * Show the Associate Logistics form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAssociateLogistics()
    {
        $userId  = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }

        return view('auth.associate-logistics', compact('userArr'));
    } 
    
    /**
     * Handle a Associate Logistics for the application.
     *
     * @param  \Illuminate\Http\AssociateLogisticsRequest  $request
     * @return \Illuminate\Http\Response
     */
    
    public function saveAssociateLogistics(AssociateLogisticsRequest $request)
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