<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Asset extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_asset';

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
        'asset_type',
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
    
    public static function getAssetList()
    {
        $res = self::where('is_active',1)->pluck('asset_type', 'id');
        return $res ?: false;
    }

    public static function getAssetById($id)
    {
        $res =   self::select('asset_type')
                    ->where('id', $id)
                    ->where('is_active', 1)
                    ->first();
        return $res ?: false;
    }

    public static function getAllAssetList() 
    {
        $result = self::select('mst_asset.id', 'mst_asset.asset_type', 'mst_asset.created_at', 'mst_asset.is_active')
                    ->orderBy('mst_asset.id', 'DESC');
        return $result;
    }

    public static function saveAsset($arrAssetData)
    {
        if (!is_array($arrAssetData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $res = self::create($arrAssetData);
        return $res ?: false;
    }

    public static function updateAsset($arrAssetData, $id)
    {
        if (!is_array($arrAssetData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where('id', $id)->first()->update($arrAssetData);
    }

    /**
     * Check Equipment name
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function checkAssetByType($assetType, $id=null)
    {
        $query = self::select('id')
                ->where('asset_type', $assetType);
        if (!is_null($id)) {
            $query->where('id', '!=', $id);
        }
        $res = $query->get();        
        return $res ?: [];
    }
}