<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class NonAnchorLead extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'non_anchor_leads';

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = true;
    
    protected $fillable = [
        'user_id',
        'f_name',
        'l_name',
        'is_buyer',
        'user_type',
        'email',
        'mobile_no',
        'biz_name',
        'pan_no',
        'is_registered',
        'assign_sale_manager',
        'reg_token',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * Save Non Anchor Lead
     *
     * @param  array $arrUsers
     *
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
    */
    public static function saveLead($arrUsers = [])
    {
        //Check data is Array
        if (!is_array($arrUsers)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        //Check data is not blank
        if (empty($arrUsers)) {
            throw new BlankDataExceptions(trans('error_message.data_not_found'));
        }
        /**
         * Create Non Anchor Lead
        */
        $objUsers = self::create($arrUsers);

        return ($objUsers ?: false);
    }

    public function user()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\User', 'user_id', 'user_id');
    }

    public static function getNonAnchorLeadByRegToken($token){
        $arrUser = self::where('reg_token', $token)->first();
        return ($arrUser ? $arrUser : FALSE);
    }

    public static function getNonAnchorLeadByPan($pan){
        $arrUser = self::where('pan_no', $pan)->first();
        return ($arrUser ? $arrUser : FALSE);
    }

    public static function getNonAnchorLeadById($id){
        $arrUser = self::find($id);
        return ($arrUser ? $arrUser : FALSE);
    }
    
    public function getFullNameAttribute(){ 
        return $this->f_name.' '.$this->l_name;
    }

    public static function updateNonAnchorLeadData($whereCond, $arrUserData){
        $rowUpdate = self::where($whereCond)->update($arrUserData);
        return ($rowUpdate ? true : false); 
    }

    public static function getAllNonAnchorLeads()
    {
        $result = self::where('user_type', 1);
        return ($result ? $result : '');
    }
}


