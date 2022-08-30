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
use App\Inv\Repositories\Models\Lms\OverdueReportLog;
use App\Inv\Repositories\Models\Master\EmailTemplate;

class OutstandingReportManual implements ShouldQueue
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
        ini_set('max_execution_time', 10000);
        
        //Second and fourth Saturday back dated overdue report 
        if($this->isSecondFourthSaturday() && is_null($this->userId) && is_null($this->toDate)){
            $this->toDate = date('Y-m-d');
        }

        $this->reportsRepo = $reportsRepo;
        //if to date== current date OR to date is null getOutstandingReportManual else getOutstandingReportManualbackDate
        if($this->toDate === date('Y-m-d')){
            $data = $this->reportsRepo->getOutstandingReportManual(['user_id' => $this->userId, 'to_date' => $this->toDate], $this->sendMail);
        }else if($this->toDate < date('Y-m-d')){
            $data = $this->reportsRepo->getBackDateOutstandingReportManual(['user_id' => $this->userId, 'to_date' => $this->toDate], $this->sendMail);
        }
        $filePath = $this->downloadOutstandingReport($data);
        if($this->toDate){
            $this->createOutstandingReportLog($this->toDate, $this->userId, $filePath);
        }
    }

    private function createOutstandingReportLog($toDate, $userId, $filePath)
    {
        OverdueReportLog::create([
            'user_id'   => $userId,
            'to_date'   => $toDate,
            'file_path' => $filePath,
        ]);
    }

    private function downloadOutstandingReport($exceldata)
    {
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 10000);
        $rows = 5;
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
        ->setCellValue('L'.$rows, 'Disbursement Amount')
        ->setCellValue('M'.$rows, 'Interest Frequency')
        ->setCellValue('N'.$rows, 'Interest Amount')
        ->setCellValue('O'.$rows, 'Disbursement Method (Net or Gross)')
        ->setCellValue('P'.$rows, 'Invoice Due Date')
        ->setCellValue('Q'.$rows, 'Virtual Account #')
        ->setCellValue('R'.$rows, 'Tenure')
        ->setCellValue('S'.$rows, 'ROI')
        ->setCellValue('T'.$rows, 'ODI Interest')
        ->setCellValue('U'.$rows, 'Principal O/S')
        ->setCellValue('V'.$rows, 'Interest')
        ->setCellValue('W'.$rows, 'Overdue Interest Posted')
        ->setCellValue('X'.$rows, 'Overdue Amount')
        ->setCellValue('Y'.$rows, 'Invoice level charge O/s')
        ->setCellValue('Z'.$rows, 'Total Outstanding')
        ->setCellValue('AA'.$rows, 'Grace Days - Interest')
        ->setCellValue('AB'.$rows, 'Grace')
        ->setCellValue('AC'.$rows, 'Principle Overdue')
        ->setCellValue('AD'.$rows, 'Principle Overdue Category')
        ->setCellValue('AE'.$rows, 'Principle DPD')
        ->setCellValue('AF'.$rows, 'Interest DPD')
        ->setCellValue('AG'.$rows, 'Final DPD')
        ->setCellValue('AH'.$rows, 'Outstanding Max Bucket')
        ->setCellValue('AI'.$rows, 'Maturity Days')
        ->setCellValue('AJ'.$rows, 'Maturity Bucket')
        ->setCellValue('AK'.$rows, 'Balance Margin to be Refunded')
        ->setCellValue('AL'.$rows, 'Balance Interest to be refunded')
        ->setCellValue('AM'.$rows, 'Balance Overdue Interest to be refunded');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':AM'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
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
            ->setCellValue('M'.$rows, $rowData['paymentFrequency'])
            ->setCellValue('N'.$rows, $rowData['interestPosted'])
            ->setCellValue('O'.$rows, $rowData['disbursementMethod'])
            ->setCellValue('P'.$rows, $rowData['paymentDueDate'])
            ->setCellValue('Q'.$rows, $rowData['virtualAc'])
            ->setCellValue('R'.$rows, $rowData['tenure'])
            ->setCellValue('S'.$rows, $rowData['roi'])
            ->setCellValue('T'.$rows, $rowData['odi'])
            ->setCellValue('U'.$rows, $rowData['principalOut'])
            ->setCellValue('V'.$rows, $rowData['interestOut'])
            ->setCellValue('W'.$rows, $rowData['overduePosted'])
            ->setCellValue('X'.$rows, $rowData['overdueOut'])
            ->setCellValue('Y'.$rows, $rowData['invoiceLevelChrgOut'])
            ->setCellValue('Z'.$rows, $rowData['totalOutStanding'])
            ->setCellValue('AA'.$rows, $rowData['intGraceDays'])
            ->setCellValue('AB'.$rows, $rowData['principalGraceDays'])
            ->setCellValue('AC'.$rows, $rowData['principalOverdue'])
            ->setCellValue('AD'.$rows, $rowData['principalOverdueCategory'])
            ->setCellValue('AE'.$rows, $rowData['principalDPD'])
            ->setCellValue('AF'.$rows, $rowData['interestPDP'])
            ->setCellValue('AG'.$rows, $rowData['finalDPD'])
            ->setCellValue('AH'.$rows, $rowData['outstandingMaxBucket'])
            ->setCellValue('AI'.$rows, $rowData['maturityDays'])
            ->setCellValue('AJ'.$rows, $rowData['maturityBucket'])
            ->setCellValue('AK'.$rows, $rowData['marginToRefunded'])
            ->setCellValue('AL'.$rows, $rowData['interestToRefunded'])
            ->setCellValue('AM'.$rows, $rowData['overdueToRefunded']);
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
        $filePath = $storage_path.'/Outstanding Report'.time().'.xlsx';
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
