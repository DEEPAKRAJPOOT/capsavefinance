<?php
namespace App\Http\Controllers\Lms;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Libraries\Idfc_lib;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use Session;
use Carbon\Carbon;
use DB;
use Helpers;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Illuminate\Support\Facades\Crypt;
class ChargeController extends Controller
{
	use ApplicationTrait;
	use LmsTrait;
        protected $invRepo;
	protected $appRepo;
	protected $userRepo;
	protected $docRepo;
	protected $lmsRepo;
	protected $masterRepo;
                  

	/**
	 * The pdf instance.
	 *
	 * @var App\Libraries\Pdf
	 */
	protected $pdf;
	
	public function __construct(InvoiceInterface $invRepo,InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvLmsRepoInterface $lms_repo ,InvMasterRepoInterface $master){
	        $this->invRepo = $invRepo;	
                $this->appRepo = $app_repo;
		$this->userRepo = $user_repo;
		$this->docRepo = $doc_repo;
		$this->lmsRepo = $lms_repo;
                $this->masterRepo = $master;
		$this->middleware('checkBackendLeadAccess');
	}
	
	/**
	 * Display a listing of the customer.
	 *
	 * @return \Illuminate\Http\Response
	 */
	 public function manageCharge(Request $request){
             $totalLimit = 0;
            $totalCunsumeLimit = 0;
            $consumeLimit = 0;
            $transactions = 0;
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
             $transactionUser  = $this->lmsRepo->getAllUserChargeTransaction();
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
             return view('lms.charges.manage_charge')->with(['user_id' =>$user_id,'trans' =>$transactionUser,'userInfo' => $userInfo]);
        }

    
     public function editLmsCharges(Request $request){
         
          $user_id =  $request->get('user_id');
          if($user_id)
          {
              $app =  $this->lmsRepo->getUserDetails($user_id);
            
          }
          $res  =  $this->lmsRepo->getTrnasType(['is_active' => 1,'chrg_type' => 2]);
          $result  =  $this->invRepo->getCustomerId($user_id);
          $program  =  $this->lmsRepo->getProgramUser($user_id);
         return view('lms.charges.edit_charges')->with(['transtype' => $res,'customer' =>$result,'program' => $program,'user' => $app]);
    }
    
     public function listLmsCharges(Request $request){
          $user_id =  $request->get('user_id');
          if($user_id)
          {
              $app =  $this->lmsRepo->getUserDetails($user_id);
            
          }

          $res  =  $this->lmsRepo->getTrnasType(['is_active' => 1,'chrg_type' => 2]);
          $result  =  $this->invRepo->getCustomerId($user_id);
          $program  =  $this->lmsRepo->getProgramUser($user_id);
          return view('lms.charges.add_charges')->with(['transtype' => $res,'customer' =>$result,'program' => $program,'user' => $app]);
      }
      
  
      
