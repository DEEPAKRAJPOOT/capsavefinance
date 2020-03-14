<?php

namespace App\Http\Controllers\Master;

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

    public function editBaseRate(Request $request) {
        $baserate_id = preg_replace('#[^0-9]#', '', $request->get('id'));
        $baserate_data = $this->masterRepo->findBaseRateById($baserate_id);
        $bank_list = $this->masterRepo->getBankList()->toArray();
//        dd($baserate_data);
        return view('master.baserates.edit_baserate', ['baserate_data' => $baserate_data,'bank_list' => $bank_list]);
    }

    public function saveBaseRate(BankBaseRateRequest $request) {
        try {
            $filter['filter_search_keyword'] = $request->filter_search_keyword;
            $validatedData = $request->all();
            
            $status = false;
            $baserate_id = false;
            if (!empty($request->get('id'))) {
                $baserate_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $baserate_data = $this->masterRepo->findBaseRateById($baserate_id);
                if (!empty($baserate_data)) {
                    $validatedData['updated_by'] = Auth::user()->user_id;
                    $status = $this->masterRepo->updateBaseRate($validatedData, $baserate_id);
                }
            } else {
                $validatedData['created_by'] = Auth::user()->user_id;
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
