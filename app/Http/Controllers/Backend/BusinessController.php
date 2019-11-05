<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PartnerFormRequest;
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
}