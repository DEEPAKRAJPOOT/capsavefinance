<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Inv\Repositories\Models\Master\EmailTemplate;
use App\Inv\Repositories\Contracts\ReportInterface;
use Illuminate\Support\Facades\Storage;
use PHPExcel_IOFactory;
use Carbon\Carbon;
use PHPExcel;
use Helpers;

class UtilizationReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $needConsolidatedReport;
    private $emailTo;
    private $anchor;
    private $sendMail;
    private $reportsRepo;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($needConsolidatedReport, $emailTo, $anchor = null)
    {
        $this->needConsolidatedReport = $needConsolidatedReport;
        $this->emailTo                = $emailTo;
        $this->anchor                 = $anchor;
        $this->sendMail               = false;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ReportInterface $reportsRepo)
    {
        ini_set("memory_limit", "-1");
        $this->reportsRepo = $reportsRepo;

        if ($this->needConsolidatedReport) {
            $this->generateConsolidatedReport();
        }

        if (is_array($this->anchor) && isset($this->anchor['anchor_id'])) {
            $this->generateAnchorReport($this->anchor['anchor_id']);
        }
    }

    private function generateConsolidatedReport()
    {
        $data = $this->reportsRepo->getUtilizationReport([], $this->sendMail);
        if ($this->sendMail) {
            $this->reportGenerateAndSendWithEmail($data, "/Consolidated Report");
        }
    }

    private function generateAnchorReport($anchorId)
    {
        $this->sendMail = false;
        $data           = $this->reportsRepo->getUtilizationReport(['anchor_id' => $anchorId], $this->sendMail);

        if ($this->sendMail) {
            $this->reportGenerateAndSendWithEmail($data, "/Anchor Wise Report".time().'_'.rand(111111, 999999));
        }
    }

    private function reportGenerateAndSendWithEmail($data, $reportName)
    {
        $emailTemplate  = EmailTemplate::getEmailTemplate("REPORT_UTILIZATION");
        if ($emailTemplate) {
            $compName                = is_array($this->anchor) && isset($this->anchor['comp_name']) ? $this->anchor['comp_name'] : '';
            $emailData               = Helpers::getDailyReportsEmailData($emailTemplate, $compName);
            $filePath                = $this->downloadUtilizationExcel($data, $reportName);
            $emailData['to']      = $this->emailTo;
            $emailData['attachment'] = $filePath;
            \Event::dispatch("NOTIFY_UTILIZATION_REPORT", serialize($emailData));
        }
    }

    private function downloadUtilizationExcel($exceldata, $reportName) 
    {
        $rows = 1;

        $sheet =  new PHPExcel();
        $sheet->setActiveSheetIndex(0)
        ->setCellValue('A'.$rows, 'Anchor Name')
        ->setCellValue('B'.$rows, 'Program Name')
        ->setCellValue('C'.$rows, 'Sub Program Name')
        ->setCellValue('D'.$rows, '# of Clients sanctioned')
        ->setCellValue('E'.$rows, '# of Overdue Customers')
        ->setCellValue('F'.$rows, 'Total Over Due Amount')
        ->setCellValue('G'.$rows, 'Client Name')
        ->setCellValue('H'.$rows, 'Customer ID')
        ->setCellValue('I'.$rows, 'Virtual Account #')
        ->setCellValue('J'.$rows, 'Client Sanction Limit')
        ->setCellValue('K'.$rows, 'Limit Utilized Limit')
        ->setCellValue('L'.$rows, 'Available Limit')
        ->setCellValue('M'.$rows, 'Expiry Date')
        ->setCellValue('N'.$rows, 'Sales Person Name')
        ->setCellValue('O'.$rows,'Invoice #')
        ->setCellValue('P'.$rows,'Invoice Date')
        ->setCellValue('Q'.$rows,'Invoice Amount')
        ->setCellValue('R'.$rows,'Invoice Approved')
        ->setCellValue('S'.$rows,'Margin Amount')
        ->setCellValue('T'.$rows,'Amount Disbursed')
        ->setCellValue('U'.$rows,'Principal OverDue Days')
        ->setCellValue('V'.$rows,'Principal OverDue Amount')
        ->setCellValue('W'.$rows,'Over Due Days')
        ->setCellValue('X'.$rows,'Over Due Interest Amount');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':X'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            if(!empty($rowData['disbursement'])){
                foreach($rowData['disbursement'] as $disb){                    
                    if(!empty($disb['invoice'])){
                        foreach($disb['invoice'] as $inv){
                            $sheet->setActiveSheetIndex(0)
                            ->setCellValueExplicit('A'.$rows, $rowData['anchor_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('B'.$rows, $rowData['prgm_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('C'.$rows, $rowData['sub_prgm_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('D'.$rows, $rowData['client_sanction'], \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('E'.$rows, $rowData['ttl_od_customer'], \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('F'.$rows, number_format($rowData['ttl_od_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('G'.$rows, $disb['client_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('H'.$rows, $disb['user_id'], \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('I'.$rows, $disb['virtual_ac'], \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('J'.$rows, number_format($disb['client_sanction_limit'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('K'.$rows, number_format($disb['limit_utilize'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('L'.$rows, number_format($disb['limit_available'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('M'.$rows, Carbon::parse($disb['end_date'])->format('d/m/Y') ?? NULL, \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('N'.$rows, $disb['sales_person_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('O'.$rows, $inv['invoice_no'], \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('P'.$rows, Carbon::parse($inv['invoice_date'])->format('d/m/Y') ?? NULL, \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('Q'.$rows, number_format($inv['invoice_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('R'.$rows, number_format($inv['approve_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('S'.$rows, number_format($inv['margin_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('T'.$rows, number_format($inv['disb_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('U'.$rows, $inv['principal_od_days'], \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('V'.$rows, number_format($inv['principal_od_amount'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('W'.$rows, $inv['od_days'], \PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('X'.$rows, number_format($inv['od_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING);
                            $rows++;
                        }
                    }
                }
            }
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        
        $dirPath = 'public/report/temp/utilizationReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        // $filePath = $storage_path.'/Utilization Report'.time().'_'.rand(1111, 9999).'_'.'.xlsx';
        $filePath = $storage_path.$reportName.'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }
}
