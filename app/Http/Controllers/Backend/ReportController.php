<?php

namespace App\Http\Controllers\Backend;
use Auth;
use Session;
use Helpers;
use PDF as DPDF;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Contracts\ReportInterface;


class ReportController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $invRepo;
    public function __construct(InvoiceInterface $invRepo, ReportInterface $reportsRepo) {
        $this->invRepo = $invRepo;
        $this->reportsRepo = $reportsRepo;
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
            'GSTN' => ($inv_comp_data['gst_no'] ?? $result->biz_gst_no), 
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
           return $this->array_to_excel($toExportData);
       }
       DPDF::setOptions(['isHtml5ParserEnabled'=> true]);
       $pdf = DPDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4'])
                ->loadView('reports.leaseRegisterReport', ['leaseRegister' => $leaseArr],[],'UTF-8');
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
    
    public function pdfInvoiceDue(Request $request)
    {
        $user = LmsUser::where('customer_id',$request->customer_id)->pluck('user_id');
        $getInvoice  =  $this->invRepo->pdfInvoiceDue($request);
        DPDF::setOptions(['isHtml5ParserEnabled'=> true]);
        $pdf = DPDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4'])
                ->loadView('reports.downloadinvoicedue', ['userInfo' => $getInvoice, 'fromdate' => $request->from_date, 'todate' => $request->to_date,'user' => $user],[],'UTF-8');
        return $pdf->download('InvoiceDueReport.pdf');  
    }
    
     public function pdfInvoiceOverDue(Request $request)
    {
        $user = LmsUser::where('customer_id',$request->customer_id)->pluck('user_id');
        $getInvoice  =  $this->invRepo->pdfInvoiceOverDue($request);
        DPDF::setOptions(['isHtml5ParserEnabled'=> true]);
        $pdf = DPDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4'])
                ->loadView('reports.downloadinvoiceoverdue', ['userInfo' => $getInvoice, 'fromdate' => $request->from_date, 'todate' => $request->to_date,'user' => $user],[],'UTF-8');
        return $pdf->download('InvoiceOverDueReport.pdf');  
    }

    public function array_to_excel($toExportData, $file_name = "") {
        ob_start();
        if(empty($file_name)) {
            $file_name = "Report - " . _getRand(15).".xlsx";
        }
        $activeSheet = 0;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->createSheet();
        foreach ($toExportData as $title => $data) {
            $rec_count = count($data[0]);
            $header_cols = array_keys($data[0]);
            $sheetTitle = $title;
            $objPHPExcel->setActiveSheetIndex($activeSheet);
            $activeSheet++;
            $column = 0;
            $header_row = 2;
            $start_row = 4;
            $row = $start_row;
            $column = 0;
            $floor = floor($rec_count/26);
            $reminder = $rec_count % 26;
            $char = ($floor > 0 ? chr(ord("A") + $floor - 1) : '').chr(ord("A") + $reminder - 1);
            foreach($data as $key => $item) {
              foreach($item as $key1 => $item1) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $row, $item1);
                $column++;
              }
              $argb = "FFFFFFFF";
              if ($row % 2 == 1){
                $argb = "FFE0E0E0";
              }
              $styleArray = array(
                'fill' => array(
                  'type' => PHPExcel_Style_Fill::FILL_SOLID,
                  'startcolor' => array(
                    'argb' => $argb,
                  ),
                ),
              );
              $objPHPExcel->getActiveSheet()->getStyle('A'. $row .':' . $char . $row)->applyFromArray($styleArray);
              $column = 0;
              $row++;
            }
            $end_row = $row - 1;
            $row = $header_row;
            $column = 0;
            foreach($header_cols as $key) {
               $key = ucwords(str_replace('_', ' ', $key));
               $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($column, $row)->setValueExplicit($key, PHPExcel_Cell_DataType::TYPE_STRING);
                  $column++;
            }
            $styleArray = array(
              'font' => array(
                'bold' => true,
              ),
              'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              ),
              'borders' => array(
                  'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                  ),
              ),
              'fill' => array(
                  'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                  'rotation' => 90,
                  'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                  ),
                  'endcolor' => array(
                    'argb' => 'FFFFFFFF',
                  ),
              ),
            );
     
            $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $char . '1');
            $objPHPExcel->getActiveSheet()->setCellValue('A1', $file_name);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
     
            $styleArray = array(
              'font' => array(
                'bold' => true,
              ),
              'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              ),
              'borders' => array(
                  'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                  ),
              ),
              'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'rotation' => 90,
                'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                ),
                'endcolor' => array(
                    'argb' => 'FFFFFFFF',
                ),
              ),
            );
            $objPHPExcel->getActiveSheet()->getStyle('A'. $header_row .':' . $char . $header_row)->applyFromArray($styleArray);
            foreach($header_cols as $key => $el) {
                 $floor = floor(($key)/26);
                 $reminder = ($key) % 26;
                 $char = ($floor > 0 ? chr(ord("A") + $floor-1) : '').chr(ord("A") + $reminder);
                 $objPHPExcel->getActiveSheet()->getColumnDimension($char)->setAutoSize(true);
            }
            $styleArray = array(
              'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY,
              ),
              'borders' => array(
                'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN,
                  // 'color' => array('argb' => 'FFFF0000'),
                ),
              ),
            );
            $objPHPExcel->getActiveSheet()->getStyle('A'. $start_row .':' . $char . $end_row)->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->setTitle($sheetTitle);
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        ob_end_flush();
        exit; 
    } 
}