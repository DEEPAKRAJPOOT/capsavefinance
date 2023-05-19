<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;

class NewGroup extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_group_new';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'group_id';

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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_name',
        'group_code',
        'current_group_sanction',
        'current_group_outstanding',
        'group_field_1',
        'group_field_2',
        'group_field_3',
        'group_field_4',
        'group_field_5',
        'group_field_6',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    /**
     * Create and update group
     * 
     * @param array $attributes
     * @param int $id
     * @return type
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function updateOrCreateGroup($attributes , $id = null) 
    {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        
        if ($id) {
            $res = self::where('group_id', $id)->first()->update($attributes);
        } else {
            $res = self::create($attributes);
        }
        return $res ?: false;
    }

    /**
     * Get All group
     * 
     * @return type
     */
    public static function getAllGroup() 
    {
       $res = self::orderBy('group_id', 'DESC');
       return $res ?: false;
    }
    
    /**
     * Get group by Id
     * 
     * @param int $id
     * @return type
     * @throws InvalidDataTypeExceptions
     */
    public static function getGroupById($id) 
    {
        if(!is_int($id)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $res = self::where('group_id', $id)->first();
        return $res ?: false;
    }
    
    /**
     * Get All Active group
     * 
     * @return type
     */
    public static function getAllActiveGroup($whereIn) 
    {
       $query = self::where('is_active', 1)
                ->whereHas('makerCheckers', function ($query) {
                    $query->where('status', 1);
                });
        if (count($whereIn)) {
            $query->whereIn('group_id', $whereIn);
        }        
        $res = $query->get();
       return $res ?: false;
    }
    
    /**
     * Check Charge Name
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function checkGroupName($groupName, $groupId=null)
    {
        $query = self::select('group_id')->where('group_name', $groupName);
        if (!is_null($groupId)) {
            $query->where('group_id', '!=', $groupId);
        }
        $res = $query->get();
        return $res ?: [];
    }

    public function makerCheckers()
    {
        return $this->hasMany('App\Inv\Repositories\Models\Master\MakerChecker', 'group_id', 'group_id');
    }

    public static function checkGroupNameSuggestions($groupName)
    {
        return self::where('group_name', 'like', "%$groupName%")
                    ->whereHas('makerCheckers', function ($query) {
                        $query->where('status', 1);
                    })
                    ->pluck('group_name')->toArray();
    }

    public function appGroupDetails()
    {
        return $this->hasMany('App\Inv\Repositories\Models\AppGroupDetail', 'group_id', 'group_id');
    }

    public static function getCurrentActiveGroupSanction($groupId)
    {
        $approvedStatus = \Helpers::approvalStatusOfAppForGroupExpoInArray();     

        $groupData = \DB::table('app_group_detail')
                ->select(\DB::raw('SUM(sanction) as sanction, SUM(outstanding) as outstanding'))
                ->whereIn('app_group_detail_id', function($query) use($groupId, $approvedStatus) {
                    $query->select(\DB::raw('MAX(app_group_detail_id)'))
                        ->from('app_group_detail')
                        ->join('app', 'app.app_id', '=', 'app_group_detail.biz_app_id')
                        ->join('mst_maker_checker', 'mst_maker_checker.group_id', '=', 'app_group_detail.group_id')           
                        ->where('app_group_detail.group_id', $groupId)
                        ->where('app_group_detail.status', 1)
                        ->where('mst_maker_checker.status', 1)
                        ->whereIn('curr_status_id', $approvedStatus)
                        ->groupBy('biz_app_id', 'product_id');
                })
                ->first();
        $sanctionAmt = $groupData ? $groupData->sanction : 0;
        $outstandingAmt = $groupData ? $groupData->outstanding : 0;
        return ['sanctionAmt' => $sanctionAmt, 'outstandingAmt' => $outstandingAmt];
    }
}
