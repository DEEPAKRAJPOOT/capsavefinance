<?php

namespace App\Console\Commands\ETL;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\ETL\OverdueReport as OverdueReportModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Session;

class OverdueReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etl:report_overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To sync overdue report to ETL database';

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
        $reportDate = now()->format('Ymd');
        $dirPath = 'public/report/temp/overdueReport/'.$reportDate;
        
        if (Storage::exists($dirPath)) {
            $files = Storage::files($dirPath);
            foreach($files as $filePath)
            {
                if (Storage::exists($filePath)) {
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
                        $searchKey = array_search("Virtual Account #", $headings);
                        $headings[$searchKey] = "Virtual Account";
                    }

                    // create array of row data
                    $dataRow = $headingRow + 1;
                    $dataRecords = [];
                    for ($row = $dataRow; $row <= $highestRow; $row++){ 
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                        array_push($dataRecords, array_combine($headings, $rowData[0]));
                    }

                    OverdueReportModel::truncate();
                    // database insertion
                    foreach($dataRecords as $dataRecord)
                    {                        
                        OverdueReportModel::create([
                            "Customer Name"      =>  $dataRecord["Customer Name"],
                            "Customer ID"        =>  $dataRecord["Customer ID"],
                            "Invoice No"         =>  $dataRecord["Invoice No"],
                            "Invoice Due Date"   =>  $dataRecord["Invoice Due Date"],
                            "Virtual Account"    =>  $dataRecord["Virtual Account"],
                            "Sanction Limit"     =>  str_replace(',', '', $dataRecord["Sanction Limit"]),
                            "Limit Available"    =>  str_replace(',', '', $dataRecord["Limit Available"]),
                            "O/s Amount"         =>  str_replace(',', '', $dataRecord["O/s Amount"]),
                            "Over Due Days"      =>  $dataRecord["Over Due Days"],
                            "Overdue Amount"     =>  str_replace(',', '', $dataRecord["Overdue Amount"]),
                            "Sales Person Name"  =>  $dataRecord["Sales Person Name"]
                        ]);
                    }
                }        
            }
            $this->info("The Overdue Report sync to database successfully.");
        } else {
            $this->info("No Overdue Report found.");
        }
    }
}
