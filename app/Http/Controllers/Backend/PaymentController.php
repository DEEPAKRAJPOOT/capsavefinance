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
use  App\Inv\Repositories\Contracts\Traits\LmsTrait;
use Session;
use Helpers;
use DB;
use App\Libraries\Pdf;
use Carbon\Carbon;
use App\Inv\Repositories\Contracts\ApplicationInterface;
use PHPExcel;
use PHPExcel_IOFactory;

class PaymentController extends Controller {

    protected $invRepo;
    protected $docRepo;
    use LmsTrait;
    public function __construct(InvoiceInterface $invRepo, InvDocumentRepoInterface $docRepo, InvLmsRepoInterface $lms_repo,InvUserRepoInterface $user_repo, ApplicationInterface $appRepo) {
        $this->invRepo = $invRepo;
        $this->docRepo = $docRepo;
        $this->lmsRepo = $lms_repo;
        $this->userRepo = $user_repo;
        $this->appRepo = $appRepo;
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
          
       }
      
            $utr ="";
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
          if($request['trans_type']==17){
            $this->paySettlement( $request['customer_id']);
            }
          Session::flash('message',trans('backend_messages.add_payment_manual'));
          return redirect()->route('payment_list');
             //Session::flash('message', 'Bulk amount has been saved');
            // return back(); 
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

  /* Payment Advice List   */
  public function  paymentAdviceList()
  {
    return view('backend.payment.payment_advice_list');

  }

  public function  paymentAdviceExcel()
  {
    $counter = 1;
    $objPHPExcel =  new PHPExcel();

    // $objPHPExcel->getActiveSheet('A1:F1')->getStyle()->getFont()->setBold(true);
    // Setting font to Arial Black
    // $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial Black');
   

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Prolitus")
    ->setLastModifiedBy("Prolitus")
    ->setTitle("Office 2007 XLSX Test Document")
    ->setSubject("Office 2007 XLSX Test Document")
    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("Test result file");
    
    // Add some data

    $objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A'.$counter, 'Tran Date')
    ->setCellValue('B'.$counter, 'Value DaTE!')
    ->setCellValue('C'.$counter, 'Tran Type')
    ->setCellValue('D'.$counter, 'Invoice No')
    ->setCellValue('E'.$counter, 'Debit')
    ->setCellValue('F'.$counter, 'Credit');

    $counter += 0;
    // Data

    for($i = 0; $i <= 10; $i++) {
      $counter++;
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A'.$counter, '12/01/2020')
                  ->setCellValue('B'.$counter, '12/03/2020')
                  ->setCellValue('C'.$counter, 'Repayment')
                  ->setCellValue('D'.$counter, 'MOD-AHM-33090')
                  ->setCellValue('E'.$counter, '552,521,000')
                  ->setCellValue('F'.$counter, '521,000');
        if($counter == 10) {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$counter, 'Total Factored');
        }
        if($counter == 11) {
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$counter, 'Non Factored');
          $objPHPExcel->getActiveSheet()->getStyle("A".$counter)->getFont()->setBold(true);
        }
    }
    
    
    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$counter, 'Total Factored');
    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$counter, 'Non Factored');
    // $objPHPExcel->getActiveSheet()->getStyle("A".$counter)->getFont()->setBold(true);
    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$counter, 'asdfsd');

    $counter++;
    
    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('Simple');



    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="01simple.xlsx"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    
  }
}



