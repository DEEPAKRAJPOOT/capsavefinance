<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\BusinessInterface as InvBusinessRepoInterface;

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

    public function saveBusinessInformation(Request $request)
    {
        $request->validate([
            'biz_gst_number' => 'required|string|max:50',
            'biz_pan_number' => 'required|string|max:10',
            'biz_entity_name' => 'required|string|max:100',
            'biz_type_id' => 'required|numeric|digits:1',
            'biz_email' => 'required|email',
            'biz_mobile' => 'required|numeric|digits:10',
            'entity_type_id' => 'required|numeric',
            'biz_cin' => 'required|string|max:50',
            'biz_address' => 'required|string|max:100',
            'biz_city' => 'required|string|max:50',
            'biz_state' => 'required|string|max:50',
            'biz_pin' => 'required|numeric|digits:6',
            'biz_corres_address' => 'required|string|max:100',
            'biz_corres_city' => 'required|string|max:50',
            'biz_corres_state' => 'required|string|max:50',
            'biz_corres_pin' => 'required|numeric|digits:6',
        ],[
            'biz_gst_number.required' => 'GST number is required',
            'biz_pan_number.required' => 'PAN number is required',
            'biz_entity_name.required' => 'Business name is required',
            'biz_type_id.required' => 'Type of industry is required',
            'biz_email.required' => 'Business email is required',
            'biz_mobile.required' => 'Business mobile is required',
            'entity_type_id.required' => 'Entity type is required',
            'biz_cin' => 'Business CIN is required',
            'biz_address.required' => 'Business address is required',
            'biz_city.required' => 'Business city is required',
            'biz_state.required' => 'Business state is required',
            'biz_pin.required' => 'Business PIN is required',
            'biz_corres_address.required' => 'Correspondence address is required',
            'biz_corres_city.required' => 'Correspondence city is required',
            'biz_corres_state.required' => 'Correspondence state is required',
            'biz_corres_pin.required' => 'Correspondence PIN is required',
        ]);

        try {
            $arrFileData = $request->all();
            $business_Info = $this->businessRepo->saveBusinessInfo($arrFileData,1);//Auth::user()->id
            if ($business_Info) {
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
