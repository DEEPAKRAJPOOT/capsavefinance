<?php
namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\BusinessInformationRequest;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Models\BizApi;
use Session;
use Helpers;
use Datetime;
use DB;
use Intervention\Image\File;
use App\Libraries\Pdf;
use Carbon\Carbon;
use PHPExcel; 
use PHPExcel_IOFactory;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\Traits\InvoiceTrait;
use App\Libraries\Idfc_lib;
use App\Helpers\ManualApportionmentHelper;
use Event;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;
use Illuminate\Support\Facades\App;
use App\Inv\Repositories\Models\Lms\Transactions;

class InvoiceController extends Controller {

    use ApplicationTrait;
    use LmsTrait;
    use ActivityLogTrait;

    protected $appRepo;
    protected $invRepo;
    protected $docRepo;
    protected $lmsRepo;
    protected $userRepo;
    protected $application;
    protected $master;

    public function __construct(InvAppRepoInterface $app_repo, InvAppRepoInterface $application, InvoiceInterface $invRepo, InvUserRepoInterface $user_repo,InvDocumentRepoInterface $docRepo, InvLmsRepoInterface $lms_repo, MasterInterface $master) {
        $this->appRepo = $app_repo;
        $this->invRepo = $invRepo;
        $this->docRepo = $docRepo;
        $this->lmsRepo = $lms_repo;
        $this->userRepo = $user_repo;
        $this->application  =  $application;
        $this->master = $master;
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->middleware('checkEodProcess');
    }

    /* Invoice upload page  */

    public function getInvoice(Request $request) {
        $anchor_id = $request->anchor_id;
        $user_id = $request->user_id;
        $app_id = $request->app_id;
        $biz_id = $request->biz_id;
        $get_user = $this->invRepo->getUser($user_id);
        $get_anchor = $this->invRepo->getAnchor($anchor_id);
        $get_program = $this->invRepo->getProgram($anchor_id);
        return view('backend.application.invoice.uploadinvoice')
                        ->with(['get_user' => $get_user, 'get_anchor' => $get_anchor, 'get_program' => $get_program, 'app_id' => $app_id, 'biz_id' => $biz_id]);
    }

    public function getAllInvoice() {
        $get_anchor = $this->invRepo->getLmsLimitAllAnchor();
        // dd($get_anchor);
        $id = Auth::user()->user_id;
        $res =  $this->userRepo->getUserDetail($id);
        $aid    =  $res->anchor_id;
        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
        $get_program = $this->invRepo->getLimitProgram($aid);
        $get_supplier = [];
        foreach($get_program as $v){
            $program_id = $v->program->prgm_id;
            $supplierData = $this->invRepo->getProgramOfferByPrgmId($program_id);
            foreach ($supplierData as $v1){
                $get_supplierD['user_id'] = $v1->user_id;
                $get_supplierD['app_id'] = $v1->app_id;
                $get_supplierD['prgm_offer_id'] = $v1->prgm_offer_id;
                $get_supplierD['biz_entity_name'] = $v1->biz_entity_name;
                $get_supplierD['customer_id'] = $v1->customer_id;
                $get_supplier[$program_id][] = $get_supplierD; 
            }
        }
        $get_program_limit = $this->invRepo->geAnchortLimitProgram($aid);
        return view('backend.invoice.upload_all_invoice')
                        ->with(['get_anchor' => $get_anchor,'anchor' => $chkUser->id,'id' =>  $aid,'limit' => $get_program_limit,'get_program' =>$get_program,'get_supplier'=>$get_supplier ]);
    }

