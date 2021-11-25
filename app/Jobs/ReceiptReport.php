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

class ReceiptReport implements ShouldQueue
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
        $data              = $this->reportsRepo->getReceiptReport([], $this->sendMail);
        if ($this->sendMail) {
            $emailTemplate  = EmailTemplate::getEmailTemplate("REPORT_RECEIPT");
            if ($emailTemplate) {
                $emailData               = Helpers::getDailyReportsEmailData($emailTemplate);
                $filePath                = $this->downloadReceiptReport($data);
                $emailData['to']      = $this->emailTo;
                $emailData['attachment'] = $filePath;
                \Event::dispatch("NOTIFY_RECEIPT_REPORT", serialize($emailData));
            }
        }
    }

    private function downloadReceiptReport($exceldata)
    {
        $rows = 5;
        $sheet =  new PHPExcel();
        $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Receipt Date')
            ->setCellValue('B'.$rows, 'Receipt Account #')
            ->setCellValue('C'.$rows, 'Client/borrower name')
            ->setCellValue('D'.$rows, 'Client ID')
            ->setCellValue('E'.$rows, 'Head against which applied Interest/principal/charges')
            ->setCellValue('F'.$rows, 'Adjusted against client invoice #')
            ->setCellValue('G'.$rows, 'Receipt UTR #')
            ->setCellValue('H'.$rows, 'Client Invoice Date')
            ->setCellValue('I'.$rows, 'Adjusted against capsave invoice #')
            ->setCellValue('J'.$rows, 'Capsave Invoice Date')
            ->setCellValue('K'.$rows, 'Date on which original disbursement happen')
            ->setCellValue('L'.$rows, 'Amount applied')
            ->setCellValue('M'.$rows, 'Total amount received');
            
        $sheet->getActiveSheet()->getStyle('A'.$rows.':M'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, $rowData['receipt_date'])
            ->setCellValue('B'.$rows, $rowData['receipt_account'])
            ->setCellValue('C'.$rows, $rowData['client_name'])
            ->setCellValue('D'.$rows, $rowData['client_id'])
            ->setCellValue('E'.$rows, $rowData['trans_type_name'])
            ->setCellValue('F'.$rows, $rowData['invoice_no'])
            ->setCellValue('G'.$rows, $rowData['receipt_utr'])
            ->setCellValue('H'.$rows, $rowData['invoice_date'])
            ->setCellValue('I'.$rows, $rowData['capsave_invoice_no'])
            ->setCellValue('J'.$rows, $rowData['capsave_inv_date'])
            ->setCellValue('K'.$rows, $rowData['disburse_date'])
            ->setCellValue('L'.$rows, $rowData['amount'])
            ->setCellValue('M'.$rows, $rowData['total_amount']);
            $rows++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');

        $dirPath = 'public/report/temp/receiptReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.'/Receipt Report'.time().'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }
}
