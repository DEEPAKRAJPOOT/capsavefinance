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
                $base_amt = $totalamount * 100/18;
                if(!$is_state_diffrent) {
                    $igst_rate = 18;
                    $igst_amt = round((($base_amt * $igst_rate)/100),2);
                } else {
                    $igst_rate = 9;
                    $cgst_amt = round((($base_amt * $cgst_rate)/100),2);
                    $igst_rate = 9;
                    $sgst_amt = round((($base_amt * $sgst_rate)/100),2);
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
        $userStateId = $companyStateId = 5;
         if (empty(preg_replace('#[^0-9]+#', '', $user_id))) {
            $response['message'] = 'Invalid user Id found.';
             return response()->json($response);
        }
        $txnsData = $this->UserInvRepo->getUserInvoiceTxns($user_id, $invoice_type);
        if ($txnsData->isEmpty()) {
           $response['message'] = 'No transaction found for the user.';
            return response()->json($response);
        }
        $is_state_diffrent = ($userStateId != $companyStateId);
        $inv_data = $this->_calculateInvoiceTxns($txnsData);
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
        if($txnsData->isEmpty()){
            return response()->json(['status' => 0,'message' => 'No transaction found for the user.']); 
        }
        $userStateId = $companyStateId = 5;
        $is_state_diffrent = ($userStateId != $companyStateId);
        $inv_data = $this->_calculateInvoiceTxns($txnsData);
        $intrest_charges = $inv_data[0];
        $total_sum_of_rental = $inv_data[1];
        $billingDetails = $this->_getBillingDetail($user_id);
        $origin_of_recipient = [
            'reference_no' => $reference_no,
            'invoice_no' => $invoice_no,
            'place_of_supply' => $state_name,
            'invoice_date' => $invoice_date,
        ];
        $company_data = $this->_getCompanyDetail($user_id);
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
            $isRelationBuilt = true;
            if (!$isRelationBuilt) {
               return redirect()->back()->withInput()->with('error', 'No relation is found between customer and company.');
            }
            $billingDetails = $this->_getBillingDetail($user_id);
            $origin_of_recipient = $this->_getOriginRecipent($user_id);
            return view('lms.invoice.create_user_invoice')->with(['user_id'=> $user_id, 'billingDetails' => $billingDetails, 'origin_of_recipient' => $origin_of_recipient]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    private function _getCompanyDetail($user_id) {
        $CompanyDetails = [
            'comp_id' => '123',
            'name' => 'CAPSAVE FINANCE PRIVATE LIMITED',
            'address' => 'Unit 501, Wing D, Lotus Corporate Park, Western Exp. Highway, Goregaon (E), Mumbai - 400063',
            'phone' => '+91 22 6173 7600',
            'cin' => 'U67120MH1992PTC068062',
            'email' => 'accounts@rentalpha.com',
            'bank_id' => '137',
            'bank_name' => 'HDFC Bank',
            'acc_no' => '33607554763',
            'branch_name' => 'SBI Bank Beera',
            'ifsc_code' => 'SBIN0009257',
        ];
        return $CompanyDetails;
    }

    private function _getBillingDetail($user_id) {
        $billingDetails = [
            'name' => 'Ador Powertron Limited',
            'pan_no' => 'AAACA4269Q',
            'gstin_no' => '27AAACA4269Q2Z5',
            'address' => 'Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019',
        ];
        return $billingDetails;
    }

    private function _getOriginRecipent($user_id) {
        $reference_no = _getRand(10). $user_id;
        $origin_of_recipient = [
            'reference_no' => 'RENT'. $reference_no,
            'state_code' => 'MH',
            'financial_year' => '19-20',
            'rand_4_no' => sprintf('%04d', rand(0, 9999)),
            'state_name' => 'Maharashtra',
            'address' => 'Unit 501, Wing D, Lotus Corporate Park, Western Exp. Highway, Goregaon (E), Mumbai - 400063',
        ];
        return $origin_of_recipient;
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
            if($txnsData->isEmpty()){
                return redirect()->route('view_user_invoice', ['user_id' => $user_id])->with('error', 'No remaining txns found for the invoice.');
            }
            $userStateId = $companyStateId = 5;
            $is_state_diffrent = ($userStateId != $companyStateId);
            $inv_data = $this->_calculateInvoiceTxns($txnsData);
            $intrest_charges = $inv_data[0];
            $total_sum_of_rental = $inv_data[1];
            $arrUserData['created_at'] = \carbon\Carbon::now();
            $arrUserData['created_by'] = Auth::user()->user_id;
            $billingDetails = $this->_getBillingDetail($user_id);
            $CompanyDetails = $this->_getCompanyDetail($user_id);
            $userInvoiceData = [
                'invoice_user_id' => $arrUserData['user_id'],
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
                'comp_id' => $CompanyDetails['comp_id'],
                'bank_id' => $CompanyDetails['bank_id'],
                'is_active' => 1,
                'created_at' => $arrUserData['created_at'],
                'created_by' => $arrUserData['created_by'],
            ];

            $invoiceResp = $this->UserInvRepo->saveUserInvoice($userInvoiceData);
            if(!empty($invoiceResp->user_invoice_id)){
                $userInvoice_id = $invoiceResp->user_invoice_id;
                foreach ($trans_ids as $key => $txn_id) {
                   $user_invoice_trans_data[] = [
                        'user_invoice_id' => $userInvoice_id,
                        'trans_id' => $txn_id,
                   ]; 
                }
                $UserInvoiceTxns = $this->UserInvRepo->saveUserInvoiceTxns($user_invoice_trans_data);
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
     * Get Business Address by ajax
     */
    public function getBizUserInvoiceAddr(Request $request) {
       try {
        $user_id = $request->get('user_id');
        $addr = 'Ador Powertron Limited Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019';
        return response()->json($addr);
       } catch(Exception $ex) {
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
       }
    }

}
