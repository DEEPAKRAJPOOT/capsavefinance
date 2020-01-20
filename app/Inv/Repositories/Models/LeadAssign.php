<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class LeadAssign extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lead_assign';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'lead_assign_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_id',
        'to_id',
        'assigned_user_id',
        'app_id',
        'assign_status',
        'sharing_comment',
        'is_owner',
        'created_by',
        'updated_by'
    ];

    /**
     * update user details
     *
     * @param integer $user_id     user id
     * @param array   $arrUserData user data
     *
     * @return boolean
     */
    public static function createLeadAssign($arrLeadAssign) {
        //Check data is Array
        if (!is_array($arrLeadAssign)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($arrLeadAssign)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        $arrLeadAssign = self::create($arrLeadAssign);

        return ($arrLeadAssign->lead_assign_id ?: false);
    }
   
    /**
     * Get Assigned Sales Manager User Id
     * 
     * @param integer $userId
     * @return integer
     */
    public static function getAssignedSalesManager($userId)
    {
        $result = self::select('lead_assign.to_id')
                ->join('role_user', 'role_user.user_id', '=', 'lead_assign.to_id')
                ->where('lead_assign.assigned_user_id', $userId)
                ->where('lead_assign.is_owner', 1)
                ->where('role_user.role_id', 4)   //4=>Sales Manager Role
                ->first();
        
        return $result ? $result->to_id : null;
    }
}