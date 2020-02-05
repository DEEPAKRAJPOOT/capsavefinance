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
use App\Inv\Repositories\Models\BizApi;
use Session;
use Helpers;
use DB;
use App\Libraries\Pdf;
use Carbon\Carbon;

class PaymentController extends Controller {

    protected $invRepo;
    protected $docRepo;
    public function __construct(InvoiceInterface $invRepo, InvDocumentRepoInterface $docRepo) {
        $this->invRepo = $invRepo;
        $this->docRepo = $docRepo;
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
       $result  =  $this->invRepo->getCustomerId();
      return view('backend.payment.add_payment')->with(['bank' => $bank,'customer' => $result]);
   
    }
      /*     Excel  Payment list page   */
    public function  excelPaymentList()
    {
      $result  =  $this->invRepo->getCustomerId();
      return view('backend.payment.excel_payment_list')->with(['customer' => $result]);
   
    }
    
      public function excelBulkPayment(Request $request)
    {
          $result  =  $this->invRepo->getCustomerId();
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
                'customer_id' => 'required', 
                'bank_name' => 'required',  
                'date_of_payment' => 'required', 
                'amount' => 'required', 
                'refrence_no' => 'required', 
                'description' => 'required'
          ]);
        $user_id  = Auth::user()->user_id;
        $mytime = Carbon::now(); 
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
          
       }
      
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
         $tran  = [  'gl_flag' => 1,
                        'soa_flag' => 1,
                        'user_id' =>  $request['customer_id'],
                        'trans_date' => ($request['date_of_payment']) ? Carbon::createFromFormat('d/m/Y', $request['date_of_payment'])->format('Y-m-d') : '',
                        'trans_type'   => 17, 
                        'trans_by'   => 1,
                        'pay_from'   => 0,
                        'amount' =>  $request['amount'],
                        'mode_of_pay' =>  $request['payment_type'],
                        'comment' =>  $request['description'],
                        'utr_no' =>  $utr,
                        'cheque_no' =>  $check,
                        'unr_no'    => $unr,
                        'created_at' =>  $mytime,
                        'created_by' =>  $user_id];
            
        $res = $this->invRepo->saveRepaymentTrans($tran);
        if( $res)
        {
             Session::flash('message', 'Bulk amount has been saved');
             return back(); 
        }
        else
        {
             Session::flash('message', 'Something went wrong, Please try again');
             return back(); 
        }
       
    }
    
    public function saveExcelPayment(Request $request)
    {
         $validatedData = $request->validate([
                'customer' => 'required',
                'upload' => 'required'
          ]);
       $extension = $request['upload']->getClientOriginalExtension();
       if($extension!="csv" || $extension!="csv")
       {
           Session::flash('message', 'Please check  file format');
           return back(); 
       }
       $i=0;
       $date = Carbon::now();
       $data = array();
        $userId =  $request['customer'];
        $id = Auth::user()->user_id;
        if ($request['upload']) {
            if (!Storage::exists('/public/user/' . $userId . '/excelpayment')) {
                Storage::makeDirectory('/public/user/' . $userId . '/excelpayment', 0775, true);
            }
            $path = Storage::disk('public')->put('/user/' . $userId . '/excelpayment', $request['upload'], null);
            $inputArr['file_path'] = $path;
       }
        $csvFilePath = storage_path("app/public/" . $inputArr['file_path']);
         $file = fopen($csvFilePath, "r");
     
        while (!feof($file)) {
          
            $rowData[] = explode(",",fgets($file));
          }
        $rowcount = count($rowData) -1;
        foreach($rowData as $key=>$row)
        {
           
            if($i > 0 && $i < $rowcount)  
            {
               
               if(count($row) < 5)
              {  
                   Session::flash('message', 'Please check column format');
                   return back();  
                 
               }  
               
                $payment_date  = $row[0]; 
                $virtual_acc  = $row[1]; 
                $amount  = $row[2];
                $payment_ref_no  = $row[3];
                $remarks  =  $row[4];
                $payment_date_format  = $this->validateDate($payment_date, $format = 'd/m/Y');
               
               if(strlen($payment_date) < 10)
               {
                    Session::flash('message', 'Please check the  payment date, It Should be "dd/mm/yy" format');
                    return back();  
               } 
                if( $payment_date_format==false)
               {
                    Session::flash('message', 'Please check the payment date, It should be "dd/mm/yy" format');
                    return back(); 
               }
               if($amount=='')
               {
                    Session::flash('message', 'Please check amount, Amount should not be null');
                    return back();
               } 
                if(!is_numeric($amount))
               {
                    Session::flash('message', 'Please check  amount, string value not allowed');
                    return back();
               } 
                $data[$i]['payment_date'] = ($payment_date) ? Carbon::createFromFormat('d/m/Y', $payment_date)->format('Y-m-d') : '';
                $data[$i]['amount'] =  $amount;
                $data[$i]['user_id'] = $request['customer'];
                $data[$i]['virtual_account_no'] =  $virtual_acc;
                $data[$i]['payment_ref_no'] =  $payment_ref_no;
                $data[$i]['file_path'] =  $inputArr['file_path'];
                $data[$i]['remark'] = $remarks;
                $data[$i]['created_by'] =  $id;
                $data[$i]['created_at'] =  $date;
             }
          
           $i++;
         
        }
        if(count($data)==0)
        {
                  Session::flash('message', 'Something went wrong, Please try again');
                   return back();
        }
       else {
               $whr  = ['status' =>0,'user_id' =>$request['customer']];
                $this->invRepo->deleteExcelTrans($whr);
                $res =  $this->invRepo->insertExcelTrans($data);
                if($res)
                {
                      Session::flash('message', 'Payment details successfully saved');
                      return back(); 
                }
                else
                {
                   Session::flash('message', 'Something went wrong, Please try again');
                   return back();
                }
         }       
                
    }    
  
  
}



