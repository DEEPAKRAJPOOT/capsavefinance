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
	$netIncomeGrowth = (($curr_year_data['TotalOperatingIncome'] - $prev_year_data['TotalOperatingIncome']) /$prev_year_data['TotalOperatingIncome'])*100;
	$netProfitGrowth = (($curr_year_data['NetProfit'] - $prev_year_data['NetProfit']) /$prev_year_data['NetProfit'])*100;
	$tangibleNetWorthGrowth = (($curr_year_data['TangibleNetWorth'] - $prev_year_data['TangibleNetWorth']) /$prev_year_data['TangibleNetWorth'])*100;
	return array(
		'netSalesGrowth' => $netSalesGrowth,
		'netIncomeGrowth' => $netIncomeGrowth,
		'netProfitGrowth' => $netProfitGrowth,
		'tangibleNetWorthGrowth' => $tangibleNetWorthGrowth,
	);
}

function arrayValuesToInt(&$array){
  if(is_array($array)){
    foreach($array as &$arrayPiece){
      arrayValuesToInt($arrayPiece);
    }
  }else{
    $array = !is_numeric($array) ? floatval($array) : $array;
  }
  return $array;
}

#================================================================================================#
function CalculateGrossSale($ProfitAndLoss, $growth = array()) {
	return ($ProfitAndLoss['GrossDomesticSales'] + $ProfitAndLoss['ExportSales']);
}
function CalculateNetSales($ProfitAndLoss, $growth = array()) {	
	return (CalculateGrossSale($ProfitAndLoss) - $ProfitAndLoss['LessExciseDuty']);
}
function CalculateIncreaseInNetSales($ProfitAndLoss, $growth = array()) {
	return sprintf('%.2f', $growth['netSalesGrowth']);
}
function CalculateTotalOperatingIncome($ProfitAndLoss, $growth = array()) {
	$TotalOperatingIncome = $ProfitAndLoss['GrossDomesticSales'] + $ProfitAndLoss['ExportSales'] - $ProfitAndLoss['LessExciseDuty']+ $ProfitAndLoss['AddTradingOtherOperatingIncome']+ $ProfitAndLoss['ExportIncentives']+ $ProfitAndLoss['DutyDrawback']+ $ProfitAndLoss['Others'];
	return sprintf('%.2f', $TotalOperatingIncome);
}
function CalculateIncreaseInNetIncome($ProfitAndLoss, $growth = array()){
	return sprintf('%.2f', $growth['netIncomeGrowth']);
	
}
function CalculateSubTotal($ProfitAndLoss) {
	$RawMaterials = array_sum($ProfitAndLoss['RawMaterials']);
	$OtherSpares = array_sum($ProfitAndLoss['OtherSpares']);
	$subtotal = $RawMaterials +  $OtherSpares + $ProfitAndLoss['PowerFuel'] + $ProfitAndLoss['DirectLabour'] + $ProfitAndLoss['OtherManufacturingExpenses'] + $ProfitAndLoss['Depreciation'] + $ProfitAndLoss['RepairsMaintenance'] + $ProfitAndLoss['CostOfTradingGoods'];
	return sprintf('%.2f', $subtotal);
}
function CalculateCostofProduction($ProfitAndLoss) {
	$RawMaterials = array_sum($ProfitAndLoss['RawMaterials']);
	$OtherSpares = array_sum($ProfitAndLoss['OtherSpares']);
	$CostofProduction = $RawMaterials + $OtherSpares + $ProfitAndLoss['PowerFuel'] + $ProfitAndLoss['DirectLabour'] + $ProfitAndLoss['OtherManufacturingExpenses'] + $ProfitAndLoss['Depreciation'] + $ProfitAndLoss['RepairsMaintenance'] + $ProfitAndLoss['CostOfTradingGoods'] + $ProfitAndLoss['AddOpeningStockInProcess'] - $ProfitAndLoss['DeductClosingStockInProcess'];
	return sprintf('%.2f', $CostofProduction);
}
function CalculateCOPSofGrossIncome($ProfitAndLoss) {
	$COPSofGrossIncome =  CalculateCostofProduction($ProfitAndLoss)/ CalculateGrossSale($ProfitAndLoss);
	return sprintf('%.2f', $COPSofGrossIncome);
}
function CalculateCostofSales($ProfitAndLoss) {
	$CostofSales = CalculateCostofProduction($ProfitAndLoss) + $ProfitAndLoss['AddOpeningStockOfFinishedGoods'] - 
	$ProfitAndLoss['DeductClosingStockOfFinishedGoods'];
	return sprintf('%.2f', $CostofSales);
}
function CalculateCostofSalesasPerGrossIncome($ProfitAndLoss) {
	$CostofSalesasPerGrossIncome = CalculateCostofSales($ProfitAndLoss) / CalculateGrossSale($ProfitAndLoss);
	return sprintf('%.2f', $CostofSalesasPerGrossIncome);

}
function CalculateCostofSalesPlusSGA($ProfitAndLoss) {
	$CostofSalesPlusSGA = CalculateCostofSales($ProfitAndLoss) + $ProfitAndLoss['SellingGeneralAdmExpenses'];
	return sprintf('%.2f', $CostofSalesPlusSGA);
}
function CalculateProfitBeforeInterestTax($ProfitAndLoss) {
	 $ProfitBeforeInterestTax = CalculateTotalOperatingIncome($ProfitAndLoss) - CalculateCostofSalesPlusSGA($ProfitAndLoss);
	 return sprintf('%.2f', $ProfitBeforeInterestTax);
}
function CalculatePBITasPerGrossSale($ProfitAndLoss) {
	$PBITasPerGrossSale = CalculateProfitBeforeInterestTax($ProfitAndLoss) / CalculateGrossSale($ProfitAndLoss);
	return sprintf('%.2f', $PBITasPerGrossSale);
}
function CalculateInterestOtherFinanceCharge($ProfitAndLoss) {
	$InterestPaymentToBanks = array_sum($ProfitAndLoss['InterestPaymentToBanks']);
	$InterestPaymentToFIs = array_sum($ProfitAndLoss['InterestPaymentToFIs']);
	$InterestOtherFinanceCharge = $InterestPaymentToBanks + $InterestPaymentToFIs + $ProfitAndLoss['BankCharges'];
	return sprintf('%.2f', $InterestOtherFinanceCharge);
}
function CalculateInttFinChargeasPerGrossSale($ProfitAndLoss) {
	$InttFinChargeasPerGrossSale = CalculateInterestOtherFinanceCharge($ProfitAndLoss) / CalculateGrossSale($ProfitAndLoss);
	return sprintf('%.2f', $InttFinChargeasPerGrossSale);
}
function CalculateOperatingProfitBeforeTax($ProfitAndLoss) {
	$OperatingProfitBeforeTax = CalculateProfitBeforeInterestTax($ProfitAndLoss)-CalculateInterestOtherFinanceCharge($ProfitAndLoss);
	return sprintf('%.2f', $OperatingProfitBeforeTax);
}
function CalculateOPBTasPerGrossIncome($ProfitAndLoss) {
	$OPBTasPerGrossIncome = CalculateOperatingProfitBeforeTax($ProfitAndLoss)/CalculateGrossSale($ProfitAndLoss);
	return sprintf('%.2f', $OPBTasPerGrossIncome);
}
function CalculateTotalNonOperatingIncome($ProfitAndLoss) {
	$TotalNonOperatingIncome = $ProfitAndLoss['InterestOnDepositsDividendReceived']+$ProfitAndLoss['ForexGains']+$ProfitAndLoss['NonOperatingIncomeFromSubsidiaries']+$ProfitAndLoss['TaxRefund']+$ProfitAndLoss['MiscIncome']+$ProfitAndLoss['ProfitOnSaleOfAssetsInvestments']+$ProfitAndLoss['OtherIncome']+$ProfitAndLoss['ProvisionsExpensesWrittenBack'];
	return sprintf('%.2f', $TotalNonOperatingIncome);
}
function CalculateTotalNonOperatingExpenses($ProfitAndLoss) {
	$TotalNonOperatingExpenses = $ProfitAndLoss['LossOnSaleOfInvestments'] + $ProfitAndLoss['LossOnSaleOfFa'] + $ProfitAndLoss['DerivativeLossesBooked'] + $ProfitAndLoss['NetLossOnForeignCurrencyTranslationAndTransactionsLossDueToFire'] + $ProfitAndLoss['PreliExpOneTimeExpensesWrittenOff'] + $ProfitAndLoss['MiscExpWrittenOff'] + $ProfitAndLoss['ProvForDoubDebtsDimInTheValOfInv'] + $ProfitAndLoss['WealthTax'];
	return sprintf('%.2f', $TotalNonOperatingExpenses);
}
function CalculateNetofNonOperatingIncomeExpenses($ProfitAndLoss) {
	$NetofNonOperatingIncomeExpenses = CalculateTotalNonOperatingExpenses($ProfitAndLoss)-CalculateTotalNonOperatingIncome($ProfitAndLoss);
	return sprintf('%.2f', $NetofNonOperatingIncomeExpenses);
}
function CalculateProfitBeforeInterestDepreciationTax($ProfitAndLoss) {
	$ProfitBeforeInterestDepreciationTax = $ProfitAndLoss['Depreciation'] + CalculateInterestOtherFinanceCharge($ProfitAndLoss) + CalculateOperatingProfitBeforeTax($ProfitAndLoss) + CalculateNetofNonOperatingIncomeExpenses($ProfitAndLoss);
	return sprintf('%.2f', $ProfitBeforeInterestDepreciationTax);
}
function CalculateProfitBeforeTaxLoss($ProfitAndLoss) {
	$ProfitBeforeTaxLoss = CalculateOperatingProfitBeforeTax($ProfitAndLoss)+ CalculateNetofNonOperatingIncomeExpenses($ProfitAndLoss);
	return sprintf('%.2f', $ProfitBeforeTaxLoss);
}
function CalculateDefferedTaxes($ProfitAndLoss) {
	return "0";
}
function CalculateProvisionForTaxesTotal($ProfitAndLoss) {
	$ProvisionForTaxesTotal = $ProfitAndLoss['ProvisionForTaxesCurrentPeriod'] + $ProfitAndLoss['ProvisionForTaxesDefferedTaxes'];
	return sprintf('%.2f', $ProvisionForTaxesTotal);
}
function CalculateNetProfitLoss($ProfitAndLoss) {
	$NetProfitLoss = CalculateProfitBeforeTaxLoss($ProfitAndLoss)- $ProfitAndLoss['TaxPaid'] - $ProfitAndLoss['ProvisionForTaxesCurrentPeriod'] + $ProfitAndLoss['ProvisionForTaxesDefferedTaxes'];
	return sprintf('%.2f', $NetProfitLoss);
}
function CalculatePATasPerGrossIncome($ProfitAndLoss) {
	$PATasPerGrossIncome = CalculateNetProfitLoss($ProfitAndLoss)/CalculateGrossSale($ProfitAndLoss);
	return sprintf('%.2f', $PATasPerGrossIncome);
}
function CalculateTotalExtraordinaryItems($ProfitAndLoss) {
	$TotalExtraordinaryItems = $ProfitAndLoss['ExtraordinaryIncomeAdjustments']-$ProfitAndLoss['ExtraordinaryExpensesAdjustments'];
	return sprintf('%.2f', $TotalExtraordinaryItems);
}
function CalculateAdjustedPAT($ProfitAndLoss) {
	$AdjustedPAT = CalculateNetProfitLoss($ProfitAndLoss) + $ProfitAndLoss['ExtraordinaryIncomeAdjustments']-$ProfitAndLoss['ExtraordinaryExpensesAdjustments'];
	return sprintf('%.2f', $AdjustedPAT);
}
function CalculateRetainedProfit($ProfitAndLoss) {
	$RetainedProfit = CalculateAdjustedPAT($ProfitAndLoss)- $ProfitAndLoss['EquityDividendPaidAmount'] - $ProfitAndLoss['DividendTax'] - $ProfitAndLoss['PartnersWithdrawal'] - $ProfitAndLoss['DividendPreference'];
	return sprintf('%.2f', $RetainedProfit);
}

