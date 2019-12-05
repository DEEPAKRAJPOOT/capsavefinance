<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use DB;

class FinanceModel extends Model
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'financials';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'sno';

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
             'average_collection_period',
             'finance_id','period_ended',
             'projection_aval_amount',
             'audit',
             'prfomnc_net_sales',
             'prfomnc_othr_non_income',
             'prfomnc_pbdit',
             'prfomnc_depreciation',
             'prfomnc_avrg_fixed_assets_prcnt',
             'prfomnc_intrst',
             'prfomnc_intrst_prcnt',
             'prfomnc_intrst_ratio',
             'prfomnc_net_profit',
             'prfomnc_cash_profit',
             'prfomnc_dscr',
             'prfomnc_raw_material_prcnt',
             'prfomnc_labour_prcnt',
             'prfomnc_mnufctr_expns_prcnt',
             'profit_pbdit_prcnt',
             'profit_pbit_prcnt',
             'profit_pbt_prcnt',
             'profit_net_prcnt',
             'profit_cash_prcnt',
             'profit_retained_prcnt',
             'profit_return_net_prcnt',
             'profit_return_assets_prcnt',
             'profit_return_cptl_prcnt',
             'growth_net_sales_prcnt',
             'growth_net_profit_prcnt',
             'growth_tangible_prcnt',
             'fncl_total_assets',
             'fncl_curr_assets',
             'fncl_non_curr_assets',
             'fncl_tol','fncl_tnw',
             'fncl_investment',
             'fncl_quasi_equity',
             'fncl_atnw',
             'levrge_tnw',
             'levrge_atnw',
             'levrge_long_tnw',
             'levrge_long_atnw',
             'levrge_cash_profit',
             'levrge_total_debt',
             'levrge_pbdit',
             'liqdty_net_capital',
             'liqdty_curr_ratio',
             'liqdty_quick_ratio',
             'activity_domestic_trnvr',
             'activity_export_trnvr',
             'activity_total_trnvr',
             'activity_inventory_trnvr',
             'activity_creditors_trnvr',
             'activity_fixed_trnvr',
             'funds_long_source',
             'funds_long_uses',
             'funds_net_capital',
             'cash_net',
             'cash_before_funding',
             'cash_investment',
             'cash_negative_capital',
             'cash_negative_debts',
             'cash_negative_equity',
             'sales_and_profit',
             'gearing',
             'liquidity_ratio',
             'capital_cycle',
             'debtors',
             'financial_risk_comments',
             'inventory_payable_days',
             'inventory_projections'
    ];



    /**
     * Get Bank Statements
     * 
     * @param integer $app_id
     * @return mixed
     */
    public function getBankStatements($app_id) {        
        $result = self::select('app_doc_file.doc_name','app_doc_file.app_id','app_doc_file.file_id','file.file_type','file.file_name','file.file_size','file.file_path')
              ->from('app_doc_file')
              ->join('file', 'app_doc_file.file_id', '=', 'file.file_id')
              ->where('app_doc_file.app_id', '=', $app_id)
              ->where('app_doc_file.doc_id', '=', '4')
              ->where('app_doc_file.is_active', '=', '1')
              ->get();
        return ($result ?? null);        
    }

     public function getFinanceStatements($app_id) {        
        $result = self::select('app_doc_file.doc_name','app_doc_file.app_id','app_doc_file.finc_year','app_doc_file.file_id','file.file_type','file.file_name','file.file_size','file.file_path')
              ->from('app_doc_file')
              ->join('file', 'app_doc_file.file_id', '=', 'file.file_id')
              ->where('app_doc_file.app_id', '=', $app_id)
              ->where('app_doc_file.doc_id', '=', '5')
              ->where('app_doc_file.is_active', '=', '1')
              ->get();
        return ($result ?? null);        
    }

     public function getGSTStatements($app_id) {        
        $result = self::select('app_doc_file.gst_month','app_doc_file.gst_year','app_doc_file.app_id','app_doc_file.file_id','file.file_type','file.file_name','file.file_size','file.file_path')
              ->from('app_doc_file')
              ->join('file', 'app_doc_file.file_id', '=', 'file.file_id')
              ->where('app_doc_file.app_id', '=', $app_id)
              ->where('app_doc_file.doc_id', '=', '6')
              ->where('app_doc_file.is_active', '=', '1')
              ->get();
        return ($result ?? null);        
    }

    public static function getGstbyUser($user_id){
        $result = self::select('*')
                ->from('biz_pan_gst')
                ->where('user_id', $user_id)
                ->where('parent_pan_gst_id', '0')
                ->where('type', '2')
                ->first();
        return ($result ?? null);
    }


    public static function getUserByAPP($app_id){
        $result = self::select('*')
                ->from('app')
                ->where('app_id', $app_id)
                ->first();
        return ($result ?? null);
    }

    public static function getBankData(){
        $result = self::select('*')
                ->from('mst_bank')
                ->where('is_active', '1')
                ->get();
        return ($result ?? null);
    }

    public static function insertPerfios($data, $table = 'biz_perfios'){
      $inserted_id = DB::table($table)->insertGetId($data);
      return $inserted_id;
    }

    public static function updatePerfios($data, $table = 'biz_perfios_log', $value = '1', $column = 'id'){
      $inserted_id = DB::table($table)->where($column, $value)->update($data);
      return $inserted_id;
    }

    public static function getPendingFinanceStatement($app_id){
       $result = self::select('*')
                ->from('biz_perfios')
                ->where('app_id', $app_id)
                ->where('api_name', '1005')
                ->first();
        return ($result ?? null);
    }

    public static function getPendingBankStatement($app_id){
       $result = self::select('*')
                ->from('biz_perfios')
                ->where('app_id', $app_id)
                ->where('api_name', '1007')
                ->first();
        return ($result ?? null);
    }

    public static function getPerfiosData($biz_perfios_id){
       $result = self::select('*')
                ->from('biz_perfios')
                ->where('biz_perfios_id', $biz_perfios_id)
                ->first();
        return ($result ?? null);
    }

   
}
