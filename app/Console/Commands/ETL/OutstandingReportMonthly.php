<?php

namespace App\Console\Commands\ETL;

use Carbon\Carbon;
use App\Helpers\Helper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\Lms\OutstandingReportLog;
use App\Inv\Repositories\Models\ETL\OutstandingReportMonthly as OutstandingReportMonthlyModel;

class OutstandingReportMonthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etl:report_outstanding_monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To sync outstanding report monthly to ETL database';

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
        ini_set('memory_limit', '-1');
        $outstandingReportLog = OutstandingReportLog::whereNull('user_id')->where('created_by','0')->orderBy('id','desc')->limit(1)->first();
        $filePath = $outstandingReportLog->file_path ?? NULL;
        $reportLogId = $outstandingReportLog->id ?? NULL;
        $currDate = NULL;
        if(file_exists($filePath)) {
            $currDate = Helper::utcToIst($outstandingReportLog->created_at);
            try {
                $inputFileType = IOFactory::identify($filePath);
                $objReader = IOFactory::createReader($inputFileType);
                $objSpreadsheet = $objReader->load($filePath);
            } catch (\Exception $e) {
                $this->error('Error loading file "'.pathinfo($filePath,PATHINFO_BASENAME).'": '.$e->getMessage());
                die();
            }
            //  Get worksheet dimensions
            $sheet = $objSpreadsheet->getSheet(0);
            $highestRow = $sheet->getHighestRow(); 
            $highestColumn = $sheet->getHighestColumn();

            // create array of headings
            $headingRow = 1;
            $headings = $sheet->rangeToArray('A' . $headingRow . ':' . $highestColumn . $headingRow, NULL, TRUE, FALSE);
            if (is_array($headings) && count($headings)) {
                $headings = array_values($headings[0]);
                $searchArray = ["Virtual Account #" => "Virtual Account No",
                'Invoice Level Charges Deducted (If Any)' => 'Invoice Level Charges Deducted If Any',	
                'Invoice Level Charges Applied (If Any)' => 'Invoice Level Charges Applied If Any',
                'Disbursement Method (Net or Gross)' => 'Disbursement Method Net or Gross',
                'Principal O/S' => 'Principal Outstanding',
                'Invoice level charge O/S' => 'Invoice Level Charges Outstanding',
                'Grace Days - Interest' => 'Grace Days Interest',
                'Margin O/S' => 'Margin Outstanding',
                'Grace Days - Principal' => 'Grace Days Principal',
                'ODI Interest %' => 'ODI Interest Rate',
                'ROI %' => 'ROI Rate'];
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

            $batchNo = Carbon::now()->setTimezone(config('common.timezone'))->timestamp;
            foreach($dataRecords as $dataRecord)
            {  
                OutstandingReportMonthlyModel::create([
                    'report_log_id' => $reportLogId, 
                    'Batch No' => $batchNo,
                    'UCIC ID' => '',
                    'Customer Name' => $dataRecord['Customer Name'],
                    'Customer ID' => $dataRecord['Customer ID'],
                    'Anchor Name' => $dataRecord['Anchor Name'],
                    'Sub Program Name' => $dataRecord['Sub Program Name'],
                    'Invoice No' => $dataRecord['Invoice No'],
                    'Date of Disbursement' => implode("-", array_reverse(explode("-", $dataRecord['Date of Disbursement']))),
                    'Invoice Amount' => (double)$dataRecord['Invoice Amount'],
                    'Invoice Approved Amount' => (double)$dataRecord['Invoice Approved Amount'],
                    'Margin' => (double)$dataRecord['Margin'],
                    'Upfront Interest Deducted' => (double)$dataRecord['Upfront Interest Deducted'],
                    'Invoice Level Charges Deducted If Any' => (double)$dataRecord['Invoice Level Charges Deducted If Any'],
                    'Invoice Level Charges Applied If Any' => (double)$dataRecord['Invoice Level Charges Applied If Any'],
                    'Invoice Disbursement Amount' => (double)$dataRecord['Invoice Disbursement Amount'],
                    'Product' => $dataRecord['Product'],
                    'Interest Frequency' => $dataRecord['Interest Frequency'],
                    'Interest Amount Posted' => (double)$dataRecord['Interest Amount Posted'],
                    'Disbursement Method Net or Gross' => $dataRecord['Disbursement Method Net or Gross'],
                    'Invoice Due Date' => implode("-", array_reverse(explode("-", $dataRecord['Invoice Due Date']))),
                    'Virtual Account No' => $dataRecord['Virtual Account No'],
                    'Tenure' => (int)$dataRecord['Tenure'],
                    'ROI' => $dataRecord['ROI Rate'],
                    'ODI Interest' => $dataRecord['ODI Interest Rate'],
                    'Principal Outstanding' => (double)$dataRecord['Principal Outstanding'],
                    'Margin O/S' => (double)$dataRecord['Margin Outstanding'],
                    'Interest' => (double)$dataRecord['Interest Outstanding'],
                    'Overdue Interest Posted' => (double)$dataRecord['Overdue Interest Posted'],
                    'Overdue Interest Outstanding' => (double)$dataRecord['Overdue Interest Outstanding'],
                    'Invoice Level Charges Outstanding' => (double)$dataRecord['Invoice Level Charges Outstanding'],
                    'Total Outstanding' => (double)$dataRecord['Total Outstanding'],
                    'Grace Days Interest' => (int)$dataRecord['Grace Days Interest'],
                    'Grace Days Principal' => (int)$dataRecord['Grace Days Principal'],
                    'Grace Period End Date' => implode("-", array_reverse(explode("-", $dataRecord['Invoice Due Date After Grace']))) ?? NULL,
                    'Principal Overdue' => $dataRecord['Principal Overdue'],
                    'Principal Overdue Category' => $dataRecord['Principal Overdue Category'],
                    'Principal DPD' => (int)$dataRecord['Principal DPD'],
                    'Interest DPD' => (int)$dataRecord['Interest DPD'],
                    'Final DPD' => (int)$dataRecord['Final DPD'],
                    'Outstanding Max Bucket' => $dataRecord['Outstanding Max Bucket'],
                    'Maturity Days' => (int)$dataRecord['Maturity Days'],
                    'Maturity Bucket' => $dataRecord['Maturity Bucket'],
                    'Balance Margin to be Refunded' => (double)$dataRecord['Balance Margin to be Refunded'],
                    'Balance Interest to be refunded' => (double)$dataRecord['Balance Interest to be refunded'],
                    'Balance Overdue Interest to be refunded' => (double)$dataRecord['Balance Overdue Interest to be refunded'],
                    'Sales Manager' => $dataRecord['Sales Manager'],
                    'Report Date' => $currDate ?? NULL,
                ]);
            }
            $this->info("The Outstanding Report Manual sync to database successfully.");
        } else {
            $this->info("No Outstanding Report Manual found.");
        }
    }
}