    public function saveManualCharges(Request $request)
       {  
         
           $getUserState = $this->lmsRepo->getUserAddress($request->app_id);
           $comAddrState = $this->lmsRepo->companyAdress();
           $getAmount =  str_replace(',', '', $request->amount);
           $getTransType  =  DB::table('mst_trans_type')->where(['chrg_master_id' => $request->chrg_name])->first();
           $totalSumAmount = 0;
           if($getTransType)
           {
                 if($request->chrg_calculation_type==1)
                 {
                    $percent  = 0; 
                    if($request->is_gst_applicable==1)
                   {
                        $totalSumAmount  =  $request->charge_amount_gst_new;  
                        $cgst = 0;
                        $sgst = 0; 
                        $igst =  18;
                        $is_gst   = 1;  
                   }
                   else
                   {
                        $totalSumAmount  =  $request->amount; 
                        $cgst = 9;
                        $sgst = 9;
                        $igst =  0;
                        $is_gst   = 0;  
                   }
                   $chrg_applicable_id = null;
                 }
                 else
                 {
                    $chrg_applicable_id = $request->chrg_applicable_hidden_id; 
                    $percent  = $request->amount;
                   if($request->is_gst_applicable==1)
                   {
                       $totalSumAmount  =  $request->charge_amount_gst_new;  
                       if($comAddrState== $getUserState)
                        {
                            $cgst = 9;
                            $sgst = 9;
                            $igst =  0;
                            $is_gst   = 0; 
                        }
                        else
                        {
                            $cgst = 0;
                            $sgst = 0; 
                            $igst =  18;
                            $is_gst   = 1; 
                        }
                   }
                   else
                   {
                         $totalSumAmount  =  $request->charge_amount_new; 
                         $is_gst   = 0; 
                         $cgst = 0;
                         $sgst = 0;
                         $igst =  0;
                   }
                 }
                
                    $id  = Auth::user()->user_id;
                    $mytime = Carbon::now();
                    $paymentId = null;
                    if(in_array($request->chrg_name,[config('lms.CHARGE_TYPE.CHEQUE_BOUNCE'),config('lms.CHARGE_TYPE.NACH_BOUNCE')])){
                        if(!$request->has('payment')){
                            Session::flash('message', 'Payment Detail missing, Please try again');
                            return redirect()->route('manage_charge', ['user_id' => $request->user_id]);
                        }else{
                            $payments = [];
                            $paymentId =  Crypt::decryptString($request->payment); 
                            $paymentDetail = $this->lmsRepo->getPaymentDetail($paymentId,$request->user_id);
                        
                            if($paymentDetail){
                                $payments[] = [
                                    "payment_id"=>$paymentId,
                                    "user_id" => $request->user_id,
                                    "amount" => $paymentDetail->amount,
                                    "soa_flag" => 1,
                                    "gst" => 1,
                                    'entry_type' => 0,
                                    "trans_date" => $paymentDetail->date_of_payment,
                                    "trans_type" => config('lms.TRANS_TYPE.REPAYMENT')
                                ];
                                
                                $payments[] = [
                                    "payment_id"=>$paymentId,
                                    "user_id" => $request->user_id,
                                    "amount" => $paymentDetail->amount,
                                    "soa_flag" => 1,
                                    "gst" => 1,
                                    'entry_type' => 1,
                                    "trans_date" =>  $paymentDetail->date_of_payment,
                                    "trans_type" => config('lms.TRANS_TYPE.FAILED'),
                                ];
                                foreach ($payments as $payment) {
                                    $this->lmsRepo->saveCharge($payment);
                                }
                            }
                            $paymentDetail->is_settled = 1;
                            $paymentDetail->save();
                        }
                    }
                    $getMstLog =  $this->lmsRepo->getChrgLog($request->chrg_name);
                    $arr  = [ 
                            "user_id" =>  $request->user_id,
                            "payment_id"=>$paymentId,
                            "amount" =>   $totalSumAmount,
                            "soa_flag" =>1,
                            "gst"   => 1,
                            'gst_per' => ($getMstLog->gst_val) ? $getMstLog->gst_val : '',
                            'chrg_gst_id' => ($getMstLog->chrg_gst_id) ? $getMstLog->chrg_gst_id : '',
                            'entry_type' =>0,
                            "trans_date" => ($request['charge_date']) ? Carbon::createFromFormat('d/m/Y', $request['charge_date'])->format('Y-m-d') : '',
                            "trans_type" => $getTransType->id,
                            "pay_from" => $request['pay_from'] 
                        ];
                       $res =   $this->lmsRepo->saveCharge($arr);
                    
                    
                    $arr  = [   "prgm_id" => $request->program_id,
                                'trans_id' => $res['trans_id'],
                                "chrg_master_id" =>$request->chrg_name,
                                "percent" => $percent,
                                "chrg_applicable_id" =>  $chrg_applicable_id,
                                "amount" =>   $totalSumAmount,
                                "virtual_acc_id" =>  $this->lmsRepo->getVirtualAccIdByUserId($request->user_id),
                                'created_by' =>  $id,
                                'created_at' =>  $mytime ];
                
                  $chrgTransId =   $this->lmsRepo->saveChargeTrans($arr);  
              
                  if( $chrgTransId)
                  {
                        
                          if($res)
                        {
                                Session::flash('message', 'Data has been saved');
                                return redirect()->route('manage_charge', ['user_id' => $request->user_id]);
                                 
                        }
                        else
                        {
                                Session::flash('message', 'Something went wrong, Please try again');
                                return redirect()->route('manage_charge', ['user_id' => $request->user_id]);
                        }
                  }
                   else
                        {
                                Session::flash('message', 'Something went wrong1, Please try again');
                                return redirect()->route('manage_charge', ['user_id' => $request->user_id]);
                        }
                 
                 }
                        else {
                               Session::flash('message', 'Something went wrong2, Please try again');
                              return redirect()->route('manage_charge', ['user_id' => $request->user_id]);
                      }
        
       }
      
      
}