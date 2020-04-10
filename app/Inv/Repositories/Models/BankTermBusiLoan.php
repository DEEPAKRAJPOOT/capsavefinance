<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BankTermBusiLoan extends BaseModel
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bank_term_busi_loan';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'bank_term_busi_loan_id';

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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                        'bank_detail_id',
                        'bank_name_tlbl',
                        'loan_name',
                        'facility_amt',  
                        'facility_os_amt', 
                        'relationship_len_tlbl',
                        'is_active',             
                        'created_by',
                        'created_at',
                        'updated_at',
                        'updated_by'
    ];
}