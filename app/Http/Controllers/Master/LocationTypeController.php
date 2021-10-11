<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
 
class LocationTypeController extends Controller {

    public function __construct(InvMasterRepoInterface $master){
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }


     public function index(){
        return view('master.locationtype.index');
    }

    public function addLocationType(){
    	return view('master.locationtype.add_locationtype');
    }

    public function editLocationType(Request $request){
        $locationId = preg_replace('#[^0-9]#', '', $request->get('location_id'));
        $location_data = $this->masterRepo->findLocationById($locationId);
    	return view('master.locationtype.edit_locationtype',['location_data' => $location_data]);
    }


    public function saveLocationType(Request $request) {
        try {
            $arrLocationTypeData = $request->all();
            $status = false;
            $locationId = false;
            if(!empty($request->get('location_id'))){
                $locationId = preg_replace('#[^0-9]#', '', $request->get('location_id'));
                $location_data = $this->masterRepo->findLocationById($locationId);
                if (!empty($location_data)) {
                    $arrLocationTypeData['updated_by'] = Auth::user()->user_id;
                    $status = $this->masterRepo->updateLocationType($arrLocationTypeData, $locationId);
                }
            }else{
               $arrLocationTypeData['created_by'] = Auth::user()->user_id;
               $status = $this->masterRepo->saveLocationType($arrLocationTypeData); 
            }
            if($status){
                Session::flash('message', $locationId ? trans('master_messages.location_edit_success') :trans('master_messages.location_add_success'));
                return redirect()->route('list_location_type');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('list_location_type');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}