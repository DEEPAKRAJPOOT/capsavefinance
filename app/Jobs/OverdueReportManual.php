<?php

namespace App\Jobs;
use App\Helpers\Helper;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
        ini_set('max_execution_time', 10000);
        
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
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 10000);
        $rows = 5;
        $sheet =  new Spreadsheet();
        $sheet->setActiveSheetIndex(0)
        ->setCellValue('A'.$rows, 'Customer Name')
        ->setCellValue('B'.$rows, 'Customer ID')
        ->setCellValue('C'.$rows, 'Anchor Name')
        ->setCellValue('D'.$rows, 'Invoice No')
        ->setCellValue('E'.$rows, 'Date of Disbursement')
        ->setCellValue('F'.$rows, 'Disbursement Amount')
        ->setCellValue('G'.$rows, 'Interest Frequency')
        ->setCellValue('H'.$rows, 'Interest Amount')
        ->setCellValue('I'.$rows, 'Disbursement Method (Net or Gross)')
        ->setCellValue('J'.$rows, 'Invoice Due Date')
        ->setCellValue('K'.$rows, 'Virtual Account #')
        ->setCellValue('L'.$rows, 'Sanction Limit')
        ->setCellValue('M'.$rows, 'Limit Available')
        ->setCellValue('N'.$rows, 'Total Utilization')
        ->setCellValue('O'.$rows, 'Tenure')
        ->setCellValue('P'.$rows, 'ROI')
        ->setCellValue('Q'.$rows, 'ODI Interest')
        ->setCellValue('R'.$rows, 'Principal O/S')
        ->setCellValue('S'.$rows, 'Interest')
        ->setCellValue('T'.$rows, 'Over Due Days')
        ->setCellValue('U'.$rows, 'Overdue Amount')
        ->setCellValue('V'.$rows, 'Total Outstanding')
        ->setCellValue('W'.$rows, 'Grace')
        ->setCellValue('X'.$rows, 'OverDue After Grace Days')
        ->setCellValue('Y'.$rows, 'Max Bucket OverDue After Grace Days')
        ->setCellValue('Z'.$rows, 'Outstanding Max Bucket')
        ->setCellValue('AA'.$rows, 'Maturity Days')
        ->setCellValue('AB'.$rows, 'Maturity Bucket');
        //->setCellValue('AC'.$rows, 'Sales Person Name');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':AB'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
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
            ->setCellValue('I'.$rows, $rowData['disbursement_method'])
            ->setCellValue('J'.$rows, $rowData['payment_due_date'])
            ->setCellValue('K'.$rows, $rowData['virtual_ac'])
            ->setCellValue('L'.$rows, number_format($rowData['client_sanction_limit'],2))
            ->setCellValue('M'.$rows, number_format($rowData['limit_available'],2))
            ->setCellValue('N'.$rows, $rowData['utilized_amt'])
            ->setCellValue('O'.$rows, $rowData['tenure'])
            ->setCellValue('P'.$rows, $rowData['roi'])
            ->setCellValue('Q'.$rows, $rowData['roodi'])
            ->setCellValue('R'.$rows, $rowData['principalOut'])
            ->setCellValue('S'.$rows, $rowData['interestOut'])
            ->setCellValue('T'.$rows, $rowData['overdueDays'])
            ->setCellValue('U'.$rows, $rowData['overdueOut'])
            ->setCellValue('V'.$rows, '=+R'.$rows.'+S'.$rows.'+U'.$rows)
            ->setCellValue('W'.$rows, $rowData['grace_period'])
            ->setCellValue('X'.$rows, $rowData['odDaysWithoutGrace'])
            ->setCellValue('Y'.$rows, $rowData['maxBucOdDaysWithoutGrace'])
            ->setCellValue('Z'.$rows, '= IF(AND(R'.$rows.'>100,Y'.$rows.'>0), IF(Y'.$rows.'<7,"01 - 07 Days", IF(Y'.$rows.'<15,"08 - 15 Days", IF(Y'.$rows.'<30,"16 - 30 Days", IF(Y'.$rows.'<60,"31-60 Days", IF(Y'.$rows.'<90,"61 - 90 Days","90 + Days"))))),"Not Outstanding")')
            ->setCellValue('AA'.$rows, $rowData['maturityDays'])
            ->setCellValue('AB'.$rows, '= IF(AND(R'.$rows.'>100,AA'.$rows.'>0), IF(AA'.$rows.'<7,"01 - 07 Days", IF(AA'.$rows.'<15,"08 - 15 Days", IF(AA'.$rows.'<30,"16 - 30 Days", IF(AA'.$rows.'<60,"31-60 Days", IF(AA'.$rows.'<90,"61 - 90 Days","90 + Days"))))),"Not Outstanding")');
            //->setCellValue('AC'.$rows, $rowData['sales_person_name'], \DataType::TYPE_STRING);
            $rows++;
        }

        $tmpHandle = tmpfile();
        $metaDatas = stream_get_meta_data($tmpHandle);
        $tmpFilename = $metaDatas['uri'];

        $objWriter = IOFactory::createWriter($sheet, 'Xlsx');
        $objWriter->setPreCalculateFormulas(true);

        $dirPath = 'public/report/temp/overdueReport/manual/console';
        if(!App::runningInConsole()){
            $dirPath = 'public/report/temp/overdueReport/manual/http';
        }
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $fileName = '/Overdue Report'.time().'.xlsx';
        $storage_path = Storage::path($dirPath);
        $objWriter->save($tmpFilename);
        $attributes['temp_file_path'] = $tmpFilename;
        $path = Helper::uploadAwsS3Bucket($storage_path, $attributes, $fileName);
        unlink($tmpFilename);
        return $path;
    }

    private function isSecondFourthSaturday(){
        $month = date('M');
        $year = date('Y');
        $secondSat = date('Ymd', strtotime('second sat of '.$month.' '.$year));
        $fourthSat = date('Ymd', strtotime('fourth sat of '.$month.' '.$year));
        return in_array(date('Ymd'),[$secondSat,$fourthSat]);
    }
}
