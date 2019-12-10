<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;
//use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Factory\Models\BaseModel;

class CamHygiene extends BaseModel
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cam_hygiene';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'cam_hygiene_id';

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
                        'remarks', 
                        'rbi_willful_defaulters', 
                        'watchoutinvestors', 
                        'cibil_check', 
                        'politically_check', 
                        'politically_exposed_person_comments', 
                        'CDR_check', 
                        'CDR_comments', 
                        'UNSC_check', 
                        'UNSC_comments',
                        'npa_history_check', 
                        'npa_history_comments', 
                        'cop_gov_check', 
                        'cop_gov_issues_comments', 
                        'change_auditor_check', 
                        'change_in_auditor_comments', 
                        'change_auditor_qualification_check', 
                        'change_auditor_qualification_comment', 
                        'audit_report_check', 
                        'audit_report_comment', 
                        'adequate_insurance_check', 
                        'adequate_insurance_comments', 
                        'negative_news_report_check', 
                        'negative_news_report_comments', 
                        'cm_comment',
                        'created_by',
                        'created_at',
                        'updated_at',
                        'updated_by'
 ];


    public static function creates($attributes, $userId){
        $inputArr= array(
                        'biz_id' => $attributes['biz_id'],
                        'app_id' => $attributes['app_id'],
                        'remarks' => $attributes['remarks'], 
                        'rbi_willful_defaulters' => $attributes['rbi_willful_defaulters'], 
                        'watchoutinvestors' => $attributes['watchoutinvestors'], 
                        'cibil_check' => $attributes['cibil_check'], 
                        'politically_check' => $attributes['politically_check'], 
                        'politically_exposed_person_comments' => $attributes['politically_exposed_person_comments'], 
                        'CDR_check' => $attributes['CDR_check'], 
                        'CDR_comments' => $attributes['CDR_comments'], 
                        'UNSC_check' => $attributes['UNSC_check'],
                        'UNSC_comments' => $attributes['UNSC_comments'],
                        'npa_history_check' => $attributes['npa_history_check'], 
                        'npa_history_comments' => $attributes['npa_history_comments'], 
                        'cop_gov_check' => $attributes['cop_gov_check'], 
                        'cop_gov_issues_comments' => $attributes['cop_gov_issues_comments'], 
                        'change_auditor_check' => $attributes['change_auditor_check'], 
                        'change_in_auditor_comments' => $attributes['change_in_auditor_comments'], 
                        'change_auditor_qualification_check' => $attributes['change_auditor_qualification_check'], 
                        'change_auditor_qualification_comment' => $attributes['change_auditor_qualification_comment'], 
                        'audit_report_check' => $attributes['audit_report_check'], 
                        'audit_report_comment' => $attributes['audit_report_comment'], 
                        'adequate_insurance_check' => $attributes['adequate_insurance_check'], 
                        'adequate_insurance_comments' => $attributes['adequate_insurance_comments'], 
                        'negative_news_report_check' => $attributes['negative_news_report_check'], 
                        'negative_news_report_comments' => $attributes['negative_news_report_comments'], 
                        'created_by'=>$userId
        );
        $cam = CamHygiene::create($inputArr);
        return  $cam ? true : false;

    }

    public static function updateHygieneData($attributes, $userId){
        $cam = CamHygiene::where('app_id', $attributes['app_id'])->where('biz_id','=',$attributes['biz_id'])->first();
        //update cam_hygiene table
        $updateCamData = $cam->update([
                        'remarks' => $attributes['remarks'], 
                        'rbi_willful_defaulters' => $attributes['rbi_willful_defaulters'], 
                        'watchoutinvestors' => $attributes['watchoutinvestors'], 
                        'cibil_check' => $attributes['cibil_check'], 
                        'politically_check' => $attributes['politically_check'], 
                        'politically_exposed_person_comments' => $attributes['politically_exposed_person_comments'], 
                        'CDR_check' => $attributes['CDR_check'], 
                        'CDR_comments' => $attributes['CDR_comments'], 
                        'UNSC_check' => $attributes['UNSC_check'],
                        'UNSC_comments' => $attributes['UNSC_comments'],
                        'npa_history_check' => $attributes['npa_history_check'], 
                        'npa_history_comments' => $attributes['npa_history_comments'], 
                        'cop_gov_check' => $attributes['cop_gov_check'], 
                        'cop_gov_issues_comments' => $attributes['cop_gov_issues_comments'], 
                        'change_auditor_check' => $attributes['change_auditor_check'], 
                        'change_in_auditor_comments' => $attributes['change_in_auditor_comments'], 
                        'change_auditor_qualification_check' => $attributes['change_auditor_qualification_check'], 
                        'change_auditor_qualification_comment' => $attributes['change_auditor_qualification_comment'], 
                        'audit_report_check' => $attributes['audit_report_check'], 
                        'audit_report_comment' => $attributes['audit_report_comment'], 
                        'adequate_insurance_check' => $attributes['adequate_insurance_check'], 
                        'adequate_insurance_comments' => $attributes['adequate_insurance_comments'], 
                        'negative_news_report_check' => $attributes['negative_news_report_check'], 
                        'negative_news_report_comments' => $attributes['negative_news_report_comments'],
                        'updated_by'=>$userId,
        ]);
        return $updateCamData ? true : false;
    }
}