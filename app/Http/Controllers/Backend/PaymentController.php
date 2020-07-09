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
use App\Inv\Repositories\Models\Payment;
use Session;
use Helpers;
use DB;
use App\Libraries\Pdf;
use Carbon\Carbon;
use App\Inv\Repositories\Contracts\ApplicationInterface;
use PHPExcel;
use PHPExcel_IOFactory;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Helpers\ApportionmentHelper;
use Illuminate\Validation\Rule;

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
        $this->middleware('checkEodProcess');

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
	   //dd($tranType);
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
	 function validateDate($date, $format = 'd/m/Y') { 
	   return  $d = \DateTime::createFromFormat($format, $date);
	 }

	public function unsettledPayment() {
		return view('backend.payment.unsettled_payment');
	}

	public function settledPayment() {
		return view('backend.payment.settled_payment');
	}

 	public function EditPayment(Request $request) {
 		$paymentId = $request->payment_id;
	  	$data  =  $this->invRepo->getPaymentById($paymentId);
	   	return view('backend.payment.edit_payment')->with(['data' => $data]);
 	}
	 
	/* save payment details   */
	public function  savePayment(Request $request)
	{
		try {
            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            
			$arrFileData = $request->all();
			$validatedData = $request->validate([
				'payment_type' => Rule::requiredIf(function () use ($request) {
					return ($request->action_type == 1)?true:false;
					}),
				'utr_no' => 'required',
				'trans_type' => 'required',
				'customer_id' => 'required', 
				'virtual_acc' => 'required',  
				'date_of_payment' => 'required', 
				'amount' => 'required', 
				'description' => 'required'
			]);
			$user_id  = Auth::user()->user_id;
			$mytime = Carbon::now()->setTimezone(config('common.timezone'))->format('Y-m-d h:i:s');

			$utr ="";
			$check  ="";
			$unr  ="";
			if($request['payment_type']==1) {
				$utr =   $request['utr_no'];  
			} else  if($request['payment_type']==2) {
				$check = $request['utr_no'];
			} else  if($request['payment_type']==3) {
				$unr =  $request['utr_no'];
			} else  if($request['payment_type']==4) {
				$unr =  $request['utr_no'];
			}
			if(isset($arrFileData['doc_file']) && !is_null($arrFileData['doc_file'])) {
				$app_data = $this->appRepo->getAppDataByBizId($request->biz_id);
			  	$uploadData = Helpers::uploadUserLMSFile($arrFileData, $app_data->app_id);
				$userFile = $this->docRepo->saveFile($uploadData);
			}

			$paymentData = [
				'user_id' => $request->user_id,
				'biz_id' => $request->biz_id,
				'virtual_acc' => $request->virtual_acc,
				'action_type' => $request->action_type,
				'trans_type' => $request->trans_type,
				'parent_trans_id' => $request->charges,
				'amount' => $request->amount,
				'date_of_payment' => ($request['date_of_payment']) ? Carbon::createFromFormat('d/m/Y', $request['date_of_payment'])->format('Y-m-d') : '',
				'gst' => $request->gst,
				'sgst_amt' => $request->sgst_amt ?? 0,
				'cgst_amt' => $request->cgst_amt ?? 0,
				'igst_amt' => $request->igst_amt ?? 0,
				'payment_type' => $request->payment_type,
				'utr_no' => $utr ?? NULL,
				'unr_no' => $unr ?? NULL,
				'cheque_no' => $check ?? NULL,
				'tds_certificate_no' => $request->tds_certificate_no ?? '',
				'file_id' => $userFile->file_id ?? '',
				'description' => $request->description,
				'is_settled' => (in_array($request->action_type, [3])) ? '1':'0',
				'is_manual' => '1',
				'sys_date'=>\Helpers::getSysStartDate(),
				'created_at' => $mytime,
				'created_by' => $user_id,
				'generated_by' => 0,
			];
			$paymentId = NULL;
			
			if($request->has('charges') && $request->action_type == 3){
				$transaction = Transactions::find($request->charges);
				if(isset($transaction) && (float)$transaction->outstanding <= 0){
					$paymentData['is_refundable'] = '1';
				}else{
					$paymentData['is_refundable'] = '0';
				}
			}
			
			if (in_array($request->action_type, [1,3])) {
				$paymentId = Payment::insertPayments($paymentData);
				if(!is_int($paymentId)){
					Session::flash('error', $paymentId);
					return back();
				}
			}
			$udata=$this->userRepo->getSingleUserDetails($request->customer_id);
			$getAmount =  $this->invRepo->getRepaymentAmount($request->customer_id);  
			$enterAmount =  str_replace(',', '', $request->amount);
			  
			foreach($getAmount as $val)
			{
				$getAmount = $val->repayment_amount;
				if($getAmount >= $enterAmount) {
				  $finalAmount = $getAmount - $enterAmount;
				  $this->invRepo->singleRepayment($val->disbursal_id,$finalAmount);
				  Session::flash('message', 'Bulk amount has been saved');
				  return back();
				}
				else {       
				  $this->invRepo->singleRepayment($val->disbursal_id,0);
				}
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
				
			$tran  = [  
					'payment_id' => $paymentId,
					'link_trans_id' =>$request->charges,
					'parent_trans_id' =>$request->charges,
					'user_id' => $request['user_id'],
					'trans_date' => ($request['date_of_payment']) ? Carbon::createFromFormat('d/m/Y', $request['date_of_payment'])->format('Y-m-d') : '',
					'trans_type' => (in_array($request->action_type, [3])) ? 7 : $request['trans_type'],
					'amount' => str_replace(',', '', $request['amount']),
					'entry_type' => 1,
					'gst'=> $request['incl_gst'],
					'tds_per' => 1,
					'gl_flag' => 1,
					'soa_flag' => 1,
					'trans_by' => 1,
					'pay_from' => ($udata)?$udata->is_buyer:'',
					'is_settled' => 1,
					'is_posted_in_taaly' => 0,
					'sys_date'=>\Helpers::getSysStartDate(),
					'created_at' =>  $mytime,
                    'created_by' =>  $user_id,
                  ];
			if($request->action_type == 3){
				$res = $this->invRepo->saveRepaymentTrans($tran);
			}

			if($paymentId)
			{
		  		Session::flash('message',trans('backend_messages.add_payment_manual'));
			  	return redirect()->route('payment_list');
			}
			else
			{
				Session::flash('message', 'Something went wrong, Please try again');
				return back(); 
			}
	   	} catch (Exception $ex) {
			return Helpers::getExceptionMessage($ex);
		}
	}
	public function updatePayment(Request $request)
	{
		try {                    
                        if ($request->get('eod_process')) {
                            Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                            return back();
                        }
                        
			$arrFileData = $request->all();
			$user_id  = Auth::user()->user_id;
			$mytime = Carbon::now();

			if(isset($arrFileData['doc_file']) && !is_null($arrFileData['doc_file'])) {
				$app_data = $this->appRepo->getAppDataByBizId($request->biz_id);
			  	$uploadData = Helpers::uploadUserLMSFile($arrFileData, $app_data->app_id);
				$userFile = $this->docRepo->saveFile($uploadData);
			}

			$paymentData = [
				'tds_certificate_no' => $request->tds_certificate_no ?? '',
				'file_id' => $userFile->file_id ?? '',
				'description' => $request->description,
				'updated_at' => $mytime,
				'updated_by' => $user_id,
			];
			
			$response =  $this->invRepo->updatePayment($paymentData, $request->payment_id);

			Session::flash('message',trans('success_messages.paymentUpdated'));
        	return redirect()->route('payment_list');
	   	} catch (Exception $ex) {
			return Helpers::getExceptionMessage($ex);
		}
	}
   /////////* save bulk payment by excel ///////////////////////
   public function  saveExcelPayment(Request $request)
   {
                        if ($request->get('eod_process')) {
                            Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                            return back();
                        }
                        
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
  public function  paymentAdviceList(Request $request)
  {
	$trans_id = preg_replace('#[^0-9]#', '', $request->get('trans_id'));
	// $trans_data = $this->invRepo->findTransById($trans_id);
	return view('backend.payment.payment_advice_list');

  }

  public function  paymentAdviceExcel(Request $request)
  {
	$transId = $request->get('trans_id');
	$counter = 1;
	$overdueInterest = 0;
	$interestRefund = 0;
	$totalMarginAmount = 0;
	$nonFactoredAmount = 0;
	
	$repayment = $this->lmsRepo->getTransactions(['trans_id'=>$transId,'trans_type'=>config('lms.TRANS_TYPE.REPAYMENT')])->first();
	$repaymentTrails = $this->lmsRepo->getTransactions(['payment_id'=>$transId]);
	
	$disbursalIds = Transactions::where('payment_id','=',$transId)
	->whereNotNull('disbursal_id')
	->where('trans_type','=',config('lms.TRANS_TYPE.INVOICE_KNOCKED_OFF'))
	->distinct('disbursal_id')
	->pluck('disbursal_id')
	->toArray();
	
	$principalSettled = Transactions::where('payment_id','=',$transId)
	->whereNotNull('disbursal_id')
	->whereIn('trans_type',[config('lms.TRANS_TYPE.INVOICE_KNOCKED_OFF'),config('lms.TRANS_TYPE.INVOICE_PARTIALLY_KNOCKED_OFF')])
	->sum('amount');
	
	$amountForMargin = $this->userRepo->getDisbursalList()->whereIn('disbursal_id',$disbursalIds)
	->sum('invoice_approve_amount'); 
	$marginAmountData = $this->userRepo->getDisbursalList()->whereIn('disbursal_id',$disbursalIds)
	->groupBy('margin')
	->select(DB::raw('(sum(invoice_approve_amount)*margin)/100 as margin_amount ,margin'))->get();
	
	if($principalSettled>0){
	  $nonFactoredAmount = $repayment->amount-$principalSettled;
	}
	
	//dd($repayment, $repaymentTrails, $disbursalIds, $marginAmountData, $totalMarginAmount);
	$objPHPExcel =  new PHPExcel();
	$objPHPExcel->getProperties()
				->setCreator("Capsave")
				->setLastModifiedBy("Capsave")
				->setTitle("Payment Advice Excel")
				->setSubject("Payment Advice Excel")
				->setDescription("Payment Advice Excel")
				->setKeywords("Payment Advice Excel")
				->setCategory("Payment Advice Excel");
	
	$objPHPExcel->getActiveSheet()->getStyle("A".$counter.":F".$counter)->getFont()->setBold(true);

	foreach(range('A','F') as $columnID) {
	  $objPHPExcel->getActiveSheet()
				  ->getColumnDimension($columnID)
				  ->setAutoSize(true);
	}

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$counter, 'Tran Date')
				->setCellValue('B'.$counter, 'Value Date')
				->setCellValue('C'.$counter, 'Tran Type')
				->setCellValue('D'.$counter, 'Invoice No')
				->setCellValue('E'.$counter, 'Debit')
				->setCellValue('F'.$counter, 'Credit');
				

	if($repayment->count()>0){
	  $counter++;
	  $objPHPExcel->setActiveSheetIndex(0)
	  ->setCellValue('A'.$counter, date('d-M-Y',strtotime($repayment->trans_date)))
	  ->setCellValue('B'.$counter, date('d-M-Y',strtotime($repayment->created_at)))
	  ->setCellValue('C'.$counter, ($repayment->transType->chrg_master_id!='0')?$repayment->transType->charge->chrg_name:$repayment->transType->trans_name)
	  ->setCellValue('D'.$counter, ($repayment->disburse && $repayment->disburse->invoice && $repayment->trans_type == config('lms.TRANS_TYPE.INVOICE_KNOCKED_OFF'))? $repayment->disburse->invoice->invoice_no:'')
	  ->setCellValue('E'.$counter, ($repayment->entry_type=='0')?$repayment->amount:'')
	  ->setCellValue('F'.$counter, ($repayment->entry_type=='1')?$repayment->amount:'');            

	  foreach($repaymentTrails as $rtrail){
		$counter++;
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$counter, date('d-M-Y',strtotime($rtrail->trans_date)))
		->setCellValue('B'.$counter, date('d-M-Y',strtotime($rtrail->created_at)))
		->setCellValue('C'.$counter, ($rtrail->transType->chrg_master_id!='0')?$rtrail->transType->charge->chrg_name:$rtrail->transType->trans_name)
		->setCellValue('D'.$counter, ($rtrail->disburse && $rtrail->disburse->invoice && $rtrail->trans_type == config('lms.TRANS_TYPE.INVOICE_KNOCKED_OFF'))? $rtrail->disburse->invoice->invoice_no:'')
		->setCellValue('E'.$counter, ($rtrail->entry_type=='0')?$rtrail->amount:'')
		->setCellValue('F'.$counter, ($rtrail->entry_type=='1')?$rtrail->amount:'');  

		if($rtrail->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
		  $overdueInterest += $rtrail->amount;
		}

		if($rtrail->trans_type == config('lms.TRANS_TYPE.INTEREST_REFUND')){
		  $interestRefund += $rtrail->amount;
		}
	  }
	}
 
	$counter +=2;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$counter, 'Total Factored')
				->setCellValue('E'.$counter, $repayment->amount);


	$counter +=1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$counter, 'Non Factored')
				->setCellValue('E'.$counter, $nonFactoredAmount);
	$objPHPExcel->getActiveSheet()->getStyle("A".$counter.":F".$counter)->getFont()->setBold(true);
	
	$counter +=2;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$counter, 'Total amt for Margin')
				->setCellValue('E'.$counter, $amountForMargin);
	
	foreach($marginAmountData as $margin){

	  $counter +=1;
	  $objPHPExcel->setActiveSheetIndex(0)
	  ->setCellValue('A'.$counter, '% Margin')
	  ->setCellValue('D'.$counter, $margin['margin'].' %')
	  ->setCellValue('E'.$counter, $margin['margin_amount']);
	  $totalMarginAmount += $margin['margin_amount'];
	}
	
	$counter +=1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$counter, 'Overdue Interest')
				->setCellValue('E'.$counter, $overdueInterest);
	
	$totalMarginAmount -= $overdueInterest;
	
	// $counter +=1;
	// $objPHPExcel->setActiveSheetIndex(0)
	//             ->setCellValue('A'.$counter, 'Interest Sept')
	//             ->setCellValue('E'.$counter, '');

	$counter +=1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$counter, 'Margin Released')
				->setCellValue('E'.$counter, ($totalMarginAmount>0)?$totalMarginAmount:0);
	$objPHPExcel->getActiveSheet()->getStyle("A".$counter.":F".$counter)->getFont()->setBold(true);

	$counter +=2;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$counter, 'Interest Refund')
				->setCellValue('E'.$counter, $interestRefund);
	$objPHPExcel->getActiveSheet()->getStyle("A".$counter.":F".$counter)->getFont()->setBold(true);
	$totalMarginAmount += $interestRefund;

	$counter +=1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('F'.$counter, $totalMarginAmount);
	
    /*  $counter +=1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$counter, 'Overdue')
				->setCellValue('E'.$counter, '');
	
	$counter +=1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$counter, 'Int Type')
				->setCellValue('E'.$counter, '');
	*/
	// Rename worksheet
	$objPHPExcel->getActiveSheet()
				->setTitle('Payment Advice');



	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Payment Advice.xlsx"');
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

  public function paymentInvoiceList(Request $request)
  {
	$transId = $request->get('trans_id');
	$data = $this->calculateRefund($transId);
	return view('backend.payment.payment_invoice_list', $data);
  }
  
  public function createPaymentRefund(Request $request)
  {
    if ($request->get('eod_process')) {
        Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
        return back();
    }
    
	$transId = $request->get('trans_id');
	$refundAmount = $request->get('total_refund_amount');

	try {
		$addlData=[];
		$addlData['trans_id'] = $transId;
		$addlData['amount'] = $refundAmount;
		$addlData['sharing_comment'] = '';
		
		$refundData = $this->calculateRefund($transId);
	  
		$transaction = [];
		$transactions = [];

		$transaction['TRANS_DATE'] = $refundData['repayment']->trans_date;
		$transaction['VALUE_DATE'] = $refundData['repayment']->created_at;
		
		if ($refundData['repayment']->transType->chrg_master_id != '0') {
			$transaction['TRANS_TYPE'] = $refundData['repayment']->transType->charge->chrg_name;
		} else {
			$transaction['TRANS_TYPE'] = $refundData['repayment']->transType->trans_name;
		}
										
		if ($refundData['repayment']->disbursal_id &&  $refundData['repayment']->disburse && $refundData['repayment']->disburse->invoice) {
			$transaction['INV_NO'] = $refundData['repayment']->disburse->invoice->invoice_no;
		} else {
			$transaction['INV_NO'] = '';
		}      
		
		if ($refundData['repayment']->entry_type == '0') {
			$transaction['DEBIT'] = $refundData['repayment']->amount;
		} else {
			$transaction['DEBIT'] = '';
		}

		if ($refundData['repayment']->entry_type == '1') {
			$transaction['CREDIT'] = $refundData['repayment']->amount;
		} else {
			$transaction['CREDIT'] = '';
		}
		
		$transactions[] = $transaction;

		foreach ($refundData['repaymentTrails'] as $repay) {
		  $transaction = [];
		  $transaction['TRANS_DATE'] = $repay->trans_date;
		  $transaction['VALUE_DATE'] = $repay->created_at;

		  if ($repay->transType->chrg_master_id != '0') {
			  $transaction['TRANS_TYPE'] = $repay->transType->charge->chrg_name;
		  } else {
			  $transaction['TRANS_TYPE'] = $repay->transType->trans_name;
		  }

		  if ($repay->disbursal_id && $repay->disburse && $repay->disburse->invoice->invoice_no) {
			  $transaction['INV_NO'] = $repay->disburse->invoice->invoice_no;
		  } else {
			  $transaction['INV_NO'] = '';
		  }      

		  if ($repay->entry_type == '0') {
			  $transaction['DEBIT'] = $repay->amount;
		  } else {
			  $transaction['DEBIT'] = '';
		  }

		  if ($repay->entry_type == '1') {
			  $transaction['CREDIT'] = $repay->amount;
		  } else {
			  $transaction['CREDIT'] = '';
		  }
		  
		  $transactions[] = $transaction;   
		}
	
		$data['TRANSACTIONS'] = $transactions;
		$data['TOTAL_FACTORED'] = $refundData['repayment']->amount;
		$data['NON_FACTORED'] = $refundData['nonFactoredAmount'];
		$data['OVERDUE_INTEREST'] = $refundData['interestOverdue'];
		$data['INTEREST_REFUND'] = $refundData['interestRefund'];
		$data['MARGIN_RELEASED'] = $refundData['marginTotal'];
		$data['TOTAL_REFUNDABLE_AMT'] = $refundData['refundableAmount'];
		//$data['TOTAL_AMT_FOR_MARGIN'] = '';
		//$data['MARGIN'] = '';

		$this->saveRefundData($transId, $data); 
		$result = $this->createApprRequest(config('lms.REQUEST_TYPE.REFUND'), $addlData);
		//$this->saveRefundTransactions($transId,);
		if ($result) {
			Session::flash('is_accept', 1);
			return redirect()->back();
		} else {
			Session::flash('error_code', 'create_refund');
			return redirect()->back();                
		}

	} catch (Exception $ex) {
		return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
	}    
  }
}