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
                $emailData['to']      = $this->emailTo;
                $emailData['attachment'] = $filePath;
                \Event::dispatch("NOTIFY_OVERDUE_REPORT", serialize($emailData));
            }
        }
    }

    private function downloadOverdueReport($exceldata)
    {
        $rows = 5;
        $sheet =  new PHPExcel();
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
            ->setCellValue('A'.$rows, $rowData['cust_name'])
            ->setCellValue('B'.$rows, $rowData['customer_id'])
            ->setCellValue('C'.$rows, $rowData['invoice_no'])
            ->setCellValue('D'.$rows, $rowData['payment_due_date'])
            ->setCellValue('E'.$rows, $rowData['virtual_ac'])
            ->setCellValue('F'.$rows, number_format($rowData['client_sanction_limit'],2))
            ->setCellValue('G'.$rows, number_format($rowData['limit_available'],2))
            ->setCellValue('H'.$rows, number_format($rowData['out_amt'],2))
            ->setCellValue('I'.$rows, $rowData['od_days'])
            ->setCellValue('J'.$rows, number_format($rowData['od_amt'],2))
            ->setCellValue('K'.$rows, $rowData['sales_person_name']);
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
}
