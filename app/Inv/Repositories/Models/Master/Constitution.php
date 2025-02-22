<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Constitution extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_biz_constitution';

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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'cibil_lc_code',
        'is_active',
        'created_at',
        'updated_at'
    ];


    public static function getConstitutionDropDown()
    {
        $res = self::where('is_active', 1)->pluck('name', 'id');
        return $res ?: [];
    }

    /**
     * Get all Constitution
     *
     * @return type array
    */
    public static function getAllConstitution() 
    {
        $result = self::select('mst_biz_constitution.id', 'mst_biz_constitution.name', 'mst_biz_constitution.created_at', 'mst_biz_constitution.is_active')
        ->orderBy('mst_biz_constitution.id', 'DESC');
    return $result;
    }

    public static function saveConstitution($arrConstiData)
    {
        if (!is_array($arrConstiData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $res = self::create($arrConstiData);
        return $res ?: false;
    }

    public static function updateConstitution($arrConstiData, $id)
    {
        if (!is_array($arrConstiData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where('id', $id)->first()->update($arrConstiData);
    }

    /**
     * Check Constitution name
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function checkConsitutionName($constiName, $constitId=null)
    {
        $query = self::select('id')
                ->where($constiName);
        if (!is_null($constitId)) {
            $query->where('id', '!=', $constitId);
        }
        $res = $query->get();        
        return $res ?: [];
    }    
    
}