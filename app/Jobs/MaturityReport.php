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

class MaturityReport implements ShouldQueue
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
        $data = $this->reportsRepo->getMaturityReport([], $this->sendMail);
        if ($this->sendMail) {
            $this->reportGenerateAndSendWithEmail($data, "/Consolidated Report");
        }
    }

    private function generateAnchorReport($anchorId)
    {
        $this->sendMail = false;
        $data           = $this->reportsRepo->getMaturityReport(['anchor_id' => $anchorId], $this->sendMail);

        if ($this->sendMail) {
            $this->reportGenerateAndSendWithEmail($data, "/Anchor Wise Report".time().'_'.rand(111111, 999999));
        }
    }

    private function reportGenerateAndSendWithEmail($data, $reportName)
    {
        $emailTemplate  = EmailTemplate::getEmailTemplate("REPORT_MATURITY");
        if ($emailTemplate) {
            $compName                = is_array($this->anchor) && isset($this->anchor['comp_name']) ? $this->anchor['comp_name'] : '';
            $emailData               = Helpers::getDailyReportsEmailData($emailTemplate, $compName);
            $filePath                = $this->downloadMaturityReport($data, $reportName);
            $emailData['to']      = $this->emailTo;
            $emailData['attachment'] = $filePath;
            \Event::dispatch("NOTIFY_MATURITY_REPORT", serialize($emailData));
        }
    }

    private function downloadMaturityReport($exceldata, $reportName)
    {
        $rows  = 5;
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
        // $filePath = $storage_path.'/Maturity Report'.time().'_'.rand(1111, 9999).'_'.'.xlsx';
        $filePath = $storage_path.$reportName.'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }
}
