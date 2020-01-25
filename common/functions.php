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

function getColumns() {
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
		),
		'profitability_analysis_cols' => array(
			'pbditNetsales' => 'PBDIT / NET SALES (%))',
			'netProfitNetSales' => 'NET PROFIT / NET SALES (%)',
			'cashProfitNetSales' => 'CASH PROFIT / NET SALES (%)',
		),
		'financial_position_analysis_cols' => array(
			'TangibleNetWorth' => 'TANGIBLE NETWORTH (TNW)',
		),
		'leverage_analysis_cols' => array(
			'TolTnw' => 'TOL / TNW RATIO',
			'TolAdjTnwAtnw' => 'TOL / ADJ. TNW (ATNW)',
			'DebtPbdit' => 'DEBT /PBDIT',
		),
		'activity_efficiency_analysis_cols' => array(
			'RecievableTurnover' => 'RECEIVABLE TURNOVER DAYS (TOTAL , Inc. DEBTORS > 6 MONTHS)',
		),
		
		// 'CashAndBankBalances' => '',
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
	$InterestPaymentToBanks = $InterestPaymentToBanks['InterestWc'] + $InterestPaymentToBanks['InterestTermLoans'];
	$InterestPaymentToFIs = $InterestPaymentToFIs['InterestWc'] + $InterestPaymentToFIs['InterestTermLoans'];

	$response['TotalOperatingIncome'] = $TotalOperatingIncome = $GrossDomesticSales + $ExportSales - $LessExciseDuty+ $AddTradingOtherOperatingIncome+ $ExportIncentives+ $DutyDrawback+ $Others;

	$response['TotalNonOperatingIncome'] = $InterestOnDepositsDividendReceived + $ForexGains + $NonOperatingIncomeFromSubsidiaries + $TaxRefund + $MiscIncome + $ProfitOnSaleOfAssetsInvestments + $OtherIncome + $ProvisionsExpensesWrittenBack;

	$response['PBDITOperatingProfit'] = $TotalOperatingIncome -($AddOpeningStockInProcessRawMaterials + $OtherSpares + $PowerFuel + $DirectLabour + $OtherManufacturingExpenses+ $Depreciation+ $RepairsMaintenance + 
		$CostOfTradingGoods + $AddOpeningStockInProcess - $DeductClosingStockInProcess + 
		$AddOpeningStockOfFinishedGoods - $DeductClosingStockOfFinishedGoods + $SellingGeneralAdmExpenses ) + $Depreciation;

	$response['Depreciation'] = $Depreciation;

	$response['DeprecationAverageNetFixedAssetsPer'] = ($Depreciation / (($Land+$Building+$Vehicles+$PlantMachinery+$FurnitureFixtures + $OtherFixedAssets + $CapitalWip-$LessAccumulatedDepreciation-$RevaluationReserve)/2))  * 100;

	$response['Interest'] = $InterestPaymentToBanks+$InterestPaymentToFIs+$BankCharges;

	$response['InterestNetSalesPer'] = ($response['Interest']/$TotalOperatingIncome)*100; 

	$response['PbditInterestPer'] = $response['PBDITOperatingProfit']/$response['Interest'];

	$response['NetProfit'] = $TotalOperatingIncome -($AddOpeningStockInProcessRawMaterials + $OtherSpares + $PowerFuel + $DirectLabour + $OtherManufacturingExpenses+ $Depreciation+ $RepairsMaintenance + 
		$CostOfTradingGoods + $AddOpeningStockInProcess - $DeductClosingStockInProcess + 
		$AddOpeningStockOfFinishedGoods - $DeductClosingStockOfFinishedGoods + $SellingGeneralAdmExpenses );

	$response['CashProfit'] = ($TotalOperatingIncome -($AddOpeningStockInProcessRawMaterials+$OtherSpares+$PowerFuel + $DirectLabour + $OtherManufacturingExpenses+ $Depreciation+ $RepairsMaintenance + 
		$CostOfTradingGoods + $AddOpeningStockInProcess - $DeductClosingStockInProcess + 
		$AddOpeningStockOfFinishedGoods - $DeductClosingStockOfFinishedGoods + $SellingGeneralAdmExpenses)) - ($InterestPaymentToBanks + $InterestPaymentToFIs + $BankCharges) + ( $InterestOnDepositsDividendReceived + $ForexGains + $NonOperatingIncomeFromSubsidiaries + $TaxRefund + $MiscIncome + $ProfitOnSaleOfAssetsInvestments + $OtherIncome + $ProvisionsExpensesWrittenBack) - ($LossOnSaleOfInvestments+$LossOnSaleOfFa+$DerivativeLossesBooked+$NetLossOnForeignCurrencyTranslationAndTransactionsLossDueToFire+$PreliExpOneTimeExpensesWrittenOff+$MiscExpWrittenOff +$ProvForDoubDebtsDimInTheValOfInv +$WealthTax) - $TaxPaid - ($ProvisionForTaxesCurrentPeriod + $ProvisionForTaxesDefferedTaxes) +$Depreciation - $ProvisionsExpensesWrittenBack;

	$response['TangibleNetWorth'] = ($PartnersCapitalProprietorSCapital+ $ShareCapitalPaidUp + $ShareApplicationFinalizedForAllotment+$StatutoryAndCapitalReserves+ $GeneralReserve+ $RevaluationReserve+ $OtherReservesExcludingProvisions + $SurplusOrDeficitInPLAccount+ $SharePremiumAC + $CapitalSubsidy + $InvestmentAllowanceUtilizationReserve -$RevaluationReserve)-
	($AccumulatedLossesPreliminaryExpensesMiscellaneousExpenditureNotWOffOtherDeferredRevenueExpenses + $DeferredTaxAsset);

	$response['TolTnw'] = ($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $SundryCreditorsTrade + $ShortTermBorrowingsFromAssociatesGroupConcerns + $ShortTermBorrowingsCommercialPaper + $ShortTermBorrowingsFromOthers + $AdvancesPaymentsFromCustomersDepositsFromDealers + $ProvisionForTaxation + $ProposedDividend + $OtherStatutoryLiabilitiesDueWithinOneYear + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $DepositsDueForRepaymentDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $OtherCurrentLiabilitiesProvisionsDueWithin1Year + $InterestAccButNotDue + $ProvisionForNpa + $ProvisionForLeaveEncashmentGratuity + $UnclaimedDividend + $OtherLiabilities + $DueToSubsidiaryCompaniesAffiliates + $TaxOnInterimDividendPayable + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)/ $response['TangibleNetWorth'];

	$response['TolAdjTnwAtnw'] = ((($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $SundryCreditorsTrade + $ShortTermBorrowingsFromAssociatesGroupConcerns + $ShortTermBorrowingsCommercialPaper + $ShortTermBorrowingsFromOthers + $AdvancesPaymentsFromCustomersDepositsFromDealers + $ProvisionForTaxation + $ProposedDividend + $OtherStatutoryLiabilitiesDueWithinOneYear + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $DepositsDueForRepaymentDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $OtherCurrentLiabilitiesProvisionsDueWithin1Year + $InterestAccButNotDue + $ProvisionForNpa + $ProvisionForLeaveEncashmentGratuity + $UnclaimedDividend + $OtherLiabilities + $DueToSubsidiaryCompaniesAffiliates + $TaxOnInterimDividendPayable + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)) - $BorrowingsFromSubsidiariesAffiliatesQuasiEquity) / ( $response['TangibleNetWorth'] -$InvestmentsInSubsidiaryCompaniesAffiliates + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity);

	$response['DebtPbdit'] = ($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $ShortTermBorrowingsCommercialPaper + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)/ $response['PBDITOperatingProfit'];

	$response['RecievableTurnover'] = (($ReceivablesOtherThanDeferredExportsInclBillsPurchasedDiscountedByBanks+$ExportReceivablesIncludingBillPurchasedAndDiscounted+$RetentionMoneySecurityDeposit+$DebtorsMoreThan6Months)/($GrossDomesticSales+$ExportSales+$AddTradingOtherOperatingIncome))*365;

	$response['CashAndBankBalances'] = $CashAndBankBalances;

	$response['pbditNetsales'] = ($response['PBDITOperatingProfit'] / $response['TotalOperatingIncome']) * 100;
	$response['netProfitNetSales'] = ($response['NetProfit'] / $response['TotalOperatingIncome']) * 100;
	
	$response['cashProfitNetSales'] = ($response['CashProfit'] / $response['TotalOperatingIncome']) * 100;
	return $response;
}


