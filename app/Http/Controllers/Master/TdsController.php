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
                    $tds_data['start_date'] = ($request['start_date']) ? Carbon::createFromFormat('d/m/Y', $request['start_date'])->format('Y-m-d') : '';
                    //$tds_data['end_date'] = ($request['end_date']) ? Carbon::createFromFormat('d/m/Y', $request['end_date'])->format('Y-m-d') : null;
                    $tds_data['updated_by'] = Auth::user()->user_id;
                    $status = $this->masterRepo->updateTds($tdsData, $tds_id);
                }
            } else {
                $e_date = Carbon::createFromFormat('d/m/Y', $request['start_date'])->addDays(-1)->format('Y-m-d');
                $tdsData['start_date'] = ($request['start_date']) ? Carbon::createFromFormat('d/m/Y', $request['start_date'])->format('Y-m-d') : '';
                //$tdsData['end_date'] = ($request['end_date']) ? Carbon::createFromFormat('d/m/Y', $request['end_date'])->format('Y-m-d') : null;
                $tdsData['created_by'] = Auth::user()->user_id;
                $tdsData['created_at'] = \carbon\Carbon::now();
                $status = $this->masterRepo->saveTds($tdsData); 
                $this->masterRepo->updateTdsEndDate($status->id, $e_date);
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
        $tds_data['start_date'] = $this->getFormatedDate($tds_data->start_date);
        $tds_data['end_date'] = $this->getFormatedDate(($tds_data->end_date != null) ? $tds_data->end_date : '');
    	return view('master.tds.edit_tds',['tds_data' => $tds_data]);
    }

    public function getFormatedDate($strDate) {
        if(!empty($strDate)){
            $arr = explode(" ", $strDate);
            $formated_date = explode("/", str_replace('-', '/', $arr[0]));
            $new_format = $formated_date[2] . '/' . $formated_date[1] . '/' . $formated_date[0];
            return $new_format;
        }
    }

}
