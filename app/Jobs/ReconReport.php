<?php

namespace App\Jobs;
use App\Helpers\Helper;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
            $data = $this->reportsRepo->getReconReportData(['user_id' => $this->userId,'to_date' => $this->toDate], $this->sendMail);
            $filePath = $this->downloadReconReport($data);
            
            if($this->toDate && $this->logId){
                $this->createReconReportLog($this->toDate, $this->userId, $filePath, $this->logId);
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        } 
    }

    private function createReconReportLog($toDate, $userId, $filePath, $logId)
    {
        try{
            ReconReportLog::updateOrCreate(['id' => $logId],[
                'user_id'   => $userId,
                'to_date'   => $toDate,
                'file_path' => $filePath,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        } 
    }

    private function downloadReconReport($exceldata)
    {
        try{
            ini_set("memory_limit", "-1");
            ini_set('max_execution_time', 10000);
            $rows = 1;
            $sheet =  new Spreadsheet();
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Customer Id')
            ->setCellValue('B'.$rows, 'SOA Balance')
            ->setCellValue('C'.$rows, 'Charge Outstanding')
            ->setCellValue('D'.$rows, 'Non Factored')
            ->setCellValue('E'.$rows, 'Charge Refund')
            ->setCellValue('F'.$rows, 'Customer Outstanding');
            $sheet->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
            $rows++;
            foreach($exceldata as $key => $rowData){
                $sheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$rows, $key)
                ->setCellValue('B'.$rows, isset($rowData['SOA_Outstanding']) ? $rowData['SOA_Outstanding'] : 0)
                ->setCellValue('C'.$rows, isset($rowData['Outstanding_Amount']) ? $rowData['Outstanding_Amount'] : 0)
                ->setCellValue('D'.$rows, isset($rowData['outstandingAmount']) ? $rowData['outstandingAmount'] : 0)
                ->setCellValue('E'.$rows, isset($rowData['Refundable']) ? $rowData['Refundable'] : 0)
                ->setCellValue('F'.$rows, isset($rowData['customer_outstanding_amount']) ? $rowData['customer_outstanding_amount'] : 0);
                
                $rows++;
            }

            $dirPath = 'public/report/temp/ReconReport/manual/console';
            if(!App::runningInConsole()){
                $dirPath = 'public/report/temp/ReconReport/manual/http';
            }
            if (!Storage::exists($dirPath)) {
                Storage::makeDirectory($dirPath);
            }
            $storage_path = Storage::path($dirPath);
            $filename = 'Recon Report'.'_'.Carbon::now()->setTimezone(config('common.timezone'))->format('Ymd_hisA').'.xlsx';
            $filePath = $storage_path.'/'.$filename;
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
            header('Content-Disposition: attachment;filename="'.$filePath.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            
            $tmpHandle = tmpfile();
            $metaDatas = stream_get_meta_data($tmpHandle);
            $tmpFilename = $metaDatas['uri'];

            $objWriter = IOFactory::createWriter($sheet, 'Xlsx');
        
            $objWriter->save($tmpFilename);
            $attributes['temp_file_path'] = $tmpFilename;
            $path = Helper::uploadAwsS3Bucket($storage_path, $attributes, $filename);
            unlink($tmpFilename);
            return $path;
        } catch (\Throwable $th) {
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
        } catch (\Throwable $th) {
            throw $th;
        } 
    }
}
