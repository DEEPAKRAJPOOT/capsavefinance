<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Inv\Repositories\Models\AppLimit;
use App\Inv\Repositories\Models\AppSecurityDoc;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\Master\SecurityDocument;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\AppStatusLog;
use App\Inv\Repositories\Models\Master\Status;
use Storage;

class ImportPrePostSecurityData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:prepost-security-data-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import prepost security csv data and insert into the database.';

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
        $this->info('Does not perform any query only for data Imported one time process....');
        //$fullFilePath = '/home/deepak/Documents/prepostimportData/Prepost data mapping dated 30-09-2022-to be filled.csv'; //for testing purpose data for local system
        //$fullFilePath = 'public/prepostcsvfiledata/Prepost-data-mapping-dated-30-09-2022-to-be-filled.csv'; //uncomment for live server old
        $fullFilePath = 'public/prepostcsvfiledata/Prepost-data-mapping-dated-30-09-2022-to-10-10-2022-missing-data.csv'; //uncomment for live server
        if (!isset($fullFilePath)) {
            $this->info('Csv file has been not found.Please try again.');
            return false;
        }
        $fileArrayData = $this->csvToArray($fullFilePath);
        //dd($fileArrayData);
        if ($fileArrayData['status'] != 'success') {
            $this->info('Data has been not found.Please try again.');
            return false;
        }
        $rowData = $fileArrayData['data'];
        foreach ($rowData as $key => $arrCamData) {
            $securityDocId = isset($arrCamData['security_doc_id']) ? $arrCamData['security_doc_id'] : null;
            if ($securityDocId == null) {
                $this->info('security_doc_id Data null has been not found.Please try again.');
                return false;
            }
        }
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $app_security_doc_id = null;
        foreach ($rowData as $key => $arrCamData) {
            $applicationData = Application::where(['app_id' => trim($arrCamData['app_id'])])->where('app_id', '>=', 2216)->first('curr_status_id');
            if ($applicationData) {
                $inputArr = array(
                    'description' => isset($arrCamData['cond']) ? strip_tags($arrCamData['cond']) : null,
                );
                AppSecurityDoc::updateOrcreate(['app_id' => $arrCamData['app_id'],'biz_id' => $arrCamData['biz_id']], $inputArr);
            }
        }
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->info('Imported successfully....');
    }


    public function csvToArray($filename = '', $delimiter = ',')
    {
        $respArray = [
            'status' => 'success',
            'message' => 'success',
            'data' => [],
        ];
        try {
            if (!file_exists($filename) || !is_readable($filename))
                return false;

            $header = null;
            $fileDetails = pathinfo($filename);
            $tempFileName = Session::getId().'_'.$fileDetails['basename'];
            $localPath = Storage::disk('temp')->put($tempFileName, Storage::get($filename));
            $localPath = Storage::disk('temp')->path($tempFileName);
            if (($handle = fopen($localPath, 'r')) !== false) {
                $rows = 1;
                while (($row = fgetcsv($handle, 10000, $delimiter)) !== false) {
                    if (!$header) {
                        $header = $row;
                    } else {
                        $respArray['data'][] = array_combine($header, $row);
                    }
                    $rows++;
                }
                fclose($handle);
            }
            Storage::disk('temp')->delete($tempFileName);
        } catch (\Exception $e) {
            $respArray['data'] = [];
            $respArray['status'] = 'fail';
            $respArray['message'] = str_replace($filename, '', $e->getMessage());
        }
        return $respArray;
    }
}
