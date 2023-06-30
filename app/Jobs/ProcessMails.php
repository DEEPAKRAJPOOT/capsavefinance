<?php

namespace App\Jobs;

use Exception;
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
            $to_users = [];
            $to_all = false;
            if (!is_null($assignmentData->to_id)) {
                $userRoleData = $userRepo->getRoleDataById($assignmentData->to_id);
            }
            if($assignmentData->count()){
                switch ($assignmentData->assign_type) {
                    case '0': //New
                        break;
                    case '1': //Pickup
                        $event = "APPLICATION_PICKUP";
                        $to_all = true;
                    break;
                    case '2': //Next 
                        if(is_null($assignmentData->to_id) && $assignmentData->role_id){
                            $event = "APPLICATION_MOVE_NEXT_POOL";
                            $to_all = true;
                        } else if (($assignmentData->to_id == $assignmentData->from_id) && $userRoleData->role_id == 10){
                            $event = "APPLICATION_MOVE_LMS";
                        } else {
                            $event = "APPLICATION_MOVE_NEXT_USER";
                        }
                    break;
                    case '3': //Back
                        $event = "APPLICATION_MOVE_BACK";
                        
                    break;
                }
                if($assignmentData->assign_type == '1'){
                    $from_user = $userRepo->getfullUserDetail($assignmentData->to_id);
                }else{
                    $from_user = $userRepo->getfullUserDetail($assignmentData->from_id);
                }
                
                $application = $appRepo->getAppDataByAppId($assignmentData->app_id);
                $emailData['lead_id'] = \Helpers::formatIdWithPrefix($application->user_id, 'LEADID');
                $emailData['entity_name'] = (isset($application->business->biz_entity_name))?$application->business->biz_entity_name:'';
                $emailData['app_id'] = \Helpers::formatIdWithPrefix($assignmentData->app_id, 'APP');
                $emailData['comment'] = $assignmentData->sharing_comment;
                $emailData['sender_user_name'] = $from_user->f_name .' '. $from_user->m_name .' '. $from_user->l_name ;
                $emailData['sender_role_name'] = '';//$from_user->roles[0]->name;
                if($to_all){
                    if(is_null($assignmentData->role_id)){
                        $to_user = $userRepo->getfullUserDetail($assignmentData->to_id);
                        $to_users = $userRepo->getBackendUsersByRoleId($to_user->roles[0]->id);
                        if ($event == "APPLICATION_PICKUP") {
                            $to_user = $userRepo->getfullUserDetail($assignmentData->to_id);
                            $to_users = $userRepo->getBackendUsersByRoleId($to_user->roles[0]->id, [$assignmentData->to_id], [$assignmentData->from_id]);
                        }
                    } else {
                        $to_users = $userRepo->getBackendUsersByRoleId($assignmentData->role_id);
                        if ($event == "APPLICATION_PICKUP") {
                            $to_users = $userRepo->getBackendUsersByRoleId($assignmentData->role_id, [$assignmentData->to_id], [$assignmentData->from_id]);
                        }
                    }
                    foreach($to_users as $user) {
                        $emailData['receiver_user_name'] = $user->f_name .' '. $user->m_name .' '. $user->l_name;
                        $emailData['receiver_role_name'] = '';//$user->roles[0]->name;
                        $emailData['receiver_email'] = $user->email;
                        \Event::dispatch($event, serialize($emailData));
                    }
                }else{
                    if ($event == 'APPLICATION_MOVE_LMS') {
                        $user = $application->user;
                    } else {
                        $user = $userRepo->getfullUserDetail($assignmentData->to_id);
                    }
                    $emailData['receiver_user_name'] = $user->f_name .' '. $user->m_name .' '. $user->l_name;
                    $emailData['receiver_role_name'] = '';//$user->roles[0]->name;
                    $emailData['receiver_email'] = $user->email;
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
        $exception->getMessage();
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addSeconds(5);
    }
}
