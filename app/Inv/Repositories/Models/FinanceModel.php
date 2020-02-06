<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use DB;
use Storage;
use App\Inv\Repositories\Factory\Models\BaseModel;

class FinanceModel extends BaseModel
{    
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
        $result = self::select('app_doc_file.app_doc_file_id','app_doc_file.doc_id','app_doc_file.file_bank_id','app_doc_file.doc_name','app_doc_file.facility','app_doc_file.sanctionlimitfixed','app_doc_file.drawingpowervariableamount','app_doc_file.sanctionlimitvariableamount','app_doc_file.app_id','app_doc_file.file_id','app_doc_file.gst_month','app_doc_file.gst_year','file.file_type','file.file_name','file.file_size','file.file_path')
              ->from('app_doc_file')
              ->join('file', 'app_doc_file.file_id', '=', 'file.file_id')
              ->where('app_doc_file.app_id', '=', $app_id)
              ->where('app_doc_file.doc_id', '=', '4')
              ->where('app_doc_file.is_active', '=', '1')
              ->get();
        return ($result ?? null);        
    }

    public function getSingleBankStatement($app_id, $app_doc_file_id) {        
        $result = self::select('app_doc_file.app_doc_file_id','app_doc_file.doc_id','app_doc_file.file_bank_id','app_doc_file.doc_name','app_doc_file.facility','app_doc_file.sanctionlimitfixed','app_doc_file.drawingpowervariableamount','app_doc_file.sanctionlimitvariableamount','app_doc_file.app_id','app_doc_file.file_id','app_doc_file.gst_month','app_doc_file.gst_year','file.file_type','file.file_name','file.file_size','file.file_path')
              ->from('app_doc_file')
              ->join('file', 'app_doc_file.file_id', '=', 'file.file_id')
              ->where('app_doc_file.app_id', '=', $app_id)
              ->where('app_doc_file.app_doc_file_id', '=', $app_doc_file_id)
              ->where('app_doc_file.doc_id', '=', '4')
              ->where('app_doc_file.is_active', '=', '1')
              ->first();
        return ($result ?? null);        
    }

     public function getFinanceStatements($app_id) {        
        $result = self::select('app_doc_file.app_doc_file_id','app_doc_file.doc_id','app_doc_file.doc_name','app_doc_file.app_id','app_doc_file.gst_month','app_doc_file.gst_year','app_doc_file.finc_year','app_doc_file.file_id','file.file_type','file.file_name','file.file_size','file.file_path')
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

    public static function getSelectedGstForApp($biz_id){
        $result = self::select('*')
                ->from('biz_pan_gst')
                ->where('biz_id', $biz_id)
                ->where('parent_pan_gst_id', '0')
                ->where('type', '2')
                ->first();
        return ($result ?? null);
    }

     public static function getAllGstForApp($biz_id){
        $data = self::select('*')
                ->from('biz_pan_gst')
                ->where('biz_id', $biz_id)
                ->where('type', '2')
                ->where('parent_pan_gst_id', '!=','0')
                ->get();
        return ($data ? $data : false);
    }

    public static function getUserByAPP($app_id){
        $result = self::select('*')
                ->from('app')
                ->where('app_id', $app_id)
                ->first();
        return ($result ?? null);
    }

    public static function getLoanByAPP($app_id){
        $result = self::select('*')
                ->from('app_product')
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

    public static function getBankDetail($id){
        $result = self::select('*')
                ->from('mst_bank')
                ->where('is_active', '1')
                ->where('id', $id)
                ->first();
        return ($result ?? null);
    }

    public static function getDebtPosition($appId){
         $result = self::select(DB::raw("DATE_FORMAT(debt_on, '%d/%m/%Y') as debt_on"),'debt_position_comments','bank_detail_id')
                ->from('app_biz_bank_detail')
                ->where('app_id', $appId)
                ->orderBy('bank_detail_id','desc')->first();
        return ($result ?? null);
    }

    public static function insertPerfios($data, $table = 'biz_perfios'){
      $inserted_id = DB::table($table)->insertGetId($data);
      return $inserted_id;
    }


     /**
     * Email Logger
     * 
     * @param Array/Mixed $attributes
     */
    public static function logEmail($emailData) {
     $loggerData = [
            'mail_from' => $emailData['email_from'],
            'mail_type' => $emailData['email_type'] ?? __FUNCTION__,
            'subject' => $emailData['subject'],
            'body' => base64_encode($emailData['body']),
            'name' => $emailData['name'] ?? NULL,
            'fileid' => $emailData['fileid'] ?? NULL,
            'sent_by' => \Auth::user()->user_id,
        ];

        if (!empty($emailData['email_cc'])) {
            $emailData['email_cc'] = is_string($emailData['email_cc']) ? explode(',', $emailData['email_cc']) : $emailData['email_cc'];
            $loggerData['mail_cc'] = implode('|', $emailData['email_cc']);
        }
        if (!empty($emailData['email_bcc'])) {
            $emailData['email_bcc'] = is_string($emailData['email_bcc']) ? explode(',', $emailData['email_bcc']) : $emailData['email_bcc'];
            $loggerData['mail_bcc'] = implode('|', $emailData['email_bcc']);
        }
        if (!empty($emailData['email_to'])) {
            $emailData['email_to'] = is_string($emailData['email_to']) ? explode(',', $emailData['email_to']) : $emailData['email_to'];
            $loggerData['mail_to'] = implode('|', $emailData['email_to']);
        }
        if (!empty($emailData['attachment'])) {
          $attachment = $emailData['attachment'];
          $filename = $emailData['att_name'] ?? 'attachment.pdf';
          $fileparts = pathinfo($filename);
          $filename = $fileparts['filename'];
          $ext = $fileparts['extension'];
          if(!Storage::exists('public/user/docs/attachments')) {
                Storage::makeDirectory('public/user/docs/attachments', 0777, true);
          }
          $saveFileName = _uuid_rand(40) . ".$ext";
          $myfile = fopen(storage_path('app/public/user/docs/attachments/'.$saveFileName), "w");
          \File::put(storage_path('app/public/user/docs/attachments/'.$saveFileName), $attachment);
          $loggerData['file_path'] = 'user/docs/attachments/'.$saveFileName;
        }
        return SELF::dataLogger($loggerData, 'email_logger');
    }


    public static function dataLogger($data, $table = 'email_logger'){
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
                ->latest()->first();
        return ($result ?? null);
    }

    public static function getPendingBankStatement($app_id){
       $result = self::select('*')
                ->from('biz_perfios')
                ->where('app_id', $app_id)
                ->where('api_name', '1007')
                ->latest()->first();
        return ($result ?? null);
    }

    public static function getPerfiosData($biz_perfios_id){
       $result = self::select('*')
                ->from('biz_perfios')
                ->where('biz_perfios_id', $biz_perfios_id)
                ->orWhere('perfios_log_id', $biz_perfios_id)
                ->first();
        return ($result ?? null);
    }


    public static function getGstData($request_id){
       $result = self::select('*')
                ->from('biz_gst_log')
                ->where('request_id', $request_id)
                ->first();
       return ($result ?? null);
    }
   
}
