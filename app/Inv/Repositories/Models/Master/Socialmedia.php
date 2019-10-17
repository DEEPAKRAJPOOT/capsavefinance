<?php

namespace App\Inv\Repositories\Models\Master;

use Carbon\Carbon;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Socialmedia extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_socialmedia';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';

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
    public $userstamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'is_active'
    ];

    /**
     * Scopes for active list
     *
     * @param string $query
     * @param string $type
     * @return type
     */
    public function scopeActive($query, $type)
    {
        return $query->where('is_active', $type);
    }

    /**
     * Get Drop down list
     *
     * @return type
     */
    public static function getDropDown()
    {
        return self::active(config('inv_common.ACTIVE'))->pluck('title', 'id');
    }

    /**
     * Get all Cluster
     *
     * @return type
     */
    public static function getSocialmediaList()
    {
        $cluster = self::select('id', 'title', 'is_active')
            ->whereNull('deleted_at')->get();
        return $cluster ?: false;
    }
    /*
     * get Country data by id
     * @param $id
     */

    public static function getSocialmediaById($id)
    {
        //Check id is not blank
        if (empty($id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        //Check id is not an integer

        if (!is_int($id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $data = self::select('*')
            ->where('id', $id)
            ->first();
        return ($data ? $data : false);
    }
    /*
     * Save or update country
     * @param $postArr, $id
     * return int
     */

    public static function saveOrEditSocialmedia($postArr, $id = null)
    {

        if (!is_int($id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $res = self::updateOrCreate(['id' => $id], $postArr);

        return $res->id ?: false;
    }

    /**
     * Delete Cluster
     * @param array $cid
     * @return integer $res
     */
    public static function deleteSocialmedia($cid)
    {
        $res = self::whereIn('id', $cid)->update(['deleted_at' => Carbon::now()]);
        return $res ? $res : false;
    }
}