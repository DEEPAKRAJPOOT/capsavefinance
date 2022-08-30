<?php

namespace App\Console\Commands\ETL;

use Carbon\Carbon;
use PHPExcel_IOFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\Lms\OutstandingReportLog;
use App\Inv\Repositories\Models\ETL\OutstandingReport as OutstandingReportModel;

class OutstandingReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etl:report_outstanding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To sync outstanding report to ETL database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $reportDate = now()->format('Y-m-d');
        ini_set('memory_limit', '-1'); // now()->subDays(1)->format('Ymd');
        $filePath = OutstandingReportLog::whereNull('user_id')->whereDate('to_date',$reportDate)->where('created_by','0')->orderBy('id','desc')->limit(1)->first()->file_path;

        if(file_exists($filePath)) {
            try {
                $inputFileType = PHPExcel_IOFactory::identify($filePath);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($filePath);
            } catch (\Exception $e) {
                die('Error loading file "'.pathinfo($filePath,PATHINFO_BASENAME).'": '.$e->getMessage());
            }
            //  Get worksheet dimensions
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); 
            $highestColumn = $sheet->getHighestColumn();

            // create array of headings
            $headingRow = 5;
            $headings = $sheet->rangeToArray('A' . $headingRow . ':' . $highestColumn . $headingRow, NULL, TRUE, FALSE);
            if (is_array($headings) && count($headings)) {
                $headings = array_values($headings[0]);
                $searchArray = ["Virtual Account #" => "Virtual Account No",
                'Invoice Level Charges Deducted (If Any)' => 'Invoice Level Charges Deducted If Any',	
                'Invoice Level Charges Applied (If Any)' => 'Invoice Level Charges Applied If Any',
                'Disbursement Method (Net or Gross)' => 'Disbursement Method Net or Gross',
                'Principal O/S' => 'Principal Outstanding',
                'Invoice level charge O/S' => 'Invoice Level Charges Outstanding',
                'Grace Days - Interest' => 'Grace Days Interest'];
                foreach($searchArray as $key => $searchArr)
                {
                    $searchKey = array_search($key, $headings);
                    if ($searchKey) {
                        $headings[$searchKey] = $searchArr;
                    }
                }
            }    
            // create array of row data
            $dataRow = $headingRow + 1;
            $dataRecords = [];
            for ($row = $dataRow; $row <= $highestRow; $row++){ 
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                array_push($dataRecords, array_combine($headings, $rowData[0]));
            }

            $batchNo = strtotime("now");
            foreach($dataRecords as $dataRecord)
            {  
                OutstandingReportModel::create([
                    'Batch No' => $batchNo,
                    'Customer Name' => $dataRecord['Customer Name'],
                    'Customer ID' => $dataRecord['Customer ID'],
                    'Anchor Name' => $dataRecord['Anchor Name'],
                    'Invoice No' => $dataRecord['Invoice No'],
                    'Date of Disbursement' => implode("-", array_reverse(explode("-", $dataRecord['Date of Disbursement']))),
                    'Invoice Amount' => (double)$dataRecord['Invoice Amount'],
                    'Invoice Approved Amount' => (double)$dataRecord['Invoice Approved Amount'],
                    'Margin' => (double)$dataRecord['Margin'],
                    'Upfront Interest Deducted' => (double)$dataRecord['Upfront Interest Deducted'],
                    'Invoice Level Charges Deducted If Any' => (double)$dataRecord['Invoice Level Charges Deducted If Any'],
                    'Invoice Level Charges Applied If Any' => (double)$dataRecord['Invoice Level Charges Applied If Any'],
                    'Disbursement Amount' => (double)$dataRecord['Disbursement Amount'],
                    'Interest Frequency' => $dataRecord['Interest Frequency'],
                    'Interest Amount' => (double)$dataRecord['Interest Amount'],
                    'Disbursement Method Net or Gross' => $dataRecord['Disbursement Method Net or Gross'],
                    'Invoice Due Date' => implode("-", array_reverse(explode("-", $dataRecord['Invoice Due Date']))),
                    'Virtual Account No' => $dataRecord['Virtual Account No'],
                    'Tenure' => (int)$dataRecord['Tenure'],
                    'ROI' => (float)$dataRecord['ROI'],
                    'ODI Interest' => (float)$dataRecord['ODI Interest'],
                    'Principal Outstanding' => (double)$dataRecord['Principal Outstanding'],
                    'Interest' => (double)$dataRecord['Interest Outstanding'],
                    'Overdue Interest Posted' => (double)$dataRecord['Overdue Interest Posted'],
                    'Overdue Amount' => (double)$dataRecord['Overdue Amount'],
                    'Invoice Level Charges Outstanding' => (double)$dataRecord['Invoice Level Charges Outstanding'],
                    'Total Outstanding' => (double)$dataRecord['Total Outstanding'],
                    'Grace Days Interest' => (int)$dataRecord['Grace Days Interest'],
                    'Grace' => (int)$dataRecord['Grace'],
                    'Principle Overdue' => (double)$dataRecord['Principle Overdue'],
                    'Principle Overdue Category' => $dataRecord['Principle Overdue Category'],
                    'Principle DPD' => (int)$dataRecord['Principle DPD'],
                    'Interest DPD' => (int)$dataRecord['Interest DPD'],
                    'Final DPD' => (int)$dataRecord['Final DPD'],
                    'Outstanding Max Bucket' => $dataRecord['Outstanding Max Bucket'],
                    'Maturity Days' => (int)$dataRecord['Maturity Days'],
                    'Maturity Bucket' => $dataRecord['Maturity Bucket'],
                    'Balance Margin to be Refunded' => (double)$dataRecord['Balance Margin to be Refunded'],
                    'Balance Interest to be refunded' => (double)$dataRecord['Balance Interest to be refunded'],
                    'Balance Overdue Interest to be refunded' => (double)$dataRecord['Balance Overdue Interest to be refunded']
                ]);
            }
            $this->info("The Outstanding Report sync to database successfully.");
        } else {
            $this->info("No Outstanding Report found.");
        }
    }
}
