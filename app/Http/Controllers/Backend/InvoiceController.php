<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
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
use DB;
use App\Libraries\Pdf;
use Carbon\Carbon;
use PHPExcel; 
use PHPExcel_IOFactory;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;

class InvoiceController extends Controller {

    use ApplicationTrait;
    use LmsTrait;

    protected $appRepo;
    protected $invRepo;
    protected $docRepo;
    protected $lmsRepo;
    protected $userRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvoiceInterface $invRepo, InvUserRepoInterface $user_repo,InvDocumentRepoInterface $docRepo, InvLmsRepoInterface $lms_repo) {
        $this->appRepo = $app_repo;
        $this->invRepo = $invRepo;
        $this->docRepo = $docRepo;
        $this->lmsRepo = $lms_repo;
        $this->userRepo = $user_repo;
        $this->middleware('auth');
        //$this->middleware('checkBackendLeadAccess');
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

        $get_anchor = $this->invRepo->getLimitAllAnchor();

        return view('backend.invoice.upload_all_invoice')
                        ->with(['get_anchor' => $get_anchor]);
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
        $userInfo = $this->invRepo->getCustomerDetail($user_id);

        $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(7);
        $get_bus = $this->invRepo->getBusinessNameApp(7);
        $status =  DB::table('mst_status')->where(['status_type' =>4])->get();
        return view('backend.invoice.user_wise_invoice')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo,'status' =>$status]);
    } 

    public function getBulkInvoice() {

        $getAllInvoice = $this->invRepo->getLimitAllAnchor();
        $get_bus = $this->invRepo->getBusinessName();
        return view('backend.invoice.bulk_invoice')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice]);
    }

    public function viewApproveInvoice(Request $req) {
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        $userInfo = $this->invRepo->getCustomerDetail($user_id);
        $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(8);
        $get_bus = $this->invRepo->getBusinessNameApp(8);
        return view('backend.invoice.approve_invoice')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo]);
    }

    public function viewDisbursedInvoice(Request $req) {
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        $userInfo = $this->invRepo->getCustomerDetail($user_id);
        $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(9);
        $get_bus = $this->invRepo->getBusinessNameApp(9);
        return view('backend.invoice.disbursed_invoice')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo]);
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
        return view('backend.invoice.sent_to_bank')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo]);
    }

    public function viewBankInvoice(Request $req) {
        $flag = $req->get('flag') ?: null;
        $user_id = $req->get('user_id') ?: null;
        $app_id = $req->get('app_id') ?: null;
        return view('backend.invoice.bank_invoice')->with(['flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id]);
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
        return view('backend.invoice.view_invoice_details')->with(['invoice' => $res, 'status' => $get_status]);
    }

    /* save bulk invoice */

    public function saveBulkInvoice(Request $request) {
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

            Session::flash('message', 'Invoice Amount successfully Updated');
            return back();
        } else {
            Session::flash('message', 'Something wrong, Amount is not Updated');
            return back();
        }
    }

    /*   save invoice */

    public function saveInvoice(Request $request) {
        $attributes = $request->all();
        $explode = explode(',', $attributes['supplier_id']);
        $attributes['supplier_id'] = $explode[0];
        $appId = $explode[1];
        $date = Carbon::now();
        $id = Auth::user()->user_id;
        $res = $this->invRepo->getSingleAnchorDataByAppId($appId);
        $biz_id = $res->biz_id;
        if ($attributes['exception']) {
            $statusId = 28;
        } else {
            $statusId = 7;
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
            'file_id' => $userFile->file_id,
            'created_by' => $id,
            'created_at' => $date);
        $result = $this->invRepo->save($arr);

        if ($result) {

            $this->invRepo->saveInvoiceActivityLog($result, 7, null, $id, null);
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
        $userIds = [];

        foreach ($supplierIds as $userid) {
            foreach ($allinvoices as $invoice) {
                if($invoice['supplier_id'] = $userid && !in_array($userid, $userIds)) {
                    $userIds[] = $userid;
                }
            }
        } 

        $customersDisbursalList = $this->userRepo->lmsGetDisbursalCustomer($userIds);

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
        $invoiceIds = $request->get('invoice_ids');
        $disburseType = config('lms.DISBURSE_TYPE')['ONLINE']; // Online by Bank Api
        if(empty($invoiceIds)){
            return redirect()->route('backend_get_disbursed_invoice')->withErrors(trans('backend_messages.noSelectedInvoice'));
        }
        $record = array_filter(explode(",",$invoiceIds));
        $allrecords = array_unique($record);
        $allrecords = array_map('intval', $allrecords);
        $allinvoices = $this->lmsRepo->getInvoices($allrecords)->toArray();
        $supplierIds = $this->lmsRepo->getInvoiceSupplier($allrecords)->toArray();
        $userIds = [];

        foreach ($supplierIds as $userid) {
            foreach ($allinvoices as $invoice) {
                if($invoice['supplier_id'] = $userid && !in_array($userid, $userIds)) {
                    $userIds[] = $userid;
                }
            }
        } 

        $customersDisbursalList = $this->userRepo->lmsGetDisbursalCustomer($userIds);

        return view('backend.invoice.confirm_invoice')
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
    public function disburseOffline(Request $request)
    {
        $transId = _getRand(18);
        $invoiceIds = $request->get('invoice_ids');
        $disburseType = config('lms.DISBURSE_TYPE')['OFFLINE']; // Online by Bank Api i.e 2
        if(empty($invoiceIds)){
            return redirect()->route('backend_get_disbursed_invoice')->withErrors(trans('backend_messages.noSelectedInvoice'));
        }
        $record = array_filter(explode(",",$invoiceIds));
        $allrecords = array_unique($record);
        $allrecords = array_map('intval', $allrecords);
        $allinvoices = $this->lmsRepo->getInvoices($allrecords)->toArray();
        $supplierIds = $this->lmsRepo->getInvoiceSupplier($allrecords)->toArray();

        $disburseDate = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $fundedAmount = 0;
        $interest = 0;
        $disburseAmount = 0;
        $totalInterest = 0;
        $totalFunded = 0;
        $totalMargin = 0;
        $exportData = [];

        foreach ($supplierIds as $userid) {
            $disburseAmount = 0;

            foreach ($allinvoices as $invoice) {
                $invoice['batch_id'] = _getRand(12);
                $invoice['disburse_date'] = $disburseDate;
                $disburseRequestData = $this->createInvoiceDisbursalData($invoice, $disburseType);
                $createDisbursal = $this->lmsRepo->saveDisbursalRequest($disburseRequestData);

                $refId = $invoice['lms_user']['virtual_acc_id'];
                
                if($invoice['supplier_id'] = $userid) {

                    $interest= 0;
                    $margin= 0;

                    $tenor = $this->calculateTenorDays($invoice);
                    $margin = $this->calMargin($invoice['invoice_approve_amount'], $invoice['program_offer']['margin']);
                    $fundedAmount = $invoice['invoice_approve_amount'] - $margin;
                    $tInterest = $this->calInterest($fundedAmount, $invoice['program_offer']['interest_rate']/100, $tenor);

                    if($invoice['program_offer']['payment_frequency'] == 1) {
                        $interest = $tInterest;
                    }

                    $totalInterest += $interest;
                    $totalMargin += $margin;
                    $totalFunded += $fundedAmount;
                    $disburseAmount += round($fundedAmount, 2);

                }

                if($disburseType == 2) {

                    $updateInvoiceStatus = $this->lmsRepo->updateInvoiceStatus($invoice['invoice_id'], 10);
                    $exportData[$userid]['RefNo'] = $refId;
                    $exportData[$userid]['Amount'] = $disburseAmount;
                    $exportData[$userid]['Debit_Acct_No'] = '12334445511111';
                    $exportData[$userid]['Debit_Acct_Name'] = 'testing name';
                    $exportData[$userid]['Debit_Mobile'] = '9876543210';
                    $exportData[$userid]['Ben_IFSC'] = $invoice['supplier_bank_detail']['ifsc_code'];
                    $exportData[$userid]['Ben_Acct_No'] = $invoice['supplier_bank_detail']['acc_no'];
                    $exportData[$userid]['Ben_Name'] = $invoice['supplier_bank_detail']['acc_name'];
                    $exportData[$userid]['Ben_BankName'] = $invoice['supplier_bank_detail']['bank']['bank_name'];
                    $exportData[$userid]['Ben_Email'] = $invoice['supplier']['email'];
                    $exportData[$userid]['Ben_Mobile'] = $invoice['supplier']['mobile_no'];
                    $exportData[$userid]['Mode_of_Pay'] = 'IFT';
                    $exportData[$userid]['Nature_of_Pay'] = 'MPYMT';
                    $exportData[$userid]['Remarks'] = 'test remarks';
                    $exportData[$userid]['Value_Date'] = date('Y-m-d');

                    if ($createDisbursal) {
                        $updateInvoiceStatus = $this->lmsRepo->updateInvoiceStatus($invoice['invoice_id'], 10);
                    }

                } 


            }
            
            if ($disburseAmount) {
                if($disburseType == 2) {
                    
                    // disburse transaction $tranType = 16 for payment acc. to mst_trans_type table
                    $transactionData = $this->createTransactionData($disburseRequestData['user_id'], ['amount' => $disburseAmount, 'trans_date' => $disburseDate], $transId, 16);
                    $createTransaction = $this->lmsRepo->saveTransaction($transactionData);
                    
                    // interest transaction $tranType = 9 for interest acc. to mst_trans_type table
                    $intrstAmt = round($totalInterest,2);
                    if ($intrstAmt > 0.00) {
                        $intrstDbtTrnsData = $this->createTransactionData($disburseRequestData['user_id'], ['amount' => $intrstAmt, 'trans_date' => $disburseDate], $transId, 9);
                        $createTransaction = $this->lmsRepo->saveTransaction($intrstDbtTrnsData);

                        $intrstCdtTrnsData = $this->createTransactionData($disburseRequestData['user_id'], ['amount' => $intrstAmt, 'trans_date' => $disburseDate], $transId, 9, 1);
                        $createTransaction = $this->lmsRepo->saveTransaction($intrstCdtTrnsData);
                    }

                    // Margin transaction $tranType = 10 
                    $marginAmt = round($totalMargin,2);
                    if ($marginAmt > 0.00) {
                        $marginTrnsData = $this->createTransactionData($disburseRequestData['user_id'], ['amount' => $marginAmt, 'trans_date' => $disburseDate], $transId, 10, 1);
                        $createTransaction = $this->lmsRepo->saveTransaction($marginTrnsData);
                    }

                    

                }
            }
        }
        $this->export($exportData);
        die("here");

        Session::flash('message',trans('backend_messages.disbursed'));
        return view('backend.invoice.confirm_invoice')
                ->with([
                    'customersDisbursalList' => $customersDisbursalList, 
                    'invoiceIds' => $invoiceIds 
                ]);;              
    }

    public function export($data) {

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
        header('Content-Disposition: attachment;filename="test.xlsx"');
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
}
