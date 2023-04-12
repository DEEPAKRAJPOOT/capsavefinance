<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\AppApprover;
use App\Inv\Repositories\Models\Application;
use Illuminate\Database\Eloquent\Builder;
use App\Inv\Repositories\Models\OfferPrimarySecurity;
use App\Inv\Repositories\Models\OfferCollateralSecurity;
use App\Inv\Repositories\Models\OfferPersonalGuarantee;
use App\Inv\Repositories\Models\OfferCorporateGuarantee;
use App\Inv\Repositories\Models\OfferEscrowMechanism;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\LmsUsersLog;

class AppProgramOfferDsa extends BaseModel {

    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'app_prgm_offer_dsa';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'offer_dsa_id';

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
        'prgm_offer_id',
        'dsa_name',
        'payout',
        'payout_event',
        'created_at',
        'created_by',
        'updated_at',        
        'updated_by'
    ];

    public function programOfferDsa(){
        return $this->hasOne('App\Inv\Repositories\Models\AppProgramOffer', 'prgm_offer_id', 'prgm_offer_id');
    }
}