function getNetSales($ProfitAndLoss) {
	extract($ProfitAndLoss);
 $TotalOperatingIncome = $GrossDomesticSales + $ExportSales - $LessExciseDuty+ $AddTradingOtherOperatingIncome+ $ExportIncentives+ $DutyDrawback+ $Others;
 return $TotalOperatingIncome; 
}

function getTotalNonOperatingIncome($ProfitAndLoss) {
	extract($ProfitAndLoss);
	$TotalNonOperatingIncome = $InterestOnDepositsDividendReceived + $ForexGains + $NonOperatingIncomeFromSubsidiaries + $TaxRefund + $MiscIncome + $ProfitOnSaleOfAssetsInvestments + $OtherIncome + $ProvisionsExpensesWrittenBack;
 return $TotalNonOperatingIncome; 
}

function getPBDITOperatingProfit($ProfitAndLoss) {
	$TotalOperatingIncome = getNetSales($ProfitAndLoss);
	extract($ProfitAndLoss);
	$AddOpeningStockInProcessRawMaterials = $RawMaterials['Imported'] + $RawMaterials['Indigenous'];
	$OtherSpares = $OtherSpares['Imported'] + $OtherSpares['Indigenous'] ;
	$PBDITOperatingProfit = $TotalOperatingIncome -($AddOpeningStockInProcessRawMaterials + $OtherSpares + $PowerFuel + $DirectLabour + $OtherManufacturingExpenses+ $Depreciation+ $RepairsMaintenance + 
		$CostOfTradingGoods + $AddOpeningStockInProcess - $DeductClosingStockInProcess + 
		$AddOpeningStockOfFinishedGoods - $DeductClosingStockOfFinishedGoods + $SellingGeneralAdmExpenses ) +
		$Depreciation;
 return $PBDITOperatingProfit; 
}

