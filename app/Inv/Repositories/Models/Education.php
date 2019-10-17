<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Education extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_education';

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
    public $userstamps = true;
    
    // Column for soft deletes
    protected $dates   = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'university',
        'course',
        'date_attended_from_year',
        'date_attended_to_year',
        'remarks',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at'
    ];

    /*
     * Save or update education details
     * @param $postArr, $id
     * return int
     */

    public static function updateOrSaveEducationDetail($postArr, $id = null)
    {
        //Check id is integer
        if (!is_int($id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $res = self::updateOrCreate(['id' => $id], $postArr);
        return $res->id ?: false;
    }

    /**
     * Save Education details
     *
     * @param  array $arrEducation
     *
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function saveEducationDetails($arrEducation = [])
    {

        //Check Data is Array
        if (!is_array($arrEducation)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        //Check Data is not blank
        if (empty($arrEducation)) {
            throw new BlankDataExceptions(trans('error_message.data_not_found'));
        }
        /**
         * Create Education
         */
        $objEducation = self::create($arrEducation);

        return ($objEducation->id ?: false);
    }

    /**
     * Get Education
     *
     * @param  integer $userId     
     *
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function getEductions($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrEducation = self::where('user_id', (int) $userId)
            ->get();

        return ($arrEducation ?: false);
    }

    /**
     * Delete educations
     *
     * @param  integer $userId
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function deleteEucations($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $eduDelete = self::where('user_id', '=', $userId)->delete();

        return ($eduDelete ?: false);
    }
}