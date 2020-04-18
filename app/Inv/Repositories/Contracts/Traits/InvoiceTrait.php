<?php
namespace App\Inv\Repositories\Contracts\Traits;

use Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
use App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\BizPanGst;
use App\Helpers\ApportionmentHelper;
use App\Inv\Repositories\Models\Lms\RefundTransactions;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\AppLimit;
use App\Inv\Repositories\Models\User;

trait InvoiceTrait
{
    /**
     * Calculate Interest
     * 
     * @param float $principalAmt
     * @param float $interestRate
     * @param integer $tenorDays
     * 
     * @return mixed
     */
   public static function checkCsvFile($data)
   {
      
        $cusomer_id  =   $data['cusomer_id']; 
        $inv_no  =   $data['inv_no']; 
        $inv_date  =   $data['inv_date']; 
        $inv_due_date  =   $data['inv_due_date']; 
        $amount  =   $data['amount']; 
        $file_name  =   $data['file_name'];
        $user_id  =   $data['user_id'];
        $attr[]="";
        $mytime = Carbon::now();
        $cDate   =  $mytime->toDateTimeString();
        $res =  self::checkDuplicateInvoice($inv_no,$user_id);  /* duplicate Invoice */
        $to =    Carbon::createFromFormat('d-m-Y', $data['inv_date'])->format('Y-m-d');
        $from =  Carbon::createFromFormat('d-m-Y', $data['inv_due_date'])->format('Y-m-d');
        $CFrom =  Carbon::createFromFormat('Y-m-d H:i:s', $cDate)->format('Y-m-d');
        $diffTenor =  self::DateDiff($to,$from); /* Inv date and Invoice Date diff */
        $diffOldTenor =  self::DateDiff($to,$CFrom); /* Inv date and Current Date diff */
        $dueDateGreaterCurrentdate =  self::twoDateDiff($to,$CFrom);  /* Current date and Invoice Date diff */
        $approval =  self::chkApproval($data['approval']); /* Check approval */
        $invoice_date_validate  = self::validateDate($data['inv_date'], $format = 'd-m-Y'); /* chk date format */
        //dd($inv_no);
        /*  if($res)
        {
          //  $attr['status'] = 0;
          //  $attr['message']=  'Invoice No "'.$inv_no.'" already exits with '.$cusomer_id;
          //  return  $attr;  
           
        } */
       if(!is_numeric($amount))
       {
            $attr['status'] = 0;
            $attr['message']=  'Invoice Amount '.$data['amount'].'  should be numaric.';
            return  $attr;    
        
       }
       else if($amount==0)
       {
            $attr['status'] = 0;
            $attr['message']=  'Invoice Amount should not be 0.';
            return  $attr;    
        
       }
       else if($diffTenor > $data['tenor'])
        {
             $attr['status'] = 0;
             $attr['message']=  'Invoice Date ('.$data['inv_date'].') & Invoice Due Date ('.$data['inv_due_date'].') difference should not be more than '.$data['tenor'].' days.';
             return  $attr;    
        }
          else if ($dueDateGreaterCurrentdate)
        {
             $attr['status'] = 0;
             $attr['message']=  'Please check the Invoice  Date ('.$data['inv_date'].'), It Should  not be more than current date.';
             return  $attr;    
       
        }
        else if($user_id==0)
        {
             $attr['status_id'] = 7;
             $attr['error'] = 0;
             $attr['status'] = 0;
             $attr['message']= 'Customer Id "('.$cusomer_id.')" does not exits in our records.';
             return  $attr;    
       
        }
       else if(strlen($inv_no) < 3)
        {
             $attr['status_id'] = 7;
             $attr['error'] = 2;
             $attr['status'] = 1;
             $attr['message']= 'Invoice Number  length should be minimum 3 for customer id "('.$cusomer_id.')"';
             return  $attr;    
       
        }
        else if($diffOldTenor > $data['old_tenor'])
        {
             $attr['status_id'] = 28;
             $attr['error'] = 0;
             $attr['status'] = 1;
             $attr['message']= '(Exceptional Case)Invoice date & current date difference should not be more than '.$data['old_tenor'].' days.';
             return  $attr;    
     
        }
        else if($diffOldTenor < $data['old_tenor'])
        {
             $attr['status_id'] = $approval;
             $attr['status'] = 1;
             $attr['error'] = 0;
             $attr['message']= 'Auto Approve';
             return  $attr;    
     
        }
      
        else
        {
             $attr['status_id'] = 7;
             $attr['status'] = 1;
             $attr['error'] = 0;
             $attr['message']= True;
             return  $attr;    
    
        }
       
   }
  