function getDepreciation($ProfitAndLoss) {
	extract($ProfitAndLoss);
 return $Depreciation; 
}

function  getDeprecationAverageNetFixedAssetsPer($fullArray) {
	extract($fullArray['ProfitAndLoss']);
	extract($fullArray['BalanceSheet']['Liabilities']);
	extract($fullArray['BalanceSheet']['Assets']);
	$DeprecationAverageNetFixedAssetsPer = ($Depreciation / (($Land+$Building+$Vehicles+$PlantMachinery+$FurnitureFixtures + $OtherFixedAssets + $CapitalWip-$LessAccumulatedDepreciation-$RevaluationReserve)/2))  * 100;
 return $DeprecationAverageNetFixedAssetsPer; 
}

function  getInterest($ProfitAndLoss) {
	extract($ProfitAndLoss);
	$InterestPaymentToBanks = array_sum($InterestPaymentToBanks);
	$InterestPaymentToFIs = array_sum($InterestPaymentToFIs);
	$Interest = $InterestPaymentToBanks+$InterestPaymentToFIs+$BankCharges;
 return $Interest; 
}

function  getInterestNetSalesPer($ProfitAndLoss) {
	$Interest = getInterest($ProfitAndLoss);
	$NetSales =  getNetSales($ProfitAndLoss);
	return ($Interest/$NetSales)*100; 
}

function  getPbditInterestPer($ProfitAndLoss) {
	$Interest = getInterest($ProfitAndLoss);
	$Pbdit =  getPBDITOperatingProfit($ProfitAndLoss);
	return ($Pbdit/$Interest); 
}

