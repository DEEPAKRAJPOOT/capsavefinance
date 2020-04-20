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
use App\Inv\Repositories\Models\InvoiceBulkUpload;
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
       if($res)
        {
           $attr['status_id'] = 7;
           $attr['error'] = 2;
           $attr['status'] = 1;
           $attr['message']=  'Invoice auto cancel due to duplicate invoice no <'.$inv_no.'>';
           return  $attr;  
           
        } 
       else  if(!is_numeric($amount))
       {
            $attr['status'] = 0;
            $attr['message']=  'Invoice amount should be numeric for Invoice Number <'.$inv_no.'>.';
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
             $attr['message']= 'Customer Id "('.$cusomer_id.')" does not exist in our records.';
             return  $attr;    
       
        }
       else if(strlen($inv_no) < 3)
        {
             $attr['status_id'] = 7;
             $attr['error'] = 2;
             $attr['status'] = 1;
             $attr['message']= 'Invoice auto cancel due to invoice no <'.$inv_no.'> max-length.';
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
  
  public static function multiValiChk($handle,$prgm_id,$anchor_id,$customerId)
  {
       
         $inv_no_var   = ""; 
         $inv_no_var1   = ""; 
         $inv_no_var2   = ""; 
         $inv_no_var3   = ""; 
         $inv_no_var4   = ""; 
         $multichk['status']   = 1;
         $mytime = Carbon::now();
         $cDate   =  $mytime->toDateTimeString();
         $CFrom =  Carbon::createFromFormat('Y-m-d H:i:s', $cDate)->format('Y-m-d');
        
         /* Current date and Invoice Date diff */
        while(($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
        {   
            if($customerId==null)
            {
                 $dataAttr['cusomer_id']  =   $data[0]; 
                 $inv_no  =   $data[1]; 
                 $inv_date  =   $data[2]; 
                 $amount  =   $data[3]; 
                 $file_name  =   $data[4];
            }
            else
            {
                 $dataAttr['cusomer_id']  =   $customerId; 
                 $inv_no  =   $data[0]; 
                 $inv_date  =   $data[1]; 
                 $amount  =   $data[2]; 
                 $file_name  =   $data[3];
            }
            
            $dataAttr['anchor_id'] = $anchor_id;
            $dataAttr['prgm_id'] = $prgm_id;
           
            $invoice_date_validate  = self::validateDate($inv_date, $format = 'd-m-Y');
            $chlLmsCusto =  self::getLimitProgram($dataAttr);
          
            if( $invoice_date_validate==false)
            {
               $multichk['status'] =0; 
               $inv_no_var.=$inv_no.',';
               $multichk['multiVali1'] = '* Invoice date is not correct for following invoice Number ('.substr($inv_no_var,0,-1).'), Date format should be "dd-mm-yy"';
         
            }
            if(!is_numeric($amount) || $amount==0)
           {
               $multichk['status'] =0; 
               $inv_no_var1.=$inv_no.',';
               $multichk['multiVali2'] = '* Invalid numeric value in amount field for following invoice ('.substr($inv_no_var1,0,-1).') amount value should not be null, zero or less than zero.';
           
            }
           if($invoice_date_validate==true)
            {
               $to =    Carbon::createFromFormat('d-m-Y', $inv_date)->format('Y-m-d');
               $dueDateGreaterCurrentdate =  self::twoDateDiff($to,$CFrom);  /* Current date and Invoice Date diff */
               if($dueDateGreaterCurrentdate)
               {
                 $multichk['status'] =0;
                 $inv_no_var2.=$inv_no.',';
                 $multichk['multiVali3'] = '* Invoice date should not be greater than current date for following invoice Number  ('.substr($inv_no_var2,0,-1).')';
              }
            }
             if($chlLmsCusto['status']==0)
            {
                   $multichk['status'] =0;
                   $inv_no_var3.=$inv_no.',';
                   $multichk['multiVali4'] = '* You cannot upload invoice for following invoice Number ('.substr($inv_no_var3,0,-1).') as limit is not sanctioned or offer is not approved.';
          
            }
            if($chlLmsCusto['status']==1)
           {
                 $getDupli  = self::checkDuplicateInvoice($inv_no,$chlLmsCusto['user_id']);
               
                 if($getDupli)
                 {
                     
                      $multichk['status'] =0;
                      $inv_no_var4.=$inv_no.',';
                      $multichk['multiVali5'] = '* following invoice Number ('.substr($inv_no_var4,0,-1).') already exists in our system.';
          
                 }
                 
           }
         
            
        }
        return $multichk;
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
                  $attr['prgm_offer_id'] =  $res['prgm_offer_id']; 
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
                  $attr['message']= 'You cannot upload for Customer ID - '.$attr['cusomer_id'].' as limit is not sanctioned or offer is not approved.';
                  return  $attr;    
          
            }
      }
     
   public static  function getInvoiceDueDate($dataAttr)
   {
         $invoice_date_validate  = self::validateDate($dataAttr['inv_date'], $format = 'd-m-Y');
        
        if( $invoice_date_validate==false)
        {
             $attr['status'] = 0;
             
             $attr['message']=  'Invoice date ('.$dataAttr['inv_date'].') is not correct for Invoice Number <'.$dataAttr['inv_no'].'>, Date format should be "dd-mm-yy".';
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
        
        return InvoiceBulkUpload::where(['invoice_no' => $invNo,'supplier_id' => $user_id])->first();
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
