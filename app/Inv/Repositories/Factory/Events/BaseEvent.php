<?php

namespace App\Inv\Repositories\Factory\Events;

use Request;
use App\Events\Event;
use App\Inv\Repositories\Models\ActivityLog;

abstract class BaseEvent extends Event {

    /**
     * Save activity log
     *
     * @param integer $activity_type_id
     * @param string $activity_desc
     * @param array $arrActivity
     * @return bool
     * @since 0.1
     */ 
    public static function addActivityLog($activity_type_id, $activity_desc, $arrActivity)
    {
        $arrActivity['session_id']       = \Session::get('uuid') ? \Session::get('uuid') : null;
 
        $arrActivity['activity_type_id'] = $activity_type_id;
        $arrActivity['activity_desc'] = $activity_desc . (isset($arrActivity['auto_logout']) ? ' (timed out)' : '');
        $arrActivity['status'] = 1;

        if (!isset($arrActivity['ip_address'])) {
            $arrActivity['ip_address'] = Request::getClientIp();
        }

        $arrActivity['source'] = Request::server('HTTP_REFERER');
        $arrActivity['browser_info'] = Request::server('HTTP_USER_AGENT');
        $arrActivity['route_name'] = Request::route()->getName();

        $objActivity = new ActivityLog($arrActivity);
        $saved = $objActivity->save();

        return $saved;
    }

}
