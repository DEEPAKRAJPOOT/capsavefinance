<?php

namespace App\Http\Controllers\Backend;
use Auth;
use Session;
use Helpers;
use PDF as DPDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Contracts\ReportInterface;
use App\Helpers\FileHelper;


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

    public function downloadLeaseReport(Request $request) {
       $whereRaw = '';
       if(!empty($request->get('from_date')) && !empty($request->get('to_date'))){
            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');
            $cond[] = " invoice_date between '$from_date' AND '$to_date' ";
       }
       if(!empty($request->get('user_id'))){
            $user_id = $request->get('user_id');
            $cond[] = " user_id='$user_id' ";
       }
       if (!empty($cond)) {
           $whereRaw = implode(' AND ', $cond);
       }
       $leaseRegistersList = $this->reportsRepo->leaseRegisters([], $whereRaw);
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
            'Customer Name' => $lease->biz_entity_name, 
            'Customer Address' => $lease->gst_addr, 
            'Customer GSTN' => $lease->biz_gst_no, 
            'SAC Code' => $sac_code, 
            'Contract No' => $contract_no, 
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
         ];
       }
       if (strtolower($request->type) == 'excel') {
           $toExportData['Lease Register'] = $leaseArr;
           return $this->fileHelper->array_to_excel($toExportData, 'leaseRegisterReport.xlsx');
       }
       $pdfArr = ['pdfArr' => $leaseArr];
       $pdf = $this->fileHelper->array_to_pdf($pdfArr);
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
        $getInvoice  =  $this->invRepo->pdfInvoiceDue($request);
        $duereport = [];
        foreach($getInvoice as $invoice) :
        $duereport[] = [
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
           return $this->fileHelper->array_to_excel($toExportData, 'InvoiceDueReport.xlsx');
        }
        $pdfArr = ['userInfo' => $getInvoice, 'fromdate' => $request->from_date, 'todate' => $request->to_date,'user' => $user];
        $pdf = $this->fileHelper->array_to_pdf($pdfArr, 'reports.downloadinvoicedue');
        return $pdf->download('InvoiceDueReport.pdf');  
    }
    
     public function pdfInvoiceOverDue(Request $request) {
        $user = LmsUser::where('customer_id',$request->customer_id)->pluck('user_id');
        $getInvoice  =  $this->invRepo->pdfInvoiceOverDue($request);
        $overduereport = [];
        foreach($getInvoice as $invoice) :
        $overduereport[] = [
            'Batch No' => $invoice->disbursal->disbursal_batch->batch_id ?? NULL,
            'Batch Date' => date('d/m/Y',strtotime($invoice->disbursal->disbursal_batch->created_at)) ?? NULL,
            'Bill No' => $invoice->invoice->invoice_no ?? NULL,
            'Bill Date' => Carbon::parse($invoice->invoice->invoice_date)->format('d/m/Y') ?? NULL,
            'Due Date' => Carbon::parse($invoice->payment_due_date)->format('d/m/Y') ?? NULL,
            'Bill Amount' => number_format($invoice->invoice->invoice_amount ?? 0),
            'Approve Amount' => number_format($invoice->invoice->invoice_approve_amount ?? 0),
            'Balance' => number_format($invoice->invoice->invoice_approve_amount ?? 0),
            'Days OD' => $invoice->InterestAccrual->count() ?? 0,
        ];
        endforeach;
        if (strtolower($request->type) == 'excel') {
           $toExportData['Invoice OverDue'] = $overduereport;
           return $this->fileHelper->array_to_excel($toExportData, 'InvoiceOverDueReport.xlsx');
        }
        $pdfArr = ['userInfo' => $getInvoice, 'fromdate' => $request->from_date, 'todate' => $request->to_date,'user' => $user];
        $pdf = $this->fileHelper->array_to_pdf($pdfArr, 'reports.downloadinvoiceoverdue');
        return $pdf->download('InvoiceOverDueReport.pdf');  
    }
    
   public function pdfInvoiceRealisation(Request $request)
   {
        $user = LmsUser::where('customer_id',$request->customer_id)->pluck('user_id');
        $getInvoice  =  $this->invRepo->pdfInvoiceRealisation($request);
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
            'Business' => $invoice->invoice->business->biz_entity_name ?? NULL,
        ];
        endforeach;
        if (strtolower($request->type) == 'excel') {
           $toExportData['Invoice Realisation'] = $realisationreport;
           return $this->fileHelper->array_to_excel($toExportData, 'InvoiceRealisation.xlsx');
        }
        $pdfArr = ['userInfo' => $getInvoice, 'fromdate' => $request->from_date, 'todate' => $request->to_date,'user' => $user];
        $pdf = $this->fileHelper->array_to_pdf($pdfArr, 'reports.downloadrealisation');
        return $pdf->download('InvoiceRealisation.pdf');    
   }
}