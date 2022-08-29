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
        ->setCellValue('J'.$rows, 'Invoice Level Charges Deducted (If Any)
        ')
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
        //->setCellValue('AC'.$rows, 'Sales Person Name');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':AM'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, $rowData['cust_name'])
            ->setCellValue('B'.$rows, $rowData['customer_id'])
            ->setCellValue('C'.$rows, $rowData['anchor_name'])
            ->setCellValue('D'.$rows, $rowData['invoice_no'])
            ->setCellValue('E'.$rows, $rowData['disbursement_date'])
            ->setCellValue('F'.$rows, $rowData['invoice_amt'])
            ->setCellValue('G'.$rows, $rowData['invoice_approve_amount'])
            ->setCellValue('H'.$rows, $rowData['margin_amt'])
            ->setCellValue('I'.$rows, $rowData['total_interest'])
            ->setCellValue('J'.$rows, 0)
            ->setCellValue('K'.$rows, $rowData['invoice_level_charge_applied'])
            ->setCellValue('L'.$rows, $rowData['disburse_amount'])
            // ->setCellValue('L'.$rows, number_format($rowData['client_sanction_limit'],2))
            // ->setCellValue('M'.$rows, number_format($rowData['limit_available'],2))
            ->setCellValue('M'.$rows, $rowData['payment_frequency'])
            ->setCellValue('N'.$rows, $rowData['interest_amount'])
            ->setCellValue('O'.$rows, $rowData['disbursement_method'])
            ->setCellValue('P'.$rows, $rowData['payment_due_date'])
            ->setCellValue('Q'.$rows, $rowData['virtual_ac'])
            ->setCellValue('R'.$rows, $rowData['tenure'])
            ->setCellValue('S'.$rows, $rowData['roi'])
            ->setCellValue('T'.$rows, $rowData['odi_interest'])
            ->setCellValue('U'.$rows, $rowData['principalOut'])
            ->setCellValue('V'.$rows, $rowData['interest_os'])
            ->setCellValue('W'.$rows, $rowData['overdueInterestPosted'])
            ->setCellValue('X'.$rows, $rowData['overdueOut'])
            ->setCellValue('Y'.$rows, $rowData['invoice_levelcharge'])
            ->setCellValue('Z'.$rows, $rowData['totalOutStanding'])
            ->setCellValue('AA'.$rows, 'N/A')
            ->setCellValue('AB'.$rows, $rowData['grace_period'])
            ->setCellValue('AC'.$rows, 'N/A')
            ->setCellValue('AD'.$rows, $rowData['principal_overdue_category'])
            ->setCellValue('AE'.$rows, $rowData['principal_dpd'])
            ->setCellValue('AF'.$rows, $rowData['interest_dpd'])
            ->setCellValue('AG'.$rows, $rowData['final_dpd'])
            ->setCellValue('AH'.$rows, $rowData['outstanding_max_bucket'])
            ->setCellValue('AI'.$rows, $rowData['maturityDays'])
            ->setCellValue('AJ'.$rows, $rowData['maturity_bucket'])
            ->setCellValue('AK'.$rows, $rowData['margin_to_refunded'])
            ->setCellValue('AL'.$rows, $rowData['interest_to_refunded'])
            ->setCellValue('AM'.$rows, $rowData['overdueinterest_to_refunded']);
            //->setCellValue('AC'.$rows, $rowData['sales_person_name'], \PHPExcel_Cell_DataType::TYPE_STRING);
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
