<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Auth;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessInformationRequest;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Models\BizApi;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\PaymentExcel;
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
use App\Inv\Repositories\Models\Lms\InterestAccrualTemp;
use App\Helpers\FileHelper;
use App\Inv\Repositories\Models\UserFile;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;

class PaymentController extends Controller {

	protected $invRepo;
	protected $docRepo;
	protected $master;
	use LmsTrait;
	use ActivityLogTrait;
	public function __construct(InvoiceInterface $invRepo, InvDocumentRepoInterface $docRepo, InvLmsRepoInterface $lms_repo,InvUserRepoInterface $user_repo, ApplicationInterface $appRepo, FileHelper $file_helper, MasterInterface $master) {
		$this->invRepo = $invRepo;
		$this->docRepo = $docRepo;
		$this->lmsRepo = $lms_repo;
		$this->userRepo = $user_repo;
		$this->appRepo = $appRepo;
				$this->fileHelper = $file_helper;
		$this->master = $master;
		$this->middleware('auth');
        $this->middleware('checkEodProcess');
        $this->middleware('checkBackendLeadAccess');

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

	public function unsettledPayment(Request $request) {
		$customer = [];
		if($request->has('user_id')){
			$lmsUser = $this->userRepo->lmsGetCustomer($request->get('user_id'));
			if($lmsUser){
				$customer['user_id'] = $lmsUser->user_id; 
				$customer['customer_id'] = $lmsUser->customer_id;
			}
		}
		return view('backend.payment.unsettled_payment')->with(['customer'=>$customer]);
	}

	public function settledPayment() {
		return view('backend.payment.settled_payment');
	}

 	public function EditPayment(Request $request) {
		 $paymentId = $request->payment_id;
		 $paymentType = $request->payment_type;
		  $data  =  $this->invRepo->getPaymentById($paymentId);
	   	return view('backend.payment.edit_payment')->with(['data' => $data]);
 	}
	 
	/* save payment details   */
	public function  savePayment(Request $request)
	{
		try {
			$transaction = null;
			$request['amount'] = str_replace(',', '', $request->amount);

			if ($request->get('eod_process')) {
				Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
				return back();
			}
			$curdate = Carbon::parse(Helpers::getSysStartDate())->format('Y-m-d');
			$curdateMesg = Carbon::parse(Helpers::getSysStartDate())->format('d/m/Y');
			$arrFileData = $request->all();
			$validatedData = $request->validate([
				'payment_type' => Rule::requiredIf(function () use ($request) {
					return ($request->action_type == 1)?true:false;
				}),
				// 'charges' => Rule::requiredIf(function () use ($request) {
				// 	return ($request->action_type == 3)?true:false;
				// }),
				'utr_no' => Rule::requiredIf(function () use ($request) {
					return ($request->action_type == 1)?true:false;
				}),
				'trans_type' => Rule::requiredIf(function () use ($request) {
					return ($request->action_type == 1)?true:false;
				}),
				'customer_id' => 'required', 
				'virtual_acc' => 'required',  
				'date_of_payment' => 'required|date_format:d/m/Y|before_or_equal:'.$curdate,
				'amount' => 'required|numeric|gt:0', 
				'description' => 'required',
				'doc_file' => 'checkmime'
			],
			[
				'amount.required' => 'Transaction amount is required',
				'amount.numeric' => 'Transaction amount must be number',
				'amount.gt' => 'Transaction amount must be greater than zero',
				'date_of_payment.before_or_equal' => 'The Transaction Date must be a date before or equal to '.$curdateMesg.'.',
				'doc_file.checkmime' => 'Invalid file format'
			]);

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
                        
			if(isset($arrFileData['cheque']) && !is_null($arrFileData['cheque'])) {
				$app_data = $this->appRepo->getAppDataByBizId($request->biz_id);
                $arrFileData['doc_file'] = $arrFileData['cheque'];
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
				'is_settled' => '0',
				'is_manual' => '1',
				'sys_date'=>\Helpers::getSysStartDate(),
				'generated_by' => 0,
			];
			$paymentId = NULL;
			
			if($request->has('charges') && $request->action_type == 3){
				$paymentData['trans_type'] = '7'; // for tds 7
				$transaction = Transactions::find($request->charges);
				if($transaction){
					if(round($transaction->TDSAmount, 2) < $request->amount){
						Session::flash('error', 'TDS amount must be less than or equal to '.$transaction->TDSAmount);
						return back();
					}
				}
				if(isset($transaction) && (float)$transaction->outstanding <= 0){
					$paymentData['is_refundable'] = '1';
				}else{
					$paymentData['is_refundable'] = '0';
				}
			}
			if (in_array($request->action_type, [1,3])) {
				$paymentId = Payment::insertPayments($paymentData);
			
				$whereActivi['activity_code'] = 'save_payment';
				$activity = $this->master->getActivity($whereActivi);
				if(!empty($activity)) {
					$activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
					$activity_desc = 'Add Repayment & Waived Off TDS (Manage Repayment)';
					$arrActivity['app_id'] = null;
					$this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($paymentData), $arrActivity);
				}   				
				
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
            $validatedData = Validator::make($request->all(),
				[
					'doc_file' => 'checkmime',
					'cheque' => 'checkmime'
                ],
				[
					'doc_file.checkmime' => 'Invalid file format',
					'cheque.checkmime' => 'Invalid file format'
                ]);
			if ($validatedData->fails()) {
				return redirect()->back()
					->withErrors($validatedData)
					->withInput();
			}
            $paymentId = $request->get('payment_id');
			$oldData  =  $this->invRepo->getPaymentById($paymentId);  
			$oldDateOfPayment = $oldData->date_of_payment;
			$currDate = Carbon::now()->format('Y-m-d 00:00:00');
			$arrFileData = $request->all();
			// $fileId = '';

			if(isset($request->tds_certificate_no)) {
				$id = $request->has('payment_id') ? $request->get('payment_id') : null ;
				$result =  Payment::checkTdsCertificate($request->tds_certificate_no, $id);
				if(isset($result[0])) {
					Session::flash('error', 'Please enter another TDS Certificate No.');
					return back();	
				}
			}

			if(isset($arrFileData['doc_file']) && !is_null($arrFileData['doc_file'])) {
				$app_data = $this->appRepo->getAppDataByBizId($request->biz_id);
			  	$uploadData = Helpers::uploadUserLMSFile($arrFileData, $app_data->app_id);
				$userFile = $this->docRepo->saveFile($uploadData);
				// $fileId = $userFile->file_id;
			}

			// dd($arrFileData);
			// if(!isset($arrFileData['doc_file'])) {
			// 	$data  =  $this->invRepo->getPaymentById($request->payment_id);
			// 	if(!empty($data) && $data->file_id == 0){
			// 		$fileId = $data->file_id;
			// 	}
			// }
			// dd($userFile, 'kjsafhk');

			if(isset($arrFileData['cheque']) && !is_null($arrFileData['cheque'])) {
				$app_data = $this->appRepo->getAppDataByBizId($request->biz_id);
                $arrFileData['doc_file'] = $arrFileData['cheque'];
			  	$uploadData = Helpers::uploadUserLMSFile($arrFileData, $app_data->app_id);
				$userFile = $this->docRepo->saveFile($uploadData);
				// $fileId = $userFile->file_id;
			}

			$request['amount'] = str_replace(',', '', $request->amount);
			$paymentData = [
				'cheque_no' => $request->cheque_no ?? '',
				'tds_certificate_no' => $request->tds_certificate_no ?? '',
				'file_id' => $userFile->file_id ?? '',
				// 'file_id' => $fileId ?? '',
				'amount' => ($request['amount']) ? $request->amount : $oldData->amount,
				'date_of_payment' => ($request['date_of_payment']) ? Carbon::createFromFormat('d/m/Y', $request['date_of_payment'])->format('Y-m-d') : $oldData->date_of_payment,
				'description' => $request->description,
			];
			
			$response =  $this->invRepo->updatePayment($paymentData, $request->payment_id);

			$whereActivi['activity_code'] = 'update_payment';
			$activity = $this->master->getActivity($whereActivi);
			if(!empty($activity)) {
				$activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
				$activity_desc = 'Update Payment, Repayment List (Manage Repayment)';
				$arrActivity['app_id'] = null;
				$this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['paymentData'=>$paymentData, 'request'=>$request->all()]), $arrActivity);
			}   			
			
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
							'comment' => $request['remarks'][$i] 
						];
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
  
