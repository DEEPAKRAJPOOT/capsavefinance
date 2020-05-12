<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Auth;
use Carbon\Carbon;

class AnchorRelation extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'anchor_relation';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'anchor_relation_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

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
                'biz_id',
                'app_id',
                'year',
                'year_of_association',
                'year_of_assoc_actual',
                'year_of_assoc_remark',
                'payment_terms',
                'grp_rating',
                'contact_person',
                'contact_number',
                'dependence_on_anchor',
                'dependence_on_anchor_actual',
                'dependence_on_anchor_remark',
                'qoq_ot_from_anchor',
                'qoq_ot_from_anchor_actual',
                'qoq_ot_from_anchor_remark',
                'cat_relevance_by_anchor',
                'cat_relevance_by_anchor_actual',
                'cat_relevance_by_anchor_remark',
                'security_deposit',
                'repayment_track_record',
                'repayment_track_record_actual',
                'repayment_track_record_remark',
                'sec_third_gen_trader',
                'gen_trader_actual',
                'gen_trader_remark',
                'alt_buss_of_trader',
                'alt_buss_of_trader_actual',
                'alt_buss_of_trader_remark',
                'self_owned_prop',
                'self_owned_prop_actual',
                'self_owned_prop_remark',
                'trade_ref_check_actual',
                'trade_ref_check_remark',
                'adv_tax_payment',
                'adv_tax_payment_actual',
                'adv_tax_payment_remark',
                'note_on_lifting',
                'reference_from_anchor',
                'anchor_risk_comments',
                'created_by',
                'created_at',
                'updated_at',
                'updated_by'
    ];

    /*
     * save data in table
     */

    public static function creates($attributes) {
        self::create($attributes);
        return true;
    }

}