    public function viewInvoice(Request $req) {
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        $userInfo = $this->invRepo->getCustomerDetail($user_id);
        $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(7);
        $get_bus = $this->invRepo->getBusinessNameApp(7);
        return view('backend.invoice.invoice')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo]);
    }
    
    public function UserWiseInvoice(Request $req) {
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        $totalLimit = 0;
        $totalCunsumeLimit = 0;
        $consumeLimit = 0;
        $transactions = 0;
        $userInfo = $this->invRepo->getCustomerDetail($user_id);
        $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(7);
        $get_bus = $this->invRepo->getBusinessNameApp(7);
        $status =  DB::table('mst_status')->where(['status_type' =>4])->get();
        $application = $this->appRepo->getCustomerApplications($user_id);
        $anchors = $this->appRepo->getCustomerPrgmAnchors($user_id);

        foreach ($application as $key => $app) {
            if (isset($app->prgmLimits)) {
                foreach ($app->prgmLimits as $value) {
                    $totalLimit += $value->limit_amt;
                }
            }
            if (isset($app->acceptedOffers)) {
                foreach ($app->acceptedOffers as $value) {
                    $totalCunsumeLimit += $value->prgm_limit_amt;
                }
            }
        }
        $userInfo->total_limit = number_format($totalLimit);
        $userInfo->consume_limit = number_format($totalCunsumeLimit);
        $userInfo->utilize_limit = number_format($totalLimit - $totalCunsumeLimit);
        $userInfo->outstandingAmt = number_format($this->lmsRepo->getUnsettledTrans($user_id, ['trans_type_not_in' => [config('lms.TRANS_TYPE.MARGIN'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')] ])->sum('outstanding'),2);
        $userInfo->marginOutstandingAmt = number_format($this->lmsRepo->getUnsettledTrans($user_id, ['trans_type_in' => [config('lms.TRANS_TYPE.MARGIN')] ])->sum('outstanding'),2);
        $userInfo->nonfactoredOutstandingAmt = number_format($this->lmsRepo->getUnsettledTrans($user_id, ['trans_type_in' => [config('lms.TRANS_TYPE.NON_FACTORED_AMT')] ])->sum('outstanding'),2);
        $userInfo->unsettledPaymentAmt = number_format($this->lmsRepo->getUnsettledPayments($user_id)->sum('amount'),2);
        return view('backend.invoice.user_wise_invoice')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo,'status' =>$status]);
    } 

    public function getBulkInvoice() {

        $getAllInvoice = $this->invRepo->getLmsLimitAllAnchor();
        $get_bus = $this->invRepo->getBusinessName();
        $id = Auth::user()->user_id;
        $res =  $this->userRepo->getUserDetail($id);
        $aid    =  $res->anchor_id;
        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
        $get_program = $this->invRepo->getLimitProgram($aid);
        $get_program_limit = $this->invRepo->geAnchortLimitProgram($aid);
        $getBulkInvoice = $this->invRepo->getAllBulkInvoice();
        return view('backend.invoice.bulk_invoice')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice,'anchor' => $chkUser->id,'id' =>  $aid,'limit' => $get_program_limit,'get_program' =>$get_program,'getBulkInvoice' =>$getBulkInvoice]);
    }

    public function viewApproveInvoice(Request $req) {
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        $userInfo = $this->invRepo->getCustomerDetail($user_id);
        $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(8);
        $get_bus = $this->invRepo->getBusinessNameApp(8);
        $id = Auth::user()->user_id;
        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
        return view('backend.invoice.approve_invoice')->with(['role' =>$chkUser->id,'get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo]);
    }

    public function viewDisbursedInvoice(Request $req) {
       
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        // $userInfo = $this->invRepo->getCustomerDetail($user_id);
        // $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(9);
        // $get_bus = $this->invRepo->getBusinessNameApp(9);
        $id = Auth::user()->user_id;
        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
        return view('backend.invoice.disbursed_invoice')->with([
            'role' =>$chkUser->id,
            // 'get_bus' => $get_bus, 
            // 'anchor_list' => $getAllInvoice, 
            'flag' => $flag, 
            'user_id' => $user_id, 
            'app_id' => $app_id, 
            // 'userInfo' => $userInfo
            ]);
    }

    public function viewRepaidInvoice(Request $req) {
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        $userInfo = $this->invRepo->getCustomerDetail($user_id);
        $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(13);
        $get_bus = $this->invRepo->getBusinessNameApp(13);
        return view('backend.invoice.repaid_invoice')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo]);
    }

    public function viewSentToBankInvoice(Request $req) {
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        $userInfo = $this->invRepo->getCustomerDetail($user_id);
        $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(10);
        $get_bus = $this->invRepo->getBusinessNameApp(10);
        $batchData = $this->lmsRepo->getallBatch();

        return view('backend.invoice.sent_to_bank')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo, 'batchData' => $batchData]);
    }

    public function viewBankInvoice(Request $req) {
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        return view('backend.invoice.bank_invoice')->with(['user_id' => $user_id, 'app_id' => $app_id]);
    }

    public function viewBankInvoiceCustomers(Request $req) {
        $batch_id = $req->get('batch_id') ?: null;
        return view('backend.invoice.bank_invoice_customers')->with(['batch_id' => $batch_id]);
    }

    public function viewDisburseInvoice(Request $req) {
        $batch_id = $req->get('batch_id') ?: null;
        $disbursed_user_id = $req->get('disbursed_user_id') ?: null;
        return view('backend.invoice.view_disburse_invoice')->with(['batch_id' => $batch_id, 'disbursed_user_id' => $disbursed_user_id]);
    }

    public function viewfailedDisbursment(Request $req) {
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        $userInfo = $this->invRepo->getCustomerDetail($user_id);
        $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(11);
        $get_bus = $this->invRepo->getBusinessNameApp(11);
        return view('backend.invoice.failed_disbursment')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo ]);
    }

    public function viewdisbursed(Request $req) {
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        $userInfo = $this->invRepo->getCustomerDetail($user_id);
        $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(12);
        $get_bus = $this->invRepo->getBusinessNameApp(12);
        return view('backend.invoice.disbursment')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo]);
    }

    public function viewRejectInvoice(Request $req) {
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        $userInfo = $this->invRepo->getCustomerDetail($user_id);
        $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(14);
        $get_bus = $this->invRepo->getBusinessNameApp(14);
        return view('backend.invoice.reject_invoice')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo]);
    }

    public function exceptionCases(Request $req) {
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        $userInfo = $this->invRepo->getCustomerDetail($user_id);
        $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(28);
        $get_bus = $this->invRepo->getBusinessNameApp(28);
        return view('backend.invoice.exception_cases')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo]);
    }

    /* get suplier & program b behalf of anchor id */

    public function getProgramSupplier(Request $request) {
        $attributes = $request->all();
        $get_user = $this->invRepo->getUserBehalfApplication($attributes);
        return response()->json(['status' => 1, 'userList' => $get_user]);
    }

    /* failed invoice status iframe    */

    public function invoiceFailedStatus(Request $request) {
        dd($request->invoice_id);
        return view('backend.invoice.invoice_failed_status');
    }

    /* success invoice status iframe    */

    public function invoiceSuccessStatus(Request $request) {
        $result = $this->invRepo->getDisbursedAmount($request->get('invoice_id'));
        return view('backend.invoice.invoice_success_status')->with(['result' => $result]);
    }

    /* success invoice status iframe    */

    public function viewInvoiceDetails(Request $request) {
        $invoice_id = $request->get('invoice_id');
        $res = $this->invRepo->getSingleInvoice($invoice_id);
        $get_status = DB::table('mst_status')->where('status_type', 4)->get();
        $id = Auth::user()->user_id;
        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
        return view('backend.invoice.view_invoice_details')->with(['invoice' => $res, 'status' => $get_status,'role'=> $chkUser->id]);
    }

    public function viewBatchUserInvoice(Request $request) {
        $userId = $request->get('user_id');
        $batchId = $request->get('disbursal_batch_id');

        $invoiceData = $this->lmsRepo->getAllUserBatchInvoice(['user_id' => $userId, 'disbursal_batch_id' => $batchId]);
        // dd($invoiceData);
        return view('backend.invoice.view_batch_user_invoice')
                ->with(
                    ['user_id' => $userId, 
                    'disbursal_batch_id' => $batchId,
                    'userIvoices' => $invoiceData
                ]);
    }

    public function invoiceUpdateDisbursal(Request $request) {
        $userId = $request->get('user_id');
        $batchId = $request->get('disbursal_batch_id');

        $disbursal = $this->lmsRepo->getDisbursalByUserAndBatchId(['user_id' => $userId, 'disbursal_batch_id' => $batchId]);
        // dd($disbursal);
        return view('backend.invoice.update_invoice_disbursal')
                ->with(
                    ['user_id' => $userId, 
                    'disbursal_batch_id' => $batchId,
                    'disbursal' => $disbursal
                ]);
    }

    public function updateDisburseInvoice(Request $request) {
        try {
            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            $userId = $request->user_id;
            $disbursalBatchId = $request->disbursal_batch_id;
            $transId = $request->trans_id;
            $fundedDate = $request->funded_date;
            $remarks = $request->remarks;
            $createdBy = Auth::user()->user_id;

            $invoiceIds = $this->lmsRepo->findInvoicesByUserAndBatchId(['user_id' => $userId, 'disbursal_batch_id' => $disbursalBatchId])->toArray();
            $disbursalIds = $this->lmsRepo->findDisbursalByUserAndBatchId(['user_id' => $userId, 'disbursal_batch_id' => $disbursalBatchId])->toArray();
            if (!isset($disbursalIds) || empty($disbursalIds)) {
                    return redirect()->route('backend_get_sent_to_bank')->withErrors('Something went wrong please try again.');
            }

            if ($disbursalIds) {
                $updateDisbursal = $this->lmsRepo->updateDisburseByUserAndBatch([
                        'tran_id' => $transId,
                        'status_id' => 12,
                        'funded_date' => (!empty($fundedDate)) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$fundedDate))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s')
                    ], $disbursalIds);
                foreach ($disbursalIds as $key => $value) {
                    $this->lmsRepo->createDisbursalStatusLog($value, 12, $remarks, $createdBy);
                }
            }            
            if ($invoiceIds) {
                $updateInvoiceStatus = $this->lmsRepo->updateInvoicesStatus($invoiceIds, 12);
                foreach ($invoiceIds as $key => $value) {
                    $this->invRepo->saveInvoiceStatusLog($value, 12);
                }
            }
            $updateTransaction = $this->updateTransactionInvoiceDisbursed($disbursalIds, $fundedDate);

            $whereActivi['activity_code'] = 'updateDisburseInvoice';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Update Disburse Invoice, Send To Bank (Manage Invoice)';
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
            }            
            
            Session::flash('message',trans('backend_messages.disburseMarked'));
            return redirect()->route('backend_get_sent_to_bank');

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    public function updateTransactionInvoiceDisbursed($disbursalIds, $fundedDate) {
        $invoiceDisbursed = $this->lmsRepo->getInvoiceDisbursed($disbursalIds)->toArray();
        $selectDate = (!empty($fundedDate)) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$fundedDate))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $Obj = new ManualApportionmentHelper($this->lmsRepo);
        $invDisb = [];
        foreach ($invoiceDisbursed as $key => $value) {            
            $invDisb[$value['disbursal']['user_id']][$value['invoice_disbursed_id']] = $value['invoice_disbursed_id'];           
            $tenor = $value['tenor_days'];
            $banchMarkDateFlag = $value['invoice']['program_offer']['benchmark_date'];

            $updateInvoiceDisbursed = $this->lmsRepo->updateInvoiceDisbursed([
                        'payment_due_date' => date('Y-m-d', strtotime(str_replace('/','-',$fundedDate). "+ $tenor Days")),                        
                        // 'payment_due_date' => ($banchMarkDateFlag == 2) ? date('Y-m-d', strtotime(str_replace('/','-',$fundedDate). "+ $tenor Days")) : date('Y-m-d', strtotime($value['invoice']['invoice_date']. "+ $tenor Days")),                        
                        'status_id' => config('lms.DISBURSAL_STATUS')['DISBURSED'],
                        'int_accrual_start_dt' => $selectDate,
                        'updated_by' => Auth::user()->user_id ?? 0,
                        'updated_at' => $curData
                    ], $value['invoice_disbursed_id']);

            $interest= 0;
            $margin= 0;

            // $tenor = $this->calculateTenorDays($value['invoice']);
            $margin = $this->calMargin($value['invoice']['invoice_approve_amount'], $value['margin']);
            $fundedAmount = $value['invoice']['invoice_approve_amount'] - $margin;
            $tInterest = $this->calInterest($fundedAmount, (float)$value['interest_rate'], $tenor);

            if($value['invoice']['program_offer']['payment_frequency'] == 1) {
                $interest = $tInterest;
            }
            $intrstAmt = round($interest, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
            $marginAmt = round($margin, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
            $disbAmt = round($value['disburse_amt'], config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);

            /* Sudesh: Code to save Invoice Disbursed details : S */
            $invoiceDetails = [
                'invoice_id' => $value['invoice']['invoice_id'],
                'invoice_disbursed_id' => $value['invoice_disbursed_id'],
                'request_amount' => $value['invoice']['invoice_amount'],
                'approve_amount' => $value['invoice']['invoice_approve_amount'],
                'upfront_interest' => $intrstAmt,
                'disbursed_amount' => ($value['invoice']['program_offer']['payment_frequency'] == 1 && $value['invoice']['program_offer']['program']['interest_borne_by'] == 2)? ($value['invoice']['invoice_approve_amount'] - $marginAmt - $intrstAmt): ($value['invoice']['invoice_approve_amount'] - $marginAmt),
                'final_disbursed_amount' => $value['disburse_amt'],
                'invoice_date' => $value['invoice']['invoice_date'],
                'funded_date' => $value['disbursal']['funded_date'],
                'payment_due_date' => $value['payment_due_date'],
                'interest_start_date' => $value['int_accrual_start_dt'],
                'tenor' => $value['tenor_days'],
                'grace_period' => $value['grace_period'],
                'interest_born_by' => $value['invoice']['program_offer']['program']['interest_borne_by']?? null,
                'payment_frequency' => $value['invoice']['program_offer']['payment_frequency'],
                'interest_rate' => $value['interest_rate'],
                'overdue_rate' => $value['overdue_interest_rate'],
                'limit_used' => ($value['is_adhoc']==0)?$value['invoice']['invoice_approve_amount']:0,
                'adhoc_limit_used' => ($value['is_adhoc']==1)?$value['invoice']['invoice_approve_amount']:0
            ];
            $whereInvoiceDetails = [
                'invoice_id' => $value['invoice']['invoice_id'],
                'invoice_disbursed_id' => $value['invoice_disbursed_id']];


            $this->lmsRepo->saveInvoiceDisbursedDetails($invoiceDetails,$whereInvoiceDetails);
            unset($invoiceDetails);
            unset($whereInvoiceDetails);
            /* Sudesh: Code to save Invoice Disbursed details : E */

            if($disbAmt > 0.00){
                $transactionData = $this->createTransactionData($value['disbursal']['user_id'], ['amount' => $value['disburse_amt'], 'trans_date' => $fundedDate, 'invoice_disbursed_id' => $value['invoice_disbursed_id']], config('lms.TRANS_TYPE.PAYMENT_DISBURSED'));
                $createTransaction = $this->lmsRepo->saveTransaction($transactionData);   
            }

            if ($intrstAmt > 0.00) {
                $intrstDbtTrnsData = $this->createTransactionData($value['disbursal']['user_id'], ['amount' => $intrstAmt, 'trans_date' => $fundedDate, 'invoice_disbursed_id' => $value['invoice_disbursed_id']], config('lms.TRANS_TYPE.INTEREST'));
                $createTransaction = $this->lmsRepo->saveTransaction($intrstDbtTrnsData);

                if ($value['invoice']['program_offer']['program']['interest_borne_by'] == 2) {
                    $intrstCdtTrnsData = $this->createTransactionData($value['disbursal']['user_id'], ['parent_trans_id' => $createTransaction->trans_id, 'link_trans_id' => $createTransaction->trans_id, 'amount' => $intrstAmt, 'trans_date' => $fundedDate, 'invoice_disbursed_id' => $value['invoice_disbursed_id']], config('lms.TRANS_TYPE.INTEREST'), 1);
                    $createTransaction = $this->lmsRepo->saveTransaction($intrstCdtTrnsData);
                }
            }

            if ($value['processing_fee'] > 0.00) {
                $transData['amount'] = $value['processing_fee'];
                $chrgData = $this->appRepo->getInvoiceProcessingFeeCharge();
                $getPercentage  = $this->lmsRepo->getLastGSTRecord();

                if($getPercentage)
                {
                    $tax_value  = $getPercentage['tax_value'];
                    $chid  = $getPercentage['tax_id'];
                }
                else
                {
                    $tax_value  =0; 
                    $chid  = 0;
                }
                $fWGst = $value['processing_fee_gst'];
                
                $transData['invoice_disbursed_id'] = $value['invoice_disbursed_id'];
                $transData['trans_mode']  = 1;
                $transData['trans_date'] = $fundedDate;
                if($chrgData->is_gst_applicable == 1) {
                    $transData['amount'] += $fWGst;
                    $transData['base_amt'] = $value['processing_fee'];
                    $transData['chrg_gst_id']  = $chid;
                    $transData['gst_amt']  = $tax_value;
                    $transData['gst'] = 1;
                }
                $intrstDbtTrnsData = $this->createTransactionData($value['disbursal']['user_id'], $transData, config('lms.TRANS_TYPE.INVOICE_PROCESSING_FEE'));
                $createTransaction = $this->lmsRepo->saveTransaction($intrstDbtTrnsData);

                $transData['parent_trans_id'] = $createTransaction->trans_id;
                $transData['link_trans_id'] = $createTransaction->trans_id;
                $intrstCdtTrnsData = $this->createTransactionData($value['disbursal']['user_id'], $transData, config('lms.TRANS_TYPE.INVOICE_PROCESSING_FEE'), 1);
                $createTransaction = $this->lmsRepo->saveTransaction($intrstCdtTrnsData);
            }

            // Margin transaction $tranType = 10
            $marginAmt = round($margin, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
            if ($marginAmt > 0.00) {
                $marginTrnsData = $this->createTransactionData($value['disbursal']['user_id'], ['amount' => $marginAmt, 'trans_date' => $fundedDate, 'invoice_disbursed_id' => $value['invoice_disbursed_id']], config('lms.TRANS_TYPE.MARGIN'), 0);
                $createTransaction = $this->lmsRepo->saveTransaction($marginTrnsData);
            }
           
            $Obj->intAccrual($value['invoice_disbursed_id']);

        }
        foreach($invDisb as $userId => $invDisb){
            $invDisbIds = array_keys($invDisb);

            $intList = Transactions::whereIn('invoice_disbursed_id',$invDisbIds)
            ->whereIn('trans_type', [config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])
            ->where('user_id',$userId)
            ->where('entry_type','0')
            ->where('is_invoice_generated','0')
            ->pluck('trans_id')
            ->toArray();
            
            $chrgList = Transactions::whereIn('invoice_disbursed_id',$invDisbIds)
            ->whereHas('transType', function($query){ $query->where('chrg_master_id','!=','0'); })
            ->where('user_id',$userId)
            ->where('entry_type',0)
            ->where('is_invoice_generated',0)
            ->pluck('trans_id')
            ->toArray();
            
            if(!empty($intList)){
                $controller = app()->make('App\Http\Controllers\Lms\userInvoiceController');
                $controller->generateCapsaveInvoice($intList, $userId, 'I');
            }
            
            if(!empty($chrgList)){
                $controller = app()->make('App\Http\Controllers\Lms\userInvoiceController');
                $controller->generateCapsaveInvoice($chrgList, $userId, 'C');
            }
        }

        $disbursals = $this->lmsRepo->getDisbursals($disbursalIds)->toArray();
        foreach ($disbursals as $key => $value) {
        if($value['lms_user']['user']['is_buyer'] == 2) {
            $benifiName = isset($value['lms_user']['user']['anchor_bank_details']['acc_name']) ? $value['lms_user']['user']['anchor_bank_details']['acc_name'] : '';
        } else {
            $benifiName = isset($value['user']['biz']['biz_entity_name']) ? $value['user']['biz']['biz_entity_name'] : '';
        }            
        $userMailArr['receiver_user_name'] = $name = isset($value['user']['email']) ?  $value['user']['biz']['biz_entity_name'] : $value['user']['biz']['biz_entity_name'].',';
        $userMailArr['amount'] = number_format($value['disburse_amount'],2);
        $userMailArr['receiver_email'] = isset($value['user']['email']) ? $value['user']['email'] : $value['user']['anchor']['comp_email'];
        $fullName = $value['user']['biz']['biz_entity_name'];
        $userMailArr['user_id'] = \Helpers::formatIdWithPrefix($value['user_id'], 'CUSTID').'-'.$fullName;
        $userMailArr['id'] = \Helpers::formatIdWithPrefix($value['user_id'], 'CUSTID');
        $userMailArr['app_id'] = \Helpers::formatIdWithPrefix($value['user_id'], 'APP');
        $userMailArr['utr_no'] = isset($value['tran_id']) ? $value['tran_id'] : '';
        // $userMailArr['benefi_name'] = $benifiName;
        $userMailArr['disbursed_date'] = isset($value['disburse_date']) ? Carbon::parse($value['disburse_date'])->format('d-m-Y') : '';  
        $userMailArr['anchor_email'] = isset($value['user']['anchor']) && isset($value['user']['anchor']['comp_email']) ? $value['user']['anchor']['comp_email'] : null;
        $userMailArr['sales_email'] = isset($value['user']['anchor']) && isset($value['user']['anchor']['sales_user']) ? $value['user']['anchor']['sales_user']['email'] : null;
        $userMailArr['auth_email'] = \Auth::user() ? \Auth::user()->email : null; 
        Event::dispatch("LMS_USER_DISBURSAL", serialize($userMailArr));

        // $userMailArr['receiver_user_name'] = $name = $value['user']['anchor']['comp_name'];
        // $userMailArr['amount'] = $value['disburse_amount'];
        // $userMailArr['receiver_email'] = $value['user']['anchor']['comp_email'];
        // $userMailArr['user_id'] = \Helpers::formatIdWithPrefix($value['user_id'], 'CUSTID');
        // $userMailArr['app_id'] = \Helpers::formatIdWithPrefix($value['user_id'], 'APP');
        // $userMailArr['utr_no'] = isset($value['tran_id']) ? $value['tran_id'] : '';
        // $userMailArr['benefi_name'] = $benifiName;
        // $userMailArr['disbursed_date'] = isset($value['disburse_date']) ? Carbon::parse($value['disburse_date'])->format('d-m-Y') : '';  
        // Event::dispatch("LMS_USER_DISBURSAL", serialize($userMailArr));
        }
        return true;
    }


    /* save bulk invoice */

    public function saveBulkInvoice(Request $request) {
        if ($request->get('eod_process')) {
            Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
            return back();
        }
        $attributes = $request->all();
        $res = $this->invRepo->saveBulk($attributes);
        if ($res) {

            Session::flash('message', 'Invoice successfully saved');
            return back();
        } else {
            Session::flash('message', 'Something wrong, Invoice is not saved');
            return back();
        }
    }

    /* update invoice amount  */

    public function saveInvoiceAmount(Request $request) {
        $id = Auth::user()->user_id;
        $attributes = $request->all();
        $res = $this->invRepo->updateInvoiceAmount($attributes);
       if ($res) {
            $whereActivi['activity_code'] = 'update_invoice_amount';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Update Invoice Amount (Manage Invoice)';
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($attributes), $arrActivity);
            } 
            Session::flash('message', 'Invoice Amount successfully Updated ');
            return back();
        } else {
            Session::flash('message', 'Something wrong, Amount is not Updated');
            return back();
        }
    }

    /*   save invoice */

    public function saveInvoice(Request $request) {
        if ($request->get('eod_process')) {
            Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
            return back();
        }

        $attributes = $request->all();
        $explode = explode(',', $attributes['supplier_id']);
        $attributes['supplier_id'] = $attributes['user_id'] = $explode[0];
        $explode1 = explode(',', $attributes['program_id']);
        $attributes['program_id'] = $attributes['prgm_id'] = $explode1[0];
        $appId = $attributes['app_id'] = $explode[1];
        $prgmOfferId = $explode[2];
        $date = Carbon::now();
        $id = Auth::user()->user_id;
        $res = $this->invRepo->getSingleAnchorDataByAppId($appId);
        $biz_id = $res->biz_id;
        $getPrgm  = $this->application->getProgram($attributes['program_id']);
        $chkUser  = $this->application->chkUser();
        $customer  = 4;
        $expl  =  explode(",",$getPrgm->invoice_approval); 
        
        $invoice_amount = str_replace(',', '', $attributes['invoice_approve_amount']);
        $invoice_approve_amount = str_replace(',', '', $attributes['invoice_approve_amount']);
        // $invUtilizedAmt = Helpers::anchorSupplierUtilizedLimitByInvoice($attributes['supplier_id'], $request->anchor_id);
        // $totalProductLimit = Helpers::getTotalProductLimit($appId, $productId = 1);
        $marginAmt = Helpers::getOfferMarginAmtOfInvoiceAmt($prgmOfferId, $invoice_amount);

        // $limit =   InvoiceTrait::ProgramLimit($attributes);
        // $sum   =   InvoiceTrait::invoiceApproveLimit($attributes);
        // $remainAmount = $limit - $sum;

        // if ($marginAmt > $remainAmount) {
        //     Session::flash('error', 'Invoice amount should not be greater than the remaining limit amount after excluding the margin amount.');
        //     return back();
        // }

        // if ($totalProductLimit > 0 && $invoice_amount > 0 && $marginAmt > ($totalProductLimit - $invUtilizedAmt)) {
        //     Session::flash('error', 'Invoice amount should not be greater than the balance limit amount.');
        //     return back();
        // }
        if (!empty($attributes['exception'])) {
            $statusId = 28;
            $attributes['remark'] = 'Invoice date & current date difference should not be more than old tenor days';           
        } else {
          if(in_array($customer, $expl))  
          {
            $statusId = 8; 
            
          }
          else if($getPrgm->invoice_approval==4)
          {
              $statusId = 8;   
             
          }
          else
          {
            $statusId = 7;
           
          }
        }
       //////* chk the adhoc condition  
       //created by gajendra chauhan*/
        if(isset($attributes['limit_type']))
        {
            $is_adhoc=1;
        }
        else
        {
            $is_adhoc=0;
        }

        $uploadData = Helpers::uploadAppFile($attributes, $appId);
        $userFile = $this->docRepo->saveFile($uploadData);

        $arr = array('anchor_id' => $attributes['anchor_id'],
            'supplier_id' => $attributes['supplier_id'],
            'program_id' => $attributes['program_id'],
            'app_id' => $appId,
            'biz_id' => $biz_id,
            'invoice_no' => $attributes['invoice_no'],
            'tenor' => $attributes['tenor'],
            'invoice_due_date' => ($attributes['invoice_due_date']) ? Carbon::createFromFormat('d/m/Y', $attributes['invoice_due_date'])->format('Y-m-d') : '',
            'invoice_date' => ($attributes['invoice_date']) ? Carbon::createFromFormat('d/m/Y', $attributes['invoice_date'])->format('Y-m-d') : '',
            'invoice_approve_amount' => $invoice_approve_amount,
            'invoice_amount' => $invoice_amount,
            'prgm_offer_id' => $attributes['prgm_offer_id'],
            'status_id' => $statusId,
            'remark' => $attributes['remark'],
            'is_adhoc' => $is_adhoc,
            'file_id' => $userFile->file_id,
            'created_by' => $id,
            'updated_by' => $id,
            'created_at' => $date,
            'invoice_margin_amount' => $marginAmt
        );
        $result = $this->invRepo->save($arr);
       
        if ($result) {
            if($is_adhoc==1 && $statusId==8) 
            {
              InvoiceTrait::saveAdhocApproveStatus($result);
            }
            else 
            {
           
               InvoiceTrait::getManualInvoiceStatus($result);
            }
            if( $statusId==8)
            {
               $inv_apprv_margin_amount = InvoiceTrait::invoiceMargin($result);
               $is_margin_deduct =  1;  
               $this->invRepo->updateFileId(['invoice_margin_amount'=>$inv_apprv_margin_amount,'is_margin_deduct' =>1],$result['invoice_id']);
            }

            $whereActivi['activity_code'] = 'backend_save_invoice';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Add Backend Invoice Invoice Upload (Manage Invoice) AppID. ' . $appId;
                $arrActivity['app_id'] = $appId;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arr), $arrActivity);
            }             
            
            Session::flash('message', 'Invoice successfully saved');
            return back();
        } else {
            Session::flash('message', 'Something wrong, Invoice is not saved');
            return back();
        }
    }

    /**
     * Display a pop up iframe for disburse check
     *
     * @return \Illuminate\Http\Response
     */
    public function disburseConfirm(Request $request)
    {
        $disburseType = $request->get('disburse_type');
        $invoiceIds = $request->get('invoice_ids');
        $bankType = $request->get('bank_type');
        if(empty($invoiceIds)) {
            Session::flash('message', trans('backend_messages.noSelectedInvoice'));
            Session::flash('operation_status', 1);
            
            return redirect()->route('backend_get_disbursed_invoice');
        }
        $record = array_filter(explode(",",$invoiceIds));
        $allrecords = array_unique($record);
        $allrecords = array_map('intval', $allrecords);
        $allinvoices = $this->lmsRepo->getInvoices($allrecords)->toArray();
        $supplierIds = $this->lmsRepo->getInvoiceSupplier($allrecords)->toArray();
        
        $customersDisbursalList = $this->lmsRepo->lmsGetInvoiceClubCustomer($supplierIds, $allrecords);
        // dd($disburseType);
        return view('backend.invoice.disburse_check')
                ->with([
                    'customersDisbursalList' => $customersDisbursalList,
                    'invoiceIds' => $invoiceIds, 
                    'disburseType' => $disburseType,
                    'bankType' => $bankType
                ]);;              
    }

    /**
     * Display a pop up iframe for bankAPiOnline
     *
     * @return \Illuminate\Http\Response
     */
    public function disburseOnline(Request $request)
    {
        try {
            date_default_timezone_set("Asia/Kolkata");
            $currentTimeHour = \Carbon\Carbon::now()->format('H');
            $validateTimeHour = config('lms.DISBURSAL_TIME_VALIDATE');
            $invoiceIds = $request->get('invoice_ids');
            $disburseDateCal = $request->get('value_date');
            // $disburseDate =  \Helpers::getSysStartDate();
            $disburseDate = \Carbon\Carbon::createFromFormat('d/m/Y', $disburseDateCal)->setTimezone(config('common.timezone'))->format('Y-m-d');
            $disburseType = config('lms.DISBURSE_TYPE')['ONLINE'];
            $creatorId = Auth::user()->user_id;

            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            // if (date('H') >= $validateTimeHour) { 
            //     Session::flash('error', 'Disbursment can not be done after '. Carbon::createFromFormat('H', $validateTimeHour)->format('g:i A'));
            //     return redirect()->route('backend_get_disbursed_invoice');
            // }
            if(empty($invoiceIds)){
                return redirect()->route('backend_get_disbursed_invoice')->withErrors(trans('backend_messages.noSelectedInvoice'));
            }

            $record = array_filter(explode(",",$invoiceIds));
            $allrecords = array_unique($record);
            $allrecords = array_map('intval', $allrecords);
            $allinvoices = $this->lmsRepo->getInvoices($allrecords)->toArray();


            foreach ($allinvoices as $inv) {
                $disbursedInvoiceId = $this->lmsRepo->findInvoiceDisbursedInvoiceIdByInvoiceId($inv['invoice_id']);

                if($disbursedInvoiceId->count() > 0) {
                    return redirect()->route('backend_get_disbursed_invoice')->withErrors('Invoice '.$inv['invoice_no'].' already under process of disbursment');
                }
                else if($inv['supplier']['is_buyer'] == 2 && empty($inv['supplier']['anchor_bank_details'])){
                    return redirect()->route('backend_get_disbursed_invoice')->withErrors(trans('backend_messages.noBankAccount'));
                } elseif ($inv['supplier']['is_buyer'] == 1 && empty($inv['supplier_bank_detail'])) {
                    return redirect()->route('backend_get_disbursed_invoice')->withErrors(trans('backend_messages.noBankAccount'));
                }
            }

            $supplierIds = $this->lmsRepo->getInvoiceSupplier($allrecords)->toArray();
            $fundedAmount = 0;
            $interest = 0;
            $disburseAmount = 0;
            $totalInterest = 0;
            $totalProcessingFee = 0;
            $totalFunded = 0;
            $totalMargin = 0;
            $exportData = [];
            $invoiceDisbursedIds = [];
            $disbursalIds = [];
            $disbursalData = [];
            $otherData = [];
            $transId = _getRand(18);

            foreach ($supplierIds as $userid) {
                $refNo = _getRand(12);
                $disburseAmount = 0;
                foreach ($allinvoices as $invoice) {
                    if($invoice['supplier_id'] == $userid) {
                        
                        $interest= 0;
                        $processingFee= 0;
                        $margin= 0;

                        $tenor = $this->calculateTenorDays($invoice);
                        $margin = $this->calMargin($invoice['invoice_approve_amount'], $invoice['program_offer']['margin']);
                        $fundedAmount = $invoice['invoice_approve_amount'] - $margin;
                        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$disburseDate)) {
                            $str_to_time_date = strtotime($disburseDate);
                        } else {
                            $str_to_time_date = strtotime(\Carbon\Carbon::createFromFormat('d/m/Y', $disburseDate)->setTimezone(config('common.timezone'))->format('Y-m-d'));
                        }
                        $bankId = $invoice['program_offer']['bank_id'];
                        $oldIntRate = $invoice['program_offer']['interest_rate'] - $invoice['program_offer']['base_rate'];
                        $interestRate = ($invoice['is_adhoc'] == 1) ? (float)$invoice['program_offer']['adhoc_interest_rate'] : (float)$invoice['program_offer']['interest_rate'];
                        $Obj = new ManualApportionmentHelper($this->lmsRepo);
                        $bankRatesArr = $Obj->getBankBaseRates($bankId);
                        if ($bankRatesArr && $invoice['is_adhoc'] != 1) {
                          $actIntRate = $Obj->getIntRate($oldIntRate, $bankRatesArr, $str_to_time_date);
                        } else {
                          $actIntRate = $interestRate;
                        }
                        if ($invoice['program_offer']['benchmark_date'] == 1) {
                            $tenor = $this->calDiffDays($invoice['invoice_due_date'], $disburseDate);
                        }

                        $tInterest = $this->calInterest($fundedAmount, $actIntRate, $tenor);
                        // if (isset($invoice['processing_fee']['chrg_type']) && $invoice['processing_fee']['chrg_type'] == 2) {
                        //     $processingFee = $this->calPercentage($fundedAmount, $invoice['processing_fee']['chrg_value']);
                        // } else {
                        //     $processingFee = $invoice['processing_fee']['chrg_value'];

                        // }
                        $processingFee = $invoice['processing_fee']['gst_chrg_value'];

                        $prgmWhere=[];
                        $prgmWhere['prgm_id'] = $invoice['program_id'];
                        $prgmData = $this->appRepo->getSelectedProgramData($prgmWhere, ['interest_borne_by']);   
                        
                        if(isset($prgmData[0]) && $prgmData[0]->interest_borne_by == 2 && $invoice['program_offer']['payment_frequency'] == 1) {
                            $interest = $tInterest;
                        }

                        $totalInterest += $interest;
                        $totalMargin += $margin;
                        $amount = round($fundedAmount - $interest - $processingFee, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
                        $disburseAmount += $amount;


                        $disbursalData['invoice'] = $invoice;

                    }
                }
                if($disburseType == 1) {
                    $modePay = ($disburseAmount < 200000) ? 'NEFT' : 'RTGS' ;
                    $userData = $this->lmsRepo->getUserBankDetail($userid)->toArray();
                    $bank_account_id = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['bank_account_id'] : $userData['supplier_bank_detail']['bank_account_id'];
                    $bank_name = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['bank']['bank_name'] : $userData['supplier_bank_detail']['bank']['bank_name'] ;
                    $bank_id = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['bank']['id'] : $userData['supplier_bank_detail']['bank']['id'] ;
                    $ifsc_code = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['ifsc_code'] : $userData['supplier_bank_detail']['ifsc_code'];
                    $acc_no = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['acc_no'] : $userData['supplier_bank_detail']['acc_no'];
                    $acc_name = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['acc_name'] : $userData['supplier_bank_detail']['acc_name'];
                    $exportData[$userid]['RefNo'] = $refNo;
                    $exportData[$userid]['Amount'] = "$disburseAmount";
                    $exportData[$userid]['Debit_Acct_No'] = config('lms.IDFC_DEBIT_BANK')['DEBIT_ACC_NO'];
                    $exportData[$userid]['Debit_Acct_Name'] = config('lms.IDFC_DEBIT_BANK')['DEBIT_ACC_NAME'];
                    $exportData[$userid]['Debit_Mobile'] = config('lms.IDFC_DEBIT_BANK')['DEBIT_MOBILE'];
                    if (config('lms.UAT_ACTIVE') == 1) {
                        $exportData[$userid]['Ben_IFSC'] = config('lms.IDFC_CREDIT_BANK')['BEN_IFSC'];
                        $exportData[$userid]['Ben_Acct_No'] = config('lms.IDFC_CREDIT_BANK')['BEN_ACC_NO'];
                    } else {
                        //$exportData[$userid]['Ben_IFSC'] = ($bank_id == config('lms.IDFC_BANK_ID')) ? null : $ifsc_code;
                        $exportData[$userid]['Ben_IFSC']    = $ifsc_code;
                        $exportData[$userid]['Ben_Acct_No'] = $acc_no;
                    }
                    $exportData[$userid]['Ben_Name'] = $acc_name;
                    $exportData[$userid]['Ben_BankName'] = $bank_name;
                    $exportData[$userid]['Ben_Email'] = $disbursalData['invoice']['supplier']['email'];
                    $exportData[$userid]['Ben_Mobile'] = $disbursalData['invoice']['supplier']['mobile_no'];
                    //$exportData[$userid]['Mode_of_Pay'] = $modePay;
                    $exportData[$userid]['Mode_of_Pay'] = ($bank_id == config('lms.IDFC_BANK_ID')) ? 'IFT' : $modePay;
                    //$exportData[$userid]['Mode_of_Pay'] = ($bank_id == config('lms.IDFC_BANK_ID')) ? 'BT' : $modePay;
                    $exportData[$userid]['Nature_of_Pay'] = 'MPYMT';
                    $exportData[$userid]['Remarks'] = 'invoice disbursal';

                } 
            }
            if($disburseType == 1 && !empty($allrecords)) {
            
                $http_header = [
                    'timestamp' => \Carbon\Carbon::createFromFormat('d/m/Y', $disburseDateCal)->setTimezone(config('common.timezone'))->format('Y-m-d H:i:s'),
                    'txn_id' => $transId
                    ];

                $header = [
                    'Maker_ID' => "CAPSAVE.M",
                    'Checker_ID' => "CAPSAVE.C1",
                    'Approver_ID' => "CAPSAVE.C2"
                    ];

                $params = [
                    'http_header' => $http_header,
                    'header' => $header,
                    'request' => $exportData
                    ];

                $idfcObj= new Idfc_lib();
                $getResponse = false;
                $result = $idfcObj->api_call(Idfc_lib::MULTI_PAYMENT, $params, $getResponse);
                if ($getResponse) {
                    dd($result);
                }
                if (isset($result['code'])) {
                    if (isset($result['http_code']) && $result['http_code'] == 200) {
                        
                    } else{
                        $http_code = $result['code'] ? $result['code']  : $result['http_code']. ', ';
                        $message = $result['message'] ?? $result['message'];
                        Session::flash('message', 'Error : '. $http_code  .  $message);
                        return redirect()->route('backend_get_disbursed_invoice');
                    }
                }
                $fileDirPath = getPathByTxnId($transId);
                $time = date('y-m-d H:i:s');
                
                $result['result']['http_header'] = (is_array($result['result']['http_header'])) ? json_encode($result['result']['http_header']): $result['result']['http_header'];
                $fileContents = PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['url'].  PHP_EOL
                    .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['payload']  .PHP_EOL
                    .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['http_header']  .PHP_EOL
                    .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['response'] . PHP_EOL;
                
                $createOrUpdatefile = Helpers::uploadOrUpdateFileWithContent($fileDirPath, $fileContents, true);
                if(is_array($createOrUpdatefile)) {
                    $userFileSaved = $this->docRepo->saveFile($createOrUpdatefile)->toArray();
                } else {
                    $userFileSaved = $createOrUpdatefile;
                }
                
                $otherData['bank_type'] = config('lms.BANK_TYPE')['IDFC'];
                $disbusalApiLogData = $this->createDisbusalApiLogData($userFileSaved, $result, $otherData);
                $createDisbusalApiLog = $this->lmsRepo->saveUpdateDisbursalApiLog($disbusalApiLogData);
                if ($createDisbusalApiLog) {
                    $disbursalApiLogId = $createDisbusalApiLog->disbursal_api_log_id;
                }
                if ($result['status'] == 'success') {
                    $this->disburseTableInsert($exportData, $supplierIds, $allinvoices, $disburseType, $disburseDate, $disbursalApiLogId);
                } else { 
                    $http_code = $result['http_code'] ? $result['http_code'] . ', ' : $result['code'];
                    $message = $result['message'] ?? $result['message'];
                    Session::flash('message', 'Error : '. 'HTTP Code '. $http_code  .  $message);
                    return redirect()->route('backend_get_disbursed_invoice');
                }
            }

            $whereActivi['activity_code'] = 'disburse_online';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Disburse Online, Disbursement Queue (Manage Invoice)';
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['supplierIds'=>$supplierIds, 'request'=>$request->all()]), $arrActivity);
            }             
                    
            Session::flash('message',trans('backend_messages.disbursed'));
            return redirect()->route('backend_get_disbursed_invoice')->withErrors('message',trans('backend_messages.disbursed'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }             
    }

