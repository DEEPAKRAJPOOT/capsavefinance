<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
 
class ChargeController extends Controller {

    public function __construct(InvMasterRepoInterface $master){
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }


     public function index(){
        return view('master.charges.index');
    }

    public function addCharges(){
    	return view('master.charges.add_charges');
    }

    public function editCharges(Request $request){
        $charge_id = preg_replace('#[^0-9]#', '', $request->get('id'));
        $charge_data = $this->masterRepo->findChargeById($charge_id);
    	return view('master.charges.edit_charges',['charge_data' => $charge_data]);
    }


    public function saveCharges(Request $request) {
        try {
            $arrChargesData = $request->all();
            $arrChargesData['created_at'] = \carbon\Carbon::now();
            $arrChargesData['created_by'] = Auth::user()->user_id;
            $status = false;
            $charge_id = false;
            if(!empty($request->get('id'))){
                $charge_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $charge_data = $this->masterRepo->findChargeById($charge_id);
                if (!empty($charge_data)) {
                    $status = $this->masterRepo->updateCharges($arrChargesData, $charge_id);
                }
            }else{
               $status = $this->masterRepo->saveCharges($arrChargesData); 
            }
            if($status){
                Session::flash('message', $charge_id ? trans('backend_messages.charges_edit_success') :trans('backend_messages.charges_add_success'));
                return redirect()->route('get_charges_list');
            }else{
                Session::flash('error', trans('backend_messages.something_went_wrong'));
                return redirect()->route('get_charges_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}