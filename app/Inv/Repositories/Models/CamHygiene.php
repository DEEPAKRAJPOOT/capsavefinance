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
                        'watchout_investors', 
                        'cibil_check', 
                        'politically_check', 
                        'pol_exp_per_cmnt', 
                        'cdr_check', 
                        'cdr_cmnt', 
                        'unsc_check',
                        'comment', 
                        'unsc_cmnt',
                        'npa_history_check', 
                        'npa_history_cmnt', 
                        'cop_gov_check', 
                        'cop_gov_issues_cmnt', 
                        'change_auditor_check', 
                        'change_in_audit_cmnt', 
                        'change_audit_qual_check', 
                        'change_audit_qual_cmnt', 
                        'audit_report_check', 
                        'audit_report_cmnt', 
                        'adeq_ins_check', 
                        'adeq_ins_cmnt', 
                        'neg_news_report_check', 
                        'neg_news_report_cmnt', 
                        'nach_mandate_check', 
                        'nach_mandate_cmnt', 
                        'asset_insp_check',
                        'asset_insp_cmnt',
                        'asset_insu_policy_check',
                        'asset_insu_policy_cmnt',
                        'cfpl_default_check',
                        'cfpl_default_cmnt',
                        'contact_point_check',
                        'contact_point_cmnt',
                        'bank_ref_check',
                        'bank_ref_cmnt',
                        'trade_ref_check',
                        'trade_ref_cmnt',
                        'neg_industry_check',
                        'neg_industry_cmnt',
                        'senstive_sector_check',
                        'senstive_sector_cmnt',
                        'senstive_region_check',
                        'senstive_region_cmnt',
                        'kyc_risk_check',
                        'kyc_risk_cmnt',
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
                        'comment' => $attributes['comment'], 
                        'rbi_willful_defaulters' => $attributes['rbi_willful_defaulters'], 
                        'watchout_investors' => $attributes['watchout_investors'], 
                        'cibil_check' => $attributes['cibil_check'], 
                        'politically_check' => $attributes['politically_check'], 
                        'pol_exp_per_cmnt' => $attributes['pol_exp_per_cmnt'], 
                        'cdr_check' => $attributes['cdr_check'], 
                        'cdr_cmnt' => $attributes['cdr_cmnt'], 
                        'unsc_check' => $attributes['unsc_check'],
                        'unsc_cmnt' => $attributes['unsc_cmnt'],
                        'npa_history_check' => $attributes['npa_history_check'], 
                        'npa_history_cmnt' => $attributes['npa_history_cmnt'], 
                        'cop_gov_check' => $attributes['cop_gov_check'], 
                        'cop_gov_issues_cmnt' => $attributes['cop_gov_issues_cmnt'], 
                        'change_auditor_check' => $attributes['change_auditor_check'], 
                        'change_in_audit_cmnt' => $attributes['change_in_audit_cmnt'], 
                        'change_audit_qual_check' => $attributes['change_audit_qual_check'], 
                        'change_audit_qual_cmnt' => $attributes['change_audit_qual_cmnt'], 
                        'audit_report_check' => $attributes['audit_report_check'], 
                        'audit_report_cmnt' => $attributes['audit_report_cmnt'], 
                        'adeq_ins_check' => $attributes['adeq_ins_check'], 
                        'adeq_ins_cmnt' => $attributes['adeq_ins_cmnt'], 
                        'neg_news_report_check' => $attributes['neg_news_report_check'], 
                        'neg_news_report_cmnt' => $attributes['neg_news_report_cmnt'], 
                        'nach_mandate_check' => $attributes['nach_mandate_check'], 
                        'nach_mandate_cmnt' => $attributes['nach_mandate_cmnt'],
                        'asset_insp_check' => $attributes['asset_insp_check'], 
                        'asset_insp_cmnt' => $attributes['asset_insp_cmnt'],
                        'asset_insu_policy_check' => $attributes['asset_insu_policy_check'], 
                        'asset_insu_policy_cmnt' => $attributes['asset_insu_policy_cmnt'],
                        'cfpl_default_check' => $attributes['cfpl_default_check'], 
                        'cfpl_default_cmnt' => $attributes['cfpl_default_cmnt'],
                        'contact_point_check' => $attributes['contact_point_check'], 
                        'contact_point_cmnt' => $attributes['contact_point_cmnt'],
                        'bank_ref_check' => $attributes['bank_ref_check'], 
                        'bank_ref_cmnt' => $attributes['bank_ref_cmnt'],
                        'trade_ref_check' => $attributes['trade_ref_check'], 
                        'trade_ref_cmnt' => $attributes['trade_ref_cmnt'],
                        'neg_industry_check' => $attributes['neg_industry_check'], 
                        'neg_industry_cmnt' => $attributes['neg_industry_cmnt'],
                        'senstive_sector_check' => $attributes['senstive_sector_check'], 
                        'senstive_sector_cmnt' => $attributes['senstive_sector_cmnt'],
                        'senstive_region_check' => $attributes['senstive_region_check'], 
                        'senstive_region_cmnt' => $attributes['senstive_region_cmnt'],
                        'kyc_risk_check' => $attributes['kyc_risk_check'], 
                        'kyc_risk_cmnt' => $attributes['kyc_risk_cmnt'],
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
                        'comment' => $attributes['comment'], 
                        'rbi_willful_defaulters' => $attributes['rbi_willful_defaulters'], 
                        'watchout_investors' => $attributes['watchout_investors'], 
                        'cibil_check' => $attributes['cibil_check'], 
                        'politically_check' => $attributes['politically_check'], 
                        'pol_exp_per_cmnt' => $attributes['pol_exp_per_cmnt'], 
                        'cdr_check' => $attributes['cdr_check'], 
                        'cdr_cmnt' => $attributes['cdr_cmnt'], 
                        'unsc_check' => $attributes['unsc_check'],
                        'unsc_cmnt' => $attributes['unsc_cmnt'],
                        'npa_history_check' => $attributes['npa_history_check'], 
                        'npa_history_cmnt' => $attributes['npa_history_cmnt'], 
                        'cop_gov_check' => $attributes['cop_gov_check'], 
                        'cop_gov_issues_cmnt' => $attributes['cop_gov_issues_cmnt'], 
                        'change_auditor_check' => $attributes['change_auditor_check'], 
                        'change_in_audit_cmnt' => $attributes['change_in_audit_cmnt'], 
                        'change_audit_qual_check' => $attributes['change_audit_qual_check'], 
                        'change_audit_qual_cmnt' => $attributes['change_audit_qual_cmnt'], 
                        'audit_report_check' => $attributes['audit_report_check'], 
                        'audit_report_cmnt' => $attributes['audit_report_cmnt'], 
                        'adeq_ins_check' => $attributes['adeq_ins_check'], 
                        'adeq_ins_cmnt' => $attributes['adeq_ins_cmnt'], 
                        'neg_news_report_check' => $attributes['neg_news_report_check'], 
                        'neg_news_report_cmnt' => $attributes['neg_news_report_cmnt'],
                        'nach_mandate_check' => $attributes['nach_mandate_check'], 
                        'nach_mandate_cmnt' => $attributes['nach_mandate_cmnt'],
                        'asset_insp_check' => $attributes['asset_insp_check'], 
                        'asset_insp_cmnt' => $attributes['asset_insp_cmnt'],
                        'asset_insu_policy_check' => $attributes['asset_insu_policy_check'], 
                        'asset_insu_policy_cmnt' => $attributes['asset_insu_policy_cmnt'],
                        'cfpl_default_check' => $attributes['cfpl_default_check'], 
                        'cfpl_default_cmnt' => $attributes['cfpl_default_cmnt'],
                        'contact_point_check' => $attributes['contact_point_check'], 
                        'contact_point_cmnt' => $attributes['contact_point_cmnt'],
                        'bank_ref_check' => $attributes['bank_ref_check'], 
                        'bank_ref_cmnt' => $attributes['bank_ref_cmnt'],
                        'trade_ref_check' => $attributes['trade_ref_check'], 
                        'trade_ref_cmnt' => $attributes['trade_ref_cmnt'],
                        'neg_industry_check' => $attributes['neg_industry_check'], 
                        'neg_industry_cmnt' => $attributes['neg_industry_cmnt'],
                        'senstive_sector_check' => $attributes['senstive_sector_check'], 
                        'senstive_sector_cmnt' => $attributes['senstive_sector_cmnt'],
                        'senstive_region_check' => $attributes['senstive_region_check'], 
                        'senstive_region_cmnt' => $attributes['senstive_region_cmnt'],
                        'kyc_risk_check' => $attributes['kyc_risk_check'], 
                        'kyc_risk_cmnt' => $attributes['kyc_risk_cmnt'],
                        'updated_by'=>$userId,
        ]);
        return $updateCamData ? true : false;
    }
}