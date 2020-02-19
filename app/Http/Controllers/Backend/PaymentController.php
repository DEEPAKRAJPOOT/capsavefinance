<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Auth;
use Datetime;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessInformationRequest;
use Illuminate\Support\Facades\Storage;
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

class PaymentController extends Controller {

    protected $invRepo;
    protected $docRepo;
    public function __construct(InvoiceInterface $invRepo, InvDocumentRepoInterface $docRepo, InvLmsRepoInterface $lms_repo,InvUserRepoInterface $user_repo) {
        $this->invRepo = $invRepo;
        $this->docRepo = $docRepo;
        $this->lmsRepo = $lms_repo;
        $this->userRepo = $user_repo;
        $this->middleware('auth');
    }

   
    /*      Payment list page   */
    public function  paymentList()
    {
      return view('backend.payment.payment_list');
   
    }
    
      /*      Payment list page   */
    public function  addPayment()
    {
       $bank = DB::table('mst_bank')->where(['is_active' => 1])->get();  
       //$result  =  $this->invRepo->getCustomerId();
       $tranType=$this->lmsRepo->getManualTranType();
       $getGstDropVal=$this->lmsRepo->getActiveGST();
       $result= $this->lmsRepo->getAllLmsUser();     
      return view('backend.payment.add_payment')->with(['bank' => $bank,'customer' => $result, 'tranType'=>$tranType, 'getGstDropVal'=>$getGstDropVal]);
   
    }
      /*     Excel  Payment list page   */
    public function  excelPaymentList()
    {
     
      $result  =  $this->invRepo->getDisburseCustomerId();
      return view('backend.payment.excel_payment_list')->with(['customer' => $result]);
   
    }
    
      public function excelBulkPayment(Request $request)
    {
          $result  =  $this->invRepo->getDisburseCustomerId();
          return view('backend.payment.excel_bulk_payment')->with(['customer' => $result]);
    }
       ///////////* change date format ********////////////////   
     function validateDate($date, $format = 'd/m/Y')
     { 
     
       return  $d = \DateTime::createFromFormat($format, $date);
     }
     
    
    /* save payment details   */
    public function  savePayment(Request $request)
    {
        $validatedData = $request->validate([
                'payment_type' => 'required',
                'trans_type' => 'required',
                'customer_id' => 'required', 
                'virtual_acc' => 'required',  
                'date_of_payment' => 'required', 
                'amount' => 'required', 
                'utr_no' => 'required', 
                'description' => 'required'
          ]);
        $user_id  = Auth::user()->user_id;
        $mytime = Carbon::now(); 

        $udata=$this->userRepo->getSingleUserDetails($request->customer_id);
        $getAmount =  $this->invRepo->getRepaymentAmount($request->customer_id);  
        $enterAmount =  str_replace(',', '', $request->amount);
       foreach($getAmount as $val)
       {
            $getAmount = $val->repayment_amount;
           if($getAmount >= $enterAmount)
           {
              
               $finalAmount = $getAmount - $enterAmount;
               $this->invRepo->singleRepayment($val->disbursal_id,$finalAmount);
               Session::flash('message', 'Bulk amount has been saved');
               return back();
           }
           else
           {
                    
                 $this->invRepo->singleRepayment($val->disbursal_id,0);
               
           }
          
       }  */
      
            $utr  ="";
            $check  ="";
            $unr  ="";
            if($request['payment_type']==1)
            {
                $utr =   $request['utr_no'];  
            }
            else  if($request['payment_type']==2)
            {
               $check = $request['utr_no'];
            }
              else  if($request['payment_type']==3)
            {
               $unr =  $request['utr_no'];
            }

            $sgst=0;
            $cgst=0;
            $igst=0;
              if(isset($request['sgst_amt'])) {
              $sgst =   $request['sgst_amt'];  
              } if(isset($request['cgst_amt'])){
              $cgst =   $request['cgst_amt'];  
              } if(isset($request['igst_amt'])){
              $igst =   $request['igst_amt'];  
              }
            
         $tran  = [  'gl_flag' => 1,
                        'soa_flag' => 1,
                        'user_id' =>  $request['customer_id'],
                        'entry_type' =>1,
                        'trans_date' => ($request['date_of_payment']) ? Carbon::createFromFormat('d/m/Y', $request['date_of_payment'])->format('Y-m-d') : '',
                        'trans_type'   => $request['trans_type'], 
                        'trans_by'   => 1,
                        'pay_from'   => ($udata)?$udata->is_buyer:'',
                        'amount' =>  str_replace(',', '', $request['amount']),
                        'gst'=> $request['incl_gst'],
                        'sgst' =>  $sgst,
                        'cgst' =>  $cgst,
                        'igst' =>  $igst,
                        'mode_of_pay' =>  $request['payment_type'],
                        'comment' =>  $request['description'],
                        'utr_no' =>  $utr,
                        'cheque_no' =>  $check,
                        'unr_no'    => $unr,
                        'virtual_acc_id'=> $request['virtual_acc'],
                        'created_at' =>  $mytime,
                        'created_by' =>  $user_id,
                    ];
            
        $res = $this->invRepo->saveRepaymentTrans($tran);
        if( $res)
        {
             Session::flash('message', 'Data has been saved');
             return back(); 
        }
        else
        {
             Session::flash('message', 'Something went wrong, Please try again');
             return back(); 
        }
       
    }
   /////////* save bulk payment by excel ///////////////////////
   public function  saveExcelPayment(Request $request)
   {
            $data = array();
            $id  = Auth::user()->user_id;
            $mytime = Carbon::now(); 
            $count =  count($request['payment_date']);
               for($i=0; $i < $count ;$i++)
               {
                   $arr = [ 'user_id' => $request['user_id'],
                            'trans_by' => 2, 
                            'trans_type'   =>  17,
                             'entry_type' =>1,
                            'trans_date' => ($request['payment_date'][$i]) ? Carbon::createFromFormat('d/m/Y', $request['payment_date'][$i])->format('Y-m-d') : '',
                            'virtual_acc_id' => $request['virtual_acc_no'][$i],
                            'amount' => $request['amount'][$i],
                            'comment' => $request['remarks'][$i],  
                            'created_by' =>  $id,
                            'created_at' =>  $mytime ];
                   $res = $this->invRepo->saveRepaymentTrans($arr);
               }
         
       
        if( $res)
        {
             Session::flash('message', 'Data has been saved');
             return back(); 
        }
        else
        {
             Session::flash('message', 'Something went wrong, Please try again');
             return back(); 
        }
   }
  
}



