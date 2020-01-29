<?php 

define('ENCRYPTION_KEY', '0702f2c9c1414b70efc1e69f2ff31af0');

function _encrypt($plaintext = ''){
	$method = "AES-256-CBC";
		$iv = '0000000000000000';
		$key = ENCRYPTION_KEY;
		$ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
		return base64_encode($ciphertext);
}

function _decrypt($encoded = ''){
	$method = "AES-256-CBC";
		$iv = '0000000000000000';
		$key = ENCRYPTION_KEY;
		$ciphertext  = base64_decode($encoded);
		$plaintext = openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
		return $plaintext;
}

function _rand_str($length = 2){
	 $random_string = '';
	 $permitted_chars = str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
	 $input_length = strlen($permitted_chars); 
	 for($i = 0; $i < $length; $i++) {
			$random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
			$random_string .= $random_character;
	 }
	 return $random_string;
}

function _uuid_rand($strLen = 60){
	$string = sprintf('%04x%04x%04x%05x%05x%04x%04x%04x%05x%05x%06x',
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),mt_rand(0, 0xffff),
		mt_rand(0, 0x0fff) | 0x4000,mt_rand(0, 0x3fff) | 0x8000,
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		mt_rand(0, 0x0fff) | 0x4000,
		mt_rand(0, 0x3fff) | 0x8000,
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	);
	return substr($string, 0, $strLen);
}



function extra_char($string = ''){
	 $i = 0;
	 $extra_char = '';
	 while (isset($string[$i])) {
				$char  = $string[$i];
				if (!is_numeric($char))  break;
				$extra_char .= $char;
				$i++;
	 }
	 return $extra_char;
}

function _getRand($stringLen = 12, $min_year = 1950) {
		$temp =  $y = date('Y') - $min_year;
		$append = '';
		$div = $temp / 26;
		if (is_int($div)) {
				$temp =  $temp - 26;
		}
		$fixed = $temp >= 26 ? floor($temp / 26) : 0;
		$y = $y % 26 == 0 ?  90 : ($y % 26) + 64;
		$year = $fixed. chr($y);
		$m = date('m') + 64;
		$d = date('d');
		$d = (($d <= 25) ? ($d + 64) : ($d + 23));
		$h = date('H') + 65;
		$i = date('i');
		$s = date('s');
		$timestamp = $year . chr($m) . chr($d) . chr($h). $i . $s;
		$randStrLen = $stringLen - strlen($timestamp);
		return $timestamp . ($randStrLen <= 0 ? '' : _rand_str($randStrLen));
}

function _getRandReverse($string = '', $min_year = 1950) {
		if (is_numeric($string) || strlen($string) < 9) return $string;
		$strlen = strlen($string);
		$extra_char = extra_char($string);
		$extra_year = $extra_char * 26;
		$value = substr($string, strlen($extra_char), $strlen);
		$date = substr($value, 0, 4);
		$time = substr($value, 4, 4);
		$random = substr($value, 8);
		list($y , $m, $d, $h) = str_split($date);
		$y = ord($y) + $min_year - 64 + $extra_year;
		$m = sprintf('%02d', ord($m) - 64);
		$d = sprintf('%02d', is_numeric($d) ? ord($d) - 23 : ord($d) - 64);
		$h = ord($h) - 65;
		$i = substr($time, 0, 2);
		$s = substr($time,-2);
		$datetime = "$y-$m-$d $h:$i:$s";
		return $datetime . ($random ? "-$random" : '' );
}

function getGrowth($curr_year, $prev_year) {
	$curr_year_data = getTotalFinanceData($curr_year);
	$prev_year_data = getTotalFinanceData($prev_year);
	$netSalesGrowth = (($curr_year_data['TotalOperatingIncome'] - $prev_year_data['TotalOperatingIncome']) /$prev_year_data['TotalOperatingIncome'])*100;
	$netProfitGrowth = (($curr_year_data['NetProfit'] - $prev_year_data['NetProfit']) /$prev_year_data['NetProfit'])*100;
	$tangibleNetWorthGrowth = (($curr_year_data['TangibleNetWorth'] - $prev_year_data['TangibleNetWorth']) /$prev_year_data['TangibleNetWorth'])*100;
	return array(
		'netSalesGrowth' => $netSalesGrowth,
		'netProfitGrowth' => $netProfitGrowth,
		'tangibleNetWorthGrowth' => $tangibleNetWorthGrowth,
	);
}

