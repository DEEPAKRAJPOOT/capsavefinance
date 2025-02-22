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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use App\Helpers\Helper;

class MarginReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sendMail;
    private $emailTo;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emailTo)
    {
        $this->sendMail = false;
        $this->emailTo  = $emailTo;
       
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
        $data              = $this->reportsRepo->getMarginReport([], $this->sendMail);

        if ($this->sendMail) {
            $emailTemplate  = EmailTemplate::getEmailTemplate("REPORT_MARGIN");
            if ($emailTemplate) {
                $emailData               = Helpers::getDailyReportsEmailData($emailTemplate);
                $filePath                = $this->downloadMarginReport($data);
                $emailData['to']      = $this->emailTo;
                $emailData['attachment'] = Storage::url($filePath);
                \Event::dispatch("NOTIFY_MARGIN_REPORT", serialize($emailData));
            }
        }
    }

    private function downloadMarginReport($exceldata)
    {
        $rows = 5;
        $sheet =  new Spreadsheet();
        $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Anchor')
            ->setCellValue('B'.$rows, 'Client')
            ->setCellValue('C'.$rows, 'Client ID')
            ->setCellValue('D'.$rows, 'Invoice#')
            ->setCellValue('E'.$rows, 'Invoice Date')
            ->setCellValue('F'.$rows, 'Invoice Amount')
            ->setCellValue('G'.$rows, 'Disbursed Amount')
            ->setCellValue('H'.$rows, 'Disbursal Date')
            ->setCellValue('I'.$rows, 'Margin %')
            ->setCellValue('J'.$rows, 'Margin Allocated')
            ->setCellValue('K'.$rows, 'Margin Outstanding');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, $rowData['anchor'])
            ->setCellValue('B'.$rows, $rowData['client'])
            ->setCellValue('C'.$rows, $rowData['client_id'])
            ->setCellValue('D'.$rows, $rowData['invoice_no'])
            ->setCellValue('E'.$rows, $rowData['invoice_date'])
            ->setCellValue('F'.$rows, $rowData['invoice_amount'])
            ->setCellValue('G'.$rows, $rowData['disbursed_amt'])
            ->setCellValue('H'.$rows, $rowData['disbursal_date'])
            ->setCellValue('I'.$rows, $rowData['margin_per'])
            ->setCellValue('J'.$rows, $rowData['margin_allocated'])
            ->setCellValue('K'.$rows, $rowData['margin_outstanding']);
            $rows++;
        }

        $tmpHandle = tmpfile();
        $metaDatas = stream_get_meta_data($tmpHandle);
        $tmpFilename = $metaDatas['uri'];

        $objWriter = IOFactory::createWriter($sheet, 'Xlsx');

        $dirPath = 'public/report/temp/marginReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = Storage::path($dirPath);
        $fileName = '/Margin Report'.time().'.xlsx';
        $objWriter->save($tmpFilename);
        $attributes['temp_file_path'] = $tmpFilename;
        $path = Helper::uploadAwsS3Bucket($storage_path, $attributes, $fileName);
        unlink($tmpFilename);
        return $path;
    }
}
