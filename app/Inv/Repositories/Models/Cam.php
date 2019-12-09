<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;
//use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Factory\Models\BaseModel;

class Cam extends BaseModel
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cam_report';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'cam_report_id';

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
        'biz_id',
        'app_id',
        'operational_person',
        'program',
        'rating_no',
        'rating_comment',
        'existing_exposure',
        'proposed_exposure',
        't_o_f_limit',
        't_o_f_purpose', 
        't_o_f_takeout',   
        't_o_f_recourse',
        't_o_f_security',
        't_o_f_adhoc_limit',
        't_o_f_covenants',
        't_o_f_profile_comp',
        'risk_comments',
        'cm_comment',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public static function creates($attributes, $userId){
        $inputArr= array(
            'biz_id'=>$attributes['biz_id'],
            'app_id'=>$attributes['app_id'],
            'operational_person'=>$attributes['operational_person'],
            'program'=>$attributes['program'],
            'rating_no'=>$attributes['rating_no'],
            'rating_comment'=>$attributes['rating_comment'],
            'existing_exposure'=>$attributes['existing_exposure'],
            'proposed_exposure'=>$attributes['proposed_exposure'],
            't_o_f_limit'=>$attributes['t_o_f_limit'],
            't_o_f_purpose'=>$attributes['t_o_f_purpose'],
            't_o_f_takeout'=>$attributes['t_o_f_takeout'],
            't_o_f_recourse'=>$attributes['t_o_f_recourse'],
            't_o_f_security'=>$attributes['t_o_f_security'],
            't_o_f_adhoc_limit'=>$attributes['t_o_f_adhoc_limit'],
            't_o_f_covenants'=>$attributes['rating_comment'],
            't_o_f_profile_comp'=>$attributes['t_o_f_profile_comp'],
            'risk_comments'=>$attributes['risk_comments'],
            'cm_comment'=>$attributes['cm_comment'],
            'created_by'=>$userId
        );
        $cam = Cam::create($inputArr);
        return  $cam ? true : false;

    }

    public static function updateCamData($attributes, $userId){
        $cam = Cam::where('app_id', $attributes['app_id'])->first();

        //update Cam table
        $updateCamData = $cam->update([
                    'operational_person'=>$attributes['operational_person'],
                    'program'=>$attributes['program'],
                    'rating_no'=>$attributes['rating_no'],
                    'rating_comment'=>$attributes['rating_comment'],
                    'existing_exposure'=>$attributes['existing_exposure'],
                    'proposed_exposure'=>$attributes['proposed_exposure'],
                    't_o_f_limit'=>$attributes['t_o_f_limit'],
                    't_o_f_purpose'=>$attributes['t_o_f_purpose'],
                    't_o_f_takeout'=>$attributes['t_o_f_takeout'],
                    't_o_f_recourse'=>$attributes['t_o_f_recourse'],
                    't_o_f_security'=>$attributes['t_o_f_security'],
                    't_o_f_adhoc_limit'=>$attributes['t_o_f_adhoc_limit'],
                    't_o_f_covenants'=>$attributes['rating_comment'],
                    't_o_f_profile_comp'=>$attributes['t_o_f_profile_comp'],
                    'risk_comments'=>$attributes['risk_comments'],
                    'cm_comment'=>$attributes['cm_comment'],
                    'updated_by'=>$userId,
        ]);
        return $updateCamData ? true : false;
    }
}