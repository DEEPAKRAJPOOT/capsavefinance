<?php

namespace App\Http\Controllers\Backend;
use Auth;
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
use App\Inv\Repositories\Contracts\ReportInterface;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;


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
                ->setCellValue('D'.$rows, 'Amount Disbrused (₹)')
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
       $whereRaw = '';
       $userInfo = '';
       if(!empty($request->get('from_date')) && !empty($request->get('to_date'))){
            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');
            $cond[] = " invoice_date between '$from_date' AND '$to_date' ";
       }
       if(!empty($request->get('user_id'))){
            $user_id = $request->get('user_id');
            $cond[] = " user_id='$user_id' ";
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
       foreach ($leaseRecords as $lease) {
         $inv_comp_data = json_decode($lease->inv_comp_data, TRUE);
         $sac_code = ($lease->sac_code != 0 ? $lease->sac_code : '000');   
         $contract_no = 'HEL/'.($lease->sac_code != 0 ? $lease->sac_code : '000');  
         $total_rate =  ($lease->sgst_rate + $lease->cgst_rate + $lease->igst_rate);
         $total_tax =  ($lease->sgst_amount + $lease->cgst_amount + $lease->igst_amount);
         $total_amount =  ($lease->base_amount + $lease->sgst_amount + $lease->cgst_amount + $lease->igst_amount);
         $leaseArr[] = [
            'State' => $lease->name, 
            'GSTN' => ($inv_comp_data['gst_no'] ?? $lease->biz_gst_no), 
            'Cust. Id' =>  \Helpers::formatIdWithPrefix($lease->user_id, 'LEADID'), 
            'Business Name' => $lease->biz_entity_name, 
            'Cust. Addr' => $lease->gst_addr, 
            'Cust. GSTN' => $lease->biz_gst_no, 
            'SAC Code' => $sac_code, 
            // 'Contract No' => $contract_no, 
            'Invoice No' => $lease->invoice_no, 
            'Invoice Date' => $lease->invoice_date, 
            'Base Amount' => number_format($lease->base_amount, 2), 
            'SGST Rate' => ($lease->sgst_rate != 0 ? $lease->sgst_rate .'%' : '-'), 
            'SGST Amount' => ($lease->sgst_amount != 0 ? number_format($lease->sgst_amount, 2) : '-'), 
            'CGST Rate' => ($lease->cgst_rate != 0 ? $lease->cgst_rate .'%' : '-'), 
            'CGST Amount' => ($lease->cgst_amount != 0 ? number_format($lease->cgst_amount, 2) : '-'), 
            'IGST Rate' => ($lease->igst_rate != 0 ? $lease->igst_rate .'%' : '-'), 
            'IGST Amount' => ($lease->igst_amount != 0 ? number_format($lease->igst_amount, 2) : '-'), 
            'Total Amount' => number_format($total_amount, 2), 
            'Total Rate' => ($total_rate != 0 ? $total_rate.'%' : '-'), 
            'Total Tax' => ($total_tax != 0 ? number_format($total_tax, 2) : '-'), 
            'CashFlow Type' => (!empty($lease->invoice_type) && $lease->invoice_type == 'C' ? 'Charge' : 'Interest'), 
            'Considered In' => date('M-Y', strtotime($lease->invoice_date)), 
         ];
       }
       if (strtolower($request->type) == 'excel') {
           $toExportData['Lease Register'] = $leaseArr;
           return $this->fileHelper->array_to_excel($toExportData, 'leaseRegisterReport.xlsx', $moreDetails);
       }
       $pdfArr = ['pdfArr' => $leaseArr, 'filter' => $condArr];
       $pdf = $this->fileHelper->array_to_pdf($pdfArr, 'reports.leaseRegisterReport');
       return $pdf->download('leaseRegisterReport.pdf');        
    }
    
     public function duereport(Request $request) {
         
        try {
            return view('reports.duereport');
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
           if( $row->payment->date_of_payment) {
             $payment[] = Carbon::parse($row->payment->date_of_payment)->format('d/m/Y');
           }
           if (($chk_no = $row->payment->utr_no) || ($chk_no = $row->payment->unr_no) || ($chk_no = $row->payment->cheque_no)) {
              $chk[] =  $chk_no;
           }  
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
            $emailData['name'] = 'Sudesh Kumar';
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
            $emailData['name'] = 'Sudesh Kumar';
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
            $emailData['name'] = 'Sudesh Kumar';
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
            $emailData['name'] = 'Sudesh Kumar';
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
            $emailData['name'] = 'Sudesh Kumar';
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
            ->setCellValue('J'.$rows, 'Amount Disbrused')
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
            ->setCellValue('K'.$rows, 'Remark while uploading Invoice');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
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
            ->setCellValue('K'.$rows, $rowData['remark']);
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
                    ->setCellValue('B'.$rows, 'Loan #')
                    ->setCellValue('C'.$rows, 'Virtual Account #')
                    ->setCellValue('D'.$rows, 'Client Sanction Limit')
                    ->setCellValue('E'.$rows, 'Limit Utilized Limit')
                    ->setCellValue('F'.$rows, 'Available Limit')
                    ->setCellValue('G'.$rows, 'Expiry Date')
                    ->setCellValue('H'.$rows, 'Sales Person Name')
                    ->setCellValue('I'.$rows, 'Sub Program Name');
                    $sheet->getActiveSheet()->getStyle('A'.$rows.':I'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
                    $rows++;
                    $sheet->setActiveSheetIndex(0)
                    ->setCellValue('A'.$rows, $disb['client_name'])
                    ->setCellValue('B'.$rows, $disb['loan_ac'])
                    ->setCellValue('C'.$rows, $disb['virtual_ac'])
                    ->setCellValue('D'.$rows, number_format($disb['client_sanction_limit'],2))
                    ->setCellValue('E'.$rows, number_format($disb['limit_utilize'],2))
                    ->setCellValue('F'.$rows, number_format($disb['limit_available'],2))
                    ->setCellValue('G'.$rows, Carbon::parse($disb['end_date'])->format('d/m/Y') ?? NULL)
                    ->setCellValue('H'.$rows, '')
                    ->setCellValue('I'.$rows, $disb['sub_prgm_name']);
                    $rows++;
                    $rows++;
                    if(!empty($disb['invoice'])){
                        $sheet->setActiveSheetIndex(0)
                        ->setCellValue('B'.$rows,'Invoice #')
                        ->setCellValue('C'.$rows,'Invoice Date')
                        ->setCellValue('D'.$rows,'Invoice Amount')
                        ->setCellValue('E'.$rows,'Margin Amount')
                        ->setCellValue('F'.$rows,'Amount Disbrused')
                        ->setCellValue('G'.$rows,'Over Due Days')
                        ->setCellValue('H'.$rows,'Over Due Amount');
                        $sheet->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
                        $rows++;

                        foreach($disb['invoice'] as $inv){
                            $sheet->setActiveSheetIndex(0)
                            ->setCellValue('B'.$rows,$inv['invoice_no'])
                            ->setCellValue('C'.$rows,Carbon::parse($inv['invoice_date'])->format('d/m/Y') ?? NULL)
                            ->setCellValue('D'.$rows,number_format($inv['invoice_amt'],2))
                            ->setCellValue('E'.$rows,number_format($inv['margin_amt'],2))
                            ->setCellValue('F'.$rows,number_format($inv['disb_amt'],2))
                            ->setCellValue('G'.$rows,$inv['od_days'])
                            ->setCellValue('H'.$rows,number_format($inv['od_amt'],2));
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
            ->setCellValue('B'.$rows, 'Loan Account #')
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
            ->setCellValue('B'.$rows, $rowData['loan_ac'])
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
}