     public static function getLimitProgram($attr)
     {      
            $attr[]="";
            $lms_user_id =    LmsUser::where('customer_id',$attr['cusomer_id'])->pluck('user_id');
            $app_id =    AppLimit::whereIn('user_id',$lms_user_id)->where('status',1)->first();
            $res =  AppProgramOffer::whereHas('productHas')->where(['app_id' => $app_id['app_id'],'anchor_id' => $attr['anchor_id'],'prgm_id'=> $attr['prgm_id'], 'is_active' => 1, 'is_approve' => 1, 'status' => 1])->first();
            if ($res) {
              
                  $limit =  AppProgramOffer::whereHas('productHas')->where('app_id',$app_id['app_id'])->where(['anchor_id' => $attr['anchor_id'],'prgm_id'=> $attr['prgm_id'], 'is_active' => 1, 'is_approve' => 1, 'status' => 1])->sum('prgm_limit_amt');
                  $attr['status_id'] = 7;
                  $attr['status'] = 1;
                  $attr['user_id'] = $app_id['user_id'];
                  $attr['tenor'] =  $res['tenor']; 
                  $attr['tenor_old_invoice'] = $res['tenor_old_invoice'];
                  $attr['limit'] = $limit; 
                  $attr['app_id'] =  $app_id['app_id'];
                  $attr['biz_id'] =  $app_id['biz_id'];
                  $attr['message']= '';
                  return  $attr;    
          
             }
            else {
                  $attr['status_id'] = 7;
                  $attr['status'] = 0;
                  $attr['message']= 'Offer is not approve for Customer id "('.$attr['cusomer_id'].')"';
                  return  $attr;    
          
            }
      }
     
   public static  function getInvoiceDueDate($dataAttr)
   {
         $invoice_date_validate  = self::validateDate($dataAttr['inv_date'], $format = 'd-m-Y');
        
        if( $invoice_date_validate==false)
        {
             $attr['status'] = 0;
             $attr['message']=  'Please checkd the  invoice date ('.$dataAttr['inv_date'].'), It Should be "dd-mm-yy" format';
             return  $attr;    
        } 
        else {
          
             $daysToAdd = $dataAttr['tenor'];
             $invDueDate =    date('d-m-Y', strtotime($dataAttr['inv_date']. ' + '.$daysToAdd.' days')); 
             $attr['status'] = 1;
             $attr['inv_due_date'] = $invDueDate;
             $attr['message']= True;
             return  $attr;    
        }
   }
 

   public static function twoDateDiff($fdate,$tdate)
    {
            $curdate=strtotime($fdate);
            $mydate=strtotime($tdate);

            if($curdate > $mydate)
            {
               return 1;
            }
            else
            {
                return 0;
            }
    }
   public static function DateDiff($to,$from)
   {
       
        $formatted_dt1=Carbon::parse($to);
        $formatted_dt2=Carbon::parse($from);
        return  $date_diff=$formatted_dt1->diffInDays($formatted_dt2);
   }
    
    public static function checkDuplicateInvoice($invNo,$user_id)
    {
        
        return BizInvoice::where(['invoice_no' => $invNo,'supplier_id' => $user_id])->first();
    }  
        
       public static  function validateDate($date, $format = 'd-m-Y')
    { 
       
       return   DateTime::createFromFormat($format, $date);
    
     }
   public static function chkApproval($getPrgm)
   {
          $customerAuto  = 4;
          $expl  =  explode(",",$getPrgm->invoice_approval); 
          if(in_array($customerAuto, $expl))  
          {
            return $statusId = 8;  
          }
          else if($getPrgm->invoice_approval==4)
          {
            return  $statusId = 8;   
          }
          else
          {
            return $statusId = 7;
          }
   }
   
}
