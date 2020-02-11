<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Factory\Models\BaseModel;

class AppBizFinDetail extends BaseModel
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_biz_fin_detail';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'fin_detail_id';

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
                        'adj_net_worth_check', 
                        'adj_net_worth_cmnt', 
                        'cash_profit_check', 
                        'cash_profit_cmnt', 
                        'dscr_check', 
                        'dscr_cmnt', 
                        'debt_check', 
                        'debt_cmnt',
                        'financial_risk_comments',                         
                        'created_by',
                        'created_at',
                        'updated_at',
                        'updated_by'
    ];


    public static function creates($attributes, $userId){
        $inputArr= array(
                        'biz_id' => $attributes['biz_id'],
                        'app_id' => $attributes['app_id'],
                        'adj_net_worth_check' => $attributes['adj_net_worth_check'], 
                        'adj_net_worth_cmnt' => $attributes['adj_net_worth_cmnt'], 
                        'cash_profit_check' => $attributes['cash_profit_check'], 
                        'cash_profit_cmnt' => $attributes['cash_profit_cmnt'], 
                        'dscr_check' => $attributes['dscr_check'], 
                        'dscr_cmnt' => $attributes['dscr_cmnt'], 
                        'debt_check' => $attributes['debt_check'], 
                        'debt_cmnt' => $attributes['debt_cmnt'],                         
                        'financial_risk_comments' => $attributes['financial_risk_comments'],                         
                        'created_by'=>$userId
        );
        $finDetail = self::create($inputArr);
        return  $finDetail ? true : false;

    }

    public static function updateHygieneData($attributes, $userId){
        $cam = self::where('app_id', $attributes['app_id'])->where('biz_id',$attributes['biz_id'])->first();
        $finDetail = $cam->update([
            'adj_net_worth_check' => $attributes['adj_net_worth_check'], 
            'adj_net_worth_cmnt' => $attributes['adj_net_worth_cmnt'], 
            'cash_profit_check' => $attributes['cash_profit_check'], 
            'cash_profit_cmnt' => $attributes['cash_profit_cmnt'], 
            'dscr_check' => $attributes['dscr_check'], 
            'dscr_cmnt' => $attributes['dscr_cmnt'], 
            'debt_check' => $attributes['debt_check'], 
            'debt_cmnt' => $attributes['debt_cmnt'], 
            'financial_risk_comments' => $attributes['financial_risk_comments'], 
            'updated_by'=>$userId,
        ]);
        return $finDetail ? true : false;
    }


    public static function saveBankDetail($attributes, $userId){
        $cam = DB::insert('insert into rta_app_biz_bank_detail (app_id, debt_on, debt_position_comments , created_by) values ($attributes["appId"], $attributes["debt_on"], $attributes["debt_position_comments"], $userId )');
        return $cam ? true : false;
    }
}