<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PartnerFormRequest;
use App\Inv\Repositories\Contracts\KycInterface as InvKycRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
//use App\Http\Requests\BusinessInformationRequest;
use Session;

class KycController extends Controller
{
    protected $KycRepo;
    protected $userRepo;

    public function __construct(InvUserRepoInterface $user_repo, InvKycRepoInterface $kyc_repo){
        $this->KycRepo = $kyc_repo;
    	$this->userRepo = $user_repo;
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

        return view('auth.bank-document', compact('userArr'));
    } 
    
    /**
     * Handle a Business documents for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function saveBankDocument(Request $request)
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
    public function showGSTDocument()
    {
        $userId  = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }

        return view('auth.gst-document', compact('userArr'));
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