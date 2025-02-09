<?php

namespace App\Jobs;
use App\Helpers\Helper;
use Carbon\Carbon;
use Helpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Inv\Repositories\Contracts\ReportInterface;
use App\Inv\Repositories\Models\Master\EmailTemplate;

class OverdueReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sendMail;
    private $emailTo;
    private $userId;
    private $toDate;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emailTo, $userId, $toDate)
    {
        $this->sendMail = false;
        $this->emailTo  = $emailTo;
        $this->userId = $userId;
        $this->toDate = $toDate;
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
        $data              = $this->reportsRepo->getOverdueReport(['user_id' => $this->userId, 'to_date' => $this->toDate], $this->sendMail);

        if ($this->sendMail) {
            $emailTemplate  = EmailTemplate::getEmailTemplate("REPORT_OVERDUE");
            if ($emailTemplate) {
                $emailData               = Helpers::getDailyReportsEmailData($emailTemplate);
                $filePath                = $this->downloadOverdueReport($data);
                $emailData['to']         = $this->emailTo;
                $emailData['attachment'] = Storage::url($filePath);
                \Event::dispatch("NOTIFY_OVERDUE_REPORT", serialize($emailData));
            }
        }
    }

    private function downloadOverdueReport($exceldata)
    {
        $rows = 5;
        $sheet =  new Spreadsheet();
        $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Customer Name')
            ->setCellValue('B'.$rows, 'Customer ID')
            ->setCellValue('C'.$rows, 'Invoice No')
            ->setCellValue('D'.$rows, 'Invoice Due Date')
            ->setCellValue('E'.$rows, 'Virtual Account #')
            ->setCellValue('F'.$rows, 'Sanction Limit')
            ->setCellValue('G'.$rows, 'Limit Available')
            ->setCellValue('H'.$rows, 'O/s Amount')
            ->setCellValue('I'.$rows, 'Over Due Days')
            ->setCellValue('J'.$rows, 'Overdue Amount')
            ->setCellValue('K'.$rows, 'Sales Person Name');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValueExplicit('A'.$rows, $rowData['cust_name'], DataType::TYPE_STRING)
            ->setCellValueExplicit('B'.$rows, $rowData['customer_id'], DataType::TYPE_STRING)
            ->setCellValueExplicit('C'.$rows, $rowData['invoice_no'], DataType::TYPE_STRING)
            ->setCellValueExplicit('D'.$rows, $rowData['payment_due_date'], DataType::TYPE_STRING)
            ->setCellValueExplicit('E'.$rows, $rowData['virtual_ac'], DataType::TYPE_STRING)
            ->setCellValueExplicit('F'.$rows, number_format($rowData['client_sanction_limit'],2), DataType::TYPE_STRING)
            ->setCellValueExplicit('G'.$rows, number_format($rowData['limit_available'],2), DataType::TYPE_STRING)
            ->setCellValueExplicit('H'.$rows, number_format($rowData['out_amt'],2), DataType::TYPE_STRING)
            ->setCellValueExplicit('I'.$rows, $rowData['od_days'], DataType::TYPE_STRING)
            ->setCellValueExplicit('J'.$rows, number_format($rowData['od_amt'],2), DataType::TYPE_STRING)
            ->setCellValueExplicit('K'.$rows, $rowData['sales_person_name'], DataType::TYPE_STRING);
            $rows++;
        }
        
        $tmpHandle = tmpfile();
        $metaDatas = stream_get_meta_data($tmpHandle);
        $tmpFilename = $metaDatas['uri'];

        $objWriter = IOFactory::createWriter($sheet, 'Xlsx');

        $dirPath = 'public/report/temp/overdueReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = Storage::path($dirPath);
        $fileName = '/Overdue Report'.time().'.xlsx';
        $objWriter->save($tmpFilename);
        $attributes['temp_file_path'] = $tmpFilename;
        $path = Helper::uploadAwsS3Bucket($storage_path, $attributes, $fileName);
        unlink($tmpFilename);
        return $path;
    }
}
