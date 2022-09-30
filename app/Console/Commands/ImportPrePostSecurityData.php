<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Inv\Repositories\Models\AppLimit;
use App\Inv\Repositories\Models\AppSecurityDoc;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\Master\SecurityDocument;
use App\Inv\Repositories\Models\Application;

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
        //$fullFilePath = 'public/prepostcsvfiledata/RTA_CAM_Reviewer_Summary-Data-final.csv'; //first please uncomment file for imported data
        //$fullFilePath = 'public/prepostcsvfiledata/rta_cam_reviewer_prepost_cond_latest_data_436_Lot 2-24AUG22.csv'; //after first file data imported then second please uncomment file for imported data please comment first file
        //$fullFilePath = '/home/deepak/Documents/prepostimportData/c1.csv'; //for testing purpose data for local system
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
        foreach ($rowData as $key => $arrCamData) {
            $applicationData = Application::where(['app_id' => trim($arrCamData['app_id'])])->first('curr_status_id');
            if ($applicationData) {
                $arrCamData['app_status'] = $applicationData->curr_status_id;
                $mstSecurityData = SecurityDocument::where(['name' => trim($arrCamData['security_doc_id']), 'is_active' => 1])->first('security_doc_id');

                $inputArr = array(
                    'cam_reviewer_summary_id' => isset($arrCamData['cam_reviewer_summary_id']) ? $arrCamData['cam_reviewer_summary_id'] : NULL,
                    'biz_id' => $arrCamData['biz_id'],
                    'app_id' => $arrCamData['app_id'],
                    'security_doc_id' => $mstSecurityData ? $mstSecurityData->security_doc_id : null,
                    'description' => isset($arrCamData['description']) ? strip_tags($arrCamData['description']) : null,
                    'due_date' => isset($arrCamData['due_date']) ? Carbon::createFromFormat('d-m-Y', $arrCamData['due_date'])->format('Y-m-d') : null,
                    'doc_type' => isset($arrCamData['cond_type']) ? $arrCamData['cond_type'] : null,
                    'created_at' => isset($arrCamData['created_at']) ? Carbon::parse($arrCamData['created_at'])->format('Y-m-d H:i:s') : null,
                    'created_by' => isset($arrCamData['created_by']) ? $arrCamData['created_by'] : null,
                    'is_non_editable' => 0,
                    'status' => 1,
                );
                $app_security_doc_id = null;
                AppSecurityDoc::updateOrcreate(['app_security_doc_id' => $app_security_doc_id], $inputArr);
                if ($arrCamData['app_status'] == 22) {
                    $appSecurtiyDocs = AppSecurityDoc::where(['app_id' => $arrCamData['app_id'], 'biz_id' => $arrCamData['biz_id'], 'is_active'  => 1, 'is_non_editable' => 0, 'status' => 1])->get();
                    foreach ($appSecurtiyDocs as $clone) {
                        $cloneAppSecData = $clone->replicate();
                        $cloneAppSecData->is_non_editable = 0;
                        $cloneAppSecData->status = 3;
                        $cloneAppSecData->save();
                    }
                    $updateStatus = AppSecurityDoc::where(['app_id' => $arrCamData['app_id'], 'biz_id' => $arrCamData['biz_id'], 'status' => 1, 'is_non_editable' => 0, 'is_active' => 1])->update(['is_non_editable' => 1, 'status' => 2]);
                } else if ($arrCamData['app_status'] != 21 && $arrCamData['app_status'] != 20 && $arrCamData['app_status'] != 56 && $arrCamData['app_status'] != 49 && $arrCamData['app_status'] != 23 && $arrCamData['app_status'] != 43 && $arrCamData['app_status'] != 45 && $arrCamData['app_status'] != 46 && $arrCamData['app_status'] != 44 && $arrCamData['app_status'] != 55) {
                    $AppLimitData = AppLimit::where(['user_id' => $arrCamData['user_id'], 'app_id' => $arrCamData['app_id'], 'biz_id' => $arrCamData['biz_id'], 'status' => 1])->first();
                    $renewal_reminder_days = $renewal_reminder_date = $maturity_date = null;
                    // if (isset($arrCamData['maturity_date']) && !empty($arrCamData['maturity_date']) && $arrCamData['maturity_date'] != '') {
                    //     $maturity_date = Carbon::parse($arrCamData['maturity_date'])->format('Y-m-d');
                    // }

                    if ($AppLimitData) {
                        $maturity_date   =  $AppLimitData->actual_end_date ? $AppLimitData->actual_end_date : $AppLimitData->end_date;
                        $renewal_reminder_days = 7;
                        $rmd = strtotime('-' . $renewal_reminder_days . ' days', strtotime($maturity_date));
                        $rmd = date('Y-m-d', $rmd);
                        $renewal_reminder_date = $rmd;
                    }

                    $arrAppSecurityDoc1 = AppSecurityDoc::where(['app_id' => $arrCamData['app_id'], 'biz_id' => $arrCamData['biz_id'], 'is_active' => 1, 'is_non_editable' => 0, 'status' => 1])->first();
                    if ($arrAppSecurityDoc1) {
                        $updateStatus1 = AppSecurityDoc::where(['app_id' => $arrCamData['app_id'], 'biz_id' => $arrCamData['biz_id'], 'status' => 1, 'is_non_editable' => 0, 'is_active' => 1])->update(['is_non_editable' => 1, 'status' => 2]);
                        if ($arrCamData['ProductNameANDProgramOfferid'] != 'NULL') {
                            $offerID = explode("|", $arrCamData['ProductNameANDProgramOfferid']);
                            $offerPrgmID = [];
                            $cntOfferID = count($offerID);
                            if ($cntOfferID == 1) {
                                foreach ($offerID as $key => $offerIdV) {
                                    $a = explode('-', $offerIdV);
                                    $whereCondition = ['prgm_offer_id' => $a[1], 'app_id' => $arrCamData['app_id'], 'is_approve' => 1, 'is_active' => 1, 'status' => 1];
                                    $offerList = AppProgramOffer::getPrgmOfferByAppId($whereCondition);
                                    if ($offerList) {
                                        $offerPrgmID[$a[1]] = $a[1];
                                    }
                                }
                            }
                            if (!empty($offerPrgmID)) {
                                $arrCamData['prgm_offer_id'] = array_rand($offerPrgmID);
                            }
                        }
                        $inputArr = array(
                            'biz_id' => $arrCamData['biz_id'],
                            'app_id' => $arrCamData['app_id'],
                            'prgm_offer_id' => isset($arrCamData['prgm_offer_id']) ? $arrCamData['prgm_offer_id'] : null,
                            'security_doc_id' => $mstSecurityData ? $mstSecurityData->security_doc_id : null,
                            'description' => isset(($arrCamData['description'])) ? strip_tags($arrCamData['description']) : null,
                            'document_number' => isset($arrCamData['document_number']) ? trim($arrCamData['document_number']) : null,
                            'due_date' => isset($arrCamData['due_date']) ? Carbon::createFromFormat('d-m-Y', $arrCamData['due_date'])->format('Y-m-d') : null,
                            'completed' => 'no',
                            'exception_received' => 'no',
                            'exception_received_from' => null,
                            'exception_received_date' =>  null,
                            'exception_remark' => null,
                            'extended_due_date' => null,
                            'maturity_date' => isset($maturity_date) ? $maturity_date : null,
                            'renewal_reminder_days' => isset($renewal_reminder_days) ? $renewal_reminder_days : null,
                            'amount_expected' => isset($arrCamData['amount_expected']) ? str_replace(",", "", trim($arrCamData['amount_expected'])) : null,
                            'document_amount' => isset($arrCamData['document_amount']) ?  str_replace(",", "", trim($arrCamData['document_amount'])) : null,
                            'doc_type' => isset($arrCamData['cond_type']) ? $arrCamData['cond_type'] : null,
                            'created_at' => isset($arrCamData['created_at']) ? Carbon::parse($arrCamData['created_at'])->format('Y-m-d H:i:s') : null,
                            'created_by' => isset($arrCamData['created_by']) ? $arrCamData['created_by'] : null,
                            'updated_by' => null,
                            'renewal_reminder_date' => isset($renewal_reminder_date) ? $renewal_reminder_date : null,
                            'is_non_editable' => 0,
                            'status' => 3,
                        );
                        //$inputArr['is_upload'] = null;
                        //$inputArr['file_id'] =  null;
                        $app_security_doc_id = null;
                        AppSecurityDoc::updateOrcreate(['app_security_doc_id' => $app_security_doc_id], $inputArr);
                        if ($arrCamData['app_status'] == 50 || $arrCamData['app_status'] == 51) {
                            $appSecurtiyDocs = AppSecurityDoc::where(['app_id' => $arrCamData['app_id'], 'biz_id' => $arrCamData['biz_id'], 'is_active' => 1])->whereIn('status', [3])->whereIn('is_non_editable', [0])->get();
                            foreach ($appSecurtiyDocs as $clone) {
                                $cloneAppSecData = $clone->replicate();
                                $cloneAppSecData->is_non_editable = 0;
                                $cloneAppSecData->status = 5;
                                $cloneAppSecData->save();
                            }
                            $updateStatus2 = AppSecurityDoc::where(['app_id' => $arrCamData['app_id'], 'biz_id' => $arrCamData['biz_id'], 'status' => 3, 'is_non_editable' => 0, 'is_active' => 1])->update(['is_non_editable' => 1, 'status' => 4]);
                        }
                    }
                }
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
            if (($handle = fopen($filename, 'r')) !== false) {
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
        } catch (\Exception $e) {
            $respArray['data'] = [];
            $respArray['status'] = 'fail';
            $respArray['message'] = str_replace($filename, '', $e->getMessage());
        }
        return $respArray;
    }
}
