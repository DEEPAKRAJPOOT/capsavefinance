<?php

namespace App\Console\Commands\ETL;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\ETL\DisbursalReport as DisbursalReportModel;
use PHPExcel_IOFactory;
use Carbon\Carbon;

class DisbursalReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etl:report_disbursal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To sync disbursal report to ETL database';

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
        $dirPath = 'public/report/temp/dailyDisbursalReport/'.$reportDate;
        
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
                    $headingRow = 5;
                    $headings = $sheet->rangeToArray('A' . $headingRow . ':' . $highestColumn . $headingRow, NULL, TRUE, FALSE);
                    if (is_array($headings) && count($headings)) {
                        $headings = array_values($headings[0]);
                        $searchArray = ["Vendor/Beneficiary Name"                               => "Vendor_Beneficiary Name",
                                        "Supply chain type (upfront, Rare or monthly interest)" => "Supply chain type", 
                                        "Tenure (Days)"                                         => "Tenure",
                                        "Net of interest, PF & Stamp"                           => "Net of interest_PF _Stamp",
                                        "From"                                                 => "From_1",
                                        "To"                                                    => "To_1",
                                        "Sanction date"                                         => "Sanction Date",
                                        "Funds to be received from Anchor or client"            => "Funds_rec From anchor_client",
                                        "Sanction no."                                          => "Sanction no"
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
                        DisbursalReportModel::create([
                            "Borrower name"                         => $dataRecord['Borrower name'],
                            "RM"                                    => $dataRecord['RM'],
                            "Anchor name"                           => $dataRecord['Anchor name'],
                            "Anchor program name"                   => $dataRecord['Anchor program name'],
                            "Vendor_Beneficiary Name"               => $dataRecord['Vendor_Beneficiary Name'] ?? '---',
                            "Region"                                => $dataRecord['Region'] ?? '---',
                            "Sanction no"                           => $dataRecord['Sanction no'],
                            "Sanction Date"                         => Carbon::parse($dataRecord['Sanction Date'])->format('Y-m-d'),
                            "Sanction Amount"                       => str_replace(',', '', $dataRecord['Sanction Amount']),
                            "Status"                                => $dataRecord['Status'],
                            "Disbrusal Month"                       => $dataRecord['Disbrusal Month'],
                            "Disburse amount"                       => str_replace(',', '', $dataRecord['Disburse amount']),
                            "Disbursement date"                     => Carbon::parse($dataRecord['Disbursement date'])->format('Y-m-d'),
                            "Disbursal UTR No"                      => $dataRecord['Disbursal UTR No'],
                            "Disbursal Act No"                      => $dataRecord['Disbursal Act No'],
                            "Disbursal IFSC Code"                   => $dataRecord['Disbursal IFSC Code'],
                            "Type of Finance"                       => $dataRecord['Type of Finance'],
                            "Supply chain type"                     => $dataRecord['Supply chain type'],
                            "Tenure"                                => $dataRecord['Tenure'],
                            "Interest rate"                         => $dataRecord['Interest rate'],
                            "Interest amount"                       => str_replace(',', '', $dataRecord['Interest amount']),
                            "From_1"                                => Carbon::parse($dataRecord['From_1'])->format('Y-m-d'),
                            "To_1"                                  => Carbon::parse($dataRecord['To_1'])->format('Y-m-d'),
                            "TDS on Interest"                       => str_replace(',', '', $dataRecord['TDS on Interest']),
                            "Net Interest"                          => str_replace(',', '', $dataRecord['Net Interest']),
                            "Interest received date"                => $dataRecord['Interest received date'] !== '---' ? Carbon::parse($dataRecord['Interest received date'])->format('Y-m-d') : $dataRecord['Interest received date'],
                            "Processing fees"                       => str_replace(',', '', $dataRecord['Processing fees']),
                            "Processing amount"                     => str_replace(',', '', $dataRecord['Processing amount']),
                            "Processing fee with GST"               => str_replace(',', '', $dataRecord['Processing fee with GST']),
                            "TDS on Processing fee"                 => str_replace(',', '', $dataRecord['TDS on Processing fee']),
                            "Net Processing fee receivable"         => str_replace(',', '', $dataRecord['Net Processing fee receivable']),
                            "Processing fee received"               => str_replace(',', '', $dataRecord['Processing fee received']),
                            "Processing Fee Amount received date"   => $dataRecord['Processing Fee Amount received date'] !== '---' ? Carbon::parse($dataRecord['Processing Fee Amount received date'])->format('Y-m-d') : $dataRecord['Processing Fee Amount received date'],
                            "Balance"                               => str_replace(',', '', $dataRecord['Balance']),
                            "Margin"                                => str_replace(',', '', $dataRecord['Margin']),
                            "Due date"                              => Carbon::parse($dataRecord['Due date'])->format('Y-m-d'),
                            "Funds_rec From anchor_client"          => str_replace(',', '', $dataRecord['Funds_rec From anchor_client']),
                            "Principal receivable"                  => str_replace(',', '', $dataRecord['Principal receivable']),
                            "Received"                              => str_replace(',', '', $dataRecord['Received']),
                            "Net Receivable"                        => str_replace(',', '', $dataRecord['Net Receivable']),
                            "Adhoc interest"                        => str_replace(',', '', $dataRecord['Adhoc interest']),
                            "Net Disbursement"                      => str_replace(',', '', $dataRecord['Net Disbursement']),
                            "Gross"                                 => str_replace(',', '', $dataRecord['Gross']),
                            "Net of interest_PF _Stamp"             => $dataRecord['Net of interest_PF _Stamp']
                        ]);
                    }
                }        
            }
            $this->info("The Disbursal Report sync to database successfully.");
        } else {
            $this->info("No Disbursal Report found.");
        }
    }
}