    public function downloadCheque()
    {
        $paymentId = $request->get('payment_id');
        try {

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }  
    }
	
	public function deletePayment(Request $request)
	{
		try {
			$paymentId = $request->get('payment_id');
			if($paymentId){
				$payment = Payment::find($paymentId);
				if($payment){
					if($payment->is_settled == '0' && in_array($payment->action_type, [1,3]) && in_array($payment->trans_type, [17,7])){
						$payment->delete();
						InterestAccrualTemp::where('payment_id',$paymentId)->delete();

						$whereActivi['activity_code'] = 'delete_payment';
						$activity = $this->master->getActivity($whereActivi);
						if(!empty($activity)) {
							$activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
							$activity_desc = 'Delete Payment (Manage Payment)';
							$arrActivity['app_id'] = null;
							$this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
						}						
						
						return response()->json(['status' => 1,'message' => 'Successfully Deleted Payment']); 
					}
					else{
						return response()->json(['status' => 0,'message' => 'Invalid Request: Payment cannot be deleted']);
					}
				}
				return response()->json(['status' => 0,'message' => 'Record Not Found / Already deleted!']);
			}
			return response()->json(['status' => 0,'message' => 'Invalid Request: Payment details missing.']);
        } catch (Exception $ex) {
			return response()->json(['status' => 0,'message' => Helpers::getExceptionMessage($ex)]); 
		}  
	}

