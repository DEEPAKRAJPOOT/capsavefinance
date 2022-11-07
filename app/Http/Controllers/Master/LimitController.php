<?php

namespace App\Http\Controllers\Master;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Http\Requests\Master\BankBaseRateRequest;
use Session;
use Auth;

class LimitController extends Controller
{
    public function __construct(InvMasterRepoInterface $master) {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }


    public function list() {
         
        $is_avail = $this->masterRepo->getavailFutureDate();
        return view('master.limit.limit_list',['is_avail'=>$is_avail]);
    }

    public function addLimit() {
        $limit_data = $this->masterRepo->findLastLimit();
        /*if($limit_data != false){
            dd(Carbon::now()->addDays(+1)->format('d/m/Y'));
            if(!empty($limit_data['end_date']))
              $lastStartDate = ($limit_data['end_date']) ? Carbon::parse($limit_data['end_date'])->addDays(+1)->format('d/m/Y') : '';
            else
              $lastStartDate = ($limit_data['start_date']) ? Carbon::parse($limit_data['start_date'])->addDays(+1)->format('d/m/Y') : '';
            
        }else{ }*/
        $lastStartDate = Carbon::now()->addDays(+1)->format('d/m/Y');
        $limit=1;
        if($limit_data == false){
          $limit=0;
          $lastStartDate = Carbon::now()->format('d/m/Y');
        }else{
            $lastStartDate = Carbon::parse($limit_data->end_date)->addDays(+1)->format('d/m/Y');
        }
            
       
        
        return view('master.limit.add_limit',['lastStartDate'=>$lastStartDate,'limit'=>$limit]);
    }


    public function saveLimit(Request $request) {
        try {
            $arrSaveData = $request->all();
            $status = false;
            $limit_id = false;
            $arrSaveData['single_limit'] =  $arrSaveData['single_limit'];
            $arrSaveData['multiple_limit'] = $arrSaveData['multiple_limit'];
            $updatedstatus = $this->masterRepo->updatePrevLimitStatus();
            if(!empty($request->get('id'))){
                $limit_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $limit_data = $this->masterRepo->findLimitById($limit_id);
                if(!empty($limit_data)) {
                    
                    $arrSaveData['start_date'] = ($request['start_date']) ? Carbon::createFromFormat('d/m/Y', $request['start_date'])->format('Y-m-d') : '';
                    $status = $this->masterRepo->updateLimit($arrSaveData, $limit_id);
                    if((int)$request['is_active']){
                        $e_date = Carbon::createFromFormat('d/m/Y', $request['start_date'])->addDays(-1)->format('Y-m-d');
                    }else{
                        $e_date = null;
                    }
                    $this->masterRepo->updateLimitEndDate($limit_id, $e_date);
                }
            }else{
                $e_date = Carbon::createFromFormat('d/m/Y', $request['start_date'])->addDays(-1)->format('Y-m-d');
                $arrSaveData['start_date'] = ($request['start_date']) ? Carbon::createFromFormat('d/m/Y', $request['start_date'])->format('Y-m-d') : '';
                $status = $this->masterRepo->saveLimit($arrSaveData); 
                $this->masterRepo->updateLimitEndDate($status->limit_id, $e_date);
            }
            if($status){
                Session::flash('message',trans('master_messages.borrower_limit_success'));
                return redirect()->route('get_borrower_limit');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_borrower_limit');
            }

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }



    public function editLimit(Request $request){
        $limit_id = preg_replace('#[^0-9]#', '', $request->get('limit_id'));
        $limitData = $this->masterRepo->findLimitById($limit_id);
        $limitData['start_date'] = $this->getFormatedDate($limitData->start_date);
        $limitData['end_date'] = $this->getFormatedDate(($limitData->end_date != null) ? $limitData->end_date : '');
    	return view('master.limit.edit_limit',['limitData' => $limitData]);
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
