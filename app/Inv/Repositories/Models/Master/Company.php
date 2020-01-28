<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\Exceptions\InvalidDataTypeExceptions;

class Company extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_company';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'company_id';

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = false;

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
        'cmp_name',
        'cmp_add',
        'gst_no',
        'pan_no',
        'cin_no',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public static function saveCompanies($arrCompany) {
        //Check data is Array
        if (!is_array($arrCompany)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($arrCompany)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
        $res = self::create($arrCompany);
        return $res ?: false;
    }

    public static function getAllCompanies($key) {

        $res = self::where('is_active', '!=', '2')
                ->orderBy('company_id', 'DESC');
        
        if (isset($key['search_keyword'])) {
            if ($key['search_keyword'] != "") {
                $search_keyword = trim($key['search_keyword']);
                $search_keyword = strtolower($search_keyword);
                $res->where(function ($res) use ($search_keyword) {
                    $res->where('mst_company.cmp_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orWhere('mst_company.cmp_add', 'LIKE', '%' . $search_keyword . '%');
                });
            }
        }
        
        $res = $res->get();
        
        return $res ?: false;
    }

    public static function findCompanyById($id) {
        
        if (empty($id)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        $res = self::find($id);

        return $res ?: false;
    }

    public static function updateCompanies($compArr, $companyId) {

        if (!is_array($compArr)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($compArr)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
        
        $res = self::where('company_id', $companyId)->first()->update($compArr);
        
        return $res ?: false;
    }
    
    

}
