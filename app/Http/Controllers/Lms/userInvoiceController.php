<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\UserInvoiceInterface as InvUserInvRepoInterface;
use App\Inv\Repositories\Models\Master\State;
use DB;
use Carbon\Carbon;
use PDF;
use Session;
use Helpers;
// use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

class userInvoiceController extends Controller
{
    //use ApplicationTrait;

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
            $user_id = $request->get('user_id');
            $userCompanyRelation  = $this->UserInvRepo->getUserCompanyRelation($user_id);
            if (empty($userCompanyRelation)) {
                return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'No Relation found between Company and User.'); 
            }
            $registeredCompany  = $this->UserInvRepo->getCompanyRegAddr();
            $allApplications  = $this->UserInvRepo->getUserAllApplicationsDetail($user_id);
            if (empty($registeredCompany) || $registeredCompany->isEmpty()) {
               return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Company Registered address not found..'); 
            }
            if ($registeredCompany->count() != 1) {
               return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Multiple Company Registered addresses found..'); 
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
            $encData = _encrypt("$user_id|$company_id|$biz_addr_id|$user_invoice_rel_id");
            $origin_of_recipient = $origin_of_recipient['data'];
            return view('lms.invoice.create_user_invoice')->with(['user_id'=> $user_id, 'billingDetails' => $billingDetails, 'origin_of_recipient' => $origin_of_recipient, 'encData' => $encData, 'allApplications' => $allApplications]);
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
        if (!$bankDetailsFound) {
            $response['message'] = 'No BankDetail is found for the Company.';
            return $response;
        }
        $activeBankAcc = NULL;
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
        if (empty($activeBankAcc)) {
            $response['message'] = 'No default Bank Detail found for the Company.';
            return $response;
        }
        if (empty($companyDetail->getStateDetail)) {
            $response['message'] = 'State Detail not found for the the company.';
            return $response;
        }
        $CompanyArr = [
            'comp_id' => $companyDetail->comp_addr_id,
            'name' => $companyDetail->cmp_name,
            'address' => $companyDetail->cmp_add,
            'state' => $companyDetail->getStateDetail,
            'city' => $companyDetail->city,
            'phone' => $companyDetail->cmp_mobile,
            'email' => $companyDetail->cmp_email,
            'pan_no' => $companyDetail->pan_no,
            'gst_no' => $companyDetail->gst_no,
            'cin_no' => $companyDetail->cin_no,
            'bank_id' => $activeBankAcc->bank_account_id,
            'bank_name' => $activeBankAcc->bank->bank_name,
            'acc_no' => $activeBankAcc->acc_no,
            'branch_name' => $activeBankAcc->branch_name,
            'ifsc_code' => $activeBankAcc->ifsc_code,
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
        $invoice_no_id = $this->UserInvRepo->getNextInv(['user_id' => $user_id])->invoice_no_id;
        $curr_date = date('y-m-d');
        $origin_of_recipient = [
            'reference_no' => 'RENT'. $reference_no,
            'financial_year' => getFinancialYear($curr_date),
            'rand_4_no' => sprintf('%04d', $invoice_no_id ?? rand(0, 9999)),
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
        return response()->json(['status' => 1,'view' => base64_encode($view)]); 
    }

    public function previewUserInvoice(Request $request){
        $url_user_id = $request->get('user_id');
        $invoice_date = $request->get('invoice_date');
        $reference_no = $request->get('reference_no');
        $invoice_no = $request->get('invoice_no');
        $state_name = $request->get('state_name');
        $invoice_type = $request->get('invoice_type');
        $trans_ids = $request->get('trans_id');
        $trans_ids = $request->get('trans_id');

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
        $origin_of_recipient = [
            'reference_no' => $reference_no,
            'invoice_no' => $invoice_no,
            'place_of_supply' => $state_name,
            'invoice_date' => $invoice_date,
        ];
        $is_state_diffrent = ($userStateId != $companyStateId);
        $inv_data = $this->_calculateInvoiceTxns($txnsData, $is_state_diffrent);
        $intrest_charges = $inv_data[0];
        $total_sum_of_rental = $inv_data[1];
        $registeredCompany = $registeredCompany->toArray();
        $data = [
            'company_data' => $company_data,
            'billingDetails' => $billing_data,
            'origin_of_recipient' => $origin_of_recipient, 
            'intrest_charges' => $intrest_charges,
            'total_sum_of_rental' => $total_sum_of_rental,
            'registeredCompany' => $registeredCompany[0],
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
            $requestedData = $request->all();
            $decryptedData = _decrypt($requestedData['encData']);
            if (empty($decryptedData)) {
                return response()->json(['status' => 0,'message' => 'Data modified, Please try again.']); 
            }
            list($user_id, $company_id, $biz_addr_id, $user_invoice_rel_id) = explode('|', $decryptedData);
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

            $txnsData = $this->UserInvRepo->getUserInvoiceTxns($user_id, $invoice_type, $trans_ids, true);
            if(empty($txnsData) ||  $txnsData->isEmpty()){
                return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'No remaining txns found for the invoice.');
            }
            $is_state_diffrent = ($userStateId != $companyStateId);
            $inv_data = $this->_calculateInvoiceTxns($txnsData, $is_state_diffrent);
            $intrest_charges = $inv_data[0];
            $total_sum_of_rental = $inv_data[1];
            $requestedData['created_at'] = \carbon\Carbon::now();
            $requestedData['created_by'] = Auth::user()->user_id;
            
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
                'invoice_no' => $requestedData['invoice_no'],
                'invoice_date' => Carbon::createFromFormat('d/m/Y', $invoice_date)->format('Y-m-d H:i:s'),
                'invoice_state_code' => $company_data['state_code'],
                'place_of_supply' => $billing_data['state_name'],
                'tot_no_of_trans' => count($requestedData['trans_id']),
                'tot_paid_amt' => $total_sum_of_rental ?? 0,
                'comp_addr_id' => $company_data['comp_id'],
                'registered_comp_id' => $registeredCompany['comp_addr_id'],
                'comp_addr_register' => json_encode($registeredCompany),
                'bank_id' => $company_data['bank_id'],
                'is_active' => 1,
                'created_at' => $requestedData['created_at'],
                'created_by' => $requestedData['created_by'],
            ];
            $invoiceResp = $this->UserInvRepo->saveUserInvoice($userInvoiceData);
            if(!empty($invoiceResp->user_invoice_id)){
                $userInvoice_id = $invoiceResp->user_invoice_id;
                foreach ($intrest_charges as $key => $txnsRec) {
                   $update_transactions[] = $txnsRec['trans_id'];
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
                }
                $UserInvoiceTxns = $this->UserInvRepo->saveUserInvoiceTxns($user_invoice_trans_data);
                $isInvoiceGenerated = $this->UserInvRepo->updateIsInvoiceGenerated($update_transactions);
                if ($UserInvoiceTxns == true) {
                   return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('message', 'Invoice generated Successfully');
                }
            }else{
               return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Some error occured while inserting UserInvoice Data');
            }
        } catch (Exception $ex) {
             return redirect()->route('view_user_invoice', ['user_id' => $user_id])->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function downloadUserInvoice(Request $request){
        $user_id = $request->get('user_id');
        $user_invoice_id = $request->get('user_invoice_id');
        $invData = $this->UserInvRepo->getInvoiceById($user_invoice_id);
        $reference_no = $invData->reference_no;
        $invoice_no = $invData->invoice_no;
        $state_name = $invData->place_of_supply;
        $invoice_type = $invData->invoice_type;
        $invoice_date = $invData->invoice_date;
        $company_id = $invData->comp_addr_id;
        $registered_comp_id = $invData->registered_comp_id;

        $bank_account_id = $invData->bank_id;
        $totalTxnsInInvoice = $invData->userInvoiceTxns->toArray();
        $trans_ids = [];
        foreach ($totalTxnsInInvoice as $key => $value) {
           $trans_ids[] =  $value['trans_id'];
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
        ];

        $companyDetail = $this->_getCompanyDetail($company_id, $bank_account_id);
        if ($companyDetail['status'] != 'success') {
            return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', $companyDetail['message']);
        }
        $company_data = $companyDetail['data'];
        $is_state_diffrent = ($userStateId != $companyStateId);
        $intrest_charges = [];
        $total_sum_of_rental = 0;
        foreach ($totalTxnsInInvoice as  $key => $invTrans) {
            $transDetail = $this->UserInvRepo->getTxnByTransId($invTrans['trans_id']);
            if (empty($transDetail->transType)) {
               return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'Transaction Type detail not found for the used transaction.');
            }
            $igst_amt = $invTrans['igst_amount'];
            $igst_rate = $invTrans['igst_rate'];
            $cgst_amt = $invTrans['cgst_amount'];
            $cgst_rate = $invTrans['cgst_rate'];
            $sgst_amt = $invTrans['sgst_amount'];
            $sgst_rate = $invTrans['sgst_rate'];
            $base_amt = $invTrans['base_amount'];
            $sac_code = $invTrans['sac_code'];
            $intrest_charges[$key] = array(
                'trans_id' => $invTrans['trans_id'],
                'desc' => $transDetail->transType->trans_name,
                'sac' => $sac_code,
                'base_amt' => round($base_amt,2),
                'sgst_rate' => ($sgst_rate != 0 ? $sgst_rate : 0),
                'sgst_amt' => ($sgst_amt != 0 ? $sgst_amt : 0),
                'cgst_rate' => ($cgst_rate != 0 ? $cgst_rate : 0),
                'cgst_amt' =>  ($cgst_amt != 0 ? $cgst_amt : 0),
                'igst_rate' => ($igst_rate != 0 ? $igst_rate : 0),
                'igst_amt' =>  ($igst_amt != 0 ? $igst_amt : 0),
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
        return $this->viewInvoiceAsPDF($data, true);
    }


    private function _calculateInvoiceTxns($txnsData = [], $is_state_diffrent = false) {
        $intrest_charges = [];
        if (empty($txnsData)) {
           return [array(), 0];
        }
        $total_sum_of_rental = 0;
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
                $base_amt = $totalamount * 100/118;
                if(!$is_state_diffrent) {
                    $cgst_rate = 9;
                    $cgst_amt = round((($base_amt * $cgst_rate)/100),2);
                    $sgst_rate = 9;
                    $sgst_amt = round((($base_amt * $sgst_rate)/100),2);
                } else {
                   $igst_rate = 18;
                    $igst_amt = round((($base_amt * $igst_rate)/100),2); 
                }
            }

            $intrest_charges[$key] = array(
                'trans_id' => $txn->trans_id,
                'desc' => $txn->transType->trans_name,
                'sac' => $txn->transType->charge->sac_code ?? '0000',
                'base_amt' => round($base_amt,2),
                'sgst_rate' => $sgst_rate,
                'sgst_amt' => $sgst_amt,
                'cgst_rate' => $cgst_rate,
                'cgst_amt' =>  $cgst_amt,
                'igst_rate' => $igst_rate,
                'igst_amt' =>  $igst_amt,
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
            $userAddresswithbiz = $this->UserInvRepo->getAddressByUserId($user_id);
            $capsave_addr = $this->UserInvRepo->getCapsavAddr();
            if (empty($userAddresswithbiz) || $userAddresswithbiz->count() != 1) {
               return redirect()->back()->with(['user_id' => $user_id])->with('error', 'Multiple / No default addresses found.');
            }
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
   

}
