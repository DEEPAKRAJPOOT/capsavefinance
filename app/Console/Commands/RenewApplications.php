<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RenewApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:renewapplication';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for renewal applications and send notifications';

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
        $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface');
        
        $appData = $appRepo->getRenewalApp();
        foreach($appData as $app) {
            $appId = $app->app_id;
            $bizId = $app->biz_id;
            
            $user = User::getfullUserDetail((int)$approver->user_id);
            $emailData['app_id'] = \Helpers::formatIdWithPrefix($application->app_id, 'APP');
            $emailData['receiver_user_name'] = $user->f_name .' '. $user->m_name .' '. $user->l_name;
            $emailData['receiver_role_name'] = '';//$user->roles[0]->name;
            $emailData['receiver_email'] = $user->email;
            $emailData['cover_note'] = (isset($reviewerSummaryData->cover_note))?$reviewerSummaryData->cover_note:'';  
            $allEmailData[] = $emailData;
        
            \Event::dispatch("APPLICATION_APPROVER_MAIL", serialize($allEmailData));
        }
            
    }
}
