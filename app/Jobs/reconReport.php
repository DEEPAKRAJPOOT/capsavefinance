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
use App\Inv\Repositories\Models\Lms\ReconReportLog;

class ReconReport implements ShouldQueue
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
            $this->toDate = Carbon::now()->setTimezone(config('common.timezone'))->format('Y-m-d');
            $this->reportsRepo = $reportsRepo;
            $data = $this->reportsRepo->getReconReportData(['user_id' => $this->userId]);
            dd($data);
            $filePath = $this->downloadReconReport($data);
            
            if($this->toDate && $this->logId){
                $this->createReconReportLog($this->toDate, $this->userId, $filePath, $this->logId);
            }
            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollback();
        } 
    }

    private function createReconReportLog($toDate, $userId, $filePath, $logId)
    {
        ReconReportLog::updateOrCreate(['id' => $logId],[
            'user_id'   => $userId,
            'to_date'   => $toDate,
            'file_path' => $filePath,
        ]);
    }

    private function downloadReconReport($exceldata)
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
        ->setCellValue('E'.$rows, 'Date of Disbursement');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, $rowData['custName'])
            ->setCellValue('B'.$rows, $rowData['customerId'])
            ->setCellValue('C'.$rows, $rowData['anchorName'])
            ->setCellValue('D'.$rows, $rowData['invoiceNo'])
            ->setCellValue('E'.$rows, $rowData['disbursementDate']);
            $rows++;
        }

        $dirPath = 'public/report/temp/ReconReport/manual/console';
        if(!App::runningInConsole()){
            $dirPath = 'public/report/temp/ReconReport/manual/http';
        }
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        $filename = 'Recon Report'.'_'.Carbon::now()->setTimezone(config('common.timezone'))->format('Ymd_hisA').'.xlsx';
        $filePath = $storage_path.'/'.$filename;
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
        header('Content-Disposition: attachment;filename="'.$filePath.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
       
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
