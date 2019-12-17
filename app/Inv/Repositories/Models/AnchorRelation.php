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
                'payment_terms',
                'grp_rating',
                'contact_person',
                'contact_number',
                'security_deposit',
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
