<?php

namespace App\Jobs;
use Helpers;
use PHPExcel;
use Carbon\Carbon;
use PHPExcel_IOFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Inv\Repositories\Contracts\ReportInterface;
use App\Inv\Repositories\Models\Master\EmailTemplate;
use App\Inv\Repositories\Models\Lms\OutstandingReportLog;

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

        DB::beginTransaction();
        try {
            ini_set("memory_limit", "-1");
            ini_set('max_execution_time', 10000);
            $this->toDate = Carbon::now()->setTimezone(config('common.timezone'))->format('Y-m-d');
            $this->reportsRepo = $reportsRepo;
            $data = $this->reportsRepo->getOutstandingReportManual(['user_id' => $this->userId, 'to_date' => $this->toDate], $this->sendMail);
            $filePath = $this->downloadOutstandingReport($data);
            unset($data);
            if($this->toDate && $this->logId){
                $this->createOutstandingReportLog($this->toDate, $this->userId, $filePath, $this->logId);
            }
            DB::commit();
            return true;
        } catch (\Throwable $ex) {
            DB::rollback();
            throw $ex;
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
        try{
            ini_set("memory_limit", "-1");
            ini_set('max_execution_time', 10000);
            $rows = 1;
            $sheet =  new PHPExcel();
            $sheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$rows, 'UCIC ID')
                ->setCellValue('B'.$rows, 'Customer Name')
                ->setCellValue('C'.$rows, 'Customer ID')
                ->setCellValue('D'.$rows, 'Anchor Name')
                ->setCellValue('E'.$rows, 'Sub Program Name')
                ->setCellValue('F'.$rows, 'Invoice No')
                ->setCellValue('G'.$rows, 'Date of Disbursement')
                ->setCellValue('H'.$rows, 'Invoice Amount')
                ->setCellValue('I'.$rows, 'Invoice Approved Amount')
                ->setCellValue('J'.$rows, 'Margin')
                ->setCellValue('K'.$rows, 'Upfront Interest Deducted')
                ->setCellValue('L'.$rows, 'Invoice Level Charges Deducted (If Any)')
                ->setCellValue('M'.$rows, 'Invoice Level Charges Applied (If Any)')
                ->setCellValue('N'.$rows, 'Invoice Disbursement Amount')
                ->setCellValue('O'.$rows, 'Product')
                ->setCellValue('P'.$rows, 'Interest Frequency')
                ->setCellValue('Q'.$rows, 'Interest Amount Posted')
                ->setCellValue('R'.$rows, 'Disbursement Method (Net or Gross)')
                ->setCellValue('S'.$rows, 'Invoice Due Date')
                ->setCellValue('T'.$rows, 'Virtual Account #')
                ->setCellValue('U'.$rows, 'Tenure')
                ->setCellValue('V'.$rows, 'ROI %')
                ->setCellValue('W'.$rows, 'ODI Interest %')
                ->setCellValue('X'.$rows, 'Principal O/S')
                ->setCellValue('Y'.$rows, 'Margin O/S')
                ->setCellValue('Z'.$rows, 'Interest Outstanding')
                ->setCellValue('AA'.$rows, 'Overdue Interest Posted')
                ->setCellValue('AB'.$rows, 'Overdue Interest Outstanding')
                ->setCellValue('AC'.$rows, 'Invoice level charge O/S')
                ->setCellValue('AD'.$rows, 'Total Outstanding')
                ->setCellValue('AE'.$rows, 'Grace Days - Interest')
                ->setCellValue('AF'.$rows, 'Grace Days - Principal')
                ->setCellValue('AG'.$rows, 'Invoice Due Date After Grace')
                ->setCellValue('AH'.$rows, 'Principal Overdue')
                ->setCellValue('AI'.$rows, 'Principal Overdue Category')
                ->setCellValue('AJ'.$rows, 'Principal DPD')
                ->setCellValue('AK'.$rows, 'Interest DPD')
                ->setCellValue('AL'.$rows, 'Overdue DPD')
                ->setCellValue('AM'.$rows, 'Final DPD')
                ->setCellValue('AN'.$rows, 'Outstanding Max Bucket')
                ->setCellValue('AO'.$rows, 'Maturity Days')
                ->setCellValue('AP'.$rows, 'Maturity Bucket')
                ->setCellValue('AQ'.$rows, 'Balance Margin to be Refunded')
                ->setCellValue('AR'.$rows, 'Balance Interest to be refunded')
                ->setCellValue('AS'.$rows, 'Balance Overdue Interest to be refunded')
                ->setCellValue('AT'.$rows, 'Sales Manager');
            $sheet->getActiveSheet()->getStyle('A'.$rows.':AT'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
            $rows++;
            foreach($exceldata as $key => $rowData){
                $sheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$rows, '')
                ->setCellValue('B'.$rows, $rowData['custName'])
                ->setCellValue('C'.$rows, $rowData['customerId'])
                ->setCellValue('D'.$rows, $rowData['anchorName'])
                ->setCellValue('E'.$rows, $rowData['SubPrgmName'])
                ->setCellValue('F'.$rows, $rowData['invoiceNo'])
                ->setCellValue('G'.$rows, $rowData['disbursementDate'])
                ->setCellValue('H'.$rows, $rowData['invoiceAmt'])
                ->setCellValue('I'.$rows, $rowData['invoiceApproveAmount'])
                ->setCellValue('J'.$rows, $rowData['marginPosted'])
                ->setCellValue('K'.$rows, $rowData['upfrontInterest'])
                ->setCellValue('L'.$rows, $rowData['chargeDeduction'])
                ->setCellValue('M'.$rows, $rowData['invoiceLevelChrg'])
                ->setCellValue('N'.$rows, $rowData['disburseAmount'])
                ->setCellValue('O'.$rows, $rowData['product'])
                ->setCellValue('P'.$rows, $rowData['paymentFrequency'])
                ->setCellValue('Q'.$rows, $rowData['interestPosted'])
                ->setCellValue('R'.$rows, $rowData['disbursementMethod'])
                ->setCellValue('S'.$rows, $rowData['paymentDueDate'])
                ->setCellValue('T'.$rows, $rowData['virtualAc'])
                ->setCellValue('U'.$rows, $rowData['tenure'])
                ->setCellValue('V'.$rows, $rowData['roi'])
                ->setCellValue('W'.$rows, $rowData['odi'])
                ->setCellValue('X'.$rows, $rowData['principalOut'])
                ->setCellValue('Y'.$rows, $rowData['marginOut'])
                ->setCellValue('Z'.$rows, $rowData['interestOut'])
                ->setCellValue('AA'.$rows, $rowData['overduePosted'])
                ->setCellValue('AB'.$rows, $rowData['overdueOut'])
                ->setCellValue('AC'.$rows, $rowData['invoiceLevelChrgOut'])
                ->setCellValue('AD'.$rows, $rowData['totalOutStanding'])
                ->setCellValue('AE'.$rows, $rowData['intGraceDays'])
                ->setCellValue('AF'.$rows, $rowData['principalGraceDays'])
                ->setCellValue('AG'.$rows, $rowData['gracePeriodEndDate'])
                ->setCellValue('AH'.$rows, $rowData['principalOverdue'])
                ->setCellValue('AI'.$rows, $rowData['principalOverdueCategory'])
                ->setCellValue('AJ'.$rows, $rowData['principalDPD'])
                ->setCellValue('AK'.$rows, $rowData['interestDPD'])
                ->setCellValue('AL'.$rows, $rowData['overdueDPD'])
                ->setCellValue('AM'.$rows, $rowData['finalDPD'])
                ->setCellValue('AN'.$rows, $rowData['outstandingMaxBucket'])
                ->setCellValue('AO'.$rows, $rowData['maturityDays'])
                ->setCellValue('AP'.$rows, $rowData['maturityBucket'])
                ->setCellValue('AQ'.$rows, $rowData['marginToRefunded'])
                ->setCellValue('AR'.$rows, $rowData['interestToRefunded'])
                ->setCellValue('AS'.$rows, $rowData['overdueToRefunded'])
                ->setCellValue('AT'.$rows, $rowData['salesManager']);
                $rows++;
                unset($exceldata[$key]);
            }

            $dirPath = 'public/report/temp/OutstandingReport/manual/console';
            if(!App::runningInConsole()){
                $dirPath = 'public/report/temp/OutstandingReport/manual/http';
            }
            if (!Storage::exists($dirPath)) {
                Storage::makeDirectory($dirPath);
            }
            $storage_path = storage_path('app/'.$dirPath);
            $filename = 'Invoice Outstanding Report'.'_'.Carbon::now()->setTimezone(config('common.timezone'))->format('Ymd_hisA').'.xlsx';
            $filePath = $storage_path.'/'.$filename;
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
            header('Content-Disposition: attachment;filename="'.$filePath.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            
            $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        
            $objWriter->save($filePath);
            $s3path = env('S3_BUCKET_DIRECTORY_PATH').'/report/OutstandingReport/manual/console';
            if(!App::runningInConsole()){
                $s3path = env('S3_BUCKET_DIRECTORY_PATH').'/report/OutstandingReport/manual/http';
            }
            $attributes['temp_file_path'] = $filePath;
            $path = Helper::uploadAwsS3Bucket($s3path, $attributes, $filename);
            return $path;
        } catch (\Throwable $ex) {
            throw $th;
        } 
    }

    private function isSecondFourthSaturday(){
        try{
            $month = date('M');
            $year = date('Y');
            $secondSat = date('Ymd', strtotime('second sat of '.$month.' '.$year));
            $fourthSat = date('Ymd', strtotime('fourth sat of '.$month.' '.$year));
            return in_array(date('Ymd'),[$secondSat,$fourthSat]);
        } catch (\Throwable $ex) {
            throw $th;
        } 
    }
}
