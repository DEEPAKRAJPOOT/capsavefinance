<?php

namespace App\Jobs;

use Helpers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Inv\Repositories\Models\Master\EmailTemplate;
use App\Inv\Repositories\Contracts\ReportInterface;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Carbon\Carbon;
use App\Helpers\Helper;

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
            $emailData['attachment'] = Storage::url($filePath);
            \Event::dispatch("NOTIFY_MATURITY_REPORT", serialize($emailData));
        }
    }

    private function downloadMaturityReport($exceldata, $reportName)
    {
        $rows  = 5;
        $sheet =  new Spreadsheet();
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
            ->setCellValueExplicit('A'.$rows, $rowData['cust_name'], DataType::TYPE_STRING)
            ->setCellValueExplicit('B'.$rows, $rowData['loan_ac'], DataType::TYPE_STRING)
            ->setCellValueExplicit('C'.$rows, $rowData['virtual_ac'], DataType::TYPE_STRING)
            ->setCellValueExplicit('D'.$rows, Carbon::parse($rowData['trans_date'])->format('d-m-Y'), DataType::TYPE_STRING)
            ->setCellValueExplicit('E'.$rows, $rowData['trans_no'], DataType::TYPE_STRING)
            ->setCellValueExplicit('F'.$rows, $rowData['invoice_no'], DataType::TYPE_STRING)
            ->setCellValueExplicit('G'.$rows, Carbon::parse($rowData['invoice_date'])->format('d-m-Y'), DataType::TYPE_STRING)
            ->setCellValueExplicit('H'.$rows, number_format($rowData['invoice_amt'],2), DataType::TYPE_STRING)
            ->setCellValueExplicit('I'.$rows, number_format($rowData['margin_amt'],2), DataType::TYPE_STRING)
            ->setCellValueExplicit('J'.$rows, number_format($rowData['disb_amt'],2), DataType::TYPE_STRING)
            ->setCellValueExplicit('K'.$rows, number_format($rowData['out_amt'],2), DataType::TYPE_STRING)
            ->setCellValueExplicit('L'.$rows, $rowData['out_days'], DataType::TYPE_STRING)
            ->setCellValueExplicit('M'.$rows, $rowData['tenor'], DataType::TYPE_STRING)
            ->setCellValueExplicit('N'.$rows, Carbon::parse($rowData['due_date'])->format('d-m-Y'), DataType::TYPE_STRING)
            ->setCellValueExplicit('O'.$rows, number_format($rowData['due_amt'],2), DataType::TYPE_STRING)
            ->setCellValueExplicit('P'.$rows, $rowData['od_days'], DataType::TYPE_STRING)
            ->setCellValueExplicit('Q'.$rows, number_format($rowData['od_amt'],2), DataType::TYPE_STRING)
            ->setCellValueExplicit('R'.$rows, $rowData['remark'], DataType::TYPE_STRING);
            $rows++;
        }

        $tmpHandle = tmpfile();
        $metaDatas = stream_get_meta_data($tmpHandle);
        $tmpFilename = $metaDatas['uri'];

        $objWriter = IOFactory::createWriter($sheet, 'Xlsx');

        $dirPath = 'public/report/temp/maturityReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }

        $storage_path = Storage::path($dirPath);
        // $filePath = $storage_path.'/Maturity Report'.time().'_'.rand(1111, 9999).'_'.'.xlsx';
        $fileName = $reportName.'.xlsx';
        $objWriter->save($tmpFilename);
        $attributes['temp_file_path'] = $tmpFilename;
        $path = Helper::uploadAwsS3Bucket($storage_path, $attributes, $fileName);
        unlink($tmpFilename);
        return $path;
    }
}
