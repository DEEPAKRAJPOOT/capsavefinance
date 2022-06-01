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

class OverdueReportManual implements ShouldQueue
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
        
        //Second and fourth Saturday back dated overdue report 
        if($this->isSecondFourthSaturday() && is_null($this->userId) && is_null($this->toDate)){
            $this->toDate = date('Y-m-d');
        }

        $this->reportsRepo = $reportsRepo;
        $data = $this->reportsRepo->getOverdueReportManual(['user_id' => $this->userId, 'to_date' => $this->toDate], $this->sendMail);
        $filePath = $this->downloadOverdueReport($data);
        if($this->toDate){
            $this->createOverdueReportLog($this->toDate, $this->userId, $filePath);
        }
    }

    private function createOverdueReportLog($toDate, $userId, $filePath)
    {
        OverdueReportLog::create([
            'user_id'   => $userId,
            'to_date'   => $toDate,
            'file_path' => $filePath,
        ]);
    }

    private function downloadOverdueReport($exceldata)
    {
        $rows = 5;
        $sheet =  new PHPExcel();
        $sheet->setActiveSheetIndex(0)
        ->setCellValue('A'.$rows, 'Customer Name')
        ->setCellValue('B'.$rows, 'Customer ID')
        ->setCellValue('C'.$rows, 'Anchor Name')
        ->setCellValue('D'.$rows, 'Invoice No')
        ->setCellValue('E'.$rows, 'Date of Disbursement')
        ->setCellValue('F'.$rows, 'Disbursement Amount')
        ->setCellValue('G'.$rows, 'Interest Frequency')
        ->setCellValue('H'.$rows, 'Interest Amount')
        ->setCellValue('I'.$rows, 'Invoice Due Date')
        ->setCellValue('J'.$rows, 'Virtual Account #')
        ->setCellValue('K'.$rows, 'Sanction Limit')
        ->setCellValue('L'.$rows, 'Limit Available')
        ->setCellValue('M'.$rows, 'O/s Amount')
        ->setCellValue('N'.$rows, 'Interest')
        ->setCellValue('O'.$rows, 'Over Due Days')
        ->setCellValue('P'.$rows, 'Overdue Amount')
        ->setCellValue('Q'.$rows, 'SoA Balance')
        ->setCellValue('R'.$rows, 'Grace')
        ->setCellValue('S'.$rows, 'OverDue After Grace Days')
        ->setCellValue('T'.$rows, 'Max Bucket OverDue After Grace Days')
        ->setCellValue('U'.$rows, 'Outstanding Max Bucket')
        ->setCellValue('V'.$rows, 'Maturity Days')
        ->setCellValue('W'.$rows, 'Maturity Bucket')
        ->setCellValue('X'.$rows, 'Sales Person Name');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':X'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, $rowData['cust_name'])
            ->setCellValue('B'.$rows, $rowData['customer_id'])
            ->setCellValue('C'.$rows, $rowData['anchor_name'])
            ->setCellValue('D'.$rows, $rowData['invoice_no'])
            ->setCellValue('E'.$rows, $rowData['disbursement_date'])
            ->setCellValue('F'.$rows, $rowData['disburse_amount'])
            ->setCellValue('G'.$rows, $rowData['payment_frequency'])
            ->setCellValue('H'.$rows, $rowData['interest_amount'])
            ->setCellValue('I'.$rows, $rowData['payment_due_date'])
            ->setCellValue('J'.$rows, $rowData['virtual_ac'])
            ->setCellValue('K'.$rows, number_format($rowData['client_sanction_limit'],2))
            ->setCellValue('L'.$rows, number_format($rowData['limit_available'],2))
            ->setCellValue('M'.$rows, $rowData['principalOut'])
            ->setCellValue('N'.$rows, $rowData['interestOut'])
            ->setCellValue('O'.$rows, $rowData['overdueDays'])
            ->setCellValue('P'.$rows, $rowData['overdueOut'])
            ->setCellValue('Q'.$rows, '=+M'.$rows.'+N'.$rows.'+P'.$rows)
            ->setCellValue('R'.$rows, $rowData['grace_period'])
            ->setCellValue('S'.$rows, $rowData['odDaysWithoutGrace'])
            ->setCellValue('T'.$rows, $rowData['maxBucOdDaysWithoutGrace'])
            ->setCellValue('U'.$rows, '=IF(AND(M'.$rows.'>100,T'.$rows.'>0),IF(T'.$rows.'<7,"01 - 07 Days",IF(T'.$rows.'<15,"08 - 15 Days",IF(T'.$rows.'<30,"16 - 30 Days",IF(T'.$rows.'<60,"31-60 Days",IF(T'.$rows.'<90,"61 - 90 Days","90 + Days"))))),"Not Outstanding")')
            ->setCellValue('V'.$rows, $rowData['maturityDays'])
            ->setCellValue('W'.$rows, '=IF(AND(M'.$rows.'>100,V'.$rows.'>0),IF(V'.$rows.'<7,"01 - 07 Days",IF(V'.$rows.'<15,"08 - 15 Days",IF(V'.$rows.'<30,"16 - 30 Days",IF(V'.$rows.'<60,"31-60 Days",IF(V'.$rows.'<90,"61 - 90 Days","90 + Days"))))),"Not Outstanding")')
            ->setCellValue('X'.$rows, $rowData['sales_person_name'], \PHPExcel_Cell_DataType::TYPE_STRING);
            $rows++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        $objWriter->setPreCalculateFormulas(true);

        $dirPath = 'public/report/temp/overdueReport/manual/console';
        if(!App::runningInConsole()){
            $dirPath = 'public/report/temp/overdueReport/manual/http';
        }
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.'/Overdue Report'.time().'.xlsx';
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