function  getNetProfit($ProfitAndLoss) {
	extract($ProfitAndLoss);
	$TotalOperatingIncome = getNetSales($ProfitAndLoss);
	$AddOpeningStockInProcessRawMaterials = $RawMaterials['Imported'] + $RawMaterials['Indigenous'];
	$OtherSpares = $OtherSpares['Imported'] + $OtherSpares['Indigenous'] ;
	$NetProfit = $TotalOperatingIncome -($AddOpeningStockInProcessRawMaterials + $OtherSpares + $PowerFuel + $DirectLabour + $OtherManufacturingExpenses+ $Depreciation+ $RepairsMaintenance + 
		$CostOfTradingGoods + $AddOpeningStockInProcess - $DeductClosingStockInProcess + 
		$AddOpeningStockOfFinishedGoods - $DeductClosingStockOfFinishedGoods + $SellingGeneralAdmExpenses );
	return $NetProfit; 
}

function  getCashProfit($ProfitAndLoss) {
	extract($ProfitAndLoss);
	$TotalOperatingIncome = getNetSales($ProfitAndLoss);
	$AddOpeningStockInProcessRawMaterials = $RawMaterials['Imported'] + $RawMaterials['Indigenous'];
	$OtherSpares = $OtherSpares['Imported'] + $OtherSpares['Indigenous'] ;
	$InterestPaymentToBanks = array_sum($InterestPaymentToBanks);
	$InterestPaymentToFIs = array_sum($InterestPaymentToFIs);


	$CashProfit = ($TotalOperatingIncome -($AddOpeningStockInProcessRawMaterials+$OtherSpares+$PowerFuel + $DirectLabour + $OtherManufacturingExpenses+ $Depreciation+ $RepairsMaintenance + 
		$CostOfTradingGoods + $AddOpeningStockInProcess - $DeductClosingStockInProcess + 
		$AddOpeningStockOfFinishedGoods - $DeductClosingStockOfFinishedGoods + $SellingGeneralAdmExpenses)) - ($InterestPaymentToBanks + $InterestPaymentToFIs + $BankCharges) + ( $InterestOnDepositsDividendReceived + $ForexGains + $NonOperatingIncomeFromSubsidiaries + $TaxRefund + $MiscIncome + $ProfitOnSaleOfAssetsInvestments + $OtherIncome + $ProvisionsExpensesWrittenBack) - ($LossOnSaleOfInvestments+$LossOnSaleOfFa+$DerivativeLossesBooked+$NetLossOnForeignCurrencyTranslationAndTransactionsLossDueToFire+$PreliExpOneTimeExpensesWrittenOff+$MiscExpWrittenOff +$ProvForDoubDebtsDimInTheValOfInv +$WealthTax) - $TaxPaid - ($ProvisionForTaxesCurrentPeriod + $ProvisionForTaxesDefferedTaxes) +$Depreciation - $ProvisionsExpensesWrittenBack;

	return $CashProfit; 
}

function  getTangibleNetWorth($fullArray) {
	extract($fullArray['ProfitAndLoss']);
	extract($fullArray['BalanceSheet']['Liabilities']);
	extract($fullArray['BalanceSheet']['Assets']);
	$TangibleNetWorth = ($PartnersCapitalProprietorSCapital+ $ShareCapitalPaidUp + $ShareApplicationFinalizedForAllotment+$StatutoryAndCapitalReserves+ $GeneralReserve+ $RevaluationReserve+ $OtherReservesExcludingProvisions + $SurplusOrDeficitInPLAccount+ $SharePremiumAC + $CapitalSubsidy + $InvestmentAllowanceUtilizationReserve - $RevaluationReserve) - ($AccumulatedLossesPreliminaryExpensesMiscellaneousExpenditureNotWOffOtherDeferredRevenueExpenses + $DeferredTaxAsset);

	return $TangibleNetWorth; 
}

