<?php

namespace App\Jobs;
use Helpers;
use PHPExcel;
use Carbon\Carbon;
use PHPExcel_IOFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Inv\Repositories\Contracts\ReportInterface;
use App\Inv\Repositories\Models\Lms\OutstandingReportLog;
use App\Inv\Repositories\Models\Master\EmailTemplate;

class OutstandingReportManual implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sendMail;
    private $emailTo;
    private $userId;
    private $toDate;
    private $logId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emailTo, $userId, $toDate, $logId)
    {
        $this->sendMail = false;
        $this->emailTo  = $emailTo;
        $this->userId = $userId;
        $this->toDate = $toDate;
        $this->logId = $logId;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ReportInterface $reportsRepo)
    {
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 10000);
        
        //Second and fourth Saturday back dated overdue report 
        // if($this->isSecondFourthSaturday() && is_null($this->userId) && is_null($this->toDate)){
        //     $this->toDate = date('Y-m-d');
        // }

        $this->toDate = date('Y-m-d');

        $this->reportsRepo = $reportsRepo;
        //if to date== current date OR to date is null getOutstandingReportManual else getOutstandingReportManualbackDate
        if($this->toDate === date('Y-m-d')){
            $data = $this->reportsRepo->getOutstandingReportManual(['user_id' => $this->userId, 'to_date' => $this->toDate], $this->sendMail);
        }else if($this->toDate < date('Y-m-d')){
            $data = $this->reportsRepo->getBackDateOutstandingReportManual(['user_id' => $this->userId, 'to_date' => $this->toDate], $this->sendMail);
        }
        $filePath = $this->downloadOutstandingReport($data);
        
        if($this->toDate && $this->logId){
            $this->createOutstandingReportLog($this->toDate, $this->userId, $filePath, $this->logId);
        }
    }

    private function createOutstandingReportLog($toDate, $userId, $filePath, $logId)
    {
        OutstandingReportLog::updateOrCreate(['id' => $logId],[
            'user_id'   => $userId,
            'to_date'   => $toDate,
            'file_path' => $filePath,
        ]);
    }

    private function downloadOutstandingReport($exceldata)
    {
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 10000);
        $rows = 1;
        $sheet =  new PHPExcel();
        $sheet->setActiveSheetIndex(0)
        ->setCellValue('A'.$rows, 'Customer Name')
        ->setCellValue('B'.$rows, 'Customer ID')
        ->setCellValue('C'.$rows, 'Anchor Name')
        ->setCellValue('D'.$rows, 'Invoice No')
        ->setCellValue('E'.$rows, 'Date of Disbursement')
        ->setCellValue('F'.$rows, 'Invoice Amount')
        ->setCellValue('G'.$rows, 'Invoice Approved Amount')
        ->setCellValue('H'.$rows, 'Margin')
        ->setCellValue('I'.$rows, 'Upfront Interest Deducted')
        ->setCellValue('J'.$rows, 'Invoice Level Charges Deducted (If Any)')
        ->setCellValue('K'.$rows, 'Invoice Level Charges Applied (If Any)')
        ->setCellValue('L'.$rows, 'Invoice Disbursement Amount')
        ->setCellValue('M'.$rows, 'Product')
        ->setCellValue('N'.$rows, 'Interest Frequency')
        ->setCellValue('O'.$rows, 'Interest Amount Posted')
        ->setCellValue('P'.$rows, 'Disbursement Method (Net or Gross)')
        ->setCellValue('Q'.$rows, 'Invoice Due Date')
        ->setCellValue('R'.$rows, 'Virtual Account #')
        ->setCellValue('S'.$rows, 'Tenure')
        ->setCellValue('T'.$rows, 'ROI %')
        ->setCellValue('U'.$rows, 'ODI Interest %')
        ->setCellValue('V'.$rows, 'Principal O/S')
        ->setCellValue('W'.$rows, 'Margin O/S')
        ->setCellValue('X'.$rows, 'Interest Outstanding')
        ->setCellValue('Y'.$rows, 'Overdue Interest Posted')
        ->setCellValue('Z'.$rows, 'Overdue Interest Outstanding')
        ->setCellValue('AA'.$rows, 'Invoice level charge O/S')
        ->setCellValue('AB'.$rows, 'Total Outstanding')
        ->setCellValue('AC'.$rows, 'Grace Days - Interest')
        ->setCellValue('AD'.$rows, 'Grace Days - Principle')
        ->setCellValue('AE'.$rows, 'Principle Overdue')
        ->setCellValue('AF'.$rows, 'Principle Overdue Category')
        ->setCellValue('AG'.$rows, 'Principle DPD')
        ->setCellValue('AH'.$rows, 'Interest DPD')
        ->setCellValue('AI'.$rows, 'Final DPD')
        ->setCellValue('AJ'.$rows, 'Outstanding Max Bucket')
        ->setCellValue('AK'.$rows, 'Maturity Days')
        ->setCellValue('AL'.$rows, 'Maturity Bucket')
        ->setCellValue('AM'.$rows, 'Balance Margin to be Refunded')
        ->setCellValue('AN'.$rows, 'Balance Interest to be refunded')
        ->setCellValue('AO'.$rows, 'Balance Overdue Interest to be refunded');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':AO'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, $rowData['custName'])
            ->setCellValue('B'.$rows, $rowData['customerId'])
            ->setCellValue('C'.$rows, $rowData['anchorName'])
            ->setCellValue('D'.$rows, $rowData['invoiceNo'])
            ->setCellValue('E'.$rows, $rowData['disbursementDate'])
            ->setCellValue('F'.$rows, $rowData['invoiceAmt'])
            ->setCellValue('G'.$rows, $rowData['invoiceApproveAmount'])
            ->setCellValue('H'.$rows, $rowData['marginPosted'])
            ->setCellValue('I'.$rows, $rowData['upfrontInterest'])
            ->setCellValue('J'.$rows, $rowData['chargeDeduction'])
            ->setCellValue('K'.$rows, $rowData['invoiceLevelChrg'])
            ->setCellValue('L'.$rows, $rowData['disburseAmount'])
            ->setCellValue('M'.$rows, $rowData['product'])
            ->setCellValue('N'.$rows, $rowData['paymentFrequency'])
            ->setCellValue('O'.$rows, $rowData['interestPosted'])
            ->setCellValue('P'.$rows, $rowData['disbursementMethod'])
            ->setCellValue('Q'.$rows, $rowData['paymentDueDate'])
            ->setCellValue('R'.$rows, $rowData['virtualAc'])
            ->setCellValue('S'.$rows, $rowData['tenure'])
            ->setCellValue('T'.$rows, $rowData['roi'])
            ->setCellValue('U'.$rows, $rowData['odi'])
            ->setCellValue('V'.$rows, $rowData['principalOut'])
            ->setCellValue('W'.$rows, $rowData['marginOut'])
            ->setCellValue('X'.$rows, $rowData['interestOut'])
            ->setCellValue('Y'.$rows, $rowData['overduePosted'])
            ->setCellValue('Z'.$rows, $rowData['overdueOut'])
            ->setCellValue('AA'.$rows, $rowData['invoiceLevelChrgOut'])
            ->setCellValue('AB'.$rows, $rowData['totalOutStanding'])
            ->setCellValue('AC'.$rows, $rowData['intGraceDays'])
            ->setCellValue('AD'.$rows, $rowData['principalGraceDays'])
            ->setCellValue('AE'.$rows, $rowData['principalOverdue'])
            ->setCellValue('AF'.$rows, $rowData['principalOverdueCategory'])
            ->setCellValue('AG'.$rows, $rowData['principalDPD'])
            ->setCellValue('AH'.$rows, $rowData['interestPDP'])
            ->setCellValue('AI'.$rows, $rowData['finalDPD'])
            ->setCellValue('AJ'.$rows, $rowData['outstandingMaxBucket'])
            ->setCellValue('AK'.$rows, $rowData['maturityDays'])
            ->setCellValue('AL'.$rows, $rowData['maturityBucket'])
            ->setCellValue('AM'.$rows, $rowData['marginToRefunded'])
            ->setCellValue('AN'.$rows, $rowData['interestToRefunded'])
            ->setCellValue('AO'.$rows, $rowData['overdueToRefunded']);
            $rows++;
        }
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        //$objWriter->setPreCalculateFormulas(true);

        $dirPath = 'public/report/temp/OutstandingReport/manual/console';
        if(!App::runningInConsole()){
            $dirPath = 'public/report/temp/OutstandingReport/manual/http';
        }
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.'/Invoice Outstanding Report'.'_'.time().'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }

    private function isSecondFourthSaturday(){
        $month = date('M');
        $year = date('Y');
        $secondSat = date('Ymd', strtotime('second sat of '.$month.' '.$year));
        $fourthSat = date('Ymd', strtotime('fourth sat of '.$month.' '.$year));
        return in_array(date('Ymd'),[$secondSat,$fourthSat]);
    }
}
