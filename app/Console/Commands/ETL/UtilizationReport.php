<?php

namespace App\Console\Commands\ETL;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\ETL\UtilizationReport as UtilizationReportModel;
use PHPExcel_IOFactory;
use Carbon\Carbon;

class UtilizationReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etl:report_utilization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To sync utilization report to ETL database';

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
        $dirPath = 'public/report/temp/utilizationReport/'.$reportDate;
        
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
                    // database insertion
                    foreach($dataRecords as $dataRecord)
                    {  
                        UtilizationReportModel::create([
                            "Anchor Name"               => $dataRecord["Anchor Name"],
                            "Program Name"              => $dataRecord["Program Name"],
                            "Sub Program Name"          => $dataRecord["Sub Program Name"],
                            "No of Clients Sanctioned"  => $dataRecord["No of Clients Sanctioned"],
                            "No of Over Due Customer"   => $dataRecord["No of Over Due Customer"],
                            "Total Over Due Amount"     => str_replace(',', '', $dataRecord["Total Over Due Amount"]),
                            "Client Name"               => $dataRecord["Client Name"],
                            "Customer ID"               => $dataRecord["Customer ID"],
                            "Virtual Account No"        => $dataRecord["Virtual Account No"],
                            "Client Sanction Limit"     => str_replace(',', '', $dataRecord["Client Sanction Limit"]),
                            "Limit Utilized Limit"      => str_replace(',', '', $dataRecord["Limit Utilized Limit"]),
                            "Available Limit"           => str_replace(',', '', $dataRecord["Available Limit"]),
                            "Expiry Date"               => Carbon::parse(str_replace('/', '-', $dataRecord["Expiry Date"]))->format('Y-m-d'),
                            "Sales Person Name"         => $dataRecord["Sales Person Name"],
                            "Invoice No"                => $dataRecord["Invoice No"],
                            "Invoice Date"              => Carbon::parse(str_replace('/', '-', $dataRecord["Invoice Date"]))->format('Y-m-d'),
                            "Invoice Amount"            => str_replace(',', '', $dataRecord["Invoice Amount"]),
                            "Invoice Approved"          => str_replace(',', '', $dataRecord["Invoice Approved"]),
                            "Margin Amount"             => str_replace(',', '', $dataRecord["Margin Amount"]),
                            "Amount Disbursed"          => str_replace(',', '', $dataRecord["Amount Disbursed"]),
                            "Principal OverDue Days"    => $dataRecord["Principal OverDue Days"],
                            "Principal OverDue Amount"  => str_replace(',', '', $dataRecord["Principal OverDue Amount"]),
                            "Over Due Days"             => $dataRecord["Over Due Days"],
                            "Over Due Interest Amount"  => str_replace(',', '', $dataRecord["Over Due Interest Amount"])
                        ]);
                    }
                }        
            }
            $this->info("The Utilization Report sync to database successfully.");
        } else {
            $this->info("No Utilization Report found.");
        }
    }
}
