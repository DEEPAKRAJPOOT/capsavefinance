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
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            return view('lms.invoice.user_invoice_list')->with(['userInfo' => $userInfo]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
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
                'sac' => $txn->transType->sac_code,
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
        $user_id = $request->get('user_id');
        $userData = $this->UserInvRepo->getUser($user_id);
        $userStateId = $userData->state_id;

        $company_data = $this->_getCompanyDetail($user_id);
        if ($company_data['status'] != 'success') {
           return response()->json(['status' => 0,'message' => $company_data['message']]); 
        }
        $company_data = $company_data['data'];
        $companyStateId = $company_data['state']->id;
        if (empty(preg_replace('#[^0-9]+#', '', $user_id))) {
            $response['message'] = 'Invalid user Id found.';
             return response()->json($response);
        }
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
        $user_id = $request->get('user_id');
        $invoice_date = $request->get('invoice_date');
        $reference_no = $request->get('reference_no');
        $invoice_no = $request->get('invoice_no');
        $state_name = $request->get('state_name');
        $invoice_type = $request->get('invoice_type');
        $trans_ids = $request->get('trans_id');

        if (!in_array($invoice_type, ['I', 'C'])) {
           return response()->json(['status' => 0,'message' => "Invalid Invoice Type found."]); 
        }
        if (empty(preg_replace('#[^0-9]+#', '', $user_id))) {
           return response()->json(['status' => 0,'message' => 'Invalid UserId Found.']); 
        }
        
        $txnsData = $this->UserInvRepo->getUserInvoiceTxns($user_id, $invoice_type, $trans_ids);
        if(empty($txnsData) ||  $txnsData->isEmpty()){
            return response()->json(['status' => 0,'message' => 'No transaction found for the user.']); 
        }
        $billingDetails = $this->_getBillingDetail($user_id);
        if ($billingDetails['status'] != 'success') {
           return response()->json(['status' => 0,'message' => $billingDetails['message']]); 
        }
        $billingDetails = $billingDetails['data'];
        $origin_of_recipient = [
            'reference_no' => $reference_no,
            'invoice_no' => $invoice_no,
            'place_of_supply' => $state_name,
            'invoice_date' => $invoice_date,
        ];
        $company_data = $this->_getCompanyDetail($user_id);
        if ($company_data['status'] != 'success') {
           return response()->json(['status' => 0,'message' => $company_data['message']]); 
        }
        $company_data = $company_data['data'];
        $userData = $this->UserInvRepo->getUser($user_id);
        $userStateId = $userData->state_id;
        $companyStateId = $company_data['state']->id;
        $is_state_diffrent = ($userStateId != $companyStateId);
        $inv_data = $this->_calculateInvoiceTxns($txnsData, $is_state_diffrent);
        $intrest_charges = $inv_data[0];
        $total_sum_of_rental = $inv_data[1];
        $data = [
            'company_data' => $company_data,
            'billingDetails' => $billingDetails,
            'origin_of_recipient' => $origin_of_recipient, 
            'intrest_charges' => $intrest_charges,
            'total_sum_of_rental' => $total_sum_of_rental,
        ];
        $view = $this->viewInvoiceAsPDF($data);
        return response()->json(['status' => 1,'view' => base64_encode($view)]); 
    }

    public function downloadUserInvoice(Request $request){
        $user_id = $request->get('user_id');
        $user_invoice_id = $request->get('user_invoice_id');
        $invData = $this->UserInvRepo->getInvoices(['user_invoice_id'=> $user_invoice_id, 'user_id' => $user_id])[0];
        $reference_no = $invData->reference_no;
        $invoice_no = $invData->invoice_no;
        $state_name = $invData->place_of_supply;
        $invoice_type = $invData->invoice_type;
        $invoice_date = $invData->invoice_date;
        $company_id = $invData->comp_id;
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
        
        $txnsData = $this->UserInvRepo->getUserInvoiceTxns($user_id, $invoice_type, $trans_ids);
        if(empty($txnsData) || $txnsData->isEmpty()){
            return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'No transaction found for the user.');
        }
        $userData = $this->UserInvRepo->getUser($user_id);
        $userStateId = $userData->state_id;

        $company_data = $this->_getCompanyDetail($user_id, $company_id, $bank_account_id);
        if ($company_data['status'] != 'success') {
           return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error',  $company_data['message']);
        }
        $company_data = $company_data['data'];
        $companyStateId = $company_data['state']->id;

        $billingDetails = $this->_getBillingDetail($user_id);
        if ($billingDetails['status'] != 'success') {
           return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error',  $billingDetails['message']);
        }
        $billingDetails = $billingDetails['data'];
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
        $data = [
            'company_data' => $company_data,
            'billingDetails' => $billingDetails,
            'origin_of_recipient' => $origin_of_recipient, 
            'intrest_charges' => $intrest_charges,
            'total_sum_of_rental' => $total_sum_of_rental,
        ];
        return $this->viewInvoiceAsPDF($data, true);
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
     * Create invoice as per User.
     *
     */
       public function createUserInvoice(Request $request) {
        try {
            $user_id = $request->get('user_id');
            $billingDetails = $this->_getBillingDetail($user_id);
            if ($billingDetails['status'] != 'success') {
               return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', $billingDetails['message']); 
            }
            $billingDetails = $billingDetails['data'];
            $origin_of_recipient = $this->_getOriginRecipent($user_id);
            if ($origin_of_recipient['status'] != 'success') {
                return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', $origin_of_recipient['message']); 
            }
            $origin_of_recipient = $origin_of_recipient['data'];
            return view('lms.invoice.create_user_invoice')->with(['user_id'=> $user_id, 'billingDetails' => $billingDetails, 'origin_of_recipient' => $origin_of_recipient]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    private function _getCompanyDetail($user_id, $company_id = null, $bank_account_id = null) {
        $response = [
            'status' => 'fail',
            'message' => 'Company detail for the user not found.',
            'data' => [],
        ];
        if (empty($company_id)) {
            $userCompanyDetail  = $this->UserInvRepo->getUserCurrCompany($user_id);
            if (empty($userCompanyDetail)) {
              $response['message'] = 'No Company detail is mapped with the user.';
              return $response;
            }
            $company_id = $userCompanyDetail->company_id;
        }
        $companyDetail = $this->UserInvRepo->getCompanyDetail($company_id);
        if (empty($companyDetail) ) {
            $response['message'] = 'No Company detail is mapped with the user.';
            return $response;
        }
        $BankDetails = $this->UserInvRepo->getCompanyBankAcc($user_id);
        $bankDetailsFound =!empty($BankDetails) && !$BankDetails->isEmpty();
        if (!$bankDetailsFound) {
            $response['message'] = 'No BankDetail is found for the User.';
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
            $response['message'] = 'No default Bank Detail found for the user.';
            return $response;
        }
        if (empty($companyDetail->getStateDetail)) {
            $response['message'] = 'State Detail not found for the the company.';
            return $response;
        }
        $CompanyDetails = [
            'comp_id' => $companyDetail->company_id,
            'name' => $companyDetail->cmp_name,
            'address' => $companyDetail->cmp_add,
            'state' => $companyDetail->getStateDetail,
            'city' => $companyDetail->city,
            'phone' => '+91 22 6173 7600',
            'email' => 'accounts@rentalpha.com',
            'cin' => $companyDetail->cin_no,
            'bank_id' => $activeBankAcc->bank_account_id,
            'bank_name' => $activeBankAcc->bank->bank_name,
            'acc_no' => $activeBankAcc->acc_no,
            'branch_name' => $activeBankAcc->branch_name,
            'ifsc_code' => $activeBankAcc->ifsc_code,
        ];

        $response['status'] = 'success';
        $response['message'] = 'success';
        $response['data'] = $CompanyDetails;
        return $response;
    }

    private function _getBillingDetail($user_id) {
        $response = [
            'status' => 'fail',
            'message' => 'Pan Or GST no not found.',
            'data' => [],
        ];
        $AllUserApps =  $this->UserInvRepo->getAppsByUserId($user_id);
        $isDefaultAddrSet = !empty($AllUserApps) && !$AllUserApps->isEmpty();
        if (!$isDefaultAddrSet) {
            $response['message'] = 'No default address is found. Please set the default address first.';
            return $response;
        }
        $AllUserApps = $AllUserApps[0];
        $business = $AllUserApps->business;
        $address = $AllUserApps->address[0];
        $bizPanGst = $AllUserApps->bizPanGst;

        $billingDetails = [
            'name' => $business->biz_entity_name,
            'address' => $address->addr_1 . ' '. $address->addr_2 . ' ' . $address->city_name . ' '.  ($address->state->name ?? '') . ', '. $address->pin_code,
            'pan_no' => '',
            'state_name' => $address->state->name ?? '',
            'gstin_no' => '',
        ];
        if (empty($billingDetails['state_name'])) {
            $response['message'] = 'State Detail not found. Please update address with state first.';
            return $response;
        }
        foreach ($bizPanGst as $key => $pangst) {
           if ($pangst->type == 1) {
              $billingDetails['pan_no'] = $pangst->pan_gst_hash;
           }else{
              $billingDetails['gstin_no'] = $pangst->pan_gst_hash;
           }
        }
        $response['status'] = 'success';
        $response['message'] = 'success';
        $response['data'] = $billingDetails;
        return $response;
    }

    private function _getOriginRecipent($user_id) {
        $CompanyDetails = $this->_getCompanyDetail($user_id);
        if ($CompanyDetails['status'] != 'success') {
            return $CompanyDetails;
        }
        $CompanyDetails = $CompanyDetails['data'];
        $reference_no = _getRand(10). $user_id;
        $invoice_no_id = $this->UserInvRepo->getNextInv(['user_id' => $user_id])->invoice_no_id;
        $origin_of_recipient = [
            'reference_no' => 'RENT'. $reference_no,
            'state_code' => $CompanyDetails['state']->state_code,
            'financial_year' => '19-20',
            'rand_4_no' => sprintf('%04d', $invoice_no_id ?? rand(0, 9999)),
            'state_name' => $CompanyDetails['state']->name,
            'address' => $CompanyDetails['address'],
        ];
        $response['status'] = 'success';
        $response['message'] = 'success';
        $response['data'] = $origin_of_recipient;
        return $response;
    }


    /**
     * Save invoice as per User.
     *
     */
    public function saveUserInvoice(Request $request) {
        try {
            $arrUserData = $request->all();
            $user_id = $request->get('user_id');
            $invoice_type = $request->get('invoice_type');
            $trans_ids = $request->get('trans_id');
            $invoice_date = $request->get('invoice_date');
            if (!is_array($trans_ids) || empty($trans_ids)) {
                return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'No selected txns found for the invoice.');
            }
            $txnsData = $this->UserInvRepo->getUserInvoiceTxns($user_id, $invoice_type, $trans_ids);
            if(empty($txnsData) ||  $txnsData->isEmpty()){
                return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'No remaining txns found for the invoice.');
            }
            $userData = $this->UserInvRepo->getUser($user_id);
            $userStateId = $userData->state_id;
            $company_data = $this->_getCompanyDetail($user_id);
            if ($company_data['status'] != 'success') {
                return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', $company_data['message']);
            }
            $company_data = $company_data['data'];
            $companyStateId = $company_data['state']->id;
            $billingDetails = $this->_getBillingDetail($user_id);
            if ($billingDetails['status'] != 'success') {
                return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', $billingDetails['message']);
            }
            $billingDetails = $billingDetails['data'];
            $is_state_diffrent = ($userStateId != $companyStateId);
            $inv_data = $this->_calculateInvoiceTxns($txnsData, $is_state_diffrent);
            $intrest_charges = $inv_data[0];
            $total_sum_of_rental = $inv_data[1];
            $arrUserData['created_at'] = \carbon\Carbon::now();
            $arrUserData['created_by'] = Auth::user()->user_id;
            $userInvoiceData = [
                'user_id' => $arrUserData['user_id'],
                'app_id' => $arrUserData['app_id'] ?? NULL,
                'pan_no' => $billingDetails['pan_no'],
                'biz_gst_no' => $billingDetails['gstin_no'],
                'gst_addr' => $billingDetails['address'],
                'reference_no' => $arrUserData['reference_no'],
                'invoice_type' => $arrUserData['invoice_type'],
                'invoice_no' => $arrUserData['invoice_no'],
                'invoice_date' => Carbon::createFromFormat('d/m/Y', $invoice_date)->format('Y-m-d H:i:s'),
                'invoice_state_code' => $arrUserData['state_code'],
                'place_of_supply' => $arrUserData['place_of_supply'],
                'tot_no_of_trans' => count($arrUserData['trans_id']),
                'tot_paid_amt' => $total_sum_of_rental ?? 0,
                'comp_id' => $company_data['comp_id'],
                'bank_id' => $company_data['bank_id'],
                'is_active' => 1,
                'created_at' => $arrUserData['created_at'],
                'created_by' => $arrUserData['created_by'],
            ];

            $invoiceResp = $this->UserInvRepo->saveUserInvoice($userInvoiceData);
            if(!empty($invoiceResp->user_invoice_id)){
                $userInvoice_id = $invoiceResp->user_invoice_id;
                foreach ($trans_ids as $key => $txn_id) {
                   $update_transactions[] = $txn_id;
                   $user_invoice_trans_data[] = [
                        'user_invoice_id' => $userInvoice_id,
                        'trans_id' => $txn_id,
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

    /**
     * Get Business Address by ajax
     */
    public function getGstinOfApp(Request $request) {
       try {
        $appID = $request->get('app_id');
        $gstInfo = $this->UserInvRepo->getGSTs($appID)->toArray();
        $panInfo = $this->UserInvRepo->getPAN($appID)->toArray();
        if (empty($gstInfo) || empty($panInfo)) {
          return response()->json(['status' => 0,'message' => 'Selected application is not valid.']); 
        }
        return response()->json(['status' => 1,'gstInfo' => $gstInfo, 'panInfo' => $panInfo]); 
       } catch(Exception $ex) {
         return response()->json(['status' => 0,'message' => 'Selected application is not valid.']); 
       }
    }

    /**
     * Get user invoice location
     */
    public function userInvoiceLocation(Request $request) {
        try {
            
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            $capsave_addr = $this->UserInvRepo->getCapsavAddr();
            $user_addr = $this->UserInvRepo->getUserBizAddr();
            return view('lms.invoice.user_invoice_location')->with(['userInfo' => $userInfo, 'user_id'=> $user_id, 'capsave_addr' => $capsave_addr, 'user_addr' => $user_addr]);
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
            $userInfo = $this->userRepo->getCustomerDetail($user_id);

            $userInvoiceData = [
                'user_id' => $arrUserData['user_id'],
                'biz_addr_id' => $arrUserData['customer_pri_loc'],
                'company_id' => $arrUserData['capsav_location'],
                'company_state_id' => $arrUserData['capsave_state'],
                'biz_addr_state_id' => $arrUserData['user_state'],
                'biz_addr_state_id' => $arrUserData['user_state'],
                'is_active' => 1,
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
            } else {

            }

            $this->UserInvRepo->unPublishAddr($user_id);
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

}
