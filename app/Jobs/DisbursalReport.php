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
            $this->generateAnchorReport($this->anchor);
        }
    }

    private function generateConsolidatedReport()
    {
        $data = $this->reportsRepo->getDisbursalReport([], $this->sendMail);
        if ($this->sendMail) {
            $this->reportGenerateAndSendWithEmail($data);
        }
    }

    private function generateAnchorReport($anchorId)
    {
        $this->sendMail = false;
        $data           = $this->reportsRepo->getDisbursalReport(['anchor_id' => $anchorId], $this->sendMail);

        if ($this->sendMail) {
            $this->reportGenerateAndSendWithEmail($data);
        }
    }

    private function reportGenerateAndSendWithEmail($data)
    {
        $emailTemplate  = EmailTemplate::getEmailTemplate("REPORT_DISBURSAL");
        if ($emailTemplate) {
            $compName                = is_array($this->anchor) && isset($this->anchor['comp_name']) ? $this->anchor['comp_name'] : '';
            $emailData               = Helpers::getDailyReportsEmailData($emailTemplate, $compName);
            $filePath                = $this->downloadDailyDisbursalReport($data);
            $emailData['email']      = $this->emailTo;
            $emailData['attachment'] = $filePath;
            \Event::dispatch("NOTIFY_DISBURSAL_REPORT", serialize($emailData));
        }
    }

    private function downloadDailyDisbursalReport($exceldata)
    {
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
            ->setCellValue('I'.$rows, 'Amount Disbursed')
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
        $filePath = $storage_path.'/Daily Disbursal Report'.time().'_'.rand(1111, 9999).'_'.'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }
}
