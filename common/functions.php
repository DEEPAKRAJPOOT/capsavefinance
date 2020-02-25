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

function format_number($number) {
    $num = number_format($number,6, '.', '');
    return (strpos($num,'.')!==false ? preg_replace("/\.?0*$/",'',$num) : $num);
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

function getPaginate($pages, $currpage = 1, $title = array()) {
		$output = '';
		if($pages > 1) {
			if($currpage == 1) 
				$output .= '<li class="paginate_button disabled"><span>First</span></li><li class="paginate_button disabled"><span>Previous</span></li>';
			else	
				$output .= '<li class="paginate_button" onclick="getresult(1)" title="'.($title[0] ?? '').'"><span>First</span></li><li class="paginate_button"  onclick="getresult('.($currpage-1).')"  title="'.($title[$currpage-2] ?? '').'"><span>Previous</span></li>';

			if(($currpage-3)>0) {
				if($currpage == 1)
					$output .= '<li class="paginate_button active" title="'.($title[0] ?? '').'"><span>1</span></li>';
				else				
					$output .= '<li class="paginate_button" onclick="getresult(1)" title="'.($title[0] ?? '').'"><span>1</span></li>';
			}
			if(($currpage-3)>1) {
					$output .= '<li class="paginate_button"><span>....</span></li>';
			}
			
			for($i=($currpage-2); $i<=($currpage+2); $i++)	{
				if($i<1) continue;
				if($i>$pages) break;
				if($currpage == $i)
					$output .= '<li class="paginate_button active" id="'.$i.'" title="'.($title[$i-1] ?? '').'"><span>'.$i.'</span></li>';
				else
					$output .= '<li class="paginate_button" onclick="getresult('.$i.')" title="'.($title[$i-1] ?? '').'"><span>'.$i.'</span></li>';
			}
			
			if(($pages-($currpage+2))>1) {
				$output .= '<li class="paginate_button"><span>....</span></li>';
			}
			if(($pages-($currpage+2))>0) {
				if($currpage == $pages)
					$output .= '<li class="paginate_button active" id="'.$pages.'" title="'.($title[$pages-1] ?? '').'"><span>'.$pages.'</span></li>';
				else				
					$output .= '<li class="paginate_button" onclick="getresult('.$pages.')" title="'.($title[$pages-1] ?? '').'"><span>'.$pages.'</span></li>';
			}
			
			if($currpage < $pages)
				$output .= '<li class="paginate_button" onclick="getresult('.($currpage+1).')" title="'.($title[$currpage] ?? '').'"><span>Next</span></li><li class="paginate_button"  onclick="getresult('.$pages.')" title="'.($title[$pages-1] ?? '').'"><span>Last</span></li>';
			else				
				$output .= '<li class="paginate_button disabled"><span>Next</span></li><li class="paginate_button disabled"><span>Last</span></li>';
		}
		return !empty($output) ? '<ul class="pagination_ul">'.$output.'</ul>' : $output;
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
	$netSalesGrowth = $prev_year_data['TotalOperatingIncome'] == 0 ? 0 : ((($curr_year_data['TotalOperatingIncome'] - $prev_year_data['TotalOperatingIncome']) /$prev_year_data['TotalOperatingIncome'])*100);
	$netIncomeGrowth = $prev_year_data['TotalOperatingIncome'] == 0 ? 0 : ((($curr_year_data['TotalOperatingIncome'] - $prev_year_data['TotalOperatingIncome']) /$prev_year_data['TotalOperatingIncome'])*100);
	$netProfitGrowth = $prev_year_data['NetProfit'] == 0 ? 0 : ((($curr_year_data['NetProfit'] - $prev_year_data['NetProfit']) /$prev_year_data['NetProfit'])*100);
	$tangibleNetWorthGrowth = $prev_year_data['TangibleNetWorth'] == 0 ? 0 : ((($curr_year_data['TangibleNetWorth'] - $prev_year_data['TangibleNetWorth']) /$prev_year_data['TangibleNetWorth'])*100);
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
	$COPSofGrossIncome =  CalculateGrossSale($ProfitAndLoss) == 0 ? 0 : ((CalculateCostofProduction($ProfitAndLoss)/ CalculateGrossSale($ProfitAndLoss)))  * 100;
	return sprintf('%.2f', $COPSofGrossIncome);
}
function CalculateCostofSales($ProfitAndLoss) {
	$CostofSales = CalculateCostofProduction($ProfitAndLoss) + $ProfitAndLoss['AddOpeningStockOfFinishedGoods'] - 
	$ProfitAndLoss['DeductClosingStockOfFinishedGoods'];
	return sprintf('%.2f', $CostofSales);
}
function CalculateCostofSalesasPerGrossIncome($ProfitAndLoss) {
	$CostofSalesasPerGrossIncome = CalculateGrossSale($ProfitAndLoss) == 0 ? 0 : ((CalculateCostofSales($ProfitAndLoss) / CalculateGrossSale($ProfitAndLoss))) * 100;
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
	$PBITasPerGrossSale = CalculateGrossSale($ProfitAndLoss) == 0 ? 0 : ((CalculateProfitBeforeInterestTax($ProfitAndLoss) / CalculateGrossSale($ProfitAndLoss))) * 100;
	return sprintf('%.2f', $PBITasPerGrossSale);
}
function CalculateInterestOtherFinanceCharge($ProfitAndLoss) {
	$InterestPaymentToBanks = array_sum($ProfitAndLoss['InterestPaymentToBanks']);
	$InterestPaymentToFIs = array_sum($ProfitAndLoss['InterestPaymentToFIs']);
	$InterestOtherFinanceCharge = $InterestPaymentToBanks + $InterestPaymentToFIs + $ProfitAndLoss['BankCharges'];
	return sprintf('%.2f', $InterestOtherFinanceCharge);
}
function CalculateInttFinChargeasPerGrossSale($ProfitAndLoss) {
	$InttFinChargeasPerGrossSale = CalculateGrossSale($ProfitAndLoss) == 0 ? 0 : ((CalculateInterestOtherFinanceCharge($ProfitAndLoss) / CalculateGrossSale($ProfitAndLoss))) * 100;
	return sprintf('%.2f', $InttFinChargeasPerGrossSale);
}
function CalculateOperatingProfitBeforeTax($ProfitAndLoss) {
	$OperatingProfitBeforeTax = CalculateProfitBeforeInterestTax($ProfitAndLoss)-CalculateInterestOtherFinanceCharge($ProfitAndLoss);
	return sprintf('%.2f', $OperatingProfitBeforeTax);
}
function CalculateOPBTasPerGrossIncome($ProfitAndLoss) {
	$OPBTasPerGrossIncome = CalculateGrossSale($ProfitAndLoss) == 0 ? 0 : ((CalculateOperatingProfitBeforeTax($ProfitAndLoss)/CalculateGrossSale($ProfitAndLoss))) * 100;
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
	$NetofNonOperatingIncomeExpenses = CalculateTotalNonOperatingIncome($ProfitAndLoss) - CalculateTotalNonOperatingExpenses($ProfitAndLoss);
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
function CalculateProvisionForTaxesTotal($ProfitAndLoss) {
	$ProvisionForTaxesTotal = $ProfitAndLoss['ProvisionForTaxesCurrentPeriod'] + $ProfitAndLoss['ProvisionForTaxesDefferedTaxes'];
	return sprintf('%.2f', $ProvisionForTaxesTotal);
}
function CalculateNetProfitLoss($ProfitAndLoss) {
	$NetProfitLoss = CalculateProfitBeforeTaxLoss($ProfitAndLoss)- $ProfitAndLoss['TaxPaid'] - ($ProfitAndLoss['ProvisionForTaxesCurrentPeriod'] + $ProfitAndLoss['ProvisionForTaxesDefferedTaxes']);
	return sprintf('%.2f', $NetProfitLoss);
}
function CalculatePATasPerGrossIncome($ProfitAndLoss) {
	$PATasPerGrossIncome = CalculateGrossSale($ProfitAndLoss) == 0 ? 0 : ((CalculateNetProfitLoss($ProfitAndLoss)/CalculateGrossSale($ProfitAndLoss))) * 100;
	return sprintf('%.2f', $PATasPerGrossIncome);
}
function CalculateTotalExtraordinaryItems($ProfitAndLoss) {
	$TotalExtraordinaryItems = $ProfitAndLoss['ExtraordinaryIncomeAdjustments']-$ProfitAndLoss['ExtraordinaryExpensesAdjustments'];
	return sprintf('%.2f', $TotalExtraordinaryItems);
}
function CalculateAdjustedPAT($ProfitAndLoss) {
	$AdjustedPAT = CalculateNetProfitLoss($ProfitAndLoss) - ($ProfitAndLoss['ExtraordinaryIncomeAdjustments']-$ProfitAndLoss['ExtraordinaryExpensesAdjustments']);
	return sprintf('%.2f', $AdjustedPAT);
}
function CalculateRetainedProfit($ProfitAndLoss) {
	$RetainedProfit = CalculateAdjustedPAT($ProfitAndLoss)- $ProfitAndLoss['EquityDividendPaidAmount'] - $ProfitAndLoss['DividendTax'] - $ProfitAndLoss['PartnersWithdrawal'] - $ProfitAndLoss['DividendPreference'];
	return sprintf('%.2f', $RetainedProfit);
}

#====================================================================================#
function CalculateCurrentLiabilitiesBankSubTotal($Liabilities){
	$SubTotal = $Liabilities['FromApplicantBankCcWcdl'] + $Liabilities['FromOtherBanks'] + $Liabilities['OfIAndIiInWhichBillPurchasedDisc'];
	return sprintf('%.2f', $SubTotal);
}
function CalculateCurrentLiabilitiesSubTotal($Liabilities){
	$SubTotal = $Liabilities['SundryCreditorsTrade'] + $Liabilities['ShortTermBorrowingsFromAssociatesGroupConcerns'] + $Liabilities['ShortTermBorrowingsCommercialPaper'] + $Liabilities['ShortTermBorrowingsFromOthers'] + $Liabilities['AdvancesPaymentsFromCustomersDepositsFromDealers'] + $Liabilities['ProvisionForTaxation'] + $Liabilities['ProposedDividend'] + $Liabilities['OtherStatutoryLiabilitiesDueWithinOneYear'] + $Liabilities['InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year'] + $Liabilities['DepositsDueForRepaymentDueWithin1Year'] + $Liabilities['PreferenceSharesRedeemableWithin1Year'] + $Liabilities['OtherCurrentLiabilitiesProvisionsDueWithin1Year'] + $Liabilities['InterestAccButNotDue'] + $Liabilities['ProvisionForNpa'] + $Liabilities['ProvisionForLeaveEncashmentGratuity'] + $Liabilities['UnclaimedDividend'] + $Liabilities['OtherLiabilities'] + $Liabilities['DueToSubsidiaryCompaniesAffiliates'] + $Liabilities['TaxOnInterimDividendPayable'];
	return sprintf('%.2f', $SubTotal);
}
function CalculateTotalCurrentLiabilities($Liabilities){
	$TotalCurrentLiabilities = CalculateCurrentLiabilitiesSubTotal($Liabilities) + CalculateCurrentLiabilitiesBankSubTotal($Liabilities);
	return sprintf('%.2f', $TotalCurrentLiabilities);
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
#====================================================================================#

function CalculateAssetsReceivables($Assets){
	return 'Need to Calculate';
}
function CalculateAssetsInventory($Assets){
	return 'Need to Calculate';
}
function CalculateAssetsSubTotalOtherComsumableSpares($Assets){
	$SubTotalOtherComsumableSpares = $Assets['OtherConsumableSparesIndigenous'] + $Assets['OtherConsumableSparesImported'];
	return sprintf('%.2f', $SubTotalOtherComsumableSpares);
}
function CalculateSubTotalInventory($Assets){
	$SubTotalInventory = $Assets['RawMaterialIndigenous'] + $Assets['RawMaterialImported'] + $Assets['StockInProcess'] + $Assets['FinishedGoods'] + CalculateAssetsSubTotalOtherComsumableSpares($Assets) + $Assets['OtherStocks'];
	return sprintf('%.2f', $SubTotalInventory);
}
function CalculateIntangibleAssetSubtotal($Assets, $Liabilities=array()){
	$IntangibleAssetSubtotal = $Assets['AccumulatedLossesPreliminaryExpensesMiscellaneousExpenditureNotWOffOtherDeferredRevenueExpenses'] + $Assets['DeferredTaxAsset'];
	return sprintf('%.2f', $IntangibleAssetSubtotal);
}
function CalculateTotalAssets($Assets, $Liabilities=array()){
	$IntangibleTotalAssets = CalculateTotalCurrentAssets($Assets) + CalculateNetBlock($Assets, $Liabilities) + CalculateTotalOtherNonCurrentAssets($Assets) + CalculateIntangibleAssetSubtotal($Assets);
	return sprintf('%.2f', $IntangibleTotalAssets);
}
function CalculateTangibleNetworth($Assets, $Liabilities=array()){
	$TangibleNetworth = CalculateTotalNetWorth($Liabilities) - $Liabilities['RevaluationReserve'] -CalculateIntangibleAssetSubtotal($Assets);
	return sprintf('%.2f', $TangibleNetworth);
}
function CalculateTotalLiabilitiesMinusTotalAssets($Assets, $Liabilities=array()){
	$TotalLiabilitiesMinusTotalAssets = CalculateTotalLiabilities($Liabilities) - CalculateTotalAssets($Assets, $Liabilities);
	return sprintf('%.2f', $TotalLiabilitiesMinusTotalAssets);
}
function CalculateTotalOtherNonCurrentAssets($Assets){
	$TotalOtherNonCurrentAssets = $Assets['InvestmentsInSubsidiaryCompaniesAffiliates'] + $Assets['OtherInvestmentsInvestmentForAcquisition'] + $Assets['DueFromSubsidiaries'] + $Assets['DeferredReceivablesMaturityExceeding1Year'] + $Assets['MarginMoneyKeptWithBanks'] + $Assets['DebtorsMoreThan6Months'] + $Assets['AdvanceAgainstMortgageOfHouseProperty'] + $Assets['DeferredRevenueExpenditure'] + $Assets['OtherNonCurrentAssetsSurplusForFutureExpansionLoansAdvancesNonCurrentInNatureIcdSDuesFromDirectors'] + $Assets['Deposits'];
	return sprintf('%.2f', $TotalOtherNonCurrentAssets);
}
function CalculateGrossBlock($Assets, $Liabilities=array()){
	$GrossBlock = $Assets['Land'] +
						$Assets['Building'] +
						$Assets['Vehicles'] +
						$Assets['PlantMachinery'] +
						$Assets['FurnitureFixtures'] +
						$Assets['OtherFixedAssets'] +
						$Assets['CapitalWip'];
	return sprintf('%.2f', $GrossBlock);
}
function CalculateNetBlock($Assets, $Liabilities=array()){
	$NetBlock = CalculateGrossBlock($Assets) - $Assets['LessAccumulatedDepreciation'] - $Liabilities['RevaluationReserve'];
	return sprintf('%.2f', $NetBlock);
}
function CalculateTotalCurrentAssets($Assets){
	$TotalCurrentAssets = $Assets['GovtOtherSecurities'] + 
				$Assets['CashAndBankBalances'] + 
				$Assets['FixedDepositsWithBanks'] + 
				$Assets['Others'] + 
				$Assets['ReceivablesOtherThanDeferredExportsInclBillsPurchasedDiscountedByBanks'] + 
				$Assets['ExportReceivablesIncludingBillPurchasedAndDiscounted'] + 
				$Assets['RetentionMoneySecurityDeposit'] + 
				$Assets['RawMaterialIndigenous'] + 
				$Assets['RawMaterialImported'] + 
				$Assets['StockInProcess'] + 
				$Assets['FinishedGoods'] + 
				$Assets['OtherConsumableSparesIndigenous'] + 
				$Assets['OtherConsumableSparesImported'] + 
				CalculateAssetsSubTotalOtherComsumableSpares($Assets) + 
				$Assets['OtherStocks'] + 
				$Assets['AdvancesToSuppliersOfRawMaterial'] + 
				$Assets['AdvancePaymentOfTax'] + 
				$Assets['InterestAccrued'] + 
				$Assets['AdvanceReceivableInCashOrKind'] + 
				$Assets['SundryDeposit'] + 
				$Assets['ModvatCreditReceivable'] + 
				$Assets['OtherCurrentAssets'];
	return sprintf('%.2f', $TotalCurrentAssets);
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
			//'CalculateAssetsReceivables' =>  'RECEIVABLES',
			'ReceivablesOtherThanDeferredExportsInclBillsPurchasedDiscountedByBanks' =>  'RECEIVABLES other than deferred & exports (Incl. bills purchased & discounted by banks)',
			'ExportReceivablesIncludingBillPurchasedAndDiscounted' =>  'Export Receivables (including bill purchased and discounted)',
			'RetentionMoneySecurityDeposit' =>  'Retention Money / Security Deposit',
			//'CalculateAssetsInventory' =>  'INVENTORY',
			'RawMaterialIndigenous' =>  'Raw Material - Indigenous',
			'RawMaterialImported' =>  'Raw Material - Imported',
			'StockInProcess' =>  'Stock in process',
			'FinishedGoods' =>  'Finished Goods',
			'OtherConsumableSparesIndigenous' =>  'Other Consumable spares - Indigenous',
			'OtherConsumableSparesImported' =>  'Other Consumable spares - Imported',
			'CalculateAssetsSubTotalOtherComsumableSpares' =>  'Sub Total: Other Consumable spares',
			'OtherStocks' =>  'Other stocks',
			'CalculateSubTotalInventory' =>  'Sub Total: Inventory',
			'AdvancesToSuppliersOfRawMaterial' =>  'Advances to suppliers of raw material',
			'AdvancePaymentOfTax' =>  'Advance payment of tax',
			'InterestAccrued' =>  'Interest Accrued',
			'AdvanceReceivableInCashOrKind' =>  'Advance receivable in cash or kind',
			'SundryDeposit' =>  'Sundry Deposit',
			'ModvatCreditReceivable' =>  'Modvat Credit Receivable',
			'OtherCurrentAssets' =>  'Other Current Assets:',
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
			'CalculateGrossBlock' => 'GROSS BLOCK',
			'LessAccumulatedDepreciation' => 'Less: Accumulated Depreciation',
			'CalculateNetBlock' => 'NET BLOCK',
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
			'Deposits' => '(x) Deposits',
			'CalculateTotalOtherNonCurrentAssets' => 'TOTAL OTHER NON CURRENT ASSETS',
		),
		'inTangibleAssets_cols' => array(
			'AccumulatedLossesPreliminaryExpensesMiscellaneousExpenditureNotWOffOtherDeferredRevenueExpenses' => '(i) Accumulated Losses, Preliminary expenses, Miscellaneous expenditure not w/off, Other deferred revenue expenses',
			'DeferredTaxAsset' => '(ii) Deferred Tax Asset',
			'CalculateIntangibleAssetSubtotal' => 'Sub Total',
			'CalculateTotalAssets' => 'TOTAL ASSETS',
			'CalculateTangibleNetworth' => 'TANGIBLE NETWORTH',
			'CalculateTotalLiabilitiesMinusTotalAssets' => 'Total Liabilities - Total Assets',
		),
		'buildUpofCurrentAssets_cols' => array(
			'RawMaterialIndigenousAmount' => 'Raw Material - Indigenous AMOUNT',
			'MonthSConsumptionIndigenous' => 'MONTH\'S CONSUMPTION',
			'RawMaterialImportedAmount' => 'Raw Material - Imported AMOUNT',
			'MonthSConsumptionImported' => 'MONTH\'S CONSUMPTION',
			'ConsumableSparesIndigenousAmount' => 'Consumable spares indigenous AMOUNT',
			'MonthSConsumptionConsumableSparesIndigenous' => 'MONTH\'S CONSUMPTION',
			'ConsumableSparesImportedAmount' => 'Consumable spares- Imported AMOUNT',
			'MonthSConsumptionConsumableSparesImported' => 'MONTH\'S CONSUMPTION',
			'StockInProcessAmount' => 'Stock in process - AMOUNT',
			'MonthSCostOfProduction' => 'MONTH\'S COST OF PRODUCTION',
			'FinishedGoodsAmount' => 'Finished Goods - AMOUNT',
			'MonthSCostOfSales' => 'MONTH\'S COST OF SALES',
			'ReceivablesDomesticOtherThanDeferredExportsInclBillsPurchasedDiscountedByBanksAmount' => 'RECEIVABLES (DOMESTIC) other than deferred & exports (Incl. bills purchased & discounted by banks) AMOUNT',
			'MonthSDomesticIncome' => 'MONTH\'S DOMESTIC Income',
			'ExportRecvInclBillsPurchasedDiscountedByBanksAmount' => 'EXPORT RECV.(Incl. bills purchased & discounted by banks) AMOUNT',
			'MonthSExportIncome' => 'MONTH\'S EXPORT Income',
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
			'CalculateCurrentLiabilitiesBankSubTotal' => 'SUB TOTAL',
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
			'CalculateCurrentLiabilitiesSubTotal' => 'Sub Total',
			'CalculateTotalCurrentLiabilities' => 'Total current Liabilities',
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
			'ArrearsOfCumulativeDividends' =>  'Arrears of cumulative dividends',
			'DisputedExciseCustomsIncomeTaxSalesTaxLiabilities' =>  'Disputed excise / customs / Income tax / Sales tax Liabilities',
			'GratuityLiabilityNotProvidedFor' =>  'Gratuity Liability not provided for',
			'GuaranteesIssuedRelatingToBusiness' =>  'Guarantees issued (relating to business)',
			'GuaranteesIssuedForGroupCompanies' =>  'Guarantees issued (for group companies)',
			'Lcs' =>  'LCs',
			'AllOtherContingentLiabilitiesIncldgBillsPurchasedUnderLc' =>  'All other contingent liabilities -(incldg. Bills purchased - Under LC)',
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
			'AdjustedTangibleNetWorth' => 'Adjusted Tangible Net Worth',
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
			'ProvisionForTaxesDefferedTaxes' => 'Deffered Taxes',
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

function extraData($wholeArary) {
	$AdjustedTangibleNetWorth = [];
	$CashProfit = [];
	$DSCR = [];
	$DebtEBIDTA = [];
	$TangibleNetWorth = [];

	foreach ($wholeArary as $year => $data) {
		$response = getTotalFinanceData($data);
		$CashProfit[] = $year .':' . sprintf('%.2f', $response['CashProfit']);
		$DSCR[] = $year .':' . sprintf('%.2f', $response['DSCR']);
		$DebtPbdit[] = $year .':' . sprintf('%.2f', $response['DebtPbdit']);
		$TangibleNetWorth[] = $year .':' . sprintf('%.2f', $response['TangibleNetWorth']);
		$AdjustedTangibleNetWorth[] = $year .':' . sprintf('%.2f', $response['AdjustedTangibleNetWorth']);
	}
	$extraData = array(
		'AdjustedTangibleNetWorth' => !empty($AdjustedTangibleNetWorth) ? implode('||', $AdjustedTangibleNetWorth) : 0,
		'CashProfit' => !empty($CashProfit) ? implode('||', $CashProfit) : 0,
		'DSCR' => !empty($DSCR) ? implode('||', $DSCR) : 0,
		'DebtEBIDTA' => !empty($DebtPbdit) ? implode('||', $DebtPbdit) : 0,
		'TangibleNetWorth' => !empty($TangibleNetWorth) ? implode('||', $TangibleNetWorth) : 0,
	);
	return $extraData;
}


function getTotalFinanceData($fullArray, $prevFullArray = []){
	$ProfitAndLoss = $fullArray['ProfitAndLoss'];
	$Liabilities = $fullArray['BalanceSheet']['Liabilities'];
	$Assets = $fullArray['BalanceSheet']['Assets'];


	$PrevProfitAndLoss = $prevFullArray['ProfitAndLoss'] ?? [];
	$PrevLiabilities = $prevFullArray['BalanceSheet']['Liabilities'] ?? [];
	$PrevAssets = $prevFullArray['BalanceSheet']['Assets'] ?? [];

	extract($ProfitAndLoss);	
	$response = [];
	$AddOpeningStockInProcessRawMaterials = $RawMaterials['Imported'] + $RawMaterials['Indigenous'];
	$OtherSpares = $OtherSpares['Imported'] + $OtherSpares['Indigenous'] ;
	$InterestPaymentToBanksSum = $InterestPaymentToBanks['InterestWc'] + $InterestPaymentToBanks['InterestTermLoans'];
	$InterestPaymentToFIsSum = $InterestPaymentToFIs['InterestWc'] + $InterestPaymentToFIs['InterestTermLoans'];

	$response['TotalOperatingIncome'] =  $GrossDomesticSales + $ExportSales - $LessExciseDuty+ $AddTradingOtherOperatingIncome+ $ExportIncentives+ $DutyDrawback+ $Others;
	$response['TotalNonOperatingIncome'] = $InterestOnDepositsDividendReceived + $ForexGains + $NonOperatingIncomeFromSubsidiaries + $TaxRefund + $MiscIncome + $ProfitOnSaleOfAssetsInvestments + $OtherIncome + $ProvisionsExpensesWrittenBack;
	$response['PBDITOperatingProfit'] = $response['TotalOperatingIncome'] -($AddOpeningStockInProcessRawMaterials + $OtherSpares + $PowerFuel + $DirectLabour + $OtherManufacturingExpenses+ $Depreciation+ $RepairsMaintenance + $CostOfTradingGoods + $AddOpeningStockInProcess - $DeductClosingStockInProcess + $AddOpeningStockOfFinishedGoods - $DeductClosingStockOfFinishedGoods + $SellingGeneralAdmExpenses ) + $Depreciation;
	$response['Depreciation'] = $Depreciation;
	extract($Liabilities);
	extract($Assets);
	$curr_netblock = $NetBlock ?? 0;
	$prev_netblock = $PrevAssets['NetBlock'] ?? 0;
	$Prev_TotalRepaymentsDueWithin1Year = $PrevLiabilities['TotalRepaymentsDueWithin1Year'] ?? 0;
	$netBlock = $curr_netblock + $prev_netblock;
	$a = ($netBlock/2);
	$response['DeprecationAverageNetFixedAssetsPer'] = ($a == 0) ? 0 : (($Depreciation / $a)  * 100);
	$response['Interest'] = $InterestPaymentToBanksSum+$InterestPaymentToFIsSum+$BankCharges;
	$response['InterestNetSalesPer'] = $response['TotalOperatingIncome'] == 0 ? 0 : (($response['Interest']/$response['TotalOperatingIncome'])*100); 
	$response['PbditInterestPer'] = $response['Interest'] == 0 ? 0 : ($response['PBDITOperatingProfit']/$response['Interest']);
	$response['NetProfit'] = $response['TotalOperatingIncome']-($AddOpeningStockInProcessRawMaterials+$OtherSpares+$PowerFuel+$DirectLabour+$OtherManufacturingExpenses+$Depreciation+$RepairsMaintenance+$CostOfTradingGoods+0+$AddOpeningStockInProcess-$DeductClosingStockInProcess+$AddOpeningStockOfFinishedGoods-$DeductClosingStockOfFinishedGoods+$SellingGeneralAdmExpenses)-$response['Interest']+$response['TotalNonOperatingIncome']+0-($LossOnSaleOfInvestments+$LossOnSaleOfFa+$DerivativeLossesBooked+$NetLossOnForeignCurrencyTranslationAndTransactionsLossDueToFire + $PreliExpOneTimeExpensesWrittenOff+$MiscExpWrittenOff+$ProvForDoubDebtsDimInTheValOfInv+$WealthTax)-$TaxPaid-($ProvisionForTaxesCurrentPeriod+$ProvisionForTaxesDefferedTaxes);
	$response['CashProfit'] = ($response['TotalOperatingIncome'] -($AddOpeningStockInProcessRawMaterials+$OtherSpares+$PowerFuel + $DirectLabour + $OtherManufacturingExpenses+ $Depreciation+ $RepairsMaintenance + $CostOfTradingGoods + $AddOpeningStockInProcess - $DeductClosingStockInProcess + $AddOpeningStockOfFinishedGoods - $DeductClosingStockOfFinishedGoods + $SellingGeneralAdmExpenses)) - ($InterestPaymentToBanksSum + $InterestPaymentToFIsSum + $BankCharges) + ( $InterestOnDepositsDividendReceived + $ForexGains + $NonOperatingIncomeFromSubsidiaries + $TaxRefund + $MiscIncome + $ProfitOnSaleOfAssetsInvestments + $OtherIncome + $ProvisionsExpensesWrittenBack) - ($LossOnSaleOfInvestments+$LossOnSaleOfFa+$DerivativeLossesBooked+$NetLossOnForeignCurrencyTranslationAndTransactionsLossDueToFire+$PreliExpOneTimeExpensesWrittenOff+$MiscExpWrittenOff +$ProvForDoubDebtsDimInTheValOfInv +$WealthTax) - $TaxPaid - ($ProvisionForTaxesCurrentPeriod + $ProvisionForTaxesDefferedTaxes) +$Depreciation - $ProvisionsExpensesWrittenBack;
	$response['TangibleNetWorth'] = ($PartnersCapitalProprietorSCapital+ $ShareCapitalPaidUp + $ShareApplicationFinalizedForAllotment+$StatutoryAndCapitalReserves+ $GeneralReserve+ $RevaluationReserve+ $OtherReservesExcludingProvisions + $SurplusOrDeficitInPLAccount+ $SharePremiumAC + $CapitalSubsidy + $InvestmentAllowanceUtilizationReserve -$RevaluationReserve) - ($AccumulatedLossesPreliminaryExpensesMiscellaneousExpenditureNotWOffOtherDeferredRevenueExpenses + $DeferredTaxAsset);
	$response['TolTnw'] = $response['TangibleNetWorth'] == 0 ? 0 :(($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $SundryCreditorsTrade + $ShortTermBorrowingsFromAssociatesGroupConcerns + $ShortTermBorrowingsCommercialPaper + $ShortTermBorrowingsFromOthers + $AdvancesPaymentsFromCustomersDepositsFromDealers + $ProvisionForTaxation + $ProposedDividend + $OtherStatutoryLiabilitiesDueWithinOneYear + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $DepositsDueForRepaymentDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $OtherCurrentLiabilitiesProvisionsDueWithin1Year + $InterestAccButNotDue + $ProvisionForNpa + $ProvisionForLeaveEncashmentGratuity + $UnclaimedDividend + $OtherLiabilities + $DueToSubsidiaryCompaniesAffiliates + $TaxOnInterimDividendPayable + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)/ $response['TangibleNetWorth']);
	$AdjustedTangibleNetWorth = ($response['TangibleNetWorth'] -$InvestmentsInSubsidiaryCompaniesAffiliates + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity);
	$response['TolAdjTnwAtnw'] = $AdjustedTangibleNetWorth == 0 ? 0 : (((($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $SundryCreditorsTrade + $ShortTermBorrowingsFromAssociatesGroupConcerns + $ShortTermBorrowingsCommercialPaper + $ShortTermBorrowingsFromOthers + $AdvancesPaymentsFromCustomersDepositsFromDealers + $ProvisionForTaxation + $ProposedDividend + $OtherStatutoryLiabilitiesDueWithinOneYear + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $DepositsDueForRepaymentDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $OtherCurrentLiabilitiesProvisionsDueWithin1Year + $InterestAccButNotDue + $ProvisionForNpa + $ProvisionForLeaveEncashmentGratuity + $UnclaimedDividend + $OtherLiabilities + $DueToSubsidiaryCompaniesAffiliates + $TaxOnInterimDividendPayable + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)) - $BorrowingsFromSubsidiariesAffiliatesQuasiEquity) / $AdjustedTangibleNetWorth);
	$response['DebtPbdit'] = $response['PBDITOperatingProfit'] == 0 ? 0 : (($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $ShortTermBorrowingsCommercialPaper + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)/ $response['PBDITOperatingProfit']);
	$c =  $GrossDomesticSales + $ExportSales + $AddTradingOtherOperatingIncome;
	$response['RecievableTurnover'] = $c == 0 ? 0 : ((($ReceivablesOtherThanDeferredExportsInclBillsPurchasedDiscountedByBanks+$ExportReceivablesIncludingBillPurchasedAndDiscounted+$RetentionMoneySecurityDeposit+$DebtorsMoreThan6Months)/ $c)*365);
	$response['CashAndBankBalances'] = $CashAndBankBalances;
	$response['pbditNetsales'] = $response['TotalOperatingIncome'] == 0 ? 0 : (($response['PBDITOperatingProfit'] / $response['TotalOperatingIncome']) * 100);
	$response['netProfitNetSales'] = $response['TotalOperatingIncome'] == 0 ? 0 : (($response['NetProfit'] / $response['TotalOperatingIncome']) * 100);
	$response['cashProfitNetSales'] = $response['TotalOperatingIncome'] == 0 ? 0 : (($response['CashProfit'] / $response['TotalOperatingIncome']) * 100);
	$response['TotalFixedAssets'] = $Land + $Building + $Vehicles + $PlantMachinery + $FurnitureFixtures + $OtherFixedAssets + $CapitalWip-$LessAccumulatedDepreciation-$RevaluationReserve;
	$response['TotalOutsideLiabilities'] = $FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $SundryCreditorsTrade + $ShortTermBorrowingsFromAssociatesGroupConcerns + $ShortTermBorrowingsCommercialPaper + $ShortTermBorrowingsFromOthers + $AdvancesPaymentsFromCustomersDepositsFromDealers + $ProvisionForTaxation + $ProposedDividend + $OtherStatutoryLiabilitiesDueWithinOneYear + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $DepositsDueForRepaymentDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $OtherCurrentLiabilitiesProvisionsDueWithin1Year + $InterestAccButNotDue + $ProvisionForNpa + $ProvisionForLeaveEncashmentGratuity + $UnclaimedDividend + $OtherLiabilities + $DueToSubsidiaryCompaniesAffiliates + $TaxOnInterimDividendPayable + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances + $PartnersCapitalProprietorSCapital + $ShareCapitalPaidUp + $ShareApplicationFinalizedForAllotment + $StatutoryAndCapitalReserves + $GeneralReserve + $RevaluationReserve + $OtherReservesExcludingProvisions + $SurplusOrDeficitInPLAccount + $SharePremiumAC + $CapitalSubsidy + $InvestmentAllowanceUtilizationReserve-$RevaluationReserve-($PartnersCapitalProprietorSCapital + $ShareCapitalPaidUp + $ShareApplicationFinalizedForAllotment + $StatutoryAndCapitalReserves + $GeneralReserve + $RevaluationReserve + $OtherReservesExcludingProvisions + $SurplusOrDeficitInPLAccount + $SharePremiumAC + $CapitalSubsidy + $InvestmentAllowanceUtilizationReserve-$RevaluationReserve);


	$d = ($InterestPaymentToBanks['InterestTermLoans'] + $InterestPaymentToFIs['InterestTermLoans'] + $Prev_TotalRepaymentsDueWithin1Year);

	$response['DSCR'] =  $d == 0 ? 0 : ($response['CashProfit'] + $InterestPaymentToBanks['InterestTermLoans'] + $InterestPaymentToFIs['InterestTermLoans']) / $d;

	$response['AdjustedTangibleNetWorth'] =  $AdjustedTangibleNetWorth;
	return $response;
}

function getFinContent() {
	return 'ewogICAiRmluYW5jaWFsU3RhdGVtZW50Ijp7CiAgICAgICJGWSI6WwogICAgICAgICB7CiAgICAgICAgICAgICJ5ZWFyIjowLAogICAgICAgICAgICAiUHJvZml0QW5kTG9zcyI6ewogICAgICAgICAgICAgICAiUHJvZml0QmVmb3JlVGF4TG9zc1BidCI6MCwKICAgICAgICAgICAgICAgIkFkZE9wZW5pbmdTdG9ja0luUHJvY2VzcyI6MCwKICAgICAgICAgICAgICAgIlByZWxpRXhwT25lVGltZUV4cGVuc2VzV3JpdHRlbk9mZiI6MCwKICAgICAgICAgICAgICAgIlBiaXRBc09mR3Jvc3NTYWxlcyI6MCwKICAgICAgICAgICAgICAgIk9wZXJhdGluZ1Byb2ZpdEJlZm9yZVRheE9wYnQiOjAsCiAgICAgICAgICAgICAgICJOZXRMb3NzT25Gb3JlaWduQ3VycmVuY3lUcmFuc2xhdGlvbkFuZFRyYW5zYWN0aW9uc0xvc3NEdWVUb0ZpcmUiOjAsCiAgICAgICAgICAgICAgICJFeHBvcnRJbmNlbnRpdmVzIjowLAogICAgICAgICAgICAgICAiRXh0cmFvcmRpbmFyeUV4cGVuc2VzQWRqdXN0bWVudHMiOjAsCiAgICAgICAgICAgICAgICJMZXNzRXhjaXNlRHV0eSI6MCwKICAgICAgICAgICAgICAgIkNvc3RPZlNhbGVzIjowLAogICAgICAgICAgICAgICAiQWRqdXN0ZWRQYXRFeGNsRXh0cmFvcmRpbmFyeUl0ZW1zIjowLAogICAgICAgICAgICAgICAiUHJvdmlzaW9uRm9yVGF4ZXNEZWZmZXJlZFRheGVzIjowLAogICAgICAgICAgICAgICAiTm9uT3BlcmF0aW5nSW5jb21lRnJvbVN1YnNpZGlhcmllcyI6MCwKICAgICAgICAgICAgICAgIk1pc2NFeHBXcml0dGVuT2ZmIjowLAogICAgICAgICAgICAgICAiRm9yZXhHYWlucyI6MCwKICAgICAgICAgICAgICAgIkVxdWl0eURpdmlkZW5kUGFpZEFtb3VudCI6MCwKICAgICAgICAgICAgICAgIkNvc3RPZlNhbGVzU2dhIjowLAogICAgICAgICAgICAgICAiUHJvZml0QmVmb3JlSW50ZXJlc3RUYXhQYml0IjowLAogICAgICAgICAgICAgICAiU3ViVG90YWxTYWxlcyI6MCwKICAgICAgICAgICAgICAgIlRheFBhaWQiOjAsCiAgICAgICAgICAgICAgICJJbnRlcmVzdFBheW1lbnRUb0ZJcyI6ewogICAgICAgICAgICAgICAgICAiSW50ZXJlc3RXYyI6MCwKICAgICAgICAgICAgICAgICAgIkludGVyZXN0VGVybUxvYW5zIjowCiAgICAgICAgICAgICAgIH0sCiAgICAgICAgICAgICAgICJHcm9zc1NhbGVzIjowLAogICAgICAgICAgICAgICAiTmV0U2FsZXMiOjAsCiAgICAgICAgICAgICAgICJEdXR5RHJhd2JhY2siOjAsCiAgICAgICAgICAgICAgICJXZWFsdGhUYXgiOjAsCiAgICAgICAgICAgICAgICJDb3N0T2ZTYWxlc0FzT2ZHcm9zc0luY29tZSI6MCwKICAgICAgICAgICAgICAgIlByb3Zpc2lvbkZvclRheGVzVG90YWwiOjAsCiAgICAgICAgICAgICAgICJEZXByZWNpYXRpb24iOjAsCiAgICAgICAgICAgICAgICJQcm92Rm9yRG91YkRlYnRzRGltSW5UaGVWYWxPZkludiI6MCwKICAgICAgICAgICAgICAgIlJhd01hdGVyaWFscyI6ewogICAgICAgICAgICAgICAgICAiSW5kaWdlbm91cyI6MCwKICAgICAgICAgICAgICAgICAgIkltcG9ydGVkIjowCiAgICAgICAgICAgICAgIH0sCiAgICAgICAgICAgICAgICJFcXVpdHlEaXZpZGVuZFBhaWRSYXRlIjowLAogICAgICAgICAgICAgICAiUGFydG5lcnNXaXRoZHJhd2FsIjowLAogICAgICAgICAgICAgICAiVGF4UmVmdW5kIjowLAogICAgICAgICAgICAgICAiQ29zdE9mVHJhZGluZ0dvb2RzIjowLAogICAgICAgICAgICAgICAiRGl2aWRlbmRQcmVmZXJlbmNlIjowLAogICAgICAgICAgICAgICAiVG90YWxPcGVyYXRpbmdJbmNvbWUiOjAsCiAgICAgICAgICAgICAgICJBZGRPcGVuaW5nU3RvY2tPZkZpbmlzaGVkR29vZHMiOjAsCiAgICAgICAgICAgICAgICJFeHBvcnRTYWxlcyI6MCwKICAgICAgICAgICAgICAgIlNlbGxpbmdHZW5lcmFsQWRtRXhwZW5zZXMiOjAsCiAgICAgICAgICAgICAgICJSZXRhaW5lZFByb2ZpdCI6MCwKICAgICAgICAgICAgICAgIkRpcmVjdExhYm91ciI6MCwKICAgICAgICAgICAgICAgIkRlcml2YXRpdmVMb3NzZXNCb29rZWQiOjAsCiAgICAgICAgICAgICAgICJUb3RhbE5vbk9wZXJhdGluZ0luY29tZSI6MCwKICAgICAgICAgICAgICAgIkNPUEFzT2ZHcm9zc0luY29tZSI6MCwKICAgICAgICAgICAgICAgIkludGVyZXN0UGF5bWVudFRvQmFua3MiOnsKICAgICAgICAgICAgICAgICAgIkludGVyZXN0V2MiOjAsCiAgICAgICAgICAgICAgICAgICJJbnRlcmVzdFRlcm1Mb2FucyI6MAogICAgICAgICAgICAgICB9LAogICAgICAgICAgICAgICAiUHJvdmlzaW9uc0V4cGVuc2VzV3JpdHRlbkJhY2siOjAsCiAgICAgICAgICAgICAgICJJbnRlcmVzdE90aGVyRmluYW5jZUNoYXJnZXMiOjAsCiAgICAgICAgICAgICAgICJPcGJ0QXNPZkdyb3NzSW5jb21lIjowLAogICAgICAgICAgICAgICAiQmFua0NoYXJnZXMiOjAsCiAgICAgICAgICAgICAgICJUb3RhbEV4dHJhb3JkaW5hcnlJdGVtcyI6MCwKICAgICAgICAgICAgICAgIkRpdmlkZW5kVGF4IjowLAogICAgICAgICAgICAgICAiR3Jvc3NEb21lc3RpY1NhbGVzIjowLAogICAgICAgICAgICAgICAiRGVkdWN0Q2xvc2luZ1N0b2NrT2ZGaW5pc2hlZEdvb2RzIjowLAogICAgICAgICAgICAgICAiVG90YWxOb25PcGVyYXRpbmdFeHBlbnNlcyI6MCwKICAgICAgICAgICAgICAgIk5ldE9mTm9uT3BlcmF0aW5nSW5jb21lRXhwZW5zZXMiOjAsCiAgICAgICAgICAgICAgICJPdGhlcnMiOjAsCiAgICAgICAgICAgICAgICJJbnR0RmluQ2hhcmdlc0FzT2ZHcm9zc1NhbGVzIjowLAogICAgICAgICAgICAgICAiUmVwYWlyc01haW50ZW5hbmNlIjowLAogICAgICAgICAgICAgICAiUHJvZml0QmVmb3JlSW50ZXJlc3REZXByZWNpYXRpb25UYXhQYmlkdCI6MCwKICAgICAgICAgICAgICAgIkludGVyZXN0T25EZXBvc2l0c0RpdmlkZW5kUmVjZWl2ZWQiOjAsCiAgICAgICAgICAgICAgICJPdGhlckluY29tZSI6MCwKICAgICAgICAgICAgICAgIlBhdEFzT2ZHcm9zc0luY29tZSI6MCwKICAgICAgICAgICAgICAgIkFkZFRyYWRpbmdPdGhlck9wZXJhdGluZ0luY29tZSI6MCwKICAgICAgICAgICAgICAgIkNvc3RPZlByb2R1Y3Rpb24iOjAsCiAgICAgICAgICAgICAgICJEZWR1Y3RDbG9zaW5nU3RvY2tJblByb2Nlc3MiOjAsCiAgICAgICAgICAgICAgICJPdGhlck1hbnVmYWN0dXJpbmdFeHBlbnNlcyI6MCwKICAgICAgICAgICAgICAgIlByb2ZpdE9uU2FsZU9mQXNzZXRzSW52ZXN0bWVudHMiOjAsCiAgICAgICAgICAgICAgICJPdGhlclNwYXJlcyI6ewogICAgICAgICAgICAgICAgICAiSW5kaWdlbm91cyI6MCwKICAgICAgICAgICAgICAgICAgIkltcG9ydGVkIjowCiAgICAgICAgICAgICAgIH0sCiAgICAgICAgICAgICAgICJNaXNjSW5jb21lIjowLAogICAgICAgICAgICAgICAiUG93ZXJGdWVsIjowLAogICAgICAgICAgICAgICAiTG9zc09uU2FsZU9mSW52ZXN0bWVudHMiOjAsCiAgICAgICAgICAgICAgICJFeHRyYW9yZGluYXJ5SW5jb21lQWRqdXN0bWVudHMiOjAsCiAgICAgICAgICAgICAgICJMb3NzT25TYWxlT2ZGYSI6MCwKICAgICAgICAgICAgICAgIk5ldFByb2ZpdExvc3NQYXQiOjAsCiAgICAgICAgICAgICAgICJQcm92aXNpb25Gb3JUYXhlc0N1cnJlbnRQZXJpb2QiOjAKICAgICAgICAgICAgfSwKICAgICAgICAgICAgIkJhbGFuY2VTaGVldCI6ewogICAgICAgICAgICAgICAiTGlhYmlsaXRpZXMiOnsKICAgICAgICAgICAgICAgICAgIlNoYXJlQ2FwaXRhbFBhaWRVcCI6MCwKICAgICAgICAgICAgICAgICAgIlNob3J0VGVybUJvcnJvd2luZ3NDb21tZXJjaWFsUGFwZXIiOjAsCiAgICAgICAgICAgICAgICAgICJCb3Jyb3dpbmdzRnJvbVN1YnNpZGlhcmllc0FmZmlsaWF0ZXNRdWFzaUVxdWl0eSI6MCwKICAgICAgICAgICAgICAgICAgIlRvdGFsTmV0V29ydGgiOjAsCiAgICAgICAgICAgICAgICAgICJEZXBvc2l0c0R1ZUZvclJlcGF5bWVudER1ZVdpdGhpbjFZZWFyIjowLAogICAgICAgICAgICAgICAgICAiR3JhdHVpdHlMaWFiaWxpdHlOb3RQcm92aWRlZEZvciI6MCwKICAgICAgICAgICAgICAgICAgIlN1YlRvdGFsUmVzZXJ2ZXMiOjAsCiAgICAgICAgICAgICAgICAgICJDYXBpdGFsU3Vic2lkeSI6MCwKICAgICAgICAgICAgICAgICAgIlN1bmRyeUNyZWRpdG9yc1RyYWRlIjowLAogICAgICAgICAgICAgICAgICAiSW52ZXN0bWVudEFsbG93YW5jZVV0aWxpemF0aW9uUmVzZXJ2ZSI6MCwKICAgICAgICAgICAgICAgICAgIlRvdGFsQ3VycmVudExpYWJpbGl0aWVzIjowLAogICAgICAgICAgICAgICAgICAiQ29udGluZ2VudExpYWJpbGl0aWVzIjowLAogICAgICAgICAgICAgICAgICAiVW5zZWN1cmVkTG9hbnMiOjAsCiAgICAgICAgICAgICAgICAgICJHdWFyYW50ZWVzSXNzdWVkUmVsYXRpbmdUb0J1c2luZXNzIjowLAogICAgICAgICAgICAgICAgICAiU3ViVG90YWxCYW5rTGlhYmlsaXRpZXMiOjAsCiAgICAgICAgICAgICAgICAgICJJbnRlcmVzdEFjY0J1dE5vdER1ZSI6MCwKICAgICAgICAgICAgICAgICAgIkRlYmVudHVyZXMiOjAsCiAgICAgICAgICAgICAgICAgICJQYXJ0bmVyc0NhcGl0YWxQcm9wcmlldG9yU0NhcGl0YWwiOjAsCiAgICAgICAgICAgICAgICAgICJTdGF0dXRvcnlBbmRDYXBpdGFsUmVzZXJ2ZXMiOjAsCiAgICAgICAgICAgICAgICAgICJBcnJlYXJzT2ZDdW11bGF0aXZlRGl2aWRlbmRzIjowLAogICAgICAgICAgICAgICAgICAiVGVybURlcG9zaXRzIjowLAogICAgICAgICAgICAgICAgICAiVW5jbGFpbWVkRGl2aWRlbmQiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlclJlc2VydmVzRXhjbHVkaW5nUHJvdmlzaW9ucyI6MCwKICAgICAgICAgICAgICAgICAgIkxjcyI6MCwKICAgICAgICAgICAgICAgICAgIlByZWZTaGFyZXNQb3J0aW9uUmVkZWVtYWJsZUFmdGVyMVlyIjowLAogICAgICAgICAgICAgICAgICAiVGVybUxvYW5zRnJvbUZpcyI6MCwKICAgICAgICAgICAgICAgICAgIlN1cnBsdXNPckRlZmljaXRJblBMQWNjb3VudCI6MCwKICAgICAgICAgICAgICAgICAgIlByb3Bvc2VkRGl2aWRlbmQiOjAsCiAgICAgICAgICAgICAgICAgICJJbnN0YWxsbWVudHNPZlRlcm1Mb2Fuc0RlYmVudHVyZXNEcGdzRXRjRHVlV2l0aGluMVllYXIiOjAsCiAgICAgICAgICAgICAgICAgICJPZklBbmRJaUluV2hpY2hCaWxsUHVyY2hhc2VkRGlzYyI6MCwKICAgICAgICAgICAgICAgICAgIkZyb21PdGhlckJhbmtzIjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJDdXJyZW50TGlhYmlsaXRpZXNQcm92aXNpb25zRHVlV2l0aGluMVllYXIiOjAsCiAgICAgICAgICAgICAgICAgICJTdWJUb3RhbE90aGVyTGlhYmlsaXRpZXMiOjAsCiAgICAgICAgICAgICAgICAgICJXY3RsIjowLAogICAgICAgICAgICAgICAgICAiVG90YWxTaGFyZUNhcGl0YWwiOjAsCiAgICAgICAgICAgICAgICAgICJUb3RhbFRlcm1MaWFiaWxpdGllcyI6MCwKICAgICAgICAgICAgICAgICAgIlByb3Zpc2lvbkZvclRheGF0aW9uIjowLAogICAgICAgICAgICAgICAgICAiUHJvdmlzaW9uRm9yTnBhIjowLAogICAgICAgICAgICAgICAgICAiUHJlZmVyZW5jZVNoYXJlc1JlZGVlbWFibGVXaXRoaW4xWWVhciI6MCwKICAgICAgICAgICAgICAgICAgIlRvdGFsTGlhYmlsaXRpZXMiOjAsCiAgICAgICAgICAgICAgICAgICJHZW5lcmFsUmVzZXJ2ZSI6MCwKICAgICAgICAgICAgICAgICAgIkFkdmFuY2VzUGF5bWVudHNGcm9tQ3VzdG9tZXJzRGVwb3NpdHNGcm9tRGVhbGVycyI6MCwKICAgICAgICAgICAgICAgICAgIkZyb21BcHBsaWNhbnRCYW5rQ2NXY2RsIjowLAogICAgICAgICAgICAgICAgICAiVGF4T25JbnRlcmltRGl2aWRlbmRQYXlhYmxlIjowLAogICAgICAgICAgICAgICAgICAiR3VhcmFudGVlc0lzc3VlZEZvckdyb3VwQ29tcGFuaWVzIjowLAogICAgICAgICAgICAgICAgICAiQWxsT3RoZXJDb250aW5nZW50TGlhYmlsaXRpZXNJbmNsZGdCaWxsc1B1cmNoYXNlZFVuZGVyTGMiOjAsCiAgICAgICAgICAgICAgICAgICJTaG9ydFRlcm1Cb3Jyb3dpbmdzRnJvbUFzc29jaWF0ZXNHcm91cENvbmNlcm5zIjowLAogICAgICAgICAgICAgICAgICAiRGlzcHV0ZWRFeGNpc2VDdXN0b21zSW5jb21lVGF4U2FsZXNUYXhMaWFiaWxpdGllcyI6MCwKICAgICAgICAgICAgICAgICAgIlJldmFsdWF0aW9uUmVzZXJ2ZSI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyU3RhdHV0b3J5TGlhYmlsaXRpZXNEdWVXaXRoaW5PbmVZZWFyIjowLAogICAgICAgICAgICAgICAgICAiRHVlVG9TdWJzaWRpYXJ5Q29tcGFuaWVzQWZmaWxpYXRlcyI6MCwKICAgICAgICAgICAgICAgICAgIlRlcm1MaWFiaWxpdGllcyI6MCwKICAgICAgICAgICAgICAgICAgIlNoYXJlQXBwbGljYXRpb25GaW5hbGl6ZWRGb3JBbGxvdG1lbnQiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlckxpYWJpbGl0aWVzIjowLAogICAgICAgICAgICAgICAgICAiU2hvcnRUZXJtQm9ycm93aW5nc0Zyb21PdGhlcnMiOjAsCiAgICAgICAgICAgICAgICAgICJEZWZlcnJlZFRheExpYWJpbGl0eSI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyVGVybUxpYWJpbGl0aWVzIjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJMb2FuQWR2YW5jZXMiOjAsCiAgICAgICAgICAgICAgICAgICJQcm92aXNpb25Gb3JMZWF2ZUVuY2FzaG1lbnRHcmF0dWl0eSI6MCwKICAgICAgICAgICAgICAgICAgIlRvdGFsUmVwYXltZW50c0R1ZVdpdGhpbjFZZWFyIjowLAogICAgICAgICAgICAgICAgICAiRGVwb3NpdEZyb21EZWFsZXJzT25seUlmQ29uc2lkZXJlZEFzQXZhaWxhYmxlRm9yTG9uZ1Rlcm0iOjAsCiAgICAgICAgICAgICAgICAgICJTaGFyZVByZW1pdW1BQyI6MCwKICAgICAgICAgICAgICAgICAgIlRvdGFsT3V0c2lkZUxpYWJpbGl0aWVzVG9sIjowLAogICAgICAgICAgICAgICAgICAiVGVybUxvYW5zRXhjbHVkaW5nSW5zdGFsbG1lbnRzUGF5YWJsZVdpdGhpbk9uZVllYXIiOjAKICAgICAgICAgICAgICAgfSwKICAgICAgICAgICAgICAgIkFzc2V0cyI6ewogICAgICAgICAgICAgICAgICAiSW50ZXJlc3RBY2NydWVkIjowLAogICAgICAgICAgICAgICAgICAiVGFuZ2libGVOZXR3b3J0aCI6MCwKICAgICAgICAgICAgICAgICAgIkZpbmlzaGVkR29vZHMiOjAsCiAgICAgICAgICAgICAgICAgICJJbmNvbWUiOjAsCiAgICAgICAgICAgICAgICAgICJNb250aFNQdXJjaGFzZXMiOjAsCiAgICAgICAgICAgICAgICAgICJGdXJuaXR1cmVGaXh0dXJlcyI6MCwKICAgICAgICAgICAgICAgICAgIlRvdGFsQXNzZXRzIjowLAogICAgICAgICAgICAgICAgICAiTW9udGhTQ29uc3VtcHRpb25Db25zdW1hYmxlU3BhcmVzSW1wb3J0ZWQiOjAsCiAgICAgICAgICAgICAgICAgICJSYXdNYXRlcmlhbEluZGlnZW5vdXNBbW91bnQiOjAsCiAgICAgICAgICAgICAgICAgICJHb3Z0T3RoZXJTZWN1cml0aWVzIjowLAogICAgICAgICAgICAgICAgICAiVmVoaWNsZXMiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlclN0b2NrcyI6MCwKICAgICAgICAgICAgICAgICAgIk1pblN0aXB1bGF0ZWROZXRXb3JraW5nQ2FwaXRhbDI1T2ZUb3RhbEN1cnJlbnRBc3NldHNFeGNsdWRpbmdFeHBvcnRSZWNlaXZhYmxlcyI6MCwKICAgICAgICAgICAgICAgICAgIk1vbnRoU0V4cG9ydEluY29tZSI6MCwKICAgICAgICAgICAgICAgICAgIkRlZmVycmVkVGF4QXNzZXQiOjAsCiAgICAgICAgICAgICAgICAgICJNb250aFNDb25zdW1wdGlvbkltcG9ydGVkIjowLAogICAgICAgICAgICAgICAgICAiUGxhbnRNYWNoaW5lcnkiOjAsCiAgICAgICAgICAgICAgICAgICJBZHZhbmNlQWdhaW5zdE1vcnRnYWdlT2ZIb3VzZVByb3BlcnR5IjowLAogICAgICAgICAgICAgICAgICAiTW9udGhTQ29uc3VtcHRpb25JbmRpZ2Vub3VzIjowLAogICAgICAgICAgICAgICAgICAiQWR2YW5jZVBheW1lbnRPZlRheCI6MCwKICAgICAgICAgICAgICAgICAgIkludmVzdG1lbnRzSW5TdWJzaWRpYXJ5Q29tcGFuaWVzQWZmaWxpYXRlcyI6MCwKICAgICAgICAgICAgICAgICAgIkRlcG9zaXRzIjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJDb25zdW1hYmxlU3BhcmVzSW1wb3J0ZWQiOjAsCiAgICAgICAgICAgICAgICAgICJDb25zdW1hYmxlU3BhcmVzSW5kaWdlbm91c0Ftb3VudCI6MCwKICAgICAgICAgICAgICAgICAgIkVzdGltYXRlZEdyb3NzVHVybm92ZXJOZXh0WWVhciI6MCwKICAgICAgICAgICAgICAgICAgIk5ldEJsb2NrIjowLAogICAgICAgICAgICAgICAgICAiRmluaXNoZWRHb29kc0Ftb3VudCI6MCwKICAgICAgICAgICAgICAgICAgIlNlbGxpbmdHZW5BZG1FeHBlbnNlcyI6MCwKICAgICAgICAgICAgICAgICAgIkxhbmQiOjAsCiAgICAgICAgICAgICAgICAgICJHcm9zc0Jsb2NrIjowLAogICAgICAgICAgICAgICAgICAiUmVjZWl2YWJsZXNEb21lc3RpY090aGVyVGhhbkRlZmVycmVkRXhwb3J0c0luY2xCaWxsc1B1cmNoYXNlZERpc2NvdW50ZWRCeUJhbmtzQW1vdW50IjowLAogICAgICAgICAgICAgICAgICAiTGltaXRzRnJvbU90aGVyQmFua3MiOjAsCiAgICAgICAgICAgICAgICAgICJNb250aFNDb3N0T2ZTYWxlcyI6MCwKICAgICAgICAgICAgICAgICAgIkRlcHJlY2lhdGlvbiI6MCwKICAgICAgICAgICAgICAgICAgIldvcmtpbmdDYXBpdGFsR2FwIjowLAogICAgICAgICAgICAgICAgICAiTWF4aW11bVBlcm1pc3NpYmxlQmFua0ZpbmFuY2VMb3dlck9mNk9yNyI6MCwKICAgICAgICAgICAgICAgICAgIkRlYnRvcnNNb3JlVGhhbjZNb250aHMiOjAsCiAgICAgICAgICAgICAgICAgICJNcGJmQXNQZXJUdXJub3Zlck1ldGhvZCI6MCwKICAgICAgICAgICAgICAgICAgIlRheGF0aW9uIjowLAogICAgICAgICAgICAgICAgICAiUHJvZml0QWZ0ZXJUYXgiOjAsCiAgICAgICAgICAgICAgICAgICJNYW51ZmFjdHVyaW5nRXhwZW5zZXMiOjAsCiAgICAgICAgICAgICAgICAgICJSYXdNYXRlcmlhbEltcG9ydGVkIjowLAogICAgICAgICAgICAgICAgICAiU3ViVG90YWxPdGhlckNvbnN1bWFibGVTcGFyZXMiOjAsCiAgICAgICAgICAgICAgICAgICJCdWlsZGluZyI6MCwKICAgICAgICAgICAgICAgICAgIkFjY3VtdWxhdGVkTG9zc2VzUHJlbGltaW5hcnlFeHBlbnNlc01pc2NlbGxhbmVvdXNFeHBlbmRpdHVyZU5vdFdPZmZPdGhlckRlZmVycmVkUmV2ZW51ZUV4cGVuc2VzIjowLAogICAgICAgICAgICAgICAgICAiTW9udGhTQ29zdE9mUHJvZHVjdGlvbiI6MCwKICAgICAgICAgICAgICAgICAgIkRpdmlkZW5kcyI6WwogICAgICAgICAgICAgICAgICAgICAwLAogICAgICAgICAgICAgICAgICAgICAwCiAgICAgICAgICAgICAgICAgIF0sCiAgICAgICAgICAgICAgICAgICJEZWZlcnJlZFJlY2VpdmFibGVzTWF0dXJpdHlFeGNlZWRpbmcxWWVhciI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyTm9uQ3VycmVudEFzc2V0c1N1cnBsdXNGb3JGdXR1cmVFeHBhbnNpb25Mb2Fuc0FkdmFuY2VzTm9uQ3VycmVudEluTmF0dXJlSWNkU0R1ZXNGcm9tRGlyZWN0b3JzIjowLAogICAgICAgICAgICAgICAgICAiQ3JlZGl0b3JzRm9yUHVyY2hhc2VzQW1vdW50IjowLAogICAgICAgICAgICAgICAgICAiV29ya2luZ0NhcGl0YWxSZXF1aXJlbWVudDI1T2ZFc3RpbWF0ZWRHcm9zc1R1cm5vdmVyIjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJJbmNvbWVFeHBlbnNlc05ldCI6MCwKICAgICAgICAgICAgICAgICAgIkZ1bmRzRnJvbU9wZXJhdGlvbnMiOjAsCiAgICAgICAgICAgICAgICAgICJSZWNlaXZhYmxlc090aGVyVGhhbkRlZmVycmVkRXhwb3J0c0luY2xCaWxsc1B1cmNoYXNlZERpc2NvdW50ZWRCeUJhbmtzIjowLAogICAgICAgICAgICAgICAgICAiU3RvY2tJblByb2Nlc3MiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlckZpeGVkQXNzZXRzIjowLAogICAgICAgICAgICAgICAgICAiQ2FzaEFuZEJhbmtCYWxhbmNlcyI6MCwKICAgICAgICAgICAgICAgICAgIkZpeGVkRGVwb3NpdHNXaXRoQmFua3MiOjAsCiAgICAgICAgICAgICAgICAgICJTdWJUb3RhbEludmVudG9yeSI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVySW52ZXN0bWVudHNJbnZlc3RtZW50Rm9yQWNxdWlzaXRpb24iOjAsCiAgICAgICAgICAgICAgICAgICJQcm92aXNpb25Gb3JOcGEiOjAsCiAgICAgICAgICAgICAgICAgICJNYXhQb3NzaWJsZUZpbmFuY2VGcm9tQ2ZwbCI6MCwKICAgICAgICAgICAgICAgICAgIkFkdmFuY2VzVG9TdXBwbGllcnNPZlJhd01hdGVyaWFsIjowLAogICAgICAgICAgICAgICAgICAiQWR2YW5jZVJlY2VpdmFibGVJbkNhc2hPcktpbmQiOjAsCiAgICAgICAgICAgICAgICAgICJFeHBvcnRSZWNlaXZhYmxlc0luY2x1ZGluZ0JpbGxQdXJjaGFzZWRBbmREaXNjb3VudGVkIjowLAogICAgICAgICAgICAgICAgICAiTW9udGhTRG9tZXN0aWNJbmNvbWUiOjAsCiAgICAgICAgICAgICAgICAgICJFeHBvcnRSZWN2SW5jbEJpbGxzUHVyY2hhc2VkRGlzY291bnRlZEJ5QmFua3NBbW91bnQiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlckN1cnJlbnRMaWFiaWxpdGllc090aGVyVGhhbkJhbmtCb3Jyb3dpbmdzVGxJbnN0YWxsbWVudHNEdWVXaXRoaW5PbmVZZWFyIjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJzIjowLAogICAgICAgICAgICAgICAgICAiSXRlbTNNaW51c0l0ZW00IjowLAogICAgICAgICAgICAgICAgICAiQ2FwaXRhbFdpcCI6MCwKICAgICAgICAgICAgICAgICAgIkl0ZW0zTWludXNJdGVtNSI6MCwKICAgICAgICAgICAgICAgICAgIkRlZmVycmVkUmV2ZW51ZUV4cGVuZGl0dXJlIjowLAogICAgICAgICAgICAgICAgICAiQ29uc3VtYWJsZVNwYXJlc0ltcG9ydGVkQW1vdW50IjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJDdXJyZW50QXNzZXRzIjowLAogICAgICAgICAgICAgICAgICAiU3RvY2tJblByb2Nlc3NBbW91bnQiOjAsCiAgICAgICAgICAgICAgICAgICJUb3RhbE90aGVyTm9uQ3VycmVudEFzc2V0cyI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyQ29uc3VtYWJsZVNwYXJlc0luZGlnZW5vdXMiOjAsCiAgICAgICAgICAgICAgICAgICJSYXdNYXRlcmlhbEluZGlnZW5vdXMiOjAsCiAgICAgICAgICAgICAgICAgICJDb3N0c0ludGVyZXN0RmluYW5jZUNoYXJnZXMiOjAsCiAgICAgICAgICAgICAgICAgICJNYXJnaW5Nb25leUtlcHRXaXRoQmFua3MiOjAsCiAgICAgICAgICAgICAgICAgICJSYXdNYXRlcmlhbEltcG9ydGVkQW1vdW50IjowLAogICAgICAgICAgICAgICAgICAiTGVzc0FjY3VtdWxhdGVkRGVwcmVjaWF0aW9uIjowLAogICAgICAgICAgICAgICAgICAiQWN0dWFsUHJvamVjdGVkTndjIjowLAogICAgICAgICAgICAgICAgICAiSW52ZXN0bWVudHNPdGhlclRoYW5Mb25nVGVybSI6MCwKICAgICAgICAgICAgICAgICAgIkR1ZUZyb21TdWJzaWRpYXJpZXMiOjAsCiAgICAgICAgICAgICAgICAgICJTdW5kcnlEZXBvc2l0IjowLAogICAgICAgICAgICAgICAgICAiUmV0ZW50aW9uTW9uZXlTZWN1cml0eURlcG9zaXQiOjAsCiAgICAgICAgICAgICAgICAgICJUb3RhbEN1cnJlbnRBc3NldHMiOjAsCiAgICAgICAgICAgICAgICAgICJUb3RhbExpYWJpbGl0aWVzVG90YWxBc3NldHMiOjAsCiAgICAgICAgICAgICAgICAgICJNb250aFNDb25zdW1wdGlvbkNvbnN1bWFibGVTcGFyZXNJbmRpZ2Vub3VzIjowLAogICAgICAgICAgICAgICAgICAiRXhwZW5zZXNPblN0b2Nrc1B1cmNoYXNlcyI6MCwKICAgICAgICAgICAgICAgICAgIk1vZHZhdENyZWRpdFJlY2VpdmFibGUiOjAKICAgICAgICAgICAgICAgfQogICAgICAgICAgICB9CiAgICAgICAgIH0sCiAgICAgICAgIHsKICAgICAgICAgICAgInllYXIiOjAsCiAgICAgICAgICAgICJQcm9maXRBbmRMb3NzIjp7CiAgICAgICAgICAgICAgICJQcm9maXRCZWZvcmVUYXhMb3NzUGJ0IjowLAogICAgICAgICAgICAgICAiQWRkT3BlbmluZ1N0b2NrSW5Qcm9jZXNzIjowLAogICAgICAgICAgICAgICAiUHJlbGlFeHBPbmVUaW1lRXhwZW5zZXNXcml0dGVuT2ZmIjowLAogICAgICAgICAgICAgICAiUGJpdEFzT2ZHcm9zc1NhbGVzIjowLAogICAgICAgICAgICAgICAiT3BlcmF0aW5nUHJvZml0QmVmb3JlVGF4T3BidCI6MCwKICAgICAgICAgICAgICAgIk5ldExvc3NPbkZvcmVpZ25DdXJyZW5jeVRyYW5zbGF0aW9uQW5kVHJhbnNhY3Rpb25zTG9zc0R1ZVRvRmlyZSI6MCwKICAgICAgICAgICAgICAgIkV4cG9ydEluY2VudGl2ZXMiOjAsCiAgICAgICAgICAgICAgICJFeHRyYW9yZGluYXJ5RXhwZW5zZXNBZGp1c3RtZW50cyI6MCwKICAgICAgICAgICAgICAgIkxlc3NFeGNpc2VEdXR5IjowLAogICAgICAgICAgICAgICAiQ29zdE9mU2FsZXMiOjAsCiAgICAgICAgICAgICAgICJBZGp1c3RlZFBhdEV4Y2xFeHRyYW9yZGluYXJ5SXRlbXMiOjAsCiAgICAgICAgICAgICAgICJQcm92aXNpb25Gb3JUYXhlc0RlZmZlcmVkVGF4ZXMiOjAsCiAgICAgICAgICAgICAgICJOb25PcGVyYXRpbmdJbmNvbWVGcm9tU3Vic2lkaWFyaWVzIjowLAogICAgICAgICAgICAgICAiTWlzY0V4cFdyaXR0ZW5PZmYiOjAsCiAgICAgICAgICAgICAgICJGb3JleEdhaW5zIjowLAogICAgICAgICAgICAgICAiRXF1aXR5RGl2aWRlbmRQYWlkQW1vdW50IjowLAogICAgICAgICAgICAgICAiQ29zdE9mU2FsZXNTZ2EiOjAsCiAgICAgICAgICAgICAgICJQcm9maXRCZWZvcmVJbnRlcmVzdFRheFBiaXQiOjAsCiAgICAgICAgICAgICAgICJTdWJUb3RhbFNhbGVzIjowLAogICAgICAgICAgICAgICAiVGF4UGFpZCI6MCwKICAgICAgICAgICAgICAgIkludGVyZXN0UGF5bWVudFRvRklzIjp7CiAgICAgICAgICAgICAgICAgICJJbnRlcmVzdFdjIjowLAogICAgICAgICAgICAgICAgICAiSW50ZXJlc3RUZXJtTG9hbnMiOjAKICAgICAgICAgICAgICAgfSwKICAgICAgICAgICAgICAgIkdyb3NzU2FsZXMiOjAsCiAgICAgICAgICAgICAgICJOZXRTYWxlcyI6MCwKICAgICAgICAgICAgICAgIkR1dHlEcmF3YmFjayI6MCwKICAgICAgICAgICAgICAgIldlYWx0aFRheCI6MCwKICAgICAgICAgICAgICAgIkNvc3RPZlNhbGVzQXNPZkdyb3NzSW5jb21lIjowLAogICAgICAgICAgICAgICAiUHJvdmlzaW9uRm9yVGF4ZXNUb3RhbCI6MCwKICAgICAgICAgICAgICAgIkRlcHJlY2lhdGlvbiI6MCwKICAgICAgICAgICAgICAgIlByb3ZGb3JEb3ViRGVidHNEaW1JblRoZVZhbE9mSW52IjowLAogICAgICAgICAgICAgICAiUmF3TWF0ZXJpYWxzIjp7CiAgICAgICAgICAgICAgICAgICJJbmRpZ2Vub3VzIjowLAogICAgICAgICAgICAgICAgICAiSW1wb3J0ZWQiOjAKICAgICAgICAgICAgICAgfSwKICAgICAgICAgICAgICAgIkVxdWl0eURpdmlkZW5kUGFpZFJhdGUiOjAsCiAgICAgICAgICAgICAgICJQYXJ0bmVyc1dpdGhkcmF3YWwiOjAsCiAgICAgICAgICAgICAgICJUYXhSZWZ1bmQiOjAsCiAgICAgICAgICAgICAgICJDb3N0T2ZUcmFkaW5nR29vZHMiOjAsCiAgICAgICAgICAgICAgICJEaXZpZGVuZFByZWZlcmVuY2UiOjAsCiAgICAgICAgICAgICAgICJUb3RhbE9wZXJhdGluZ0luY29tZSI6MCwKICAgICAgICAgICAgICAgIkFkZE9wZW5pbmdTdG9ja09mRmluaXNoZWRHb29kcyI6MCwKICAgICAgICAgICAgICAgIkV4cG9ydFNhbGVzIjowLAogICAgICAgICAgICAgICAiU2VsbGluZ0dlbmVyYWxBZG1FeHBlbnNlcyI6MCwKICAgICAgICAgICAgICAgIlJldGFpbmVkUHJvZml0IjowLAogICAgICAgICAgICAgICAiRGlyZWN0TGFib3VyIjowLAogICAgICAgICAgICAgICAiRGVyaXZhdGl2ZUxvc3Nlc0Jvb2tlZCI6MCwKICAgICAgICAgICAgICAgIlRvdGFsTm9uT3BlcmF0aW5nSW5jb21lIjowLAogICAgICAgICAgICAgICAiQ09QQXNPZkdyb3NzSW5jb21lIjowLAogICAgICAgICAgICAgICAiSW50ZXJlc3RQYXltZW50VG9CYW5rcyI6ewogICAgICAgICAgICAgICAgICAiSW50ZXJlc3RXYyI6MCwKICAgICAgICAgICAgICAgICAgIkludGVyZXN0VGVybUxvYW5zIjowCiAgICAgICAgICAgICAgIH0sCiAgICAgICAgICAgICAgICJQcm92aXNpb25zRXhwZW5zZXNXcml0dGVuQmFjayI6MCwKICAgICAgICAgICAgICAgIkludGVyZXN0T3RoZXJGaW5hbmNlQ2hhcmdlcyI6MCwKICAgICAgICAgICAgICAgIk9wYnRBc09mR3Jvc3NJbmNvbWUiOjAsCiAgICAgICAgICAgICAgICJCYW5rQ2hhcmdlcyI6MCwKICAgICAgICAgICAgICAgIlRvdGFsRXh0cmFvcmRpbmFyeUl0ZW1zIjowLAogICAgICAgICAgICAgICAiRGl2aWRlbmRUYXgiOjAsCiAgICAgICAgICAgICAgICJHcm9zc0RvbWVzdGljU2FsZXMiOjAsCiAgICAgICAgICAgICAgICJEZWR1Y3RDbG9zaW5nU3RvY2tPZkZpbmlzaGVkR29vZHMiOjAsCiAgICAgICAgICAgICAgICJUb3RhbE5vbk9wZXJhdGluZ0V4cGVuc2VzIjowLAogICAgICAgICAgICAgICAiTmV0T2ZOb25PcGVyYXRpbmdJbmNvbWVFeHBlbnNlcyI6MCwKICAgICAgICAgICAgICAgIk90aGVycyI6MCwKICAgICAgICAgICAgICAgIkludHRGaW5DaGFyZ2VzQXNPZkdyb3NzU2FsZXMiOjAsCiAgICAgICAgICAgICAgICJSZXBhaXJzTWFpbnRlbmFuY2UiOjAsCiAgICAgICAgICAgICAgICJQcm9maXRCZWZvcmVJbnRlcmVzdERlcHJlY2lhdGlvblRheFBiaWR0IjowLAogICAgICAgICAgICAgICAiSW50ZXJlc3RPbkRlcG9zaXRzRGl2aWRlbmRSZWNlaXZlZCI6MCwKICAgICAgICAgICAgICAgIk90aGVySW5jb21lIjowLAogICAgICAgICAgICAgICAiUGF0QXNPZkdyb3NzSW5jb21lIjowLAogICAgICAgICAgICAgICAiQWRkVHJhZGluZ090aGVyT3BlcmF0aW5nSW5jb21lIjowLAogICAgICAgICAgICAgICAiQ29zdE9mUHJvZHVjdGlvbiI6MCwKICAgICAgICAgICAgICAgIkRlZHVjdENsb3NpbmdTdG9ja0luUHJvY2VzcyI6MCwKICAgICAgICAgICAgICAgIk90aGVyTWFudWZhY3R1cmluZ0V4cGVuc2VzIjowLAogICAgICAgICAgICAgICAiUHJvZml0T25TYWxlT2ZBc3NldHNJbnZlc3RtZW50cyI6MCwKICAgICAgICAgICAgICAgIk90aGVyU3BhcmVzIjp7CiAgICAgICAgICAgICAgICAgICJJbmRpZ2Vub3VzIjowLAogICAgICAgICAgICAgICAgICAiSW1wb3J0ZWQiOjAKICAgICAgICAgICAgICAgfSwKICAgICAgICAgICAgICAgIk1pc2NJbmNvbWUiOjAsCiAgICAgICAgICAgICAgICJQb3dlckZ1ZWwiOjAsCiAgICAgICAgICAgICAgICJMb3NzT25TYWxlT2ZJbnZlc3RtZW50cyI6MCwKICAgICAgICAgICAgICAgIkV4dHJhb3JkaW5hcnlJbmNvbWVBZGp1c3RtZW50cyI6MCwKICAgICAgICAgICAgICAgIkxvc3NPblNhbGVPZkZhIjowLAogICAgICAgICAgICAgICAiTmV0UHJvZml0TG9zc1BhdCI6MCwKICAgICAgICAgICAgICAgIlByb3Zpc2lvbkZvclRheGVzQ3VycmVudFBlcmlvZCI6MAogICAgICAgICAgICB9LAogICAgICAgICAgICAiQmFsYW5jZVNoZWV0Ijp7CiAgICAgICAgICAgICAgICJMaWFiaWxpdGllcyI6ewogICAgICAgICAgICAgICAgICAiU2hhcmVDYXBpdGFsUGFpZFVwIjowLAogICAgICAgICAgICAgICAgICAiU2hvcnRUZXJtQm9ycm93aW5nc0NvbW1lcmNpYWxQYXBlciI6MCwKICAgICAgICAgICAgICAgICAgIkJvcnJvd2luZ3NGcm9tU3Vic2lkaWFyaWVzQWZmaWxpYXRlc1F1YXNpRXF1aXR5IjowLAogICAgICAgICAgICAgICAgICAiVG90YWxOZXRXb3J0aCI6MCwKICAgICAgICAgICAgICAgICAgIkRlcG9zaXRzRHVlRm9yUmVwYXltZW50RHVlV2l0aGluMVllYXIiOjAsCiAgICAgICAgICAgICAgICAgICJHcmF0dWl0eUxpYWJpbGl0eU5vdFByb3ZpZGVkRm9yIjowLAogICAgICAgICAgICAgICAgICAiU3ViVG90YWxSZXNlcnZlcyI6MCwKICAgICAgICAgICAgICAgICAgIkNhcGl0YWxTdWJzaWR5IjowLAogICAgICAgICAgICAgICAgICAiU3VuZHJ5Q3JlZGl0b3JzVHJhZGUiOjAsCiAgICAgICAgICAgICAgICAgICJJbnZlc3RtZW50QWxsb3dhbmNlVXRpbGl6YXRpb25SZXNlcnZlIjowLAogICAgICAgICAgICAgICAgICAiVG90YWxDdXJyZW50TGlhYmlsaXRpZXMiOjAsCiAgICAgICAgICAgICAgICAgICJDb250aW5nZW50TGlhYmlsaXRpZXMiOjAsCiAgICAgICAgICAgICAgICAgICJVbnNlY3VyZWRMb2FucyI6MCwKICAgICAgICAgICAgICAgICAgIkd1YXJhbnRlZXNJc3N1ZWRSZWxhdGluZ1RvQnVzaW5lc3MiOjAsCiAgICAgICAgICAgICAgICAgICJTdWJUb3RhbEJhbmtMaWFiaWxpdGllcyI6MCwKICAgICAgICAgICAgICAgICAgIkludGVyZXN0QWNjQnV0Tm90RHVlIjowLAogICAgICAgICAgICAgICAgICAiRGViZW50dXJlcyI6MCwKICAgICAgICAgICAgICAgICAgIlBhcnRuZXJzQ2FwaXRhbFByb3ByaWV0b3JTQ2FwaXRhbCI6MCwKICAgICAgICAgICAgICAgICAgIlN0YXR1dG9yeUFuZENhcGl0YWxSZXNlcnZlcyI6MCwKICAgICAgICAgICAgICAgICAgIkFycmVhcnNPZkN1bXVsYXRpdmVEaXZpZGVuZHMiOjAsCiAgICAgICAgICAgICAgICAgICJUZXJtRGVwb3NpdHMiOjAsCiAgICAgICAgICAgICAgICAgICJVbmNsYWltZWREaXZpZGVuZCI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyUmVzZXJ2ZXNFeGNsdWRpbmdQcm92aXNpb25zIjowLAogICAgICAgICAgICAgICAgICAiTGNzIjowLAogICAgICAgICAgICAgICAgICAiUHJlZlNoYXJlc1BvcnRpb25SZWRlZW1hYmxlQWZ0ZXIxWXIiOjAsCiAgICAgICAgICAgICAgICAgICJUZXJtTG9hbnNGcm9tRmlzIjowLAogICAgICAgICAgICAgICAgICAiU3VycGx1c09yRGVmaWNpdEluUExBY2NvdW50IjowLAogICAgICAgICAgICAgICAgICAiUHJvcG9zZWREaXZpZGVuZCI6MCwKICAgICAgICAgICAgICAgICAgIkluc3RhbGxtZW50c09mVGVybUxvYW5zRGViZW50dXJlc0RwZ3NFdGNEdWVXaXRoaW4xWWVhciI6MCwKICAgICAgICAgICAgICAgICAgIk9mSUFuZElpSW5XaGljaEJpbGxQdXJjaGFzZWREaXNjIjowLAogICAgICAgICAgICAgICAgICAiRnJvbU90aGVyQmFua3MiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlckN1cnJlbnRMaWFiaWxpdGllc1Byb3Zpc2lvbnNEdWVXaXRoaW4xWWVhciI6MCwKICAgICAgICAgICAgICAgICAgIlN1YlRvdGFsT3RoZXJMaWFiaWxpdGllcyI6MCwKICAgICAgICAgICAgICAgICAgIldjdGwiOjAsCiAgICAgICAgICAgICAgICAgICJUb3RhbFNoYXJlQ2FwaXRhbCI6MCwKICAgICAgICAgICAgICAgICAgIlRvdGFsVGVybUxpYWJpbGl0aWVzIjowLAogICAgICAgICAgICAgICAgICAiUHJvdmlzaW9uRm9yVGF4YXRpb24iOjAsCiAgICAgICAgICAgICAgICAgICJQcm92aXNpb25Gb3JOcGEiOjAsCiAgICAgICAgICAgICAgICAgICJQcmVmZXJlbmNlU2hhcmVzUmVkZWVtYWJsZVdpdGhpbjFZZWFyIjowLAogICAgICAgICAgICAgICAgICAiVG90YWxMaWFiaWxpdGllcyI6MCwKICAgICAgICAgICAgICAgICAgIkdlbmVyYWxSZXNlcnZlIjowLAogICAgICAgICAgICAgICAgICAiQWR2YW5jZXNQYXltZW50c0Zyb21DdXN0b21lcnNEZXBvc2l0c0Zyb21EZWFsZXJzIjowLAogICAgICAgICAgICAgICAgICAiRnJvbUFwcGxpY2FudEJhbmtDY1djZGwiOjAsCiAgICAgICAgICAgICAgICAgICJUYXhPbkludGVyaW1EaXZpZGVuZFBheWFibGUiOjAsCiAgICAgICAgICAgICAgICAgICJHdWFyYW50ZWVzSXNzdWVkRm9yR3JvdXBDb21wYW5pZXMiOjAsCiAgICAgICAgICAgICAgICAgICJBbGxPdGhlckNvbnRpbmdlbnRMaWFiaWxpdGllc0luY2xkZ0JpbGxzUHVyY2hhc2VkVW5kZXJMYyI6MCwKICAgICAgICAgICAgICAgICAgIlNob3J0VGVybUJvcnJvd2luZ3NGcm9tQXNzb2NpYXRlc0dyb3VwQ29uY2VybnMiOjAsCiAgICAgICAgICAgICAgICAgICJEaXNwdXRlZEV4Y2lzZUN1c3RvbXNJbmNvbWVUYXhTYWxlc1RheExpYWJpbGl0aWVzIjowLAogICAgICAgICAgICAgICAgICAiUmV2YWx1YXRpb25SZXNlcnZlIjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJTdGF0dXRvcnlMaWFiaWxpdGllc0R1ZVdpdGhpbk9uZVllYXIiOjAsCiAgICAgICAgICAgICAgICAgICJEdWVUb1N1YnNpZGlhcnlDb21wYW5pZXNBZmZpbGlhdGVzIjowLAogICAgICAgICAgICAgICAgICAiVGVybUxpYWJpbGl0aWVzIjowLAogICAgICAgICAgICAgICAgICAiU2hhcmVBcHBsaWNhdGlvbkZpbmFsaXplZEZvckFsbG90bWVudCI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyTGlhYmlsaXRpZXMiOjAsCiAgICAgICAgICAgICAgICAgICJTaG9ydFRlcm1Cb3Jyb3dpbmdzRnJvbU90aGVycyI6MCwKICAgICAgICAgICAgICAgICAgIkRlZmVycmVkVGF4TGlhYmlsaXR5IjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJUZXJtTGlhYmlsaXRpZXMiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlckxvYW5BZHZhbmNlcyI6MCwKICAgICAgICAgICAgICAgICAgIlByb3Zpc2lvbkZvckxlYXZlRW5jYXNobWVudEdyYXR1aXR5IjowLAogICAgICAgICAgICAgICAgICAiVG90YWxSZXBheW1lbnRzRHVlV2l0aGluMVllYXIiOjAsCiAgICAgICAgICAgICAgICAgICJEZXBvc2l0RnJvbURlYWxlcnNPbmx5SWZDb25zaWRlcmVkQXNBdmFpbGFibGVGb3JMb25nVGVybSI6MCwKICAgICAgICAgICAgICAgICAgIlNoYXJlUHJlbWl1bUFDIjowLAogICAgICAgICAgICAgICAgICAiVG90YWxPdXRzaWRlTGlhYmlsaXRpZXNUb2wiOjAsCiAgICAgICAgICAgICAgICAgICJUZXJtTG9hbnNFeGNsdWRpbmdJbnN0YWxsbWVudHNQYXlhYmxlV2l0aGluT25lWWVhciI6MAogICAgICAgICAgICAgICB9LAogICAgICAgICAgICAgICAiQXNzZXRzIjp7CiAgICAgICAgICAgICAgICAgICJJbnRlcmVzdEFjY3J1ZWQiOjAsCiAgICAgICAgICAgICAgICAgICJUYW5naWJsZU5ldHdvcnRoIjowLAogICAgICAgICAgICAgICAgICAiRmluaXNoZWRHb29kcyI6MCwKICAgICAgICAgICAgICAgICAgIkluY29tZSI6MCwKICAgICAgICAgICAgICAgICAgIk1vbnRoU1B1cmNoYXNlcyI6MCwKICAgICAgICAgICAgICAgICAgIkZ1cm5pdHVyZUZpeHR1cmVzIjowLAogICAgICAgICAgICAgICAgICAiVG90YWxBc3NldHMiOjAsCiAgICAgICAgICAgICAgICAgICJNb250aFNDb25zdW1wdGlvbkNvbnN1bWFibGVTcGFyZXNJbXBvcnRlZCI6MCwKICAgICAgICAgICAgICAgICAgIlJhd01hdGVyaWFsSW5kaWdlbm91c0Ftb3VudCI6MCwKICAgICAgICAgICAgICAgICAgIkdvdnRPdGhlclNlY3VyaXRpZXMiOjAsCiAgICAgICAgICAgICAgICAgICJWZWhpY2xlcyI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyU3RvY2tzIjowLAogICAgICAgICAgICAgICAgICAiTWluU3RpcHVsYXRlZE5ldFdvcmtpbmdDYXBpdGFsMjVPZlRvdGFsQ3VycmVudEFzc2V0c0V4Y2x1ZGluZ0V4cG9ydFJlY2VpdmFibGVzIjowLAogICAgICAgICAgICAgICAgICAiTW9udGhTRXhwb3J0SW5jb21lIjowLAogICAgICAgICAgICAgICAgICAiRGVmZXJyZWRUYXhBc3NldCI6MCwKICAgICAgICAgICAgICAgICAgIk1vbnRoU0NvbnN1bXB0aW9uSW1wb3J0ZWQiOjAsCiAgICAgICAgICAgICAgICAgICJQbGFudE1hY2hpbmVyeSI6MCwKICAgICAgICAgICAgICAgICAgIkFkdmFuY2VBZ2FpbnN0TW9ydGdhZ2VPZkhvdXNlUHJvcGVydHkiOjAsCiAgICAgICAgICAgICAgICAgICJNb250aFNDb25zdW1wdGlvbkluZGlnZW5vdXMiOjAsCiAgICAgICAgICAgICAgICAgICJBZHZhbmNlUGF5bWVudE9mVGF4IjowLAogICAgICAgICAgICAgICAgICAiSW52ZXN0bWVudHNJblN1YnNpZGlhcnlDb21wYW5pZXNBZmZpbGlhdGVzIjowLAogICAgICAgICAgICAgICAgICAiRGVwb3NpdHMiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlckNvbnN1bWFibGVTcGFyZXNJbXBvcnRlZCI6MCwKICAgICAgICAgICAgICAgICAgIkNvbnN1bWFibGVTcGFyZXNJbmRpZ2Vub3VzQW1vdW50IjowLAogICAgICAgICAgICAgICAgICAiRXN0aW1hdGVkR3Jvc3NUdXJub3Zlck5leHRZZWFyIjowLAogICAgICAgICAgICAgICAgICAiTmV0QmxvY2siOjAsCiAgICAgICAgICAgICAgICAgICJGaW5pc2hlZEdvb2RzQW1vdW50IjowLAogICAgICAgICAgICAgICAgICAiU2VsbGluZ0dlbkFkbUV4cGVuc2VzIjowLAogICAgICAgICAgICAgICAgICAiTGFuZCI6MCwKICAgICAgICAgICAgICAgICAgIkdyb3NzQmxvY2siOjAsCiAgICAgICAgICAgICAgICAgICJSZWNlaXZhYmxlc0RvbWVzdGljT3RoZXJUaGFuRGVmZXJyZWRFeHBvcnRzSW5jbEJpbGxzUHVyY2hhc2VkRGlzY291bnRlZEJ5QmFua3NBbW91bnQiOjAsCiAgICAgICAgICAgICAgICAgICJMaW1pdHNGcm9tT3RoZXJCYW5rcyI6MCwKICAgICAgICAgICAgICAgICAgIk1vbnRoU0Nvc3RPZlNhbGVzIjowLAogICAgICAgICAgICAgICAgICAiRGVwcmVjaWF0aW9uIjowLAogICAgICAgICAgICAgICAgICAiV29ya2luZ0NhcGl0YWxHYXAiOjAsCiAgICAgICAgICAgICAgICAgICJNYXhpbXVtUGVybWlzc2libGVCYW5rRmluYW5jZUxvd2VyT2Y2T3I3IjowLAogICAgICAgICAgICAgICAgICAiRGVidG9yc01vcmVUaGFuNk1vbnRocyI6MCwKICAgICAgICAgICAgICAgICAgIk1wYmZBc1BlclR1cm5vdmVyTWV0aG9kIjowLAogICAgICAgICAgICAgICAgICAiVGF4YXRpb24iOjAsCiAgICAgICAgICAgICAgICAgICJQcm9maXRBZnRlclRheCI6MCwKICAgICAgICAgICAgICAgICAgIk1hbnVmYWN0dXJpbmdFeHBlbnNlcyI6MCwKICAgICAgICAgICAgICAgICAgIlJhd01hdGVyaWFsSW1wb3J0ZWQiOjAsCiAgICAgICAgICAgICAgICAgICJTdWJUb3RhbE90aGVyQ29uc3VtYWJsZVNwYXJlcyI6MCwKICAgICAgICAgICAgICAgICAgIkJ1aWxkaW5nIjowLAogICAgICAgICAgICAgICAgICAiQWNjdW11bGF0ZWRMb3NzZXNQcmVsaW1pbmFyeUV4cGVuc2VzTWlzY2VsbGFuZW91c0V4cGVuZGl0dXJlTm90V09mZk90aGVyRGVmZXJyZWRSZXZlbnVlRXhwZW5zZXMiOjAsCiAgICAgICAgICAgICAgICAgICJNb250aFNDb3N0T2ZQcm9kdWN0aW9uIjowLAogICAgICAgICAgICAgICAgICAiRGl2aWRlbmRzIjpbCiAgICAgICAgICAgICAgICAgICAgIDAsCiAgICAgICAgICAgICAgICAgICAgIDAKICAgICAgICAgICAgICAgICAgXSwKICAgICAgICAgICAgICAgICAgIkRlZmVycmVkUmVjZWl2YWJsZXNNYXR1cml0eUV4Y2VlZGluZzFZZWFyIjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJOb25DdXJyZW50QXNzZXRzU3VycGx1c0ZvckZ1dHVyZUV4cGFuc2lvbkxvYW5zQWR2YW5jZXNOb25DdXJyZW50SW5OYXR1cmVJY2RTRHVlc0Zyb21EaXJlY3RvcnMiOjAsCiAgICAgICAgICAgICAgICAgICJDcmVkaXRvcnNGb3JQdXJjaGFzZXNBbW91bnQiOjAsCiAgICAgICAgICAgICAgICAgICJXb3JraW5nQ2FwaXRhbFJlcXVpcmVtZW50MjVPZkVzdGltYXRlZEdyb3NzVHVybm92ZXIiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlckluY29tZUV4cGVuc2VzTmV0IjowLAogICAgICAgICAgICAgICAgICAiRnVuZHNGcm9tT3BlcmF0aW9ucyI6MCwKICAgICAgICAgICAgICAgICAgIlJlY2VpdmFibGVzT3RoZXJUaGFuRGVmZXJyZWRFeHBvcnRzSW5jbEJpbGxzUHVyY2hhc2VkRGlzY291bnRlZEJ5QmFua3MiOjAsCiAgICAgICAgICAgICAgICAgICJTdG9ja0luUHJvY2VzcyI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyRml4ZWRBc3NldHMiOjAsCiAgICAgICAgICAgICAgICAgICJDYXNoQW5kQmFua0JhbGFuY2VzIjowLAogICAgICAgICAgICAgICAgICAiRml4ZWREZXBvc2l0c1dpdGhCYW5rcyI6MCwKICAgICAgICAgICAgICAgICAgIlN1YlRvdGFsSW52ZW50b3J5IjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJJbnZlc3RtZW50c0ludmVzdG1lbnRGb3JBY3F1aXNpdGlvbiI6MCwKICAgICAgICAgICAgICAgICAgIlByb3Zpc2lvbkZvck5wYSI6MCwKICAgICAgICAgICAgICAgICAgIk1heFBvc3NpYmxlRmluYW5jZUZyb21DZnBsIjowLAogICAgICAgICAgICAgICAgICAiQWR2YW5jZXNUb1N1cHBsaWVyc09mUmF3TWF0ZXJpYWwiOjAsCiAgICAgICAgICAgICAgICAgICJBZHZhbmNlUmVjZWl2YWJsZUluQ2FzaE9yS2luZCI6MCwKICAgICAgICAgICAgICAgICAgIkV4cG9ydFJlY2VpdmFibGVzSW5jbHVkaW5nQmlsbFB1cmNoYXNlZEFuZERpc2NvdW50ZWQiOjAsCiAgICAgICAgICAgICAgICAgICJNb250aFNEb21lc3RpY0luY29tZSI6MCwKICAgICAgICAgICAgICAgICAgIkV4cG9ydFJlY3ZJbmNsQmlsbHNQdXJjaGFzZWREaXNjb3VudGVkQnlCYW5rc0Ftb3VudCI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyQ3VycmVudExpYWJpbGl0aWVzT3RoZXJUaGFuQmFua0JvcnJvd2luZ3NUbEluc3RhbGxtZW50c0R1ZVdpdGhpbk9uZVllYXIiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlcnMiOjAsCiAgICAgICAgICAgICAgICAgICJJdGVtM01pbnVzSXRlbTQiOjAsCiAgICAgICAgICAgICAgICAgICJDYXBpdGFsV2lwIjowLAogICAgICAgICAgICAgICAgICAiSXRlbTNNaW51c0l0ZW01IjowLAogICAgICAgICAgICAgICAgICAiRGVmZXJyZWRSZXZlbnVlRXhwZW5kaXR1cmUiOjAsCiAgICAgICAgICAgICAgICAgICJDb25zdW1hYmxlU3BhcmVzSW1wb3J0ZWRBbW91bnQiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlckN1cnJlbnRBc3NldHMiOjAsCiAgICAgICAgICAgICAgICAgICJTdG9ja0luUHJvY2Vzc0Ftb3VudCI6MCwKICAgICAgICAgICAgICAgICAgIlRvdGFsT3RoZXJOb25DdXJyZW50QXNzZXRzIjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJDb25zdW1hYmxlU3BhcmVzSW5kaWdlbm91cyI6MCwKICAgICAgICAgICAgICAgICAgIlJhd01hdGVyaWFsSW5kaWdlbm91cyI6MCwKICAgICAgICAgICAgICAgICAgIkNvc3RzSW50ZXJlc3RGaW5hbmNlQ2hhcmdlcyI6MCwKICAgICAgICAgICAgICAgICAgIk1hcmdpbk1vbmV5S2VwdFdpdGhCYW5rcyI6MCwKICAgICAgICAgICAgICAgICAgIlJhd01hdGVyaWFsSW1wb3J0ZWRBbW91bnQiOjAsCiAgICAgICAgICAgICAgICAgICJMZXNzQWNjdW11bGF0ZWREZXByZWNpYXRpb24iOjAsCiAgICAgICAgICAgICAgICAgICJBY3R1YWxQcm9qZWN0ZWROd2MiOjAsCiAgICAgICAgICAgICAgICAgICJJbnZlc3RtZW50c090aGVyVGhhbkxvbmdUZXJtIjowLAogICAgICAgICAgICAgICAgICAiRHVlRnJvbVN1YnNpZGlhcmllcyI6MCwKICAgICAgICAgICAgICAgICAgIlN1bmRyeURlcG9zaXQiOjAsCiAgICAgICAgICAgICAgICAgICJSZXRlbnRpb25Nb25leVNlY3VyaXR5RGVwb3NpdCI6MCwKICAgICAgICAgICAgICAgICAgIlRvdGFsQ3VycmVudEFzc2V0cyI6MCwKICAgICAgICAgICAgICAgICAgIlRvdGFsTGlhYmlsaXRpZXNUb3RhbEFzc2V0cyI6MCwKICAgICAgICAgICAgICAgICAgIk1vbnRoU0NvbnN1bXB0aW9uQ29uc3VtYWJsZVNwYXJlc0luZGlnZW5vdXMiOjAsCiAgICAgICAgICAgICAgICAgICJFeHBlbnNlc09uU3RvY2tzUHVyY2hhc2VzIjowLAogICAgICAgICAgICAgICAgICAiTW9kdmF0Q3JlZGl0UmVjZWl2YWJsZSI6MAogICAgICAgICAgICAgICB9CiAgICAgICAgICAgIH0KICAgICAgICAgfSwKICAgICAgICAgewogICAgICAgICAgICAieWVhciI6MCwKICAgICAgICAgICAgIlByb2ZpdEFuZExvc3MiOnsKICAgICAgICAgICAgICAgIlByb2ZpdEJlZm9yZVRheExvc3NQYnQiOjAsCiAgICAgICAgICAgICAgICJBZGRPcGVuaW5nU3RvY2tJblByb2Nlc3MiOjAsCiAgICAgICAgICAgICAgICJQcmVsaUV4cE9uZVRpbWVFeHBlbnNlc1dyaXR0ZW5PZmYiOjAsCiAgICAgICAgICAgICAgICJQYml0QXNPZkdyb3NzU2FsZXMiOjAsCiAgICAgICAgICAgICAgICJPcGVyYXRpbmdQcm9maXRCZWZvcmVUYXhPcGJ0IjowLAogICAgICAgICAgICAgICAiTmV0TG9zc09uRm9yZWlnbkN1cnJlbmN5VHJhbnNsYXRpb25BbmRUcmFuc2FjdGlvbnNMb3NzRHVlVG9GaXJlIjowLAogICAgICAgICAgICAgICAiRXhwb3J0SW5jZW50aXZlcyI6MCwKICAgICAgICAgICAgICAgIkV4dHJhb3JkaW5hcnlFeHBlbnNlc0FkanVzdG1lbnRzIjowLAogICAgICAgICAgICAgICAiTGVzc0V4Y2lzZUR1dHkiOjAsCiAgICAgICAgICAgICAgICJDb3N0T2ZTYWxlcyI6MCwKICAgICAgICAgICAgICAgIkFkanVzdGVkUGF0RXhjbEV4dHJhb3JkaW5hcnlJdGVtcyI6MCwKICAgICAgICAgICAgICAgIlByb3Zpc2lvbkZvclRheGVzRGVmZmVyZWRUYXhlcyI6MCwKICAgICAgICAgICAgICAgIk5vbk9wZXJhdGluZ0luY29tZUZyb21TdWJzaWRpYXJpZXMiOjAsCiAgICAgICAgICAgICAgICJNaXNjRXhwV3JpdHRlbk9mZiI6MCwKICAgICAgICAgICAgICAgIkZvcmV4R2FpbnMiOjAsCiAgICAgICAgICAgICAgICJFcXVpdHlEaXZpZGVuZFBhaWRBbW91bnQiOjAsCiAgICAgICAgICAgICAgICJDb3N0T2ZTYWxlc1NnYSI6MCwKICAgICAgICAgICAgICAgIlByb2ZpdEJlZm9yZUludGVyZXN0VGF4UGJpdCI6MCwKICAgICAgICAgICAgICAgIlN1YlRvdGFsU2FsZXMiOjAsCiAgICAgICAgICAgICAgICJUYXhQYWlkIjowLAogICAgICAgICAgICAgICAiSW50ZXJlc3RQYXltZW50VG9GSXMiOnsKICAgICAgICAgICAgICAgICAgIkludGVyZXN0V2MiOjAsCiAgICAgICAgICAgICAgICAgICJJbnRlcmVzdFRlcm1Mb2FucyI6MAogICAgICAgICAgICAgICB9LAogICAgICAgICAgICAgICAiR3Jvc3NTYWxlcyI6MCwKICAgICAgICAgICAgICAgIk5ldFNhbGVzIjowLAogICAgICAgICAgICAgICAiRHV0eURyYXdiYWNrIjowLAogICAgICAgICAgICAgICAiV2VhbHRoVGF4IjowLAogICAgICAgICAgICAgICAiQ29zdE9mU2FsZXNBc09mR3Jvc3NJbmNvbWUiOjAsCiAgICAgICAgICAgICAgICJQcm92aXNpb25Gb3JUYXhlc1RvdGFsIjowLAogICAgICAgICAgICAgICAiRGVwcmVjaWF0aW9uIjowLAogICAgICAgICAgICAgICAiUHJvdkZvckRvdWJEZWJ0c0RpbUluVGhlVmFsT2ZJbnYiOjAsCiAgICAgICAgICAgICAgICJSYXdNYXRlcmlhbHMiOnsKICAgICAgICAgICAgICAgICAgIkluZGlnZW5vdXMiOjAsCiAgICAgICAgICAgICAgICAgICJJbXBvcnRlZCI6MAogICAgICAgICAgICAgICB9LAogICAgICAgICAgICAgICAiRXF1aXR5RGl2aWRlbmRQYWlkUmF0ZSI6MCwKICAgICAgICAgICAgICAgIlBhcnRuZXJzV2l0aGRyYXdhbCI6MCwKICAgICAgICAgICAgICAgIlRheFJlZnVuZCI6MCwKICAgICAgICAgICAgICAgIkNvc3RPZlRyYWRpbmdHb29kcyI6MCwKICAgICAgICAgICAgICAgIkRpdmlkZW5kUHJlZmVyZW5jZSI6MCwKICAgICAgICAgICAgICAgIlRvdGFsT3BlcmF0aW5nSW5jb21lIjowLAogICAgICAgICAgICAgICAiQWRkT3BlbmluZ1N0b2NrT2ZGaW5pc2hlZEdvb2RzIjowLAogICAgICAgICAgICAgICAiRXhwb3J0U2FsZXMiOjAsCiAgICAgICAgICAgICAgICJTZWxsaW5nR2VuZXJhbEFkbUV4cGVuc2VzIjowLAogICAgICAgICAgICAgICAiUmV0YWluZWRQcm9maXQiOjAsCiAgICAgICAgICAgICAgICJEaXJlY3RMYWJvdXIiOjAsCiAgICAgICAgICAgICAgICJEZXJpdmF0aXZlTG9zc2VzQm9va2VkIjowLAogICAgICAgICAgICAgICAiVG90YWxOb25PcGVyYXRpbmdJbmNvbWUiOjAsCiAgICAgICAgICAgICAgICJDT1BBc09mR3Jvc3NJbmNvbWUiOjAsCiAgICAgICAgICAgICAgICJJbnRlcmVzdFBheW1lbnRUb0JhbmtzIjp7CiAgICAgICAgICAgICAgICAgICJJbnRlcmVzdFdjIjowLAogICAgICAgICAgICAgICAgICAiSW50ZXJlc3RUZXJtTG9hbnMiOjAKICAgICAgICAgICAgICAgfSwKICAgICAgICAgICAgICAgIlByb3Zpc2lvbnNFeHBlbnNlc1dyaXR0ZW5CYWNrIjowLAogICAgICAgICAgICAgICAiSW50ZXJlc3RPdGhlckZpbmFuY2VDaGFyZ2VzIjowLAogICAgICAgICAgICAgICAiT3BidEFzT2ZHcm9zc0luY29tZSI6MCwKICAgICAgICAgICAgICAgIkJhbmtDaGFyZ2VzIjowLAogICAgICAgICAgICAgICAiVG90YWxFeHRyYW9yZGluYXJ5SXRlbXMiOjAsCiAgICAgICAgICAgICAgICJEaXZpZGVuZFRheCI6MCwKICAgICAgICAgICAgICAgIkdyb3NzRG9tZXN0aWNTYWxlcyI6MCwKICAgICAgICAgICAgICAgIkRlZHVjdENsb3NpbmdTdG9ja09mRmluaXNoZWRHb29kcyI6MCwKICAgICAgICAgICAgICAgIlRvdGFsTm9uT3BlcmF0aW5nRXhwZW5zZXMiOjAsCiAgICAgICAgICAgICAgICJOZXRPZk5vbk9wZXJhdGluZ0luY29tZUV4cGVuc2VzIjowLAogICAgICAgICAgICAgICAiT3RoZXJzIjowLAogICAgICAgICAgICAgICAiSW50dEZpbkNoYXJnZXNBc09mR3Jvc3NTYWxlcyI6MCwKICAgICAgICAgICAgICAgIlJlcGFpcnNNYWludGVuYW5jZSI6MCwKICAgICAgICAgICAgICAgIlByb2ZpdEJlZm9yZUludGVyZXN0RGVwcmVjaWF0aW9uVGF4UGJpZHQiOjAsCiAgICAgICAgICAgICAgICJJbnRlcmVzdE9uRGVwb3NpdHNEaXZpZGVuZFJlY2VpdmVkIjowLAogICAgICAgICAgICAgICAiT3RoZXJJbmNvbWUiOjAsCiAgICAgICAgICAgICAgICJQYXRBc09mR3Jvc3NJbmNvbWUiOjAsCiAgICAgICAgICAgICAgICJBZGRUcmFkaW5nT3RoZXJPcGVyYXRpbmdJbmNvbWUiOjAsCiAgICAgICAgICAgICAgICJDb3N0T2ZQcm9kdWN0aW9uIjowLAogICAgICAgICAgICAgICAiRGVkdWN0Q2xvc2luZ1N0b2NrSW5Qcm9jZXNzIjowLAogICAgICAgICAgICAgICAiT3RoZXJNYW51ZmFjdHVyaW5nRXhwZW5zZXMiOjAsCiAgICAgICAgICAgICAgICJQcm9maXRPblNhbGVPZkFzc2V0c0ludmVzdG1lbnRzIjowLAogICAgICAgICAgICAgICAiT3RoZXJTcGFyZXMiOnsKICAgICAgICAgICAgICAgICAgIkluZGlnZW5vdXMiOjAsCiAgICAgICAgICAgICAgICAgICJJbXBvcnRlZCI6MAogICAgICAgICAgICAgICB9LAogICAgICAgICAgICAgICAiTWlzY0luY29tZSI6MCwKICAgICAgICAgICAgICAgIlBvd2VyRnVlbCI6MCwKICAgICAgICAgICAgICAgIkxvc3NPblNhbGVPZkludmVzdG1lbnRzIjowLAogICAgICAgICAgICAgICAiRXh0cmFvcmRpbmFyeUluY29tZUFkanVzdG1lbnRzIjowLAogICAgICAgICAgICAgICAiTG9zc09uU2FsZU9mRmEiOjAsCiAgICAgICAgICAgICAgICJOZXRQcm9maXRMb3NzUGF0IjowLAogICAgICAgICAgICAgICAiUHJvdmlzaW9uRm9yVGF4ZXNDdXJyZW50UGVyaW9kIjowCiAgICAgICAgICAgIH0sCiAgICAgICAgICAgICJCYWxhbmNlU2hlZXQiOnsKICAgICAgICAgICAgICAgIkxpYWJpbGl0aWVzIjp7CiAgICAgICAgICAgICAgICAgICJTaGFyZUNhcGl0YWxQYWlkVXAiOjAsCiAgICAgICAgICAgICAgICAgICJTaG9ydFRlcm1Cb3Jyb3dpbmdzQ29tbWVyY2lhbFBhcGVyIjowLAogICAgICAgICAgICAgICAgICAiQm9ycm93aW5nc0Zyb21TdWJzaWRpYXJpZXNBZmZpbGlhdGVzUXVhc2lFcXVpdHkiOjAsCiAgICAgICAgICAgICAgICAgICJUb3RhbE5ldFdvcnRoIjowLAogICAgICAgICAgICAgICAgICAiRGVwb3NpdHNEdWVGb3JSZXBheW1lbnREdWVXaXRoaW4xWWVhciI6MCwKICAgICAgICAgICAgICAgICAgIkdyYXR1aXR5TGlhYmlsaXR5Tm90UHJvdmlkZWRGb3IiOjAsCiAgICAgICAgICAgICAgICAgICJTdWJUb3RhbFJlc2VydmVzIjowLAogICAgICAgICAgICAgICAgICAiQ2FwaXRhbFN1YnNpZHkiOjAsCiAgICAgICAgICAgICAgICAgICJTdW5kcnlDcmVkaXRvcnNUcmFkZSI6MCwKICAgICAgICAgICAgICAgICAgIkludmVzdG1lbnRBbGxvd2FuY2VVdGlsaXphdGlvblJlc2VydmUiOjAsCiAgICAgICAgICAgICAgICAgICJUb3RhbEN1cnJlbnRMaWFiaWxpdGllcyI6MCwKICAgICAgICAgICAgICAgICAgIkNvbnRpbmdlbnRMaWFiaWxpdGllcyI6MCwKICAgICAgICAgICAgICAgICAgIlVuc2VjdXJlZExvYW5zIjowLAogICAgICAgICAgICAgICAgICAiR3VhcmFudGVlc0lzc3VlZFJlbGF0aW5nVG9CdXNpbmVzcyI6MCwKICAgICAgICAgICAgICAgICAgIlN1YlRvdGFsQmFua0xpYWJpbGl0aWVzIjowLAogICAgICAgICAgICAgICAgICAiSW50ZXJlc3RBY2NCdXROb3REdWUiOjAsCiAgICAgICAgICAgICAgICAgICJEZWJlbnR1cmVzIjowLAogICAgICAgICAgICAgICAgICAiUGFydG5lcnNDYXBpdGFsUHJvcHJpZXRvclNDYXBpdGFsIjowLAogICAgICAgICAgICAgICAgICAiU3RhdHV0b3J5QW5kQ2FwaXRhbFJlc2VydmVzIjowLAogICAgICAgICAgICAgICAgICAiQXJyZWFyc09mQ3VtdWxhdGl2ZURpdmlkZW5kcyI6MCwKICAgICAgICAgICAgICAgICAgIlRlcm1EZXBvc2l0cyI6MCwKICAgICAgICAgICAgICAgICAgIlVuY2xhaW1lZERpdmlkZW5kIjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJSZXNlcnZlc0V4Y2x1ZGluZ1Byb3Zpc2lvbnMiOjAsCiAgICAgICAgICAgICAgICAgICJMY3MiOjAsCiAgICAgICAgICAgICAgICAgICJQcmVmU2hhcmVzUG9ydGlvblJlZGVlbWFibGVBZnRlcjFZciI6MCwKICAgICAgICAgICAgICAgICAgIlRlcm1Mb2Fuc0Zyb21GaXMiOjAsCiAgICAgICAgICAgICAgICAgICJTdXJwbHVzT3JEZWZpY2l0SW5QTEFjY291bnQiOjAsCiAgICAgICAgICAgICAgICAgICJQcm9wb3NlZERpdmlkZW5kIjowLAogICAgICAgICAgICAgICAgICAiSW5zdGFsbG1lbnRzT2ZUZXJtTG9hbnNEZWJlbnR1cmVzRHBnc0V0Y0R1ZVdpdGhpbjFZZWFyIjowLAogICAgICAgICAgICAgICAgICAiT2ZJQW5kSWlJbldoaWNoQmlsbFB1cmNoYXNlZERpc2MiOjAsCiAgICAgICAgICAgICAgICAgICJGcm9tT3RoZXJCYW5rcyI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyQ3VycmVudExpYWJpbGl0aWVzUHJvdmlzaW9uc0R1ZVdpdGhpbjFZZWFyIjowLAogICAgICAgICAgICAgICAgICAiU3ViVG90YWxPdGhlckxpYWJpbGl0aWVzIjowLAogICAgICAgICAgICAgICAgICAiV2N0bCI6MCwKICAgICAgICAgICAgICAgICAgIlRvdGFsU2hhcmVDYXBpdGFsIjowLAogICAgICAgICAgICAgICAgICAiVG90YWxUZXJtTGlhYmlsaXRpZXMiOjAsCiAgICAgICAgICAgICAgICAgICJQcm92aXNpb25Gb3JUYXhhdGlvbiI6MCwKICAgICAgICAgICAgICAgICAgIlByb3Zpc2lvbkZvck5wYSI6MCwKICAgICAgICAgICAgICAgICAgIlByZWZlcmVuY2VTaGFyZXNSZWRlZW1hYmxlV2l0aGluMVllYXIiOjAsCiAgICAgICAgICAgICAgICAgICJUb3RhbExpYWJpbGl0aWVzIjowLAogICAgICAgICAgICAgICAgICAiR2VuZXJhbFJlc2VydmUiOjAsCiAgICAgICAgICAgICAgICAgICJBZHZhbmNlc1BheW1lbnRzRnJvbUN1c3RvbWVyc0RlcG9zaXRzRnJvbURlYWxlcnMiOjAsCiAgICAgICAgICAgICAgICAgICJGcm9tQXBwbGljYW50QmFua0NjV2NkbCI6MCwKICAgICAgICAgICAgICAgICAgIlRheE9uSW50ZXJpbURpdmlkZW5kUGF5YWJsZSI6MCwKICAgICAgICAgICAgICAgICAgIkd1YXJhbnRlZXNJc3N1ZWRGb3JHcm91cENvbXBhbmllcyI6MCwKICAgICAgICAgICAgICAgICAgIkFsbE90aGVyQ29udGluZ2VudExpYWJpbGl0aWVzSW5jbGRnQmlsbHNQdXJjaGFzZWRVbmRlckxjIjowLAogICAgICAgICAgICAgICAgICAiU2hvcnRUZXJtQm9ycm93aW5nc0Zyb21Bc3NvY2lhdGVzR3JvdXBDb25jZXJucyI6MCwKICAgICAgICAgICAgICAgICAgIkRpc3B1dGVkRXhjaXNlQ3VzdG9tc0luY29tZVRheFNhbGVzVGF4TGlhYmlsaXRpZXMiOjAsCiAgICAgICAgICAgICAgICAgICJSZXZhbHVhdGlvblJlc2VydmUiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlclN0YXR1dG9yeUxpYWJpbGl0aWVzRHVlV2l0aGluT25lWWVhciI6MCwKICAgICAgICAgICAgICAgICAgIkR1ZVRvU3Vic2lkaWFyeUNvbXBhbmllc0FmZmlsaWF0ZXMiOjAsCiAgICAgICAgICAgICAgICAgICJUZXJtTGlhYmlsaXRpZXMiOjAsCiAgICAgICAgICAgICAgICAgICJTaGFyZUFwcGxpY2F0aW9uRmluYWxpemVkRm9yQWxsb3RtZW50IjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJMaWFiaWxpdGllcyI6MCwKICAgICAgICAgICAgICAgICAgIlNob3J0VGVybUJvcnJvd2luZ3NGcm9tT3RoZXJzIjowLAogICAgICAgICAgICAgICAgICAiRGVmZXJyZWRUYXhMaWFiaWxpdHkiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlclRlcm1MaWFiaWxpdGllcyI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyTG9hbkFkdmFuY2VzIjowLAogICAgICAgICAgICAgICAgICAiUHJvdmlzaW9uRm9yTGVhdmVFbmNhc2htZW50R3JhdHVpdHkiOjAsCiAgICAgICAgICAgICAgICAgICJUb3RhbFJlcGF5bWVudHNEdWVXaXRoaW4xWWVhciI6MCwKICAgICAgICAgICAgICAgICAgIkRlcG9zaXRGcm9tRGVhbGVyc09ubHlJZkNvbnNpZGVyZWRBc0F2YWlsYWJsZUZvckxvbmdUZXJtIjowLAogICAgICAgICAgICAgICAgICAiU2hhcmVQcmVtaXVtQUMiOjAsCiAgICAgICAgICAgICAgICAgICJUb3RhbE91dHNpZGVMaWFiaWxpdGllc1RvbCI6MCwKICAgICAgICAgICAgICAgICAgIlRlcm1Mb2Fuc0V4Y2x1ZGluZ0luc3RhbGxtZW50c1BheWFibGVXaXRoaW5PbmVZZWFyIjowCiAgICAgICAgICAgICAgIH0sCiAgICAgICAgICAgICAgICJBc3NldHMiOnsKICAgICAgICAgICAgICAgICAgIkludGVyZXN0QWNjcnVlZCI6MCwKICAgICAgICAgICAgICAgICAgIlRhbmdpYmxlTmV0d29ydGgiOjAsCiAgICAgICAgICAgICAgICAgICJGaW5pc2hlZEdvb2RzIjowLAogICAgICAgICAgICAgICAgICAiSW5jb21lIjowLAogICAgICAgICAgICAgICAgICAiTW9udGhTUHVyY2hhc2VzIjowLAogICAgICAgICAgICAgICAgICAiRnVybml0dXJlRml4dHVyZXMiOjAsCiAgICAgICAgICAgICAgICAgICJUb3RhbEFzc2V0cyI6MCwKICAgICAgICAgICAgICAgICAgIk1vbnRoU0NvbnN1bXB0aW9uQ29uc3VtYWJsZVNwYXJlc0ltcG9ydGVkIjowLAogICAgICAgICAgICAgICAgICAiUmF3TWF0ZXJpYWxJbmRpZ2Vub3VzQW1vdW50IjowLAogICAgICAgICAgICAgICAgICAiR292dE90aGVyU2VjdXJpdGllcyI6MCwKICAgICAgICAgICAgICAgICAgIlZlaGljbGVzIjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJTdG9ja3MiOjAsCiAgICAgICAgICAgICAgICAgICJNaW5TdGlwdWxhdGVkTmV0V29ya2luZ0NhcGl0YWwyNU9mVG90YWxDdXJyZW50QXNzZXRzRXhjbHVkaW5nRXhwb3J0UmVjZWl2YWJsZXMiOjAsCiAgICAgICAgICAgICAgICAgICJNb250aFNFeHBvcnRJbmNvbWUiOjAsCiAgICAgICAgICAgICAgICAgICJEZWZlcnJlZFRheEFzc2V0IjowLAogICAgICAgICAgICAgICAgICAiTW9udGhTQ29uc3VtcHRpb25JbXBvcnRlZCI6MCwKICAgICAgICAgICAgICAgICAgIlBsYW50TWFjaGluZXJ5IjowLAogICAgICAgICAgICAgICAgICAiQWR2YW5jZUFnYWluc3RNb3J0Z2FnZU9mSG91c2VQcm9wZXJ0eSI6MCwKICAgICAgICAgICAgICAgICAgIk1vbnRoU0NvbnN1bXB0aW9uSW5kaWdlbm91cyI6MCwKICAgICAgICAgICAgICAgICAgIkFkdmFuY2VQYXltZW50T2ZUYXgiOjAsCiAgICAgICAgICAgICAgICAgICJJbnZlc3RtZW50c0luU3Vic2lkaWFyeUNvbXBhbmllc0FmZmlsaWF0ZXMiOjAsCiAgICAgICAgICAgICAgICAgICJEZXBvc2l0cyI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyQ29uc3VtYWJsZVNwYXJlc0ltcG9ydGVkIjowLAogICAgICAgICAgICAgICAgICAiQ29uc3VtYWJsZVNwYXJlc0luZGlnZW5vdXNBbW91bnQiOjAsCiAgICAgICAgICAgICAgICAgICJFc3RpbWF0ZWRHcm9zc1R1cm5vdmVyTmV4dFllYXIiOjAsCiAgICAgICAgICAgICAgICAgICJOZXRCbG9jayI6MCwKICAgICAgICAgICAgICAgICAgIkZpbmlzaGVkR29vZHNBbW91bnQiOjAsCiAgICAgICAgICAgICAgICAgICJTZWxsaW5nR2VuQWRtRXhwZW5zZXMiOjAsCiAgICAgICAgICAgICAgICAgICJMYW5kIjowLAogICAgICAgICAgICAgICAgICAiR3Jvc3NCbG9jayI6MCwKICAgICAgICAgICAgICAgICAgIlJlY2VpdmFibGVzRG9tZXN0aWNPdGhlclRoYW5EZWZlcnJlZEV4cG9ydHNJbmNsQmlsbHNQdXJjaGFzZWREaXNjb3VudGVkQnlCYW5rc0Ftb3VudCI6MCwKICAgICAgICAgICAgICAgICAgIkxpbWl0c0Zyb21PdGhlckJhbmtzIjowLAogICAgICAgICAgICAgICAgICAiTW9udGhTQ29zdE9mU2FsZXMiOjAsCiAgICAgICAgICAgICAgICAgICJEZXByZWNpYXRpb24iOjAsCiAgICAgICAgICAgICAgICAgICJXb3JraW5nQ2FwaXRhbEdhcCI6MCwKICAgICAgICAgICAgICAgICAgIk1heGltdW1QZXJtaXNzaWJsZUJhbmtGaW5hbmNlTG93ZXJPZjZPcjciOjAsCiAgICAgICAgICAgICAgICAgICJEZWJ0b3JzTW9yZVRoYW42TW9udGhzIjowLAogICAgICAgICAgICAgICAgICAiTXBiZkFzUGVyVHVybm92ZXJNZXRob2QiOjAsCiAgICAgICAgICAgICAgICAgICJUYXhhdGlvbiI6MCwKICAgICAgICAgICAgICAgICAgIlByb2ZpdEFmdGVyVGF4IjowLAogICAgICAgICAgICAgICAgICAiTWFudWZhY3R1cmluZ0V4cGVuc2VzIjowLAogICAgICAgICAgICAgICAgICAiUmF3TWF0ZXJpYWxJbXBvcnRlZCI6MCwKICAgICAgICAgICAgICAgICAgIlN1YlRvdGFsT3RoZXJDb25zdW1hYmxlU3BhcmVzIjowLAogICAgICAgICAgICAgICAgICAiQnVpbGRpbmciOjAsCiAgICAgICAgICAgICAgICAgICJBY2N1bXVsYXRlZExvc3Nlc1ByZWxpbWluYXJ5RXhwZW5zZXNNaXNjZWxsYW5lb3VzRXhwZW5kaXR1cmVOb3RXT2ZmT3RoZXJEZWZlcnJlZFJldmVudWVFeHBlbnNlcyI6MCwKICAgICAgICAgICAgICAgICAgIk1vbnRoU0Nvc3RPZlByb2R1Y3Rpb24iOjAsCiAgICAgICAgICAgICAgICAgICJEaXZpZGVuZHMiOlsKICAgICAgICAgICAgICAgICAgICAgMCwKICAgICAgICAgICAgICAgICAgICAgMAogICAgICAgICAgICAgICAgICBdLAogICAgICAgICAgICAgICAgICAiRGVmZXJyZWRSZWNlaXZhYmxlc01hdHVyaXR5RXhjZWVkaW5nMVllYXIiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlck5vbkN1cnJlbnRBc3NldHNTdXJwbHVzRm9yRnV0dXJlRXhwYW5zaW9uTG9hbnNBZHZhbmNlc05vbkN1cnJlbnRJbk5hdHVyZUljZFNEdWVzRnJvbURpcmVjdG9ycyI6MCwKICAgICAgICAgICAgICAgICAgIkNyZWRpdG9yc0ZvclB1cmNoYXNlc0Ftb3VudCI6MCwKICAgICAgICAgICAgICAgICAgIldvcmtpbmdDYXBpdGFsUmVxdWlyZW1lbnQyNU9mRXN0aW1hdGVkR3Jvc3NUdXJub3ZlciI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVySW5jb21lRXhwZW5zZXNOZXQiOjAsCiAgICAgICAgICAgICAgICAgICJGdW5kc0Zyb21PcGVyYXRpb25zIjowLAogICAgICAgICAgICAgICAgICAiUmVjZWl2YWJsZXNPdGhlclRoYW5EZWZlcnJlZEV4cG9ydHNJbmNsQmlsbHNQdXJjaGFzZWREaXNjb3VudGVkQnlCYW5rcyI6MCwKICAgICAgICAgICAgICAgICAgIlN0b2NrSW5Qcm9jZXNzIjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJGaXhlZEFzc2V0cyI6MCwKICAgICAgICAgICAgICAgICAgIkNhc2hBbmRCYW5rQmFsYW5jZXMiOjAsCiAgICAgICAgICAgICAgICAgICJGaXhlZERlcG9zaXRzV2l0aEJhbmtzIjowLAogICAgICAgICAgICAgICAgICAiU3ViVG90YWxJbnZlbnRvcnkiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlckludmVzdG1lbnRzSW52ZXN0bWVudEZvckFjcXVpc2l0aW9uIjowLAogICAgICAgICAgICAgICAgICAiUHJvdmlzaW9uRm9yTnBhIjowLAogICAgICAgICAgICAgICAgICAiTWF4UG9zc2libGVGaW5hbmNlRnJvbUNmcGwiOjAsCiAgICAgICAgICAgICAgICAgICJBZHZhbmNlc1RvU3VwcGxpZXJzT2ZSYXdNYXRlcmlhbCI6MCwKICAgICAgICAgICAgICAgICAgIkFkdmFuY2VSZWNlaXZhYmxlSW5DYXNoT3JLaW5kIjowLAogICAgICAgICAgICAgICAgICAiRXhwb3J0UmVjZWl2YWJsZXNJbmNsdWRpbmdCaWxsUHVyY2hhc2VkQW5kRGlzY291bnRlZCI6MCwKICAgICAgICAgICAgICAgICAgIk1vbnRoU0RvbWVzdGljSW5jb21lIjowLAogICAgICAgICAgICAgICAgICAiRXhwb3J0UmVjdkluY2xCaWxsc1B1cmNoYXNlZERpc2NvdW50ZWRCeUJhbmtzQW1vdW50IjowLAogICAgICAgICAgICAgICAgICAiT3RoZXJDdXJyZW50TGlhYmlsaXRpZXNPdGhlclRoYW5CYW5rQm9ycm93aW5nc1RsSW5zdGFsbG1lbnRzRHVlV2l0aGluT25lWWVhciI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVycyI6MCwKICAgICAgICAgICAgICAgICAgIkl0ZW0zTWludXNJdGVtNCI6MCwKICAgICAgICAgICAgICAgICAgIkNhcGl0YWxXaXAiOjAsCiAgICAgICAgICAgICAgICAgICJJdGVtM01pbnVzSXRlbTUiOjAsCiAgICAgICAgICAgICAgICAgICJEZWZlcnJlZFJldmVudWVFeHBlbmRpdHVyZSI6MCwKICAgICAgICAgICAgICAgICAgIkNvbnN1bWFibGVTcGFyZXNJbXBvcnRlZEFtb3VudCI6MCwKICAgICAgICAgICAgICAgICAgIk90aGVyQ3VycmVudEFzc2V0cyI6MCwKICAgICAgICAgICAgICAgICAgIlN0b2NrSW5Qcm9jZXNzQW1vdW50IjowLAogICAgICAgICAgICAgICAgICAiVG90YWxPdGhlck5vbkN1cnJlbnRBc3NldHMiOjAsCiAgICAgICAgICAgICAgICAgICJPdGhlckNvbnN1bWFibGVTcGFyZXNJbmRpZ2Vub3VzIjowLAogICAgICAgICAgICAgICAgICAiUmF3TWF0ZXJpYWxJbmRpZ2Vub3VzIjowLAogICAgICAgICAgICAgICAgICAiQ29zdHNJbnRlcmVzdEZpbmFuY2VDaGFyZ2VzIjowLAogICAgICAgICAgICAgICAgICAiTWFyZ2luTW9uZXlLZXB0V2l0aEJhbmtzIjowLAogICAgICAgICAgICAgICAgICAiUmF3TWF0ZXJpYWxJbXBvcnRlZEFtb3VudCI6MCwKICAgICAgICAgICAgICAgICAgIkxlc3NBY2N1bXVsYXRlZERlcHJlY2lhdGlvbiI6MCwKICAgICAgICAgICAgICAgICAgIkFjdHVhbFByb2plY3RlZE53YyI6MCwKICAgICAgICAgICAgICAgICAgIkludmVzdG1lbnRzT3RoZXJUaGFuTG9uZ1Rlcm0iOjAsCiAgICAgICAgICAgICAgICAgICJEdWVGcm9tU3Vic2lkaWFyaWVzIjowLAogICAgICAgICAgICAgICAgICAiU3VuZHJ5RGVwb3NpdCI6MCwKICAgICAgICAgICAgICAgICAgIlJldGVudGlvbk1vbmV5U2VjdXJpdHlEZXBvc2l0IjowLAogICAgICAgICAgICAgICAgICAiVG90YWxDdXJyZW50QXNzZXRzIjowLAogICAgICAgICAgICAgICAgICAiVG90YWxMaWFiaWxpdGllc1RvdGFsQXNzZXRzIjowLAogICAgICAgICAgICAgICAgICAiTW9udGhTQ29uc3VtcHRpb25Db25zdW1hYmxlU3BhcmVzSW5kaWdlbm91cyI6MCwKICAgICAgICAgICAgICAgICAgIkV4cGVuc2VzT25TdG9ja3NQdXJjaGFzZXMiOjAsCiAgICAgICAgICAgICAgICAgICJNb2R2YXRDcmVkaXRSZWNlaXZhYmxlIjowCiAgICAgICAgICAgICAgIH0KICAgICAgICAgICAgfQogICAgICAgICB9CiAgICAgIF0sCiAgICAgICJOYW1lT2ZUaGVCb3Jyb3dlciI6IiIKICAgfQp9';
}
?>