<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class Research extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_reasearch_publication';

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'journal_magazine',
        'publication_month',
        'publication_year',
        'attachment',
        'is_deleted',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at'
    ];

    /**
     * Save Research and publications details
     *
     * @param  array $arrResearch
     *
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function saveReasearchDetails($arrResearch = [])
    {

        //Check Data is Array
        if (!is_array($arrResearch)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        //Check Data is not blank
        if (empty($arrResearch)) {
            throw new BlankDataExceptions(trans('error_message.data_not_found'));
        }
        /**
         * Create Education
         */
        $objResearch = self::create($arrResearch);

        return ($objResearch->id ?: false);
    }

    /**
     * Get researches
     *
     * @param  integer $userId
     *
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function getResearches($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrResearch = self::where('user_id', (int) $userId)
            ->get();

        return ($arrResearch ?: false);
    }

        /**
     * Get researches
     *
     * @param  integer $userId
     *
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function getResearchesById($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrResearch = self::where('user_id', (int) $userId)
                ->where('is_deleted', 1)
            ->get();

        return ($arrResearch ?: false);
    }
    
    /**
     * Delete researches
     *
     * @param  integer $userId
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function deleteResearches($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $resDelete = self::where('user_id', '=', $userId)->delete();

        return ($resDelete ?: false);
    }
    
    public static function deleteResearchesbyFlag($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $resDelete = self::where('user_id', '=', $userId)->where('is_deleted', 1)->delete();

        return ($resDelete ?: false);
    }
    
    
        /**
     * Get right by right & user id
     *
     * @param Integer $userId
     * @param Integer $rightId
     * @return obj|array|boolean
     * @throws InvalidDataTypeExceptions
     */
    public static function getAttachmentResearch($userId, $file_id)
    {
        /**
         * Check id is not an integer
         */
        if (!is_int($userId)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        
        //$arrRightData = self::
            $arrRightData = self::from('user_reasearch_publication as ra')
                ->where('ra.user_id', (int) $userId)
                ->where('ra.attachment', $file_id)
                ->get();

        return ($arrRightData ?: false);
    }
    
    /**
     * Update Research
     * 
     * @param Integer $rightId
     * @param Array $arrData
     * @return Integer|boolean
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function updateResearch($research_id, $arrData = [])
    {
        //Check id is not an integer
        if (!is_int($research_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        /**
         * Update Right
         */
        $rowUpdate = self::findOrFail((int) $research_id)->update($arrData);
        return ($rowUpdate ? (int) $research_id : false);
    }
    
        /**
     * Update Research
     * 
     * @param Integer $rightId
     * @param Array $arrData
     * @return Integer|boolean
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function updateflagdeleteResearches($user_id)
    {
        //dd($user_id);
        $arrData["is_deleted"] = 1;
        //Check id is not an integer
        if (!is_int($user_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $res = self::where('user_id', $user_id)->update($arrData);
        return $res ? $res : false;
    }
    
}