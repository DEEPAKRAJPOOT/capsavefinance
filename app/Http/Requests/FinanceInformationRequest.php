<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinanceInformationRequest extends FormRequest
{

	 /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          /*
           'audit' => 'required|array|min:3|max:3',
           'audit.*' => "required|string",

           'prfomnc_net_sales' => 'required|array|min:3|max:3',
           'prfomnc_net_sales.*' => "required|numeric",
           'prfomnc_othr_non_income' => 'required|array|min:3|max:3',
           'prfomnc_othr_non_income.*' => "required|numeric",
           'prfomnc_pbdit' => 'required|array|min:3|max:3',
           'prfomnc_pbdit.*' => "required|numeric",
           'prfomnc_depreciation' => 'required|array|min:3|max:3',
           'prfomnc_depreciation.*' => "required|numeric",
           'prfomnc_avrg_fixed_assets_prcnt' => 'required|array|min:3|max:3',
           'prfomnc_avrg_fixed_assets_prcnt.*' => "required|numeric",
           'prfomnc_intrst' => 'required|array|min:3|max:3',
           'prfomnc_intrst.*' => "required|numeric",
           'prfomnc_intrst_prcnt' => 'required|array|min:3|max:3',
           'prfomnc_intrst_prcnt.*' => "required|numeric",
           'prfomnc_intrst_ratio' => 'required|array|min:3|max:3',
           'prfomnc_intrst_ratio.*' => "required|numeric",
           'prfomnc_net_profit' => 'required|array|min:3|max:3',
           'prfomnc_net_profit.*' => "required|numeric",
           'prfomnc_cash_profit' => 'required|array|min:3|max:3',
           'prfomnc_cash_profit.*' => "required|numeric",
           'prfomnc_dscr' => 'required|array|min:3|max:3',
           'prfomnc_dscr.*' => "required|numeric",
           'prfomnc_raw_material_prcnt' => 'required|array|min:3|max:3',
           'prfomnc_raw_material_prcnt.*' => "required|numeric",
           'prfomnc_labour_prcnt' => 'required|array|min:3|max:3',
           'prfomnc_labour_prcnt.*' => "required|numeric",
           'prfomnc_mnufctr_expns_prcnt' => 'required|array|min:3|max:3',
           'prfomnc_mnufctr_expns_prcnt.*' => "required|numeric",

           'profit_pbdit_prcnt' => 'required|array|min:3|max:3',
           'profit_pbdit_prcnt.*' => "required|numeric",
           'profit_pbit_prcnt' => 'required|array|min:3|max:3',
           'profit_pbit_prcnt.*' => "required|numeric",
           'profit_pbt_prcnt' => 'required|array|min:3|max:3',
           'profit_pbt_prcnt.*' => "required|numeric",
           'profit_net_prcnt' => 'required|array|min:3|max:3',
           'profit_net_prcnt.*' => "required|numeric",
           'profit_cash_prcnt' => 'required|array|min:3|max:3',
           'profit_cash_prcnt.*' => "required|numeric",
           'profit_retained_prcnt' => 'required|array|min:3|max:3',
           'profit_retained_prcnt.*' => "required|numeric",
           'profit_return_net_prcnt' => 'required|array|min:3|max:3',
           'profit_return_net_prcnt.*' => "required|numeric",
           'profit_return_assets_prcnt' => 'required|array|min:3|max:3',
           'profit_return_assets_prcnt.*' => "required|numeric",
           'profit_return_cptl_prcnt' => 'required|array|min:3|max:3',
           'profit_return_cptl_prcnt.*' => "required|numeric",

           'growth_net_sales_prcnt' => 'required|array|min:3|max:3',
           'growth_net_sales_prcnt.*' => "required|numeric",
           'growth_net_profit_prcnt' => 'required|array|min:3|max:3',
           'growth_net_profit_prcnt.*' => "required|numeric",
           'growth_tangible_prcnt' => 'required|array|min:3|max:3',
           'growth_tangible_prcnt.*' => "required|numeric",


           'fncl_total_assets' => 'required|array|min:3|max:3',
           'fncl_total_assets.*' => "required|numeric",
           'fncl_curr_assets' => 'required|array|min:3|max:3',
           'fncl_curr_assets.*' => "required|numeric",
           'fncl_non_curr_assets' => 'required|array|min:3|max:3',
           'fncl_non_curr_assets.*' => "required|numeric",
           'fncl_tol' => 'required|array|min:3|max:3',
           'fncl_tol.*' => "required|numeric",
           'fncl_tnw' => 'required|array|min:3|max:3',
           'fncl_tnw.*' => "required|numeric",
           'fncl_investment' => 'required|array|min:3|max:3',
           'fncl_investment.*' => "required|numeric",
           'fncl_quasi_equity' => 'required|array|min:3|max:3',
           'fncl_quasi_equity.*' => "required|numeric",
           'fncl_atnw' => 'required|array|min:3|max:3',
           'fncl_atnw.*' => "required|numeric",


           'levrge_tnw' => 'required|array|min:3|max:3',
           'levrge_tnw.*' => "required|numeric",
           'levrge_atnw' => 'required|array|min:3|max:3',
           'levrge_atnw.*' => "required|numeric",
           'levrge_long_tnw' => 'required|array|min:3|max:3',
           'levrge_long_tnw.*' => "required|numeric",
           'levrge_long_atnw' => 'required|array|min:3|max:3',
           'levrge_long_atnw.*' => "required|numeric",
           'levrge_cash_profit' => 'required|array|min:3|max:3',
           'levrge_cash_profit.*' => "required|numeric",
           'levrge_total_debt' => 'required|array|min:3|max:3',
           'levrge_total_debt.*' => "required|numeric",
           'levrge_pbdit' => 'required|array|min:3|max:3',
           'levrge_pbdit.*' => "required|numeric",

           'liqdty_net_capital' => 'required|array|min:3|max:3',
           'liqdty_net_capital.*' => "required|numeric",
           'liqdty_curr_ratio' => 'required|array|min:3|max:3',
           'liqdty_curr_ratio.*' => "required|numeric",
           'liqdty_quick_ratio' => 'required|array|min:3|max:3',
           'liqdty_quick_ratio.*' => "required|numeric",


           'activity_domestic_trnvr' => 'required|array|min:3|max:3',
           'activity_domestic_trnvr.*' => "required|numeric",
           'activity_export_trnvr' => 'required|array|min:3|max:3',
           'activity_export_trnvr.*' => "required|numeric",
           'activity_total_trnvr' => 'required|array|min:3|max:3',
           'activity_total_trnvr.*' => "required|numeric",
           'activity_inventory_trnvr' => 'required|array|min:3|max:3',
           'activity_inventory_trnvr.*' => "required|numeric",
           'activity_creditors_trnvr' => 'required|array|min:3|max:3',
           'activity_creditors_trnvr.*' => "required|numeric",
           'activity_fixed_trnvr' => 'required|array|min:3|max:3',
           'activity_fixed_trnvr.*' => "required|numeric",

           'funds_long_source' => 'required|array|min:3|max:3',
           'funds_long_source.*' => "required|numeric",
           'funds_long_uses' => 'required|array|min:3|max:3',
           'funds_long_uses.*' => "required|numeric",
           'funds_net_capital' => 'required|array|min:3|max:3',
           'funds_net_capital.*' => "required|numeric",

           'cash_net' => 'required|array|min:3|max:3',
           'cash_net.*' => "required|numeric",
           'cash_before_funding' => 'required|array|min:3|max:3',
           'cash_before_funding.*' => "required|numeric",
           'cash_investment' => 'required|array|min:3|max:3',
           'cash_investment.*' => "required|numeric",

           'cash_negative_capital' => 'required|array|min:3|max:3',
           'cash_negative_capital.*' => "required|numeric",
           'cash_negative_debts' => 'required|array|min:3|max:3',
           'cash_negative_debts.*' => "required|numeric",
           'cash_negative_equity' => 'required|array|min:3|max:3',
           'cash_negative_equity.*' => "required|numeric",

           'sales_and_profit' => "required|string|min:20|max:200",
           'gearing' => "required|string|min:20|max:200",
           'liquidity_ratio' => "required|string|min:20|max:200",
           'capital_cycle' => "required|string|min:20|max:200",
           'average_collection_period' => "required|string|min:20|max:200",

           'debtors' => "required|numeric",
           'financial_risk_comments' => "required|string|min:20|max:200",
           'inventory_payable_days' => "required|numeric",
           'inventory_projections' => "required|numeric",*/
        ];
    }

    public function messages()
    {
    	$messages = [];

    	for ($i=0; $i < 3; $i++) { 
    			$messages['audit.'.$i.'.required'] = 'Audit field is required';
        		$messages['audit.'.$i.'.string']  = 'Audit field must be string';

        		$messages['prfomnc_net_sales.'.$i.'.required'] = 'Net sales is required';
        		$messages['prfomnc_net_sales.'.$i.'.numeric']  = 'Net sales must be number';
        		$messages['prfomnc_othr_non_income.'.$i.'.required'] = 'Non operating income is required';
        		$messages['prfomnc_othr_non_income.'.$i.'.numeric']  = 'Non operating income must be number';
        		$messages['prfomnc_pbdit.'.$i.'.required'] = 'Operating profit is required';
        		$messages['prfomnc_pbdit.'.$i.'.numeric']  = 'Operating profit must be number';
        		$messages['prfomnc_depreciation.'.$i.'.required'] = 'Depreciation field is required';
        		$messages['prfomnc_depreciation.'.$i.'.numeric']  = 'Depreciation field must be number';
        		$messages['prfomnc_avrg_fixed_assets_prcnt.'.$i.'.required'] = 'Depreciation(%) is required';
        		$messages['prfomnc_avrg_fixed_assets_prcnt.'.$i.'.numeric']  = 'Depreciation(%) must be number';
        		$messages['prfomnc_intrst.'.$i.'.required'] = 'Interest field is required';
        		$messages['prfomnc_intrst.'.$i.'.numeric']  = 'Interest field must be number';
        		$messages['prfomnc_intrst_prcnt.'.$i.'.required'] = 'Net sales(%) is required';
        		$messages['prfomnc_intrst_prcnt.'.$i.'.numeric']  = 'Net sales(%) must be number';
        		$messages['prfomnc_intrst_ratio.'.$i.'.required'] = 'Interest ratio is required';
        		$messages['prfomnc_intrst_ratio.'.$i.'.numeric']  = 'Interest ratio must be number';
        		$messages['prfomnc_net_profit.'.$i.'.required'] = 'Net profit is required';
        		$messages['prfomnc_net_profit.'.$i.'.numeric']  = 'Net profit must be number';
        		$messages['prfomnc_cash_profit.'.$i.'.required'] = 'Cash profit is required';
        		$messages['prfomnc_cash_profit.'.$i.'.numeric']  = 'Cash profit must be number';
        		$messages['prfomnc_dscr.'.$i.'.required'] = 'DSCR field is required';
        		$messages['prfomnc_dscr.'.$i.'.numeric']  = 'DSCR field must be number';
        		$messages['prfomnc_raw_material_prcnt.'.$i.'.required'] = 'Raw Material(%) is required';
        		$messages['prfomnc_raw_material_prcnt.'.$i.'.numeric']  = 'Raw Material(%) must be number';
        		$messages['prfomnc_labour_prcnt.'.$i.'.required'] = 'Labour(%) is required';
        		$messages['prfomnc_labour_prcnt.'.$i.'.numeric']  = 'Labour(%) must be number';
        		$messages['prfomnc_mnufctr_expns_prcnt.'.$i.'.required'] = 'Manufacturing Expenses(%) is required';
        		$messages['prfomnc_mnufctr_expns_prcnt.'.$i.'.numeric']  = 'Manufacturing Expenses(%) must be number';

        		$messages['profit_pbdit_prcnt.'.$i.'.required'] = 'PBDIT(%) is required';
        		$messages['profit_pbdit_prcnt.'.$i.'.numeric']  = 'PBDIT(%) must be number';
        		$messages['profit_pbit_prcnt.'.$i.'.required'] = 'PBIT(%) is required';
        		$messages['profit_pbit_prcnt.'.$i.'.numeric']  = 'PBIT(%) must be number';
        		$messages['profit_pbt_prcnt.'.$i.'.required'] = 'PBT(%) is required';
        		$messages['profit_pbt_prcnt.'.$i.'.numeric']  = 'PBT(%) must be number';
        		$messages['profit_net_prcnt.'.$i.'.required'] = 'Net Profit(%) is required';
        		$messages['profit_net_prcnt.'.$i.'.numeric']  = 'Net Profit(%) must be number';
        		$messages['profit_cash_prcnt.'.$i.'.required'] = 'Cash Profit(%) is required';
        		$messages['profit_cash_prcnt.'.$i.'.numeric']  = 'Cash Profit(%) must be number';
        		$messages['profit_retained_prcnt.'.$i.'.required'] = 'Retained Profit(%) is required';
        		$messages['profit_retained_prcnt.'.$i.'.numeric']  = 'Retained Profit(%) must be number';
        		$messages['profit_return_net_prcnt.'.$i.'.required'] = 'Return on net worth(%) is required';
        		$messages['profit_return_net_prcnt.'.$i.'.numeric']  = 'Return on net worth(%) must be number';
        		$messages['profit_return_assets_prcnt.'.$i.'.required'] = 'Return on assets(%) is required';
        		$messages['profit_return_assets_prcnt.'.$i.'.numeric']  = 'Return on assets(%) must be number';
        		$messages['profit_return_cptl_prcnt.'.$i.'.required'] = 'Return on capital(%) is required';
        		$messages['profit_return_cptl_prcnt.'.$i.'.numeric']  = 'Return on capital(%) must be number';

        		$messages['growth_net_sales_prcnt.'.$i.'.required'] = 'Net sales growth(%) is required';
        		$messages['growth_net_sales_prcnt.'.$i.'.numeric']  = 'Net sales growth(%) must be number';
        		$messages['growth_net_profit_prcnt.'.$i.'.required'] = 'Net profit growth(%) is required';
        		$messages['growth_net_profit_prcnt.'.$i.'.numeric']  = 'Net profit growth(%) must be number';
        		$messages['growth_tangible_prcnt.'.$i.'.required'] = 'Net worth growth(%) is required';
        		$messages['growth_tangible_prcnt.'.$i.'.numeric']  = 'Net worth growth(%) must be number';


        		$messages['fncl_total_assets.'.$i.'.required'] = 'Total Assets is required';
        		$messages['fncl_total_assets.'.$i.'.numeric']  = 'Total Assets must be number';
        		$messages['fncl_curr_assets.'.$i.'.required'] = 'Total current Assets is required';
        		$messages['fncl_curr_assets.'.$i.'.numeric']  = 'Total current Assets must be number';
        		$messages['fncl_non_curr_assets.'.$i.'.required'] = 'Total non current Assets is required';
        		$messages['fncl_non_curr_assets.'.$i.'.numeric']  = 'Total non current Assets must be number';
        		$messages['fncl_tol.'.$i.'.required'] = 'TOL is required';
        		$messages['fncl_tol.'.$i.'.numeric']  = 'TOL must be number';
        		$messages['fncl_tnw.'.$i.'.required'] = 'TNW is required';
        		$messages['fncl_tnw.'.$i.'.numeric']  = 'TNW must be number';
        		$messages['fncl_investment.'.$i.'.required'] = 'Investment in associates is required';
        		$messages['fncl_investment.'.$i.'.numeric']  = 'Investment in associates must be number';
        		$messages['fncl_quasi_equity.'.$i.'.required'] = 'Quasi equity is required';
        		$messages['fncl_quasi_equity.'.$i.'.numeric']  = 'Quasi equity must be number';
        		$messages['fncl_atnw.'.$i.'.required'] = 'ATNW is required';
        		$messages['fncl_atnw.'.$i.'.numeric']  = 'ATNW must be number';

        		$messages['levrge_tnw.'.$i.'.required'] = 'TNW ratio is required';
        		$messages['levrge_tnw.'.$i.'.numeric']  = 'TNW ratio must be number';
        		$messages['levrge_atnw.'.$i.'.required'] = 'ATNW is required';
        		$messages['levrge_atnw.'.$i.'.numeric']  = 'ATNW must be number';
        		$messages['levrge_long_tnw.'.$i.'.required'] = 'Long term debt(TNW) is required';
        		$messages['levrge_long_tnw.'.$i.'.numeric']  = 'Long term debt(TNW) must be number';
        		$messages['levrge_long_atnw.'.$i.'.required'] = 'Long term debt(ATNW) is required';
        		$messages['levrge_long_atnw.'.$i.'.numeric']  = 'Long term debt(ATNW) must be number';
        		$messages['levrge_cash_profit.'.$i.'.required'] = 'Cash profit is required';
        		$messages['levrge_cash_profit.'.$i.'.numeric']  = 'Cash profit must be number';
        		$messages['levrge_total_debt.'.$i.'.required'] = 'Total Debt is required';
        		$messages['levrge_total_debt.'.$i.'.numeric']  = 'Total Debt must be number';
        		$messages['levrge_pbdit.'.$i.'.required'] = 'DEBT/PBDIT is required';
        		$messages['levrge_pbdit.'.$i.'.numeric']  = 'DEBT/PBDIT must be number';

        		$messages['liqdty_net_capital.'.$i.'.required'] = 'Net working capital is required';
        		$messages['liqdty_net_capital.'.$i.'.numeric']  = 'Net working capital must be number';
        		$messages['liqdty_curr_ratio.'.$i.'.required'] = 'Current ration is required';
        		$messages['liqdty_curr_ratio.'.$i.'.numeric']  = 'Current ration must be number';
        		$messages['liqdty_quick_ratio.'.$i.'.required'] = 'Quick ratio is required';
        		$messages['liqdty_quick_ratio.'.$i.'.numeric']  = 'Quick ratio must be number';

        		$messages['activity_domestic_trnvr.'.$i.'.required'] = 'Receivable domestic turnover is required';
        		$messages['activity_domestic_trnvr.'.$i.'.numeric']  = 'Receivable domestic turnover must be number';
        		$messages['activity_export_trnvr.'.$i.'.required'] = 'Receivable export turnover is required';
        		$messages['activity_export_trnvr.'.$i.'.numeric']  = 'Receivable export turnover must be number';
        		$messages['activity_total_trnvr.'.$i.'.required'] = 'Receivable total turnover is required';
        		$messages['activity_total_trnvr.'.$i.'.numeric']  = 'Receivable total turnover must be number';

        		$messages['activity_inventory_trnvr.'.$i.'.required'] = 'Inventory turnover is required';
        		$messages['activity_inventory_trnvr.'.$i.'.numeric']  = 'Inventory turnover must be number';
        		$messages['activity_creditors_trnvr.'.$i.'.required'] = 'Creditors turnover is required';
        		$messages['activity_creditors_trnvr.'.$i.'.numeric']  = 'Creditors turnover must be number';
        		$messages['activity_fixed_trnvr.'.$i.'.required'] = 'Fixed assets turnover is required';
        		$messages['activity_fixed_trnvr.'.$i.'.numeric']  = 'Fixed assets turnover must be number';

        		$messages['funds_long_source.'.$i.'.required'] = 'Long term sources is required';
        		$messages['funds_long_source.'.$i.'.numeric']  = 'Long term sources must be number';
        		$messages['funds_long_uses.'.$i.'.required'] = 'Long term uses is required';
        		$messages['funds_long_uses.'.$i.'.numeric']  = 'Long term uses must be number';
        		$messages['funds_net_capital.'.$i.'.required'] = 'Contribution to capital is required';
        		$messages['funds_net_capital.'.$i.'.numeric']  = 'Contribution to capital must be number';

        		$messages['cash_net.'.$i.'.required'] = 'Net cash from operations is required';
        		$messages['cash_net.'.$i.'.numeric']  = 'Net cash from operations must be number';
        		$messages['cash_before_funding.'.$i.'.required'] = 'Cash before funding is required';
        		$messages['cash_before_funding.'.$i.'.numeric']  = 'Cash before funding must be number';
        		$messages['cash_investment.'.$i.'.required'] = 'Investment is required';
        		$messages['cash_investment.'.$i.'.numeric']  = 'Investment must be number';

        		$messages['cash_negative_capital.'.$i.'.required'] = 'Capital from bank is required';
        		$messages['cash_negative_capital.'.$i.'.numeric']  = 'Capital from bank must be number';
        		$messages['cash_negative_debts.'.$i.'.required'] = 'Term debts is required';
        		$messages['cash_negative_debts.'.$i.'.numeric']  = 'Term debts must be number';
        		$messages['cash_negative_equity.'.$i.'.required'] = 'Equity is required';
        		$messages['cash_negative_equity.'.$i.'.numeric']  = 'Equity must be number';
            $messages['debtors.'.$i.'.required'] = 'Debtors field is required';
            $messages['debtors.'.$i.'.numeric']  = 'Debtors field must be number';
        	}
        		$messages['sales_and_profit.required']  = 'Sales & Profit field is required';
        		$messages['sales_and_profit.string']  = 'Sales & Profit must be string only';
        		$messages['sales_and_profit.min']  = 'Sales & Profit must be at least 20 chars';
        		$messages['sales_and_profit.max']  = 'Sales & Profit must be at most 200 chars';
        		$messages['gearing.required']  = 'Gearing field is required';
        		$messages['gearing.string']  = 'Gearing must be string only';
        		$messages['gearing.min']  = 'Gearing must be at least 20 chars';
        		$messages['gearing.max']  = 'Gearing must be at most 200 chars';
        		$messages['liquidity_ratio.required']  = 'Liquidity ratio field is required';
        		$messages['liquidity_ratio.string']  = 'Liquidity ratio must be string only';
        		$messages['liquidity_ratio.min']  = 'Liquidity ratio must be at least 20 chars';
        		$messages['liquidity_ratio.max']  = 'Liquidity ratio must be at most 200 chars';
        		$messages['capital_cycle.required']  = 'Capital cycle field is required';
        		$messages['capital_cycle.string']  = 'Capital cycle must be string only';
        		$messages['capital_cycle.min']  = 'Capital cycle must be at least 20 chars';
        		$messages['capital_cycle.max']  = 'Capital cycle must be at most 200 chars';
        		$messages['average_collection_period.required']  = 'Average collection period field is required';
        		$messages['average_collection_period.string']  = 'Average collection period must be string only';
        		$messages['average_collection_period.min']  = 'Average collection period must be at least 20 chars';
        		$messages['average_collection_period.max']  = 'Average collection period must be at most 200 chars';
            $messages['financial_risk_comments.required']  = 'Risk comments field is required';
            $messages['financial_risk_comments.string']  = 'Risk comments must be string only';
            $messages['financial_risk_comments.min']  = 'Risk comments must be at least 20 chars';
            $messages['financial_risk_comments.max']  = 'Risk comments must be at most 200 chars';
            $messages['inventory_payable_days.required'] = 'Payable days field is required';
            $messages['inventory_payable_days.numeric']  = 'Payable days field must be number';
            $messages['inventory_projections.required'] = 'Projections field is required';
            $messages['inventory_projections.numeric']  = 'Projections field must be number';

        return $messages;
    }
}