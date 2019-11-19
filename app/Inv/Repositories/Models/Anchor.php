<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;


class Anchor extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'anchor';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'anchor_id';

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
        'comp_name',
        'comp_email',
        'comp_addr',
        'comp_state',
        'comp_city',
        'comp_zip',
        'comp_phone',
        'doc_name',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
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
public static function saveAnchor($arrAnchor = [])
    {
        //Check data is Array
        if (!is_array($arrAnchor)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        //Check data is not blank
        if (empty($arrAnchor)) {
            throw new BlankDataExceptions(trans('error_message.data_not_found'));
        }
        
        /**
         * Create anchor
         */
        $arrAnchorVal = self::create($arrAnchor);

        return ($arrAnchorVal->anchor_id ?: false);
    }
}