	public function viewUploadedFile(Request $request){
        try {
            
            $file_id = $request->get('file_id');
            $fileData = $this->docRepo->getFileByFileId($file_id);
            
            $filePath = 'app/public/'.$fileData->file_path;
            $path = storage_path($filePath);
           
            if (file_exists($path)) {
                return response()->file($path);
            }else{
                exit('Requested file does not exist on our server!');
			}
			
        } catch (Exception $ex) {                
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }


    /**
     * Upload excel file for import
     *
     * @param Request $request
     * @return type
     */
    public function uploadExcelPayments(Request $request)
    {
        return view('backend.payment.upload_xlsx_payments');
    }


    /**
     * Import uploaded excel file
     *
     * @param Request $request
     * @return type
     */
    public function importExcelPayment(Request $request)
    {
        try {
            $arrFileData = $request->files;
            $inputArr = [];
            $path = '';
            $userId = Auth::user()->user_id;
            if ($request['doc_file']) {
                if (!Storage::exists('/public/nachexcel/response')) {
                    Storage::makeDirectory('/public/nachexcel/response');
                }
                $path = Storage::disk('public')->put('/nachexcel/response', $request['doc_file'], null);
            }
            $uploadedFile = $request->file('doc_file');
            $destinationPath = storage_path() . '/app/public/nachexcel/response';
            $date = new DateTime;
            $currentDate = $date->format('Y-m-d H:i:s');
            $fileName = $currentDate.'_nachexcel.xlsx';
            if ($uploadedFile->isValid()) {
                $uploadedFile->move($destinationPath, $fileName);
                $filePath = $destinationPath.'/'.$fileName;
                $fileContent = $this->fileHelper->readFileContent($filePath);
                $fileData = $this->fileHelper->uploadFileWithContent($filePath, $fileContent);
                $file = UserFile::create($fileData);
                $nachBatchData['res_file_id'] = $file->file_id;
                $this->appRepo->saveNachBatch($nachBatchData, null);
                $file_id = $file->file_id;
            }
            $fullFilePath  = $destinationPath . '/' . $fileName;
            //echo $fullFilePath; exit;

            $header = [
                0,1,2,3,4,5,6,7,8,9,10,11,12,13,14
            ];
            $fileArrayData = $this->fileHelper->excelNcsv_to_array($fullFilePath, $header);

           // dd($fileArrayData);
            if($fileArrayData['status'] != 'success'){
                Session::flash('message', 'Please import correct format sheet,');
                return redirect()->route('payment_list');
            }
            $rowData = $fileArrayData['data'];
            if (empty($rowData)) {
                Session::flash('message', 'File does not contain any record');
                return redirect()->route('payment_list');
            }
            foreach ($rowData as $key => $value) {
                if(!empty($value[0])){
                    $virtual_acc = trim($value[1]);
                    if (!empty($virtual_acc) && $virtual_acc != null) {
                         $wherCond['virtual_acc_id'] = $virtual_acc;
                         $lmsData = $this->appRepo->getLmsUsers($wherCond)->first();

                       // $date="2012-09-12";
                        $txn_date = $value[10];

                        if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/",$txn_date)) {
                            
                        } else {
                            Session::flash('error','Date formate is not correct(txnDate), Use only dd-mm-yyyy formate');
                            //Session::flash('operation_status', 1);
                            return redirect()->route('payment_list');
                        }

                        $TrnTimeStampArray = explode(' ',$value[13]);
                        $TrnTimeStamp  = $TrnTimeStampArray[0];

                        if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/",$TrnTimeStamp)) {

                        } else {
                            Session::flash('error','Date formate is not correct(Trn TimeStamp), Use only dd-mm-yyyy formate');
                            //Session::flash('operation_status', 1);
                            return redirect()->route('payment_list');
                        }

                           $paymentExcelData = [
				'user_id' => $lmsData ? $lmsData->user_id : '',
				'bankcode' => $value[0],
				'virtual_acc' => $virtual_acc,
				'instrument_type' => $value[2],
				'remitter_account_number' => $value[3],
				'remitter_ifsc_code' => $value[4],
				'remitter_name' => $value[5],
				'contact_no' => $value[6],
				'email' => $value[7],
				'is_status' => $value[8],
				'txn_amount' => $value[9],
				'txn_date' => ($value[10]) ? Carbon::createFromFormat('d-m-Y', $value[10])->format('Y-m-d') : '',
				'txn_ref_number' => $value[11],
				'client_code' => $value[12],
				'trn_time_stamp' => ($value[13]) ? date('Y-m-d H:i:s', strtotime($value[13])) : '',
				'file_id' => $file_id
                            ];
                           

                         $paymentExcelId = PaymentExcel::insertPaymentsExcel($paymentExcelData);
                         //echo "==>".$paymentExcelId; exit;

                         if($value[8] == 'Success') {
                            $wherCond['virtual_acc_id'] = $virtual_acc;
                            $lmsData = $this->appRepo->getLmsUsers($wherCond)->first();
                            $user_id = $lmsData ? $lmsData->user_id : '';

                            $BizDataArray = $this->appRepo->getBizDataByUserId($user_id)->first();
                            $biz_id = $BizDataArray ? $BizDataArray->biz_id : '';
                            $biz_id = $biz_id;
                            $virtual_acc = $virtual_acc;
                            $action_type = 1;
                            $trans_type = 17; //17 for Repayment
                            $parent_trans_id = '';
                            $amount = $value[9];
                            $date_of_payment = ($value[10]) ? Carbon::createFromFormat('d-m-Y', $value[10])->format('Y-m-d') : '';
                           // $date_of_payment = '';
                            $gst = '';
                            $sgst_amt = 0;
                            $cgst_amt = 0;
                            $igst_amt = 0;
                            $payment_type = '2';
                            $utr_no = '';
                            $unr_no = '';
                            $cheque_no = '';
                            $tds_certificate_no = '';
                            $file_id = $file_id;
                            $description = '';
                            $is_settled = '0';
                            $is_manual = '3'; // Automatic
                            $sys_date = \Helpers::getSysStartDate();
                            $generated_by = 1;
                            
                            $paymentData = [
				'user_id' => $user_id,
				'biz_id' => $biz_id,
				'virtual_acc' => $virtual_acc,
				'action_type' => $action_type,
				'trans_type' => $trans_type,
				'parent_trans_id' => $parent_trans_id,
				'amount' => $amount,
				'date_of_payment' => $date_of_payment,
				'gst' => $gst,
				'sgst_amt' => $sgst_amt,
				'cgst_amt' => $cgst_amt,
				'igst_amt' => $igst_amt,
				'payment_type' => $payment_type,
				'utr_no' => $utr_no,
				'unr_no' => $unr_no,
				'cheque_no' => $cheque_no,
				'tds_certificate_no' => $tds_certificate_no,
				'file_id' => $file_id,
				'description' => $description,
				'is_settled' => $is_settled,
				'is_manual' => $is_manual,
				'sys_date'=> $sys_date,
				'generated_by' => $generated_by,
                                'generated_by' => 1,
                                'is_refundable' => 1,
                                'payment_excel_id' => $paymentExcelId
			];
                           // dd($paymentData);
			$paymentId = Payment::insertPayments($paymentData);

                        }
                    }
                }
            }
            Session::flash('message',trans('Excel Data Imported successfully.'));
            Session::flash('operation_status', 1);
            return redirect()->route('payment_list');
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

}