<?php

namespace App\Inv\Repositories\Models;

use DB;
use Carbon\Carbon;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class OfferAdhocDocument extends BaseModel
{

    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'app_offer_adhoc_doc';

     /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'offer_adhoc_doc_id';

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
        'offer_adhoc_limit_id',
        'adhoc_doc_file_id',
        'created_at',
        'created_by'
    ];

    public function file(){
        return $this->belongsTo('App\Inv\Repositories\Models\UserFile', 'file_id', 'adhoc_doc_file_id');
    }
}
