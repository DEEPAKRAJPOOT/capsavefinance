<?php

namespace App\Http\Controllers\Master;

use Auth;
use Session;
use Helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\BankAccountRequest;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;

class BankController extends Controller {

    public function __construct(InvMasterRepoInterface $master, InvAppRepoInterface $app_repo) {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
        $this->appRepo = $app_repo;
    }

    public function index(Request $request) {
        $bankList = $this->masterRepo->getAllBankList();
    //    dd('$bankList--', $bankList->get());
        return view('master.bank.index',['bankList' => $bankList]);
    }

    public function addBank(Request $request) {
        try {
            $bankData = [];
            $bank_acc_id = false;
            if (!empty($request->get('bank_id'))) {
                $bank_id = preg_replace('#[^0-9]#', '', $request->get('bank_id'));
                $bankData = $this->masterRepo->getBankById($bank_id);
            }

            $bank_list = [''=>'Please Select']+$this->masterRepo->getBankList()->toArray();
            return view('master.bank.add_bank')
                            ->with(['bank_list' => $bank_list, 'bankData' => $bankData]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    public function saveNewBank(Request $request) {
        try {
            $bank_id = ($request->get('bank_id')) ? \Crypt::decrypt($request->get('bank_id')) : null;
            
            $prepareData = [
                'bank_name' => $request->get('bank_name'),
                'perfios_bank_id' => $request->get('perfios_bank_id'),
                'is_active' => $request->get('is_active')
            ];
            $this->masterRepo->saveBank($prepareData, $bank_id);
            $messges = $bank_id ? trans('success_messages.update_bank_successfully') : trans('success_messages.save_bank_successfully');
            Session::flash('message', $messges);
            Session::flash('operation_status', 1);
            return redirect()->back();
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

}