#====================================================================================#
function CalculateCurrentLiabilitiesSubTotal($Liabilities){
	$SubTotal = $Liabilities['FromApplicantBankCcWcdl'] + $Liabilities['FromOtherBanks'] + $Liabilities['OfIAndIiInWhichBillPurchasedDisc'];
	return sprintf('%.2f', $SubTotal);
}
function CalculateTotalRepaymentDueWithin1Year($Liabilities){
	$TotalRepaymentDueWithin1Year = $Liabilities['InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year'] + $Liabilities['DepositsDueForRepaymentDueWithin1Year'] + $Liabilities['PreferenceSharesRedeemableWithin1Year'];
	return sprintf('%.2f', $TotalRepaymentDueWithin1Year);
}
function CalculateTotalTermLiabilities($Liabilities){
	$TotalTermLiabilities = $Liabilities['Wctl'] + $Liabilities['PrefSharesPortionRedeemableAfter1Yr'] + $Liabilities['TermLoansExcludingInstallmentsPayableWithinOneYear']+ $Liabilities['TermLoansFromFis'] + $Liabilities['Debentures'] + $Liabilities['TermDeposits'] + $Liabilities['UnsecuredLoans'] + $Liabilities['BorrowingsFromSubsidiariesAffiliatesQuasiEquity']+ $Liabilities['DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm']+ $Liabilities['OtherTermLiabilities']+ $Liabilities['DeferredTaxLiability']+ $Liabilities['OtherLoanAdvances'];
	return sprintf('%.2f', $TotalTermLiabilities);
}
function CalculateTotalOutsideLiabilities($Liabilities){
	$TotalOutsideLiabilities = $Liabilities['FromApplicantBankCcWcdl'] + $Liabilities['FromOtherBanks'] + $Liabilities['OfIAndIiInWhichBillPurchasedDisc'] + $Liabilities['SundryCreditorsTrade'] + $Liabilities['ShortTermBorrowingsFromAssociatesGroupConcerns'] + $Liabilities['ShortTermBorrowingsCommercialPaper'] + $Liabilities['ShortTermBorrowingsFromOthers'] + $Liabilities['AdvancesPaymentsFromCustomersDepositsFromDealers'] + $Liabilities['ProvisionForTaxation'] + $Liabilities['ProposedDividend'] + $Liabilities['OtherStatutoryLiabilitiesDueWithinOneYear'] + $Liabilities['InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year'] + $Liabilities['DepositsDueForRepaymentDueWithin1Year'] + $Liabilities['PreferenceSharesRedeemableWithin1Year'] + $Liabilities['OtherCurrentLiabilitiesProvisionsDueWithin1Year'] + $Liabilities['InterestAccButNotDue'] + $Liabilities['ProvisionForNpa'] + $Liabilities['ProvisionForLeaveEncashmentGratuity'] + $Liabilities['UnclaimedDividend'] + $Liabilities['OtherLiabilities'] + $Liabilities['DueToSubsidiaryCompaniesAffiliates'] + $Liabilities['TaxOnInterimDividendPayable'] + $Liabilities['Wctl'] + $Liabilities['PrefSharesPortionRedeemableAfter1Yr'] + $Liabilities['TermLoansExcludingInstallmentsPayableWithinOneYear'] + $Liabilities['TermLoansFromFis'] + $Liabilities['Debentures'] + $Liabilities['TermDeposits'] + $Liabilities['UnsecuredLoans'] + $Liabilities['BorrowingsFromSubsidiariesAffiliatesQuasiEquity'] + $Liabilities['DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm'] + $Liabilities['OtherTermLiabilities'] + $Liabilities['DeferredTaxLiability'] + $Liabilities['OtherLoanAdvances'];
	return sprintf('%.2f', $TotalOutsideLiabilities);
}
function CalculateTotalShareCapital($Liabilities){
	$TotalShareCapital = $Liabilities['PartnersCapitalProprietorSCapital'] + $Liabilities['ShareCapitalPaidUp'] + $Liabilities['ShareApplicationFinalizedForAllotment'];
	return sprintf('%.2f', $TotalShareCapital);
}
function CalculateReserveSubTotal($Liabilities){
	$ReserveSubTotal = ($Liabilities['StatutoryAndCapitalReserves'] + $Liabilities['GeneralReserve'] + $Liabilities['RevaluationReserve']);
	return sprintf('%.2f', $ReserveSubTotal);
}
function CalculateTotalNetWorth($Liabilities){
	$TotalNetWorth = $Liabilities['PartnersCapitalProprietorSCapital'] + $Liabilities['ShareCapitalPaidUp'] + $Liabilities['ShareApplicationFinalizedForAllotment'] + $Liabilities['StatutoryAndCapitalReserves'] + $Liabilities['GeneralReserve'] +$Liabilities['RevaluationReserve'] + $Liabilities['OtherReservesExcludingProvisions'] + $Liabilities['SurplusOrDeficitInPLAccount'] + $Liabilities['SharePremiumAC'] + $Liabilities['CapitalSubsidy'] + $Liabilities['InvestmentAllowanceUtilizationReserve'] - $Liabilities['RevaluationReserve'];
	return sprintf('%.2f', $TotalNetWorth);
}
function CalculateTotalLiabilities($Liabilities){
	$TotalLiabilities = CalculateTotalOutsideLiabilities($Liabilities) + $Liabilities['PartnersCapitalProprietorSCapital'] + $Liabilities['ShareCapitalPaidUp'] + $Liabilities['ShareApplicationFinalizedForAllotment'] + $Liabilities['StatutoryAndCapitalReserves'] + $Liabilities['GeneralReserve'] + $Liabilities['RevaluationReserve'] + 
$Liabilities['OtherReservesExcludingProvisions'] + $Liabilities['SurplusOrDeficitInPLAccount'] + $Liabilities['SharePremiumAC'] + $Liabilities['CapitalSubsidy'] + $Liabilities['InvestmentAllowanceUtilizationReserve'] - $Liabilities['RevaluationReserve'];
	return sprintf('%.2f', $TotalLiabilities);
}
function CalculateArrearsOfCumulativeDividends($Liabilities){
	return "0";
}
function CalculateDisputedExciseCustomIncomeTaxSalesTaxLiabilities($Liabilities){
	return "0";
}
function CalculateGratuityLiabilityNotProvidedFor($Liabilities){
	return "0";
}
function CalculateGuaranteesIssuedRelatingToBusiness($Liabilities){
	return "0";
}
function CalculateGuaranteesIssuedRelatingToCompanies($Liabilities){
	return "0";
}
function CalculateLCs($Liabilities){
	return "0";
}
function CalculateAllOtherContingentLiabilitiesIncldgBillsPurchasedUnderLC($Liabilities){
	return "0";
}

