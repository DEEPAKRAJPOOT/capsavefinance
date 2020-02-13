<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
 
 
class EquipmentController extends Controller {

    public function __construct(InvMasterRepoInterface $master){
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }


     public function index(){
        return view('master.equipment.index');
    }

    public function addEquipment(){
           return view('master.equipment.add_equipment');
        
    }

    public function saveEquipment(Request $request) {
        try {
            $arrEquipmentData = $request->all();
            $status = false;
            $equipment_id = false;
            if(!empty($request->get('id'))){
                $equipment_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $equipment_data = $this->masterRepo->findEquipmentsById($equipment_id);
                if(!empty($equipment_data)) {
                    $status = $this->masterRepo->updateEquipment($arrEquipmentData, $equipment_id);
                }
            }else{
                $arrEquipmentData['created_at'] = \carbon\Carbon::now();
                $status = $this->masterRepo->saveEquipment($arrEquipmentData); 
            }
            if($status){
                Session::flash('message', $equipment_id ? trans('master_messages.equipment_edit_success') :trans('master_messages.equipment_add_success'));
                return redirect()->route('get_equipment_list');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_equipment_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function editEquipment(Request $request){
        $equipment_id = preg_replace('#[^0-9]#', '', $request->get('id'));
        $equipment_data = $this->masterRepo->findEquipmentsById($equipment_id);
    	return view('master.equipment.edit_equipment',['equipment_data' => $equipment_data]);
    }

 
}