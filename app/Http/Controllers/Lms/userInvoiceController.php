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
    public function getUserInvoice(Request $request) {
        try {
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            return view('lms.invoice.user_invoice_list')->with(['userInfo' => $userInfo]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Display invoice as per User.
     *
     */
    public function viewInvoiceAsPDF($pdfData = [], $download = true) {
        if (empty($pdfData)) {
            $pdfData = [
                'comp_name' => 'CAPSAVE FINANCE PRIVATE LIMITED',
                'comp_registered_addr' => 'Unit 501, Wing D, Lotus Corporate Park, Western Exp. Highway, Goregaon (E), Mumbai - 400063',
                'comp_billing_addr' => 'Ador Powertron Limited Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019',
                'phone' => '22 6173 7600',
                'cin' => 'U67120MH1992PTC068062',
                'email' => 'accounts@rentalpha.com',
                'invoice_date' => '01-Apr-2019',
                'invoice_no' => 'MH/19-20/0001',
                'ref_no' => 'CAPII0000457',
                'place_of_supply' => 'Maharashtra',
                'pan' => 'AAACA4269Q',
                'state' => 'Maharashtra',
                'gstin' => '27AAACA4269Q2Z5',
                'intrest_charges' => [
                    array(
                        'desc' => 'Processing Fee',
                        'sac' => '9971',
                        'base_amt' => '36534',
                        'sgst_rate' => '9',
                        'sgst_amt' => '3288.06',
                        'cgst_rate' => '9',
                        'cgst_amt' => '3288.06',
                        'igst_rate' => '0',
                        'igst_amt' => '0',
                        'total_rental' => '39110.12',
                    ),
                     array(
                        'desc' => 'Documentation Fee',
                        'sac' => '9987',
                        'base_amt' => '1000',
                        'sgst_rate' => '9',
                        'sgst_amt' => '90',
                        'cgst_rate' => '9',
                        'cgst_amt' => '90',
                        'igst_rate' => '0',
                        'igst_amt' => '0',
                        'total_rental' => '1180',
                    ),
                     array(
                        'desc' => 'overdue Fee',
                        'sac' => '9961',
                        'base_amt' => '3000',
                        'sgst_rate' => '9',
                        'sgst_amt' => '270',
                        'cgst_rate' => '9',
                        'cgst_amt' => '270',
                        'igst_rate' => '12',
                        'igst_amt' => '360',
                        'total_rental' => '3900',
                    ),
                ],
            ];
        }
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
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            $appInfo = $this->UserInvRepo->getAppsByUserId($user_id);
            $state_list = $this->UserInvRepo->getStateListCode();
            $reference_no = _getRand('15') . $user_id;
            return view('lms.invoice.create_user_invoice')
            ->with(['userInfo' => $userInfo, 'state_list' => $state_list, 'appInfo' => $appInfo, 'reference_no' => $reference_no]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Save invoice as per User.
     *
     */
    public function saveUserInvoice(Request $request) {
        try {
            $arrUserData = $request->all();
            // $user_id = $request->get('user_id');
            $arrUserData['created_at'] = \carbon\Carbon::now();
            $arrUserData['created_by'] = Auth::user()->user_id;
            $status = false;
            $userInvoice_id = false;

            dd($request->all());
            $invoice_no = $request->get('state_id');
            $invoice_no .= $request->get('invoice_city');
            $invoice_no .= $request->get('invoice_id');
            $arrUserData['invoice_no'] = $invoice_no;

            if(!empty($request->get('user_invoice_id'))){
                $userInvoice_id = preg_replace('#[^0-9]#', '', $request->get('user_invoice_id'));
                $userInvoice_data = $this->UserInvRepo->findUserInvoiceById($userInvoice_id);
                if(!empty($userInvoice_data)) {
                    $status = $this->UserInvRepo->updateUserInvoice($arrUserData, $userInvoice_id);
                }
            }else{
               $status = $this->UserInvRepo->saveUserInvoiceData($arrUserData); 
            }
            if($status){
                Session::flash('message', $userInvoice_id ? trans('success_messages.user_invoice_edit_success') :trans('success_messages.user_invoice_add_success'));
                return redirect()->route('view_user_invoice');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('view_user_invoice');
            }
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
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
        $resp = $this->UserInvRepo->getBizUserInvoiceAddr($user_id);
        $addr = 'Ador Powertron Limited Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019';
        return response()->json($addr);
       } catch(Exception $ex) {
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
       }
    }

}
