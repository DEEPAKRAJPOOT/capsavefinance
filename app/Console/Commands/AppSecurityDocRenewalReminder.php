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
                $allEmailDataSecurity[$appSecDocV->user_id]=AppSecurityDoc::getAppSecurityDocDetails($appSecDocV->app_id); 
               }
            }
            $emailData=array();
            $emailIds = array();
            $userNameList = array();
            $dataFound = false;
            if(!empty($allEmailDataSecurity)){
            foreach($allEmailDataSecurity as $userId=>$appSecurtyData){
                $custDetails = Helpers::getUserInfo($userId);
                $userNameList[] = $custDetails->f_name." ".$custDetails->l_name;
            if(!empty($appSecurtyData) && $custDetails->email){
                $dataFound = true;
                $fullCustName = $custDetails->f_name." ".$custDetails->l_name;
                $emailData = array(
                  'user_name' => $fullCustName,
                  'email' => $custDetails->email,
                  'name' => 'Capsave Finance PVT LTD.',
                  'subject' => 'subject',
                  'body' => 'body',
                  'data' => $appSecurtyData,
                );
                $emailIds[] = $custDetails->email;
                \Event::dispatch("APP_SECURITY_DOCUMENT_RENEWAL_ALERT", serialize($emailData));
              }
            }
           }
          } catch (\Exception $ex) {
              return Helpers::getExceptionMessage($ex);
          }
    }
}
