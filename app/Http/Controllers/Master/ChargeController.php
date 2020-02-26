<?php
 
namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
use DB;
 
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
            if(!empty($request->get('id')))
            {
                $charge_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $charge_data = $this->masterRepo->findChargeById($charge_id);
                if (!empty($charge_data))
                {
                    $status = $this->masterRepo->updateCharges($arrChargesData, $charge_id);
                     $transUpdateData  =    (['trans_name' =>$request->chrg_name,
                                'credit_desc' => $request->credit_desc,
                                'debit_desc'  => $request->debit_desc,
                                'is_visible'  => 1,
                                'is_active'  => $request->is_active,
                                'created_at' => $arrChargesData['created_at'],
                                'created_by' => $arrChargesData['created_by']]);
                DB::table('mst_trans_type')->where('chrg_master_id',$charge_id)->update($transUpdateData);
                Session::flash('message', $charge_id ? trans('master_messages.charges_edit_success') :trans('master_messages.charges_add_success'));
                return redirect()->route('get_charges_list');
                
                }
                else
                {
                    Session::flash('error', trans('master_messages.something_went_wrong'));
                    return redirect()->route('get_charges_list');
                }
            }
            else
            {
                $status = $this->masterRepo->saveCharges($arrChargesData);
                if($status)
                {
                    $transData  =    (['trans_name' =>$request->chrg_name,
                                     'credit_desc' => $request->credit_desc,
                                     'debit_desc'  => $request->debit_desc,
                                     'chrg_master_id' => $status,
                                     'is_visible'  => 1,
                                     'is_active'  => $request->is_active,
                                     'created_at' => $arrChargesData['created_at'],
                                     'created_by' => $arrChargesData['created_by']]);
                     DB::table('mst_trans_type')->insert( $transData);
                     Session::flash('message', $charge_id ? trans('master_messages.charges_edit_success') :trans('master_messages.charges_add_success'));
                     return redirect()->route('get_charges_list');
                }
                else
                {
                    Session::flash('error', trans('master_messages.something_went_wrong'));
                    return redirect()->route('get_charges_list');
                }
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}