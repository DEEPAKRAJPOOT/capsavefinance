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
        ->setCellValue('C'.$rows, 'Invoice No')
        ->setCellValue('D'.$rows, 'Invoice Due Date')
        ->setCellValue('E'.$rows, 'Virtual Account #')
        ->setCellValue('F'.$rows, 'Sanction Limit')
        ->setCellValue('G'.$rows, 'Limit Available')
        ->setCellValue('H'.$rows, 'O/s Amount')
        ->setCellValue('I'.$rows, 'Interest')
        ->setCellValue('J'.$rows, 'Over Due Days')
        ->setCellValue('K'.$rows, 'Overdue Amount')
        ->setCellValue('L'.$rows, 'SoA Balance')
        ->setCellValue('M'.$rows, 'Grace')
        ->setCellValue('N'.$rows, 'OverDue After Grace Days')
        ->setCellValue('O'.$rows, 'Max Bucket OverDue After Grace Days')
        ->setCellValue('P'.$rows, 'Outstanding Max Bucket')
        ->setCellValue('Q'.$rows, 'Maturity Days')
        ->setCellValue('R'.$rows, 'Maturity Bucket')
        ->setCellValue('S'.$rows, 'Sales Person Name');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':S'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
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
            ->setCellValue('H'.$rows, $rowData['principalOut'])
            ->setCellValue('I'.$rows, $rowData['interestOut'])
            ->setCellValue('J'.$rows, $rowData['overdueDays'])
            ->setCellValue('K'.$rows, $rowData['overdueOut'])
            ->setCellValue('L'.$rows, '=+H'.$rows.'+I'.$rows.'+K'.$rows)
            ->setCellValue('M'.$rows, $rowData['grace_period'])
            ->setCellValue('N'.$rows, $rowData['odDaysWithoutGrace'])
            ->setCellValue('O'.$rows, $rowData['maxBucOdDaysWithoutGrace'])
            ->setCellValue('P'.$rows, '=IF(AND(H'.$rows.'>100,O'.$rows.'>0),IF(O'.$rows.'<7,"01 - 07 Days",IF(O'.$rows.'<15,"08 - 15 Days",IF(O'.$rows.'<30,"16 - 30 Days",IF(O'.$rows.'<60,"31-60 Days",IF(O'.$rows.'<90,"61 - 90 Days","90 + Days"))))),"Not Outstanding")')
            ->setCellValue('Q'.$rows, $rowData['maturityDays'])
            ->setCellValue('R'.$rows, '=IF(AND(H'.$rows.'>100,Q'.$rows.'>0),IF(Q'.$rows.'<7,"01 - 07 Days",IF(Q'.$rows.'<15,"08 - 15 Days",IF(Q'.$rows.'<30,"16 - 30 Days",IF(Q'.$rows.'<60,"31-60 Days",IF(Q'.$rows.'<90,"61 - 90 Days","90 + Days"))))),"Not Outstanding")')
            ->setCellValue('S'.$rows, $rowData['sales_person_name'], \PHPExcel_Cell_DataType::TYPE_STRING);
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
