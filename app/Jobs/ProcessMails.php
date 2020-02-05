<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Inv\Repositories\Models\AppAssignment;
use App\Inv\Repositories\Entities\User\UserRepository;
use App\Inv\Repositories\Entities\Application\ApplicationRepository;


class ProcessMails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $assignmentData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AppAssignment $assignmentData)
    {
        $this->assignmentData = $assignmentData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(UserRepository $userRepo, ApplicationRepository $appRepo)
    {   
        try {
            $assignmentData = $this->assignmentData;    
            $event = '';
            $from_user = '';
            $to_users = [];
            $to_all = false;
            
            if($assignmentData->count()){
                switch ($assignmentData->assign_type) {
                    case '0': //New
                        break;
                    case '1': //Pickup
                        $event = "APPLICATION_PICKUP";
                    break;
                    case '2': //Next 
                        if(is_null($assignmentData->to_id) && $assignmentData->role_id){
                            $event = "APPLICATION_MOVE_NEXT_POOL";
                            $to_all = true;
                        }else{
                            $event = "APPLICATION_MOVE_NEXT_USER";
                        }
                        break;
                    case '3': //Back
                        $event = "APPLICATION_MOVE_BACK";
                        
                    break;
                }

                $from_users = $userRepo->getfullUserDetail($assignmentData->from_id);
                $application = $appRepo->getAppDataByAppId($assignmentData->app_id);

                $emailData['sender_user_name'] = $from_users->f_name .' '. $from_users->m_name .' '. $from_users->l_name ;
                $emailData['sender_role_name'] = '';//$from_users->roles[0]->name;
                $emailData['lead_id'] = (isset($application->user_id))?'000'.$application->user_id:'';
                $emailData['app_id'] = 'CAPS000'.$assignmentData->app_id;
                $emailData['entity_name'] = (isset($application->business->biz_entity_name))?$application->business->biz_entity_name:'';
                $emailData['comment'] = $assignmentData->sharing_comment;

                if($to_all){
                    $to_users = $userRepo->getBackendUsersByRoleId($assignmentData->role_id);
                    foreach($to_users as $user) {
                        $emailData['receiver_user_name'] = $user->f_name .' '. $user->m_name .' '. $user->l_name;
                        $emailData['receiver_role_name'] = '';//$user->roles[0]->name;
                        $emailData['receiver_email'] = 'sudesh.kumar@prolitus.com';//$user->email;
                        \Event::dispatch($event, serialize($emailData));
                    }
                }else{
                    $user = $userRepo->getfullUserDetail($assignmentData->to_id);
                    $emailData['receiver_user_name'] = $user->f_name .' '. $user->m_name .' '. $user->l_name;
                    $emailData['receiver_role_name'] = '';//$user->roles[0]->name;
                    $emailData['receiver_email'] =  'sudesh.kumar@prolitus.com';//$user->email;
                    \Event::dispatch($event, serialize($emailData));
                }
            }
        }
        catch(Exception $e) {
            $this->failed($e);
        }
    }

    public function failed($exception)
    {
        dump($exception->getMessage());
    }
}
