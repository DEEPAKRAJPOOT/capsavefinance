<?php

namespace App\Http\Controllers\Master;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Http\Requests\Master\BankBaseRateRequest;
use Session;
use Auth;

class BaseRateController extends Controller {

    public function __construct(InvMasterRepoInterface $master) {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }

    public function index(Request $request) {

        $filter['filter_search_keyword'] = $request->filter_search_keyword;

        return view('master.baserates.index', ['filter' => $filter]);
    }

    public function addBaseRate() {

        $bank_list = $this->masterRepo->getBankList()->toArray();
//        dd($bank_list);
        return view('master.baserates.add_baserate')
                        ->with(['bank_list' => $bank_list]);
    }

    public function getFormatedDate($strDate) {
//        dd($strDate);
        if(!empty($strDate)){
            $arr = explode(" ", $strDate);
            $formated_date = explode("/", str_replace('-', '/', $arr[0]));
            $new_format = $formated_date[2] . '/' . $formated_date[1] . '/' . $formated_date[0];
            return $new_format;
        }
    }

    public function editBaseRate(Request $request) {
        $baserate_id = preg_replace('#[^0-9]#', '', $request->get('id'));
        $baserate_data = $this->masterRepo->findBaseRateById($baserate_id);
        $bank_list = $this->masterRepo->getBankList()->toArray();
        $baserate_data['start_date'] = $this->getFormatedDate($baserate_data->start_date);
        $baserate_data['end_date'] = $this->getFormatedDate(($baserate_data->end_date != null) ? $baserate_data->end_date : '');
        return view('master.baserates.edit_baserate', ['baserate_data' => $baserate_data, 'bank_list' => $bank_list]);
    }

    public function saveBaseRate(BankBaseRateRequest $request) {
        try {
            $filter['filter_search_keyword'] = $request->filter_search_keyword;
            $validatedData = $request->all();
//            dd($request->all());
            $status = false;
            $baserate_id = false;
            
            if($request->get('is_default') == 1){
                $bankId = (int)$request->get('bank_id');
                $isDefault = (int)$request->get('is_default');
                $data = $this->masterRepo->checkIsDefaultBaseRate($bankId,$isDefault);
                $baseRateData = $data ? $data->toArray() : '';
//                dd($baseRateData);
                if (!empty($baseRateData)) {
                    $baseRateId = $baseRateData['id'];
                    $baseRateData['is_default'] = 0;
//                    dd($regComData);
                    $this->masterRepo->updateBaseRate($baseRateData, $baseRateId);
                }
            }
            
            if (!empty($request->get('id'))) {
                $baserate_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $baserate_data = $this->masterRepo->findBaseRateById($baserate_id);
                if (!empty($baserate_data)) {
                    $validatedData['start_date'] = ($request['start_date']) ? Carbon::createFromFormat('d/m/Y', $request['start_date'])->format('Y-m-d') : '';
                    $validatedData['end_date'] = ($request['end_date']) ? Carbon::createFromFormat('d/m/Y', $request['end_date'])->format('Y-m-d') : null;
                    $validatedData['updated_by'] = Auth::user()->user_id;
                    $status = $this->masterRepo->updateBaseRate($validatedData, $baserate_id);
                }
            } else {
                $validatedData['start_date'] = ($request['start_date']) ? Carbon::createFromFormat('d/m/Y', $request['start_date'])->format('Y-m-d') : '';
                $validatedData['end_date'] = ($request['end_date']) ? Carbon::createFromFormat('d/m/Y', $request['end_date'])->format('Y-m-d') : null;
                $validatedData['created_by'] = Auth::user()->user_id;
//                dd($validatedData);
                $status = $this->masterRepo->saveBaseRate($validatedData);
            }
            if ($status) {
                Session::flash('message', $baserate_id ? trans('master_messages.base_rate_update_success') : trans('master_messages.base_rate_add_success'));
                return redirect()->route('get_baserate_list', ['filter_search_keyword' => $filter['filter_search_keyword']]);
            } else {
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_baserate_list', ['filter' => $filter]);
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

}