public function disburseTableInsert($exportData = [], $supplierIds = [], $allinvoices = [], $disburseType = null, $disburseDate, $disbursalApiLogId = null) {
        $totalInterest = 0;
        $totalMargin = 0;
        $batchId= _getRand(12);
        $creatorId = Auth::user()->user_id;
        $result = $this->export($exportData, $batchId);
        $file['file_path'] = $result['file_path'] ?? '';
        $disbusalApiLogData = [];
        $whereCondition = [];
        if ($file) {
            $createBatchFileData = $this->createBatchFileData($file);
            $createBatchFile = $this->lmsRepo->saveBatchFile($createBatchFileData);
            if ($createBatchFile) {
                $createDisbursalBatch = $this->lmsRepo->createDisbursalBatch($createBatchFile, $batchId, $disbursalApiLogId);
                $disbursalBatchId = $createDisbursalBatch->disbursal_batch_id;
                if ($disbursalBatchId) {
                    $disbusalApiLogData['disbursal_batch_id'] = $disbursalBatchId;
                    $whereCondition['disbursal_api_log_id'] = $disbursalApiLogId;
                    $createDisbusalApiLog = $this->lmsRepo->saveUpdateDisbursalApiLog($disbusalApiLogData, $whereCondition);
                }
            }
        }

        foreach ($supplierIds as $userid) {
            $disburseAmount = 0;
            $userData = $this->lmsRepo->getUserBankDetail($userid)->toArray();
            $userData['disbursal_batch_id'] =$disbursalBatchId;
            $userData['ref_no'] =$exportData[$userid]['RefNo'];

            $disbursalRequest = $this->createDisbursalData($userData, $disburseAmount, $disburseType);
            $createDisbursal = $this->lmsRepo->saveDisbursalRequest($disbursalRequest);
            $this->lmsRepo->createDisbursalStatusLog($createDisbursal->disbursal_id, 10, '', $creatorId);

            foreach ($allinvoices as $invoice) {
                if($invoice['supplier_id'] == $userid) {
                    $invoiceDisbursedData = $this->lmsRepo->findInvoiceDisbursedByInvoiceId($invoice['invoice_id'])->toArray();
                    $interest= 0;
                    $margin= 0;
                    $tenor = $this->calculateTenorDays($invoice);
                    $margin = $this->calMargin($invoice['invoice_approve_amount'], $invoice['program_offer']['margin']);
                    $fundedAmount = $invoice['invoice_approve_amount'] - $margin;
                    if (empty($invoiceDisbursedData)) {
                        $processingFee= 0;
                        if (isset($invoice['processing_fee']['chrg_type']) && $invoice['processing_fee']['chrg_type'] == 2) {
                            $processingFee = $this->calPercentage($fundedAmount, $invoice['processing_fee']['chrg_value']);
                        } else {
                            $processingFee = $invoice['processing_fee']['chrg_value'];
                        }
                        $processingFeeGst = $invoice['processing_fee']['gst_chrg_value'] - $processingFee;

                        $invoice['batch_id'] = $batchId;
                        $invoice['disburse_date'] = $disburseDate;
                        $invoice['disbursal_id'] = $createDisbursal->disbursal_id;
                        $invoice['processing_fee'] = $processingFee ?? 0;
                        $invoice['processing_fee_gst'] = $processingFeeGst ?? 0;
                       
                        $invoiceDisbursedRequest = $this->createInvoiceDisbursedData($invoice, $disburseType);
                        $createInvoiceDisbursed = $this->lmsRepo->saveUpdateInvoiceDisbursed($invoiceDisbursedRequest);
                        $invoiceDisbursedId = $createInvoiceDisbursed->invoice_disbursed_id;
                    }
                   
                    $updateInvoiceStatus = $this->lmsRepo->updateInvoiceStatus($invoice['invoice_id'], 10);
                    $this->invRepo->saveInvoiceStatusLog($invoice['invoice_id'], 10);
                    if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$disburseDate)) {
                        $str_to_time_date = strtotime($disburseDate);
                    } else {
                        $str_to_time_date = strtotime(\Carbon\Carbon::createFromFormat('d/m/Y', $disburseDate)->setTimezone(config('common.timezone'))->format('Y-m-d'));
                    }
                    $bankId = $invoice['program_offer']['bank_id'];
                    $oldIntRate = $invoice['program_offer']['interest_rate'] - $invoice['program_offer']['base_rate'];
                    $interestRate = ($invoice['is_adhoc'] == 1) ? (float)$invoice['program_offer']['adhoc_interest_rate'] : (float)$invoice['program_offer']['interest_rate'];
                    $Obj = new ManualApportionmentHelper($this->lmsRepo);
                    $bankRatesArr = $Obj->getBankBaseRates($bankId);
                    if ($bankRatesArr && $invoice['is_adhoc'] != 1) {
                      $actIntRate = $Obj->getIntRate($oldIntRate, $bankRatesArr, $str_to_time_date);
                    } else {
                      $actIntRate = $interestRate;
                    }
                    $banchMarkDateFlag = $invoice['program_offer']['benchmark_date'];
        
                    if ($banchMarkDateFlag == 1) {
                        $tenor = $this->calDiffDays($invoice['invoice_due_date'], $disburseDate);
                    }
                    $tInterest = $this->calInterest($fundedAmount, $actIntRate, $tenor);

                    $prgmWhere=[];
                    $prgmWhere['prgm_id'] = $invoice['program_id'];
                    $prgmData = $this->appRepo->getSelectedProgramData($prgmWhere, ['interest_borne_by']);  

                    if(isset($prgmData[0]) && $prgmData[0]->interest_borne_by == 2 && $invoice['program_offer']['payment_frequency'] == 1) {
                        $interest = $tInterest;
                    }

                    $totalInterest += $interest;
                    $totalMargin += $margin;
                    $amount = round($fundedAmount - $interest - $processingFee - $processingFeeGst, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
                    $disburseAmount += $amount;
                }
            }
           
            if($createDisbursal) {
                $updateDisbursal = $this->lmsRepo->updateDisburse([
                        'disburse_amount' => $disburseAmount
                    ], $createDisbursal->disbursal_id);
            }

        }
        return true;
    }

    
    /**
     * Display a pop up iframe for bankAPiOffline
     *
     * @return \Illuminate\Http\Response
     */
    public function disburseOffline(Request $request)
    {
        try {

            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            
            $invoiceIds = $request->get('invoice_ids');
            $disburseDate = $request->get('disburse_date');
            $fundDate = date("Y-m-d h:i:s", strtotime(str_replace('/','-',$disburseDate)));
            $creatorId = Auth::user()->user_id;
            $validator = Validator::make($request->all(), [
               'disburse_date' => 'required'
            ]);
            
            if ($validator->fails()) {
                Session::flash('error', $validator->messages()->first());
                return redirect()->back()->withInput();
            }

            $disburseType = config('lms.DISBURSE_TYPE')['OFFLINE']; // Online by Bank Api i.e 2
            if(empty($invoiceIds)){
                return redirect()->route('backend_get_disbursed_invoice')->withErrors(trans('backend_messages.noSelectedInvoice'));
            }
            $record = array_filter(explode(",",$invoiceIds));
            $allrecords = array_unique($record);
            $allrecords = array_map('intval', $allrecords);
            $allinvoices = $this->lmsRepo->getInvoices($allrecords)->toArray();


            foreach ($allinvoices as $inv) {
                $disbursedInvoiceId = $this->lmsRepo->findInvoiceDisbursedInvoiceIdByInvoiceId($inv['invoice_id']);

                if($disbursedInvoiceId->count() > 0) {
                    return redirect()->route('backend_get_disbursed_invoice')->withErrors('Invoice '.$inv['invoice_no'].' already under process of disbursment');
                }
                else if($inv['supplier']['is_buyer'] == 2 && empty($inv['supplier']['anchor_bank_details'])){
                    return redirect()->route('backend_get_disbursed_invoice')->withErrors(trans('backend_messages.noBankAccount'));
                } elseif ($inv['supplier']['is_buyer'] == 1 && empty($inv['supplier_bank_detail'])) {
                    return redirect()->route('backend_get_disbursed_invoice')->withErrors(trans('backend_messages.noBankAccount'));
                }
            }

            $supplierIds = $this->lmsRepo->getInvoiceSupplier($allrecords)->toArray();

            $fundedAmount = 0;
            $interest = 0;
            $disburseAmount = 0;
            $totalInterest = 0;
            $totalProcessingFee = 0;
            $totalFunded = 0;
            $totalMargin = 0;
            $exportData = [];
            $invoiceDisbursedIds = [];
            $disbursalIds = [];
            $disbursalData = [];
            $batchId= _getRand(12);
            $transId = _getRand(18);

            foreach ($supplierIds as $userid) {
                $disburseAmount = 0;
                foreach ($allinvoices as $invoice) {
                    if($invoice['supplier_id'] == $userid) {
                        $str_to_time_date = strtotime(\Carbon\Carbon::createFromFormat('d/m/Y', $disburseDate)->setTimezone(config('common.timezone'))->format('Y-m-d'));
                        $bankId = $invoice['program_offer']['bank_id'];
                        $oldIntRate = (float)$invoice['program_offer']['interest_rate'] - $invoice['program_offer']['base_rate'];
                        $interestRate = ($invoice['is_adhoc'] == 1) ? (float)$invoice['program_offer']['adhoc_interest_rate'] : (float)$invoice['program_offer']['interest_rate'];
                        $Obj = new ManualApportionmentHelper($this->lmsRepo);
                        $bankRatesArr = $Obj->getBankBaseRates($bankId);
                        if ($bankRatesArr && $invoice['is_adhoc'] != 1) {
                          $actIntRate = $Obj->getIntRate($oldIntRate, $bankRatesArr, $str_to_time_date);
                        } else {
                          $actIntRate = $interestRate;
                        }
                        $interest= 0;
                        $margin= 0;

                        $tenor = $this->calculateTenorDays($invoice);
                        $margin = $this->calMargin($invoice['invoice_approve_amount'], $invoice['program_offer']['margin']);
                        $fundedAmount = $invoice['invoice_approve_amount'] - $margin;
                        $banchMarkDateFlag = $invoice['program_offer']['benchmark_date'];
        
                        if ($banchMarkDateFlag == 1) {
                            $tenor = $this->calDiffDays($invoice['invoice_due_date'], $disburseDate);
                        }
                        $tInterest = $this->calInterest($fundedAmount, $actIntRate, $tenor);
                        
                        $processingFee = $invoice['processing_fee']['gst_chrg_value'] ?? 0;
                        $prgmWhere=[];
                        $prgmWhere['prgm_id'] = $invoice['program_id'];
                        $prgmData = $this->appRepo->getSelectedProgramData($prgmWhere, ['interest_borne_by']);   

                        if(isset($prgmData[0]) && $prgmData[0]->interest_borne_by == 2 && $invoice['program_offer']['payment_frequency'] == 1) {
                            $interest = $tInterest;
                        }
                        $totalInterest += $interest;
                        $totalMargin += $margin;
                        $amount = round($fundedAmount - $interest - $processingFee, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
                        $disburseAmount += $amount;


                        $disbursalData['invoice'] = $invoice;

                    }
                }
                if($disburseType == 2) {

                    $userData = $this->lmsRepo->getUserBankDetail($userid)->toArray();
                    $bank_account_id = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['bank_account_id'] : $userData['supplier_bank_detail']['bank_account_id'];
                    $bank_name = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['bank']['bank_name'] : $userData['supplier_bank_detail']['bank']['bank_name'] ;
                    $ifsc_code = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['ifsc_code'] : $userData['supplier_bank_detail']['ifsc_code'];
                    $acc_no = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['acc_no'] : $userData['supplier_bank_detail']['acc_no'];
                    $acc_name = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['acc_name'] : $userData['supplier_bank_detail']['acc_name'];

                    $exportData[$userid]['RefNo'] = $disbursalData['invoice']['lms_user']['virtual_acc_id'];
                    $exportData[$userid]['Amount'] = $disburseAmount;
                    $exportData[$userid]['Debit_Acct_No'] = '12334445511111';
                    $exportData[$userid]['Debit_Acct_Name'] = 'testing name';
                    $exportData[$userid]['Debit_Mobile'] = '9876543210';
                    $exportData[$userid]['Ben_IFSC'] = $ifsc_code;
                    $exportData[$userid]['Ben_Acct_No'] = $acc_no;
                    $exportData[$userid]['Ben_Name'] = $acc_name;
                    $exportData[$userid]['Ben_BankName'] = $bank_name;
                    $exportData[$userid]['Ben_Email'] = $disbursalData['invoice']['supplier']['email'];
                    $exportData[$userid]['Ben_Mobile'] = $disbursalData['invoice']['supplier']['mobile_no'];
                    $exportData[$userid]['Mode_of_Pay'] = 'IFT';
                    //$exportData[$userid]['Mode_of_Pay'] = 'BT';
                    $exportData[$userid]['Nature_of_Pay'] = 'MPYMT';
                    $exportData[$userid]['Remarks'] = 'test remarks';
                    $exportData[$userid]['Value_Date'] = date('Y-m-d');

                } 
            }
            // $result = $this->export($exportData, $batchId);
            $file['file_path'] = $result['file_path'] ?? '';
            if ($file) {
                $createBatchFileData = $this->createBatchFileData($file);
                $createBatchFile = $this->lmsRepo->saveBatchFile($createBatchFileData);
                if ($createBatchFile) {
                    $createDisbursalBatch = $this->lmsRepo->createDisbursalBatch($createBatchFile, $batchId);
                    $disbursalBatchId = $createDisbursalBatch->disbursal_batch_id;
                }
            }
	    $processingFeeGst = 0;
            foreach ($supplierIds as $userid) {
                $disburseAmount = 0;
                $userData = $this->lmsRepo->getUserBankDetail($userid)->toArray();
                $userData['disbursal_batch_id'] =$disbursalBatchId;
                $userData['disburse_date'] = $disburseDate;
                $disbursalRequest = $this->createDisbursalData($userData, $disburseAmount, $disburseType);
                $createDisbursal = $this->lmsRepo->saveDisbursalRequest($disbursalRequest);
                $this->lmsRepo->createDisbursalStatusLog($createDisbursal->disbursal_id, 10, '', $creatorId);

                foreach ($allinvoices as $invoice) {
                    if($invoice['supplier_id'] == $userid) {
                        $invoiceDisbursedData = $this->lmsRepo->findInvoiceDisbursedByInvoiceId($invoice['invoice_id'])->toArray();

                        if ($invoiceDisbursedData == null) {
                            $processingFee= 0;
                            $invoice['batch_id'] = $batchId;
                            $disburseNewDate = \Carbon\Carbon::createFromFormat('d/m/Y', $disburseDate)->setTimezone(config('common.timezone'))->format('Y-m-d');
                            $invoice['disburse_date'] = $disburseNewDate;
                            $invoice['disbursal_id'] = $createDisbursal->disbursal_id;
                            if ($bankRatesArr && $invoice['is_adhoc'] != 1) {
                              $actIntRate = $Obj->getIntRate($oldIntRate, $bankRatesArr, $str_to_time_date);
                            } else {
                              $actIntRate = $interestRate;
                            }
                            if (isset($invoice['processing_fee']['chrg_type']) && $invoice['processing_fee']['chrg_type'] == 2) {
                                $processingFee = $this->calPercentage($fundedAmount, $invoice['processing_fee']['chrg_value']);
                            } else {
                                $processingFee = $invoice['processing_fee']['chrg_value'] ?? 0;

                            }
                            $processingFeeGst = ($invoice['processing_fee']['gst_chrg_value'] ?? 0) - $processingFee;
                            $invoice['disbursal_id'] = $createDisbursal->disbursal_id;
                            $invoice['processing_fee'] = $processingFee;
                            $invoice['processing_fee_gst'] = $processingFeeGst;
                            
                            $invoiceDisbursedRequest = $this->createInvoiceDisbursedData($invoice, $disburseType);
                            $createInvoiceDisbursed = $this->lmsRepo->saveUpdateInvoiceDisbursed($invoiceDisbursedRequest);
                            $invoiceDisbursedId = $createInvoiceDisbursed->invoice_disbursed_id;
                        }
                        
                        $updateInvoiceStatus = $this->lmsRepo->updateInvoiceStatus($invoice['invoice_id'], 10);
                        $this->invRepo->saveInvoiceStatusLog($invoice['invoice_id'], 10);
                        $str_to_time_date = strtotime(\Carbon\Carbon::createFromFormat('d/m/Y', $disburseDate)->setTimezone(config('common.timezone'))->format('Y-m-d'));
                        $bankId = $invoice['program_offer']['bank_id'];
                        $oldIntRate = (float)$invoice['program_offer']['interest_rate'] - $invoice['program_offer']['base_rate'];
                        $interestRate = ($invoice['is_adhoc'] == 1) ? (float)$invoice['program_offer']['adhoc_interest_rate'] : (float)$invoice['program_offer']['interest_rate'];
                        $Obj = new ManualApportionmentHelper($this->lmsRepo);
                        $bankRatesArr = $Obj->getBankBaseRates($bankId);
                        if ($bankRatesArr && $invoice['is_adhoc'] != 1) {
                          $actIntRate = $Obj->getIntRate($oldIntRate, $bankRatesArr, $str_to_time_date);
                        } else {
                          $actIntRate = $interestRate;
                        }
                        $interest= 0;
                        $margin= 0;
                        
                        $prgmWhere=[];
                        $prgmWhere['prgm_id'] = $invoice['program_id'];
                        $prgmData = $this->appRepo->getSelectedProgramData($prgmWhere, ['interest_borne_by']);                          

                        $tenor = $this->calculateTenorDays($invoice);
                        $margin = $this->calMargin($invoice['invoice_approve_amount'], $invoice['program_offer']['margin']);
                        $fundedAmount = $invoice['invoice_approve_amount'] - $margin;

                        $banchMarkDateFlag = $invoice['program_offer']['benchmark_date'];
                        if ($banchMarkDateFlag == 1) {
                            $tenor = $this->calDiffDays($invoice['invoice_due_date'], $fundDate);
                        }
                        $tInterest = $this->calInterest($fundedAmount, $actIntRate, $tenor);

                        if(isset($prgmData[0]) && $prgmData[0]->interest_borne_by == 2 && $invoice['program_offer']['payment_frequency'] == 1) {
                            $interest = $tInterest;
                        }

                        $totalInterest += $interest;
                        $totalProcessingFee += $processingFee;
                        $totalMargin += $margin;
                        $amount = round($fundedAmount - $interest - $processingFee - $processingFeeGst, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
                        $disburseAmount += $amount;

                        

                    }
                }
                
                if($createDisbursal) {
                    $updateDisbursal = $this->lmsRepo->updateDisburse([
                            'disburse_amount' => $disburseAmount
                        ], $createDisbursal->disbursal_id);
                }

            }
            $whereActivi['activity_code'] = 'disburse_offline';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Disburse offline, Disbursement Queue (Manage Invoice)';
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['supplierIds'=>$supplierIds, 'request'=>$request->all()]), $arrActivity);
            } 
            Session::flash('message',trans('backend_messages.disbursed'));
            return redirect()->route('backend_get_disbursed_invoice');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }



    public function export($data, $filename) {
        $sheet =  new PHPExcel();
        $sheet->getProperties()
                ->setCreator("Capsave")
                ->setLastModifiedBy("Capsave")
                ->setTitle("Bank Disburse Excel")
                ->setSubject("Bank Disburse Excel")
                ->setDescription("Bank Disburse Excel")
                ->setKeywords("Bank Disburse Excel")
                ->setCategory("Bank Disburse Excel");
    
        $sheet->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Client Code')
                ->setCellValue('B1', 'Debit account no.')
                ->setCellValue('C1', 'Transaction type code')
                ->setCellValue('D1', 'Value date')
                ->setCellValue('E1', 'Amount')
                ->setCellValue('F1', 'Beneficary Name')
                ->setCellValue('G1', 'Beneficary Accunt no.')
                ->setCellValue('H1', 'IFSC code')
                ->setCellValue('I1', 'Customer Ref no.')
                ->setCellValue('J1', 'Beneficary email id')
                ->setCellValue('K1', 'Beneficiary mobile no.')
                ->setCellValue('L1', 'Remarks')
                ->setCellValue('M1', 'Payment Type')
                ->setCellValue('N1', 'Purpose code')
                ->setCellValue('O1', 'Bene a/c type')
                ->setCellValue('P1', 'Payable Location')
                ->setCellValue('Q1', 'Print branch name')
                ->setCellValue('R1', 'Mode of delivery')
                ->setCellValue('S1', 'Transaction currency')
                ->setCellValue('T1', 'BENE_ADD1')
                ->setCellValue('U1', 'BENE_ADD2')
                ->setCellValue('V1', 'BENE_ADD3')
                ->setCellValue('W1', 'BENE_ADD4')
                ->setCellValue('X1', 'Beneficiary ID')
                ->setCellValue('Y1', 'Remote Printing')
                ->setCellValue('Z1', 'Print Branch Location')
                ->setCellValue('AA1', 'Nature Of Payment');
        $rows = 2;

        foreach($data as $rowData){
            $sheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $rows, $rowData['Client_Code'] ?? 'XYZ')
                ->setCellValue('B' . $rows, $rowData['Debit_Acct_No'] ?? '')
                ->setCellValue('C' . $rows, $rowData['Trans_Type_Code'] ?? '')
                ->setCellValue('D' . $rows, $rowData['Value_Date'] ?? '')
                ->setCellValue('E' . $rows, $rowData['Amount'] ?? '')
                ->setCellValue('F' . $rows, $rowData['Ben_Name'] ?? '')
                ->setCellValue('G' . $rows, $rowData['Ben_Acct_No'] ?? '')
                ->setCellValue('H' . $rows, $rowData['Ben_IFSC'] ?? '')
                ->setCellValue('I' . $rows, $rowData['RefNo'] ?? '')
                ->setCellValue('J' . $rows, $rowData['Ben_Email'] ?? '')
                ->setCellValue('K' . $rows, $rowData['Ben_Mobile'] ?? '')
                ->setCellValue('L' . $rows, $rowData['Remarks'] ?? '')
                ->setCellValue('M' . $rows, $rowData['Mode_of_Pay'] ?? '')
                ->setCellValue('N' . $rows, $rowData['column'] ?? '')
                ->setCellValue('O' . $rows, $rowData['column'] ?? '')
                ->setCellValue('P' . $rows, $rowData['column'] ?? '')
                ->setCellValue('Q' . $rows, $rowData['column'] ?? '')
                ->setCellValue('R' . $rows, $rowData['column'] ?? '')
                ->setCellValue('S' . $rows, $rowData['column'] ?? '')
                ->setCellValue('T' . $rows, $rowData['column'] ?? '')
                ->setCellValue('U' . $rows, $rowData['column'] ?? '')
                ->setCellValue('V' . $rows, $rowData['column'] ?? '')
                ->setCellValue('W' . $rows, $rowData['column'] ?? '')
                ->setCellValue('X' . $rows, $rowData['column'] ?? '')
                ->setCellValue('Y' . $rows, $rowData['column'] ?? '')
                ->setCellValue('Z' . $rows, $rowData['column'] ?? '')
                ->setCellValue('AA' . $rows, $rowData['Nature_of_Pay'] ?? '');

            $rows++;
        }

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');

        // // If you're serving to IE over SSL, then the following may be needed
        // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        // header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header ('Pragma: public'); // HTTP/1.0
        
        if (!Storage::exists('/public/docs/bank_excel')) {
            Storage::makeDirectory('/public/docs/bank_excel');
        }
        $storage_path = storage_path('app/public/docs/bank_excel');
        $filePath = $storage_path.'/'.$filename.'.xlsx';

        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        $objWriter->save($filePath);

        return [ 'status' => 1,
                'file_path' => $filePath
                ];
    }

    /**
     * Display a pop up iframe for disburse check
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadBatchData(Request $request)
    {
        if ($request->get('eod_process')) {
            Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
            return back();
        }
        
        $custCode = $request->get('customer_code');
        $selectDate = $request->get('selected_date');
        $batchId = $request->get('batch_id');

        $data = $this->userRepo->lmsGetSentToBankInvToExcel($custCode, $selectDate, $batchId)->toArray();
        $sheet =  new PHPExcel();
        $sheet->getProperties()
                ->setCreator("Capsave")
                ->setLastModifiedBy("Capsave")
                ->setTitle("Customer Disbursment Excel")
                ->setSubject("Customer Disbursment Excel")
                ->setDescription("Customer Disbursment Excel")
                ->setKeywords("Customer Disbursment Excel")
                ->setCategory("Customer Disbursment Excel");
    
        $sheet->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Client Code')
                ->setCellValue('B1', 'Debit account no.')
                ->setCellValue('C1', 'Transaction type code')
                ->setCellValue('D1', 'Value date')
                ->setCellValue('E1', 'Amount')
                ->setCellValue('F1', 'Beneficary Name')
                ->setCellValue('G1', 'Beneficary Accunt no.')
                ->setCellValue('H1', 'IFSC code')
                ->setCellValue('I1', 'Customer Ref no.')
                ->setCellValue('J1', 'Beneficary email id')
                ->setCellValue('K1', 'Beneficiary mobile no.')
                ->setCellValue('L1', 'Remarks')
                ->setCellValue('M1', 'Payment Type')
                ->setCellValue('N1', 'Purpose code')
                ->setCellValue('O1', 'Bene a/c type')
                ->setCellValue('P1', 'Payable Location')
                ->setCellValue('Q1', 'Print branch name')
                ->setCellValue('R1', 'Mode of delivery')
                ->setCellValue('S1', 'Transaction currency')
                ->setCellValue('T1', 'BENE_ADD1')
                ->setCellValue('U1', 'BENE_ADD2')
                ->setCellValue('V1', 'BENE_ADD3')
                ->setCellValue('W1', 'BENE_ADD4')
                ->setCellValue('X1', 'Beneficiary ID')
                ->setCellValue('Y1', 'Remote Printing')
                ->setCellValue('Z1', 'Print Branch Location')
                ->setCellValue('AA1', 'Nature Of Payment')
                ->setCellValue('AB1', 'Bank Type');
        $rows = 2;

        foreach($data as $rowData){

            if ($rowData['lms_user']['user']['is_buyer'] == 2) {
                $benName = (isset($rowData['lms_user']['user']['anchor_bank_details']['acc_name'])) ? $rowData['lms_user']['user']['anchor_bank_details']['acc_name'] : '';
            } else {
                $benName =  (isset($rowData['lms_user']['bank_details']['acc_name'])) ? $rowData['lms_user']['bank_details']['acc_name'] : '';
            }

            if ($rowData['lms_user']['user']['is_buyer'] == 2) {
                $bank_name = (isset($rowData['lms_user']['user']['anchor_bank_details']['bank']['bank_name'])) ? $rowData['lms_user']['user']['anchor_bank_details']['bank']['bank_name'] : '';
            } else {
                $bank_name = (isset($rowData['lms_user']['bank_details']['bank']['bank_name'])) ? $rowData['lms_user']['bank_details']['bank']['bank_name'] : '';
            }

            if ($rowData['lms_user']['user']['is_buyer'] == 2) {
                $ifsc_code = (isset($rowData['lms_user']['user']['anchor_bank_details']['ifsc_code'])) ? $rowData['lms_user']['user']['anchor_bank_details']['ifsc_code'] : '';
            } else {
                $ifsc_code = (isset($rowData['lms_user']['bank_details']['ifsc_code'])) ? $rowData['lms_user']['bank_details']['ifsc_code'] : '';
            }

            if ($rowData['lms_user']['user']['is_buyer'] == 2) {
                $benAcc = (isset($rowData['lms_user']['user']['anchor_bank_details']['acc_no'])) ? $rowData['lms_user']['user']['anchor_bank_details']['acc_no'] : '';
            } else {
                $benAcc = (isset($rowData['lms_user']['bank_details']['acc_no'])) ? $rowData['lms_user']['bank_details']['acc_no'] : '';
            }

            if (isset($rowData['disbursal_batch']) && isset($rowData['disbursal_batch']['disbursal_api_log'])) {
                if ($rowData['disbursal_batch']['disbursal_api_log']['bank_type'] == 1) {
                    $bank_type = "IDFC";
                }
                if ($rowData['disbursal_batch']['disbursal_api_log']['bank_type'] == 2) {
                    $bank_type = "Kotak";
                }
            }

            $sheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $rows, $rowData['Client_Code'] ?? 'XYZ')
                ->setCellValue('B' . $rows, $rowData['Debit_Acct_No'] ?? '')
                ->setCellValue('C' . $rows, $rowData['Trans_Type_Code'] ?? '')
                ->setCellValue('D' . $rows, $rowData['disburse_date'] ?? '')
                ->setCellValue('E' . $rows, $rowData['total_disburse_amount'] ?? '')
                ->setCellValue('F' . $rows, $benName ?? '')
                ->setCellValue('G' . $rows, $benAcc ?? '')
                ->setCellValue('H' . $rows, $ifsc_code ?? '')
                ->setCellValue('I' . $rows, $rowData['RefNo'] ?? '')
                ->setCellValue('J' . $rows, $rowData['user']['email'] ?? '')
                ->setCellValue('K' . $rows, $rowData['user']['mobile_no'] ?? '')
                ->setCellValue('L' . $rows, $rowData['Remarks'] ?? '')
                ->setCellValue('M' . $rows, $rowData['Mode_of_Pay'] ?? '')
                ->setCellValue('N' . $rows, $rowData['column'] ?? '')
                ->setCellValue('O' . $rows, $rowData['column'] ?? '')
                ->setCellValue('P' . $rows, $rowData['column'] ?? '')
                ->setCellValue('Q' . $rows, $rowData['column'] ?? '')
                ->setCellValue('R' . $rows, $rowData['column'] ?? '')
                ->setCellValue('S' . $rows, $rowData['column'] ?? '')
                ->setCellValue('T' . $rows, $rowData['column'] ?? '')
                ->setCellValue('U' . $rows, $rowData['column'] ?? '')
                ->setCellValue('V' . $rows, $rowData['column'] ?? '')
                ->setCellValue('W' . $rows, $rowData['column'] ?? '')
                ->setCellValue('X' . $rows, $rowData['column'] ?? '')
                ->setCellValue('Y' . $rows, $rowData['column'] ?? '')
                ->setCellValue('Z' . $rows, $rowData['column'] ?? '')
                ->setCellValue('AA' . $rows, $rowData['Nature_of_Pay'] ?? 'MPYMT')
                ->setCellValue('AB' . $rows,  isset($bank_type) ? $bank_type : "");

            $rows++;
        }
        
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="download_Excel.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        $objWriter->save('php://output');
    }
    
    public function uploadBulkCsvInvoice(Request $request)
    {  
        $date = Carbon::now();
        $id = Auth::user()->user_id; 
        $attributes = $request->all();
        $program_name  = explode(',',$attributes['program_name']);
        $getAnchor = $this->userRepo->getAnchorById((int) $attributes['anchor_name']);
        
        if(($getAnchor->is_phy_blk_inv_req === '1') && (empty($attributes['file_image_id']))) {
            Session::flash('error', 'For this Anchor please Upload Invoice Copy');
            return back(); 
        }
        
        $prgm_id        =   $program_name[0];
        $prgm_limit_id   =   $program_name[1];
        $batch_id =  self::createBatchNumber(6);
        $uploadData = Helpers::uploadInvoiceFile($attributes, $batch_id); 
        if($uploadData['status']==0)
        {
             Session::flash('error', $uploadData['message']);
             return back(); 
        }
        $userFile = $this->docRepo->saveFile($uploadData);  ///Upload csv
        $userFile['batch_no'] =  $batch_id;
        if($userFile)
       {
           $resFile =  $this->invRepo->saveInvoiceBatch($userFile);
           if($resFile)
           {
              $uploadData = Helpers::uploadZipInvoiceFile($attributes, $batch_id); ///Upload zip file
              if(!empty($uploadData) && $uploadData['status']==0)
             {
               Session::flash('error', $uploadData['message']);
               return back(); 
             }
            //   if($uploadData)
            //   {   
                if(!empty($uploadData)) {
                    $zipBatch  =   self::createBatchNumber(6);
                    $uploadData['batch_no'] = $zipBatch;
                    $uploadData['parent_bulk_batch_id'] =  $resFile->invoice_bulk_batch_id;
                    $resZipFile =  $this->invRepo->saveInvoiceZipBatch($uploadData);
                }
                //   if($resZipFile)
                //   {
                    $csvPath = storage_path('app/public/'.$userFile->file_path);
                    $handle = fopen($csvPath, "r");
                    $data = fgetcsv($handle, 1000, ",");
                    if(count($data) < 5 || count($data) > 6)
                    {
                          Session::flash('error', 'Please check Csv file format.');
                          return back(); 
                    }
                    
                    $csvPath1 = storage_path('app/public/'.$userFile->file_path);
                    $handle1 = fopen($csvPath1, "r");
                    $data1 = fgetcsv($handle1, 1000, ",");
                    $key=0;
                    $ins = [];
                    $dataAttr[] ="";
                   $multiValiChk =  InvoiceTrait::multiValiChk($handle1,$prgm_id,$attributes['anchor_name'],$customerId=null);
               
                if($multiValiChk['status']==0)
                {

                    Session::flash('multiVali', $multiValiChk);
                    return back();   
                }
            
                  while(($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
                    {   
                      
                        $inv_no  =   $data[1]; 
                        $inv_date  =   $data[2]; 
                        $amount  =   $data[3]; 
                        $file_name  =   $data[4];
                        $dataAttr['cusomer_id']  =   $data[0]; 
                        $dataAttr['inv_no']  =   $data[1]; 
                        $dataAttr['inv_date']  =   $data[2]; 
                        $dataAttr['amount']  =   $data[3]; 
                        $dataAttr['file_name']  =   $data[4];
                        $dataAttr['anchor_id']  =   $attributes['anchor_name'];
                        $dataAttr['prgm_id']  = $dataAttr['program_id'] =  $prgm_id;
                        $chlLmsCusto  = InvoiceTrait::getLimitProgram($dataAttr);
                        $getPrgm  = $this->application->getProgram($prgm_id);
                        if($chlLmsCusto['status']==0)
                        {
                           Session::flash('error', $chlLmsCusto['message']);
                           return back(); 
                        }
                        ////// for validation paramiter here//////
                        $dataAttr['user_id']  =  $chlLmsCusto['user_id'];
                        $dataAttr['app_id']  =   $chlLmsCusto['app_id'];
                        $dataAttr['biz_id']  =   $chlLmsCusto['biz_id'];
                        $dataAttr['tenor']  =   $chlLmsCusto['tenor'];
                        $dataAttr['old_tenor']  =   $chlLmsCusto['tenor_old_invoice'];
                        $dataAttr['prgm_offer_id']  =   $chlLmsCusto['prgm_offer_id'];
                        $dataAttr['approval']  =   $getPrgm;

                        $invoice_amount = str_replace(',', '', $dataAttr['amount']);
                        // $invUtilizedAmt = Helpers::anchorSupplierUtilizedLimitByInvoice($dataAttr['user_id'], $dataAttr['anchor_id']);
                        // $totalProductLimit = Helpers::getTotalProductLimit($dataAttr['app_id'], $productId = 1);
                        
                        $marginAmt = Helpers::getOfferMarginAmtOfInvoiceAmt($dataAttr['prgm_offer_id'], $dataAttr['amount']);
                        // $limit =   InvoiceTrait::ProgramLimit($dataAttr);
                        // $sum   =   InvoiceTrait::invoiceApproveLimit($dataAttr);
                        // $remainAmount = $limit - $sum;
                        
                        // if ($marginAmt > $remainAmount) {
                        //     Session::flash('error', 'Invoice amount should not be greater than the remaining limit amount after excluding the margin amount for customer '.$dataAttr['cusomer_id']);
                        //     return back();
                        // }
                        
                        $getInvDueDate =  InvoiceTrait::getInvoiceDueDate($dataAttr); /* get invoice due date*/

                        if($getInvDueDate['status']==0)
                        {
                           Session::flash('error', $getInvDueDate['message']);
                           return back(); 
                        }
                        $dataAttr['inv_due_date']  =   $getInvDueDate['inv_due_date']; 
                        $error = InvoiceTrait::checkCsvFile($dataAttr);
                       
                        if($error['status']==0)
                        {
                           Session::flash('error', $error['message']);
                           return back(); 
                        }
                        $status_id =  $error['status_id'];
                        $comm_txt  =  $error['message'];
                        $error  =  $error['error'];
                      if($file_name)
                      {
                        $getImage =  Helpers::ImageChk($file_name,$batch_id);
                        if($getImage['status']==1)
                        {
                            
                           $FileDetail = $this->docRepo->saveFile($getImage); 
                           $FileId  = $FileDetail->file_id; 
                        }
                        else
                        {
                           $FileId  = Null; 
                           $comm_txt  =  $getImage['message']; 
                        }
                      }
                      else
                      {
                            $FileId  = Null; 
                      }
                        $userLimit = $chlLmsCusto['limit'];
                        $ins['anchor_id'] = $attributes['anchor_name'];
                        $ins['supplier_id'] = $dataAttr['user_id'];
                        $ins['program_id'] = $prgm_id;
                        $ins['prgm_offer_id'] = $dataAttr['prgm_offer_id'];
                        $ins['app_id'] = $dataAttr['app_id'];
                        $ins['biz_id'] = $dataAttr['biz_id'];
                        $ins['invoice_no'] = $inv_no;
                        $ins['tenor'] = $dataAttr['tenor'];
                        $ins['invoice_date'] = Carbon::createFromFormat('d-m-Y', $inv_date)->format('Y-m-d');
                        $ins['invoice_due_date'] = Carbon::createFromFormat('d-m-Y', $dataAttr['inv_due_date'])->format('Y-m-d');
                        $ins['pay_calculation_on'] = 2;
                        $ins['invoice_approve_amount'] = $amount;
                        $ins['comm_txt'] = $comm_txt;
                        $ins['status'] = $error;
                        $ins['status_id'] = $status_id;
                        $ins['file_id'] =  $FileId;
                        $ins['created_by'] =  $id;
                        $ins['created_at'] =  $date;
                        $ins['invoice_margin_amount'] = $marginAmt;
                        $key++;
                        $res =  $this->invRepo->saveInvoice($ins);                       
                    } 

                        $whereActivi['activity_code'] = 'upload_bulk_csv_Invoice';
                        $activity = $this->master->getActivity($whereActivi);
                        if(!empty($activity)) {
                            $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                            $activity_desc = 'Upload Bulk Invoice, (Manage Invoice)';
                            $arrActivity['app_id'] = null;
                            $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['data'=>$data, 'request'=>$request->all()]), $arrActivity);
                        } 
            
                         Session::flash('message', 'Invoice data successfully sent to under reviewer process');
                         return back();  
                     
                //   }
            //   }
           }
       }
      
    }
    

	public static function createBatchNumber($length = 6)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
       public static function accountClosure(Request $request)
       {
        
             $res = InvoiceTrait::getAccountClosure($request);
             if($res['status']==0)
             {
                Session::flash('error',$res['msg']);
                return back();
             }
             else
             {
                $master = \App::make('App\Inv\Repositories\Contracts\MasterInterface');
                $whereActivi['activity_code'] = 'account_closure';
                $activity = $master->getActivity($whereActivi);
                if(!empty($activity)) {
                    $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                    $activity_desc = 'Account Clousre in Limit Management (Manage Sanction Cases) '. null;
                    $arrActivity['app_id'] = null;
                    ActivityLogTrait::staticActivityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
                }                  
               Session::flash('message', 'Customer account has been successfully closed');
               return back();
             }
       }

    public function disbursalBatchRequest(Request $req) {
        try {

            if ($req->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            $flag = $req->get('flag') ?: null;
            $batchData = $this->lmsRepo->getallBatch();

            return view('backend.invoice.disbursal_batch_request')->with(['flag' => $flag, 'batchData' => $batchData]);
            } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function disbursalPaymentEnquiry(Request $request)
    {
        try {
            
            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            
            date_default_timezone_set("Asia/Kolkata");
            $disbursalBatchId = $request->get('disbursal_batch_id');
            $disburseDate = null;
            $transId = null;
            $createdBy = Auth::user()->user_id;
            $fundedDate = \Carbon\Carbon::now()->format('Y-m-d');
            $transDisbursalIds = [];
            $tranNewIds = [];
            
            $data = $this->lmsRepo->getdisbursalBatchByDBId($disbursalBatchId);
            if (!isset($data) || empty($data)) {
                return redirect()->route('backend_get_disbursal_batch_request')->withErrors('Something went wrong please try again.');
            } else {
                $disburseDate = date("Y-m-d", strtotime($data->disbursalOne->disburse_date));
                $reqData['txn_id'] = $data['disbursal_api_log']['txn_id'];
                $transId = $reqData['txn_id'];
            }


            if ($fundedDate != $disburseDate) {
                return redirect()->route('backend_get_disbursal_batch_request')->withErrors('funded date can not marked '.$fundedDate.'.');
            } else {
                $data = $data->toArray();
            }

            $message = [];

            if(!empty($reqData)) {
            
                $http_header = [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'txn_id' => $reqData['txn_id']
                    ];

                $header = [
                    'Maker_ID' => "CAPSAVE.M",
                    'Checker_ID' => "CAPSAVE.C1",
                    'Approver_ID' => "CAPSAVE.C2"
                    ];

                $params = [
                    'http_header' => $http_header,
                    'header' => $header,
                    'request' => $reqData
                    ];

                $idfcObj= new Idfc_lib();
                $result = $idfcObj->api_call(Idfc_lib::BATCH_ENQ, $params);
                if (isset($result['code'])) { 
                    if (isset($result['http_code']) && $result['http_code'] == 200) {
                        
                    } else {
                        $http_code = $result['code'] ? $result['code']  : $result['http_code']. ', ';
                        $message = $result['message'] ?? $result['message'];
                        Session::flash('message', 'Error : '. $http_code  .  $message);
                        return redirect()->back();
                    }
                }
                $fileDirPath = getPathByTxnId($transId);
                $time = date('y-m-d H:i:s');
                
                $result['result']['http_header'] = (is_array($result['result']['http_header'])) ? json_encode($result['result']['http_header']): $result['result']['http_header'];
                $fileContents = PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['url'].  PHP_EOL
                    .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['payload']  .PHP_EOL
                    .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['http_header']  .PHP_EOL
                    .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['response'] . PHP_EOL;
                $createOrUpdatefile = Helpers::uploadOrUpdateFileWithContent($fileDirPath, $fileContents, true);
                if(is_array($createOrUpdatefile)) {
                    $userFileSaved = $this->docRepo->saveFile($createOrUpdatefile)->toArray();
                } else {
                    $userFileSaved = $createOrUpdatefile;
                }
                
                $otherData['disbursal_batch_id'] = $disbursalBatchId;
                $otherData['txn_id'] = $transId;
                $otherData['bank_type'] = config('lms.BANK_TYPE')['IDFC'];
                $otherData['enq_txn_id'] = $transId;
                $otherData['disbursal_batch_id'] = $disbursalBatchId;
                $disbusalApiLogData = $this->createDisbusalApiLogData($userFileSaved, $result, $otherData);
                $createDisbusalApiLog = $this->lmsRepo->saveUpdateDisbursalApiLog($disbusalApiLogData);
                if ($createDisbusalApiLog) {
                    $disbursalApiLogId = $createDisbusalApiLog->disbursal_api_log_id;
                }
                if ($result['status'] == 'success') {

                    // $invoiceIds = $this->lmsRepo->findInvoicesByUserAndBatchId(['disbursal_batch_id' => $disbursalBatchId])->toArray();
                    $disbursalIds = $this->lmsRepo->findDisbursalByUserAndBatchId(['disbursal_batch_id' => $disbursalBatchId])->toArray();
                    if ($disbursalIds) {
                        
                        if ($result['result']['body']['TranID_Status'] == 'SUCCESS') {

                            $updateDisbursal = $this->lmsRepo->updateDisburseByUserAndBatch([
                                    // 'status_id' => config('lms.DISBURSAL_STATUS')['DISBURSED'],
                                    'funded_date' => (!empty($fundedDate)) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$fundedDate))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s')
                                ], $disbursalIds);

                            foreach ($result['result']['body']['Transaction'] as $key => $value) {
                                
                                if ($value['RefStatus'] == 'SUCCESS') {
                                    $disburseStatus = config('lms.DISBURSAL_STATUS')['DISBURSED'];
                                } else if($value['RefStatus'] == 'Pending for Authorization' || $value['RefStatus'] == 'Pending Auth' || $value['RefStatus'] == 'UNDER PROCESS') {
                                    $disburseStatus = config('lms.DISBURSAL_STATUS')['SENT_TO_BANK'];
                                } else if($value['RefStatus'] == 'Rejected by Checker' || $value['RefStatus'] == 'Rejected' || $value['RefStatus'] == 'Rejected by Authorizer' || $value['RefStatus'] == 'Rejected online' || $value['RefStatus'] == 'Cancelled') {
                                    $disburseStatus = config('lms.DISBURSAL_STATUS')['REJECT'];
                                } else if($value['RefStatus'] == 'FAILED') {
                                    $disburseStatus = config('lms.DISBURSAL_STATUS')['FAILED_DISBURSMENT'];
                                } else {
                                    $disburseStatus = config('lms.DISBURSAL_STATUS')['SENT_TO_BANK'];
                                } 
                                
                                $transDisbursalIds = $this->lmsRepo->findDisbursalByUserAndBatchId(['ref_no' => $value['RefNo']])->toArray();
                                $updateDisbursalByTranId = $this->lmsRepo->updateDisbursalByTranId([
                                        'status_id' => $disburseStatus,
                                        'tran_id' => $value['UTR_No']
                                    ], ['ref_no' => $value['RefNo']]);
                                foreach ($transDisbursalIds as $key => $value1) {
                                    $this->lmsRepo->createDisbursalStatusLog($value1, $disburseStatus, '', $createdBy);
                                    
                                    $invoiceDisburseIds = $this->lmsRepo->findInvoiceDisburseByDisbursalId(['disbursal_id' => $value1])->toArray();
                                    $updateInvoiceDisburseStatus = $this->lmsRepo->updateInvoiceDisburseStatus($invoiceDisburseIds, $disburseStatus);
                                    
                                    $invoiceIds = $this->lmsRepo->findInvoicesByUserAndBatchId(['disbursal_id' => $value1])->toArray();

                                    $updateInvoiceStatus = $this->lmsRepo->updateInvoicesStatus($invoiceIds, $disburseStatus);
                                    foreach ($invoiceIds as $key => $value2) {
                                        $this->invRepo->saveInvoiceStatusLog($value2, $disburseStatus);
                                    }
                                }

                                if ($value['RefStatus'] == 'SUCCESS') {
                                    $tranNewIds = array_merge($tranNewIds, $transDisbursalIds);
                                }
                                $message[] = $value['RefNo'].' is '.$value['RefStatus'];
                            }
                        } else {
                            $http_code = $result['http_code'] ? $result['http_code'] . ', ' : $result['code'];
                            $message = $result['result']['body']['Transaction'] ?? $result['message'];
                            Session::flash('message', 'Error : '. 'HTTP Code '. $http_code  .  $message);
                            return redirect()->route('backend_get_disbursal_batch_request');
                        }
                    }  
                    $updateTransaction = $this->updateTransactionInvoiceDisbursed($tranNewIds, $fundedDate);
                } else {
                    $http_code = $result['http_code'] ? $result['http_code'] . ', ' : $result['code'];
                    $message = $result['message'] ?? $result['message'];
                    Session::flash('message', 'Error : '. 'HTTP Code '. $http_code  .  $message);
                    return redirect()->route('backend_get_disbursal_batch_request');
                   
                }
                 
            }
            
            $disbureIds = $this->lmsRepo->findDisbursalByUserAndBatchId(['status_id' => config('lms.DISBURSAL_STATUS')['SENT_TO_BANK'], 'disbursal_batch_id' => $disbursalBatchId])->toArray();
            if(empty($disbureIds)) {
                $updateDisbursal = $this->lmsRepo->updateDisbursalBatchById(['batch_status' => 2], $disbursalBatchId);
            }
                    
            Session::flash('message', implode(', ', $message));
            return redirect()->back()->withErrors('message',trans('backend_messages.batch_disbursed'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }             
    }

    public function viewUploadedFile(Request $request){
        try {
            
            $file_id = $request->get('file_id');
            $fileData = $this->docRepo->getFileByFileId($file_id);
            // dd($fileData);
            $filePath = 'app/public/'.$fileData->file_path;
            $path = storage_path($filePath);
           
            if (file_exists($path)) {
                return response()->file($path);
            }else{
                exit('Requested file does not exist on our server!');
            }
        } catch (Exception $ex) {                
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }

    }

    public function rollbackDisbursalBatchRequest(Request $request)
	{
		try 
        {
            $disbursalIdsArr = [];
            $invoiceIdsArr = [];
            if($request->has('disbursal_batch_id')){
                $disbursalBatchId = $request->get('disbursal_batch_id');
            }
            
            if($disbursalBatchId && is_numeric($disbursalBatchId)){
                $disbursalBatchData = $this->lmsRepo->getdisbursalBatchByDBId($disbursalBatchId);
                $disbursalData = $this->lmsRepo->getDisbursalByDBId($disbursalBatchId);
                foreach($disbursalData as $data){
                    $disbursalIdsArr[] = $data->disbursal_id;
                }
                $disbursedInvoices = $this->lmsRepo->getInvoiceDisbursed($disbursalIdsArr);
                foreach($disbursedInvoices as $data){
                    $invoiceIdsArr[] = $data->invoice_id;
                }

                
                if($disbursalBatchData && $disbursalData && $disbursedInvoices){
                    
                    $this->lmsRepo->deleteInvoiceStatusLogByInvIdArr($invoiceIdsArr);
                    foreach($invoiceIdsArr as $invoice_id){
                        $this->lmsRepo->updateInvoiceStatus($invoice_id, 9);
                    }
                    $this->lmsRepo->deleteInvoiceDisbursed($disbursalIdsArr);
                    $this->lmsRepo->deleteDisbursalStatusLogByDidArr($disbursalIdsArr);
                    $this->lmsRepo->deleteDisbursalByDBId($disbursalBatchId);
                    $this->lmsRepo->deleteDisbursalBatchByDBId($disbursalBatchId);

                    $whereActivi['activity_code'] = 'rollback_disbursal_batch_request';
                    $activity = $this->master->getActivity($whereActivi);
                    if(!empty($activity)) {
                        $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                        $activity_desc = 'Rollback Disbursal Request tab (Manage Invoice)';
                        $arrActivity['app_id'] = null;
                        $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
                    }                     
                    
                    Session::flash('message', 'Disbursal request has been successfully rollbacked.');
                    return redirect()->route('backend_get_disbursal_batch_request');
                }
                
                Session::flash('message', 'Record Not Found / Already deleted!');
                return redirect()->route('backend_get_disbursal_batch_request');
            }            
            
            Session::flash('error', 'Invalid Request');
            return redirect()->route('backend_get_disbursal_batch_request');
        } catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}  
	}

    public function onlineDisbursalRollback(Request $req) {
        try {
            
            if ($req->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }

            $tInv = 0;
            $appId = '';
            $userId = '';
            $fullCustName = '';
            $invNoString ='';
            $tAmt = 0;
            $invNo = [];
            $idfc_res_text = $bankType = '';
            $disbursalBatchId = $req->get('disbursal_batch_id');
            $disbursalBatchData = $this->lmsRepo->getdisbursalBatchByDBId($disbursalBatchId);
            if(isset($disbursalBatchData->disbursal_api_log) && !empty($disbursalBatchData->disbursal_api_log)){
                $latestData = $disbursalBatchData->disbursal_api_log;
                $idfc_res_text = $latestData->res_text;
                $bankType = $latestData->bank_type;
            }
            $disbursal = $disbursalBatchData->disbursal ?? [];
            //$tCust = $disbursal->count();
            $tCust = count($disbursal);
            if($tCust > 0) {
            $tAmt = number_format($disbursal->sum('disburse_amount'),2);
            foreach($disbursal as $data){
                foreach($data->invoice_disbursed as $invData) {
                    $invNo[] = $invData->invoice->invoice_no ?? '';
                    $appId = $invData->invoice->lms_user->app_id ?? '';
                }
                $tInv += $data->invoice_disbursed->count();
                $userId = $data->user_id;
            }
            
            $invNoString = implode(', ',$invNo); 
            if($appId){
                $appData = $this->appRepo->getAppDataByAppId($appId); 
            }
            $custName = HelperS::getUserInfo($userId);
            $fullCustName = $custName->f_name." ".$custName->l_name;
            return view('backend.invoice.online_disbursal_rollback')->with(['disbursal_batch_id' => $disbursalBatchId, 'fullCustName' => $fullCustName, 'invNoString' => $invNoString, 'tInv' => $tInv, 'tAmt' => $tAmt, 'tCust' => $tCust, 'appId' => $appData->app_code ?? '', 'res_text' => $idfc_res_text, 'bankType' => $bankType]);

            } else {

            return view('backend.invoice.online_disbursal_rollback')
                ->with(['disbursal_batch_id' => $disbursalBatchId,
                    'fullCustName' => $fullCustName,
                    'invNoString' => $invNoString,
                    'tInv' => $tInv, 'tAmt' => $tAmt,
                    'tCust' => $tCust,
                    'appId' => $appData->app_code ?? '',
                    'res_text' => $idfc_res_text,
                    'bankType' => $bankType]);

            }

            } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Display a pop up iframe for disburse check
     *
     * @return \Illuminate\Http\Response
     */
    public function iframeUpdateInvoiceChrg(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $invoiceData = $this->invRepo->getInvoiceById($invoiceId);
        $chargeData = $this->invRepo->getInvoiceProcessingFee(['invoice_id' =>$invoiceId]);
        $offerData = $this->appRepo->getOfferData(['prgm_offer_id' =>$invoiceData->prgm_offer_id]);
        $chrgData = $this->appRepo->getInvoiceProcessingFeeCharge();
        $getPercentage  = $this->lmsRepo->getLastGSTRecord();

        $tax_value  =0; 

        if(!isset($chargeData->chrg_value) && $offerData->invoice_processingfee_value) {
            $valueAmt = $offerData->invoice_processingfee_value;
        } else if (isset($chargeData->chrg_value) && $chargeData->chrg_value) {
            $valueAmt = $chargeData->chrg_value;
        }

        if(!isset($chargeData->chrg_type) && $offerData->invoice_processingfee_type == 1) {
            $typeFlag = 1;
        } else if (isset($chargeData->chrg_type) && $chargeData->chrg_type == 1 ) {
            $typeFlag = 1;
        } else if(!isset($chargeData->chrg_type) && $offerData->invoice_processingfee_type == 2) {
            $typeFlag = 2;
        } else if (isset($chargeData->chrg_type) && $chargeData->chrg_type == 2 ) {
            $typeFlag = 2;
        }
        $marginAmt = $this->calMargin($invoiceData->invoice_approve_amount, $invoiceData->program_offer->margin);
        $principleAmt = $invoiceData->invoice_approve_amount - $marginAmt;
        if (isset($typeFlag) && $typeFlag == 2) {
            $processingFee = $this->calPercentage($principleAmt, $valueAmt);
        } else {
            $processingFee = $valueAmt;
        }

        if($chrgData->is_gst_applicable == 1) {
            if($getPercentage)
            {
                $tax_value  = $getPercentage['tax_value'];
            }
            else
            {
                $tax_value  =0; 
            }
        }

        $fWGst = round((($processingFee*$tax_value)/100),2);
        $gstChrgValue = $processingFee + $fWGst;
        // dd($invoiceData, $marginAmt);
        return view('backend.invoice.update_invoice_charge')
                ->with([
                    'invoiceId' => $invoiceId,
                    'chargeData' => $chargeData,
                    'offerData' => $offerData,
                    'chrgData' => $chrgData,
                    'gstChrgValue' => $gstChrgValue,
                    'invoiceData' => $invoiceData,
                    'marginAmt' => $marginAmt,
                    'getPercentage' => $getPercentage,
                    'processingFee' => $processingFee,
                ]);;              
    }

    /* update invoice amount  */

    public function saveInvoiceProcessingFee(Request $request) {
        $id = Auth::user()->user_id;
        $invoiceId = $request->invoice_id;
        $data['charge_id'] = 12; // processing fee charge id
        $data['chrg_value'] = $request->chrg_value;
        $data['chrg_type'] = $request->chrg_type;
        $data['gst_chrg_value'] = $request->invoice_gst_chrg_value;
        $data['is_active'] = 1;
        $data['deductable'] = 1;

        $invoiceData = $this->invRepo->getInvoiceById($invoiceId);
        
        if($request->chrg_type == 2&& $data['chrg_value'] > 50) {
            Session::flash('message', 'Charge value can not be greater than 50%.');
            return redirect()->route('backend_get_approve_invoice');
        }  
        if($request->chrg_type == 1&& $data['chrg_value'] > $invoiceData->invoice_approve_amount) {
            Session::flash('message', 'Charge value can not be greater than invoice approve amount. ');
            return redirect()->route('backend_get_approve_invoice');
        }                
        // $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        // $data['created_by'] = Auth::user()->user_id;
        // $data['created_at'] = $curData;

        $res = $this->invRepo->updateInvoiceCharge($data, $invoiceId);

       if ($res) {
            $whereActivi['activity_code'] = 'update_invoice_chrg';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Save Invoice Processing Fee, Approved tab (Manage Invoice)';
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
            }             
            Session::flash('message', 'processing fee successfully Updated ');
            return redirect()->route('backend_get_approve_invoice');
        } else {
            Session::flash('message', 'Something wrong, Tenor is not Updated');
            return redirect()->route('backend_get_approve_invoice');
        }
    }

    public function saveInvoiceTenor(Request $request) {
        $id = Auth::user()->user_id;
        $data['tenor'] = $request->tenor_invoice_tenor;
        $data['is_tenor_mannual'] = 1;
        $invoiceId = $request->tenor_invoice_id;
        $invoiceDetail = $this->invRepo->getInvoiceById($invoiceId);
        $data['invoice_due_date'] = date('Y-m-d', strtotime(str_replace('/','-',$invoiceDetail->invoice_date). "+ $request->tenor_invoice_tenor Days"));
        if (strtotime($data['invoice_due_date']) <= strtotime(date("Y-m-d"))) {
            Session::flash('message', 'Invoice due date should be greater than current date.');
            return back();
        }
        $res = $this->invRepo->updateInvoiceTenor($data, $invoiceId);
        
       if ($res) {
            $whereActivi['activity_code'] = 'update_invoice_tenor';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Update Invoice Tenor (Manage Invoice)';
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
            }            
            Session::flash('message', 'Invoice Tenor successfully Updated ');
            return back();
        } else {
            Session::flash('message', 'Something wrong, Tenor is not Updated');
            return back();
        }
    }

    /**
     * Display a pop up iframe for KotakbankAPiOnline
     *
     * @return \Illuminate\Http\Response
     */
    public function kotakDisburseOnline(Request $request)
    {
        //\DB::beginTransaction();
        try {
            date_default_timezone_set("Asia/Kolkata");
            $currentTimeHour = \Carbon\Carbon::now()->format('H');
            $validateTimeHour = config('lms.DISBURSAL_TIME_VALIDATE');
            $invoiceIds = $request->get('invoice_ids');
            $disburseDateCal = $request->get('value_date');
            // $disburseDate =  \Helpers::getSysStartDate();
            $disburseDate = \Carbon\Carbon::createFromFormat('d/m/Y', $disburseDateCal)->setTimezone(config('common.timezone'))->format('Y-m-d');
            $disburseType = config('lms.DISBURSE_TYPE')['ONLINE'];
            $creatorId = Auth::user()->user_id;

            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            // if (date('H') >= $validateTimeHour) {
            //     Session::flash('error', 'Disbursment can not be done after '. Carbon::createFromFormat('H', $validateTimeHour)->format('g:i A'));
            //     return redirect()->route('backend_get_disbursed_invoice');
            // }
            if (empty($invoiceIds)) {
                return redirect()->route('backend_get_disbursed_invoice')->withErrors(trans('backend_messages.noSelectedInvoice'));
            }

            $record = array_filter(explode(",", $invoiceIds));
            $allrecords = array_unique($record);
            $allrecords = array_map('intval', $allrecords);
            $countInvoiceId = count($allrecords);
            if ($countInvoiceId > 1) {
                return redirect()->route('backend_get_disbursed_invoice')->withErrors('Please select only one invoice');
            }
            $allinvoices = $this->lmsRepo->getInvoices($allrecords)->toArray();


            foreach ($allinvoices as $inv) {
                $disbursedInvoiceId = $this->lmsRepo->findInvoiceDisbursedInvoiceIdByInvoiceId($inv['invoice_id']);

                if ($disbursedInvoiceId->count() > 0) {
                    return redirect()->route('backend_get_disbursed_invoice')->withErrors('Invoice ' . $inv['invoice_no'] . ' already under process of disbursment');
                } else if ($inv['supplier']['is_buyer'] == 2 && empty($inv['supplier']['anchor_bank_details'])) {
                    return redirect()->route('backend_get_disbursed_invoice')->withErrors(trans('backend_messages.noBankAccount'));
                } elseif ($inv['supplier']['is_buyer'] == 1 && empty($inv['supplier_bank_detail'])) {
                    return redirect()->route('backend_get_disbursed_invoice')->withErrors(trans('backend_messages.noBankAccount'));
                }
            }

            $supplierIds = $this->lmsRepo->getInvoiceSupplier($allrecords)->toArray();
            $fundedAmount = 0;
            $interest = 0;
            $disburseAmount = 0;
            $totalInterest = 0;
            $totalProcessingFee = 0;
            $totalFunded = 0;
            $totalMargin = 0;
            $exportData = [];
            $invoiceDisbursedIds = [];
            $disbursalIds = [];
            $disbursalData = [];
            $otherData = [];
            $transId = _getRand(20);
            $requestData = [];
            foreach ($supplierIds as $userid) {
                //$refNo = _getRand(20);
                $disburseAmount = 0;
                foreach ($allinvoices as $invoice) {
                    if ($invoice['supplier_id'] == $userid) {

                        $interest = 0;
                        $processingFee = 0;
                        $margin = 0;

                        $tenor = $this->calculateTenorDays($invoice);
                        $margin = $this->calMargin($invoice['invoice_approve_amount'], $invoice['program_offer']['margin']);
                        $fundedAmount = $invoice['invoice_approve_amount'] - $margin;
                        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $disburseDate)) {
                            $str_to_time_date = strtotime($disburseDate);
                        } else {
                            $str_to_time_date = strtotime(\Carbon\Carbon::createFromFormat('d/m/Y', $disburseDate)->setTimezone(config('common.timezone'))->format('Y-m-d'));
                        }
                        $bankId = $invoice['program_offer']['bank_id'];
                        $oldIntRate = $invoice['program_offer']['interest_rate'] - $invoice['program_offer']['base_rate'];
                        $interestRate = ($invoice['is_adhoc'] == 1) ? (float)$invoice['program_offer']['adhoc_interest_rate'] : (float)$invoice['program_offer']['interest_rate'];
                        $Obj = new ManualApportionmentHelper($this->lmsRepo);
                        $bankRatesArr = $Obj->getBankBaseRates($bankId);
                        if ($bankRatesArr && $invoice['is_adhoc'] != 1) {
                            $actIntRate = $Obj->getIntRate($oldIntRate, $bankRatesArr, $str_to_time_date);
                        } else {
                            $actIntRate = $interestRate;
                        }
                        if ($invoice['program_offer']['benchmark_date'] == 1) {
                            $tenor = $this->calDiffDays($invoice['invoice_due_date'], $disburseDate);
                        }

                        $tInterest = $this->calInterest($fundedAmount, $actIntRate, $tenor);
                        // if (isset($invoice['processing_fee']['chrg_type']) && $invoice['processing_fee']['chrg_type'] == 2) {
                        //     $processingFee = $this->calPercentage($fundedAmount, $invoice['processing_fee']['chrg_value']);
                        // } else {
                        //     $processingFee = $invoice['processing_fee']['chrg_value'];

                        // }
                        $processingFee = ($invoice['processing_fee']) ? $invoice['processing_fee']['gst_chrg_value'] : 0;

                        $prgmWhere = [];
                        $prgmWhere['prgm_id'] = $invoice['program_id'];
                        $prgmData = $this->appRepo->getSelectedProgramData($prgmWhere, ['interest_borne_by']);

                        if (isset($prgmData[0]) && $prgmData[0]->interest_borne_by == 2 && $invoice['program_offer']['payment_frequency'] == 1) {
                            $interest = $tInterest;
                        }

                        $totalInterest += $interest;
                        $totalMargin += $margin;
                        $amount = round($fundedAmount - $interest - $processingFee, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
                        $disburseAmount += $amount;


                        $disbursalData['invoice'] = $invoice;
                    }
                }
                if ($disburseType == 1) {
                    $modePay = ($disburseAmount < 200000) ? 'NEFT' : 'RTGS';
                    $userData = $this->lmsRepo->getUserBankDetail($userid)->toArray();
                    $bank_account_id = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['bank_account_id'] : $userData['supplier_bank_detail']['bank_account_id'];
                    $bank_name = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['bank']['bank_name'] : $userData['supplier_bank_detail']['bank']['bank_name'];
                    $ifsc_code = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['ifsc_code'] : $userData['supplier_bank_detail']['ifsc_code'];
                    $acc_no = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['acc_no'] : $userData['supplier_bank_detail']['acc_no'];
                    $acc_name = ($userData['is_buyer'] == 2) ? $userData['anchor_bank_details']['acc_name'] : $userData['supplier_bank_detail']['acc_name'];
                    $exportData[$userid]['RefNo'] = $requestData[$userid]['MessageId'] = $transId; //Message Id
                    $requestData[$userid]['MsgSource'] = config('lms.KOTAK_MSG_SOURCE'); //'ABCCOMPANY'; //Message Source Code
                    $exportData[$userid]['Client_Code'] = $requestData[$userid]['ClientCode'] = config('lms.KOTAK_CLIENT_CODE'); //Client Code
                    $requestData[$userid]['BatchRefNmbr'] = $transId;
                    $requestData[$userid]['InstRefNo'] =  $transId; //Inst Ref No.
                    $requestData[$userid]['TransId'] =  $transId; //Inst transId No.
                    $exportData[$userid]['Nature_of_Pay'] = $requestData[$userid]['MyProdCode'] =  config('lms.KOTAK_MYPRODCODE'); //'WPAY'; //My product code
                    $exportData[$userid]['Amount'] = $requestData[$userid]['TxnAmnt'] = "$disburseAmount"; //Transaction Amount
                    $exportData[$userid]['Debit_Acct_No'] = $requestData[$userid]['AccountNo'] =  config('lms.KOTAK_DEBIT_BANK')['DEBIT_ACC_NO'];  //Client Debit account Number;
                    $requestData[$userid]['DrRefNmbr'] = '123';    //Debit reference Number
                    $exportData[$userid]['Remarks'] = $requestData[$userid]['DrDesc'] = 'Testing';  //Debit Description
                    $exportData[$userid]['Value_Date'] = $requestData[$userid]['PaymentDt'] = $disburseDate; //Payment Date
                    if (config('lms.KOTAK_UAT_ACTIVE') == 1) {
                        $exportData[$userid]['Ben_IFSC'] = $requestData[$userid]['RecBrCd'] = config('lms.KOTAK_CREDIT_BANK')['BEN_IFSC'];  //IFSC Code for beneficiary branch
                        $exportData[$userid]['Ben_Acct_No'] = $requestData[$userid]['BeneAcctNo'] = config('lms.KOTAK_CREDIT_BANK')['BEN_ACC_NO']; //Beneficiary Account Number
                        $exportData[$userid]['Ben_Name'] = $requestData[$userid]['BeneName'] = config('lms.KOTAK_CREDIT_BANK')['BEN_ACC_NAME']; //Beneficiary Name
                        $exportData[$userid]['Ben_Email'] = $requestData[$userid]['BeneEmail'] = config('lms.KOTAK_CREDIT_BANK')['BEN_EMAIL']; //Beneficiary Email ID
                    } else {
                        $exportData[$userid]['Ben_IFSC'] = $requestData[$userid]['RecBrCd'] = (strtolower($bank_name) == 'kotak mahindra bank') ? null : $ifsc_code;  //IFSC Code for beneficiary branch
                        $exportData[$userid]['Ben_Acct_No'] = $requestData[$userid]['BeneAcctNo'] = $acc_no; //Beneficiary Account Number
                        $exportData[$userid]['Ben_Name'] = $requestData[$userid]['BeneName'] = $acc_name; //Beneficiary Name
                        $exportData[$userid]['Ben_Email'] = $requestData[$userid]['BeneEmail'] = $disbursalData['invoice']['supplier']['email']; //Beneficiary Email ID
                    }
                    $exportData[$userid]['Ben_Mobile'] = $requestData[$userid]['BeneMb'] = $disbursalData['invoice']['supplier']['mobile_no']; //Beneficiary Mobile No.
                    $modePay = (strtolower($bank_name) == 'kotak mahindra bank') ? 'IFT' : $modePay;
                    $exportData[$userid]['Mode_of_Pay'] = $requestData[$userid]['PayMode']  = $modePay;

                    $requestData[$userid]['Enrichment'] = 'invoice disbursal via kotak bank online'; //Enrichment details which needs to be shared with vendor in the invoices
                }
            }
            //dd($requestData,$exportData);
            if ($disburseType == 1 && !empty($allrecords)) {
                $header = [
                    'Maker_ID : CAPSAVE.M',
                    'Checker_ID : CAPSAVE.C1',
                    'Approver_ID : CAPSAVE.C2'
                ];
                $KotakObj = \App::make('App\Libraries\Kotak_lib');
                $getResponse = false;
                $result = $KotakObj->callPaymentApi($requestData, $header, $getResponse);
                if ($getResponse) {
                    dd($result);
                }
                //dd($result);
                if (isset($result['code'])) {
                    if (isset($result['http_code']) && $result['http_code'] == 200) {
                    } else {
                        $http_code = $result['code'] ? $result['code'] . ', '  : $result['http_code'] . ', ';
                        $message = $result['message'] ?? $result['message'];
                        $message .= isset($result['SOAP-ENV_Body']["ns0_Payment"]) ? "Status Code :" . $result['SOAP-ENV_Body']["ns0_Payment"]['ns0_AckHeader']['ns0_StatusCd'] . ',' : '';
                        $message .= isset($result['SOAP-ENV_Body']["ns0_Payment"]) ? "Message Id :" . $result['SOAP-ENV_Body']["ns0_Payment"]['ns0_AckHeader']['ns0_MessageId'] . ', ' : '';
                        $message .= isset($result['SOAP-ENV_Body']["ns0_Payment"]) ? "Status Remarks :" . $result['SOAP-ENV_Body']["ns0_Payment"]['ns0_AckHeader']['ns0_StatusRem'] . ', ' : '';
                        Session::flash('message', 'Error : ' . $http_code  .  $message);
                        return redirect()->route('backend_get_disbursed_invoice');
                    }
                }
                //     $transIds = _getRand(20);
                $fileDirPath = getPathByTxnId($transId);
                $time = date('y-m-d H:i:s');

                $result['result']['http_header'] = (is_array($result['result']['http_header'])) ? json_encode($result['result']['http_header']) : $result['result']['http_header'];
                $fileContents = PHP_EOL . ' Log  ' . $time . PHP_EOL . "URL = " . $result['result']['url'] .  PHP_EOL
                    . PHP_EOL . ' Log  ' . $time . PHP_EOL . "Request Data = " . $result['result']['payload']  . PHP_EOL
                    . PHP_EOL . ' Log  ' . $time . PHP_EOL . "Request Header = " . $result['result']['http_header']  . PHP_EOL
                    . PHP_EOL . ' Log  ' . $time . PHP_EOL . "Response =" . $result['result']['response'] . PHP_EOL;
                $createOrUpdatefile = Helpers::uploadOrUpdateFileWithContent($fileDirPath, $fileContents, true);
                if (is_array($createOrUpdatefile)) {
                    $userFileSaved = $this->docRepo->saveFile($createOrUpdatefile)->toArray();
                } else {
                    $userFileSaved = $createOrUpdatefile;
                }
                //$userFileSaved['file_id'] = '101';
                $result['result']['header']['Tran_ID'] =  isset($result['SOAP-ENV_Body']["ns0_Payment"]) ? $result['SOAP-ENV_Body']["ns0_Payment"]['ns0_AckHeader']['ns0_MessageId'] : '';
                $result['status'] = 'failed';
                if (isset($result['SOAP-ENV_Body']["ns0_Payment"]) && $result['SOAP-ENV_Body']["ns0_Payment"]['ns0_AckHeader']['ns0_StatusCd'] == '000') {
                    $result['status'] = 'success';
                    $result['result']['header']['Tran_ID'] =  isset($result['SOAP-ENV_Body']["ns0_Payment"]) ? $result['SOAP-ENV_Body']["ns0_Payment"]['ns0_AckHeader']['ns0_MessageId'] : '';
                    if (empty($result['result']['header']['Tran_ID'])  && $result['result']['header']['Tran_ID'] == '') {
                        $result['result']['header']['Tran_ID'] =  isset($result['SOAP-ENV_Body']["ns0_Payment"]) ? $result['SOAP-ENV_Body']["ns0_Payment"]['ns0_AckHeader']['ns0_ResRF1'] : '';
                    }
                }
                $otherData['bank_type'] = config('lms.BANK_TYPE')['KOTAK'];
                $disbusalApiLogData = $this->createDisbusalApiLogData($userFileSaved, $result, $otherData);
                $createDisbusalApiLog = $this->lmsRepo->saveUpdateDisbursalApiLog($disbusalApiLogData);
                if ($createDisbusalApiLog) {
                    $disbursalApiLogId = $createDisbusalApiLog->disbursal_api_log_id;
                }

                if (isset($result['SOAP-ENV_Body']["ns0_Payment"]) && $result['SOAP-ENV_Body']["ns0_Payment"]['ns0_AckHeader']['ns0_StatusCd'] == '000') {
                    $this->disburseTableInsert($exportData, $supplierIds, $allinvoices, $disburseType, $disburseDate, $disbursalApiLogId);
                } else {
                    $http_code = $result['http_code'] ? $result['http_code'] . ', ' : $result['code'];
                    $message = $result['message'] ?? '';
                    $message .= isset($result['SOAP-ENV_Body']["ns0_Payment"]) ? "Status Code :" . $result['SOAP-ENV_Body']["ns0_Payment"]['ns0_AckHeader']['ns0_StatusCd'] . ',' : '';
                    $message .= isset($result['SOAP-ENV_Body']["ns0_Payment"]) ? "Message Id :" . $result['SOAP-ENV_Body']["ns0_Payment"]['ns0_AckHeader']['ns0_MessageId'] . ', ' : '';
                    $message .= isset($result['SOAP-ENV_Body']["ns0_Payment"]) ? "Status Remarks :" . $result['SOAP-ENV_Body']["ns0_Payment"]['ns0_AckHeader']['ns0_StatusRem'] . ', ' : '';
                    Session::flash('message', 'Error : ' . 'HTTP Code ' . $http_code  .  $message);
                    return redirect()->route('backend_get_disbursed_invoice');
                }
            }
            $whereActivi['activity_code'] = 'disburse_online';
            $activity = $this->master->getActivity($whereActivi);
            if (!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Disburse Online (Kotak Bank), Disbursement Queue (Manage Invoice)';
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['supplierIds' => $supplierIds, 'request' => $request->all()]), $arrActivity);
            }
            //\DB::commit();
            Session::flash('message', trans('backend_messages.sentTobank'));
            return redirect()->route('backend_get_disbursed_invoice')->withErrors('message', trans('backend_messages.disbursed'));
        } catch (Exception $ex) {
            //\DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function kotakDisbursalPaymentEnquiry(Request $request)
    {
        //\DB::beginTransaction();
        try {

            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }

            date_default_timezone_set("Asia/Kolkata");
            $disbursalBatchId = $request->get('disbursal_batch_id');
            $disburseDate = null;
            $transId = null;
            $createdBy = Auth::user()->user_id;
            $fundedDate = \Carbon\Carbon::now()->format('Y-m-d');
            $transDisbursalIds = [];
            $tranNewIds = [];

            $data = $this->lmsRepo->getdisbursalBatchByDBId($disbursalBatchId);
            if (!isset($data) || empty($data)) {
                return redirect()->route('backend_get_disbursal_batch_request')->withErrors('Something went wrong please try again.');
            } else {
                $disburseDate = date("Y-m-d", strtotime($data->disbursalOne->disburse_date));
                $reqData['txn_id'] = $data['disbursal_api_log']['txn_id'];
                $transId = $reqData['txn_id'];
            }


            if ($fundedDate != $disburseDate) {
                $dates = date('d-m-Y',strtotime($fundedDate));
                return redirect()->route('backend_get_disbursal_batch_request')->withErrors('Unable to process request as funded date cannot be less than current date ' . $dates);
            } else {
                $data = $data->toArray();
            }
            //die;
            $message = [];

            if (!empty($reqData)) {
                $header = [
                    'Maker_ID : CAPSAVE.M',
                    'Checker_ID : CAPSAVE.C1',
                    'Approver_ID : CAPSAVE.C2'
                ];
                $requestData['Req_Id'] = $reqData['txn_id'];
                $requestData['Msg_Src'] = config('lms.KOTAK_MSG_SOURCE');
                $requestData['Client_Code'] = config('lms.KOTAK_CLIENT_CODE');
                $requestData['Date_Post'] = $disburseDate;
                $requestData['Msg_Id'] = $reqData['txn_id'];
                $KotakObj = \App::make('App\Libraries\Kotak_lib');
                $getResponse = false;
                $result = $KotakObj->callReversalApi($requestData, $header, $getResponse);
                if ($getResponse) {
                    dd($result);
                }
                if (isset($result['code'])) {
                    if (isset($result['http_code']) && $result['http_code'] == 200) {
                    } else {
                        $http_code = $result['code'] ? $result['code'] . ', '  : $result['http_code'] . ', ';
                        $message = $result['message'] ?? $result['message'];
                        $message .= isset($result['SOAP-ENV_Body']["ns0_Reversal"]) ? "Status Code :" . $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_Status_Code'] . ',' : '';
                        $message .= isset($result['SOAP-ENV_Body']["ns0_Reversal"]) ? "Message Id :" . $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Header']['ns0_Req_Id'] . ', ' : '';
                        $message .= isset($result['SOAP-ENV_Body']["ns0_Reversal"]) ? "Status Remarks :" . $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_Status_Desc'] . ', ' : '';
                        Session::flash('message', 'Error : ' . $http_code  .  $message);
                        return redirect()->back();
                    }
                }
                $fileDirPath = getPathByTxnId($transId);
                $time = date('y-m-d H:i:s');

                $result['result']['http_header'] = (is_array($result['result']['http_header'])) ? json_encode($result['result']['http_header']) : $result['result']['http_header'];
                $fileContents = PHP_EOL . ' Log  ' . $time . PHP_EOL . "URL = " . $result['result']['url'] .  PHP_EOL
                    . PHP_EOL . ' Log  ' . $time . PHP_EOL . "Request Data = " . $result['result']['payload']  . PHP_EOL
                    . PHP_EOL . ' Log  ' . $time . PHP_EOL . "Request Header = " . $result['result']['http_header']  . PHP_EOL
                    . PHP_EOL . ' Log  ' . $time . PHP_EOL . "Response =" . $result['result']['response'] . PHP_EOL;
                $createOrUpdatefile = Helpers::uploadOrUpdateFileWithContent($fileDirPath, $fileContents, true);
                if (is_array($createOrUpdatefile)) {
                    $userFileSaved = $this->docRepo->saveFile($createOrUpdatefile)->toArray();
                } else {
                    $userFileSaved = $createOrUpdatefile;
                }

                $otherData['disbursal_batch_id'] = $disbursalBatchId;
                $otherData['txn_id'] = $transId;
                $otherData['bank_type'] = config('lms.BANK_TYPE')['KOTAK'];
                $otherData['enq_txn_id'] = $transId;
                $otherData['disbursal_batch_id'] = $disbursalBatchId;
                $result['result']['header']['Tran_ID'] = $reqData['txn_id'];
                $result['status'] = 'failed';
                if (isset($result['http_code']) && $result['http_code'] == 200 && isset($result['SOAP-ENV_Body']) && !empty($result['SOAP-ENV_Body'])) {
                    if (isset($result['SOAP-ENV_Body']["ns0_Reversal"]) && !empty($result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_Status_Code'])) {
                        $result['status'] = 'success';
                        $result['result']['header']['Tran_ID'] =  isset($result['SOAP-ENV_Body']) ? $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_Msg_Id'] : '';
                        if (empty($result['result']['header']['Tran_ID'])  && $result['result']['header']['Tran_ID'] == '') {
                            $result['result']['header']['Tran_ID'] =  isset($result['SOAP-ENV_Body']) ? $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Header']['ns0_Req_Id'] : '';
                        }
                    }
                }
                $disbusalApiLogData = $this->createDisbusalApiLogData($userFileSaved, $result, $otherData);
                $createDisbusalApiLog = $this->lmsRepo->saveUpdateDisbursalApiLog($disbusalApiLogData);
                if ($createDisbusalApiLog) {
                    $disbursalApiLogId = $createDisbusalApiLog->disbursal_api_log_id;
                }
                if (isset($result['http_code']) && $result['http_code'] == 200 && isset($result['SOAP-ENV_Body']) && !empty($result['SOAP-ENV_Body'])) {

                    // $invoiceIds = $this->lmsRepo->findInvoicesByUserAndBatchId(['disbursal_batch_id' => $disbursalBatchId])->toArray();
                    $disbursalIds = $this->lmsRepo->findDisbursalByUserAndBatchId(['disbursal_batch_id' => $disbursalBatchId])->toArray();
                    if ($disbursalIds) {

                        if (isset($result['SOAP-ENV_Body']["ns0_Reversal"]) && !empty($result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_Status_Code'])) {

                            $updateDisbursal = $this->lmsRepo->updateDisburseByUserAndBatch([
                                // 'status_id' => config('lms.DISBURSAL_STATUS')['DISBURSED'],
                                'funded_date' => (!empty($fundedDate)) ? date("Y-m-d h:i:s", strtotime(str_replace('/', '-', $fundedDate))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s')
                            ], $disbursalIds);
                            $STATUS_CODE = $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_Status_Code'];
                            $MSG_ID = $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_Msg_Id'];
                            $STATUS_DESC = $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_Status_Desc'];
                            $UTR_NO = (isset($result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_UTR']) && !empty($result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_UTR'])) ? $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_UTR'] : '';
                            /****RE = Rejected
                                AR = Auth Rejected
                                DF = FUNDS INSUFFICIENT
                                Error-101 = Data Not Found
                                Error-99 = Transaction is in Progress
                                CR = INVALID IFS Code
                                C = Received
                                UP = TRANSACTION TIMEOUT 91
                                U = PAID and UTR NO GET IN RESPONSE
                                CF = ACCOUNT DOES NOT EXIST
                                R = Rejected****/
                            if ($STATUS_CODE == 'U') {
                                $disburseStatus = config('lms.DISBURSAL_STATUS')['DISBURSED'];
                            } else if ($STATUS_CODE == 'Error-101' || $STATUS_CODE == 'Error-99' || $STATUS_CODE == 'C') {
                                $disburseStatus = config('lms.DISBURSAL_STATUS')['SENT_TO_BANK'];
                            } else if ($STATUS_CODE == 'RE' || $STATUS_CODE == 'R' || $STATUS_CODE == 'AR') {
                                $disburseStatus = config('lms.DISBURSAL_STATUS')['REJECT'];
                            } else if ($STATUS_CODE == 'DF' || $STATUS_CODE == 'CR' || $STATUS_CODE == 'UP' ||  $STATUS_CODE == 'CF') {
                                $disburseStatus = config('lms.DISBURSAL_STATUS')['FAILED'];
                            } else {
                                $disburseStatus = config('lms.DISBURSAL_STATUS')['SENT_TO_BANK'];
                            }

                            $transDisbursalIds = $this->lmsRepo->findDisbursalByUserAndBatchId(['ref_no' => $MSG_ID])->toArray();
                            $updateDisbursalByTranId = $this->lmsRepo->updateDisbursalByTranId([
                                'status_id' => $disburseStatus,
                                'tran_id' => $UTR_NO
                            ], ['ref_no' => $MSG_ID]);
                            foreach ($transDisbursalIds as $key => $value1) {
                                $this->lmsRepo->createDisbursalStatusLog($value1, $disburseStatus, '', $createdBy);

                                $invoiceDisburseIds = $this->lmsRepo->findInvoiceDisburseByDisbursalId(['disbursal_id' => $value1])->toArray();
                                $updateInvoiceDisburseStatus = $this->lmsRepo->updateInvoiceDisburseStatus($invoiceDisburseIds, $disburseStatus);

                                $invoiceIds = $this->lmsRepo->findInvoicesByUserAndBatchId(['disbursal_id' => $value1])->toArray();

                                $updateInvoiceStatus = $this->lmsRepo->updateInvoicesStatus($invoiceIds, $disburseStatus);
                                foreach ($invoiceIds as $key => $value2) {
                                    $this->invRepo->saveInvoiceStatusLog($value2, $disburseStatus);
                                }
                            }

                            if ($STATUS_CODE == 'U') {
                                $tranNewIds = array_merge($tranNewIds, $transDisbursalIds);
                            }
                            $message[] = $MSG_ID . ' is ' . $STATUS_CODE . " (" . $STATUS_DESC . ")";
                        } else {
                            $http_code = $result['http_code'] ? $result['http_code'] . ', ' : $result['code'];
                            $message = $result['message'] ?? 'some error occurred';
                            $message .= isset($result['SOAP-ENV_Body']["ns0_Reversal"]) ? "Status Code :" . $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_Status_Code'] . ',' : '';
                            $message .= isset($result['SOAP-ENV_Body']["ns0_Reversal"]) ? "Message Id :" . $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Header']['ns0_Req_Id'] . ', ' : '';
                            $message .= isset($result['SOAP-ENV_Body']["ns0_Reversal"]) ? "Status Remarks :" . $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_Status_Desc'] . ', ' : '';
                            Session::flash('message', 'Error : ' . 'HTTP Code ' . $http_code  .  $message);
                            return redirect()->route('backend_get_disbursal_batch_request');
                        }
                    }
                    $updateTransaction = $this->updateTransactionInvoiceDisbursed($tranNewIds, $fundedDate);
                } else {
                    $http_code = $result['http_code'] ? $result['http_code'] . ', ' : $result['code'];
                    $message = $result['message'] ?? $result['message'];
                    $message .= isset($result['SOAP-ENV_Body']["ns0_Reversal"]) ? "Status Code :" . $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_Status_Code'] . ',' : '';
                    $message .= isset($result['SOAP-ENV_Body']["ns0_Reversal"]) ? "Message Id :" . $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Header']['ns0_Req_Id'] . ', ' : '';
                    $message .= isset($result['SOAP-ENV_Body']["ns0_Reversal"]) ? "Status Remarks :" . $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Details']['ns0_Rev_Detail']['ns0_Status_Desc'] . ', ' : '';
                    Session::flash('message', 'Error : ' . 'HTTP Code ' . $http_code  .  $message);
                    return redirect()->route('backend_get_disbursal_batch_request');
                }
            }

            $disbureIds = $this->lmsRepo->findDisbursalByUserAndBatchId(['status_id' => config('lms.DISBURSAL_STATUS')['SENT_TO_BANK'], 'disbursal_batch_id' => $disbursalBatchId])->toArray();
            if (empty($disbureIds)) {
                $updateDisbursal = $this->lmsRepo->updateDisbursalBatchById(['batch_status' => 2], $disbursalBatchId);
            }
            //\DB::commit();
            Session::flash('message', implode(', ', $message));
            return redirect()->back()->withErrors('message', trans('backend_messages.batch_disbursed'));
        } catch (Exception $ex) {
            //\DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function disbursalPaymentEnquiryCron()
    {
        try {
            $cLogDetails = \Helpers::cronLogBegin(4);
            $disbursalBatchRequests = $this->lmsRepo->lmsGetDisbursalBatchRequestCron();
            $result['message'] = "No Batch Found";
            $result['code']    = 501; // no batch found
            foreach($disbursalBatchRequests as $disbursalBatchRequest) {
                // $disbursalBatchId = $disbursalBatchRequest->batch_id;
                $disbursalBatchId = $disbursalBatchRequest->disbursal_batch_id;
                $sysDate =  \Helpers::getSysStartDate();
                date_default_timezone_set("Asia/Kolkata");
                $data = $this->lmsRepo->getdisbursalBatchByDBId($disbursalBatchId);
                // dd($data['disbursal_api_log']);
                $reqData['txn_id'] = isset($data['disbursal_api_log']) ? $data['disbursal_api_log']['txn_id'] : 1;
                $transId = $reqData['txn_id'];
                // $transId = '2RGIK4436OUMXHZGXH';
                $createdBy = 0;
                $disbusalApiLogData='';
                $fundedDate = \Carbon\Carbon::now()->format('Y-m-d');
                $transDisbursalIds = [];
                $tranNewIds = [];
                if (!isset($data) || empty($data)) {
                    echo "Something went wrong please try again.";
                } else {
                    $data = $data->toArray();
                }
                $message = [];
                if(!empty($reqData)) {
                    $http_header = [
                        'timestamp' => date('Y-m-d H:i:s'),
                        'txn_id' => $reqData['txn_id']
                    ];

                    $header = [
                        'Maker_ID' => "CAPSAVE.M",
                        'Checker_ID' => "CAPSAVE.C1",
                        'Approver_ID' => "CAPSAVE.C2"
                    ];

                    $params = [
                        'http_header' => $http_header,
                        'header' => $header,
                        'request' => $reqData
                    ];

                    $idfcObj= new Idfc_lib();
                    $result = $idfcObj->api_call(Idfc_lib::BATCH_ENQ, $params);

                   
                    if (isset($result['code'])) {
                        if (isset($result['http_code']) && $result['http_code'] == 200) {

                        } else {
                            $http_code = $result['code'] ? $result['code']  : $result['http_code']. ', ';
                            $message = $result['message'] ?? $result['message'];
                           // continue;
                            // Session::flash('message', 'Error : '. $http_code  .  $message);
                            // return redirect()->back();

                            $otherData['disbursal_batch_id'] = $disbursalBatchId;
                            $otherData['txn_id'] = $transId;
                            $otherData['bank_type'] = config('lms.BANK_TYPE')['IDFC'];
                            $otherData['enq_txn_id'] = $transId;
                            $userFileSaved = null;
                            $disbusalApiLogData = $this->createDisbusalApiLogData($userFileSaved, $result, $otherData);
                           // continue;
                        }
                    } 
                    $fileDirPath = getPathByDISId($data['disbursal_one']['disbursal_id']);
                    if(is_null($fileDirPath)) {

                    }
                    $time = date('y-m-d H:i:s');
                    $result['result']['http_header'] = (is_array($result['result']['http_header'])) ? json_encode($result['result']['http_header']): $result['result']['http_header'];


                    $fileContents = PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['url'].  PHP_EOL
                        .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['payload']  .PHP_EOL
                        .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['http_header']  .PHP_EOL
                        .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['response'] . PHP_EOL;
                    $createOrUpdatefile = Helpers::uploadOrUpdateFileWithContent($fileDirPath, $fileContents, true);
                    if(is_array($createOrUpdatefile)) {
                        $userFileSaved = $this->docRepo->saveFile($createOrUpdatefile)->toArray();
                    } else {
                        $userFileSaved = $createOrUpdatefile;
                    }

                    $otherData['disbursal_batch_id'] = $disbursalBatchId;
                    $otherData['txn_id'] = $transId;
                    $otherData['bank_type'] = config('lms.BANK_TYPE')['IDFC'];
                    $otherData['enq_txn_id'] = $transId;
                    if(isset($disbusalApiLogData)) {
                        $disbusalApiLogData = $this->createDisbusalApiLogData($userFileSaved, $result, $otherData);
                    }
                    $createDisbusalApiLog = $this->lmsRepo->saveUpdateDisbursalApiLog($disbusalApiLogData);
                    if ($createDisbusalApiLog) {
                        $disbursalApiLogId = $createDisbusalApiLog->disbursal_api_log_id;
                    }

                    if ($result['status'] == 'success') {

                        // $invoiceIds = $this->lmsRepo->findInvoicesByUserAndBatchId(['disbursal_batch_id' => $disbursalBatchId])->toArray();
                        $disbursalIds = $this->lmsRepo->findDisbursalByUserAndBatchId(['disbursal_batch_id' => $disbursalBatchId])->toArray();

                        if ($disbursalIds) {

                            if ($result['result']['body']['TranID_Status'] == 'SUCCESS') {

                                $updateDisbursal = $this->lmsRepo->updateDisburseByUserAndBatch([
                                        // 'status_id' => config('lms.DISBURSAL_STATUS')['DISBURSED'],
                                        'funded_date' => (!empty($fundedDate)) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$fundedDate))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s')
                                    ], $disbursalIds);

                                foreach ($result['result']['body']['Transaction'] as $key => $value) {

                                    if ($value['RefStatus'] == 'SUCCESS') {
                                        $disburseStatus = config('lms.DISBURSAL_STATUS')['DISBURSED'];
                                    } else if($value['RefStatus'] == 'Pending for Authorization' || $value['RefStatus'] == 'Pending Auth' || $value['RefStatus'] == 'UNDER PROCESS') {
                                        $disburseStatus = config('lms.DISBURSAL_STATUS')['SENT_TO_BANK'];
                                    } else if($value['RefStatus'] == 'Rejected by Checker' || $value['RefStatus'] == 'Rejected' || $value['RefStatus'] == 'Rejected by Authorizer' || $value['RefStatus'] == 'Rejected online' || $value['RefStatus'] == 'Cancelled') {
                                        $disburseStatus = config('lms.DISBURSAL_STATUS')['REJECT'];
                                    } else if($value['RefStatus'] == 'FAILED') {
                                        $disburseStatus = config('lms.DISBURSAL_STATUS')['FAILED_DISBURSMENT'];
                                    } else {
                                        $disburseStatus = config('lms.DISBURSAL_STATUS')['SENT_TO_BANK'];
                                    }

                                    $transDisbursalIds = $this->lmsRepo->findDisbursalByUserAndBatchId(['ref_no' => $value['RefNo']])->toArray();
                                    $updateDisbursalByTranId = $this->lmsRepo->updateDisbursalByTranId([
                                            'status_id' => $disburseStatus,
                                            'tran_id' => $value['UTR_No']
                                        ], ['ref_no' => $value['RefNo']]);

                                    foreach ($transDisbursalIds as $key => $value1) {
                                        $this->lmsRepo->createDisbursalStatusLog($value1, $disburseStatus, '', $createdBy);

                                        $invoiceDisburseIds = $this->lmsRepo->findInvoiceDisburseByDisbursalId(['disbursal_id' => $value1])->toArray();
                                        $updateInvoiceDisburseStatus = $this->lmsRepo->updateInvoiceDisburseStatus($invoiceDisburseIds, $disburseStatus);

                                        $invoiceIds = $this->lmsRepo->findInvoicesByUserAndBatchId(['disbursal_id' => $value1])->toArray();

                                        $updateInvoiceStatus = $this->lmsRepo->updateInvoicesStatus($invoiceIds, $disburseStatus);
                                        foreach ($invoiceIds as $key => $value2) {
                                            $this->invRepo->saveInvoiceStatusLog($value2, $disburseStatus);
                                        }
                                    }

                                    if ($value['RefStatus'] == 'SUCCESS') {
                                        $tranNewIds = array_merge($tranNewIds, $transDisbursalIds);
                                    }
                                    $message[] = $value['RefNo'].' is '.$value['RefStatus'];
                                }
                            } else {
                                $http_code = $result['http_code'] ? $result['http_code'] . ', ' : $result['code'];
                                $message = $result['result']['body']['Transaction'] ?? $result['message'];
                            }
                        }
                        $updateTransaction = $this->updateTransactionInvoiceDisbursed($tranNewIds, $fundedDate);
                    } else {
                        $http_code = $result['http_code'] ? $result['http_code'] . ', ' : $result['code'];
                        $message = $result['message'] ?? $result['message'];
                    }
                }
                $disbureIds = $this->lmsRepo->findDisbursalByUserAndBatchId(['status_id' => config('lms.DISBURSAL_STATUS')['SENT_TO_BANK'], 'disbursal_batch_id' => $disbursalBatchId])->toArray();
                if(empty($disbureIds)) {
                    $updateDisbursal = $this->lmsRepo->updateDisbursalBatchById(['batch_status' => 2], $disbursalBatchId);
                }
            }
                if(isset($result['code']) == 501) {
                   $statusCode = 3;
                } else if(isset($result['code']) == 200) {
                    $statusCode = 1;
                } else {
                   $statusCode = 0;
                }
            if($cLogDetails){
                \Helpers::cronLogEnd($statusCode,$cLogDetails->cron_log_id);
            } 
            exit;
        } catch (Exception $ex) {
            // return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

}
