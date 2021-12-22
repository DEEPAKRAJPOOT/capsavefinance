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

class DisbursalReport implements ShouldQueue
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
        $this->reportsRepo = $reportsRepo;

        if ($this->needConsolidatedReport) {
            ini_set("memory_limit", "-1");
            $this->generateConsolidatedReport();
        }

        if (is_array($this->anchor) && isset($this->anchor['anchor_id'])) {
            $this->generateAnchorReport($this->anchor['anchor_id']);
        }
    }

    private function generateConsolidatedReport()
    {
        $data = $this->reportsRepo->getDisbursalReport([], $this->sendMail);
        if ($this->sendMail) {
            $this->reportGenerateAndSendWithEmail($data, "/Consolidated Report");
        }
    }

    private function generateAnchorReport($anchorId)
    {
        $this->sendMail = false;
        $data           = $this->reportsRepo->getDisbursalReport(['anchor_id' => $anchorId], $this->sendMail);

        if ($this->sendMail) {
            $this->reportGenerateAndSendWithEmail($data, "/Anchor Wise Report".time().'_'.rand(111111, 999999));
        }
    }

    private function reportGenerateAndSendWithEmail($data, $reportName)
    {
        $emailTemplate  = EmailTemplate::getEmailTemplate("REPORT_DISBURSAL");
        if ($emailTemplate) {
            $compName                = is_array($this->anchor) && isset($this->anchor['comp_name']) ? $this->anchor['comp_name'] : '';
            $emailData               = Helpers::getDailyReportsEmailData($emailTemplate, $compName);
            $filePath                = $this->downloadDailyDisbursalReport($data, $reportName);
            $emailData['to']      = $this->emailTo;
            $emailData['attachment'] = $filePath;
            \Event::dispatch("NOTIFY_DISBURSAL_REPORT", serialize($emailData));
        }
    }

    private function downloadDailyDisbursalReport($exceldata, $reportName)
    {
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
            ->setCellValue('V'.$rows, 'From')
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
            ->setCellValue('AR'.$rows, 'Net of interest, PF & Stamp');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':AR'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
			
            $sheet->setActiveSheetIndex(0)
            ->setCellValueExplicit('A'.$rows, $rowData['cust_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('B'.$rows, $rowData['rm_sales'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('C'.$rows,$rowData['anchor_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('D'.$rows, $rowData['anchor_prgm_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('E'.$rows, $rowData['vendor_ben_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('F'.$rows, $rowData['region'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('G'.$rows, $rowData['sanction_number'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('H'.$rows, Carbon::parse($rowData['sanction_date'])->format('d-m-Y') ?? NULL, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('I'.$rows, number_format($rowData['sanction_amount'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('J'.$rows, !empty($rowData['status']) ? $rowData['status'] : '---', \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('K'.$rows, Carbon::parse($rowData['disbursal_month'])->format('F') ?? NULL, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('L'.$rows, !empty($rowData['disburse_amount']) ? number_format($rowData['disburse_amount'],2) : '', \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('M'.$rows, Carbon::parse($rowData['disbursement_date'])->format('d-m-Y') ?? NULL, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('N'.$rows, $rowData['disbursal_utr'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('O'.$rows, $rowData['disbursal_act_no'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('P'.$rows, $rowData['disbursal_ifc'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('Q'.$rows, $rowData['type_finance'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('R'.$rows, $rowData['supl_chan_type'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('S'.$rows, $rowData['tenor'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('T'.$rows, $rowData['interest_rate'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('U'.$rows, number_format($rowData['interest_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('V'.$rows, Carbon::parse($rowData['from'])->format('d-m-Y') ?? NULL, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('W'.$rows, Carbon::parse($rowData['to'])->format('d-m-Y') ?? NULL, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('X'.$rows, number_format($rowData['tds_intrst'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('Y'.$rows, number_format($rowData['net_intrst'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('Z'.$rows, !empty($rowData['intrst_rec_date']) ? Carbon::parse($rowData['intrst_rec_date'])->format('d-m-Y') : '---', \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AA'.$rows, number_format($rowData['proce_fee'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AB'.$rows, number_format($rowData['proce_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AC'.$rows, number_format($rowData['proce_fee_gst'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AD'.$rows, number_format($rowData['tds_proce_fee'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AE'.$rows, number_format($rowData['net_proc_fee_rec'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AF'.$rows, number_format($rowData['proce_fee_rec'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AG'.$rows, !empty($rowData['proce_fee_amt_date']) ? ($rowData['proce_fee_amt_date']) : '---', \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AH'.$rows, number_format($rowData['balance'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AI'.$rows, number_format($rowData['margin_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AJ'.$rows, Carbon::parse($rowData['due_date'])->format('d-m-Y') ?? NULL, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AK'.$rows, !empty($rowData['funds_received']) ? $rowData['funds_received'] : '---', \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AL'.$rows, number_format($rowData['principal_rece'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AM'.$rows, number_format($rowData['received'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AN'.$rows, number_format($rowData['net_receivalble'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AO'.$rows, '---', \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AP'.$rows, number_format($rowData['net_disbursement'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AQ'.$rows, !empty($rowData['gross']) ? $rowData['gross'] : '---', \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AR'.$rows, !empty($rowData['net_of_interest']) ? $rowData['net_of_interest'] : '---', \PHPExcel_Cell_DataType::TYPE_STRING);
            $rows++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');

        $dirPath = 'public/report/temp/dailyDisbursalReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        // $filePath = $storage_path.'/Daily Disbursal Report'.time().'_'.rand(1111, 9999).'_'.'.xlsx';
        $filePath = $storage_path.$reportName.'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }
}
