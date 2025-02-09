<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Equipment extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_equipment';

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
        'equipment_name',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ]; 
    
    
    
    /**
     * get Equipment list
     * 
     * @return mixed
     */
    
    public static function getEquipmentList()
    {
      $res =   self::where('is_active',1)->pluck('equipment_name', 'id');
      return $res ?: false;
    }

    public static function getEquipmentTypeById($id)
    {
      $res =   self::select('equipment_name')
                    ->where('id',$id)
                    ->where('is_active',1)
                    ->first();
      return $res ?: false;
    }

    public static function getAllEquipmentList() 
    {
        $result = self::select('mst_equipment.id', 'mst_equipment.equipment_name', 'mst_equipment.created_at', 'mst_equipment.is_active')
        ->orderBy('mst_equipment.id', 'DESC');
    return $result;
    }

    public static function saveEquipment($arrEquipmentData)
    {
        if (!is_array($arrEquipmentData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $res = self::create($arrEquipmentData);
        return $res ?: false;
    }

    public static function updateEquipment($arrEquipmentData, $equipment_id)
    {
        if (!is_array($arrEquipmentData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where('id', $equipment_id)->first()->update($arrEquipmentData);
    }

    /**
     * Check Equipment name
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function checkEquipmentName($equipmentName, $equipmentId=null)
    {
        $query = self::select('id')
                ->where('equipment_name', $equipmentName);
        if (!is_null($equipmentId)) {
            $query->where('id', '!=', $equipmentId);
        }
        $res = $query->get();        
        return $res ?: [];
    }     
}