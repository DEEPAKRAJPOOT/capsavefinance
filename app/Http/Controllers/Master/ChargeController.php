<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use Session;
use Auth;
 
class ChargeController extends Controller {

    public function __construct(InvUserRepoInterface $user){
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->userRepo = $user;
    }


     public function index(){
        return view('master.charges.index');
    }

    public function addCharges($value=''){
    	return view('master.charges.add_charges');
    }

    public function editCharges($value=''){
    	return view('master.charges.edit_charges');
    }


    public function saveCharges(Request $request) {
        try {
            $arrChargesData = $request->all();
            $arrChargesData['created_at'] = \carbon\Carbon::now();
            $arrChargesData['created_by'] = Auth::user()->user_id;
            $status = $this->userRepo->saveCharges($arrChargesData);
            if($status){
                Session::flash('message', trans('backend_messages.charges_add_success'));
                return redirect()->route('get_charges_list');
            }else{
                Session::flash('message', trans('backend_messages.something_went_wrong'));
                return redirect()->route('get_charges_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}