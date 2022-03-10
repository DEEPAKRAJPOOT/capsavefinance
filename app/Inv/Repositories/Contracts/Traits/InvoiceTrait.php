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
use App\Inv\Repositories\Models\LmsUsersLog;
use App\Inv\Repositories\Models\AppLimit;
use App\Inv\Repositories\Models\AppOfferAdhocLimit;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\UserDetail;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\InvoiceStatusLog;
use App\Inv\Repositories\Models\Program;
use App\Inv\Repositories\Models\Anchor;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;


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
           $attr['message']=  'Following Invoice Number ('.$inv_no.') has been auto-cancel due to duplicate invoice number.';
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
             $attr['message']= ' Following Invoice Number ('.$inv_no.') has been auto-cancel due to invoice number max-length.';
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
             if($approval==8)
             {
                $attr['message']= 'Auto Approve';
             }
             else
             {
                $attr['message']= ''; 
             }
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
         $inv_no_var5   = ""; 
         $inv_no_var6   = ""; 
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
            $lmsU =  LmsUser::where(['customer_id' => $dataAttr['cusomer_id']])->first(); 
            $getLmsActive = UserDetail::where(['user_id' => $lmsU['user_id'],'is_active' => 0])->count(); 
            if( $invoice_date_validate==false)
            {
               $multichk['status'] =0; 
               $inv_no_var.=$inv_no.',';
               $multichk['multiVali1'] = '* Invoice date is not correct for following invoice Number ('.substr($inv_no_var,0,-1).'), Date format should be "dd-mm-yyyy"';
         
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
              if(strlen($inv_no) < 3 || strlen($inv_no) > 25)
          {
                    $multichk['status'] =0;
                    $inv_no_var4.=$inv_no.',';
                    $multichk['multiVali5'] = '* Following invoice Number ('.substr($inv_no_var4,0,-1).')  length allow between 3 to 25';

          }
            if($chlLmsCusto['status']==1)
           {
                 $getDupli  = self::checkDuplicateInvoice($inv_no,$chlLmsCusto['user_id']);
                 $inv_no_dublicate = '';
                 if($getDupli)
                 {
                      
                      $multichk['status'] =0;
                      $inv_no_var5.=$inv_no.',';
                      $inv_no_dublicate = $inv_no;
                      $multichk['multiVali6'] = '* Following invoice Number ('.substr($inv_no_var5,0,-1).') already exists in our system.';
          
                 }
                 $getDupliInvoice  = self::checkDuplicateInvoiceInInvoice($inv_no,$chlLmsCusto['user_id']);
                 if($getDupliInvoice)
                 {
                      $multichk['status'] =0;
                      if($inv_no_dublicate != $inv_no) {
                        $inv_no_var5.=$inv_no.',';
                        $multichk['multiVali6'] = '* Following invoice Number ('.substr($inv_no_var5,0,-1).') already exists in our system.';
                      }
                 }

           }
           if($getLmsActive > 0)
           {
                      $multichk['status'] =0;
                      $inv_no_var6.=$inv_no.',';
                      $multichk['multiVali7'] = '*Account has been closed for Following invoice Number ('.substr($inv_no_var6,0,-1).').';          
           }
         
            
        }
        return $multichk;
  }                
     public static function getLimitProgram($attr)
     {   
            $attr[]="";
            $lms_user_id =    LmsUser::where('customer_id',$attr['cusomer_id'])->pluck('user_id');
            $app_user_id   =  Application::whereIn('user_id',$lms_user_id)->where('status',2)->pluck('app_id');
            $app_id =    AppLimit::whereIn('app_id',$app_user_id)->where('status',1)->first();
            $res =  AppProgramOffer::whereHas('productHas')->where(['app_id' => $app_id['app_id'],'anchor_id' => $attr['anchor_id'],'prgm_id'=> $attr['prgm_id'], 'is_active' => 1, 'is_approve' => 1, 'status' => 1])->first();
            if ($res) {
                $is_enhance  =    Application::whereIn('user_id',$lms_user_id)->where(['status' =>2,'app_type' => 2])->count();  
                if($is_enhance==1)
                {
                  $limit =  AppProgramOffer::whereHas('productHas')->where(['anchor_id' => $attr['anchor_id'],'prgm_id'=> $attr['prgm_id'], 'is_active' => 1, 'is_approve' => 1, 'status' => 1])->sum('prgm_limit_amt');
                }
                else
                {
                    $limit =  AppProgramOffer::whereHas('productHas')->where('app_id',$app_id['app_id'])->where(['anchor_id' => $attr['anchor_id'],'prgm_id'=> $attr['prgm_id'], 'is_active' => 1, 'is_approve' => 1, 'status' => 1])->sum('prgm_limit_amt');
                  
                }
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
             
             $attr['message']=  'Invoice date ('.$dataAttr['inv_date'].') is not correct for Invoice Number <'.$dataAttr['inv_no'].'>, Date format should be "dd-mm-yyyy".';
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


     public static function checkDuplicateInvoiceInInvoice($invNo,$user_id)
    {

        return BizInvoice::where(['invoice_no' => $invNo,'supplier_id' => $user_id])->first();
    } 
        
       public static  function validateDate($date, $format = 'd-m-Y')
    { 
       
       return   DateTime::createFromFormat($format, $date);
    
     }
   public static function chkApproval($getPrgm)
   {
          $id = Auth::user()->user_id;
          $getUser = User::where('user_id',$id)->first();
         if($getUser->is_buyer==0)
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
         else
         {
            return $statusId = 7;
         }
   }
 
   public static function ProgramLimit($getDetails)
   {

        return   AppProgramOffer::whereHas('productHas')->where(['app_id' => $getDetails['app_id'],'anchor_id' => $getDetails['anchor_id'],'prgm_id'=> $getDetails['program_id'], 'is_active' => 1, 'is_approve' => 1, 'status' => 1])->sum('prgm_limit_amt');
        
   }
     /* check the user app limit  */
    /* Use  app_limit table */
    /* Created by gajendra chahan  */ 
   public static  function limitExpire($cid)
   {
        $mytime = Carbon::now();
        $cDate   =  $mytime->toDateTimeString();
        $CFrom =  Carbon::createFromFormat('Y-m-d H:i:s', $cDate)->format('Y-m-d');
        $app_id =    AppLimit::where('user_id',$cid)->where('status',1)->first();
       return self::twoDateDiff($CFrom,$app_id['end_date']);
   }

    public static function invoiceApproveLimit($attr)
   {  
        $prgmData = Program::where('prgm_id', $attr['prgm_id'])->first();
        if (isset($prgmData->parent_prgm_id)) {
           $prgm_ids = Program::where('parent_prgm_id', $prgmData->parent_prgm_id)->pluck('prgm_id')->toArray();
        }else{
            $prgm_ids = [$attr['prgm_id']];
        }
        $is_enhance  =    Application::whereIn('app_type',[1,2,3])->where(['user_id' => $attr['user_id'],'status' =>2])->count();  
      
        if($is_enhance==1)
        {
        $marginApprAmt = InvoiceDisbursed::getDisbursedAmountForSupplier($attr['user_id'], $attr['prgm_offer_id'],$attr['anchor_id'],NULL);
        $marginApprAmt = $marginApprAmt??0;
        $marginApprAmt   +=   BizInvoice::whereIn('status_id',[8,9,10])
        ->where('prgm_offer_id',$attr['prgm_offer_id'])
        ->whereIn('program_id', $prgm_ids)
        ->where(['is_adhoc' =>0,'supplier_id' =>$attr['user_id'],'anchor_id' =>$attr['anchor_id']])
        ->where('app_id' , '<=', $attr['app_id'])
        ->sum('invoice_approve_amount');
        
        $marginReypayAmt =   BizInvoice::whereIn('status_id',[8,9,10,12,13,15])
        ->where('prgm_offer_id',$attr['prgm_offer_id'])
        ->whereIn('program_id', $prgm_ids)
        ->where(['is_adhoc' =>0,'supplier_id' =>$attr['user_id'],'anchor_id' =>$attr['anchor_id']])
        ->where('app_id' , '<=', $attr['app_id'])
        ->sum('principal_repayment_amt');
            return $marginApprAmt-$marginReypayAmt;
       }
       else
       {
        $marginApprAmt = InvoiceDisbursed::getDisbursedAmountForSupplierIsEnhance($attr['user_id'], $attr['prgm_offer_id'],$attr['anchor_id'],$attr['app_id']);
        $marginApprAmt = $marginApprAmt??0;
        $marginApprAmt   +=  BizInvoice::whereIn('status_id',[8,9,10])
        ->where('prgm_offer_id',$attr['prgm_offer_id'])
        ->where(['is_adhoc' =>0,'app_id' =>$attr['app_id'],'supplier_id' =>$attr['user_id'],'anchor_id' =>$attr['anchor_id'],'program_id' =>$attr['prgm_id']])
        ->sum('invoice_approve_amount');
        
        $marginReypayAmt =  BizInvoice::whereIn('status_id',[8,9,10,12,13,15])
        ->where('prgm_offer_id',$attr['prgm_offer_id'])
        ->where(['is_adhoc' =>0,'app_id' =>$attr['app_id'],'supplier_id' =>$attr['user_id'],'anchor_id' =>$attr['anchor_id'],'program_id' =>$attr['prgm_id']])
        ->sum('principal_repayment_amt');
            return $marginApprAmt-$marginReypayAmt;
       }
   }

  public static function getInvoiceDetail($attr)
  {
      return BizInvoice::where(['invoice_id' => $attr['invoice_id']])->first();
  }
   /* update manuall status by limit  */
    /* Use  invoice table */
    /* Created by gajendra chahan  */ 
  public static function getManualInvoiceStatus($inv_details)
  {
            $mytime = Carbon::now();
            $cDate   =  $mytime->toDateTimeString();
            $uid = Auth::user()->user_id;
            $attr['user_id']   =   $inv_details['supplier_id'];
            $attr['anchor_id'] =   $inv_details['anchor_id'];
            $attr['prgm_id']   =   $inv_details['program_id'];
            $attr['app_id']   =    $inv_details['app_id'];
            $attr['prgm_offer_id'] = $inv_details['prgm_offer_id'];
            $invoice_id =   $inv_details['invoice_id'];
            $cid = $inv_details['supplier_id'];
            $sum =  self::invoiceApproveLimit($attr);
            $limit   =  self::ProgramLimit($inv_details);
            $getMargin =  self::invoiceMargin($inv_details);
            $dueDateGreaterCurrentdate =  self::limitExpire($cid); /* get App limit by user_id*/
            $isOverDue     =  self::isOverDue($cid); /* get overdue by user_id*/

            $anchorData = Anchor::getAnchorById($attr['anchor_id']);
            $anchorApproveInvoiceAmt = self::anchorInvoiceApproveAmount($attr['anchor_id']);
            $prgmData = $anchorData->prgmData;
            $currInvoiceAmount = $inv_details['invoice_approve_amount'];
            $fungibleAnchorLimit = false;
            
            if ($anchorData->is_fungible == 1) {
              $subPrgmData        = Program::getProgram($attr['prgm_id']);
              if ($anchorApproveInvoiceAmt > $subPrgmData->anchor_sub_limit) {
                $fungibleAnchorLimit = true;
              } else if ($anchorApproveInvoiceAmt > $subPrgmData->anchor_limit){
                $fungibleAnchorLimit = true;              
              }
            }

          if($inv_details['status_id']==8)  
         {     
               $finalsum = $sum-$inv_details['invoice_approve_amount'];
               if($fungibleAnchorLimit) {
                   $status = 28;
                   $limit_exceed='Auto Approve, Anchor Limit Exceed.';
                   InvoiceStatusLog::saveInvoiceStatusLog($invoice_id,$status);
                   return BizInvoice::where(['invoice_id' =>$invoice_id,'supplier_id' =>$cid])->update(['invoice_margin_amount' => $getMargin,'is_margin_deduct' =>1,'remark' =>$limit_exceed,'status_id' => $status]);
               }else if($isOverDue->is_overdue==1) {
                   $status=28; 
                   $limit_exceed='Auto Approve, Overdue';
                   return   BizInvoice::where(['invoice_id' =>$invoice_id,'supplier_id' =>$cid])->update(['remark' =>$limit_exceed,'status_id' =>$status]);
                
                }   
               else if($dueDateGreaterCurrentdate)
                {
                          $status=28; 
                          $limit_exceed='Customer limit has been expired.';
                          return   BizInvoice::where(['invoice_id' =>$invoice_id,'supplier_id' =>$cid])->update(['remark' =>$limit_exceed,'status_id' =>$status]);
                
                }
                    
               else if($limit  >= $finalsum)
                {
                    $remain_amount = $limit-$sum;
                   if($remain_amount >=$inv_details['invoice_approve_amount'])
                    { 
                       $status=8; 
                       $limit_exceed='Auto Approve';
                       return   BizInvoice::where(['invoice_id' =>$invoice_id,'supplier_id' =>$cid])->update(['invoice_margin_amount' => $getMargin,'is_margin_deduct' =>1,'remark' =>$limit_exceed,'status_id' =>$status]);

                    }
                    else 
                    {
                       $status=28; 
                       $limit_exceed='Auto Approve, Limit exceed';
                       return   BizInvoice::where(['invoice_id' =>$invoice_id,'supplier_id' =>$cid])->update(['remark' =>$limit_exceed,'status_id' =>$status]);

                    }
                   }
            else 
               {

                  //  $status=28; 
                  //  $limit_exceed='Auto Approve, Limit exceed';
                  //  return   BizInvoice::where(['invoice_id' =>$invoice_id,'supplier_id' =>$cid])->update(['remark' =>$limit_exceed,'status_id' =>$status]);

               }
           }
            if($inv_details['status_id']==7)  
           { 
                $status_id=7; 
                 
                if($isOverDue->is_overdue==1)
                {
                    $limit_exceed=''; 
                    $status_id=28; 
                    $limit_exceed='Overdue';
                    InvoiceStatusLog::saveInvoiceStatusLog($invoice_id,$status_id);
                  return   BizInvoice::where(['invoice_id' =>$invoice_id,'supplier_id' =>$cid])->update(['remark' =>$limit_exceed,'status_id' =>$status_id]);
                } 
                if($dueDateGreaterCurrentdate)
                {
                    $limit_exceed=''; 
                    $status_id=28; 
                    $limit_exceed='Customer limit has been expired.';
                    InvoiceStatusLog::saveInvoiceStatusLog($invoice_id,$status_id);
                    return   BizInvoice::where(['invoice_id' =>$invoice_id,'supplier_id' =>$cid])->update(['remark' =>$limit_exceed,'status_id' =>$status_id]);
                }
                if($fungibleAnchorLimit) {
                   $status_id = 28;
                   $limit_exceed='Anchor Limit Exceed.';
                   InvoiceStatusLog::saveInvoiceStatusLog($invoice_id,$status_id);
                    return   BizInvoice::where(['invoice_id' =>$invoice_id,'supplier_id' =>$cid])->update(['remark' =>$limit_exceed,'status_id' =>$status_id]);
                }
                  
            }
             if($inv_details['status_id']==28)
           {
                  InvoiceStatusLog::saveInvoiceStatusLog($invoice_id,$inv_details['status_id']); 
                  return   BizInvoice::where(['invoice_id' =>$invoice_id,'supplier_id' =>$cid])->update(['status_id' =>$inv_details['status_id']]);
          
           }
  
  }  
  /* checked  invoice limit exceed  */
    /* Use  invoice table */
    /* Created by gajendra chahan  */
   public static  function checkInvoiceLimitExced($attr)
   {
       
        $msg="";
        foreach($attr['invoice_id'] as $row)
        {  
             if($attr->status==8)
             {    
                   $attribute['invoice_id'] = $row;
                   $inv = InvoiceTrait::getInvoiceDetail($attribute);
                   $response =  self::updateApproveStatus($inv); 
                   if($response==2 || $response==3 || $response==4)
                   {
                   
                     $msg.= $inv['invoice_no'].',';
                  
                   }
                  
             }
        }
        return $msg;
    }
    
     /* Update single invoice status id according user limit */
    /* Use bulk and invoice table */
    /* Created by gajendra chahan  */
      public static  function saveAdhocApproveStatus($attr)
   {
            $limitData[]="";
            $mytime = Carbon::now();
            $cDate   =  $mytime->toDateTimeString();
            $uid = Auth::user()->user_id;
            $dueDateGreaterCurrentdate =  self::limitExpire($attr['supplier_id']); /* get App limit by user_id*/
            $isOverDue     =  self::isOverDue($attr['supplier_id']); /* get overdue by user_id*/
            $sum  =  self::adhocLimit($attr);
            $limit  = self::checkUserAdhoc($attr);
            $getMargin =  self::invoiceMargin($attr);
            $finalsum = $sum-$attr['invoice_approve_amount'];
            if($limit  >= $finalsum)
           {
               $remain_amount = $limit-$finalsum;
              if($remain_amount >=$attr['invoice_approve_amount'])
               { 
                
                  $limit_exceed='Auto Approve';
                  InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],8); 
                  BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['invoice_margin_amount' => $getMargin,'is_margin_deduct' =>1,'remark' =>$limit_exceed,'status_id' =>8,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                  return 1;
               }
               else 
               {
                 
                  $limit_exceed='Auto Approve, Limit exceed';
                  InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],28); 
                  BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['remark' =>$limit_exceed,'status_id' =>28,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                  return 2;
               }
              }
           else 
              {
                  $limit_exceed='Auto Approve, Limit exceed';
                  InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],28); 
                  BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['remark' =>$limit_exceed,'status_id' =>28,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                  return 2;
              }
            if($dueDateGreaterCurrentdate)
            {
                  InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],28); 
                  BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['remark' =>'Customer limit has been expired.','status_id' =>28,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                  return 4;
           } 
           if($isOverDue->is_overdue==1)
            {
                InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],28); 
                BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['remark' => 'Overdue','status_id' =>28,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                return 3;

            }   
            else 
            {
                InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],$attr['status_id']); 
                BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['status_id' =>$attr['status_id'],'status_update_time' => $cDate,'updated_by' =>$uid]); 
                return 1;
            }
           
           
    } 
   /* Update single invoice status id according user limit */
    /* Use bulk and invoice table */
    /* Created by gajendra chahan  */
      public static  function   updateAdhocApproveStatus($attr)
   {
            $limitData[]="";
            $mytime = Carbon::now();
            $cDate   =  $mytime->toDateTimeString();
            $uid = Auth::user()->user_id;
            $dueDateGreaterCurrentdate =  self::limitExpire($attr['supplier_id']); /* get App limit by user_id*/
            $isOverDue     =  self::isOverDue($attr['supplier_id']); /* get overdue by user_id*/
            $sum  =  self::adhocLimit($attr);
            $limit  = self::checkUserAdhoc($attr);
            if($limit  >= $sum)
           {
               $remain_amount = $limit-$sum;
              if($remain_amount >=$attr['invoice_approve_amount'])
               { 
                
                  $limit_exceed='Auto Approve';
                  InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],8); 
                  BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['invoice_margin_amount' => $attr['inv_apprv_amount'],'is_margin_deduct' =>1,'remark' =>$limit_exceed,'status_id' =>8,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                  return 1;
               }
               else 
               {
                 
                  $limit_exceed='Auto Approve, Limit exceed';
                  InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],28); 
                  BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['remark' =>$limit_exceed,'status_id' =>28,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                  return 2;
               }
              }
           else 
              {
                  $limit_exceed='Auto Approve, Limit exceed';
                  InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],28); 
                  BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['remark' =>$limit_exceed,'status_id' =>28,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                  return 2;
              }
            if($dueDateGreaterCurrentdate)
            {
                  InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],28); 
                  BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['remark' =>'Customer limit has been expired.','status_id' =>28,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                  return 4;
           } 
           if($isOverDue->is_overdue==1)
            {
                InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],28); 
                BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['remark' => 'Overdue','status_id' =>28,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                return 3;

            }   
            else 
            {
                InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],$attr['status_id']); 
                BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['status_id' =>$attr['status_id'],'status_update_time' => $cDate,'updated_by' =>$uid]); 
                return 1;
            }
           
           
    } 
    /* Update single invoice status id according user limit */
    /* Use bulk and invoice table */
    /* Created by gajendra chahan  */
      public static  function updateApproveStatus($attr)
   {
            $limitData[]="";
            $mytime = Carbon::now();
            $cDate   =  $mytime->toDateTimeString();
            $uid = Auth::user()->user_id;
            $inv_details =  self::getInvoiceDetail($attr);
            //////////* get margin amount ********///////
            $inv_apprv_amount = self::invoiceMargin($inv_details);
            if($inv_details['is_adhoc']==1)
            {   $inv_details['inv_apprv_amount'] = $inv_apprv_amount;
                $inv_details['app_id']   =   $inv_details['app_id'];
                return self::updateAdhocApproveStatus($inv_details);
            }
            $dueDateGreaterCurrentdate =  self::limitExpire($inv_details['supplier_id']);
            $attr['user_id']   =   $inv_details['supplier_id'];
            $attr['anchor_id'] =   $inv_details['anchor_id'];
            $attr['prgm_id']   =   $inv_details['program_id'];
            $attr['app_id']   =     $inv_details['app_id'];
            $attr['prgm_offer_id'] = $inv_details['prgm_offer_id'];
            $sum =  self::invoiceApproveLimit($attr);
            $limit   =  self::ProgramLimit($inv_details);
            $dueDateGreaterCurrentdate =  self::limitExpire($inv_details['supplier_id']); /* get App limit by user_id*/
            $isOverDue     =  self::isOverDue($inv_details['supplier_id']); /* get overdue by user_id*/
            $isAnchorLimitExceeded = self::isAnchorLimitExceeded($attr['anchor_id'], $inv_details['invoice_approve_amount']);

            $fungibleAnchorLimit = false;
            if ($isAnchorLimitExceeded) {
                $fungibleAnchorLimit = true;
            }
            if ($fungibleAnchorLimit) {
             // if ($fromTab == 'initiatediscount') {
                InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],28);
                BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['remark' =>'Anchor limit exceeded','status_id' =>28,'status_update_time' => $cDate,'updated_by' =>$uid]);
                return 5;
             // }
              //return 22;
            }

            if($dueDateGreaterCurrentdate)
            {
                  InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],28); 
                  BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['remark' =>'Customer limit has been expired','status_id' =>28,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                  return 4;
           } 
           if($isOverDue->is_overdue==1)
            {
                InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],8); 
                BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['invoice_margin_amount'=>$inv_apprv_amount,'is_margin_deduct' =>1,'remark' => 'Overdue','status_id' =>28,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                return 3;

            }   
            if($limit  >= $sum)
            {
              
              $anchorData = Anchor::getAnchorById($attr['anchor_id']);
              if ($anchorData && isset($anchorData->is_fungible) && $anchorData->is_fungible == 1) {
                $prmUtilizedLimit = self::anchorPrgmInvoiceApproveAmount($attr['anchor_id'], $attr['prgm_id']);
                $prgmData        = Program::getProgram($attr['prgm_id']);
                $remain_prgm_amount =  $prgmData->anchor_sub_limit - $prmUtilizedLimit;
                if ($inv_details['invoice_approve_amount'] > $remain_prgm_amount) {
                  return 2;
                }
              }
              
                $remain_amount    =  $limit - $sum;
                if($remain_amount >= $inv_details['invoice_approve_amount'])
                {
                         InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],8); 
                         BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['invoice_margin_amount'=>$inv_apprv_amount,'is_margin_deduct' =>1,'status_id' =>8,'status_update_time' => $cDate,'updated_by' =>$uid]); 
                         return 1;
         
                }
                else 
                {
                     return 2;
                }
             }
            else 
            {
                 return 2;
            }
           
           
    }
   /* Update bulk invoice status id according user limit */
    /* Use bulk and invoice table */
    /* Created by gajendra chahan  */
    
   public static function updateLimit($status_id,$limit,$inv_amout,$attr,$invoice_id)
    {
        $cid  = $attr['supplier_id'];
        $attribute['prgm_id']  = $attr['program_id'];
        $attribute['user_id']  = $attr['supplier_id'];
        $attribute['anchor_id']  = $attr['anchor_id'];
        $attribute['prgm_offer_id'] = $attr['prgm_offer_id'];
        $sum  = self::invoiceApproveLimit($attribute);
        $dueDateGreaterCurrentdate =  self::limitExpire($cid); /* get App limit by user_id*/
        $isOverDue     =  self::isOverDue($cid); /* get overdue by user_id*/
        $uid = Auth::user()->user_id;
        if($status_id==8)  
         {  
              
                     if((float)$limit  >= $sum)
                    {
                        $remain_amount =  (float)$limit-$sum;
                        if((float)$remain_amount >=  $inv_amout)
                        { 
                           $status=8; 
                           $limit_exceed='Auto Approve';
                        }
                        else 
                        {
                           $status=28; 
                           $limit_exceed='Auto Approve, Limit exceed';
                        }
                       }
                    else 
                       {
                           $status=28; 
                           $limit_exceed='Auto Approve, Limit exceed';
                       }
               if($isOverDue->is_overdue==1)
                {
                   $status=28; 
                   $limit_exceed='Auto Approve, Overdue';
                }   
               if($dueDateGreaterCurrentdate)
                {
                          $status=28; 
                          $limit_exceed='Customer limit has been expired.'; 
                }
                 
           }
           if($status_id==7)  
           { 
               if($isOverDue->is_overdue==1)
                {
                    $status=28; 
                    $limit_exceed='Overdue';
                } 
                if($dueDateGreaterCurrentdate)
                 {
                    $status=28; 
                    $limit_exceed='Customer limit has been expired.'; 
                 }
             }
       
            $res['status']  = $status;
            $res['remark']  = $limit_exceed;
            return $res;
        
    }
    /* Check Bulk invoice status */
    /* Use bulk and invoice table */
    /* Created by gajendra chahan  */
    public static function updateBulkLimit($limit,$inv_amout,$attr)
    {
        $cid  = $attr['supplier_id'];
        $attribute['prgm_id']  = $attr['program_id'];
        $attribute['user_id']  = $attr['supplier_id'];
        $attribute['anchor_id']  = $attr['anchor_id'];
        $attribute['app_id']  = $attr['app_id'];
        $attribute['prgm_offer_id'] = $attr['prgm_offer_id'];
        $sum  = self::invoiceApproveLimit($attribute);
        $dueDateGreaterCurrentdate =  self::limitExpire($cid); /* get App limit by user_id*/
        $isOverDue     =  self::isOverDue($cid); /* get overdue by user_id*/
        $invoiceMargin  = self::invoiceMargin($attr);  ////*********Invoice Margin **********//////
        $uid = Auth::user()->user_id;
       
          if($attr->status_id==8)
          {
                     if((float)$limit  >= $sum)
                    {
                       $remain_amount =  (float)$limit-$sum;
                        if((float)$remain_amount >=  $inv_amout)
                        { 
                            $datalist['comm_txt']  = 'Auto Aprove';
                            $datalist['status_id'] = 8;
                            $datalist['invoice_margin_amount']  = $invoiceMargin; 
                        }
                        else 
                        {
                            $datalist['comm_txt']  = 'Limit exceed';
                            $datalist['status_id'] = 28;
                            $datalist['invoice_margin_amount']  = $invoiceMargin; 
                        }

                    }
                    else 
                       {
                            $datalist['comm_txt']  = 'Limit exceed';
                            $datalist['status_id'] = 28;
                            $datalist['invoice_margin_amount']  = $invoiceMargin; 
                       }
                if($isOverDue->is_overdue==1)
                {
                     $datalist['comm_txt']  = 'Overdue';
                     $datalist['status_id'] = 28;
                     $datalist['invoice_margin_amount']  = $invoiceMargin;  
                
                } 
                if($dueDateGreaterCurrentdate)
                {
                      $datalist['comm_txt']  = 'User Limit has been expire';
                      $datalist['status_id'] = 28;
                      $datalist['invoice_margin_amount']  = $invoiceMargin; 
                }
          }
          if($attr->status_id==7)
          {
                $datalist['comm_txt']  = '';
                $datalist['status_id'] = 7;
                $datalist['invoice_margin_amount']  = $invoiceMargin; 
                if($isOverDue->is_overdue==1)
                {
                     $datalist['comm_txt']  = 'Overdue';
                     $datalist['status_id'] = 28;
                     $datalist['invoice_margin_amount']  = $invoiceMargin; 
                
                } 
                if($dueDateGreaterCurrentdate)
                {
                      $datalist['comm_txt']  = 'User Limit has been expire';
                      $datalist['status_id'] = 28;
                      $datalist['invoice_margin_amount']  = $invoiceMargin;  
                }
          }
          if($attr->status_id==28)
          {
               $datalist['comm_txt']  = '';
               $datalist['status_id'] = 28;
               $datalist['invoice_margin_amount']  = $invoiceMargin; 
          }
             return $datalist;
        
    }
   
   /* Check overdue amount */
    /* Use User details table */
    /* Created by gajendra chahan  */
  public static function isOverDue($user_id)
  {
    return  UserDetail::where('user_id',$user_id)->first();
  }
   /* Check adhoc limit */
   /* Created by gajendra chahan  */
  public static function adhocLimit($attr)
  {
    $sql   =  BizInvoice::whereIn('status_id',[8,9,10,12,13,15])->where(['supplier_id' =>$attr['user_id']??$attr['supplier_id'],'prgm_offer_id' =>$attr['prgm_offer_id'],'is_adhoc' =>1]);
    if (isset($attr['app_offer_adhoc_limit_id'])) {
      $sql = $sql->where(['app_offer_adhoc_limit_id' => $attr['app_offer_adhoc_limit_id']]);
    }
    $marginApprAmt = $sql->sum('invoice_approve_amount');
    $sql =  BizInvoice::whereIn('status_id',[8,9,10,12,13,15])->where(['supplier_id' =>$attr['user_id']??$attr['supplier_id'],'prgm_offer_id' =>$attr['prgm_offer_id'],'is_adhoc' =>1]);
    if (isset($attr['app_offer_adhoc_limit_id'])) {
      $sql = $sql->where(['app_offer_adhoc_limit_id' => $attr['app_offer_adhoc_limit_id']]);
    }
    $marginReypayAmt = $sql->sum('principal_repayment_amt');
    return $marginApprAmt-$marginReypayAmt;
   }
   
   public static function checkUserAdhoc($attr)
    {
        $mytime = Carbon::now();
        $dateTime  =  $mytime->toDateTimeString();
        return AppOfferAdhocLimit::where(['user_id' => $attr['user_id'],'prgm_offer_id' => $attr['prgm_offer_id'],'status' => 1])->whereRaw('"'.$dateTime.'" between `start_date` and `end_date`')->sum('limit_amt');

    }
  public static function updateReject($attr)
  {
      foreach($attr as $row)
      {
          BizInvoice::where('invoice_id',$row->invoice_id)->update(['status_id' =>14,'remark' =>'Account closed']);
      }
  }

  public static function getAccountClosure($attr)
   {
      $getBank =   BizInvoice::where(['status_id' => 10,'supplier_id' =>$attr['user_id']])->count();
      $get_outstanding =   BizInvoice::where(['is_repayment' =>0,'status_id' => 12,'supplier_id' =>$attr['user_id']])->count();
      $get_TDS_Payment  =  Payment::where(['user_id' => $attr['user_id'],'action_type' =>3])->count();
      $get_TDS_Payment_File = Payment::where(['user_id' => $attr['user_id'],'action_type' =>3])->where('file_id','<>', 0)->count();
      $getReject =   BizInvoice::whereIn('status_id',[7,8,9,11,28])->where(['supplier_id' =>$attr['user_id']])->get();
      if($getBank > 0)
      {
         $data['msg']  = 'You cannot close this account as some invoices are sent to bank for disbursal.';
         $data['status'] = 0;
          
      }
      else if($get_TDS_Payment <> $get_TDS_Payment_File)
      {
        $data['msg']  = 'You cannot close this account as TDS certificated is not uploaded.';
        $data['status'] = 0;
      }
      else if($get_outstanding > 0)
      {
         $data['msg']  = 'You cannot close this account as outstanding amount is pending for this customer.';
         $data['status'] = 0;
       
      }
       else if(count($getReject) > 0 && $get_outstanding==0 && $get_Payment > 0 && $getBank==0)
      {
         self::updateReject($getReject);
         self::accountLock($attr['user_id']);
         $data['msg']  = 'Customer account has been closed.';
         $data['status'] = 1;
       
      }
      else
      {
        self::accountLock($attr['user_id']);
        $data['msg']  = '';
        $data['status'] = 1;
      }
      return $data;
   }
   
   public static function accountLock($uid)
   {
        $mytime = Carbon::now();
        $cDate   =  $mytime->toDateTimeString();
        $create_uid = Auth::user()->user_id;
        $getLogId = LmsUsersLog::create(['user_id' => $uid,'status_id' => 35,'created_by' => $create_uid,'created_at' => $cDate]);
        UserDetail::where(['user_id' => $uid])->update(['is_active' => 0,'lms_users_log_id' => $getLogId->lms_users_log_id]);
       
   }
   ////////////////* offer margin amount  **//////////////
   public static function invoiceMargin($inv_details)
   {
       $res  = AppProgramOffer::where(['prgm_offer_id' => $inv_details['prgm_offer_id']])->first(); 
      if($res->margin!=null &&  $res->margin!=0 && $inv_details['is_margin_deduct']==0)
      {
        $marginAmount  =  round($inv_details['invoice_approve_amount']*$res->margin/100);
        return     $inv_details['invoice_approve_amount']-$marginAmount;
      }
     else 
      {
         return  $inv_details['invoice_approve_amount']; 
      }
   }
   
    public static function isLimitExceed($invoice_id) {
        $invData = BizInvoice::getInvoiceData(['invoice_id' => $invoice_id]);
        if (isset($invData[0])) {
            $invData   = $invData[0];
            $user_id   = $invData->supplier_id;
            $anchor_id = $invData->anchor_id;
            $prgm_id   = $invData->program_id;
            //$is_po     = $invData->is_po;
            $app_id    = $invData->app_id;
            //$margin =  self::invoiceMargin($res);
            //$po_inv_amount = $invData->invoice_approve_amount;
            $po_inv_amount = $invData->invoice_margin_amount;
            
            $attribute['user_id'] = $user_id;
            $attribute['anchor_id'] = $anchor_id;
            $attribute['prgm_id'] = $prgm_id;
            $attribute['program_id'] = $prgm_id;
            $attribute['prgm_offer_id'] = $invData->prgm_offer_id;
            //$attribute['is_po'] = $is_po;
            $attribute['app_id'] = $app_id;            
            $sum   = self::invoiceApproveLimit($attribute);
            $limit = self::ProgramLimit($attribute);
            $finalsum = $sum - $po_inv_amount;
            if ($limit  >= $finalsum) {
                $remain_amount = $limit - $finalsum;
                if ($remain_amount < $po_inv_amount) { 
                    return true;
                }
            } else {
                return false;
            }
        }
        return false;
    }

    public static function updateInvoiceData($attr) {
        InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'], $attr['status']); 
        BizInvoice::where(['invoice_id' => $attr['invoice_id']])->update(['remark' => $attr['remark'],'status_id' => $attr['status'],'status_update_time' => \Carbon\Carbon::now(),'updated_by' => \Auth::user()->user_id]); 
        return true;
    }
    public static function isAnchorLimitExceeded($anchor_id, $currInvoiceAmount = 0){
    $anchorData = Anchor::getAnchorById($anchor_id);
    $anchorApproveInvoiceAmt = self::anchorInvoiceApproveAmount($anchor_id);
    $prgmData = $anchorData->prgmData;
    $limitExceeded = false;
    if (($anchorApproveInvoiceAmt + $currInvoiceAmount) > $prgmData->anchor_limit && $anchorData->is_fungible == 1) {
        $limitExceeded = true;
    }
    return $limitExceeded;
  }

  public static function anchorInvoiceApproveAmount($anchor_id){
    $marginApprAmt   =   BizInvoice::whereIn('status_id',[8,9,10,12])->where(['is_adhoc' =>0,'anchor_id' =>$anchor_id])->sum('invoice_margin_amount');
    $marginReypayAmt =   BizInvoice::whereIn('status_id',[8,9,10,12])->where(['is_adhoc' =>0,'anchor_id' =>$anchor_id])->sum('principal_repayment_amt');
    return $marginApprAmt-$marginReypayAmt;
  }

  // sub program utilized limit for fungible
  public static function anchorPrgmInvoiceApproveAmount($anchor_id, $prgm_id){
    $marginApprAmt   =   BizInvoice::whereIn('status_id',[8,9,10,12])->where(['is_adhoc' => 0,'anchor_id' => $anchor_id, 'program_id' => $prgm_id])->sum('invoice_margin_amount');
    $marginReypayAmt =   BizInvoice::whereIn('status_id',[8,9,10,12])->where(['is_adhoc' => 0,'anchor_id' => $anchor_id, 'program_id' => $prgm_id])->sum('principal_repayment_amt');
    return $marginApprAmt-$marginReypayAmt;
  }

  public static function customerInvoiceApproveAmount($custId, $anchor_id){
    $marginApprAmt   =  BizInvoice::whereIn('status_id',[8,9,10,12])->where(['is_adhoc' =>0, 'anchor_id' => $anchor_id, 'supplier_id' => $custId])->sum('invoice_margin_amount');
    $marginReypayAmt =  BizInvoice::whereIn('status_id',[8,9,10,12])->where(['is_adhoc' =>0, 'anchor_id' => $anchor_id, 'supplier_id' => $custId])->sum('principal_repayment_amt');
    return $marginApprAmt-$marginReypayAmt;
  }

  public static function invoiceOverdueCheck($invoiceId = false) {
        $lmsRepo = \App::make('App\Inv\Repositories\Contracts\LmsInterface');
        $invoice = $lmsRepo->getInvoice($invoiceId);
        $apps = $invoice->supplier->apps;
        foreach ($apps as $app) {
            foreach ($app->disbursed_invoices as $inv) {
                $invc = $inv->toArray();
                $invc['invoice_disbursed'] = $inv->invoice_disbursed->toArray();
                if ((isset($invc['invoice_disbursed']['payment_due_date']))) {
                    if (!is_null($invc['invoice_disbursed']['payment_due_date'])) {
                        $calDay = $invc['invoice_disbursed']['grace_period'];
                        $dueDate = strtotime($invc['invoice_disbursed']['payment_due_date']."+ $calDay Days");
                        $dueDate = $dueDate ?? 0; // or your date as well
                        $now = strtotime(date('Y-m-d'));
                        $datediff = ($dueDate - $now);
                        $days = round($datediff / (60 * 60 * 24));
                        if ($days < 0 && $invc['is_repayment'] == 0) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

}
