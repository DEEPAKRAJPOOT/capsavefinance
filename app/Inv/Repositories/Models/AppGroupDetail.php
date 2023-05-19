<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use App\Inv\Repositories\Factory\Models\BaseModel;

class AppGroupDetail extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_group_detail';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_group_detail_id';

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
        'group_id',
        'ucic_id',
        'app_id',
        'biz_app_id',
        'biz_id',
        'borrower',
        'product_id',
        'sanction',
        'outstanding',
        'proposed',
        'status',
        'final_sanction',
        'final_outstanding',
        'freezed_at',
        'is_latest',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function app(){
        return $this->belongsTo('App\Inv\Repositories\Models\Application', 'app_id', 'app_id');
    }
    
    public function group(){
        return $this->belongsTo('App\Inv\Repositories\Models\Master\NewGroup', 'group_id', 'group_id');
    }

    public static function updateAppGroupDetails($whereCondition, $whereInCondition, $data)
    {
        $query = '';
        $isUpdateData = false;
        if (isset($whereCondition)) {
            $query = self::where($whereCondition);
        }
        if (isset($whereInCondition)) {
            $query = self::whereIn($whereInCondition);
        }
        if ($query) {
            $isUpdateData = $query->update($data);
        }
        return $isUpdateData;
    }

    public static function getGroupDataByAppId($appId){
        
        $groupDatasArray = self::where('app_id', $appId)->get()->toArray();
        return is_array($groupDatasArray) && count($groupDatasArray) ? $groupDatasArray : [];
    }
    
    public static function deleteGroupDataByAppId($appId)
    {
        return self::where('app_id', $appId)->delete();
    }

    public static function getUcicDataByGroupId($groupId)
    {
        $approvedStatus = \Helpers::approvalStatusOfAppForGroupExpoInArray();
        return self::where('group_id', $groupId)
                    ->where('status', 1)
                    ->whereHas('app', function ($newQuery) use($approvedStatus) {
                        $newQuery->whereIn('curr_status_id', $approvedStatus);
                        $newQuery->whereHas('ucicUser');
                    })
                    ->groupBy('app_id');
    }

    public function ucic(){
        return $this->belongsTo('App\Inv\Repositories\Models\UcicUser', 'app_id', 'app_id');
    }
}
