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

class InvoiceController extends Controller {

    use ApplicationTrait;
    use LmsTrait;

    protected $appRepo;
    protected $invRepo;
    protected $docRepo;
    protected $lmsRepo;
    protected $userRepo;
    protected $application;

    public function __construct(InvAppRepoInterface $app_repo, InvAppRepoInterface $application, InvoiceInterface $invRepo, InvUserRepoInterface $user_repo,InvDocumentRepoInterface $docRepo, InvLmsRepoInterface $lms_repo) {
        $this->appRepo = $app_repo;
        $this->invRepo = $invRepo;
        $this->docRepo = $docRepo;
        $this->lmsRepo = $lms_repo;
        $this->userRepo = $user_repo;
        $this->application  =  $application;
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
        $id = Auth::user()->user_id;
        $res =  $this->userRepo->getUserDetail($id);
        $aid    =  $res->anchor_id;
        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
        $get_program = $this->invRepo->getLimitProgram($aid);
        $get_program_limit = $this->invRepo->geAnchortLimitProgram($aid);
        return view('backend.invoice.upload_all_invoice')
                        ->with(['get_anchor' => $get_anchor,'anchor' => $chkUser->id,'id' =>  $aid,'limit' => $get_program_limit,'get_program' =>$get_program ]);
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
        $userInfo->outstandingAmt = number_format($this->lmsRepo->getUnsettledTrans($user_id)->sum('amount'),2);
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

        return view('backend.invoice.update_invoice_disbursal')
                ->with(
                    ['user_id' => $userId, 
                    'disbursal_batch_id' => $batchId
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
        
        foreach ($invoiceDisbursed as $key => $value) {
            $tenor = $value['tenor_days'];
            $updateInvoiceDisbursed = $this->lmsRepo->updateInvoiceDisbursed([
                        'payment_due_date' => ($value['invoice']['pay_calculation_on'] == 2) ? date('Y-m-d', strtotime(str_replace('/','-',$fundedDate). "+ $tenor Days")) : $value['invoice']['invoice_due_date'],
                        'status_id' => config('lms.DISBURSAL_STATUS')['DISBURSED'],
                        'int_accrual_start_dt' => $selectDate,
                        'updated_by' => Auth::user()->user_id,
                        'updated_at' => $curData
                    ], $value['invoice_disbursed_id']);

            $interest= 0;
            $margin= 0;

            $tenor = $this->calculateTenorDays($value['invoice']);
            $margin = $this->calMargin($value['invoice']['invoice_approve_amount'], $value['margin']);
            $fundedAmount = $value['invoice']['invoice_approve_amount'] - $margin;
            $tInterest = $this->calInterest($fundedAmount, (float)$value['interest_rate']/100, $tenor);

            if($value['invoice']['program_offer']['payment_frequency'] == 1) {
                $interest = $tInterest;
            }

            $transactionData = $this->createTransactionData($value['disbursal']['user_id'], ['amount' => $value['disburse_amt'], 'trans_date' => $fundedDate, 'invoice_disbursed_id' => $value['invoice_disbursed_id']], config('lms.TRANS_TYPE.PAYMENT_DISBURSED'));
            $createTransaction = $this->lmsRepo->saveTransaction($transactionData);

            $intrstAmt = round($interest, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
            if ($intrstAmt > 0.00) {
                $intrstDbtTrnsData = $this->createTransactionData($value['disbursal']['user_id'], ['amount' => $intrstAmt, 'trans_date' => $fundedDate, 'invoice_disbursed_id' => $value['invoice_disbursed_id']], config('lms.TRANS_TYPE.INTEREST'));
                $createTransaction = $this->lmsRepo->saveTransaction($intrstDbtTrnsData);

                $intrstCdtTrnsData = $this->createTransactionData($value['disbursal']['user_id'], ['parent_trans_id' => $createTransaction->trans_id, 'link_trans_id' => $createTransaction->trans_id, 'amount' => $intrstAmt, 'trans_date' => $fundedDate, 'invoice_disbursed_id' => $value['invoice_disbursed_id']], config('lms.TRANS_TYPE.INTEREST'), 1);
                $createTransaction = $this->lmsRepo->saveTransaction($intrstCdtTrnsData);
            }

            // Margin transaction $tranType = 10 
            $marginAmt = round($margin, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
            if ($marginAmt > 0.00) {
                $marginTrnsData = $this->createTransactionData($value['disbursal']['user_id'], ['amount' => $marginAmt, 'trans_date' => $fundedDate, 'invoice_disbursed_id' => $value['invoice_disbursed_id']], config('lms.TRANS_TYPE.MARGIN'), 0);
                $createTransaction = $this->lmsRepo->saveTransaction($marginTrnsData);
            }
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
        $attributes['supplier_id'] = $explode[0];
        $explode1 = explode(',', $attributes['program_id']);
        $attributes['program_id'] = $explode1[0];
        $appId = $explode[1];
        $date = Carbon::now();
        $id = Auth::user()->user_id;
        $res = $this->invRepo->getSingleAnchorDataByAppId($appId);
        $biz_id = $res->biz_id;
        $getPrgm  = $this->application->getProgram($attributes['program_id']);
        $chkUser  = $this->application->chkUser();
        $customer  = 4;
        $expl  =  explode(",",$getPrgm->invoice_approval); 
        if ($attributes['exception']) {
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
        $invoice_approve_amount = str_replace(",", "", $attributes['invoice_approve_amount']);
        $invoice_amount = str_replace(',', '', $attributes['invoice_approve_amount']);
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
            'created_at' => $date);
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
        // dd($customersDisbursalList);
        return view('backend.invoice.disburse_check')
                ->with([
                    'customersDisbursalList' => $customersDisbursalList,
                    'invoiceIds' => $invoiceIds 
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
            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            if (date('H') >= $validateTimeHour) { 
                Session::flash('error', 'Disbursment can not be done after '. Carbon::createFromFormat('H', $validateTimeHour)->format('g:i A'));
                return redirect()->route('backend_get_disbursed_invoice');
            }
            $invoiceIds = $request->get('invoice_ids');
            $disburseDate =  \Helpers::getSysStartDate();
            $disburseType = config('lms.DISBURSE_TYPE')['ONLINE'];
            $creatorId = Auth::user()->user_id;
            if(empty($invoiceIds)){
                return redirect()->route('backend_get_disbursed_invoice')->withErrors(trans('backend_messages.noSelectedInvoice'));
            }

            $record = array_filter(explode(",",$invoiceIds));
            $allrecords = array_unique($record);
            $allrecords = array_map('intval', $allrecords);
            $allinvoices = $this->lmsRepo->getInvoices($allrecords)->toArray();


            foreach ($allinvoices as $inv) {
                if($inv['supplier']['is_buyer'] == 2 && empty($inv['supplier']['anchor_bank_details'])){
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
                        $margin= 0;

                        $tenor = $this->calculateTenorDays($invoice);
                        $margin = $this->calMargin($invoice['invoice_approve_amount'], $invoice['program_offer']['margin']);
                        $fundedAmount = $invoice['invoice_approve_amount'] - $margin;
                        $tInterest = $this->calInterest($fundedAmount, (float)$invoice['program_offer']['interest_rate']/100, $tenor);

                        if($invoice['program_offer']['payment_frequency'] == 1) {
                            $interest = $tInterest;
                        }

                        $totalInterest += $interest;
                        $totalMargin += $margin;
                        $amount = round($fundedAmount - $interest, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
                        $disburseAmount += $amount;


                        $disbursalData['invoice'] = $invoice;

                    }
                }
                if($disburseType == 1) {
                    $modePay = ($disburseAmount < 200000) ? 'NEFT' : 'RTGS' ;
                    $exportData[$userid]['RefNo'] = $refNo;
                    $exportData[$userid]['Amount'] = $disburseAmount;
                    $exportData[$userid]['Debit_Acct_No'] = '21480259346';
                    $exportData[$userid]['Debit_Acct_Name'] = 'testing name';
                    $exportData[$userid]['Debit_Mobile'] = '1234567890';
                    // $exportData[$userid]['Ben_IFSC'] = $disbursalData['invoice']['supplier_bank_detail']['ifsc_code'];
                    $exportData[$userid]['Ben_IFSC'] = 'UTIB0000001';
                    // $exportData[$userid]['Ben_Acct_No'] = $disbursalData['invoice']['supplier_bank_detail']['acc_no'];
                    $exportData[$userid]['Ben_Acct_No'] = '21480314831';
                    $exportData[$userid]['Ben_Name'] = $disbursalData['invoice']['supplier_bank_detail']['acc_name'];
                    $exportData[$userid]['Ben_BankName'] = $disbursalData['invoice']['supplier_bank_detail']['bank']['bank_name'];
                    $exportData[$userid]['Ben_Email'] = $disbursalData['invoice']['supplier']['email'];
                    $exportData[$userid]['Ben_Mobile'] = $disbursalData['invoice']['supplier']['mobile_no'];
                    $exportData[$userid]['Mode_of_Pay'] = $modePay;
                    $exportData[$userid]['Nature_of_Pay'] = 'MPYMT';
                    $exportData[$userid]['Remarks'] = 'test remarks';

                } 
            }
            if($disburseType == 1 && !empty($allrecords)) {
            
                $http_header = [
                    'timestamp' => date('Y-m-d H:i:s'),
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
                $result = $idfcObj->api_call(Idfc_lib::MULTI_PAYMENT, $params);
                // dd($result);
                if ($result['status'] == 'success') {
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
                    $disbusalApiLogData = $this->createDisbusalApiLogData($userFileSaved, $result['result'], $otherData);
                    $createDisbusalApiLog = $this->lmsRepo->saveUpdateDisbursalApiLog($disbusalApiLogData);
                    if ($createDisbusalApiLog) {
                        $disbursalApiLogId = $createDisbusalApiLog->disbursal_api_log_id;
                    }
                    $this->disburseTableInsert($exportData, $supplierIds, $allinvoices, $disburseType, $disburseDate, $disbursalApiLogId);
                } else {
                    Session::flash('message',trans('backend_messages.disbursed_error'));
                    return redirect()->route('backend_get_disbursed_invoice')->withErrors('message',trans('backend_messages.disbursed_error'));
                }
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
            $userData['tran_id'] =$exportData[$userid]['RefNo'];;

            $disbursalRequest = $this->createDisbursalData($userData, $disburseAmount, $disburseType);
            $createDisbursal = $this->lmsRepo->saveDisbursalRequest($disbursalRequest);
            $this->lmsRepo->createDisbursalStatusLog($createDisbursal->disbursal_id, 10, '', $creatorId);

            foreach ($allinvoices as $invoice) {
                if($invoice['supplier_id'] == $userid) {
                    $invoiceDisbursedData = $this->lmsRepo->findInvoiceDisbursedByInvoiceId($invoice['invoice_id'])->toArray();

                        // dd($invoiceDisbursedData);
                    if ($invoiceDisbursedData == null) {
                        $invoice['batch_id'] = $batchId;
                        $invoice['disburse_date'] = $disburseDate;
                        $invoice['disbursal_id'] = $createDisbursal->disbursal_id;
                        
                        $invoiceDisbursedRequest = $this->createInvoiceDisbursedData($invoice, $disburseType);
                        $createInvoiceDisbursed = $this->lmsRepo->saveUpdateInvoiceDisbursed($invoiceDisbursedRequest);
                        $invoiceDisbursedId = $createInvoiceDisbursed->invoice_disbursed_id;
                    }
                    
                    $updateInvoiceStatus = $this->lmsRepo->updateInvoiceStatus($invoice['invoice_id'], 10);
                    $this->invRepo->saveInvoiceStatusLog($invoice['invoice_id'], 10);
                    $interest= 0;
                    $margin= 0;

                    $tenor = $this->calculateTenorDays($invoice);
                    $margin = $this->calMargin($invoice['invoice_approve_amount'], $invoice['program_offer']['margin']);
                    $fundedAmount = $invoice['invoice_approve_amount'] - $margin;
                    $tInterest = $this->calInterest($fundedAmount, (float)$invoice['program_offer']['interest_rate']/100, $tenor);

                    if($invoice['program_offer']['payment_frequency'] == 1) {
                        $interest = $tInterest;
                    }

                    $totalInterest += $interest;
                    $totalMargin += $margin;
                    $amount = round($fundedAmount - $interest, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
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
                if($inv['supplier']['is_buyer'] == 2 && empty($inv['supplier']['anchor_bank_details'])){
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
                        
                        $interest= 0;
                        $margin= 0;

                        $tenor = $this->calculateTenorDays($invoice);
                        $margin = $this->calMargin($invoice['invoice_approve_amount'], $invoice['program_offer']['margin']);
                        $fundedAmount = $invoice['invoice_approve_amount'] - $margin;
                        $tInterest = $this->calInterest($fundedAmount, (float)$invoice['program_offer']['interest_rate']/100, $tenor);

                        if($invoice['program_offer']['payment_frequency'] == 1) {
                            $interest = $tInterest;
                        }

                        $totalInterest += $interest;
                        $totalMargin += $margin;
                        $amount = round($fundedAmount - $interest, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
                        $disburseAmount += $amount;


                        $disbursalData['invoice'] = $invoice;

                    }
                }
                if($disburseType == 2) {

                    $exportData[$userid]['RefNo'] = $disbursalData['invoice']['lms_user']['virtual_acc_id'];
                    $exportData[$userid]['Amount'] = $disburseAmount;
                    $exportData[$userid]['Debit_Acct_No'] = '12334445511111';
                    $exportData[$userid]['Debit_Acct_Name'] = 'testing name';
                    $exportData[$userid]['Debit_Mobile'] = '9876543210';
                    $exportData[$userid]['Ben_IFSC'] = $disbursalData['invoice']['supplier_bank_detail']['ifsc_code'];
                    $exportData[$userid]['Ben_Acct_No'] = $disbursalData['invoice']['supplier_bank_detail']['acc_no'];
                    $exportData[$userid]['Ben_Name'] = $disbursalData['invoice']['supplier_bank_detail']['acc_name'];
                    $exportData[$userid]['Ben_BankName'] = $disbursalData['invoice']['supplier_bank_detail']['bank']['bank_name'];
                    $exportData[$userid]['Ben_Email'] = $disbursalData['invoice']['supplier']['email'];
                    $exportData[$userid]['Ben_Mobile'] = $disbursalData['invoice']['supplier']['mobile_no'];
                    $exportData[$userid]['Mode_of_Pay'] = 'IFT';
                    $exportData[$userid]['Nature_of_Pay'] = 'MPYMT';
                    $exportData[$userid]['Remarks'] = 'test remarks';
                    $exportData[$userid]['Value_Date'] = date('Y-m-d');

                } 
            }
            $result = $this->export($exportData, $batchId);
            $file['file_path'] = $result['file_path'] ?? '';
            if ($file) {
                $createBatchFileData = $this->createBatchFileData($file);
                $createBatchFile = $this->lmsRepo->saveBatchFile($createBatchFileData);
                if ($createBatchFile) {
                    $createDisbursalBatch = $this->lmsRepo->createDisbursalBatch($createBatchFile, $batchId);
                    $disbursalBatchId = $createDisbursalBatch->disbursal_batch_id;
                }
            }

            foreach ($supplierIds as $userid) {
                $disburseAmount = 0;
                $userData = $this->lmsRepo->getUserBankDetail($userid)->toArray();
                $userData['disbursal_batch_id'] =$disbursalBatchId;
                $disbursalRequest = $this->createDisbursalData($userData, $disburseAmount, $disburseType);
                $createDisbursal = $this->lmsRepo->saveDisbursalRequest($disbursalRequest);
                $this->lmsRepo->createDisbursalStatusLog($createDisbursal->disbursal_id, 10, '', $creatorId);

                foreach ($allinvoices as $invoice) {
                    if($invoice['supplier_id'] == $userid) {
                        $invoiceDisbursedData = $this->lmsRepo->findInvoiceDisbursedByInvoiceId($invoice['invoice_id'])->toArray();

                        if ($invoiceDisbursedData == null) {
                            $invoice['batch_id'] = $batchId;
                            $invoice['disburse_date'] = $disburseDate;
                            $invoice['disbursal_id'] = $createDisbursal->disbursal_id;
                            
                            $invoiceDisbursedRequest = $this->createInvoiceDisbursedData($invoice, $disburseType);
                            $createInvoiceDisbursed = $this->lmsRepo->saveUpdateInvoiceDisbursed($invoiceDisbursedRequest);
                            $invoiceDisbursedId = $createInvoiceDisbursed->invoice_disbursed_id;
                        }
                        
                        $updateInvoiceStatus = $this->lmsRepo->updateInvoiceStatus($invoice['invoice_id'], 10);
                        $this->invRepo->saveInvoiceStatusLog($invoice['invoice_id'], 10);
                        $interest= 0;
                        $margin= 0;

                        $tenor = $this->calculateTenorDays($invoice);
                        $margin = $this->calMargin($invoice['invoice_approve_amount'], $invoice['program_offer']['margin']);
                        $fundedAmount = $invoice['invoice_approve_amount'] - $margin;
                        $tInterest = $this->calInterest($fundedAmount, (float)$invoice['program_offer']['interest_rate']/100, $tenor);

                        if($invoice['program_offer']['payment_frequency'] == 1) {
                            $interest = $tInterest;
                        }

                        $totalInterest += $interest;
                        $totalMargin += $margin;
                        $amount = round($fundedAmount - $interest, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
                        $disburseAmount += $amount;

                        

                    }
                }
                
                if($createDisbursal) {
                    $updateDisbursal = $this->lmsRepo->updateDisburse([
                            'disburse_amount' => $disburseAmount
                        ], $createDisbursal->disbursal_id);
                }

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
                ->setCellValue('AA1', 'Nature Of Payment');
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
                ->setCellValue('AA' . $rows, $rowData['Nature_of_Pay'] ?? 'MPYMT');

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
              if($uploadData['status']==0)
             {
               Session::flash('error', $uploadData['message']);
               return back(); 
             }
              if($uploadData)
              {   
                  $zipBatch  =   self::createBatchNumber(6);
                  $uploadData['batch_no'] = $zipBatch;
                  $uploadData['parent_bulk_batch_id'] =  $resFile->invoice_bulk_batch_id;
                  $resZipFile =  $this->invRepo->saveInvoiceZipBatch($uploadData);
                  if($resZipFile)
                  {
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
                        $dataAttr['prgm_id']  =   $prgm_id;
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
                        $key++;
                      
                        $res =  $this->invRepo->saveInvoice($ins);
                       
                    } 
            
                         Session::flash('message', 'Invoice data successfully sent to under reviewer process');
                         return back();  
                     
                  }
              }
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
            
            $disbursalBatchId = $request->get('disbursal_batch_id');
            $sysDate =  \Helpers::getSysStartDate();
            date_default_timezone_set("Asia/Kolkata");
            $data = $this->lmsRepo->getdisbursalBatchByDBId($disbursalBatchId)->toArray();
            $reqData['txn_id'] = $data['disbursal_api_log']['txn_id'];
            $transId = $reqData['txn_id'];
            // $transId = '2RGIK4436OUMXHZGXH';
            $createdBy = Auth::user()->user_id;
            $fundedDate = \Carbon\Carbon::now()->format('Y-m-d');
            $transDisbursalIds = [];
            $tranNewIds = [];

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
                if ($result['status'] == 'success') {
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
                    $otherData['enq_txn_id'] = $transId;
                    $disbusalApiLogData = $this->createDisbusalApiLogData($userFileSaved, $result['result'], $otherData);
                    $createDisbusalApiLog = $this->lmsRepo->saveUpdateDisbursalApiLog($disbusalApiLogData);
                    if ($createDisbusalApiLog) {
                        $disbursalApiLogId = $createDisbusalApiLog->disbursal_api_log_id;
                    }

                    $invoiceIds = $this->lmsRepo->findInvoicesByUserAndBatchId(['disbursal_batch_id' => $disbursalBatchId])->toArray();
                    $disbursalIds = $this->lmsRepo->findDisbursalByUserAndBatchId(['disbursal_batch_id' => $disbursalBatchId])->toArray();
                    if ($disbursalIds) {
                        $updateDisbursal = $this->lmsRepo->updateDisbursalBatchById([
                                'batch_status' => 2], $disbursalBatchId);

                        $updateDisbursal = $this->lmsRepo->updateDisburseByUserAndBatch([
                                'status_id' => config('lms.DISBURSAL_STATUS')['DISBURSED'],
                                'funded_date' => (!empty($fundedDate)) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$fundedDate))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s')
                            ], $disbursalIds);
                        foreach ($disbursalIds as $key => $value) {
                            $this->lmsRepo->createDisbursalStatusLog($value, config('lms.DISBURSAL_STATUS')['DISBURSED'], 'online disbursed', $createdBy);
                        }
                        foreach ($result['result']['body']['Transaction'] as $key => $value) {
                            if ($value['RefStatus'] == 'SUCCESS') {
                                $updateDisbursalByTranId = $this->lmsRepo->updateDisbursalByTranId([
                                    'status_id' => config('lms.DISBURSAL_STATUS')['DISBURSED']
                                ], $value['RefNo']);
                                $transDisbursalIds = $this->lmsRepo->findDisbursalByUserAndBatchId(['tran_id' => $value['RefNo']])->toArray();
                                $tranNewIds = array_merge($tranNewIds, $transDisbursalIds);

                            } else {
                                $updateDisbursalByTranId = $this->lmsRepo->updateDisbursalByTranId([
                                    'status_id' => config('lms.DISBURSAL_STATUS')['FAILED_DISBURSMENT']
                                ], $value['RefNo']);
                            }
                        }
                    }            
                    if ($invoiceIds) {
                        $updateInvoiceStatus = $this->lmsRepo->updateInvoicesStatus($invoiceIds, config('lms.DISBURSAL_STATUS')['DISBURSED']);
                        foreach ($invoiceIds as $key => $value) {
                            $this->invRepo->saveInvoiceStatusLog($value, config('lms.DISBURSAL_STATUS')['DISBURSED']);
                        }
                    }
                    $updateTransaction = $this->updateTransactionInvoiceDisbursed($tranNewIds, $fundedDate);
                } else {
                    Session::flash('message',trans('backend_messages.disbursed_error'));
                    return redirect()->back()->withErrors('message',trans('backend_messages.disbursed_error'));
                }
                 
            }
                    
            Session::flash('message',trans('backend_messages.disbursed'));
            return redirect()->back()->withErrors('message',trans('backend_messages.disbursed'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }             
    }
}
