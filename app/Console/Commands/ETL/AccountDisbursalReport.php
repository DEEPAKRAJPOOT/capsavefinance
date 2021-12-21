<?php

namespace App\Console\Commands\ETL;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\ETL\AccountDisbursalReport as AccountDisbursalReportModel;
use PHPExcel_IOFactory;
use Carbon\Carbon;

class AccountDisbursalReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etl:report_account_disbursal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To sync account disbursal report to ETL database';

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
        ini_set("memory_limit", "-1");
        $reportDate = now()->format('Ymd'); // now()->subDays(1)->format('Ymd');
        $dirPath = 'public/report/temp/accountDailyDisbursalReport/'.$reportDate;
        
        if (Storage::exists($dirPath)) {
            $files = Storage::disk('local')->files($dirPath);
            foreach($files as $file)
            {
                $filePath = storage_path('app/'.$file);
                if (file_exists($filePath)) {
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
                        $searchArray = ["Loan Account #" => "Loan Account", "tranction #" => "Tranction", "Invoice #" => "Invoice", "Beneficiary Credit Account No." => "Beneficiary Credit Account No"];
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
                        AccountDisbursalReportModel::create([
                            "Customer Name"                   => $dataRecord["Customer Name"],
                            "Loan Account"                    => $dataRecord["Loan Account"],
                            "Transction Date"                 => Carbon::parse($dataRecord["Transction Date"])->format('Y-m-d'),
                            "Tranction"                       => $dataRecord["Tranction"],
                            "Invoice"                         => $dataRecord["Invoice"],
                            "Invoice Date"                    => Carbon::parse($dataRecord["Invoice Date"])->format('Y-m-d'),
                            "Invoice Amount"                  => str_replace(',', '', $dataRecord["Invoice Amount"]),
                            "Margin Amount"                   => str_replace(',', '', $dataRecord["Margin Amount"]),
                            "Amount Disbrused"                => str_replace(',', '', $dataRecord["Amount Disbrused"]),
                            "UTR"                             => $dataRecord["UTR"],
                            "Remark while uploading Invoice"  => $dataRecord["Remark while uploading Invoice"],
                            "Beneficiary Credit Account No"   => $dataRecord["Beneficiary Credit Account No"],
                            "Beneficiary IFSC Code"           => $dataRecord["Beneficiary IFSC Code"],
                            "Status"                          => $dataRecord["Status"],
                            "Status Description"              => $dataRecord["Status Description"] ?? '---'
                        ]);
                    }
                }        
            }
            $this->info("The Account Disbursal Report sync to database successfully.");
        } else {
            $this->info("No Account Disbursal Report found.");
        }
    }
}
