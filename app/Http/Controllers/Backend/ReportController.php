<?php

namespace App\Http\Controllers\Backend;
use Auth;
use Mail;
use Helpers;
use Session;
use PHPExcel;
use PDF as DPDF;
use Carbon\Carbon;
use App\Events\Event;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use App\Helpers\FileHelper;
use PHPExcel_Cell_DataType;
use Illuminate\Http\Request;
use PHPExcel_Style_Alignment;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Models\Anchor;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\FinanceModel;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Contracts\ReportInterface;
use App\Inv\Repositories\Models\Lms\OverdueReportLog;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Models\Lms\OutstandingReportLog;
use App\Inv\Repositories\Models\Lms\UserInvoice;
use App\Inv\Repositories\Models\Lms\ReconReportLog;
use App\Mail\SendEmail;

class ReportController extends Controller
{

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	protected $invRepo;
	public function __construct(InvoiceInterface $invRepo, ReportInterface $reportsRepo, FileHelper $file_helper) {
		$this->invRepo = $invRepo;
		$this->reportsRepo = $reportsRepo;
		$this->fileHelper = $file_helper;
		$this->middleware('guest')->except('logout');
		$this->middleware('checkBackendLeadAccess');
	}
   

	public function index(Request $request) {
		try {
			return view('reports.summary');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
		
	}

	public function customer(Request $request) {
		try {
			return view('reports.customer');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
		
	}

	public function leaseRegister(Request $request) {
		try {
			return view('reports.lease_register');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
	}

	public function interestBreakup(Request $request) {
		try {
			return view('reports.interest_breakup');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
	}

	public function downloadInterestBreakup(Request $request) {
		if($request->get('from_date')!= '' && $request->get('to_date')!=''){
			$from_date = $request->get('from_date');
			$to_date = $request->get('to_date');
		}
		$condArr = [
			'from_date' => $from_date ?? NULL,
			'to_date' => $to_date ?? NULL,
		];
		$rows = 1;
		$sheet =  new PHPExcel();
		$sheet->getActiveSheet()->getStyle('A1:M1')->applyFromArray(['font' => ['bold'  => true]]);
		$sheet->setActiveSheetIndex(0)
				->setCellValue('A'.$rows, 'Loan #')
				->setCellValue('B'.$rows, 'Cutomer ID')
				->setCellValue('C'.$rows, 'Client Name')
				->setCellValue('D'.$rows, 'Amount Disbursed (₹)')
				->setCellValue('E'.$rows, 'From Date')
				->setCellValue('F'.$rows, 'To date')
				->setCellValue('G'.$rows, 'Days')
				->setCellValue('H'.$rows, 'Interest Rate (%)')
				->setCellValue('I'.$rows, 'Interest Amount (₹)')
				->setCellValue('J'.$rows, 'Date of Interest Collection')
				->setCellValue('K'.$rows, 'TDS Rate (%)')
				->setCellValue('L'.$rows, 'TDS Amount (₹)')
				->setCellValue('M'.$rows, 'Net Interest (₹)')
				->setCellValue('N'.$rows, 'Tally Batch');
		$rows++;
		$exceldata = $this->reportsRepo->getInterestBreakupReport($condArr, NULL);
		foreach($exceldata as $rowData){
			$sheet->setActiveSheetIndex(0)
				->setCellValue('A' . $rows, $rowData['loan'])
				->setCellValue('B' . $rows, $rowData['cust_id'])
				->setCellValue('C' . $rows, $rowData['client_name'])
				->setCellValue('D' . $rows, number_format($rowData['disbursed_amt'],2))
				->setCellValue('E' . $rows, Carbon::parse($rowData['from_date'])->format('d-m-Y'))
				->setCellValue('F' . $rows, Carbon::parse($rowData['to_date'])->format('d-m-Y'))
				->setCellValue('G' . $rows, number_format($rowData['days'],2))
				->setCellValue('H' . $rows, number_format($rowData['int_rate'],2))
				->setCellValue('I' . $rows, number_format($rowData['int_amt'],2))
				->setCellValue('J' . $rows, Carbon::parse($rowData['collection_date'])->format('d-m-Y'))
				->setCellValue('K' . $rows, $rowData['tds_rate'])
				->setCellValue('L' . $rows, $rowData['tds_amt']?number_format($rowData['tds_amt'],2):'')
				->setCellValue('M' . $rows, number_format($rowData['net_int'],2))
				->setCellValue('N' . $rows, $rowData['tally_batch']);
			$rows++;
		}
		
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Interest Breakup Report.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
		$objWriter->save('php://output');     
	}

	public function chargeBreakup(Request $request){
		try {
			return view('reports.charge_breakup');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
	}

	public function downloadChargeBreakup(Request $request){
		$rowWhere = null;
		if($request->get('from_date')!= '' && $request->get('to_date')!=''){
			$from_date = $request->get('from_date');
			$to_date = $request->get('to_date');
			$rowWhere = "trans_date between '".$from_date."' AND '". $to_date."'";
		}
		$rows = 1;
		$sheet =  new PHPExcel();
		$sheet->getActiveSheet()->getStyle('A1:H1')->applyFromArray(['font' => ['bold'  => true]]);
		$sheet->setActiveSheetIndex(0)
				->setCellValue('A'.$rows, 'Loan #')
				->setCellValue('B'.$rows, 'Cutomer ID')
				->setCellValue('C'.$rows, 'Client Name')
				->setCellValue('D'.$rows, 'Charge Date')
				->setCellValue('E'.$rows, 'Charge Name')
				->setCellValue('F'.$rows, 'Charge (%)')
				->setCellValue('G'.$rows, 'Charge Amount (₹)')
				->setCellValue('H'.$rows, 'GST Amount (₹)')
				->setCellValue('I'.$rows, 'Total Amount (₹)')
				->setCellValue('J'.$rows, 'Tally Batch #');
		$rows++;
		$exceldata = $this->reportsRepo->getChargeBreakupReport([], $rowWhere);
		foreach($exceldata as $rowData){
			$sheet->setActiveSheetIndex(0)
			->setCellValue('A'. $rows, $rowData['loan'])
			->setCellValue('B'. $rows, $rowData['cust_id'])
			->setCellValue('C'. $rows, $rowData['client_name'])
			->setCellValue('D'. $rows, Carbon::parse($rowData['trans_date'])->format('d-m-Y'))
			->setCellValue('E'. $rows, $rowData['chrg_name'])
			->setCellValue('F'. $rows, $rowData['chrg_rate'] ? number_format($rowData['chrg_rate'],2):'')
			->setCellValue('G'. $rows, $rowData['chrg_amt'] ? number_format($rowData['chrg_amt'],2):'')
			->setCellValue('H'. $rows, $rowData['gst'] ? number_format($rowData['gst'],2):'')
			->setCellValue('I'. $rows, $rowData['net_amt'] ? number_format($rowData['net_amt'],2):'')
			->setCellValue('J'. $rows, $rowData['tally_batch']);
			$rows++;
		}
		
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Charge Breakup Report.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
		$objWriter->save('php://output');
	}

	public function tdsBreakup(Request $request){
		try {
			return view('reports.tds_breakup');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
	}

	public function downloadTdsBreakup(Request $request){
		$rowWhere = null;
		if($request->get('from_date')!= '' && $request->get('to_date')!=''){
			$from_date = $request->get('from_date');
			$to_date = $request->get('to_date');
			$rowWhere = "trans_date between '".$from_date."' AND '". $to_date."'";
		}
		$rows = 1;
		$sheet =  new PHPExcel();
		$sheet->getActiveSheet()->getStyle('A1:M1')->applyFromArray(['font' => ['bold'  => true]]);
		$sheet->setActiveSheetIndex(0)
			->setCellValue('A'.$rows, 'Loan #')
			->setCellValue('B'.$rows, 'Cutomer ID')
			->setCellValue('C'.$rows, 'Client Name')
			->setCellValue('E'.$rows, 'Interest Amount (₹)')
			->setCellValue('F'.$rows, 'Date of Interest Deduction')
			->setCellValue('D'.$rows, 'TDS Date')
			->setCellValue('G'.$rows, 'TDS Amount (₹)')
			->setCellValue('H'.$rows, 'TDS certificate #')
			->setCellValue('I'.$rows, 'Tally Batch #');
		$rows++;
		$exceldata = $this->reportsRepo->getTdsBreakupReport([], $rowWhere);
		foreach($exceldata as $rowData){
			$sheet->setActiveSheetIndex(0)
				->setCellValue('A' . $rows, $rowData['loan'])
				// ->setCellValue('B'. $rows, $rowData['cust_id'])
				->setCellValue('C' . $rows, $rowData['client_name'])
				->setCellValue('E' . $rows, number_format($rowData['int_amt'],2))
				->setCellValue('F' . $rows, Carbon::parse($rowData['deduction_date'])->format('d-m-Y'))
				->setCellValue('D' . $rows, Carbon::parse($rowData['trans_date'])->format('d-m-Y'))
				->setCellValue('G' . $rows, number_format($rowData['tds_amt'],2))
				->setCellValue('H' . $rows, $rowData['tds_certificate'])
				->setCellValue('I' . $rows, $rowData['tally_batch']);
			$rows++;
		}
		
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Tds Breakup Report.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
		$objWriter->save('php://output');
	}
	
	public function downloadLeaseReport(Request $request) {
		ini_set("memory_limit", "-1");
		$whereRaw = '';
		$userInfo = '';
		if(!empty($request->get('from_date')) && !empty($request->get('to_date'))){
			$from_date = $request->get('from_date');
			$to_date = $request->get('to_date');
			$cond[] = " rta_user_invoice.invoice_date between '$from_date' AND '$to_date' ";
		}
		if(!empty($request->get('user_id'))){
				$user_id = $request->get('user_id');
				$cond[] = " rta_user_invoice.user_id='$user_id' ";
				$userInfo = $this->reportsRepo->getCustomerDetail($user_id);
		}
		if (!empty($cond)) {
			$whereRaw = implode(' AND ', $cond);
		}
		$leaseRegistersList = $this->reportsRepo->leaseRegisters([], $whereRaw);

		$condArr = [
			'from_date' => $from_date ?? NULL,
			'to_date' => $to_date ?? NULL,
			'user_id' => $request->get('user_id'),
			'userInfo' => $userInfo,
		];
		$moreDetails = [
			'From Date' => $from_date ?? NULL,
			'To Date' => $to_date ?? NULL,
		];
		if (!empty($userInfo)) {
			$moreDetails['Business Name'] = $userInfo->biz->biz_entity_name;
			$moreDetails['Full Name'] = $userInfo->f_name . ' ' . $userInfo->m_name . ' ' . $userInfo->l_name;
			$moreDetails['Email'] = $userInfo->email;
			$moreDetails['Mobile No'] = $userInfo->mobile_no;
		}
		$leaseRecords = $leaseRegistersList->get();
		$leaseArr = [];
		foreach ($leaseRecords as $invoiceRec) {

			$inv_comp_data = json_decode($invoiceRec->inv_comp_data, TRUE);

			$narration = $invoiceRec->trans_name;
			if(in_array($invoiceRec->transTypeId, [config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])){
				$narration .= " for Period " . date('d-M-Y', strtotime($invoiceRec->from_date)) . " To " . date('d-M-Y', strtotime($invoiceRec->to_date));
			}
			elseif(in_array($invoiceRec->transTypeId,[config('lms.TRANS_TYPE.WAVED_OFF')])){
				if($invoiceRec->invoice_cat == 2){
					$narration .= " against $invoiceRec->parent_invoice_no";
				}
			}  
			elseif(in_array($invoiceRec->transTypeId,[config('lms.TRANS_TYPE.REVERSE')])){
				if($invoiceRec->invoice_cat == 2){
					$narration .= " against $invoiceRec->parent_invoice_no";
				}elseif($invoiceRec->invoice_cat == 3){
					$narration .= " against $invoiceRec->link_invoice_no";
				}
			}
			elseif(in_array($invoiceRec->transTypeId,[config('lms.TRANS_TYPE.CANCEL')])){
				if($invoiceRec->invoice_cat == 2){
					$narration .= " against $invoiceRec->parent_invoice_no";
				}elseif($invoiceRec->invoice_cat == 3){
					$narration .= " against $invoiceRec->link_invoice_no";
				}
			}

			$capsaveInvNo = '';
			if($invoiceRec->invoice_cat == 3) {
                $capsaveInvNo = '';
            }else{
                $capsaveInvNo = $invoiceRec->capinvoice;
            }

			$invCat = '';
			if($invoiceRec->invoice_cat == 1){
				$invCat = 'Debit Note';       
			}elseif($invoiceRec->invoice_cat == 2){
				$invCat = 'Credit Note';
			}elseif($invoiceRec->invoice_cat == 3){
				$invCat = 'Credit Note Reversed';
			}

			$gst_applied = 'No';
			if($invoiceRec->sgst_rate > 0 || $invoiceRec->cgst_rate > 0 || $invoiceRec->igst_rate > 0){
				$gst_applied = 'Yes';
			}  
			
			$totalTax = ($invoiceRec->sgst_amount + $invoiceRec->igst_amount + $invoiceRec->cgst_amount);
			
			$totalRate = ($invoiceRec->sgst_rate + $invoiceRec->igst_rate + $invoiceRec->cgst_rate);

			$cashFlowType = '';
			if($invoiceRec->invoice_type_name == 1){
				$cashFlowType = 'Charge';
			}
			elseif($invoiceRec->invoice_type_name == 2){
				$cashFlowType = 'Interest';
			}

			$status = '';
			if($invoiceRec->invoice_cat == 1){
				$status = "Processed";
			}elseif($invoiceRec->transTypeId == config('lms.TRANS_TYPE.REVERSE')){
				$status = "Reversed";
			}elseif($invoiceRec->transTypeId == config('lms.TRANS_TYPE.CANCEL')){
				$status = "Cancelled";
			}elseif($invoiceRec->transTypeId == config('lms.TRANS_TYPE.WAVED_OFF')){
				$status = "Waived Off";
			}

			$leaseArr[] = [
				'State' => $invoiceRec->name,
				'GSTN' => $inv_comp_data['gst_no'] ?? $invoiceRec->biz_gst_no,
				'Cust. Id' => Helpers::formatIdWithPrefix($invoiceRec->user_id, 'LEADID'),
				'Business Name' => $invoiceRec->biz_entity_name,
				'Cust. Addr' => $invoiceRec->gst_addr,
				'Cust. GSTN' => $invoiceRec->biz_gst_no,
				'SAC Code' => $invoiceRec->sac_code != 0 ? $invoiceRec->sac_code : '000',
				'Narration' => $narration,
				'Capsave Invoice No' => $capsaveInvNo,
				'Invoice No' => $invoiceRec->invoice,
				'Invoice Type' => $invCat,
				'Invoice Date' => date('d-m-Y', strtotime($invoiceRec->invoice_date)),
				'Base Amount' => number_format($invoiceRec->base_amount, 2),
				'GST Applicable' => $gst_applied,
				'SGST Rate' => $invoiceRec->sgst_rate != 0 ? $invoiceRec->sgst_rate . '%' : '-',
				'SGST Amount' => $invoiceRec->sgst_amount != 0 ? number_format($invoiceRec->sgst_amount, 2) : '-',
				'CGST Rate' => $invoiceRec->cgst_rate != 0 ? $invoiceRec->cgst_rate . '%' : '-',
				'CGST Amount' => $invoiceRec->cgst_amount != 0 ? number_format($invoiceRec->cgst_amount, 2) : '-',
				'IGST Rate' => $invoiceRec->igst_rate != 0 ? $invoiceRec->igst_rate . '%' : '-',
				'IGST Amount' => $invoiceRec->igst_amount != 0 ? number_format($invoiceRec->igst_amount, 2) : '-',
				'Total Amount' => number_format($invoiceRec->base_amount + $invoiceRec->sgst_amount + $invoiceRec->cgst_amount + $invoiceRec->igst_amount, 2),
				'Total Rate' => $totalRate != 0 ? $totalRate . '%' : '-',
				'Total Tax' => $totalTax != 0 ? number_format($totalTax, 2) : '-',
				'CashFlow Type' => $cashFlowType,
				'Considered In' => date('M-Y', strtotime($invoiceRec->invoice_date)),
				'Status' => $status,
			];
		}
		if (strtolower($request->type) == 'excel') {
			$toExportData['Lease Register'] = $leaseArr;
			return $this->fileHelper->array_to_excel($toExportData, 'CFPL_InvRegister.xlsx', $moreDetails);
		}
		$pdfArr = ['pdfArr' => $leaseArr, 'filter' => $condArr];
		$pdf = $this->fileHelper->array_to_pdf($pdfArr, 'reports.leaseRegisterReport');
		return $pdf->download('CFPL_InvRegister.pdf');        
	}
	
	 public function duereport(Request $request) {
		 
		try {
			return view('reports.duereport');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
		
	}
	
	public function outstandingreport(Request $request){
		try {
			return view('reports.outstandingReport');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
	}

	public function overduereport(Request $request) {
		try {
			return view('reports.overduereport');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
		
	}
	 public function realisationreport(Request $request) {
		try {
			return view('reports.realisationreport');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
		
	} 
	public function pdfInvoiceDue(Request $request) {
		$user = LmsUser::where('customer_id',$request->customer_id)->pluck('user_id');
		$user_id = $user[0] ?? '';
		$getInvoice  =  $this->invRepo->pdfInvoiceDue($request);
		$condArr = [
			'from_date' => $request->from_date,
			'to_date' => $request->to_date,
			'user_id' => $user_id,
			'userInfo' => $this->reportsRepo->getCustomerDetail($user_id) ?? '',
		];
		$moreDetails = [
			'From Date' => $request->from_date,
			'To Date' => $request->to_date,
		];
		if (!empty($condArr['userInfo'])) {
		  $moreDetails['Business Name'] = $condArr['userInfo']->biz->biz_entity_name;
		  $moreDetails['Full Name'] = $condArr['userInfo']->f_name . ' ' . $condArr['userInfo']->m_name . ' ' . $condArr['userInfo']->l_name;
		  $moreDetails['Email'] = $condArr['userInfo']->email;
		  $moreDetails['Mobile No'] = $condArr['userInfo']->mobile_no;
		}
		$duereport = [];
		foreach($getInvoice as $invoice) :
		$duereport[] = [
			'Customer Id' => $invoice->customer_id ??  NULL,
			'Batch No' => $invoice->disbursal->disbursal_batch->batch_id ?? NULL,
			'Batch Date' => date('d/m/Y',strtotime($invoice->disbursal->disbursal_batch->created_at)) ?? NULL,
			'Bill No' => $invoice->invoice->invoice_no ?? NULL,
			'Bill Date' => Carbon::parse($invoice->invoice->invoice_date)->format('d/m/Y') ?? NULL,
			'Due Date' => Carbon::parse($invoice->payment_due_date)->format('d/m/Y') ?? NULL,
			'Bill Amount' => number_format($invoice->invoice->invoice_amount ?? 0),
			'Approve Amount' => number_format($invoice->invoice->invoice_approve_amount ?? 0),
			'Balance' => number_format($invoice->invoice->invoice_approve_amount ?? 0),
		];
		endforeach;
		if (strtolower($request->type) == 'excel') {
		   $toExportData['Invoice Due'] = $duereport;
		   return $this->fileHelper->array_to_excel($toExportData, 'InvoiceDueReport.xlsx', $moreDetails);
		}
		$pdfArr = ['filter' => $condArr, 'userInfo' => $getInvoice, 'fromdate' => $request->from_date, 'todate' => $request->to_date,'user' => $user];
		$pdf = $this->fileHelper->array_to_pdf($pdfArr, 'reports.downloadinvoicedue');
		return $pdf->download('InvoiceDueReport.pdf');  
	}
	
	 public function pdfInvoiceOverDue(Request $request) {
		$user = LmsUser::where('customer_id',$request->customer_id)->pluck('user_id');
		$user_id = $user[0] ?? '';
		$getInvoice  =  $this->invRepo->pdfInvoiceOverDue($request);
		$condArr = [
			'from_date' => $request->from_date,
			'to_date' => $request->to_date,
			'user_id' => $user_id,
			'userInfo' => $this->reportsRepo->getCustomerDetail($user_id) ?? '',
		];
		$moreDetails = [
			'From Date' => $request->from_date,
			'To Date' => $request->to_date,
		];
		if (!empty($condArr['userInfo'])) {
		  $moreDetails['Business Name'] = $condArr['userInfo']->biz->biz_entity_name;
		  $moreDetails['Full Name'] = $condArr['userInfo']->f_name . ' ' . $condArr['userInfo']->m_name . ' ' . $condArr['userInfo']->l_name;
		  $moreDetails['Email'] = $condArr['userInfo']->email;
		  $moreDetails['Mobile No'] = $condArr['userInfo']->mobile_no;
		}
		$overduereport = [];
		foreach($getInvoice as $invoice) :
		$overduereport[] = [
			'Customer Id' => $invoice->customer_id ??  NULL,
			'Batch No' => $invoice->disbursal->disbursal_batch->batch_id ?? NULL,
			'Batch Date' => date('d/m/Y',strtotime($invoice->disbursal->disbursal_batch->created_at)) ?? NULL,
			'Bill No' => $invoice->invoice->invoice_no ?? NULL,
			'Bill Date' => Carbon::parse($invoice->invoice->invoice_date)->format('d/m/Y') ?? NULL,
			'Due Date' => Carbon::parse($invoice->payment_due_date)->format('d/m/Y') ?? NULL,
			'Bill Amount' => number_format($invoice->invoice->invoice_amount ?? 0),
			'Approve Amount' => number_format($invoice->invoice->invoice_approve_amount ?? 0),
			'Days OD' => $invoice->InterestAccrual->count() ?? 0,
			'Balance' => number_format($invoice->invoice->invoice_approve_amount ?? 0),
		   
		];
		endforeach;
		if (strtolower($request->type) == 'excel') {
		   $toExportData['Invoice OverDue'] = $overduereport;
		   return $this->fileHelper->array_to_excel($toExportData, 'InvoiceOverDueReport.xlsx', $moreDetails);
		}
		$pdfArr = ['filter' => $condArr, 'userInfo' => $getInvoice, 'fromdate' => $request->from_date, 'todate' => $request->to_date,'user' => $user];
		$pdf = $this->fileHelper->array_to_pdf($pdfArr, 'reports.downloadinvoiceoverdue');
		return $pdf->download('InvoiceOverDueReport.pdf');  
	}
	
   public function pdfInvoiceRealisation(Request $request)
   {
		$user = LmsUser::where('customer_id',$request->customer_id)->pluck('user_id');
		$user_id = $user[0] ?? '';
		$getInvoice  =  $this->invRepo->pdfInvoiceRealisation($request);
		$condArr = [
			'from_date' => $request->from_date,
			'to_date' => $request->to_date,
			'user_id' => $user_id,
			'userInfo' => $this->reportsRepo->getCustomerDetail($user_id) ?? '',
		];
		$moreDetails = [
			'From Date' => $request->from_date,
			'To Date' => $request->to_date,
		];
		if (!empty($condArr['userInfo'])) {
		  $moreDetails['Business Name'] = $condArr['userInfo']->biz->biz_entity_name;
		  $moreDetails['Full Name'] = $condArr['userInfo']->f_name . ' ' . $condArr['userInfo']->m_name . ' ' . $condArr['userInfo']->l_name;
		  $moreDetails['Email'] = $condArr['userInfo']->email;
		  $moreDetails['Mobile No'] = $condArr['userInfo']->mobile_no;
		}
		$realisationreport = [];
		foreach($getInvoice as $invoice) :
		$payment  = [];                   
		$chk  = [];                   
		foreach($invoice->transaction as $row) {
		   if(!empty($row->payment->date_of_payment)) {
			 $payment[] = Carbon::parse($row->payment->date_of_payment)->format('d/m/Y');
		   }
		   if (!empty($row->payment->utr_no)) {
		   		$chk_no = $row->payment->utr_no;
		   }
		   if (!empty($row->payment->unr_no)) {
		   		$chk_no = $row->payment->unr_no;
		   }
		   if (!empty($row->payment->cheque_no)) {
		   		$chk_no = $row->payment->cheque_no;
		   }
		   $chk[] =  $chk_no ?? '';
		}
		$realisationOnDate = implode(', ', $payment);
		$cheque = implode(', ', $chk);
		$realisationreport[] = [
			'Customer Id' => $invoice->customer_id ??  NULL,
			'Debtor Name' => $invoice->invoice->anchor->comp_name ?? NULL,
			'Debtor Invoice Acc. No.' => $invoice->Invoice->anchor->anchorAccount->acc_no ?? NULL,
			'Invoice Date' => Carbon::parse($invoice->invoice->invoice_date)->format('d/m/Y') ?? NULL,
			'Invoice Due Amount' => number_format($invoice->invoice->invoice_approve_amount ?? 0),
			'Invoice Due Amount Date' => Carbon::parse($invoice->payment_due_date)->format('d/m/Y') ?? NULL,
			'Grace Period' => $invoice->grace_period ?? NULL,
			'Realisation on Date' => $realisationOnDate,
			'Realisation Amount' => number_format($invoice->invoice->invoice_approve_amount ?? 0),
			'OD/OP Days' => $invoice->InterestAccrual->count() ?? 0,
			'Cheque' => $cheque,
			'Business Name' => $invoice->invoice->business->biz_entity_name ?? NULL,
		];
		endforeach;
		if (strtolower($request->type) == 'excel') {
		   $toExportData['Invoice Realisation'] = $realisationreport;
		   return $this->fileHelper->array_to_excel($toExportData, 'InvoiceRealisation.xlsx', $moreDetails);
		}
		$pdfArr = ['filter' => $condArr, 'userInfo' => $getInvoice, 'fromdate' => $request->from_date, 'todate' => $request->to_date,'user' => $user];
		$pdf = $this->fileHelper->array_to_pdf($pdfArr, 'reports.downloadrealisation');
		return $pdf->download('InvoiceRealisation.pdf');    
   }
   
   /**
    * TDS report listing
    * 
    * @param Request $request
    * @return type
    */
    public function tdsReport(Request $request) {
        try {
            return view('reports.tds');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * Download TDS report pdf/xlxs
     * 
     * @param Request $request
     * @return type
     */
    public function downloadTdsReport(Request $request) {
       $whereRaw = '';
       if(!empty($request->get('user_id'))){
            $user_id = $request->get('user_id');
            $cond[] = " rta_payments.user_id='$user_id' ";
       }
       if (!empty($cond)) {
           $whereRaw = implode(' AND ', $cond);
       }
       $tdsList = $this->reportsRepo->tds([], $whereRaw);
       $condArr = [
            'user_id' => $request->get('user_id'),
        ];
       $tdsRecords = $tdsList->get();
       $tdsArr = [];
       foreach ($tdsRecords as $tds) {
         $user_id = $tds->user_id != 0 ? \Helpers::formatIdWithPrefix($tds->user_id, 'LEADID') : '';   
         $customer_name = $tds->biz_entity_name ? $tds->biz_entity_name : '';  
         $trans_name =  $tds->trans_name ? $tds->trans_name : '';
         $date_of_payment =  $tds->date_of_payment ? $tds->date_of_payment : '';
         $trans_date = $tds->trans_date ? date('d-m-Y', strtotime($tds->trans_date)) : '';
         $amount =  $tds->amount ? $tds->amount : '';
         $trans_by = $tds->f_name.' '.$tds->l_name;
         $tds_certificate_no = $tds->tds_certificate_no;
         $file_id = $tds->file_id == 0 ? 'N' : '';
         $tdsArr[] = [
            'customer_id' => $user_id,
            'customer_name' => $customer_name,
            'transaction_type' => $trans_name,
            'date_of_payment' => $date_of_payment,
            'transaction_date' => $trans_date,
            'Transaction_amount' => $amount,
            'transaction_by' => $trans_by,
            'tds_certificate_no' => $tds_certificate_no,
            'file_id' => $file_id,
         ];
       }
       if (strtolower($request->type) == 'excel') {
           $toExportData['TDS'] = $tdsArr;
           return $this->fileHelper->array_to_excel($toExportData, 'tdsReport.xlsx');
       }
       $pdfArr = ['pdfArr' => $tdsArr, 'filter' => $condArr];
       $pdf = $this->fileHelper->array_to_pdf($pdfArr, 'reports.tdsReport');
       return $pdf->download('tdsReport.pdf');        
    }

    public function maturityReport(){
        ini_set("memory_limit", "-1");
        dump('start....');
		$anchor_id = null;
        $emailTo =  config('lms.DAILY_REPORT_MAIL');
        if(empty($emailTo)){
            dd('DAILY_REPORT_MAIL is missing');
        }
		array_push($emailTo,"sudesh.kumar@zuron.in");
		
        $anchorList = Anchor::where('is_active','1');
        if($anchor_id){
            $anchorList->where('anchor_id',$anchor_id);
        }
        $anchorList = $anchorList->get();
		
        $sendMail = false;
        $data = $this->reportsRepo->getMaturityReport([],$sendMail);
        if($sendMail){
            $filePath = $this->downloadMaturityReport($data);
            $emailData['email'] = $emailTo;
            $emailData['name'] = 'Capsave Team';
            $emailData['body'] = 'PFA';
            $emailData['attachment'] = $filePath;
            $emailData['subject'] ="Maturity Report";
            \Event::dispatch("NOTIFY_MATURITY_REPORT", serialize($emailData));
            
            foreach($anchorList as $anchor){
                $sendMail = false;
                $data = $this->reportsRepo->getMaturityReport(['anchor_id'=>$anchor->anchor_id],$sendMail);
                if($sendMail && $anchor->comp_email){
                    $filePath = $this->downloadMaturityReport($data);
                    //$emailData['email'] = $anchor->comp_email;
                    $emailData['email'] = $emailTo;
                    $emailData['name'] = $anchor->comp_name;
                    $emailData['body'] = 'PFA';
                    $emailData['attachment'] = $filePath;
                    $emailData['subject'] ="Maturity Report (".$anchor->comp_name.")";
                    \Event::dispatch("NOTIFY_MATURITY_REPORT", serialize($emailData));
                }
            }
        }

        $sendMail = false;
        $data = $this->reportsRepo->getUtilizationReport( [],$sendMail);
        if($sendMail){
            $filePath = $this->downloadUtilizationExcel($data);
            //$emailData['email'] = $anchor->comp_email;
            $emailData['email'] = $emailTo;
            $emailData['name'] = 'Capsave Team';
            $emailData['body'] = 'PFA';
            $emailData['attachment'] = $filePath;
            $emailData['subject'] ="Utilization Report";
            \Event::dispatch("NOTIFY_UTILIZATION_REPORT", serialize($emailData));
			         
            foreach($anchorList as $anchor){
                $sendMail = false;
                $data = $this->reportsRepo->getUtilizationReport( ['anchor_id'=>$anchor->anchor_id],$sendMail);
                if($sendMail && $anchor->comp_email){
                    $filePath = $this->downloadUtilizationExcel($data);
                    //$emailData['email'] = $anchor->comp_email;
                    $emailData['email'] = $emailTo;
                    $emailData['name'] = $anchor->comp_name;
                    $emailData['body'] = 'PFA';
                    $emailData['attachment'] = $filePath;
                    $emailData['subject'] ="Utilization Report (".$anchor->comp_name.")";
                    \Event::dispatch("NOTIFY_UTILIZATION_REPORT", serialize($emailData));
                }
            }
        }        

        $sendMail = false;
        $data = $this->reportsRepo->getDisbursalReport([],$sendMail);
        if($sendMail){
            $filePath = $this->downloadDailyDisbursalReport($data);
            //$emailData['email'] = $anchor->comp_email;
            $emailData['email'] = $emailTo;
            $emailData['name'] = 'Capsave Team';
            $emailData['body'] = 'PFA';
            $emailData['attachment'] = $filePath;
            $emailData['subject'] ="Disbursal Report";
            \Event::dispatch("NOTIFY_DISBURSAL_REPORT", serialize($emailData));
            
            foreach($anchorList as $anchor){
                $sendMail = false;
                $data = $this->reportsRepo->getDisbursalReport(['anchor_id'=>$anchor->anchor_id],$sendMail);
                if($sendMail && $anchor->comp_email){
                    $filePath = $this->downloadDailyDisbursalReport($data);
                    //$emailData['email'] = $anchor->comp_email;
                    $emailData['email'] = $emailTo;
                    $emailData['name'] = $anchor->comp_name;
                    $emailData['body'] = 'PFA';
                    $emailData['attachment'] = $filePath;
                    $emailData['subject'] ="Disbursal Report (".$anchor->comp_name.")";
                    \Event::dispatch("NOTIFY_DISBURSAL_REPORT", serialize($emailData));
                }
            }
		}
		
        $sendMail = false;
        $data = $this->reportsRepo->getOverdueReport([],$sendMail);
        if($sendMail){
            $filePath = $this->downloadOverdueReport($data);
            //$emailData['email'] = $anchor->comp_email;
            $emailData['email'] = $emailTo;
            $emailData['name'] = 'Capsave Team';
            $emailData['body'] = 'PFA';
            $emailData['attachment'] = $filePath;
            $emailData['subject'] ="Overdue Report";
            \Event::dispatch("NOTIFY_OVERDUE_REPORT", serialize($emailData));
        }
        
        $sendMail = false;
        $data = $this->reportsRepo->getAccountDisbursalReport([],$sendMail);
        if($sendMail){
            $filePath = $this->downloadAccountDailyDisbursalReport($data);
            //$emailData['email'] = $anchor->comp_email;
            $emailData['email'] = $emailTo;
            $emailData['name'] = 'Capsave Team';
            $emailData['body'] = 'PFA';
            $emailData['attachment'] = $filePath;
            $emailData['subject'] ="Disbursal Report";
            \Event::dispatch("NOTIFY_ACCOUNT_DISBURSAL_REPORT", serialize($emailData));
        }
        
        dump('end....');
    }

    public function downloadMaturityReport($exceldata){
        $rows = 5;
        $sheet =  new PHPExcel();
        $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Customer Name')
            ->setCellValue('B'.$rows, 'Loan Account #')
            ->setCellValue('C'.$rows, 'Virtual Account #')
            ->setCellValue('D'.$rows, 'Transction Date')
            ->setCellValue('E'.$rows, 'Tranction #')
            ->setCellValue('F'.$rows, 'Invoice #')
            ->setCellValue('G'.$rows, 'Invoice Date')
            ->setCellValue('H'.$rows, 'Invoice Amount')
            ->setCellValue('I'.$rows, 'Margin Amount')
            ->setCellValue('J'.$rows, 'Amount Disbursed')
            ->setCellValue('K'.$rows, 'O/s Amount')
            ->setCellValue('L'.$rows, 'O/s Days')
            ->setCellValue('M'.$rows, 'Credit Period')
            ->setCellValue('N'.$rows, 'Maturity Date (Due Date)')
            ->setCellValue('O'.$rows, 'Maturity Amount')
            ->setCellValue('P'.$rows, 'Over Due Days')
            ->setCellValue('Q'.$rows, 'Overdue Amount')
            ->setCellValue('R'.$rows, 'Remark while uploading Invoice');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':R'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, $rowData['cust_name'])
            ->setCellValue('B'.$rows, $rowData['loan_ac'])
            ->setCellValue('C'.$rows, $rowData['virtual_ac'])
            ->setCellValue('D'.$rows, Carbon::parse($rowData['trans_date'])->format('d-m-Y'))
            ->setCellValue('E'.$rows, $rowData['trans_no'])
            ->setCellValue('F'.$rows, $rowData['invoice_no'])
            ->setCellValue('G'.$rows, Carbon::parse($rowData['invoice_date'])->format('d-m-Y'))
            ->setCellValue('H'.$rows, number_format($rowData['invoice_amt'],2))
            ->setCellValue('I'.$rows, number_format($rowData['margin_amt'],2))
            ->setCellValue('J'.$rows, number_format($rowData['disb_amt'],2))
            ->setCellValue('K'.$rows, number_format($rowData['out_amt'],2))
            ->setCellValue('L'.$rows, $rowData['out_days'])
            ->setCellValue('M'.$rows, $rowData['tenor'])
            ->setCellValue('N'.$rows, Carbon::parse($rowData['due_date'])->format('d-m-Y'))
            ->setCellValue('O'.$rows, number_format($rowData['due_amt'],2))
            ->setCellValue('P'.$rows, $rowData['od_days'])
            ->setCellValue('Q'.$rows, number_format($rowData['od_amt'],2))
            ->setCellValue('R'.$rows, $rowData['remark']); 
            $rows++;
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        
        $dirPath = 'public/report/temp/maturityReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.'/Maturity Report'.time().'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }

    public function downloadDailyDisbursalReport($exceldata){
        $rows = 5;
        $sheet =  new PHPExcel();
        $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Borrower name')
            ->setCellValue('B'.$rows, 'RM')
            ->setCellValue('C'.$rows, 'Anchor name')
            ->setCellValue('D'.$rows, 'Anchor program name')
            ->setCellValue('E'.$rows, 'Vendor/Beneficiary Name')
            ->setCellValue('F'.$rows, 'Region')
            ->setCellValue('G'.$rows, 'Sanction no.')
            ->setCellValue('H'.$rows, 'Sanction date')
            ->setCellValue('I'.$rows, 'Sanction Amount')
            ->setCellValue('J'.$rows, 'Status')
            ->setCellValue('K'.$rows, 'Disbrusal Month')
            ->setCellValue('L'.$rows, 'Disburse amount')
            ->setCellValue('M'.$rows, 'Disbursement date')
            ->setCellValue('N'.$rows, 'Disbursal UTR No')
            ->setCellValue('O'.$rows, 'Disbursal Act No')
            ->setCellValue('P'.$rows, 'Disbursal IFSC Code')
            ->setCellValue('Q'.$rows, 'Type of Finance')
            ->setCellValue('R'.$rows, 'Supply chain type (upfront, Rare or monthly interest)')
            ->setCellValue('S'.$rows, 'Tenure (Days)')
            ->setCellValue('T'.$rows, 'Interest rate')
            ->setCellValue('U'.$rows, 'Interest amount')
            ->setCellValue('V'.$rows, 'From ')
            ->setCellValue('W'.$rows, 'To')
            ->setCellValue('X'.$rows, 'TDS on Interest')
            ->setCellValue('Y'.$rows, 'Net Interest')
            ->setCellValue('Z'.$rows, 'Interest received date')
            ->setCellValue('AA'.$rows, 'Processing fees')
            ->setCellValue('AB'.$rows, 'Processing amount')
            ->setCellValue('AC'.$rows, 'Processing fee with GST')
            ->setCellValue('AD'.$rows, 'TDS on Processing fee')
            ->setCellValue('AE'.$rows, 'Net Processing fee receivable')
            ->setCellValue('AF'.$rows, 'Processing fee received')
            ->setCellValue('AG'.$rows, 'Processing Fee Amount received date')
            ->setCellValue('AH'.$rows, 'Balance')
            ->setCellValue('AI'.$rows, 'Margin')
            ->setCellValue('AJ'.$rows, 'Due date')
            ->setCellValue('AK'.$rows, 'Funds to be received from Anchor or client')
            ->setCellValue('AL'.$rows, 'Principal receivable')
            ->setCellValue('AM'.$rows, 'Received')
            ->setCellValue('AN'.$rows, 'Net Receivable')
            ->setCellValue('AO'.$rows, 'Adhoc interest')
            ->setCellValue('AP'.$rows, 'Net Disbursement')
            ->setCellValue('AQ'.$rows, 'Gross')
            ->setCellValue('AR'.$rows, 'Net of interest, PF & Stamp')
            ->setCellValue('AS'.$rows, 'Interest Borne By')
            ->setCellValue('AT'.$rows, 'Grace Period (Days)')
            ->setCellValue('AU'.$rows, 'Anchor Address');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':AU'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
			
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, $rowData['cust_name'])
            ->setCellValue('B'.$rows, $rowData['rm_sales'])
            ->setCellValue('C'.$rows,$rowData['anchor_name'])
            ->setCellValue('D'.$rows, $rowData['anchor_prgm_name'])
            ->setCellValue('E'.$rows, $rowData['vendor_ben_name'])
            ->setCellValue('F'.$rows, $rowData['region'])
            ->setCellValue('G'.$rows, $rowData['sanction_number'])
            ->setCellValue('H'.$rows, Carbon::parse($rowData['sanction_date'])->format('d-m-Y') ?? NULL)
            ->setCellValue('I'.$rows, number_format($rowData['sanction_amount'],2))
            ->setCellValue('J'.$rows, !empty($rowData['status']) ? $rowData['status'] : '---')
            ->setCellValue('K'.$rows, Carbon::parse($rowData['disbursal_month'])->format('F') ?? NULL)
            ->setCellValue('L'.$rows, !empty($rowData['disburse_amount']) ? number_format($rowData['disburse_amount'],2) : '')
            ->setCellValue('M'.$rows, Carbon::parse($rowData['disbursement_date'])->format('d-m-Y') ?? NULL)
            ->setCellValue('N'.$rows, $rowData['disbursal_utr'])
            ->setCellValue('O'.$rows, $rowData['disbursal_act_no'])
            ->setCellValue('P'.$rows, $rowData['disbursal_ifc'])
            ->setCellValue('Q'.$rows, $rowData['type_finance'])
            ->setCellValue('R'.$rows, $rowData['supl_chan_type'])
            ->setCellValue('S'.$rows, $rowData['tenor'])
            ->setCellValue('T'.$rows, $rowData['interest_rate'])
            ->setCellValue('U'.$rows, number_format($rowData['interest_amt'],2))
            ->setCellValue('V'.$rows, Carbon::parse($rowData['from'])->format('d-m-Y') ?? NULL)
            ->setCellValue('W'.$rows, Carbon::parse($rowData['to'])->format('d-m-Y') ?? NULL)
            ->setCellValue('X'.$rows, number_format($rowData['tds_intrst'],2))
            ->setCellValue('Y'.$rows, number_format($rowData['net_intrst'],2))
            ->setCellValue('Z'.$rows, !empty($rowData['intrst_rec_date']) ? Carbon::parse($rowData['intrst_rec_date'])->format('d-m-Y') : '---')
            ->setCellValue('AA'.$rows, number_format($rowData['proce_fee'],2))
            ->setCellValue('AB'.$rows, number_format($rowData['proce_amt'],2))
            ->setCellValue('AC'.$rows, number_format($rowData['proce_fee_gst'],2))
            ->setCellValue('AD'.$rows, number_format($rowData['tds_proce_fee'],2))
            ->setCellValue('AE'.$rows, number_format($rowData['net_proc_fee_rec'],2))
            ->setCellValue('AF'.$rows, number_format($rowData['proce_fee_rec'],2))
            ->setCellValue('AG'.$rows, !empty($rowData['proce_fee_amt_date']) ? ($rowData['proce_fee_amt_date']) : '---')
            ->setCellValue('AH'.$rows, number_format($rowData['balance'],2))
            ->setCellValue('AI'.$rows, number_format($rowData['margin_amt'],2))
            ->setCellValue('AJ'.$rows, Carbon::parse($rowData['due_date'])->format('d-m-Y') ?? NULL)
            ->setCellValue('AK'.$rows, !empty($rowData['funds_received']) ? $rowData['funds_received'] : '---')
            ->setCellValue('AL'.$rows, number_format($rowData['principal_rece'],2))
            ->setCellValue('AM'.$rows, number_format($rowData['received'],2))
            ->setCellValue('AN'.$rows, number_format($rowData['net_receivalble'],2))
            ->setCellValue('AO'.$rows, '---')
            ->setCellValue('AP'.$rows, number_format($rowData['net_disbursement'],2))
            ->setCellValue('AQ'.$rows, !empty($rowData['gross']) ? $rowData['gross'] : '---')
            ->setCellValue('AR'.$rows, !empty($rowData['net_of_interest']) ? $rowData['net_of_interest'] : '---')
            ->setCellValue('AS'.$rows, !empty($rowData['interest_borne_by']) ? $rowData['interest_borne_by'] : '---')
            ->setCellValue('AT'.$rows, !empty($rowData['grace_period']) ? $rowData['grace_period'] : '---')
            ->setCellValue('AU'.$rows, !empty($rowData['anchor_address']) ? $rowData['anchor_address'] : '---');
            $rows++;
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');

        $dirPath = 'public/report/temp/dailyDisbursalReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.'/Daily Disbursal Report'.time().'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }

    public function downloadUtilizationExcel($exceldata) {
    
        $rows = 5;

        $sheet =  new PHPExcel();
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Anchor Name')
            ->setCellValue('B'.$rows, 'Program Name')
            ->setCellValue('C'.$rows, 'Sub Program Name')
            ->setCellValue('D'.$rows, '# of Clients sanctioned')
            ->setCellValue('E'.$rows, '# of Overdue Customers')
            ->setCellValue('F'.$rows, 'Total Over Due Amount');
            $sheet->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
            $rows++;

            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $rows, $rowData['anchor_name'])
            ->setCellValue('B' . $rows, $rowData['prgm_name'])
            ->setCellValue('C' . $rows, $rowData['sub_prgm_name'])
            ->setCellValue('D' . $rows, $rowData['client_sanction'])
            ->setCellValue('E' . $rows, $rowData['ttl_od_customer'])
            ->setCellValue('F' . $rows, number_format($rowData['ttl_od_amt'],2)); 
            $rows++;
            $rows++;
            if(!empty($rowData['disbursement'])){
                foreach($rowData['disbursement'] as $disb){
                    $rows++;
                    $sheet->setActiveSheetIndex(0)
                    ->setCellValue('A'.$rows, 'Client Name')
                    ->setCellValue('B'.$rows, 'Customer ID')
                    ->setCellValue('C'.$rows, 'Virtual Account #')
                    ->setCellValue('D'.$rows, 'Client Sanction Limit')
                    ->setCellValue('E'.$rows, 'Limit Utilized Limit')
                    ->setCellValue('F'.$rows, 'Available Limit')
                    ->setCellValue('G'.$rows, 'Expiry Date')
                    ->setCellValue('H'.$rows, 'Sales Person Name')
                    ->setCellValue('I'.$rows, 'Sub Program Name')
					->setCellValue('J'.$rows, 'Anchor Name');
                    $sheet->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
                    $rows++;
                    $sheet->setActiveSheetIndex(0)
                    ->setCellValue('A'.$rows, $disb['client_name'])
                    ->setCellValue('B'.$rows, $disb['user_id'])
                    ->setCellValue('C'.$rows, $disb['virtual_ac'])
                    ->setCellValue('D'.$rows, number_format($disb['client_sanction_limit'],2))
                    ->setCellValue('E'.$rows, number_format($disb['limit_utilize'],2))
                    ->setCellValue('F'.$rows, number_format($disb['limit_available'],2))
                    ->setCellValue('G'.$rows, Carbon::parse($disb['end_date'])->format('d/m/Y') ?? NULL)
                    ->setCellValue('H'.$rows, $disb['sales_person_name'])
                    ->setCellValue('I'.$rows, $disb['sub_prgm_name'])
					->setCellValue('J'.$rows, $rowData['anchor_name']);
                    $rows++;
                    $rows++;
                    if(!empty($disb['invoice'])){
                        $sheet->setActiveSheetIndex(0)
                        ->setCellValue('B'.$rows,'Invoice #')
                        ->setCellValue('C'.$rows,'Invoice Date')
                        ->setCellValue('D'.$rows,'Invoice Amount')
						->setCellValue('E'.$rows,'Invoice Approved')
                        ->setCellValue('F'.$rows,'Margin Amount')
                        ->setCellValue('G'.$rows,'Amount Disbursed')
                        ->setCellValue('H'.$rows,'Principal OverDue Days')
						->setCellValue('I'.$rows,'Principal OverDue Amount')
						->setCellValue('J'.$rows,'Over Due Days')
                        ->setCellValue('K'.$rows,'Over Due Interest Amount');

                        $sheet->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
                        $rows++;

                        foreach($disb['invoice'] as $inv){
                            $sheet->setActiveSheetIndex(0)
                            ->setCellValue('B'.$rows,$inv['invoice_no'])
                            ->setCellValue('C'.$rows,Carbon::parse($inv['invoice_date'])->format('d/m/Y') ?? NULL)
                            ->setCellValue('D'.$rows,number_format($inv['invoice_amt'],2))
							->setCellValue('E'.$rows,number_format($inv['approve_amt'],2))
                            ->setCellValue('F'.$rows,number_format($inv['margin_amt'],2))
                            ->setCellValue('G'.$rows,number_format($inv['disb_amt'],2))
							->setCellValue('H'.$rows,$inv['principal_od_days'])
							->setCellValue('I'.$rows,number_format($inv['principal_od_amount'],2))
                            ->setCellValue('J'.$rows,$inv['od_days'])
                            ->setCellValue('K'.$rows,number_format($inv['od_amt'],2));
                            $rows++;
                        }
                    }
                }
            }
            $rows++;
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        
        $dirPath = 'public/report/temp/utilizationReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.'/Utilization Report'.time().'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }

    public function downloadOverdueReport($exceldata){
        $rows = 5;
        $sheet =  new PHPExcel();
        $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Customer Name')
            ->setCellValue('B'.$rows, 'Customer ID')
			//->setCellValue('C'.$rows, 'Program Name')
            //->setCellValue('D'.$rows, 'Sub Program Name')
            ->setCellValue('C'.$rows, 'Virtual Account #')
            ->setCellValue('D'.$rows, 'Sanction Limit')
            ->setCellValue('E'.$rows, 'Limit Available')
            ->setCellValue('F'.$rows, 'O/s Amount')
            ->setCellValue('G'.$rows, 'Over Due Days')
            ->setCellValue('H'.$rows, 'Overdue Amount')
            ->setCellValue('I'.$rows, 'Sales Person Name');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':I'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, $rowData['cust_name'])
            ->setCellValue('B'.$rows, $rowData['customer_id'])
			//->setCellValue('C'.$rows, $rowData['prgm_name'])
			//->setCellValue('D'.$rows, $rowData['sub_prgm_name'])
            ->setCellValue('C'.$rows, $rowData['virtual_ac'])
            ->setCellValue('D'.$rows, number_format($rowData['client_sanction_limit'],2))
            ->setCellValue('E'.$rows, number_format($rowData['limit_available'],2))
            ->setCellValue('F'.$rows, number_format($rowData['out_amt'],2))
            ->setCellValue('G'.$rows, $rowData['od_days'])
            ->setCellValue('H'.$rows, number_format($rowData['od_amt'],2))
            ->setCellValue('I'.$rows, $rowData['sales_person_name']);
            $rows++;
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        
        $dirPath = 'public/report/temp/overdueReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.'/Overdue Report'.time().'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }

    public function downloadAccountDailyDisbursalReport($exceldata){
        $rows = 5;
        $sheet =  new PHPExcel();
        $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Customer Name')
            ->setCellValue('B'.$rows, 'Loan Account #')
            ->setCellValue('C'.$rows, 'Transction Date')
            ->setCellValue('D'.$rows, 'tranction #')
            ->setCellValue('E'.$rows, 'Invoice #')
            ->setCellValue('F'.$rows, 'Invoice Date')
            ->setCellValue('G'.$rows, 'Invoice Amount')
            ->setCellValue('H'.$rows, 'Margin Amount')
            ->setCellValue('I'.$rows, 'Amount Disbrused')
            ->setCellValue('J'.$rows, 'UTR')
            ->setCellValue('K'.$rows, 'Remark while uploading Invoice')
            ->setCellValue('L'.$rows, 'Beneficiary Credit Account No.')	
            ->setCellValue('M'.$rows, 'Beneficiary IFSC Code')
            ->setCellValue('N'.$rows, 'Status')	
            ->setCellValue('O'.$rows, 'Status Description');

        $sheet->getActiveSheet()->getStyle('A'.$rows.':O'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, $rowData['cust_name'])
            ->setCellValue('B'.$rows, $rowData['loan_ac'])
            ->setCellValue('C'.$rows, Carbon::parse($rowData['trans_date'])->format('d-m-Y'))
            ->setCellValue('D'.$rows, $rowData['trans_no'])
            ->setCellValue('E'.$rows, $rowData['invoice_no'])
            ->setCellValue('F'.$rows, Carbon::parse($rowData['invoice_date'])->format('d-m-Y'))
            ->setCellValue('G'.$rows, number_format($rowData['invoice_amt'],2))
            ->setCellValue('H'.$rows, number_format($rowData['margin_amt'],2))
            ->setCellValue('I'.$rows, number_format($rowData['disb_amt'],2))
            ->setCellValue('J'.$rows, $rowData['trans_utr'])
            ->setCellValue('K'.$rows, $rowData['remark'])
            ->setCellValue('L'.$rows, $rowData['bank_ac'])
            ->setCellValue('M'.$rows, $rowData['ifsc'])
            ->setCellValue('N'.$rows, $rowData['status'])
            ->setCellValue('O'.$rows, $rowData['status_des']);
            $rows++;
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');

        $dirPath = 'public/report/temp/accountDailyDisbursalReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.'/Account Daily Disbursal Report'.time().'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }

	/**
	 * Alert to anchor before 7 days to complete maturity
	 * Email alert
	 */
	public function maturityAlertReport() 
	{
		set_time_limit(10000);
        ini_set("memory_limit", "-1");
		try {
			$anchor_id = null;
			$anchorNameList = array();
			$emailIds = array();
			// $anchorList = Anchor::where('is_active','1')->get();
			$userList = LmsUser::get();
			$invoiceFound = false;
			foreach($userList as $key=>$user){
				$data = $this->invRepo->getMaturityData($user->user_id);
				$userNameList[$key] = $user->user->f_name.' '.$user->user->l_name;
				if(!empty($data) && $user->user->email){
					$invoiceFound = true;
					$emailData = array(
						'user_name' => $user->user->f_name.' '.$user->user->l_name,
						'email' => $user->user->email,
						'name' => 'Capsave Finance PVT LTD.',
						'subject' => 'subject',
						'body' => 'body',
						'data' => $data,
					);
					$emailIds[] = $user->user->email;
					\Event::dispatch("SUPPLY_CHAIN_INVOICE_DUE_ALERT", serialize($emailData));
				}
			}
			

			if (!$invoiceFound) {
			  return printf('No coming matured Invoice found.' .PHP_EOL);
			}else{
				$this->sendAnStringFromArr($emailIds, 'Maturity Invoice due Alert Email sent to the users');
			}
			return printf(implode(PHP_EOL . '<br />', $anchorNameList));
			
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
	}

	/**
	 * Alert to invoice overdue alert daily
	 * Email alert
	 */
	public function maturityOverdueAlertReport() 
	{
		set_time_limit(10000);
        ini_set("memory_limit", "-1");
		try {
			$anchor_id = null;
			$anchorNameList = array();
			$emailIds = array();
			// $anchorList = Anchor::where('is_active','1')->get();
			$userList = LmsUser::get();
			$invoiceFound = false;
			foreach($userList as $key=>$user){
				$data = $this->invRepo->getMaturityOverdueData($user->user_id);
				// return view('reports.invoice_overdue_alrt')->with('data', $data);
				$userNameList[$key] = $user->user->f_name.' '.$user->user->l_name;
				if(!empty($data) && $user->user->email){
					$invoiceFound = true;
					$emailData = array(
						'user_name' => $user->user->f_name.' '.$user->user->l_name,
						'email' => $user->user->email,
						'name' => 'Capsave Finance PVT LTD.',
						'subject' => 'subject',
						'body' => 'body',
						'data' => $data,
					);
					$emailIds[] = $user->user->email;
					\Event::dispatch("SUPPLY_CHAIN_INVOICE_OVERDUE_ALERT", serialize($emailData));
				}
			}
			if (!$invoiceFound) {
			  return printf('No coming matured Invoice found.' .PHP_EOL);
			}else{
				$this->sendAnStringFromArr($emailIds, 'Maturity Invoice overdue Alert Email sent to the users');
			}
			return printf(implode(PHP_EOL . '<br />', $anchorNameList));
			
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
	}

	public function sendAnStringFromArr($array=[], $subject) {
		$email_content = '';
		if (empty($array) || !is_array($array)) {
			return;
		}
		$mail_body = implode(PHP_EOL, $array);
		$mail_body ='<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; margin: 0 auto; padding: 20px; width:100%;"><tr><td style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">'.$mail_body.'</td></tr></table>';
		$email = $cc = $bcc = [];
		if(!empty(env('CRONINVOICE_SEND_MAIL_TO'))){
			$email = explode(',', env('CRONINVOICE_SEND_MAIL_TO'));
		}
		if(!empty(env('CRONINVOICE_SEND_MAIL_CC_TO'))){
			$cc = explode(',', env('CRONINVOICE_SEND_MAIL_CC_TO'));
		}
		if(!empty(env('CRONINVOICE_SEND_MAIL_BCC_TO'))){
			$bcc = explode(',', env('CRONINVOICE_SEND_MAIL_BCC_TO'));
		}
		if (empty($email) || !is_array($email)) {
			return;
		}
		$to = [
			[
				'email' => $email, //$data["email"]
				'name' => $user['name'] ?? NULL,
			]
		];
		$baseUrl = env('REDIRECT_URL','');
		$mailData = [
			'email_to' => $email, //$data["email"]
			'email_bcc' => $bcc ?? NULL,
			'email_cc' => $cc ?? NULL,
			'mail_subject' => $subject,
			'mail_body' => $mail_body,
			'base_url' => $baseUrl,
		];
		$mailLogData = [
			'email_from' => config('common.FRONTEND_FROM_EMAIL'),
			'email_type' => 'sendAnStringFromArr',
			'name' => $user['name'] ?? NULL,
		];
		// Serialize the data
		$mailDataSerialized = serialize($mailData);
		$mailLogDataSerialized = serialize($mailLogData);
		// Queue the email job
		Mail::to($to)->cc($cc)->bcc($bcc)->queue(new SendEmail($mailDataSerialized, $mailLogDataSerialized));
	}

	public function downloadOverdueReportFromLogs(Request $request)
	{
		$reportLog = OverdueReportLog::findOrfail($request->report_log_id);
		return response()->download($reportLog->file_path);
	}
	
    public function etlReportSync(){
        return $this->reportsRepo->etlReportSync();
    }

	public function outstandingReportManual(Request $request){
		try {
			return view('reports.outstandingReportManual');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
	}

	public function downloadOutstandingReportFromLogs(Request $request)
	{
		$reportLog = OutstandingReportLog::findOrfail($request->report_log_id);
		return response()->download($reportLog->file_path);
	}

	public function reconReport(Request $request){
		try {
			return view('reports.reconReport');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
		}
	}

	public function downloadReconReportFromLogs(Request $request)
	{
		$reportLog = ReconReportLog::findOrfail($request->report_log_id);
		return response()->download($reportLog->file_path);
	}
}
