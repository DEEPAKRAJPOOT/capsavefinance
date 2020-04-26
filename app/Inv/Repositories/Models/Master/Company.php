<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Factory\Models\Master\State;
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
    protected $primaryKey = 'comp_add_id';

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
        'comp_name_id',
        'cmp_name',
        'cmp_add',
        'gst_no',
        'pan_no',
        'cin_no',
        'is_active',
        'state',
        'city',
        'is_reg',
        'created_at',
        'updated_at',
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
                ->orderBy('comp_add_id', 'DESC');
        
        if (isset($key['search_keyword'])) {
            if ($key['search_keyword'] != "") {
                $search_keyword = trim($key['search_keyword']);
                $search_keyword = strtolower($search_keyword);
                $res->where(function ($res) use ($search_keyword) {
                    $res->where('mst_company.comp_add_id', 'LIKE', '%' . $search_keyword . '%')
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

        $res = self::with('state')->where(['comp_add_id'=>$id])->first();

        return $res ?: false;
    }
    
    function state()
    {
        
     return $this->belongsTo('App\Inv\Repositories\Models\Master\State', 'state','id')->where(['is_active' => 1]);  
       
   }

    public static function updateCompanies($compArr, $companyId) {

        if (!is_array($compArr)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($compArr)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
        
        $res = self::where('comp_add_id', $companyId)->first()->update($compArr);
        
        return $res ?: false;
    }

    public static  function companyAdress()
    {
        return  self::where(['company_id' => 1,'is_active' =>1])->pluck('state')->first();
        
    } 
    
    public static function checkIsRegCompany($name,$is_reg) {
        
        if (empty($is_reg) || empty($name)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        $res = self::where(['cmp_name' => $name, 'is_reg' => $is_reg])->first();
        
        return $res;
    }
    
    public static function getCompAddByCompanyName($where)
    {
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        
        $res = self::where($where)->first();
        
        return $res ?: false;
    }
    
    public static function getCompNameByCompId($compId){
        
        $compName = self::select('cmp_name')->where('comp_add_id', $compId)->first();
//        dd($compName);
        $CompIdArr = self::where(['cmp_name' => $compName->cmp_name])->get();
//        dd($CompIdArr);
        return $CompIdArr;
        
    }

}
