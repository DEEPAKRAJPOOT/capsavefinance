<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Inv\Repositories\Models\AppSecurityDoc;
use Helpers;
use Carbon\Carbon;
class AppSecurityDocRenewalReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:app_security_document_renewal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try {
            $date = Carbon::now()->format('Y-m-d');
            $appSecDoc = AppSecurityDoc::getAllAppSecurityDoc($date);
            $allEmailDataSecurity = [];
            if(!empty($appSecDoc) && isset($appSecDoc)){
               foreach($appSecDoc as $appSecDocV){
                $allEmailDataSecurity[$appSecDocV->email][]=$appSecDocV; 
               }
            }
            $emailData=array();
            $userNameList = array();
            $dataFound = false;
            if(!empty($allEmailDataSecurity)){
            foreach($allEmailDataSecurity as $email=>$appSecurtyData){
                $userNameLists = isset($appSecurtyData[0]['f_name'])?$appSecurtyData[0]['f_name']." ":'';
                $userNameLists .= isset($appSecurtyData[0]['l_name'])?$appSecurtyData[0]['l_name']:'';
                $userNameList[] = $userNameLists;
            if(!empty($appSecurtyData) && $email){
                $dataFound = true;
                $fullCustName = $userNameLists;
                $emailData = array(
                  'user_name' => $fullCustName,
                  'email' => $email,
                  'name' => 'Capsave Finance PVT LTD.',
                  'subject' => 'subject',
                  'body' => 'body',
                  'data' => $appSecurtyData,
                );
                \Event::dispatch("APP_SECURITY_DOCUMENT_RENEWAL_ALERT", serialize($emailData));
              }
            }
           }
            if (!$dataFound) {
              return printf('No coming Data Found.' .PHP_EOL);
            }
            return printf(implode(PHP_EOL . '<br />', $userNameList));
          } catch (\Exception $ex) {
              return Helpers::getExceptionMessage($ex);
          }
    }
}
