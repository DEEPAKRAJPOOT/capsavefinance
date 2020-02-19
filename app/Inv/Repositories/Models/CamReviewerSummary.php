<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Factory\Models\BaseModel;

class CamReviewerSummary extends BaseModel
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cam_reviewer_summary';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'cam_reviewer_summary_id';

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
                        'product_id',
                        'cover_note', 
                        // 'cond_nach', 
                        // 'time_nach', 
                        // 'cond_insp_asset', 
                        // 'time_insp_asset', 
                        // 'cond_insu_pol_cfpl', 
                        // 'time_insu_pol_cfpl', 
                        // 'cond_personal_guarantee',   
                        // 'time_personal_guarantee',   
                        // 'cond_pbdit', 
                        // 'time_pbdit', 
                        // 'cond_dscr', 
                        // 'time_dscr', 
                        // 'cond_lender_cfpl', 
                        // 'time_lender_cfpl', 
                        // 'cond_ebidta',   
                        // 'time_ebidta',   
                        // 'cond_credit_rating', 
                        // 'time_credit_rating', 
                        'cond_pos_track_rec', 
                        'cmnt_pos_track_rec', 
                        'cond_pos_credit_rating', 
                        'cmnt_pos_credit_rating', 
                        'cond_pos_fin_matric',   
                        'cmnt_pos_fin_matric',   
                        'cond_pos_establish_client', 
                        'cmnt_pos_establish_client', 
                        'cond_neg_competition', 
                        'cmnt_neg_competition', 
                        'cond_neg_forex_risk', 
                        'cmnt_neg_forex_risk', 
                        'cond_neg_pbdit',   
                        'cmnt_neg_pbdit',   
                        'recommendation', 
                        'criteria_rv_position', 
                        'criteria_rv_position_remark', 
                        'criteria_asset_portfolio',   
                        'criteria_asset_portfolio_remark',   
                        'criteria_sing_borr_limit', 
                        'criteria_sing_borr_remark', 
                        'criteria_borr_grp_limit', 
                        'criteria_borr_grp_remark', 
                        'criteria_invest_grade', 
                        'criteria_invest_grade_remark', 
                        'criteria_particular_portfolio',   
                        'criteria_particular_portfolio_remark',               
                        'created_by',
                        'created_at',
                        'updated_at',
                        'updated_by'
    ];

    public static function createData($attributes, $userId){
        $inputArr= array(
            'biz_id' => $attributes['biz_id'],
            'app_id' => $attributes['app_id'],
            'product_id' => $attributes['product_id'],
            'cover_note' => $attributes['cover_note'],	
            // 'cond_nach' => $attributes['cond_nach'],
            // 'time_nach' => $attributes['time_nach'], 
            // 'cond_insp_asset' => $attributes['cond_insp_asset'],
            // 'time_insp_asset' => $attributes['time_insp_asset'],
            // 'cond_insu_pol_cfpl' => $attributes['cond_insu_pol_cfpl'], 
            // 'time_insu_pol_cfpl' => $attributes['time_insu_pol_cfpl'], 
            // 'cond_personal_guarantee' => $attributes['cond_personal_guarantee'],  
            // 'time_personal_guarantee' => $attributes['time_personal_guarantee'],  
            // 'cond_pbdit' => $attributes['cond_pbdit'], 
            // 'time_pbdit' => $attributes['time_pbdit'], 
            // 'cond_dscr' => $attributes['cond_dscr'], 
            // 'time_dscr' => $attributes['time_dscr'], 
            // 'cond_lender_cfpl' => $attributes['cond_lender_cfpl'], 
            // 'time_lender_cfpl' => $attributes['time_lender_cfpl'], 
            // 'cond_ebidta' => $attributes['cond_ebidta'],   
            // 'time_ebidta' => $attributes['time_ebidta'],   
            // 'cond_credit_rating' => $attributes['cond_credit_rating'],
            // 'time_credit_rating' => $attributes['time_credit_rating'], 
            'cond_pos_track_rec' => $attributes['cond_pos_track_rec'], 
            'cmnt_pos_track_rec' => $attributes['cmnt_pos_track_rec'], 
            'cond_pos_credit_rating' => $attributes['cond_pos_credit_rating'], 
            'cmnt_pos_credit_rating' => $attributes['cmnt_pos_credit_rating'], 
            'cond_pos_fin_matric' => $attributes['cond_pos_fin_matric'],  
            'cmnt_pos_fin_matric' => $attributes['cmnt_pos_fin_matric'],  
            'cond_pos_establish_client' => $attributes['cond_pos_establish_client'], 
            'cmnt_pos_establish_client' => $attributes['cmnt_pos_establish_client'],
            'cond_neg_competition' => $attributes['cond_neg_competition'],
            'cmnt_neg_competition' => $attributes['cmnt_neg_competition'], 
            'cond_neg_forex_risk' => $attributes['cond_neg_forex_risk'], 
            'cmnt_neg_forex_risk' => $attributes['cmnt_neg_forex_risk'],
            'cond_neg_pbdit' => $attributes['cond_neg_pbdit'],  
            'cmnt_neg_pbdit' => $attributes['cmnt_neg_pbdit'],  
            'recommendation' => $attributes['recommendation'],  
            'criteria_rv_position' => $attributes['criteria_rv_position'],  
            'criteria_rv_position_remark' => $attributes['criteria_rv_position_remark'], 
            'criteria_asset_portfolio' => $attributes['criteria_asset_portfolio'],  
            'criteria_asset_portfolio_remark' => $attributes['criteria_asset_portfolio_remark'], 
            'criteria_sing_borr_limit' => $attributes['criteria_sing_borr_limit'],  
            'criteria_sing_borr_remark' => $attributes['criteria_sing_borr_remark'], 
            'criteria_borr_grp_limit' => $attributes['criteria_borr_grp_limit'],  
            'criteria_borr_grp_remark' => $attributes['criteria_borr_grp_remark'], 
            'criteria_invest_grade' => $attributes['criteria_invest_grade'],  
            'criteria_invest_grade_remark' => $attributes['criteria_invest_grade_remark'], 
            'criteria_particular_portfolio' => $attributes['criteria_particular_portfolio'],  
            'criteria_particular_portfolio_remark' => $attributes['criteria_particular_portfolio_remark'],                        
            'created_by'=> $userId
        );
        $qryOutput = self::create($inputArr);
        return  $qryOutput ?? false;
    }

    public static function updateData($attributes, $userId){
        $cam = self::where('app_id', $attributes['app_id'])->where('biz_id',$attributes['biz_id'])->first();
        $qryOutput = $cam->update([
            'cover_note' => $attributes['cover_note'],	
            // 'cond_nach' => $attributes['cond_nach'],
            // 'time_nach' => $attributes['time_nach'], 
            // 'cond_insp_asset' => $attributes['cond_insp_asset'],
            // 'time_insp_asset' => $attributes['time_insp_asset'],
            // 'cond_insu_pol_cfpl' => $attributes['cond_insu_pol_cfpl'], 
            // 'time_insu_pol_cfpl' => $attributes['time_insu_pol_cfpl'], 
            // 'cond_personal_guarantee' => $attributes['cond_personal_guarantee'],  
            // 'time_personal_guarantee' => $attributes['time_personal_guarantee'],  
            // 'cond_pbdit' => $attributes['cond_pbdit'], 
            // 'time_pbdit' => $attributes['time_pbdit'], 
            // 'cond_dscr' => $attributes['cond_dscr'], 
            // 'time_dscr' => $attributes['time_dscr'], 
            // 'cond_lender_cfpl' => $attributes['cond_lender_cfpl'], 
            // 'time_lender_cfpl' => $attributes['time_lender_cfpl'], 
            // 'cond_ebidta' => $attributes['cond_ebidta'],   
            // 'time_ebidta' => $attributes['time_ebidta'],   
            // 'cond_credit_rating' => $attributes['cond_credit_rating'],
            // 'time_credit_rating' => $attributes['time_credit_rating'], 
            'cond_pos_track_rec' => $attributes['cond_pos_track_rec'], 
            'cmnt_pos_track_rec' => $attributes['cmnt_pos_track_rec'], 
            'cond_pos_credit_rating' => $attributes['cond_pos_credit_rating'], 
            'cmnt_pos_credit_rating' => $attributes['cmnt_pos_credit_rating'], 
            'cond_pos_fin_matric' => $attributes['cond_pos_fin_matric'],  
            'cmnt_pos_fin_matric' => $attributes['cmnt_pos_fin_matric'],  
            'cond_pos_establish_client' => $attributes['cond_pos_establish_client'], 
            'cmnt_pos_establish_client' => $attributes['cmnt_pos_establish_client'],
            'cond_neg_competition' => $attributes['cond_neg_competition'],
            'cmnt_neg_competition' => $attributes['cmnt_neg_competition'], 
            'cond_neg_forex_risk' => $attributes['cond_neg_forex_risk'], 
            'cmnt_neg_forex_risk' => $attributes['cmnt_neg_forex_risk'],
            'cond_neg_pbdit' => $attributes['cond_neg_pbdit'],  
            'cmnt_neg_pbdit' => $attributes['cmnt_neg_pbdit'],  
            'recommendation' => $attributes['recommendation'],  
            'criteria_rv_position' => $attributes['criteria_rv_position'],  
            'criteria_rv_position_remark' => $attributes['criteria_rv_position_remark'], 
            'criteria_asset_portfolio' => $attributes['criteria_asset_portfolio'],  
            'criteria_asset_portfolio_remark' => $attributes['criteria_asset_portfolio_remark'], 
            'criteria_sing_borr_limit' => $attributes['criteria_sing_borr_limit'],  
            'criteria_sing_borr_remark' => $attributes['criteria_sing_borr_remark'], 
            'criteria_borr_grp_limit' => $attributes['criteria_borr_grp_limit'],  
            'criteria_borr_grp_remark' => $attributes['criteria_borr_grp_remark'], 
            'criteria_invest_grade' => $attributes['criteria_invest_grade'],  
            'criteria_invest_grade_remark' => $attributes['criteria_invest_grade_remark'], 
            'criteria_particular_portfolio' => $attributes['criteria_particular_portfolio'],  
            'criteria_particular_portfolio_remark' => $attributes['criteria_particular_portfolio_remark'],   
            'updated_by'=>$userId,
        ]);
        return $qryOutput ? true : false;
    }
}