function getFinancialDetailSummaryColumns() {
	$fields = array(
		'performance_analysis_cols' => array(
			'TotalOperatingIncome' => 'NET SALES (incl. Trading and Other Operating Income)',
			'TotalNonOperatingIncome' => 'OTHER NON OPERATING INCOME',
			'PBDITOperatingProfit' => 'PBDIT (OPERATING PROFIT)',
			'Depreciation' => 'DEPRECIATION',
			'DeprecationAverageNetFixedAssetsPer' => 'DEPRECIATION / AVEAGRE NET FIXED ASSETS (%)',
			'Interest' => 'INTEREST',
			'InterestNetSalesPer' => 'INTEREST / NET SALES (%)',
			'PbditInterestPer' => 'INTEREST COVERAGE RATIO (PBDIT / INTEREST)',
			'NetProfit' => 'NET PROFIT',
			'CashProfit' => 'CASH PROFIT',
			'DSCR' => 'DSCR',
		),
		'profitability_analysis_cols' => array(
			'pbditNetsales' => 'PBDIT / NET SALES (%))',
			'netProfitNetSales' => 'NET PROFIT / NET SALES (%)',
			'cashProfitNetSales' => 'CASH PROFIT / NET SALES (%)',
		),
		'financial_position_analysis_cols' => array(
			'TotalFixedAssets' => 'TOTAL FIXED ASSETS',
			'TotalOutsideLiabilities' => 'TOTAL OUTSIDE LIABILITIES',
			'TangibleNetWorth' => 'TANGIBLE NETWORTH (TNW)',
		),
		'growth_analysis_cols' => array(
			'netSalesGrowth' => 'NET SALES GROWTH (%)',
			'netProfitGrowth' => 'NET PROFIT GROWTH (%)',
			'tangibleNetWorthGrowth' => 'TANGIBLE NET WORTH GROWTH (%)',
		),
		'leverage_analysis_cols' => array(
			'TolTnw' => 'TOL / TNW RATIO',
			'TolAdjTnwAtnw' => 'TOL / ADJ. TNW (ATNW)',
			'DebtPbdit' => 'DEBT /PBDIT',
		),
		'activity_efficiency_analysis_cols' => array(
			'RecievableTurnover' => 'RECEIVABLE TURNOVER DAYS (TOTAL , Inc. DEBTORS > 6 MONTHS)',
		),
		'fundsFlowAnalysis_cols' => array(
			'CashAndBankBalances' => 'CASH AND BANK BALANCE',
		),
	);
	return $fields;
}


