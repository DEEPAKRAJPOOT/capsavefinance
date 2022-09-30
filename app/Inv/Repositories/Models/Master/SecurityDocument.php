<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;

class SecurityDocument extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_security_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'security_doc_id';

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
        'name',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public function userDetail(){
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check Security Document name
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function checkSecurityDocumentName($securityDocumentName, $excludeSecurityDocId=null)
    {

        $query = self::select('security_doc_id')
                ->where($securityDocumentName);
        if (!is_null($excludeSecurityDocId)) {
            $query->where('security_doc_id', '!=', $excludeSecurityDocId);
        }
        $res = $query->get();        
        return $res ?: [];
    } 
    
    public static function getSecurityDocumentDropDown()
    {
        $res = self::where('is_active', 1)->get();
        return $res ?: [];
    }
    
    public static function getSecurityDocumentName()
    {
        $res = self::where('is_active', 1)->pluck('name')->toArray();
        return $res ?: [];
    }
    

}
