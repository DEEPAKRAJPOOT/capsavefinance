<?php

namespace App\Console\Commands\ETL;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\ETL\MaturityReport as MaturityReportModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Session;

class MaturityReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etl:report_maturity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To sync maturity report to ETL database';

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
        $dirPath = 'public/report/temp/maturityReport/'.$reportDate;
        
        if (Storage::exists($dirPath)) {
            $files = Storage::files($dirPath);
            foreach($files as $filePath)
            {
                if (Storage::exists($filePath) && $filePath == Storage::path($dirPath."/Consolidated Report.xlsx")) {
                    try {
                        $fileDetails = pathinfo($filePath);
                        $tempFileName = Session::getId().'_'.$fileDetails['basename'];
                        $localPath = Storage::disk('temp')->put($tempFileName, Storage::get($filePath));
                        $localPath = Storage::disk('temp')->path($tempFileName);
                        $inputFileType = IOFactory::identify($localPath);
                        $objReader = IOFactory::createReader($inputFileType);
                        $objSpreadsheet = $objReader->load($localPath);
                        Storage::disk('temp')->delete($tempFileName);
                    } catch (\Exception $e) {
                        die('Error loading file "'.pathinfo($filePath,PATHINFO_BASENAME).'": '.$e->getMessage());
                    }
                    //  Get worksheet dimensions
                    $sheet = $objSpreadsheet->getSheet(0);
                    $highestRow = $sheet->getHighestRow(); 
                    $highestColumn = $sheet->getHighestColumn();

                    // create array of headings
                    $headingRow = 5;
                    $headings = $sheet->rangeToArray('A' . $headingRow . ':' . $highestColumn . $headingRow, NULL, TRUE, FALSE);
                    if (is_array($headings) && count($headings)) {
                        $headings = array_values($headings[0]);
                        $searchArray = ["Loan Account #" => "Loan Account", "Virtual Account #" => "Virtual Account", "Tranction #" => "Tranction", "Invoice #" => "Invoice"];
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

                    MaturityReportModel::truncate();
                    // database insertion
                    foreach($dataRecords as $dataRecord)
                    {                        
                        MaturityReportModel::create([
                            "Customer Name"                   => $dataRecord["Customer Name"],
                            "Loan Account"                    => $dataRecord["Loan Account"],
                            "Virtual Account"                 => $dataRecord["Virtual Account"],
                            "Transction Date"                 => Carbon::parse($dataRecord["Transction Date"])->format('Y-m-d'),
                            "Tranction"                       => $dataRecord["Tranction"],
                            "Invoice"                         => $dataRecord["Invoice"],
                            "Invoice Date"                    => Carbon::parse($dataRecord["Invoice Date"])->format('Y-m-d'),
                            "Invoice Amount"                  => str_replace(',', '', $dataRecord["Invoice Amount"]),
                            "Margin Amount"                   => str_replace(',', '', $dataRecord["Margin Amount"]),
                            "Amount Disbursed"                => str_replace(',', '', $dataRecord["Amount Disbursed"]),
                            "O/s Amount"                      => str_replace(',', '', $dataRecord["O/s Amount"]),
                            "O/s Days"                        => $dataRecord["O/s Days"],
                            "Credit Period"                   => $dataRecord["Credit Period"],
                            "Maturity Date"                   => Carbon::parse($dataRecord["Maturity Date (Due Date)"])->format('Y-m-d'),
                            "Maturity Amount"                 => str_replace(',', '', $dataRecord["Maturity Amount"]),
                            "Over Due Days"                   => $dataRecord["Over Due Days"],
                            "Overdue Amount"                  => str_replace(',', '', $dataRecord["Overdue Amount"]),
                            "Remark while uploading Invoice"  => $dataRecord["Remark while uploading Invoice"],
                        ]);
                    }
                }        
            }
            $this->info("The Maturity Report sync to database successfully.");
        } else {
            $this->info("No Maturity Report found.");
        }
    }
}