function CalculateGrossSale($year_array)
{
	return "Need To Calculate";
}
function CalculateNetSales($year_array)
{
	return "Need To Calculate";
}
function CalculateIncreaseInNetSales($year_array)
{
	return "Need To Calculate";
}
function CalculateTotalOperatingIncome($year_array)
{
	return "Need To Calculate";
}
function CalculateIncreaseInNetIncome($year_array)
{
	return "Need To Calculate";
}
function CalculateCostofProduction($year_array)
{
	return "Need To Calculate";
}
function CalculateCOPSofGrossIncome($year_array)
{
	return "Need To Calculate";
}
function CalculateCostofSales($year_array)
{
	return "Need To Calculate";
}
function CalculateCostofSalesasPerGrossIncome($year_array)
{
	return "Need To Calculate";
}
function CalculateCostofSalesPlusSGA($year_array)
{
	return "Need To Calculate";
}
function CalculateProfitBeforeInterestTax($year_array)
{
	return "Need To Calculate";
}
function CalculateTotalNonOperatingIncome($year_array)
{
	return "Need To Calculate";
}
function CalculateTotalNonOperatingExpenses($year_array)
{
	return "Need To Calculate";
}
function CalculateNetofNonOperatingIncomeExpenses($year_array)
{
	return "Need To Calculate";
}
function CalculateProfitBeforeInterestDepreciationTax($year_array)
{
	return "Need To Calculate";
}
function CalculateProfitBeforeTaxLoss($year_array)
{
	return "Need To Calculate";
}
function CalculateDefferedTaxes($year_array)
{
	return "Need To Calculate";
}
function CalculateProvisionForTaxesTotal($year_array)
{
	return "Need To Calculate";
}
function CalculatePATasPerGrossIncome($year_array)
{
	return "Need To Calculate";
}
function CalculateTotalExtraordinaryItems($year_array)
{
	return "Need To Calculate";
}
function CalculateAdjustedPAT($year_array)
{
	return "Need To Calculate";
}
function CalculateRetainedProfit($year_array)
{
	return "Need To Calculate";
}
function CalculateSubTotal($year_array)
{
	return "Need To Calculate";
}
function CalculateInterestOtherFinanceCharge($year_array)
{
	return "Need To Calculate";
}
function CalculateInttFinChargeasPerGrossSale($year_array)
{
	return "Need To Calculate";
}
function CalculateOperatingProfitBeforeTax($year_array)
{
	return "Need To Calculate";
}
function CalculateOPBTasPerGrossIncome($year_array)
{
	return "Need To Calculate";
}
function CalculatePBITasPerGrossSale($year_array)
{
	return "Need To Calculate";
}
function CalculateNetProfitLoss($year_array)
{
	return "Need To Calculate";
}

