<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Auth;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\UserInvoiceInterface as InvUserInvRepoInterface;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\Master\GstTax;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\Lms\PaymentApportionment;
use DB;
use Carbon\Carbon;
use PDF;
use Session;
use Helpers;
// use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;
use DateTime;
use Illuminate\Support\Facades\Storage;

class userInvoiceController extends Controller
{
    //use ApplicationTrait;
    use ActivityLogTrait;

    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $master;
    protected $UserInvRepo;

    /**
     * The pdf instance.
     *
     * @var App\Libraries\Pdf
     */
    protected $pdf;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, MasterInterface $master, InvUserInvRepoInterface $UserInvRepo) {
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->master = $master;
        $this->UserInvRepo = $UserInvRepo;
        $this->middleware('checkBackendLeadAccess');
    }

    /**
     * Display invoice as per User.
     *
     */
    public function listUserInvoice(Request $request) {
        try {
            $user_id = $request->get('user_id');
            $result = $this->getUserLimitDetais($user_id);
            return view('lms.invoice.user_invoice_list')->with(['user_id' => $user_id,'userInfo' =>  $result['userInfo'],
                            'application' => $result['application'],
                            'anchors' =>  $result['anchors']]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

     /**
     * Create invoice as per User.
     **/
       public function createUserInvoice(Request $request) {
        try {
            $paymentAppor = PaymentApportionment::checkApportionmentHold($request->get('user_id'));
            if ($paymentAppor) {
                \Session::flash('error', 'You cannot perform this action as you have not uploaded  the unsettled payment apportionment CSV file.');
                return back();
            }
            $eodStartDate = Helper::getSysStartDate();
            $due_date = \Carbon\Carbon::now()->addDays(7)->toDateTimeString();
            $user_id = $request->get('user_id');
            $userCompanyRelation  = $this->UserInvRepo->getUserCompanyRelation($user_id);
            if (empty($userCompanyRelation)) {
                return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'No Relation found between Company and User.'); 
            }
            $registeredCompany  = $this->UserInvRepo->getCompanyRegAddr();
            if (empty($registeredCompany) || $registeredCompany->isEmpty()) {
               return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Company Registered address not found..'); 
            }
            if ($registeredCompany->count() != 1) {
               return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Multiple Company Registered addresses found..'); 
            }
            $registeredCompany = $registeredCompany->toArray();
            $registeredCompany = $registeredCompany[0];
            if (empty($registeredCompany['bank_account_id'])) {
              return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'No bank detail found for the Registered Company.'); 
            }
            $company_id = $userCompanyRelation->company_id;
            $biz_addr_id = $userCompanyRelation->biz_addr_id;
            $user_invoice_rel_id = $userCompanyRelation->user_invoice_rel_id;
            $billingDetails = $this->_getBillingDetail($biz_addr_id);
            if ($billingDetails['status'] != 'success') {
               return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', $billingDetails['message']); 
            }
            $billingDetails = $billingDetails['data'];
            $origin_of_recipient = $this->_getOriginRecipent($company_id, $user_id);
            if ($origin_of_recipient['status'] != 'success') {
                return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', $origin_of_recipient['message']); 
            }
            $allApplications  = $this->UserInvRepo->getUserAllApplicationsDetail($user_id);
            $encData = _encrypt("$user_id|$company_id|$biz_addr_id|$user_invoice_rel_id");
            $origin_of_recipient = $origin_of_recipient['data'];
            $origin_of_recipient['charge_prefix'] = config('lms.INVOICE_TYPE.C');
            $origin_of_recipient['interest_prefix'] = config('lms.INVOICE_TYPE.I'); 
            $latestUserInvoice = $this->UserInvRepo->getUserLastInvoiceNo();
            if($latestUserInvoice){
                if($latestUserInvoice->created_by){
                    $fname = \Helpers::getUserInfo((int)$latestUserInvoice->created_by)->f_name;
                    $lname = \Helpers::getUserInfo((int)$latestUserInvoice->created_by)->l_name;
                    $created_by = $fname.' '.$lname;
                }else{
                    $created_by = 'System';
                }
                Session::flash('lastInvMsg','Last Invoice generated Number '.$latestUserInvoice->invoice_no .' created by '.$created_by.' created on '.$latestUserInvoice->created_at);
            }else{
                Session::flash('lastInvMsg','Still Invoice not created for any customer.');
            }
            return view('lms.invoice.create_user_invoice')->with(['user_id'=> $user_id, 'billingDetails' => $billingDetails, 'origin_of_recipient' => $origin_of_recipient, 'encData' => $encData, 'allApplications' => $allApplications, 'eodStartDate' => $eodStartDate, 'due_date' => $due_date]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }


    private function _getCompanyDetail($company_id = null, $bank_account_id = null) {
        $response = [
            'status' => 'fail',
            'message' => 'Company detail for the user not found.',
            'data' => [],
        ];
        $companyDetail = $this->UserInvRepo->getCompanyDetail($company_id);
        if (empty($companyDetail)) {
            $response['message'] = 'No Company detail found for the mapped Company.';
            return $response;
        }
        $BankDetails = $this->UserInvRepo->getCompanyBankAcc($company_id);
        $bankDetailsFound =!empty($BankDetails) && !$BankDetails->isEmpty();
        if (!$bankDetailsFound && !bankDetailIsOfRegisteredCompanyInInvoice()) {
            $response['message'] = 'No BankDetail is found for the mapped Company.';
            return $response;
        } 
        $activeBankAcc = NULL;
        if ($bankDetailsFound) {
            foreach ($BankDetails as $key => $bankDtls) {
               if ($bankDtls->bank_account_id == $bank_account_id) {
                  $activeBankAcc = $bankDtls;
                  break;
               }
               if ($bankDtls->is_default == true) {
                  $activeBankAcc = $bankDtls;
                  break;
               }
            }
        }
        
        if (empty($activeBankAcc) && empty(bankDetailIsOfRegisteredCompanyInInvoice())) {
            $response['message'] = 'No default Bank Detail found for the mapped Company.';
            return $response;
        }
        if (empty($companyDetail->getStateDetail)) {
            $response['message'] = 'State Detail not found for the the mapped company.';
            return $response;
        }
        $CompanyArr = [
            'comp_id' => $companyDetail->comp_addr_id,
            'name' => $companyDetail->cmp_name,
            'address' => $companyDetail->cmp_add,
            'state' => $companyDetail->getStateDetail,
            'city' => $companyDetail->city,
            'charge_prefix' => $companyDetail->charge_prefix,
            'interest_prefix' => $companyDetail->interest_prefix,
            'phone' => $companyDetail->cmp_mobile,
            'email' => $companyDetail->cmp_email,
            'pan_no' => $companyDetail->pan_no,
            'gst_no' => $companyDetail->gst_no,
            'cin_no' => $companyDetail->cin_no,
            'bank_id' => $activeBankAcc->bank_account_id ?? NULL,
            'bank_name' => $activeBankAcc->bank->bank_name ?? NULL,
            'acc_no' => $activeBankAcc->acc_no ?? NULL,
            'acc_name' => $activeBankAcc->acc_name ?? NULL,
            'branch_name' => $activeBankAcc->branch_name ?? NULL,
            'ifsc_code' => $activeBankAcc->ifsc_code ?? NULL,
            'state_id' => $companyDetail->getStateDetail->id,
            'state_name' => $companyDetail->getStateDetail->name,
            'state_no' => $companyDetail->getStateDetail->state_no,
            'state_code' => $companyDetail->getStateDetail->state_code,
        ];

        $response['status'] = 'success';
        $response['message'] = 'success';
        $response['data'] = $CompanyArr;
        return $response;
    }

    private function _getBillingDetail($biz_addr_id) {
        $response = [
            'status' => 'fail',
            'message' => 'Pan Or GST no not found.',
            'data' => [],
        ];
        $businessWithAddr =  $this->UserInvRepo->getBusinessAddressByaddrId($biz_addr_id);
        $billingDetails = [
            'biz_addr_id' => $biz_addr_id,
            'name' => $businessWithAddr->business->biz_entity_name,
            'address' => $businessWithAddr->addr_1 . ' '. $businessWithAddr->addr_2 . ' ' . $businessWithAddr->city_name . ' '.  ($businessWithAddr->state->name ?? '') . ', '. $businessWithAddr->pin_code,
            'pan_no' => $businessWithAddr->business->pan->pan_gst_hash ?? '',
            'state_id' => $businessWithAddr->state->id ?? '',
            'state_name' => $businessWithAddr->state->name ?? '',
            'state_no' => $businessWithAddr->state->state_no ?? '',
            'state_code' => $businessWithAddr->state->state_code ?? '',
            'gstin_no' => $businessWithAddr->business->gst->pan_gst_hash ?? '',
        ];
        if (empty($billingDetails['state_name'])) {
            $response['message'] = 'State Detail not found. Please update address with state first.';
            return $response;
        }
        $response['status'] = 'success';
        $response['message'] = 'success';
        $response['data'] = $billingDetails;
        return $response;
    }

    private function _getOriginRecipent($company_id, $user_id) {
        $companyDetail = $this->_getCompanyDetail($company_id);
        if ($companyDetail['status'] != 'success') {
            return $companyDetail;
        }
        $companyDetail = $companyDetail['data'];
        $reference_no = _getRand(10). $user_id;
        // $invoice_no_id = $this->UserInvRepo->getNextInv(['user_id' => $user_id])->invoice_no_id;
        $curr_date = date('y-m-d');
        $origin_of_recipient = [
            'reference_no' => 'RENT'. $reference_no,
            'financial_year' => preg_replace('#[^0-9]+#' , '', getFinancialYear($curr_date)),
            // 'rand_4_no' => sprintf('%04d', $invoice_no_id ?? rand(0, 9999)),
            'rand_4_no' => '----',
        ];
        $response['status'] = 'success';
        $response['message'] = 'success';
        $response['data'] = $companyDetail + $origin_of_recipient;
        return $response;
    }

    public function getUserInvoiceTxns(Request $request) {
        $response = [
            'status' => '0',
            'message' => 'Some error occured, Try after sometime'
        ];
        $invoice_type = $request->get('invoice_type');
        if (!in_array($invoice_type, ['I', 'C'])) {
            $response['message'] = 'Invalid Invoice Type';
            return response()->json($response);
        }
        $url_user_id = $request->get('user_id');
        $requestedData = $request->all();
        $decryptedData = _decrypt($requestedData['encData']);
        if (empty($decryptedData)) {
            return response()->json(['status' => 0,'message' => 'Data modified, Please try again.']); 
        }
        list($user_id, $company_id, $biz_addr_id) = explode('|', $decryptedData);
        if ($url_user_id != $user_id) {
           return response()->json(['status' => 0,'message' => 'Data can not be modified.']); 
        }
        $companyDetail = $this->_getCompanyDetail($company_id);
        if ($companyDetail['status'] != 'success') {
           return response()->json(['status' => 0,'message' => $companyDetail['message']]); 
        }
        $company_data = $companyDetail['data'];

        $billingDetail = $this->_getBillingDetail($biz_addr_id);
        if ($billingDetail['status'] != 'success') {
           return response()->json(['status' => 0,'message' => $billingDetail['message']]); 
        }
        $billing_data = $billingDetail['data'];
        $companyStateId = $company_data['state_id'];
        $userStateId = $billing_data['state_id'];

        $txnsData = $this->UserInvRepo->getUserInvoiceTxns($user_id, $invoice_type);
        if (empty($txnsData) || $txnsData->isEmpty()) {
           $response['message'] = 'No transaction found for the user.';
            return response()->json($response);
        }
        $is_state_diffrent = ($userStateId != $companyStateId);
        $inv_data = $this->_calculateInvoiceTxns($txnsData, $is_state_diffrent);
        $intrest_charges = $inv_data[0];
        view()->share(['intrest_charges' => $intrest_charges, 'checkbox' => true]);
        $view = view('lms.invoice.generate_invoice_txns');
        // dd($inv_data);
        return response()->json(['status' => 1,'view' => base64_encode($view)]); 
    }

    public function previewUserInvoice(Request $request){
        $url_user_id = $request->get('user_id');
        $invoice_date = $request->get('invoice_date');
        $due_date = $request->get('due_date');
        $reference_no = $request->get('reference_no');
        $invoice_no = $request->get('invoice_no');
        $state_name = $request->get('state_name');
        $invoice_type = $request->get('invoice_type');
        $trans_ids = $request->get('trans_id');
        $trans_ids = $request->get('trans_id');

        if (!empty($invoice_date)) {
            $invoice_date = \DateTime::createFromFormat('d/m/Y', $invoice_date)->format('Y-m-d');
        }

        if (!empty($due_date)) {
            $due_date = \DateTime::createFromFormat('d/m/Y', $due_date)->format('Y-m-d');
        }

        $lastInvData = $this->UserInvRepo->getLastInvoiceSerialNo($invoice_type);
        $invSerialNo = sprintf('%04d', (($lastInvData->inv_serial_no ?? 0) + 1) ?? rand(0, 9999));
        $InvoiceNoArr = explode('/',$invoice_no);
        $InvoiceNoArr[3] = $invSerialNo;
        $invoice_no = implode('/',$InvoiceNoArr);

        if (!in_array($invoice_type, ['I', 'C'])) {
           return response()->json(['status' => 0,'message' => "Invalid Invoice Type found."]); 
        }

        if (empty(preg_replace('#[^A-Z0-9]+#', '', strtoupper($reference_no)))) {
           return response()->json(['status' => 0,'message' => "Invalid Reference No.."]); 
        }

        $registeredCompany  = $this->UserInvRepo->getCompanyRegAddr();
        if (empty($registeredCompany) || $registeredCompany->isEmpty()) {
           return response()->json(['status' => 0,'message' => "Company Registered address not found.."]); 
        }
        if ($registeredCompany->count() != 1) {
           return response()->json(['status' => 0,'message' => "Multiple Company Registered addresses found.."]); 
        }
        $registeredCompany = $registeredCompany->toArray();
        $registeredCompany = $registeredCompany[0];
        if (empty($registeredCompany['bank_account_id'])) {
          return response()->json(['status' => 0,'message' => "No bank detail found for the Registered Company."]);  
        }
        $requestedData = $request->all();
        $decryptedData = _decrypt($requestedData['encData']);
        if (empty($decryptedData)) {
            return response()->json(['status' => 0,'message' => 'Data modified, Please try again.']); 
        }
        list($user_id, $company_id, $biz_addr_id) = explode('|', $decryptedData);
        if ($url_user_id != $user_id) {
           return response()->json(['status' => 0,'message' => 'Data can not be modified.']); 
        }
        $companyDetail = $this->_getCompanyDetail($company_id);
        if ($companyDetail['status'] != 'success') {
           return response()->json(['status' => 0,'message' => $companyDetail['message']]); 
        }
        $company_data = $companyDetail['data'];
        $billingDetail = $this->_getBillingDetail($biz_addr_id);
        if ($billingDetail['status'] != 'success') {
           return response()->json(['status' => 0,'message' => $billingDetail['message']]); 
        }
        $billing_data = $billingDetail['data'];
        $companyStateId = $company_data['state_id'];
        $userStateId = $billing_data['state_id'];

        $txnsData = $this->UserInvRepo->getUserInvoiceTxns($user_id, $invoice_type, $trans_ids);
        if(empty($txnsData) ||  $txnsData->isEmpty()){
            return response()->json(['status' => 0,'message' => 'No transaction found for the user.']); 
        }

        $lmsDetails = LmsUser::getLmsDetailByUserId($user_id);
        if (empty($lmsDetails) ||  $lmsDetails->isEmpty()) {
            return response()->json(['status' => 0,'message' => 'Lms Detail not found for the user.']);
        }
        $virtual_acc_id = $lmsDetails[0]->virtual_acc_id;
        $origin_of_recipient = [
            'reference_no' => $reference_no,
            'invoice_no' => $invoice_no,
            'place_of_supply' => $state_name,
            'invoice_date' => $invoice_date,
            'due_date' => $due_date,
            'virtual_acc_id' => $virtual_acc_id,
        ];
        $is_state_diffrent = ($userStateId != $companyStateId);
        $inv_data = $this->_calculateInvoiceTxns($txnsData, $is_state_diffrent);
        $intrest_charges = $inv_data[0];
        $total_sum_of_rental = $inv_data[1];
        $data = [
            'company_data' => $company_data,
            'billingDetails' => $billing_data,
            'origin_of_recipient' => $origin_of_recipient, 
            'intrest_charges' => $intrest_charges,
            'total_sum_of_rental' => $total_sum_of_rental,
            'registeredCompany' => $registeredCompany,
        ];
        $view = $this->viewInvoiceAsPDF($data);
        return response()->json(['status' => 1,'view' => base64_encode($view)]); 
    }


    /**
     * Save invoice as per User.
     *
     */
    public function saveUserInvoice(Request $request) {
        try {
            $url_user_id = $request->get('user_id');
            $invoice_type = $request->get('invoice_type');
            $trans_ids = $request->get('trans_id');
            $invoice_date = $request->get('invoice_date');
            $due_date = $request->get('due_date');
            $reference_no = $request->get('reference_no');
            if (!is_array($trans_ids) || empty($trans_ids)) {
                return redirect()->route('view_user_invoice', ['user_id' => $url_user_id])->with('error', 'No selected txns found for the invoice.');
            }
            if (empty(preg_replace('#[^A-Z0-9]+#', '', strtoupper($reference_no)))) {
               return redirect()->route('view_user_invoice', ['user_id' => $url_user_id])->with('error', 'Invalid Reference No found.');
            }
            $registeredCompany  = $this->UserInvRepo->getCompanyRegAddr();
            if (empty($registeredCompany) || $registeredCompany->isEmpty()) {
               return redirect()->route('view_user_invoice', ['user_id' => $url_user_id])->with('error', 'Company Registered address not found..'); 
            }
            if ($registeredCompany->count() != 1) {
               return redirect()->route('view_user_invoice', ['user_id' => $url_user_id])->with('error', 'Multiple Company Registered addresses found..'); 
            }
            $registeredCompany = $registeredCompany->toArray();
            $registeredCompany = $registeredCompany[0];
            if (empty($registeredCompany['bank_account_id'])) {
              return redirect()->route('view_user_invoice', ['user_id' => $url_user_id])->with('error', 'No bank detail found for the Registered Company.'); 
            }
            $requestedData = $request->all();
            $decryptedData = _decrypt($requestedData['encData']);
            if (empty($decryptedData)) {
                return redirect()->route('view_user_invoice', ['user_id' => $url_user_id])->with('error', 'Data modified, Please try again.');
            }
            list($user_id, $company_id, $biz_addr_id, $user_invoice_rel_id) = explode('|', $decryptedData);
            if ($url_user_id != $user_id) {
               return redirect()->route('view_user_invoice', ['user_id' => $url_user_id])->with('error', 'Data can not be modified.');
            }
            $companyDetail = $this->_getCompanyDetail($company_id);
            if ($companyDetail['status'] != 'success') {
               return redirect()->route('view_user_invoice', ['user_id' => $url_user_id])->with('error', $companyDetail['message']);
            }
            $company_data = $companyDetail['data'];
            $billingDetail = $this->_getBillingDetail($biz_addr_id);
            if ($billingDetail['status'] != 'success') {
               return redirect()->route('view_user_invoice', ['user_id' => $url_user_id])->with('error', $billingDetail['message']);
            }
            $billing_data = $billingDetail['data'];
            $companyStateId = $company_data['state_id'];
            $userStateId = $billing_data['state_id'];

            $txnsData = $this->UserInvRepo->getUserInvoiceTxns($url_user_id, $invoice_type, $trans_ids, true);
            if(empty($txnsData) ||  $txnsData->isEmpty()){
                return redirect()->route('view_user_invoice', ['user_id' => $url_user_id])->with('error', 'No remaining txns found for the invoice.');
            }
            
            $lastInvData = $this->UserInvRepo->getLastInvoiceSerialNo($invoice_type);
            $invSerialNo = sprintf('%04d', (($lastInvData->inv_serial_no ?? 0) + 1) ?? rand(0, 9999));
            $InvoiceNoArr = explode('/',$requestedData['invoice_no']);
            $InvoiceNoArr[3] = $invSerialNo;
            $newInvoiceNo = implode('/',$InvoiceNoArr);

            $is_state_diffrent = ($userStateId != $companyStateId);
            $inv_data = $this->_calculateInvoiceTxns($txnsData, $is_state_diffrent);
            $intrest_charges = $inv_data[0];
            $total_sum_of_rental = $inv_data[1];
            $requestedData['created_at'] = \carbon\Carbon::now();
            $requestedData['created_by'] = Auth::user()->user_id;
            unset($company_data['state']);
            $bank_id = bankDetailIsOfRegisteredCompanyInInvoice() ? $registeredCompany['bank_account_id'] : $company_data['bank_id'];
            $userInvoiceData = [
                'user_id' => $requestedData['user_id'],
                'user_invoice_rel_id' => $user_invoice_rel_id,
                'user_gst_state_id' => $userStateId,
                'comp_gst_state_id' => $companyStateId,
                'pan_no' => $billing_data['pan_no'],
                'biz_gst_no' => $billing_data['gstin_no'],
                'gst_addr' => $billing_data['address'],
                'biz_entity_name' => $billing_data['name'],
                'reference_no' => $reference_no,
                'invoice_type' => $requestedData['invoice_type'],
                'invoice_no' => $newInvoiceNo,
                'inv_serial_no' => $invSerialNo,
                'invoice_date' => Carbon::createFromFormat('d/m/Y', $invoice_date)->format('Y-m-d H:i:s'),
                'due_date' => Carbon::createFromFormat('d/m/Y', $due_date)->format('Y-m-d H:i:s'),
                'invoice_state_code' => $company_data['state_code'],
                'place_of_supply' => $billing_data['state_name'],
                'tot_no_of_trans' => count($requestedData['trans_id']),
                'tot_paid_amt' => $total_sum_of_rental ?? 0,
                'comp_addr_id' => $company_data['comp_id'],
                'inv_comp_data' => json_encode($company_data),
                'registered_comp_id' => $registeredCompany['comp_addr_id'],
                'comp_addr_register' => json_encode($registeredCompany),
                'bank_id' => $bank_id,
                'is_active' => 1,
                'created_at' => $requestedData['created_at'],
                'created_by' => $requestedData['created_by'],
            ];
            $invoiceResp = $this->UserInvRepo->saveUserInvoice($userInvoiceData);
            if(!empty($invoiceResp->user_invoice_id)){
                $userInvoice_id = $invoiceResp->user_invoice_id;
                foreach ($intrest_charges as $key => $txnsRec) {
                    $update_transactions[0] = $txnsRec['trans_id'];
                   $user_invoice_trans_data[] = [
                        'user_invoice_id' => $userInvoice_id,
                        'trans_id' => $txnsRec['trans_id'],
                        'sac_code' => $txnsRec['sac'],
                        'base_amount' => $txnsRec['base_amt'],
                        'sgst_rate' => $txnsRec['sgst_rate'],
                        'sgst_amount' => $txnsRec['sgst_amt'],
                        'cgst_rate' => $txnsRec['cgst_rate'],
                        'cgst_amount' => $txnsRec['cgst_amt'],
                        'igst_rate' => $txnsRec['igst_rate'],
                        'igst_amount' => $txnsRec['igst_amt'],
                   ]; 
                   $totalGst = ($txnsRec['sgst_amt'] + $txnsRec['cgst_amt'] + $txnsRec['igst_amt']);
                   $totalGstRate = ($txnsRec['sgst_rate'] + $txnsRec['cgst_rate'] + $txnsRec['igst_rate']);
                   $data = ['is_invoice_generated' => 1, 'gst_per' => $totalGstRate, 'soa_flag' => 1, 'base_amt' => $txnsRec['base_amt'], 'gst_amt' => $totalGst];
                   if ($invoice_type == 'C')
                        $this->checkIsTransactionUpdatable($txnsRec['trans_id']);

                   $isInvoiceGenerated = $this->UserInvRepo->updateIsInvoiceGenerated($update_transactions, $data);
                }
                $UserInvoiceTxns = $this->UserInvRepo->saveUserInvoiceTxns($user_invoice_trans_data);
                // $isInvoiceGenerated = $this->UserInvRepo->updateIsInvoiceGenerated($update_transactions);
                if ($UserInvoiceTxns == true) {

                    $whereActivi['activity_code'] = 'save_user_invoice';
                    $activity = $this->master->getActivity($whereActivi);
                    if(!empty($activity)) {
                        $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                        $activity_desc = 'Create Invoice Int/Charge Invoice (Manage Sanction Cases) ';
                        $arrActivity['app_id'] = null;
                        $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['userInvoiceData'=>$userInvoiceData, 'intrest_charges'=>$intrest_charges]), $arrActivity);
                    }                     
                    
                   return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('message', 'Invoice generated Successfully');
                }
            }else{
               return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Some error occured while inserting UserInvoice Data');
            }
        } catch (Exception $ex) {
             return redirect()->route('view_user_invoice', ['user_id' => $user_id])->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    private function checkIsTransactionUpdatable($trans_id)
    {
        if (isset($trans_id)) {
            Transactions::where('parent_trans_id',$trans_id)->orWhere('trans_id',$trans_id)->update(['is_transaction' => 1]);
        }
    }

    public function downloadUserInvoice(Request $request){
        $user_id = $request->get('user_id');
        $user_invoice_id = $request->get('user_invoice_id');
        $invData = $this->UserInvRepo->getInvoiceById($user_invoice_id);
        if (empty($invData)) {
           return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'No Detail found for the Invoice.'); 
        }
        $reference_no = $invData->reference_no;
        $invoice_no = $invData->invoice_no;

        $file = 'public/capsaveInvoice/'.str_replace("/","_",strtoupper($invoice_no)).'.pdf';

        if (Storage::disk('local')->exists($file)) {
            return Storage::download($file);
        }

        $state_name = $invData->place_of_supply;
        $invoice_type = $invData->invoice_type;
        $invoice_date = $invData->invoice_date;
        $due_date = $invData->due_date;

        $company_id = $invData->comp_addr_id;
        $registered_comp_id = $invData->registered_comp_id;

        $bank_account_id = $invData->bank_id;
        $totalTxnsInInvoice = $invData->userInvoiceTxns;

        $trans_ids = [];
        foreach ($totalTxnsInInvoice as $key => $value) {
           $trans_ids[] =  $value->trans_id;
        }
        if (!in_array($invoice_type, ['I', 'C'])) {
           return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Invalid Invoice Type found.'); 
        }
        if (empty(preg_replace('#[^0-9]+#', '', $user_id))) {
           return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Invalid UserId Found.');
        }
        $userStateId = $invData->user_gst_state_id;
        $companyStateId = $invData->comp_gst_state_id;

        $stateDetail = $this->UserInvRepo->getStateById($userStateId);
        if (empty($stateDetail)) {
           return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'State detail not found for the user address.');
        }

        $lmsDetails = LmsUser::getLmsDetailByUserId($user_id);
        if (empty($lmsDetails) ||  $lmsDetails->isEmpty()) {
            return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Lms Detail not found for the user.');
        }
        $virtual_acc_id = $lmsDetails[0]->virtual_acc_id;
        $billingDetails = [
            'name' => $invData->biz_entity_name,
            'address' => $invData->gst_addr,
            'pan_no' => $invData->pan_no,
            'state_id' => $stateDetail->id,
            'state_name' => $stateDetail->name,
            'state_no' => $stateDetail->state_no,
            'state_code' => $stateDetail->state_code,
            'gstin_no' => $invData->biz_gst_no,
        ];
        $origin_of_recipient = [
            'reference_no' => $reference_no,
            'invoice_no' => $invoice_no,
            'place_of_supply' => $state_name,
            'invoice_date' => $invoice_date,
            'due_date' => $due_date,
            'virtual_acc_id' => $virtual_acc_id,
        ];
        if (empty($invData->inv_comp_data)) {
            $companyDetail = $this->_getCompanyDetail($company_id, $bank_account_id);
            if ($companyDetail['status'] != 'success') {
                return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', $companyDetail['message']);
            }
            $company_data = $companyDetail['data'];
        }else{
            $company_data = json_decode($invData->inv_comp_data, true);
        }
        $is_state_diffrent = ($userStateId != $companyStateId);
        $intrest_charges = [];
        $total_sum_of_rental = 0;
        foreach ($totalTxnsInInvoice as  $key => $invTrans) {
            //$transDetail = $this->UserInvRepo->getTxnByTransId($invTrans->trans_id);
            //if (empty($transDetail->transType)) {
              // return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Transaction Type detail not found for the used transaction.');
          //  }
            $igst_amt = $invTrans->igst_amount;
            $igst_rate = $invTrans->igst_rate;
            $cgst_amt = $invTrans->cgst_amount;
            $cgst_rate = $invTrans->cgst_rate;
            $sgst_amt = $invTrans->sgst_amount;
            $sgst_rate = $invTrans->sgst_rate;
            $base_amt = $invTrans->base_amount;
            $sac_code = $invTrans->sac_code;

            $txn = $invTrans->trans;
            $invoice_no = NULL;
            $invoiceNoFound = $txn->invoiceDisbursed->invoice->invoice_no ?? NULL;

            if (isset($invoiceNoFound)) {
               $invoice_no = "($invoiceNoFound)";
            }
            $desc = $txn->transType->trans_name;
            if ($txn->trans_type == config('lms.TRANS_TYPE.INTEREST')) {
                $desc =  "Interest for period " . date('d-M-Y', strtotime($txn->fromIntDate)) . " To " . date('d-M-Y', strtotime($txn->toIntDate));
            } 

            if ($txn->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')) {
                $dueDate = strtotime($txn->toIntDate); // or your date as well
                $now = strtotime($txn->fromIntDate);
                $datediff = ($dueDate - $now);
                $OdandInterestRate = $txn->InvoiceDisbursed->invoice->program_offer->overdue_interest_rate + $txn->InvoiceDisbursed->invoice->program_offer->interest_rate;
                $days = round($datediff / (60 * 60 * 24)) . 'days-From:' . date('d-M-Y', strtotime($txn->fromIntDate)) . " to " . date('d-M-Y', strtotime($txn->toIntDate)) . ' @ ' . $OdandInterestRate . '%';
            } else {
                $days = '---';
            }

            $intrest_charges[$key] = array(
                'trans_id' => $invTrans->trans_id,
                'desc' => $desc. " $invoice_no",
                'sac' => $sac_code,
                'base_amt' => round($base_amt,2),
                'sgst_rate' => ($sgst_rate != 0 ? $sgst_rate : 0),
                'sgst_amt' => ($sgst_amt != 0 ? $sgst_amt : 0),
                'cgst_rate' => ($cgst_rate != 0 ? $cgst_rate : 0),
                'cgst_amt' =>  ($cgst_amt != 0 ? $cgst_amt : 0),
                'igst_rate' => ($igst_rate != 0 ? $igst_rate : 0),
                'igst_amt' =>  ($igst_amt != 0 ? $igst_amt : 0),
                'trans_date' =>  $days,
            );
            $total_rental = round($base_amt + $sgst_amt + $cgst_amt + $igst_amt, 2);
            $total_sum_of_rental += $total_rental; 
            $intrest_charges[$key]['total_rental'] =  $total_rental; 
        }
        $registeredCompany = json_decode($invData->comp_addr_register, true);
        if (empty($registeredCompany['bank_account_id'])) {
            $registeredCompany  = $this->UserInvRepo->getCompanyRegAddr();
            if (empty($registeredCompany) || $registeredCompany->isEmpty()) {
               return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Company Registered address not found..'); 
            }
            if ($registeredCompany->count() != 1) {
               return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Multiple Company Registered addresses found..'); 
            }
            $registeredCompany = $registeredCompany->toArray();
            $registeredCompany = $registeredCompany[0];
          //return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'No bank detail found for the Registered Company.'); 
        }
        $data = [
            'company_data' => $company_data,
            'billingDetails' => $billingDetails,
            'origin_of_recipient' => $origin_of_recipient, 
            'intrest_charges' => $intrest_charges,
            'total_sum_of_rental' => $total_sum_of_rental,
            'registeredCompany' => $registeredCompany,
        ];
        return $this->viewInvoiceAsPDF($data, true);
    }


    private function _calculateInvoiceTxns($txnsData = [], $is_state_diffrent = false) {
        $intrest_charges = [];
        if (empty($txnsData)) {
           return [array(), 0];
        }
        $total_sum_of_rental = 0;
        $where = [];
        $activeGst = GstTax::getActiveGST($where);
        $totalGST = 0;
        if (!$activeGst->isEmpty()) {
            $totalGST = $activeGst[0]->tax_value ?? 0;
        }
        foreach ($txnsData as  $key => $txn) {
            $totalamount = $txn->amount;
            $igst_amt = 0;
            $igst_rate = 0;
            $cgst_amt = 0;
            $cgst_rate = 0;
            $sgst_amt = 0;
            $sgst_rate = 0;
            $base_amt = $totalamount;
            if ($txn->gst == 1) {
                $base_amt = (!is_null($txn->base_amt) ? $txn->base_amt : $totalamount * 100/(100 + $totalGST));
                if(!$is_state_diffrent) {
                    $cgst_rate = ($totalGST/2);
                    $cgst_amt = round((($base_amt * $cgst_rate)/100),2);
                    $sgst_rate = ($totalGST/2);
                    $sgst_amt = round((($base_amt * $sgst_rate)/100),2);
                } else {
                   $igst_rate = $totalGST;
                   $igst_amt = round((($base_amt * $igst_rate)/100),2); 
                }
            }
            $invoice_no = NULL;
            $invoiceNoFound = $txn->invoiceDisbursed->invoice->invoice_no ?? NULL;

            if (isset($invoiceNoFound)) {
               $invoice_no = "($invoiceNoFound)";
            }
            $sac_code = $txn->transType->charge->sac_code ?? '0000';           
            $desc = $txn->transType->trans_name;
            if ($txn->trans_type == config('lms.TRANS_TYPE.INTEREST')) {
                $desc =  "Interest for period " . date('d-M-Y', strtotime($txn->fromIntDate)) . " To " . date('d-M-Y', strtotime($txn->toIntDate));
                $sac_code = config('lms.SAC_CODE_FOR_INT_INVOICE');
            } 
            if ($txn->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')) {
                $dueDate = strtotime($txn->toIntDate); // or your date as well
                $now = strtotime($txn->fromIntDate);
                $datediff = abs($dueDate - $now);
                $OdandInterestRate = $txn->InvoiceDisbursed->invoice->program_offer->overdue_interest_rate + $txn->InvoiceDisbursed->invoice->program_offer->interest_rate;
                $days = (round($datediff / (60 * 60 * 24)) + 1) . ' days -From:' . date('d-M-Y', strtotime($txn->fromIntDate)) . " to " . date('d-M-Y', strtotime($txn->toIntDate)) . ' @ ' . $OdandInterestRate . '%';                
            } else {
                $days = '---';
            }
            
            $intrest_charges[$key] = array(
                'trans_id' => $txn->trans_id,
                'desc' => $desc . " $invoice_no",
                'sac' => $sac_code,
                'base_amt' => round($base_amt,2),
                'sgst_rate' => $sgst_rate,
                'sgst_amt' => $sgst_amt,
                'cgst_rate' => $cgst_rate,
                'cgst_amt' =>  $cgst_amt,
                'igst_rate' => $igst_rate,
                'igst_amt' =>  $igst_amt,
                'trans_date' =>  $days,
            );
            $total_rental = round($base_amt + $sgst_amt + $cgst_amt + $igst_amt, 2);
            $total_sum_of_rental += $total_rental; 
            $intrest_charges[$key]['total_rental'] =  $total_rental; 
        }
        return [$intrest_charges, $total_sum_of_rental];
    }

    /**
     * Display invoice as per User.
     *
     */
    public function viewInvoiceAsPDF($pdfData = [], $download = false) {
        view()->share($pdfData);
        ini_set("memory_limit", "-1");
        if ($download==true) {
          $pdf = PDF::loadView('lms.invoice.generate_invoice');
          return $pdf->download('pdfview.pdf');
        }
        return view('lms.invoice.generate_invoice');
    }

    /**
     * Get user invoice location
     */
    public function userInvoiceLocation(Request $request) {
        try {
            $user_id = $request->get('user_id');
            $where = ['user_id' => $user_id];
            $allApps = $this->UserInvRepo->getAllAppData($where);
            $bizIds = [];
            foreach ($allApps as $key => $application) {
               $bizIds[$application->biz_id] = $application->biz_id;
            }
            /*$latestApp = $this->UserInvRepo->getUsersLatestApp($user_id);
            $latestBizId = $latestApp->biz_id ? $latestApp->biz_id : null;            
            $userAddresswithbiz = $this->UserInvRepo->getAddressByUserId($user_id, [$latestBizId]);
            $bizIds = [$latestBizId];*/
            $userAddresswithbiz = $this->UserInvRepo->getAddressByUserId($user_id, $bizIds);
            $capsave_addr = $this->UserInvRepo->getCapsavAddr();
            $result = $this->getUserLimitDetais($user_id);
            return view('lms.invoice.user_invoice_location')->with(['user_id'=> $user_id, 'capsave_addr' => $capsave_addr, 'user_addr' => $userAddresswithbiz,'userInfo' =>  $result['userInfo'], 'application' => $result['application'], 'anchors' =>  $result['anchors']]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * POST save user invoice location
     */
    public function saveUserInvoiceLocation(Request $request) {
        try {
            $arrUserData = $request->all();
            $user_id = $request->get('user_id');
            $arrUserData['created_at'] = \carbon\Carbon::now();
            $arrUserData['created_by'] = Auth::user()->user_id;
            if(empty($arrUserData['capsave_state'])) {
                return redirect()->route('user_invoice_location', ['user_id' => $user_id])->with('error', 'State are not present in "Capsave Location"');
            }
            if(empty($arrUserData['user_state'])) {
                return redirect()->route('user_invoice_location', ['user_id' => $user_id])->with('error', 'State are not present in "Customer Primary Location"');
            }
            $userInvoiceData = [
                'user_id' => $arrUserData['user_id'],
                'biz_addr_id' => $arrUserData['customer_pri_loc'],
                'company_id' => $arrUserData['capsav_location'],
                'company_state_id' => $arrUserData['capsave_state'] ?? 0,
                'biz_addr_state_id' => $arrUserData['user_state'] ?? 0,
                'is_active' => 1,
                'created_at' => $arrUserData['created_at'],
                'created_by' => $arrUserData['created_by'],
            ];
            $userInvData = [
                'user_id' => $arrUserData['user_id'],
                'biz_addr_id' => $arrUserData['customer_pri_loc'],
                'company_id' => $arrUserData['capsav_location'],
                'is_active' => 1,
            ];
            $checkData = $this->UserInvRepo->checkUserInvoiceLocation($userInvData);
            if($checkData) {
                return redirect()->route('user_invoice_location', ['user_id' => $user_id])->with('error', 'Same address and company are already mapped and active');
            }
            $this->UserInvRepo->unPublishAddr($user_id);
            $arrUserData['updated_at'] = \carbon\Carbon::now();
            $arrUserData['updated_by'] = Auth::user()->user_id;
            $status = $this->UserInvRepo->saveUserInvoiceLocation($userInvoiceData); 
            if($status) {
                return redirect()->route('user_invoice_location', ['user_id' => $user_id])->with('message', 'Address save Successfully');
            } else {
                return redirect()->route('user_invoice_location', ['user_id' => $user_id])->with('error', 'Some error occured while saving');
            }
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    // user_invoice relation get state id for capsave
    public function getCapsavInvoiceState(Request $request) {
        $cities = DB::table("mst_company")
            ->select("comp_addr_id", "state")
            ->where("comp_addr_id",$request->state)
            ->pluck("state", "comp_addr_id");

            return response()->json($cities);
    }

    // user_invoice relation get state id for user
    public function getUserInvoiceState(Request $request) {
        $cities = DB::table("biz_addr")
            ->select("biz_addr_id", "state_id")
            ->where("biz_addr_id",$request->state)
            ->where("is_active",1)
            ->where("is_default",1)
            ->where("address_type",6)
            ->pluck("state_id", "biz_addr_id");
            return response()->json($cities);
    }

    public function unpublishUsereAddr(Request $request) {
       try{
        $user_id = $request->get('user_id');
        $data = $this->UserInvRepo->unPublishAddr((int) $user_id);
        if($data) {
            return redirect()->route('user_invoice_location', ['user_id' => $user_id])->with('message', 'All address are unpublished');
        } else {
            return redirect()->route('user_invoice_location', ['user_id' => $user_id])->with('error', 'Some error occured!');
        }
       } catch (Exception $ex) {
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
       }
    }

    
     /* use function for the manage sention tabs */ 
    
    public  function  getUserLimitDetais($user_id) 
   {
            try {
                $totalLimit = 0;
                $totalCunsumeLimit = 0;
                $consumeLimit = 0;
                $transactions = 0;
                $userInfo = $this->userRepo->getCustomerDetail($user_id);
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
                
                $data['userInfo'] = $userInfo;
                $data['application'] = $application;
                $data['anchors'] = $anchors;
                return $data;
            } catch (Exception $ex) {
                dd($ex);
            }
    }

    public function dateFormat($date){
        if($date){
            $date_arr = explode('-',$date);
            $temp = $date_arr[0];
            $date_arr[0] = $date_arr[2];
            $date_arr[2] = $temp;
            $new_date_format = implode('-',$date_arr);
            return $new_date_format;
        }else{
            return '';
        }
    }
   
    public function generateCapsaveInvoice($transId = [], $userId, $invoiceType, $appId  = null, $invoiceDate = null, $dueDate  = null){
        DB::beginTransaction();
        $status = 0;
        $result = ['status' => &$status, 'error' => &$error, 'success' => &$success];
        try {
            $success = [];
            $error = [];
            $reference_no = \Helpers::formatIdWithPrefix($appId);
            $invoice_date = $invoiceDate ?? Carbon::now()->format('Y-m-d H:i:s');
            $due_date = $dueDate ?? Carbon::now()->addDays(7)->format('Y-m-d H:i:s');
            $registeredCompany  = $this->UserInvRepo->getCompanyRegAddr();
            if (empty($registeredCompany) || $registeredCompany->isEmpty()) {
                $error[] = 'Company Registered address not found..';
                DB::rollback();
                return $result;
            }

            if ($registeredCompany->count() != 1) {
                $error[] = 'Multiple Company Registered addresses found..';
                DB::rollback();
                return $result;
            }

            $registeredCompany = $registeredCompany->first()->toArray();
            if (empty($registeredCompany['bank_account_id'])) {
                $error[] = 'No bank detail found for the Registered Company.'; 
                DB::rollback();
                return $result;
            }
            
            $userCompanyRelation  = $this->UserInvRepo->getUserCompanyRelation($userId);
            if (empty($userCompanyRelation)) {
                $error[] = 'No Relation found between Company and User.'; 
                DB::rollback();
                return $result;
            }

            $company_id = $userCompanyRelation->company_id ?? NULL;
            $biz_addr_id = $userCompanyRelation->biz_addr_id ?? NULL;
            $user_invoice_rel_id = $userCompanyRelation->user_invoice_rel_id ?? NULL;

            $companyDetail = $this->_getCompanyDetail($company_id);
            
            if ($companyDetail['status'] != 'success') {
                $error[] = $companyDetail['message'];
                DB::rollback();
                return $result;
            }

            $company_data = $companyDetail['data'];
            $billingDetail = $this->_getBillingDetail($biz_addr_id);
            
            if ($billingDetail['status'] != 'success') {
                $error[] = $billingDetail['message'];
                DB::rollback();
                return $result;
            }

            $billing_data = $billingDetail['data'];
            $companyStateId = $company_data['state_id'];
            $userStateId = $billing_data['state_id'];

            $txnsData = $this->UserInvRepo->getUserInvoiceTxns($userId, $invoiceType, $transId, true);
            if(empty($txnsData) ||  $txnsData->isEmpty()){
                $error[] = 'No remaining txns found for the invoice.';
                DB::rollback();
                return $result;
            }
            $origin_of_recipient = $this->_getOriginRecipent($company_id, $userId);
            $origin_of_recipient = $origin_of_recipient['data'];
            $invCat = null;
            if(strtoupper($invoiceType) == 'I'){
                $invCat = 'ZR';
            }
            elseif(strtoupper($invoiceType) == 'C'){
                $invCat = 'NZ';
            }
            
            $lastInvData = $this->UserInvRepo->getLastInvoiceSerialNo($invoiceType);
            $invSerialNo = sprintf('%04d', (($lastInvData->inv_serial_no ?? 0) + 1) ?? rand(0, 9999));
            $newInvoiceNo = $origin_of_recipient['state_code'] . '/' . $origin_of_recipient['financial_year'] . '/' . $invCat . '/' . $invSerialNo;
            
            $is_state_diffrent = ($userStateId != $companyStateId);
            $inv_data = $this->_calculateInvoiceTxns($txnsData, $is_state_diffrent);
            $intrest_charges = $inv_data[0];
            $total_sum_of_rental = $inv_data[1];
            $bank_id = bankDetailIsOfRegisteredCompanyInInvoice() ? $registeredCompany['bank_account_id'] : $company_data['bank_id'];
            $created_at = Carbon::now();
            $created_by = Auth::user()->user_id ?? null;
            $userInvoiceData = [
                'user_id' => $userId,
                'user_invoice_rel_id' => $user_invoice_rel_id,
                'user_gst_state_id' => $userStateId,
                'comp_gst_state_id' => $companyStateId,
                'pan_no' => $billing_data['pan_no'],
                'biz_gst_no' => $billing_data['gstin_no'],
                'gst_addr' => $billing_data['address'],
                'biz_entity_name' => $billing_data['name'],
                'reference_no' => $reference_no,
                'invoice_type' => $invoiceType,
                'invoice_no' => $newInvoiceNo,
                'inv_serial_no' => $invSerialNo,
                'invoice_date' => $invoice_date,
                'due_date' => $due_date,
                'invoice_state_code' => $company_data['state_code'],
                'place_of_supply' => $billing_data['state_name'],
                'tot_no_of_trans' => count($transId),
                'tot_paid_amt' => $total_sum_of_rental ?? 0,
                'comp_addr_id' => $company_data['comp_id'],
                'inv_comp_data' => json_encode($company_data),
                'registered_comp_id' => $registeredCompany['comp_addr_id'],
                'comp_addr_register' => json_encode($registeredCompany),
                'bank_id' => $bank_id,
                'is_active' => 1,
                'created_at' => $created_at,
                'created_by' => $created_by,
            ];
            $invoiceResp = $this->UserInvRepo->saveUserInvoice($userInvoiceData);

            if(!empty($invoiceResp->user_invoice_id)){
                $userInvoice_id = $invoiceResp->user_invoice_id;
                foreach ($intrest_charges as $key => $txnsRec) {
                    $user_invoice_trans_data[] = [
                        'user_invoice_id' => $userInvoice_id,
                        'trans_id' => $txnsRec['trans_id'],
                        'sac_code' => $txnsRec['sac'],
                        'base_amount' => $txnsRec['base_amt'],
                        'sgst_rate' => $txnsRec['sgst_rate'],
                        'sgst_amount' => $txnsRec['sgst_amt'],
                        'cgst_rate' => $txnsRec['cgst_rate'],
                        'cgst_amount' => $txnsRec['cgst_amt'],
                        'igst_rate' => $txnsRec['igst_rate'],
                        'igst_amount' => $txnsRec['igst_amt'],
                ]; 
                $totalGst = ($txnsRec['sgst_amt'] + $txnsRec['cgst_amt'] + $txnsRec['igst_amt']);
                $totalGstRate = ($txnsRec['sgst_rate'] + $txnsRec['cgst_rate'] + $txnsRec['igst_rate']);
                $data = ['is_invoice_generated' => 1, 'gst_per' => $totalGstRate, 'soa_flag' => 1, 'base_amt' => $txnsRec['base_amt'], 'gst_amt' => $totalGst];
                if ($invoiceType == 'C')
                        $this->checkIsTransactionUpdatable($txnsRec['trans_id']);
                $isInvoiceGenerated = $this->UserInvRepo->updateIsInvoiceGenerated([$txnsRec['trans_id']], $data);
                }
                $UserInvoiceTxns = $this->UserInvRepo->saveUserInvoiceTxns($user_invoice_trans_data);
                if ($UserInvoiceTxns == true) {      
                    $status = 1;             
                    $success[] = 'Invoice generated Successfully';
                    DB::commit();
                    $pdfResult = $this->generateCapsaveInvoicePdf($userInvoice_id);
                    if($pdfResult['status']){
                        $success = array_merge($success,$pdfResult['success']);
                    }
                    return $result;
                }
            }else{
                $error[] = 'Some error occured while inserting UserInvoice Data';
                DB::rollback();
                return $result;
            }
        } catch (Exception $ex) {
            DB::rollback();
            return $result;
        }
    }
    
    public function generateCapsaveInvoicePdf($userInvoiceId = null){
        $error = [];
        $status = 0;
        $result = ['status' => &$status, 'error' => &$error, 'success' => &$success];
        if($userInvoiceId){
            $user_invoice_id = $userInvoiceId;
            $invData = $this->UserInvRepo->getInvoiceById($user_invoice_id);
            $user_id = $invData->user_id;

            if (empty($invData)) {
                $error[] = 'No Detail found for the Invoice.'; 
                return $result;
            }

            $reference_no = $invData->reference_no;
            $invoice_no = $invData->invoice_no;
            $state_name = $invData->place_of_supply;
            $invoice_type = $invData->invoice_type;
            $invoice_date = $invData->invoice_date;
            $due_date = $invData->due_date;
            $company_id = $invData->comp_addr_id;
            $registered_comp_id = $invData->registered_comp_id;
            $bank_account_id = $invData->bank_id;
            $totalTxnsInInvoice = $invData->userInvoiceTxns;
            $userStateId = $invData->user_gst_state_id;
            $companyStateId = $invData->comp_gst_state_id;

            $trans_ids = [];
            foreach ($totalTxnsInInvoice as $key => $value) {
            $trans_ids[] =  $value->trans_id;
            }
            if (!in_array($invoice_type, ['I', 'C'])) {
                $error[] = 'Invalid Invoice Type found.'; 
                return $result;
            }
            if (empty(preg_replace('#[^0-9]+#', '', $user_id))) {
                $error[] = 'Invalid UserId Found.';
                return $result;
            }
      
            $stateDetail = $this->UserInvRepo->getStateById($userStateId);
            if (empty($stateDetail)) {
                $error[] = 'State detail not found for the user address.';
                return $result;
            }

            $lmsDetails = LmsUser::getLmsDetailByUserId($user_id);
            if (empty($lmsDetails) ||  $lmsDetails->isEmpty()) {
                $error[] = 'Lms Detail not found for the user.';
                return $result;
            }

            $virtual_acc_id = $lmsDetails[0]->virtual_acc_id;
            $billingDetails = [
                'name' => $invData->biz_entity_name,
                'address' => $invData->gst_addr,
                'pan_no' => $invData->pan_no,
                'state_id' => $stateDetail->id,
                'state_name' => $stateDetail->name,
                'state_no' => $stateDetail->state_no,
                'state_code' => $stateDetail->state_code,
                'gstin_no' => $invData->biz_gst_no,
            ];
            $origin_of_recipient = [
                'reference_no' => $reference_no,
                'invoice_no' => $invoice_no,
                'place_of_supply' => $state_name,
                'invoice_date' => $invoice_date,
                'due_date' => $due_date,
                'virtual_acc_id' => $virtual_acc_id,
            ];
            if (empty($invData->inv_comp_data)) {
                $companyDetail = $this->_getCompanyDetail($company_id, $bank_account_id);
                if ($companyDetail['status'] != 'success') {
                    $error[] = $companyDetail['message'];
                    return $result;
                }
                $company_data = $companyDetail['data'];
            }else{
                $company_data = json_decode($invData->inv_comp_data, true);
            }
            $is_state_diffrent = ($userStateId != $companyStateId);
            $intrest_charges = [];
            $total_sum_of_rental = 0;
            foreach ($totalTxnsInInvoice as  $key => $invTrans) {
                $igst_amt = $invTrans->igst_amount;
                $igst_rate = $invTrans->igst_rate;
                $cgst_amt = $invTrans->cgst_amount;
                $cgst_rate = $invTrans->cgst_rate;
                $sgst_amt = $invTrans->sgst_amount;
                $sgst_rate = $invTrans->sgst_rate;
                $base_amt = $invTrans->base_amount;
                $sac_code = $invTrans->sac_code;

                $txn = $invTrans->trans;
                $invoice_no = NULL;
                $invoiceNoFound = $txn->invoiceDisbursed->invoice->invoice_no ?? NULL;

                if (isset($invoiceNoFound)) {
                $invoice_no = "($invoiceNoFound)";
                }
                $desc = $txn->transType->trans_name;
                if ($txn->trans_type == config('lms.TRANS_TYPE.INTEREST')) {
                    $desc =  "Interest for period " . date('d-M-Y', strtotime($txn->fromIntDate)) . " To " . date('d-M-Y', strtotime($txn->toIntDate));
                } 

                if ($txn->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')) {
                    $dueDate = strtotime($txn->toIntDate); // or your date as well
                    $now = strtotime($txn->fromIntDate);
                    $datediff = ($dueDate - $now);
                    $OdandInterestRate = $txn->InvoiceDisbursed->invoice->program_offer->overdue_interest_rate + $txn->InvoiceDisbursed->invoice->program_offer->interest_rate;
                    $days = round($datediff / (60 * 60 * 24)) . 'days-From:' . date('d-M-Y', strtotime($txn->fromIntDate)) . " to " . date('d-M-Y', strtotime($txn->toIntDate)) . ' @ ' . $OdandInterestRate . '%';
                } else {
                    $days = '---';
                }

                $intrest_charges[$key] = array(
                    'trans_id' => $invTrans->trans_id,
                    'desc' => $desc. " $invoice_no",
                    'sac' => $sac_code,
                    'base_amt' => round($base_amt,2),
                    'sgst_rate' => ($sgst_rate != 0 ? $sgst_rate : 0),
                    'sgst_amt' => ($sgst_amt != 0 ? $sgst_amt : 0),
                    'cgst_rate' => ($cgst_rate != 0 ? $cgst_rate : 0),
                    'cgst_amt' =>  ($cgst_amt != 0 ? $cgst_amt : 0),
                    'igst_rate' => ($igst_rate != 0 ? $igst_rate : 0),
                    'igst_amt' =>  ($igst_amt != 0 ? $igst_amt : 0),
                    'trans_date' =>  $days,
                );
                $total_rental = round($base_amt + $sgst_amt + $cgst_amt + $igst_amt, 2);
                $total_sum_of_rental += $total_rental; 
                $intrest_charges[$key]['total_rental'] =  $total_rental; 
            }
            $registeredCompany = json_decode($invData->comp_addr_register, true);
            
            $data = [
                'company_data' => $company_data,
                'billingDetails' => $billingDetails,
                'origin_of_recipient' => $origin_of_recipient, 
                'intrest_charges' => $intrest_charges,
                'total_sum_of_rental' => $total_sum_of_rental,
                'registeredCompany' => $registeredCompany,
            ];
            view()->share($data);
            ini_set("memory_limit", "-1");
            $pdf = PDF::loadView('lms.invoice.generate_invoice');
            Storage::disk('local')->put('public/capsaveInvoice/'.str_replace("/","_",strtoupper($data['origin_of_recipient']['invoice_no'])).'.pdf', $pdf->output());
            $status = 1;             
            $success[] = 'Invoice PDF generated Successfully';
        }
        return $result;
    }

    public function userInvoiceMail(){
        // $data = [
        //     'company_data' => $company_data,
        //     'billingDetails' => $billingDetails,
        //     'origin_of_recipient' => $origin_of_recipient, 
        //     'intrest_charges' => $intrest_charges,
        //     'total_sum_of_rental' => $total_sum_of_rental,
        //     'registeredCompany' => $registeredCompany,
        // ];
        // dd($data);
        // view()->share($data);
        // ini_set("memory_limit", "-1");
        // $pdf = PDF::loadView('lms.invoice.generate_invoice');

        $emailData = array(
            'email' => $email,
            'name' => 'Capsave Finance PVT LTD.',
            'subject' => 'subject',
            'body' => 'body',
            'data' => $approverAppData,
          );
        \Event::dispatch("USER_INVOICE_MAIL", serialize($emailData));
    }

}