#====================================================================================#

function CalculateAssetsReceivables($Assets){
	return "0";
}
function CalculateAssetsInventory($Assets){
	return "0";
}
function CalculateAssetsStockInProcess($Assets){
	return "0";
}
function CalculateAssetsFinishedGoods($Assets){
	return "0";
}
function CalculateAssetsSubTotalOtherComsumableSpares($Assets){
	$SubTotalOtherComsumableSpares = $Assets['OtherConsumableSparesIndigenous'] + $Assets['OtherConsumableSparesImported'];
	return sprintf('%.2f', $SubTotalOtherComsumableSpares);
}
function CalculateAssetsSubTotalInventory($Assets){
	return "0";
}
function CalculateAssetsAdvancesToSuplierofRawMaterial($Assets){
	return "0";
}
function CalculateAssetsAdvanceReceivableInOrKind($Assets){
	return "0";
}
function CalculateTotalCurrentAssets($Assets){
	return "0";
}
function CalculateAssetsGrossBlock($Assets){
	return "0";
}
function CalculateAssetsNetBlock($Assets){
	return "0";
}
function CalculateTotalOtherNonCurrentAssets($Assets){
	return "0";
}
function CalculateIntangibleAssetSubtotal($Assets){
	return "0";
}
function CalculateIntangibleAssetTotal($Assets){
	return "0";
}
function CalculateTangibleAssetNetworth($Assets){
	return "0";
}
function CalculateTotalLiabilitiesMinusTotalAssets($Assets){
	return "0";
}
function CalculateMonthsConsumption0($Assets){
	return '0';
}
function CalculateMonthsConsumption1($Assets){
	return '0';
}
function CalculateMonthsConsumption2($Assets){
	return '0';
}
function CalculateMonthsConsumption3($Assets){
	return '0';
}
function CalculateStockInProcessMinusAmount($Assets){
	return '0';
}
function CalculateMonthsCostOfProduction($Assets){
	return '0';
}
function CalculateFinishedGoodsMinusAmount($Assets){
	return '0';
}
function CalculateMonthsCostOfSales($Assets){
	return '0';
}
function CalculateMonthsDomesticIncome($Assets){
	return '0';
}
function CalculateMonthsExportIncome($Assets){
	return '0';
}