function getProfitandLossColumns(){
	$fields = array(
		'income_cols' => array(
			'GrossDomesticSales' => 'Gross Domestic Sales',
			'ExportSales' => 'Export Sales',
			'CalculateGrossSale' => 'Gross Sales',
			'LessExciseDuty' => 'Less Excise duty',
			'CalculateNetSales' => 'Net Sales',
			'CalculateIncreaseInNetSales' => 'Increase in Net Sales (%)',
			'AddTradingOtherOperatingIncome' => 'ADD: Trading / Other Operating Income',
			'ExportIncentives' => 'Export Incentives',
			'DutyDrawback' => 'Duty Drawback',
			'Others' => 'Others',
			'CalculateTotalOperatingIncome' => 'Total Operating Income',
			'CalculateIncreaseInNetIncome' => 'Increase In Net Income (%)',
		),
		'costofsales_cols' => array(
			'RawMaterials' => array('Imported'=>'Imported','Indigenous' => 'Indigenous'),
			'OtherSpares' => array('Imported'=>'Imported','Indigenous' => 'Indigenous'),
			'PowerFuel' => 'POWER & FUEL',
			'DirectLabour' => 'DIRECT LABOUR',
			'OtherManufacturingExpenses' => 'OTHER MANUFACTURING EXPENSES',
			'Depreciation' => 'DEPRECIATION',
			'RepairsMaintenance' => 'REPAIRS & MAINTENANCE',
			'CostOfTradingGoods' => 'COST OF TRADING GOODS',
			'CalculateSubTotal' => 'SUB TOTAL',
			'AddOpeningStockInProcess' => 'ADD: OPENING STOCK IN PROCESS',
			'DeductClosingStockInProcess' => 'DEDUCT: CLOSING STOCK IN PROCESS',
			'CalculateCostofProduction' => 'COST OF PRODUCTION:',
			'CalculateCOPSofGrossIncome' => 'C O P AS % OF GROSS INCOME',
			'AddOpeningStockOfFinishedGoods' => 'ADD: OPENING STOCK OF FINISHED GOODS',
			'DeductClosingStockOfFinishedGoods' => 'DEDUCT: CLOSING STOCK OF FINISHED GOODS',
			'CalculateCostofSales' => 'COST OF SALES:',
			'CalculateCostofSalesasPerGrossIncome' => 'COST OF SALES AS % OF GROSS INCOME',
			'SellingGeneralAdmExpenses' => 'SELLING, GENERAL & ADM EXPENSES',
			'CalculateCostofSalesPlusSGA' => 'Cost of Sales + SGA',
			'CalculateProfitBeforeInterestTax' => 'PROFIT BEFORE INTEREST & TAX (PBIT)',
			'CalculatePBITasPerGrossSale' => 'PBIT AS % OF GROSS SALES',
			'InterestPaymentToBanks' => array('InterestWc'=>'Interest - WC','InterestTermLoans' => 'Interest - Term Loans'),
			'InterestPaymentToFIs' => array('InterestWc'=>'Interest - WC','InterestTermLoans' => 'Interest - Term Loans'),
			'BankCharges' => 'Bank Charges',
			'CalculateInterestOtherFinanceCharge' => 'INTEREST & OTHER FINANCE CHARGES:',
			'CalculateInttFinChargeasPerGrossSale' => 'INTT. & FIN. CHARGES AS % OF GROSS SALES',
			'CalculateOperatingProfitBeforeTax' => 'OPERATING PROFIT BEFORE TAX (OPBT)',
			'CalculateOPBTasPerGrossIncome' => 'OPBT AS % OF GROSS INCOME',
		),
		'othernonoperativeincome_cols' => array(
			'InterestOnDepositsDividendReceived' => 'Interest On Deposits & Dividend Received',
			'ForexGains' => 'Forex Gains',
			'NonOperatingIncomeFromSubsidiaries' => ' Non Operating Income from Subsidiaries',
			'TaxRefund' => ' Tax Refund',
			'MiscIncome' => 'Misc Income',
			'ProfitOnSaleOfAssetsInvestments' => 'Profit on sale of assets & Investments',
			'OtherIncome' => 'Other Income',
			'ProvisionsExpensesWrittenBack' => 'Provisions / Expenses Written Back',
			'CalculateTotalNonOperatingIncome' => 'Total Non Operating Income',
		),
		'othernonoperatingexp_cols' => array(
			'LossOnSaleOfInvestments' => 'Loss on sale of Investments',
			'LossOnSaleOfFa' => 'Loss on sale of FA',
			'DerivativeLossesBooked' => 'Derivative Losses booked',
			'NetLossOnForeignCurrencyTranslationAndTransactionsLossDueToFire' => ' Net Loss on Foreign Currency Translation and Transactions',
			'PreliExpOneTimeExpensesWrittenOff' => 'Preli.Exp / One Time Expenses Written Off',
			'MiscExpWrittenOff' => 'Misc Exp. Written Off',
			'ProvForDoubDebtsDimInTheValOfInv' => 'Prov. for doub.debts & Dim.in the val. of Inv.',
			'WealthTax' => 'Wealth Tax',
			'CalculateTotalNonOperatingExpenses' => 'TOTAL NON OPERATING EXPENSES',
			'CalculateNetofNonOperatingIncomeExpenses' => 'NET OF NON OPERATING INCOME/EXPENSES',
			'CalculateProfitBeforeInterestDepreciationTax' => 'PROFIT BEFORE INTEREST, DEPRECIATION & TAX (PBIDT)',
			'CalculateProfitBeforeTaxLoss' => 'PROFIT BEFORE TAX / LOSS (PBT)',
			'TaxPaid' => 'TAX PAID',
			'ProvisionForTaxesCurrentPeriod' => 'PROVISION FOR TAXES - Current Period',
			'CalculateDefferedTaxes' => 'Deffered Taxes',
			'CalculateProvisionForTaxesTotal' => 'PROVISION FOR TAXES - TOTAL',
			'CalculateNetProfitLoss' => 'NET PROFIT/LOSS (PAT)',
			'CalculatePATasPerGrossIncome' => 'PAT AS % OF GROSS Income',
		),
		'extraordinaryitemadjustments_cols' => array(
			'ExtraordinaryIncomeAdjustments' => 'Extraordinary Income adjustments (+)',
			'ExtraordinaryExpensesAdjustments' => 'Extraordinary Expenses adjustments (-)',
			'CalculateTotalExtraordinaryItems' => 'Total Extraordinary items',
			'CalculateAdjustedPAT' => 'Adjusted PAT (excl Extraordinary Items)',
		),
		'equityDividendPaid_cols' => array(
			'EquityDividendPaidAmount' => 'AMOUNT',
			'EquityDividendPaidRate' => 'RATE',
			'DividendTax' => 'Dividend tax',
			'DividendPreference' => 'Dividend -Preference',
			'PartnersWithdrawal' => 'Partners withdrawal',
			'CalculateRetainedProfit' => 'RETAINED PROFIT',
		),
	);
	return $fields;
}


