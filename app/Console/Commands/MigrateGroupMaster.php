<?php

namespace App\Console\Commands;

use Dompdf\Helpers;
use App\Helpers\FileHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Inv\Repositories\Models\BizOwner;
use App\Inv\Repositories\Models\UcicUser;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\UcicUserUcic;
use App\Inv\Repositories\Models\AppGroupDetail;
use App\Inv\Repositories\Models\Master\Product;
use App\Inv\Repositories\Models\Master\NewGroup;
use App\Inv\Repositories\Models\Master\MakerChecker;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\UcicUserInterface as InvUcicUserRepoInterface;
use Storage;

class MigrateGroupMaster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:group_expo_ucic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Old Group Exposure & UCIC Data to New Tables';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FileHelper $file_helper, InvUcicUserRepoInterface $ucicuser_repo, InvUserRepoInterface $user_repo)
    {
        $this->fileHelper = $file_helper;
        $this->ucicuser_repo = $ucicuser_repo;
        $this->userRepo = $user_repo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::unprepared('SET FOREIGN_KEY_CHECKS = 0; TRUNCATE  rta_user_ucic; TRUNCATE  rta_user_ucic_user; TRUNCATE  rta_mst_group_new; TRUNCATE  rta_app_group_detail; TRUNCATE  rta_test_1; TRUNCATE  rta_mst_maker_checker;');
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 10000);
        $this->migrateDataFromCsv($fileName = 'master_group_data_preprod', $migrateDataType = 1);
        $this->migrateDataFromCsv($fileName = 'application_and_group_data_preprod', $migrateDataType = 4);
        $this->setPriority();
        $this->createAppGroupExposer();
        $this->createAppGroupExposer2();
        $this->createAppDataForUcic();
    }

    private function createUcicUser($userId, $appId, $pan_no, $ucic_code)
    {
        $newUcicUser = UcicUser::create([
            'user_id' => $userId,
            'app_id'  => $appId,
            'pan_no'  => $pan_no,
            'ucic_code' => $ucic_code,
        ]);

        return $newUcicUser->user_ucic_id;
    }

    private function createUcicUseruser($UcicUserUcicData)
    {
        $newUcicUser = UcicUserUcic::create([
            'user_id' => $UcicUserUcicData['user_id'],
            'app_id'  => $UcicUserUcicData['app_id'],
            'ucic_id'  => $UcicUserUcicData['ucic_id'],
            'group_id' => $UcicUserUcicData['group_id'],
            'created_by'  => 1,
        ]);
        return $newUcicUser->user_ucic_id;
    }

    private function migrateDataFromCsv($fileName, $migrateDataType)
    {
        $filePath = Storage::path("public/migrate/$fileName.csv");
        $fileArrayData = $this->csvToArrayWithSeparator($filePath, ",");
        $rowData = $fileArrayData['data'];

        if(is_array($rowData) && count($rowData)) {
            foreach($rowData as $rowNewData){
                switch ($migrateDataType) {
                    case 1:
                        $this->migrateMasterGroupData($rowNewData);   
                        break;
                    case 4:
                        $this->migrateUcicNewData($rowNewData);
                        break;
                    default:
                        break;
                }
            }
        }
    }
    
    private function migrateMasterGroupData($rowNewData)
    {
        if (!empty($rowNewData['New_Group_Name'])) {
            $existGroup = NewGroup::where('group_name', $rowNewData['New_Group_Name'])->first();
            if (!$existGroup) {
                $group = NewGroup::create([
                    'group_name' => $rowNewData['New_Group_Name'],
                    'Group_Field_1' => $rowNewData['Group_Field_1'] ?? NULL,
                    'Group_Field_2' => $rowNewData['Group_Field_2'] ?? NULL,
                    'Group_Field_3' => $rowNewData['Group_Field_3'] ?? NULL,
                    'Group_Field_4' => $rowNewData['Group_Field_4'] ?? NULL,
                    'Group_Field_5' => $rowNewData['Group_Field_5'] ?? NULL,
                    'Group_Field_6' => $rowNewData['Group_Field_6'] ?? NULL,
                ]);

                // group auto approval
                $model = new NewGroup();
                $attributes = [
                    [
                        'table_name' => $model->getTable(),
                        'type'       => 1,
                        'route_name' => 'save_new_group',
                        'status'     => 0
                    ],
                    [
                        'table_name' => $model->getTable(),
                        'type'       => 2,
                        'route_name' => 'approve_new_group',
                        'status'     => 1
                    ],
                ];

                foreach($attributes as $attribute) {
                    $group->makerCheckers()->save(new MakerChecker($attribute));
                }

                // generate group code
                $group->update([
                    'group_code' => \Helpers::generateGroupCode($group->group_id),
                    'is_active'  => 1,
                ]);
            }
        }
    }
    
    private function migrateUcicNewData($rowNewData)
    {
        $defaultAttributes = ["App_Id", "Pan_No",'UCC_id','New_Group_Name'];
        $attributes = array_keys($rowNewData);
        $diff = array_diff($defaultAttributes, $attributes);

        if (is_array($diff) && count($diff) == 0) {
            $appId = str_replace('CAPAI', '', $rowNewData['App_Id']);
            $app = Application::find($appId);
            $group = NewGroup::where('group_name', $rowNewData['New_Group_Name'])->first();
            if ($app) {
                $ucicUser = UcicUser::where('pan_no', $rowNewData['Pan_No'])->first();
                if ($ucicUser) {
                    $ucicilastInsertedID = $ucicUser->user_ucic_id; 
                }else{
                    $ucicilastInsertedID  = $this->createUcicUser($app->user_id, NULL, $rowNewData['Pan_No'], $rowNewData['UCC_id']);   
                }
                
                if($ucicilastInsertedID) {
                    $userucicuserdata['user_id'] = $app->user_id;
                    $userucicuserdata['app_id'] = $appId;
                    $userucicuserdata['ucic_id'] = $ucicilastInsertedID;
                    $userucicuserdata['group_id'] = $group->group_id ?? NULL;
                    $userucicuserdata['created_by'] = 1;
                    $this->createUcicUseruser($userucicuserdata);
                }
            }
        }
    }

    private function migrateGroupExposureDataToGroupDetail($rowNewData, $flag = 0, $appIds = [], $freezeDate = NULL)
    {
        $defaultAttributes = ["app_id", "group_id", "new_group_name", "bussiness_name", "sanction_limit", "outstanding_exposure", "proposed_exposure"];

        if (!empty($rowNewData['app_id'])) {
            $appId = str_replace('CAPAI', '', $rowNewData['app_id']);
            $app = Application::find($appId);
            $group = NewGroup::where('group_name', $rowNewData['new_group_name'])->first();

            if ($app) {
                $appId = $app->app_id; 
                $userId = $app->user_id;
                $groupId = $group->group_id ?? NULL;

                //Update UCIC Data
                $ucicDetail = UcicUser::where('pan_no',$rowNewData['pan_no'])->first();
                if($flag == 1){
                    $ucicUserApp = UcicUserUcic::where(['app_id'=>$appId])->update(['group_id'=>$groupId]);
                    $ucicDetail = UcicUser::where('pan_no',$rowNewData['pan_no'])->update(['app_id'=>$appId,'group_id'=>$groupId,'user_id'=>$userId]);
                    $status = 1;
                    $isLatest = 1;
                }
                if($flag == 2){
                    $ucicUserApp =  UcicUserUcic::where(['app_id'=>$appId])->update(['group_id'=>$groupId]);
                    $ucicDetail = UcicUser::where('pan_no',$rowNewData['pan_no'])->whereNull('app_id')->update(['app_id'=>$appId,'user_id'=>$userId]);
                    $status = 0;
                }
            }
        }
    }
    
    private function setPriority()
    {   
        DB::statement('TRUNCATE TABLE rta_test_1');
        DB::statement('INSERT INTO rta_test_1 (group_id,ucic_code,app_id,created_at,status_id,curr_status_id ) SELECT g.`group_id`, c.`ucic_code`, a.`app_id`, d.created_at, d.status_id, a.`curr_status_id` FROM rta_app AS a JOIN rta_user_ucic_user AS b ON b.`app_id` = a.`app_id` JOIN rta_user_ucic AS c ON c.`user_ucic_id` = b.`ucic_id` JOIN rta_app_status_log AS d ON d.`app_id` = a.`app_id` JOIN rta_mst_status AS e ON e.id = d.status_id LEFT JOIN rta_test AS f ON f.`app_id_id` = a.`app_id` LEFT JOIN rta_mst_group_new AS g ON g.`group_name` = f.`new_group_name` WHERE e.status_type = 1 ORDER BY d.created_at ASC ,FIELD(d.status_id,49,20,21,56,22,25,50,51)'); 
        DB::statement('UPDATE rta_user_ucic AS a JOIN rta_user_ucic_user AS b ON a.`user_ucic_id` = b.`ucic_id` JOIN rta_app AS c ON b.`app_id` = c.`app_id` JOIN rta_app_status_log AS d ON d.`app_id` = c.`app_id` SET a.is_sync = 0 WHERE d.`status_id` IN (21,22,25,50,51)');
        $Transactions = DB::table('test_1')->orderBy('id','ASC')->get();
        $preUcicCode = NULL;
        $appData = [];
        $beforeApprv = [49,20,43,56];
        $afterApprvWith = [21,22,25,50,51];
        $afterApprv = [22,25,50,51];
        $maxPrio = 0;
        foreach($Transactions as $key => $trans){
            $curUcicCode = $trans->group_id ?? 0;
          
            if(in_array($trans->status_id,$afterApprvWith)){
                $transPrio = DB::table('test_1')->where('app_id',$trans->app_id)->whereNotNull('prio')->value('prio');
                if(in_array($trans->status_id,$afterApprvWith) && is_null($transPrio)){
                    $prio = $maxPrio + 1;
                    $Transactions[$key]->prio = $prio;
                    DB::table('test_1')->where('id', $trans->id)->update(['prio' => $prio]);
                    $maxPrio = $prio;
                }
            }
        }
    }

    private function createAppGroupExposer()
    {
        $Transactions = DB::table('test_1')->whereNotNull('prio')->orderBy('prio','ASC')->get();
        foreach ($Transactions as $key => $trans) {
            $appIds = [];
            $appData = null;
            if($trans->group_id){
                $appData = DB::select("SELECT app_id FROM rta_test_1 WHERE id IN( SELECT MAX(id) FROM rta_test_1 WHERE group_id = $trans->group_id AND id <= $trans->id GROUP BY app_id) AND status_id NOT IN (43,44,51)");
                foreach ($appData as $key => $value) {
                        $appIds[] = $value->app_id;
                }
            }
            
            $rowNewData = DB::table('test')->where('app_id_id',$trans->app_id)->first();
            if($rowNewData){
                $rowNewData = json_decode(json_encode($rowNewData), true);
                $this->migrateGroupExposureDataToGroupDetail($rowNewData, 1, $appIds, $trans->created_at);
            }
        }
    }
    
    private function createAppGroupExposer2()
    {
        $Transactions = DB::select('SELECT min(a.app_id) as app_id  FROM rta_test_1 AS a LEFT JOIN rta_test_1 AS b ON a.app_id = b.app_id AND b.prio IS NOT NULL WHERE b.app_id IS NULL group by a.ucic_code');
        foreach ($Transactions as $key => $trans) {
            $rowNewData = DB::table('test')->where('app_id_id',$trans->app_id)->first();
            if($rowNewData){
                $rowNewData = json_decode(json_encode($rowNewData), true);
                $this->migrateGroupExposureDataToGroupDetail($rowNewData, 2);
            }
        }
         DB::statement('UPDATE rta_app AS a JOIN rta_app_group_detail AS b ON a.app_id = b.app_id SET b.status = 0 WHERE a.curr_status_id IN (43,44) AND b.status = 1');
    }

    private function createAppDataForUcic()
    {
        $ucicUsers = UcicUser::whereNull('business_info')
                            ->whereNull('management_info')
                            ->whereNotNull('app_id')
                            ->get();
        
        foreach($ucicUsers as $ucicUser)
        {
            $application = $ucicUser->app;
            $business = $application->business;
            $product_ids = [];
            if (!empty($application->products)) {
				foreach($application->products as $product){
					$product_ids[$product->pivot->product_id]= array(
						"loan_amount" => $product->pivot->loan_amount,
						"tenor_days" => $product->pivot->tenor_days
					);
				}
			}
            $businessInfo = $this->ucicuser_repo->formatBusinessInfoDb($business, $product_ids);
            $ownerPanApi = $this->userRepo->getOwnerApiDetail(['biz_id' => $application->biz_id]);
            $documentData = \Helpers::makeManagementInfoDocumentArrayData($ownerPanApi);
            $managementData = $this->ucicuser_repo->formatManagementInfoDb($ownerPanApi,NULL);
            $managementInfo = array_merge($managementData,$documentData);

            $this->ucicuser_repo->saveApplicationInfo($ucicUser->user_ucic_id, $businessInfo, $managementInfo, $application->app_id);
        }
    }
    
    public function csvToArrayWithSeparator($filename = '', $delimiter = ',')
    {
      $respArray = [
        'status' => 'success',
        'message' => 'success',
        'data' => [],
      ];
      try{
        if (!file_exists($filename) || !is_readable($filename))
          return false;

        $header = null;
        if (($handle = fopen($filename, 'r')) !== false)
        {
          $rows=1;
          while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
          {
            $num = count($row);
            if (!$header){
              $header = $row;
            }else{
              $respArray['data'][] = array_combine($header, $row);
            }
            $rows++;
          }
          fclose($handle);
        }
      }catch(\Exception $e){
        $respArray['data'] = [];
        $respArray['status'] = 'fail';
        $respArray['message'] = str_replace($filename, '', $e->getMessage());
      }
      return $respArray;
    }
}
