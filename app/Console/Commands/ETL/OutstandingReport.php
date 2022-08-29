<?php

namespace App\Console\Commands\ETL;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\ETL\OutstandingReport as OutstandingReportModel;
use PHPExcel_IOFactory;
use Carbon\Carbon;

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
        ini_set('memory_limit', '-1');
        $reportDate = now()->format('Ymd'); // now()->subDays(1)->format('Ymd');
        $dirPath = 'public/report/temp/outstandingReport/'.$reportDate;
        
        if (Storage::exists($dirPath)) {
            $files = Storage::disk('local')->files($dirPath);
            foreach($files as $file)
            {
                $filePath = storage_path('app/'.$file);
                if (file_exists($filePath) && $file == $dirPath."/Consolidated Report.xlsx") {
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
                    $headingRow = 1;
                    $headings = $sheet->rangeToArray('A' . $headingRow . ':' . $highestColumn . $headingRow, NULL, TRUE, FALSE);
                    if (is_array($headings) && count($headings)) {
                        $headings = array_values($headings[0]);
                        $searchArray = ["# of Clients sanctioned" => "No of Clients Sanctioned",
                                        "# of Overdue Customers" => "No of Over Due Customer", 
                                        "Virtual Account #" => "Virtual Account No",
                                        "Invoice #" => "Invoice No"
                                    ];
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

                    OutstandingReportModel::truncate();
                    // database insertion
                    foreach($dataRecords as $dataRecord)
                    {  
                        OutstandingReportModel::create([
                            "Customer Name"               => $dataRecord["Customer Name"],
                            "Customer ID"               => $dataRecord["Customer ID"],
                            "Anchor Name"               => $dataRecord["Anchor Name"],
                            "Invoice No"              => $dataRecord["Invoice No"],
                            "Date of Disbursement"          => $dataRecord["Date of Disbursement"],
                            "Invoice Amount"  => str_replace(',', '', $dataRecord["Invoice Amount"]),
                            "Invoice Approved Amount"   => str_replace(',', '', $dataRecord["Invoice Approved Amount"]),
                            "Margin"     =>  str_replace(',', '', $dataRecord["Margin"]),
                            "Upfront Interest Deducted"  => $dataRecord["Upfront Interest Deducted"],
                            "Invoice Level Charges Deducted (If Any)" => $dataRecord["Invoice Level Charges Deducted (If Any)"],
                            "Invoice Level Charges Applied (If Any)" => $dataRecord["Invoice Level Charges Applied (If Any)"],
                            "Disbursement Amount"  => str_replace(',', '', $dataRecord["Disbursement Amount"]),
                            "Interest Frequency"  => $dataRecord["Interest Frequency"],
                            "Interest Amount"  => str_replace(',', '', $dataRecord["Interest Amount"]),
                            "Disbursement Method (Net or Gross)"   => $dataRecord["Disbursement Method (Net or Gross)"],
                            "Invoice Due Date"         => Carbon::parse(str_replace('/', '-',$dataRecord["Invoice Due Date"]))->format('Y-m-d'),
                            "Virtual Account #"                => $dataRecord["Virtual Account #"],
                            "Tenure"              => $dataRecord["Tenure"],
                            "ROI"            => $dataRecord["ROI"],
                            "ODI Interest"          => $dataRecord["ODI Interest"],
                            "Principal O/S"             => $dataRecord["Principal O/S"],
                            "Interest"          => $dataRecord["Interest"],
                            "Overdue Interest Posted"    => $dataRecord["Overdue Interest Posted"],
                            "Overdue Amount"  => str_replace(',', '', $dataRecord["Overdue Amount"]),
                            "Invoice level charge O/s"             => $dataRecord["Invoice level charge O/s"],
                            "Total Outstanding"  => str_replace(',', '', $dataRecord["Total Outstanding"]),
                            "Grace Days - Interest"     => $dataRecord["Grace Days - Interest"],
                            "Grace"   => $dataRecord["Grace"],
                            "Principle Overdue"      => $dataRecord["Principle Overdue"],
                            "Principle Overdue Category"    => $dataRecord["Principle Overdue Category"],
                            "Principle DPD"   => $dataRecord["Principle DPD"],
                            "Interest DPD"   => $dataRecord["Interest DPD"],
                            "Final DPD"   => $dataRecord["Final DPD"],
                            "Outstanding Max Bucket"   => $dataRecord["Outstanding Max Bucket"],
                            "Maturity Days"   => $dataRecord["Maturity Days"],
                            "Maturity Bucket"   => $dataRecord["Maturity Bucket"],
                            "Balance Margin to be Refunded"   => $dataRecord["Balance Margin to be Refunded"],
                            "Balance Interest to be refunded"   => $dataRecord["Balance Interest to be refunded"],
                            "Balance Overdue Interest to be refunded"   => $dataRecord["Balance Overdue Interest to be refunded"],
                        ]);
                    }
                }        
            }
            $this->info("The Outstanding Report sync to database successfully.");
        } else {
            $this->info("No Outstanding Report found.");
        }
    }
}
