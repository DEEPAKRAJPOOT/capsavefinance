<?php

namespace App\Http\Controllers\Master;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Http\Requests\Master\BankBaseRateRequest;
use Session;
use Auth;

class TdsController extends Controller {

    public function __construct(InvMasterRepoInterface $master) {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }

    public function list() {
        return view('master.tds.tds_list');
    }


    public function addTds() {
        return view('master.tds.add_tds');
    }

    public function saveTds(Request $request) {
        try {
            $tdsData = $request->all();
            $status = false;
            $tds_id = false;
            if(!empty($request->get('id'))) {
                $tds_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $tds_data = $this->masterRepo->findTDSById($tds_id);
                if(!empty($tds_data)) {
                    $status = $this->masterRepo->updateTds($tdsData, $tds_id);
                }
            } else {
                $tdsData['created_at'] = \carbon\Carbon::now();
                $status = $this->masterRepo->saveTds($tdsData); 
            }
            if($status){
                Session::flash('message', $tds_id ? trans('master_messages.tds_edit_success') :trans('master_messages.tds_add_success'));
                return redirect()->route('get_tds_list');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_tds_list');
            }

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function editTds(Request $request){
        $tds_id = preg_replace('#[^0-9]#', '', $request->get('id'));
        $tds_data = $this->masterRepo->findTDSById($tds_id);
    	return view('master.tds.edit_tds',['tds_data' => $tds_data]);
    }
}