#================================================================================================#

function getBalanceSheetAssetsColumns() {
	$fields = array(
		'assetsCurrent_cols' => array(
		   'CashAndBankBalances' =>  'Cash and Bank Balances',
		),
		'aasetsInvestments_cols' => array(
			'GovtOtherSecurities' =>  '(i) Govt. & other securities',
			'FixedDepositsWithBanks' =>  '(ii) Fixed deposits with banks',
			'Others' =>  '(iii) Others',
			'CalculateAssetsReceivables' =>  'RECEIVABLES',
			'ReceivablesOtherThanDeferredExportsInclBillsPurchasedDiscountedByBanks' =>  'RECEIVABLES other than deferred & exports (Incl. bills purchased & discounted by banks)',
			'ExportReceivablesIncludingBillPurchasedAndDiscounted' =>  'Export Receivables (including bill purchased and discounted)',
			'RetentionMoneySecurityDeposit' =>  'Retention Money / Security Deposit',
			'CalculateAssetsInventory' =>  'INVENTORY',
			'RawMaterialIndigenous' =>  'Raw Material - Indigenous',
			'RawMaterialImported' =>  'Raw Material - Imported',
			'CalculateAssetsStockInProcess' =>  'Stock in process',
			'CalculateAssetsFinishedGoods' =>  'Finished Goods',
			'OtherConsumableSparesIndigenous' =>  'Other Consumable spares - Indigenous',
			'OtherConsumableSparesImported' =>  'Other Consumable spares - Imported',
			'CalculateAssetsSubTotalOtherComsumableSpares' =>  'Sub Total: Other Consumable spares',
			'OtherStocks' =>  'Other stocks',
			'CalculateAssetsSubTotalInventory' =>  'Sub Total: Inventory',
			'CalculateAssetsAdvancesToSuplierofRawMaterial' =>  'Advances to suppliers of raw material',
			'AdvancePaymentOfTax' =>  'Advance payment of tax',
			'OtherCurrentAssets' =>  'Other Current Assets:',
			'InterestAccrued' =>  'Interest Accrued',
			'CalculateAssetsAdvanceReceivableInOrKind' =>  'Advance receivable in cash or kind',
			'SundryDeposit' =>  'Sundry Deposit',
			'ModvatCreditReceivable' =>  'Modvat Credit Receivable',
			'OtherCurrentAssets' =>  'Other current assets',
			'CalculateTotalCurrentAssets' =>  'TOTAL CURRENT ASSETS',
		),
		'aasetsFixed_cols' => array(
			'Land' => '(I) Land',
			'Building' => '(ii) Building',
			'Vehicles' => '(iii) Vehicles',
			'PlantMachinery' => '(IV) Plant & Machinery',
			'FurnitureFixtures' => '(v) Furniture & Fixtures',
			'OtherFixedAssets' => '(vi) Other Fixed Assets',
			'CapitalWip' => '(vii) Capital WIP',
			'CalculateAssetsGrossBlock' => 'GROSS BLOCK',
			'LessAccumulatedDepreciation' => 'Less: Accumulated Depreciation',
			'CalculateAssetsNetBlock' => 'NET BLOCK',
		),
		'otherNonCurrentAssets' => array(
			'InvestmentsInSubsidiaryCompaniesAffiliates' => '(I) Investments in Subsidiary companies/ affiliates',
			'OtherInvestmentsInvestmentForAcquisition' => '(ii) Other Investments & Investment for acquisition',
			'DueFromSubsidiaries' => '(iii) Due from subsidiaries',
			'DeferredReceivablesMaturityExceeding1Year' => '(iv) Deferred receivables (maturity exceeding 1 year)',
			'MarginMoneyKeptWithBanks' => '(v) Margin money kept with banks.',
			'DebtorsMoreThan6Months' => '(vi)Debtors more than 6 months',
			'AdvanceAgainstMortgageOfHouseProperty' => '(vii) Advance against mortgage of house property',
			'DeferredRevenueExpenditure' => '(viii) Deferred Revenue Expenditure',
			'OtherNonCurrentAssetsSurplusForFutureExpansionLoansAdvancesNonCurrentInNatureIcdSDuesFromDirectors' => '(ix) Other Non current assets (surplus for Future expansion, Loans & Advances non current in nature, ICD\'s, Dues from Directors)',
			'CalculateTotalOtherNonCurrentAssets' => 'TOTAL OTHER NON CURRENT ASSETS',
		),
		'inTangibleAssets_cols' => array(
			'AccumulatedLossesPreliminaryExpensesMiscellaneousExpenditureNotWOffOtherDeferredRevenueExpenses' => '(i) Accumulated Losses, Preliminary expenses, Miscellaneous expenditure not w/off, Other deferred revenue expenses',
			'DeferredTaxAsset' => '(ii) Deferred Tax Asset',
			'CalculateIntangibleAssetSubtotal' => 'Sub Total',
			'CalculateIntangibleAssetTotal' => 'TOTAL ASSETS',
			'CalculateTangibleAssetNetworth' => 'TANGIBLE NETWORTH',
			'CalculateTotalLiabilitiesMinusTotalAssets' => 'Total Liabilities - Total Assets',
		),
		'buildUpofCurrentAssets_cols' => array(
			'RawMaterialIndigenous' => 'Raw Material - Indigenous AMOUNT',
			'CalculateMonthsConsumption0' => 'MONTH\'S CONSUMPTION',
			'RawMaterialImported' => 'Raw Material - Imported AMOUNT',
			'CalculateMonthsConsumption1' => 'MONTH\'S CONSUMPTION',
			'OtherConsumableSparesIndigenous' => 'Consumable spares indigenous AMOUNT',
			'CalculateMonthsConsumption2' => 'MONTH\'S CONSUMPTION',
			'OtherConsumableSparesImported' => 'Consumable spares- Imported AMOUNT',
			'CalculateMonthsConsumption3' => 'MONTH\'S CONSUMPTION',
			'CalculateStockInProcessMinusAmount' => 'Stock in process - AMOUNT',
			'CalculateMonthsCostOfProduction' => 'MONTH\'S COST OF PRODUCTION',
			'CalculateFinishedGoodsMinusAmount' => 'Finished Goods - AMOUNT',
			'CalculateMonthsCostOfSales' => 'MONTH\'S COST OF SALES',
			'ReceivablesOtherThanDeferredExportsInclBillsPurchasedDiscountedByBanks' => 'RECEIVABLES (DOMESTIC) other than deferred & exports (Incl. bills purchased & discounted by banks) AMOUNT',
			'CalculateMonthsDomesticIncome' => 'MONTH\'S DOMESTIC Income',
			'ExportReceivablesIncludingBillPurchasedAndDiscounted' => 'EXPORT RECV.(Incl. bills purchased & discounted by banks) AMOUNT',
			'CalculateMonthsExportIncome' => 'MONTH\'S EXPORT Income',
		),
	);
	return $fields;
}