function  getTolTnw($fullArray) {
	extract($fullArray['ProfitAndLoss']);
	extract($fullArray['BalanceSheet']['Liabilities']);
	extract($fullArray['BalanceSheet']['Assets']);
	$TangibleNetWorth = getTangibleNetWorth($fullArray);
	$TolTnw = ($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $SundryCreditorsTrade + $ShortTermBorrowingsFromAssociatesGroupConcerns + $ShortTermBorrowingsCommercialPaper + $ShortTermBorrowingsFromOthers + $AdvancesPaymentsFromCustomersDepositsFromDealers + $ProvisionForTaxation + $ProposedDividend + $OtherStatutoryLiabilitiesDueWithinOneYear + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $DepositsDueForRepaymentDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $OtherCurrentLiabilitiesProvisionsDueWithin1Year + $InterestAccButNotDue + $ProvisionForNpa + $ProvisionForLeaveEncashmentGratuity + $UnclaimedDividend + $OtherLiabilities + $DueToSubsidiaryCompaniesAffiliates + $TaxOnInterimDividendPayable + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)/ $TangibleNetWorth;

	return $TolTnw; 
}

function  getTolAdjTnwAtnw($fullArray) {
	extract($fullArray['ProfitAndLoss']);
	extract($fullArray['BalanceSheet']['Liabilities']);
	extract($fullArray['BalanceSheet']['Assets']);
	$TangibleNetWorth = getTangibleNetWorth($fullArray);
	$TolAdjTnwAtnw = ((($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $SundryCreditorsTrade + $ShortTermBorrowingsFromAssociatesGroupConcerns + $ShortTermBorrowingsCommercialPaper + $ShortTermBorrowingsFromOthers + $AdvancesPaymentsFromCustomersDepositsFromDealers + $ProvisionForTaxation + $ProposedDividend + $OtherStatutoryLiabilitiesDueWithinOneYear + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $DepositsDueForRepaymentDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $OtherCurrentLiabilitiesProvisionsDueWithin1Year + $InterestAccButNotDue + $ProvisionForNpa + $ProvisionForLeaveEncashmentGratuity + $UnclaimedDividend + $OtherLiabilities + $DueToSubsidiaryCompaniesAffiliates + $TaxOnInterimDividendPayable + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)) - $BorrowingsFromSubsidiariesAffiliatesQuasiEquity) / ( $TangibleNetWorth -$InvestmentsInSubsidiaryCompaniesAffiliates + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity);

	return $TolAdjTnwAtnw; 
}


function  getDebtPbdit($fullArray) {
	extract($fullArray['ProfitAndLoss']);
	extract($fullArray['BalanceSheet']['Liabilities']);
	extract($fullArray['BalanceSheet']['Assets']);
	$Pbdit =  getPBDITOperatingProfit($fullArray['ProfitAndLoss']);
	$DebtPbdit = ($FromApplicantBankCcWcdl + $FromOtherBanks + $OfIAndIiInWhichBillPurchasedDisc + $ShortTermBorrowingsCommercialPaper + $InstallmentsOfTermLoansDebenturesDpgsEtcDueWithin1Year + $PreferenceSharesRedeemableWithin1Year + $Wctl + $PrefSharesPortionRedeemableAfter1Yr + $TermLoansExcludingInstallmentsPayableWithinOneYear + $TermLoansFromFis + $Debentures + $TermDeposits + $UnsecuredLoans + $BorrowingsFromSubsidiariesAffiliatesQuasiEquity + $DepositFromDealersOnlyIfConsideredAsAvailableForLongTerm + $OtherTermLiabilities + $DeferredTaxLiability + $OtherLoanAdvances)/ $Pbdit;

	return $DebtPbdit; 
}

function  getRecievableTurnover($fullArray) {
	extract($fullArray['ProfitAndLoss']);
	extract($fullArray['BalanceSheet']['Liabilities']);
	extract($fullArray['BalanceSheet']['Assets']);
	$RecievableTurnover = (($ReceivablesOtherThanDeferredExportsInclBillsPurchasedDiscountedByBanks+$ExportReceivablesIncludingBillPurchasedAndDiscounted+$RetentionMoneySecurityDeposit+$DebtorsMoreThan6Months) / ($GrossDomesticSales+$ExportSales+$AddTradingOtherOperatingIncome))*365;

	return $RecievableTurnover; 
}

function getCashAndBankBalances($fullArray){
	extract($fullArray['BalanceSheet']['Assets']);
	return $CashAndBankBalances;
}





?>