<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use Session;
use Helpers;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Http\Requests\Lms\BankAccountRequest;

class CustomerController extends Controller {

    use ApplicationTrait;

    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $master;

    /**
     * The pdf instance.
     *
     * @var App\Libraries\Pdf
     */
    protected $pdf;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, MasterInterface $master)
    {
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->master = $master;
        $this->middleware('checkBackendLeadAccess');
    }

    /**
     * Display a listing of the customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return view('lms.customer.list');
    }

    /**
     * Display a listing of the customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function listAppliction(Request $request)
    {
        try {
            $totalLimit = 0;
            $consumeLimit = 0;
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            $application = $this->appRepo->getCustomerApplications($user_id);
            $anchor = $this->appRepo->getCustomerAnchors($user_id);

            foreach ($application as $key => $value) {
                $totalLimit += (isset($value->appLimit->tot_limit_amt)) ? $value->appLimit->tot_limit_amt : 0;
            }
            foreach ($application as $key => $value) {
                $consumeLimit += (isset($value->appPrgmOffer->loan_offer)) ? $value->appPrgmOffer->loan_offer : 0;
            }

            $userInfo->total_limit = number_format($totalLimit);
            $userInfo->consume_limit = number_format($consumeLimit);
            $userInfo->avail_limit = number_format($totalLimit - $consumeLimit);

            return view('lms.customer.list_applications')
                            ->with('userInfo', $userInfo)
                            ->with('application', $application);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     * Display a listing of the invoices.
     *
     * @return \Illuminate\Http\Response
     */
    public function listInvoice()
    {
        return view('lms.customer.list_invoices');
    }

    /**
     * bank account list
     * 
     * @return type mixed
     */
    public function bankAccountList(Request $request)
    {
        try {
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            return view('lms.customer.bank_account_list')->with(['userInfo' => $userInfo]);
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /**
     * add bank account
     * 
     * @param Request $request
     * @return type mixed
     */
    public function addBankAccount(Request $request)
    {
        try {
            $bank_list = ['' => 'Please Select'] + $this->master->getBankList()->toArray();
            return view('lms.customer.add_bank_account')->with(['bank_list' => $bank_list]);
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /**
     * Save bank account
     * 
     * @param Request $request
     * @return type mixed
     */
    public function saveBankAccount(BankAccountRequest $request)
    {
        try {
            $prepareData = [
                'acc_name' => $request->get('acc_name'),
                'acc_no' => $request->get('acc_no'),
                'bank_id' => $request->get('bank_id'),
                'ifsc_code' => $request->get('ifsc_code'),
                'branch_name' => $request->get('branch_name'),
                'is_active' => $request->get('is_active'),
                'user_id' => \auth()->user()->user_id
            ];

            $this->appRepo->UserBankAccount($prepareData);
            Session::flash('message', trans('success_messages.save_bank_account_successfully'));
            Session::flash('operation_status', 1);
            return redirect()->back();
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

}
