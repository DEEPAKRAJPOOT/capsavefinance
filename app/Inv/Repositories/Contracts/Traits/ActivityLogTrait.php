<?php
namespace App\Inv\Repositories\Contracts\Traits;

use App\Inv\Repositories\Models\ActivityLog;
use Request;
use Session;

trait ActivityLogTrait
{
    public function activityLogByTrait($activity_type_id, $activity_desc, $data, $arrActivity=null) {
        $loginId = \Auth::user()->user_id;
        $userDetils = $this->userRepo->getUserDetail($loginId);
        
        $arrActivity['session_id'] = \Session::get('uuid') ? \Session::get('uuid') : null;
 
        $arrActivity['activity_id'] = $activity_type_id;
        $arrActivity['activity_desc'] = $activity_desc . (isset($arrActivity['auto_logout']) ? ' (timed out)' : '');
        $arrActivity['data'] = $data;
        $arrActivity['status'] = 1;

        if (!isset($arrActivity['ip_address'])) {
            $arrActivity['ip_address'] = Request::getClientIp();
        }

        $arrActivity['source'] = Request::server('HTTP_REFERER');
        $arrActivity['browser_info'] = Request::server('HTTP_USER_AGENT');
        $arrActivity['route_name'] = Request::route()->getName();
        $arrActivity['user_id'] = $loginId;
        $arrActivity['email'] = $userDetils->email;

        $objActivity = new ActivityLog($arrActivity);
        $saved = $objActivity->save();

        return $saved;
    }

    public static function staticActivityLogByTrait($activity_type_id, $activity_desc, $data, $arrActivity=null) {
        $loginId = \Auth::user()->user_id;
        $userRepo = \App::make('App\Inv\Repositories\Contracts\UserInterface');
        $userDetils = $userRepo->getUserDetail($loginId);
        $arrActivity['session_id'] = \Session::get('uuid') ? \Session::get('uuid') : null;
 
        $arrActivity['activity_id'] = $activity_type_id;
        $arrActivity['activity_desc'] = $activity_desc . (isset($arrActivity['auto_logout']) ? ' (timed out)' : '');
        $arrActivity['data'] = $data;
        $arrActivity['status'] = 1;

        if (!isset($arrActivity['ip_address'])) {
            $arrActivity['ip_address'] = Request::getClientIp();
        }

        $arrActivity['source'] = Request::server('HTTP_REFERER');
        $arrActivity['browser_info'] = Request::server('HTTP_USER_AGENT');
        $arrActivity['route_name'] = Request::route()->getName();
        $arrActivity['user_id'] = $loginId;
        $arrActivity['email'] = $userDetils->email;

        $objActivity = new ActivityLog($arrActivity);
        $saved = $objActivity->save();

        return $saved;
    }
}
