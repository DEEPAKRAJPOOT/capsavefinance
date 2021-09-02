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

class AccountDisbursalReport implements ShouldQueue
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
        $data              = $this->reportsRepo->getAccountDisbursalReport([], $this->sendMail);

        if ($this->sendMail) {
            $emailTemplate  = EmailTemplate::getEmailTemplate("REPORT_ACCOUNT_DISBURSAL");
            if ($emailTemplate) {
                $compName                = is_array($this->anchor) && isset($this->anchor['comp_name']) ? $this->anchor['comp_name'] : '';
                $emailData               = Helpers::getDailyReportsEmailData($emailTemplate, $compName);
                $filePath                = $this->downloadAccountDailyDisbursalReport($data);
                $emailData['email']      = $this->emailTo;
                $emailData['attachment'] = $filePath;
                \Event::dispatch("NOTIFY_ACCOUNT_DISBURSAL_REPORT", serialize($emailData));
            }
        }
    }

    private function downloadAccountDailyDisbursalReport($exceldata)
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
            ->setCellValue('I'.$rows, 'Amount Disbrused')
            ->setCellValue('J'.$rows, 'UTR')
            ->setCellValue('K'.$rows, 'Remark while uploading Invoice')
            ->setCellValue('L'.$rows, 'Beneficiary Credit Account No.')
            ->setCellValue('M'.$rows, 'Beneficiary IFSC Code')
            ->setCellValue('N'.$rows, 'Status')
            ->setCellValue('O'.$rows, 'Status Description');

        $sheet->getActiveSheet()->getStyle('A'.$rows.':O'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
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
            ->setCellValue('K'.$rows, $rowData['remark'])
            ->setCellValue('L'.$rows, $rowData['bank_ac'])
            ->setCellValue('M'.$rows, $rowData['ifsc'])
            ->setCellValue('N'.$rows, $rowData['status'])
            ->setCellValue('O'.$rows, $rowData['status_des']);
            $rows++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');

        $dirPath = 'public/report/temp/accountDailyDisbursalReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.'/Account Daily Disbursal Report'.time().'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }
}