function getBalanceSheetLiabilitiesColumns() {
	$fields = array(
		'currentLiabilities_cols' => array(
			'FromApplicantBankCcWcdl' => '(i) from applicant bank (CC / WCDL)',
			'FromOtherBanks' => '(ii) from other banks',
			'OfIAndIiInWhichBillPurchasedDisc' => '(of (i) and (ii) in which Bill purchased & disc.)',
			'CalculateCurrentLiabilitiesSubTotal' => 'SUB TOTAL',
			'SundryCreditorsTrade' => 'Sundry Creditors (Trade)',
			'ShortTermBorrowingsFromAssociatesGroupConcerns' => 'Short term borrowings from Associates & Group Concerns',
			'ShortTermBorrowingsCommercialPaper' => 'Short Term borrowings / Commercial Paper',
			'ShortTermBorrowingsFromOthers' => 'Short term borrowings from Others',
			'AdvancesPaymentsFromCustomersDepositsFromDealers' => 'Advances/ payments from customers/deposits from dealers.',
			'ProvisionForTaxation' => 'Provision for taxation',
			'ProposedDividend' => 'Proposed dividend',
			'OtherStatutoryLiabilitiesDueWithinOneYear' => 'Other Statutory Liabilities( Due within One Year)',
			'InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year' => 'Installments of Term loans / Debentures / DPGs etc. (due within 1 year)',
			'DepositsDueForRepaymentDueWithin1Year' => 'Deposits due for repayment (due within 1 year)',
			'PreferenceSharesRedeemableWithin1Year' => 'Preference Shares redeemable (within 1 year)',
			'CalculateTotalRepaymentDueWithin1Year' => 'TOTAL REPAYMENTS DUE WITHIN 1 YEAR',
			'OtherCurrentLiabilitiesProvisionsDueWithin1Year' => 'Other Current liabilities & provisions (due within 1 year)',
			'InterestAccButNotDue' => 'Interest acc but not due',
			'ProvisionForNpa' => 'Provision for NPA',
			'ProvisionForLeaveEncashmentGratuity' => 'Provision for leave encashment & gratuity',
			'UnclaimedDividend' => 'Unclaimed dividend',
			'OtherLiabilities' => 'Other Liabilities',
			'DueToSubsidiaryCompaniesAffiliates' => 'Due to Subsidiary companies/ affiliates',
			'TaxOnInterimDividendPayable' => 'Tax on Interim Dividend Payable',
		),
		'termLiabilities_cols' => array(
			'Wctl' => 'WCTL',
			'PrefSharesPortionRedeemableAfter1Yr' => 'Pref. Shares (portion redeemable after 1 Yr)',
			'TermLoansExcludingInstallmentsPayableWithinOneYear' => 'Term Loans (Excluding installments payable within one year)',
			'TermLoansFromFis' => 'Term Loans - From Fis',
			'Debentures' => 'Debentures',
			'TermDeposits' => 'Term deposits',
			'UnsecuredLoans' => 'Unsecured loans',
			'BorrowingsFromSubsidiariesAffiliatesQuasiEquity' => 'Borrowings from subsidiaries / affiliates (Quasi Equity)',
			'DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm' => 'Deposit from Dealers (only if considered as available for long term)',
			'OtherTermLiabilities' => 'Other term liabilities',
			'DeferredTaxLiability' => 'Deferred Tax Liability',
			'OtherLoanAdvances' => 'Other Loan & Advances',
			'CalculateTotalTermLiabilities' => 'TOTAL TERM LIABILITIES',
			'CalculateTotalOutsideLiabilities' => 'TOTAL OUTSIDE LIABILITIES (TOL)',
		),
		'networthLiabilities_cols' => array(
			'PartnersCapitalProprietorSCapital' => 'Partners capital / Proprietor\'s capital',
			'ShareCapitalPaidUp' => 'Share Capital (Paid-up)',
			'ShareApplicationFinalizedForAllotment' => 'Share Application (finalized for allotment)',
			'CalculateTotalShareCapital' => 'Total Share Capital',
		),
		'reserveLiabilities_cols' => array(
			'StatutoryAndCapitalReserves' => 'Statutory and Capital Reserves',
			'GeneralReserve' => 'General Reserve',
			'RevaluationReserve' => 'Revaluation Reserve',
			'CalculateReserveSubTotal' => 'Sub Total',
			'OtherReservesExcludingProvisions' => 'Other Reserves ( Excluding provisions)',
			'SurplusOrDeficitInPLAccount' => 'Surplus (+) or deficit (-) in P & L Account',
			'SharePremiumAC' => 'Share Premium A/c',
			'CapitalSubsidy' => 'Capital Subsidy',
			'InvestmentAllowanceUtilizationReserve' => 'Investment Allowance Utilization Reserve',
			'CalculateTotalNetWorth' => 'TOTAL NET WORTH',
			'CalculateTotalLiabilities' => 'TOTAL LIABILITIES',
		),
		'contingentLiabilities_cols' => array(
			'CalculateArrearsOfCumulativeDividends' =>  'Arrears of cumulative dividends',
			'CalculateDisputedExciseCustomIncomeTaxSalesTaxLiabilities' =>  'Disputed excise / customs / Income tax / Sales tax Liabilities',
			'CalculateGratuityLiabilityNotProvidedFor' =>  'Gratuity Liability not provided for',
			'CalculateGuaranteesIssuedRelatingToBusiness' =>  'Guarantees issued (relating to business)',
			'CalculateGuaranteesIssuedRelatingToCompanies' =>  'Guarantees issued (for group companies)',
			'CalculateLCs' =>  'LCs',
			'CalculateAllOtherContingentLiabilitiesIncldgBillsPurchasedUnderLC' =>  'All other contingent liabilities -(incldg. Bills purchased - Under LC)',
		),
	);
	return $fields;
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
	$response['PbditInterestPer'] = $response['Interest'] == 0 ? 0 ($response['PBDITOperatingProfit']/$response['Interest']);
	$response['NetProfit'] = $response['TotalOperatingIncome']-($AddOpeningStockInProcessRawMaterials+$OtherSpares+$PowerFuel+$DirectLabour+$OtherManufacturingExpenses+$Depreciation+$RepairsMaintenance+$CostOfTradingGoods+0+$AddOpeningStockInProcess-$DeductClosingStockInProcess+$AddOpeningStockOfFinishedGoods-$DeductClosingStockOfFinishedGoods+$SellingGeneralAdmExpenses)-$response['Interest']+$response['TotalNonOperatingIncome']+0-($LossOnSaleOfInvestments+$LossOnSaleOfFa+$DerivativeLossesBooked+$NetLossOnForeignCurrencyTranslationAndTransactionsLossDueToFire + $PreliExpOneTimeExpensesWrittenOff+$MiscExpWrittenOff+$ProvForDoubDebtsDimInTheValOfInv+$WealthTax)-$TaxPaid-($ProvisionForTaxesCurrentPeriod+$ProvisionForTaxesDefferedTaxes);
	$response['CashProfit'] = ($TotalOperatingIncome -($AddOpeningStockInProcessRawMaterials+$OtherSpares+$PowerFuel + $DirectLabour + $OtherManufacturingExpenses+ $Depreciation+ $RepairsMaintenance + $CostOfTradingGoods + $AddOpeningStockInProcess - $DeductClosingStockInProcess + $AddOpeningStockOfFinishedGoods - $DeductClosingStockOfFinishedGoods + $SellingGeneralAdmExpenses)) - ($InterestPaymentToBanksSum + $InterestPaymentToFIsSum + $BankCharges) + ( $InterestOnDepositsDividendReceived + $ForexGains + $NonOperatingIncomeFromSubsidiaries + $TaxRefund + $MiscIncome + $ProfitOnSaleOfAssetsInvestments + $OtherIncome + $ProvisionsExpensesWrittenBack) - ($LossOnSaleOfInvestments+$LossOnSaleOfFa+$DerivativeLossesBooked+$NetLossOnForeignCurrencyTranslationAndTransactionsLossDueToFire+$PreliExpOneTimeExpensesWrittenOff+$MiscExpWrittenOff +$ProvForDoubDebtsDimInTheValOfInv +$WealthTax) - $TaxPaid - ($ProvisionForTaxesCurrentPeriod + $ProvisionForTaxesDefferedTaxes) +$Depreciation - $ProvisionsExpensesWrittenBack;
	$response['TangibleNetWorth'] = ($PartnersCapitalProprietorSCapital+ $ShareCapitalPaidUp + $ShareApplicationFinalizedForAllotment+$StatutoryAndCapitalReserves+ $GeneralReserve+ $RevaluationReserve+ $OtherReservesExcludingProvisions + $SurplusOrDeficitInPLAccount+ $SharePremiumAC + $CapitalSubsidy + $InvestmentAllowanceUtilizationReserve -$RevaluationReserve) - ($AccumulatedLossesPreliminaryExpensesMiscellaneousExpenditureNotWOffOtherDeferredRevenueExpenses + $DeferredTaxAsset);
	$response['TolTnw'] = $response['TangibleNetWorth'] == 0 ? 0 :(($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $SundryCreditorsTrade + $ShortTermBorrowingsFromAssociatesGroupConcerns + $ShortTermBorrowingsCommercialPaper + $ShortTermBorrowingsFromOthers + $AdvancesPaymentsFromCustomersDepositsFromDealers + $ProvisionForTaxation + $ProposedDividend + $OtherStatutoryLiabilitiesDueWithinOneYear + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $DepositsDueForRepaymentDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $OtherCurrentLiabilitiesProvisionsDueWithin1Year + $InterestAccButNotDue + $ProvisionForNpa + $ProvisionForLeaveEncashmentGratuity + $UnclaimedDividend + $OtherLiabilities + $DueToSubsidiaryCompaniesAffiliates + $TaxOnInterimDividendPayable + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)/ $response['TangibleNetWorth']);
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