function getTotalFinanceData($fullArray){
	$ProfitAndLoss = $fullArray['ProfitAndLoss'];
	$Liabilities = $fullArray['BalanceSheet']['Liabilities'];
	$Assets = $fullArray['BalanceSheet']['Assets'];
	extract($ProfitAndLoss);
	extract($Liabilities);
	extract($Assets);
	$response = [];
	$AddOpeningStockInProcessRawMaterials = $RawMaterials['Imported'] + $RawMaterials['Indigenous'];
	$OtherSpares = $OtherSpares['Imported'] + $OtherSpares['Indigenous'] ;
	$InterestPaymentToBanksSum = $InterestPaymentToBanks['InterestWc'] + $InterestPaymentToBanks['InterestTermLoans'];
	$InterestPaymentToFIsSum = $InterestPaymentToFIs['InterestWc'] + $InterestPaymentToFIs['InterestTermLoans'];

	$response['TotalOperatingIncome'] = $TotalOperatingIncome = $GrossDomesticSales + $ExportSales - $LessExciseDuty+ $AddTradingOtherOperatingIncome+ $ExportIncentives+ $DutyDrawback+ $Others;
	$response['TotalNonOperatingIncome'] = $InterestOnDepositsDividendReceived + $ForexGains + $NonOperatingIncomeFromSubsidiaries + $TaxRefund + $MiscIncome + $ProfitOnSaleOfAssetsInvestments + $OtherIncome + $ProvisionsExpensesWrittenBack;
	$response['PBDITOperatingProfit'] = $TotalOperatingIncome -($AddOpeningStockInProcessRawMaterials + $OtherSpares + $PowerFuel + $DirectLabour + $OtherManufacturingExpenses+ $Depreciation+ $RepairsMaintenance + $CostOfTradingGoods + $AddOpeningStockInProcess - $DeductClosingStockInProcess + $AddOpeningStockOfFinishedGoods - $DeductClosingStockOfFinishedGoods + $SellingGeneralAdmExpenses ) + $Depreciation;
	$response['Depreciation'] = $Depreciation;
	$response['DeprecationAverageNetFixedAssetsPer'] = ($Depreciation / (($Land+$Building+$Vehicles+$PlantMachinery+$FurnitureFixtures + $OtherFixedAssets + $CapitalWip-$LessAccumulatedDepreciation-$RevaluationReserve)/2))  * 100;
	$response['Interest'] = $InterestPaymentToBanksSum+$InterestPaymentToFIsSum+$BankCharges;
	$response['InterestNetSalesPer'] = ($response['Interest']/$TotalOperatingIncome)*100; 
	$response['PbditInterestPer'] = $response['PBDITOperatingProfit']/$response['Interest'];
	$response['NetProfit'] = $response['TotalOperatingIncome']-($AddOpeningStockInProcessRawMaterials+$OtherSpares+$PowerFuel+$DirectLabour+$OtherManufacturingExpenses+$Depreciation+$RepairsMaintenance+$CostOfTradingGoods+0+$AddOpeningStockInProcess-$DeductClosingStockInProcess+$AddOpeningStockOfFinishedGoods-$DeductClosingStockOfFinishedGoods+$SellingGeneralAdmExpenses)-$response['Interest']+$response['TotalNonOperatingIncome']+0-($LossOnSaleOfInvestments+$LossOnSaleOfFa+$DerivativeLossesBooked+$NetLossOnForeignCurrencyTranslationAndTransactionsLossDueToFire + $PreliExpOneTimeExpensesWrittenOff+$MiscExpWrittenOff+$ProvForDoubDebtsDimInTheValOfInv+$WealthTax)-$TaxPaid-($ProvisionForTaxesCurrentPeriod+$ProvisionForTaxesDefferedTaxes);
	$response['CashProfit'] = ($TotalOperatingIncome -($AddOpeningStockInProcessRawMaterials+$OtherSpares+$PowerFuel + $DirectLabour + $OtherManufacturingExpenses+ $Depreciation+ $RepairsMaintenance + $CostOfTradingGoods + $AddOpeningStockInProcess - $DeductClosingStockInProcess + $AddOpeningStockOfFinishedGoods - $DeductClosingStockOfFinishedGoods + $SellingGeneralAdmExpenses)) - ($InterestPaymentToBanksSum + $InterestPaymentToFIsSum + $BankCharges) + ( $InterestOnDepositsDividendReceived + $ForexGains + $NonOperatingIncomeFromSubsidiaries + $TaxRefund + $MiscIncome + $ProfitOnSaleOfAssetsInvestments + $OtherIncome + $ProvisionsExpensesWrittenBack) - ($LossOnSaleOfInvestments+$LossOnSaleOfFa+$DerivativeLossesBooked+$NetLossOnForeignCurrencyTranslationAndTransactionsLossDueToFire+$PreliExpOneTimeExpensesWrittenOff+$MiscExpWrittenOff +$ProvForDoubDebtsDimInTheValOfInv +$WealthTax) - $TaxPaid - ($ProvisionForTaxesCurrentPeriod + $ProvisionForTaxesDefferedTaxes) +$Depreciation - $ProvisionsExpensesWrittenBack;
	$response['TangibleNetWorth'] = ($PartnersCapitalProprietorSCapital+ $ShareCapitalPaidUp + $ShareApplicationFinalizedForAllotment+$StatutoryAndCapitalReserves+ $GeneralReserve+ $RevaluationReserve+ $OtherReservesExcludingProvisions + $SurplusOrDeficitInPLAccount+ $SharePremiumAC + $CapitalSubsidy + $InvestmentAllowanceUtilizationReserve -$RevaluationReserve) - ($AccumulatedLossesPreliminaryExpensesMiscellaneousExpenditureNotWOffOtherDeferredRevenueExpenses + $DeferredTaxAsset);
	$response['TolTnw'] = ($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $SundryCreditorsTrade + $ShortTermBorrowingsFromAssociatesGroupConcerns + $ShortTermBorrowingsCommercialPaper + $ShortTermBorrowingsFromOthers + $AdvancesPaymentsFromCustomersDepositsFromDealers + $ProvisionForTaxation + $ProposedDividend + $OtherStatutoryLiabilitiesDueWithinOneYear + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $DepositsDueForRepaymentDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $OtherCurrentLiabilitiesProvisionsDueWithin1Year + $InterestAccButNotDue + $ProvisionForNpa + $ProvisionForLeaveEncashmentGratuity + $UnclaimedDividend + $OtherLiabilities + $DueToSubsidiaryCompaniesAffiliates + $TaxOnInterimDividendPayable + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)/ $response['TangibleNetWorth'];
	$response['TolAdjTnwAtnw'] = ((($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $SundryCreditorsTrade + $ShortTermBorrowingsFromAssociatesGroupConcerns + $ShortTermBorrowingsCommercialPaper + $ShortTermBorrowingsFromOthers + $AdvancesPaymentsFromCustomersDepositsFromDealers + $ProvisionForTaxation + $ProposedDividend + $OtherStatutoryLiabilitiesDueWithinOneYear + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $DepositsDueForRepaymentDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $OtherCurrentLiabilitiesProvisionsDueWithin1Year + $InterestAccButNotDue + $ProvisionForNpa + $ProvisionForLeaveEncashmentGratuity + $UnclaimedDividend + $OtherLiabilities + $DueToSubsidiaryCompaniesAffiliates + $TaxOnInterimDividendPayable + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)) - $BorrowingsFromSubsidiariesAffiliatesQuasiEquity) / ( $response['TangibleNetWorth'] -$InvestmentsInSubsidiaryCompaniesAffiliates + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity);
	$response['DebtPbdit'] = ($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $ShortTermBorrowingsCommercialPaper + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)/ $response['PBDITOperatingProfit'];
	$response['RecievableTurnover'] = (($ReceivablesOtherThanDeferredExportsInclBillsPurchasedDiscountedByBanks+$ExportReceivablesIncludingBillPurchasedAndDiscounted+$RetentionMoneySecurityDeposit+$DebtorsMoreThan6Months)/($GrossDomesticSales+$ExportSales+$AddTradingOtherOperatingIncome))*365;
	$response['CashAndBankBalances'] = $CashAndBankBalances;
	$response['pbditNetsales'] = ($response['PBDITOperatingProfit'] / $response['TotalOperatingIncome']) * 100;
	$response['netProfitNetSales'] = ($response['NetProfit'] / $response['TotalOperatingIncome']) * 100;
	$response['cashProfitNetSales'] = ($response['CashProfit'] / $response['TotalOperatingIncome']) * 100;
	$response['TotalFixedAssets'] = $Land + $Building + $Vehicles + $PlantMachinery + $FurnitureFixtures + $OtherFixedAssets + $CapitalWip-$LessAccumulatedDepreciation-$RevaluationReserve;
	$response['TotalOutsideLiabilities'] = $FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $SundryCreditorsTrade + $ShortTermBorrowingsFromAssociatesGroupConcerns + $ShortTermBorrowingsCommercialPaper + $ShortTermBorrowingsFromOthers + $AdvancesPaymentsFromCustomersDepositsFromDealers + $ProvisionForTaxation + $ProposedDividend + $OtherStatutoryLiabilitiesDueWithinOneYear + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $DepositsDueForRepaymentDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $OtherCurrentLiabilitiesProvisionsDueWithin1Year + $InterestAccButNotDue + $ProvisionForNpa + $ProvisionForLeaveEncashmentGratuity + $UnclaimedDividend + $OtherLiabilities + $DueToSubsidiaryCompaniesAffiliates + $TaxOnInterimDividendPayable + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances + $PartnersCapitalProprietorSCapital + $ShareCapitalPaidUp + $ShareApplicationFinalizedForAllotment + $StatutoryAndCapitalReserves + $GeneralReserve + $RevaluationReserve + $OtherReservesExcludingProvisions + $SurplusOrDeficitInPLAccount + $SharePremiumAC + $CapitalSubsidy + $InvestmentAllowanceUtilizationReserve-$RevaluationReserve-($PartnersCapitalProprietorSCapital + $ShareCapitalPaidUp + $ShareApplicationFinalizedForAllotment + $StatutoryAndCapitalReserves + $GeneralReserve + $RevaluationReserve + $OtherReservesExcludingProvisions + $SurplusOrDeficitInPLAccount + $SharePremiumAC + $CapitalSubsidy + $InvestmentAllowanceUtilizationReserve-$RevaluationReserve);
	$response['DSCR'] =  ($response['CashProfit'] + $InterestPaymentToBanks['InterestTermLoans'] + $InterestPaymentToFIs['InterestTermLoans']) / ($InterestPaymentToBanks['InterestTermLoans'] + $InterestPaymentToFIs['InterestTermLoans'] + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $DepositsDueForRepaymentDueWithin1Year + $PreferenceSharesRedeemableWithin1Year);

	return $response;
}

?>