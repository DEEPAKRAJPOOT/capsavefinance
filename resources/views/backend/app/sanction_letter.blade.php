@extends('layouts.backend.admin-layout')
@section('additional_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-wysiwyg/0.3.3/bootstrap3-wysihtml5.min.css" />
@endsection
@section('content')
@include('layouts.backend.partials.admin-subnav')
<style>
  h5{ 
    margin:0px;
    font-size: 14px;
    margin-bottom:15px;
  }
  .table{
    width:100%; 
    font-family:Arial;font-size: 14px; 
  }
  .table > thead > tr > th,.table > tbody > tr > td{
    padding:5px 10px;
    text-align:left;
  }
  .table-border{
    border:#ccc solid 1px;
  }
  .table-border>thead>tr>th,.table-border>tbody>tr>td {
    -webkit-print-color-adjust: exact;
    color: #000000;
    border-right: 1px solid #cccccc;
    border-bottom: 1px solid #cccccc;
    vertical-align: top;
    font-size: 14px;
    text-align:left;
  }
  .table-border>thead>tr>th:last-child,.table-border>tbody>tr>td:last-child{ 
    border-right:none;
  }
  .table-border>tbody>tr:last-child>td{
    border-bottom:none;
  }
  .blank{
    background-color:#cccccc !important;
    -webkit-print-color-adjust: exact;
  }
  .table-border.table-inner{
    border:none; margin:0px;
  }
  .pd-0{
    padding:0px !important;
  }
  .select{
    width: 150px;
    height: 27px;
    padding: 0 5px;
    border: #ccc solid 1px;
    border-radius: 2px;
    margin-top: 5px;
    background-color: #FFF;
  }
  .input_sanc{
    width: 100%;
    height: 27px;
    border: none;
    padding: 0 5px;
    border-radius: 2px;
    margin-top: 2px;
    background-color: #FFF;
  }
  .input_sanc:focus{
    border: #ccc solid 1px;
  }
  .offerdiv{
    border: 2px solid #cccccc;
    margin-bottom: 20px;
  }
  .offerdiv h5{
    background-color: #ccc;
    padding: 10px;
    margin-bottom: 0;
  }
  .section6>ol>li{
    padding: 2px;
  }
</style>
<style media="print">
  .height{
    height:48px;
  }
</style>
<div class="content-wrapper">
  <ul class="nav nav-tabs sub-menu-main pl-0 m-0">
    <li class="active"><a data-toggle="tab" href="#sanctionSupplyChain">SupplyChain</a></li>
    <li><a data-toggle="tab" href="#SanctionLeasing">Leasing</a></li>
  </ul>
  <div class="row grid-margin mt-3 mb-2">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="tab-content">
            <div id="sanctionSupplyChain" class="tab-pane fadein active">
              <form action="{{route('save_sanction_letter_supplychain')}}" method="POST">
                @if(!empty($supplyChaindata['offerData']) && $supplyChaindata['offerData']->count())
                <div class="form-fields">
                  <h5 class="card-title form-head-h5">Sanction Letter Supply Chain</h5>
                  <table class="table" cellpadding="0" cellspacing="0">
                    <tr>
                      <td><b>To</b></td>
                    </tr>
                    <tr>
                      <td>{{$supplyChaindata['ConcernedPersonName']}}</td>
                    </tr>
                    <tr>
                      <td>{{$supplyChaindata['EntityName']}}</td>
                    </tr>
                    <tr>
                      <td>{{$supplyChaindata['Address']}}</td>
                    </tr>
                    <tr>
                      <td>{{$supplyChaindata['EmailId']}}</td>
                    </tr>
                    <tr>
                      <td>{{$supplyChaindata['MobileNumber']}}</td>
                    </tr>
                  </table>
                  <table  class="table table-border"  cellpadding="0" cellspacing="0">
                    <thead>
                      <tr>
                        <th width="33.33%">Facility (Product)</th>
                        <th width="33.33%">Amount (Rs. In Mn)</th>
                        <th width="33.33%">Sub-Limit of</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="">{{getProductType($supplyChaindata['product_id'])}}</td>
                        <td class="">{{$supplyChaindata['tot_limit_amt']}}</td>
                        <td class="">
                          <select class="select" name="sublimit_of" id="sublimit_of">
                            <option>Term Loan</option>
                            <option>Purchase Finance Facility</option>
                            <option>Invoice Discounting Facility</option>
                            <option>Vendor Finance</option>
                          </select>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  <h5>Section 1:- Conditions for individual facilities<br/><small>(Select facilitylies from below mentioned facilities and delete others while submitting the final term sheet.)</small></h5>
                  <!-- Vender Program -->
                  <table  class="table table-border"  cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td width="20%">Facility No</td>
                        <td width="20%">{{$supplyChaindata['prgm_type']}}</td>
                        <td width="30%">Facility Name</td>
                        <td width="30%">{{$supplyChaindata['prgm_type'] == '2' ? 'Purchase Finance Facility  /  Channel Financing' : 'Vendor Finance Facility'}}</td>
                      </tr>
                      <tr>
                        <td width="33.33%">Facility Amount</td>
                        <td width="66.66%" colspan="3">{{$supplyChaindata['limit_amt']}}</td>
                      </tr>
                      <tr>
                        <td width="33.33%">Purpose</td>
                        <td width="66.66%" colspan="3">{{$supplyChaindata['purpose']}}</td>
                      </tr>
                      <tr>
                        <td width="33.33%">Expiry of Limit</td>
                        <td width="66.66%" colspan="3"> Limit will be valid for 1 year from
                          <select class="select" name="expiry_of_limit">
                            <option>date of sanction letter</option>
                            <option>date of first disbusrement</option>
                          </select>
                          (Date will be selected from sanction letter itself) Documents required for renewal of facility to be submitted to Capsave Finance Pvt Limited at least 40 days prior to limit expiry.
                        </td>
                      </tr>
                      <tr>
                        <td width="33.33%">Specific Condition</td>
                        <td width="66.66%" colspan="3">
                          <ul style="padding:0px 0px 0px 15px; margin:0px; line-height:23px;list-style-type:unset;">
                            <li>Invoices should not be older than 30 days from 
                              <select class="select" name="specific_cond" id="specific_cond">
                                <option>Invoice Date</option>
                                <option>BOE Date</option>
                                <option>GRN Date</option>
                                <option>Date of Discounting</option>
                              </select> On the date of Discounting.
                            </li>
                            <li>Discounting proceed to be credited to working capital account of the borrowers.</li>
                          </ul>
                        </td>
                      </tr>
                      <tr>
                        <td width="33.33%">Specific Pre-disbursement Condition</td>
                        <td width="66.66%" colspan="3"></td>
                      </tr>
                      <tr>
                        <td width="33.33%"> Specific Post-disbursement Condition</td>
                        <td width="66.66%" colspan="3"></td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  @foreach($supplyChaindata['offerData'] as $offerD)
                  <table  class="table table-border"  cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td width="33.33%" class="pd-0" style="padding: 0px !important;">
                          <table class="table-border table table-inner" cellpadding="0" cellspacing="0">
                            <thead>
                              <tr>
                                <th width="70%">Apprv. Debtor Name</th>
                                <th width="30%" class="height">Sub Limit</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>{{$supplyChaindata['anchorData'][$offerD->anchor_id]['comp_name'] ?? ''}}</td>
                                <td>{{$offerD->prgm_limit_amt}}</td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                        <td width="66.66%" colspan="3" class="pd-0" style="padding: 0px !important;">
                          <table class="table-border table table-inner" cellpadding="0" cellspacing="0">
                            <thead>
                              <tr>
                                <th width="35%">Max. Discounting Period</th>
                                <th width="25%">Grace Period</th>
                                <th width="25%">ROI</th>
                                <th width="25%">Margin</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>{{$offerD->tenor}}</td>
                                <td>{{$offerD->grace_period}}</td>
                                <td>{{$offerD->interest_rate}}</td>
                                <td>{{$offerD->margin}}</td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td width="33.33%">Investment Payment Frequency</td>
                        <td width="66.66%" colspan="3">{{$offerD['payment_frequency']}}</td>
                      </tr>
                      <tr>
                        <td width="33.33%">Penal Interest</td>
                        <td width="66.66%">

                          <ul style="padding:0px 0px 0px 15px; margin:0px; line-height:23px;list-style-type:unset;">
                            <li>{{!empty($offerD['overdue_interest_rate']) ? $offerD['overdue_interest_rate'] .'%' : ''}}
                            <select class="select" name="penal_on[]">
                            <option>On</option>
                            <option>over and above the rate for the last draw down  or Rollover of facility on</option>
                            </select> entire principal / payable interest on delay in repayment of principal / Interest / charges <select class="select" name="penal_applicable[]">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                            </select></li>
                            <li>The rate of interest will be {{!empty($offerD['overdue_interest_rate']) ? $offerD['overdue_interest_rate'] .'%' : ''}} higher than the rate stipulated under each of the facilities till the security is created
                            <select class="select" name="penal_applicable[]">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                            </select></li>
                            <li>If security is not created within the stipulated timeframe then a penal interest of 
                            {{!empty($offerD['overdue_interest_rate']) ? $offerD['overdue_interest_rate'] .'%' : ''}} p.a.  
                            <select class="select" name="penal_applicable[]">
                            <option>On</option>
                            <option>over and above the rate for the last draw down  or Rollover of facility on</option>
                            </select> entire principle <select class="select"><option>Applicable</option><option>Not applicable</option></select></li>
                          </ul>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  @endforeach
                  <br />

                  <table  class="table table-border"  cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td width="33.33%">Prepayment</td>
                        <td width="66.66%">In case borrower desires to prepay the loan, the prepayment of loan will be accepted on the terms and conditions to be decided by CFPL for time to time.
                        </td>
                      </tr>
                      <tr>
                        <td width="33.33%">Payment Mechanism of Interest</td>
                        <td width="66.66%" name="payment_machanism_of_interest">
                          <select class="select">
                            <option>Choose an Item</option>
                            <option>UDC</option>
                            <option>PDC</option>
                            <option>ECS Mandate</option>
                            <option>RTGS</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td width="33.33%">Payment Mechanism of Principal</td>
                        <td width="66.66%">
                          <select class="select" name="payment_machanism_of_principal">
                            <option>Choose an Item</option>
                            <option>UDC</option>
                            <option>PDC</option>
                            <option>ECS Mandate</option>
                            <option>RTGS</option>
                          </select>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  <h5>Section 2:- Common Securities << Depending on Addition Security selected on Limit Assesment>></h5>
                  @foreach($supplyChaindata['offerData'] as $offerD)
                  <div class="offerdiv">
                    @if($offerD->offerPs->count())
                    <h5> Primary Security </h5>
                    <table  class="table table-border"  cellpadding="0" cellspacing="0">
                      <thead>
                        <tr>
                          <th width="20%">Security</th>
                          <th width="20%">Type of security</th>
                          <th width="20%">Status of security</th>
                          <th width="20%">Time for perfecting security</th>
                          <th width="20%">Description of security</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($offerD->offerPs as $PrimarySecurity)
                        <tr>
                          <td>{{config('common.ps_security_id.'.$PrimarySecurity->ps_security_id)}}</td>
                          <td>{{config('common.ps_type_of_security_id.'.$PrimarySecurity->ps_type_of_security_id)}}</td>
                          <td>{{config('common.ps_status_of_security_id.'.$PrimarySecurity->ps_status_of_security_id)}}</td>
                          <td>{{config('common.ps_time_for_perfecting_security_id.'.$PrimarySecurity->ps_time_for_perfecting_security_id)}}</td>
                          <td>{{$PrimarySecurity->ps_desc_of_security}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    @endif
                    @if($offerD->offerCs->count())
                    <h5> Collateral Security </h5>
                    <table  class="table table-border"  cellpadding="0" cellspacing="0">
                      <thead>
                        <tr>
                          <th width="20%">Security</th>
                          <th width="20%">Type of security</th>
                          <th width="20%">Status of security</th>
                          <th width="20%">Time for perfecting security</th>
                          <th width="20%">Description of security</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($offerD->offerCs as $CollateralSecurity)
                        <tr>
                          <td>{{config('common.cs_desc_security_id.'.$CollateralSecurity->cs_desc_security_id)}}</td>
                          <td>{{config('common.cs_type_of_security_id.'.$CollateralSecurity->cs_type_of_security_id)}}</td>
                          <td>{{config('common.cs_status_of_security_id.'.$CollateralSecurity->cs_status_of_security_id)}}</td>
                          <td>{{config('common.cs_time_for_perfecting_security_id.'.$CollateralSecurity->cs_time_for_perfecting_security_id)}}</td>
                          <td>{{$CollateralSecurity->cs_desc_of_security}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    @endif
                    @if($offerD->offerPg->count())
                    <h5>Personal Guarantee</h5>
                    <table  class="table table-border"  cellpadding="0" cellspacing="0">
                      <thead>
                        <tr>
                          <th width="20%">Name of Guarantor</th>
                          <th width="20%">Time for perfecting security</th>
                          <th width="20%">Residential Address </th>
                          <th width="20%">Net worth as per IT return/CA Certificate</th>
                          <th width="20%">Comment if any </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($offerD->offerPg as $PersonalGuarantee)
                        <tr>
                          <td>{{$supplyChaindata['bizOwnerData'][$PersonalGuarantee->pg_name_of_guarantor_id]['first_name'] ?? ''}}</td>
                          <td>{{config('common.pg_time_for_perfecting_security_id.'.$PersonalGuarantee->pg_time_for_perfecting_security_id)}}</td>
                          <td>{{$PersonalGuarantee->pg_residential_address}}</td>
                          <td>{{$PersonalGuarantee->pg_net_worth}}</td>
                          <td>{{$PersonalGuarantee->pg_comments}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    @endif
                    @if($offerD->offerCg->count())
                    <h5>Corporate Guarantee/ Letter of Comfort/ Put Option</h5>
                    <table  class="table table-border"  cellpadding="0" cellspacing="0">
                      <thead>
                        <tr>
                          <th width="20%">Type</th>
                          <th width="20%">Name of Guarantor</th>
                          <th width="20%">Time for perfecting security</th>
                          <th width="20%">Registered Address</th>
                          <th width="20%">Comment if any </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($offerD->offerCg as $CorporateGuarantee)
                        <tr>
                          <td>{{config('common.cg_type_id.'.$CorporateGuarantee->cg_type_id)}}</td>
                          <td>{{$supplyChaindata['bizOwnerData'][$CorporateGuarantee->cg_name_of_guarantor_id]['first_name'] ?? ''}}</td>
                          <td>{{config('common.cg_time_for_perfecting_security_id.'.$CorporateGuarantee->cg_time_for_perfecting_security_id)}}</td>
                          <td>{{$CorporateGuarantee->cg_residential_address}}</td>
                          <td>{{$CorporateGuarantee->cg_comments}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    @endif
                    @if($offerD->offerEm->count())
                    <h5>Escrow Mechanism</h5>
                    <table  class="table table-border"  cellpadding="0" cellspacing="0">
                      <thead>
                        <tr>
                          <th width="20%">Name of Debtor</th>
                          <th width="20%">Expected cash flow per month</th>
                          <th width="20%">Time for perfecting security</th>
                          <th width="20%">Mechanism</th>
                          <th width="20%">Comment if any</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($offerD->offerEm as $EscrowMechanism)
                        <tr>
                          <td>{{$EscrowMechanism->em_debtor_id}}</td>
                          <td>{{$EscrowMechanism->em_expected_cash_flow}}</td>
                          <td>{{config('common.em_time_for_perfecting_security_id.'.$EscrowMechanism->em_time_for_perfecting_security_id)}}</td>
                          <td>{{$EscrowMechanism->em_mechanism_id}}</td>
                          <td>{{$EscrowMechanism->em_comments}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    @endif
                  </div>
                  @endforeach
                  <h5>Section 3:Specific Security<select class="select">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                   </select></h5>
                  <h5>Section 4:- Security PDCs/ECS Mandate with Undertaking, DSRA and Other Securities</h5>
                  <h5>PDC</h5>
                  <table  class="table table-border"  cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td width="33.33%">Facility No</td>
                        <td width="6.66%"><input type="text" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc" placeholder="Click here to enter text"></td>
                        <td width="30%">Facility Name</td>
                        <td width="30%"><input type="text" name="pdc_facility_name" id="pdc_facility_name" class="input_sanc" placeholder="Click here to enter text"></td>
                      </tr>
                      <tr>
                        <td width="33.33%">Facility Amount</td>
                        <td width="66.66%" colspan="3"><input type="text" name="pdc_facility_amt" id="pdc_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                      </tr>
                      <tr>
                        <td width="33.33%">Purpose</td>
                        <td width="66.66%" colspan="3"><input type="text" name="pdc_facility_purpose" id="pdc_facility_purpose" class="input_sanc" placeholder="Click here to enter text"></td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  <table  class="table table-border"  cellpadding="0" cellspacing="0">
                    <thead>
                      <tr>
                        <th>Cheque for</th>
                        <th>No of Cheque </th>
                        <th>Not Above </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td >Principal</td>
                        <td><input type="text" name="pdc_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                        <td><input type="text" name="pdc_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                      </tr>
                      <tr>
                        <td >Interest</td>
                        <td><input type="text" name="pdc_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                        <td><input type="text" name="pdc_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                      </tr>
                      <tr>
                        <td>Repayment</td>
                        <td><input type="text" name="pdc_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                        <td><input type="text" name="pdc_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                      </tr>
                      <tr>
                        <td>Other</td>
                        <td><input type="text" name="pdc_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                        <td><input type="text" name="pdc_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                      </tr>
                      <tr>
                        <td>security</td>
                        <td><input type="text" name="pdc_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                        <td><input type="text" name="pdc_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  <h5>NACH Mandate with undertaking</h5>
                  <table  class="table table-border"  cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td width="33.33%">Facility No</td>
                        <td width="6.66%"><input type="text" name="nach_facility_no" id="nach_facility_no" class="input_sanc"></td>
                        <td width="30%">Facility Name</td>
                        <td width="30%"><input type="text" name="nach_facility_name" id="nach_facility_name" class="input_sanc"></td>
                      </tr>
                      <tr>
                        <td width="33.33%">Facility Amount</td>
                        <td width="66.66%" colspan="3"><input type="text" name="nach_facility_amt" id="nach_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                      </tr>
                      <tr>
                        <td width="33.33%">Purpose</td>
                        <td width="66.66%" colspan="3"><input type="text" name="nach_facility_purpose" id="nach_facility_purpose" class="input_sanc" placeholder="Click here to enter text"></td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  <table  class="table table-border"  cellpadding="0" cellspacing="0">
                    <thead>
                      <tr>
                        <th>Cheque for</th>
                        <th>No of Cheque </th>
                        <th>Not Above </th>
                      </tr>
                    </thead>
                     <tbody>
                      <tr>
                        <td >Principal</td>
                        <td><input type="text" name="nach_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                        <td><input type="text" name="nach_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                      </tr>
                      <tr>
                        <td >Interest</td>
                        <td><input type="text" name="nach_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                        <td><input type="text" name="nach_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                      </tr>
                      <tr>
                        <td>Repayment</td>
                        <td><input type="text" name="nach_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                        <td><input type="text" name="nach_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                      </tr>
                      <tr>
                        <td>Other</td>
                        <td><input type="text" name="nach_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                        <td><input type="text" name="nach_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                      </tr>
                      <tr>
                        <td>security</td>
                        <td><input type="text" name="nach_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                        <td><input type="text" name="nach_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  <h5>DSRA <select class="select" name="dsra_applicability">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                   </select></h5>
                  <table  class="table table-border"  cellpadding="0" cellspacing="0">
                    <thead>
                      <tr>
                        <th>Amount(lacs in Inr )</th>
                        <th>Tenure(in months)</th>
                        <th>Comment if any</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><input type="text" name="dsra_amt" class="input_sanc" placeholder="Enter DSRA Amount"></td>
                        <td><input type="text" name="dsra_tenure" class="input_sanc" placeholder="Enter DSRA Tenure"></td>
                        <td><input type="text" name="dsra_comment" class="input_sanc" placeholder="Comment if any"></td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  <h5>Any other security <select class="select" name="dsra_applicability">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                   </select></h5>
                  <table  class="table table-border"  cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td><input type="text" name="other_sucurities" id="other_sucurities" class="input_sanc" placeholder="Click here to enter Securities"></td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  <h5>Section 5:- Financial Covenants
                    <span class="btn btn-danger btn-sm remove_covenants" style="float: right;margin: 5px;cursor: pointer;">Remove -</span> 
                    <span class="btn btn-success btn-sm clone_covenants" style="float: right;margin: 5px;cursor: pointer;">Add More +</span>
                  </h5>
                  <table  class="table table-border"  cellpadding="0" cellspacing="0">
                    <thead>
                      <tr>
                        <th width="75%">Covenants</th>
                        <th width="25%">Minimum/Maximum ratio</th>
                        <th width="25%">Applicability</th>
                      </tr>
                    </thead>
                    <tbody class="FinancialCovenantsTBody">
                      <tr class="covenants_clone_tr">
                        <td><input type="text" name="covenants_name[]" class="input_sanc" placeholder="Enter Covenants"></td>
                        <td><input type="text" name="covenants_ratio[]" class="input_sanc" placeholder="Enter Minimum/Maximum ratio"></td>
                        <td><select class="select" name="covenants_ratio_applicability[]">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                        </select></td>
                      </tr>
                    </tbody>
                  </table>
                  <p>The financial covenants shall be tested on a choose an item.basis and shall be reported in the monitoring report to be submitted by choose an item.</p>

                  <h5>Section 6:- General Pre-disbursement and Post Disbursement conditions</h5>
                     <div class="section6">
                      <ol>
                 <li>Form CHG-1 to be filed with ROC within 30 days from the date of execution of Security Documents of the borrower/Corporate Guarantor<select class="select hide" name="pre_post_condition[]">
                            <option>Applicable</option></select>
                  </li>
                  <li>CFPL shall, at its discretion, obtain a confidential credit report on the borrower from its other lenders.
                          <select class="select" name="pre_post_condition[]">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                          </select>
                  </li>
                  <li>All the assets charged to the CFPL are to be insured for full value covering all risks with usual CFPL clause. A copy of the insurance policy(ies) to be furnished to the CFPL within 30 days of security perfection.<select class="select hide" name="pre_post_condition[]"><option>Applicable</option></select>
                  </li>

                  <li>The obligation of the Lender to make disbursements out of the Facility shall be subject to the Borrower complying with the following conditions to the satisfaction of CFPL .The Borrower shall complete all documentation as stipulated, to the satisfaction of CFPL.The Borrower to furnish title investigation search and valuation of security ( being mortgaged to CFPL) prior to disbursement.<select class="select" name="pre_post_condition[]">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                          </select>
                  </li>  

                <li>The borrower shall finalise its selling arrangements to the satisfaction of CFPL.
                          <select class="select" name="pre_post_condition[]">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                          </select>
                </li> 

                <li>The borrower shall obtain necessary sanction of power, water, fuel, etc from the relevant authorities to the satisfaction of CFPL. 
                          <select class="select" name="pre_post_condition[]">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                          </select>
                </li>

                <li>The borrower shall make adequate arrangements for treatment and disposal of effluents, solid waste and emissions from its project and shall furnish appropriate approvals from the authorities in this regard.
                          <select class="select" name="pre_post_condition[]">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                          </select>
                </li> 

                <li>The borrower shall broadbase its Board of Directors and finalise and strengthen its management set-up to the satisfaction of CFPL, if necessary. <select class="select" name="pre_post_condition[]">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                          </select>
                </li> 

                <li>The borrower shall carry out safety/environment/energy audit of its project to the satisfaction of CFPL.
                          <select class="select" name="pre_post_condition[]">
                            <option>Applicable</option>
                            <option>Not applicable</option>
                          </select>
                </li> 

                <li>CFPL reserves the right to appoint qualified accountants / technical experts /management consultants of its choice to examine the books of accounts, factories and operations of the borrower or to carry out a full concurrent/statutory audit. The cost of such inspection shall be borne by the <select class="select"><option>Borrower</option><option>ABFL</option></select><select class="select hide" name="pre_post_condition[]"><option>Applicable</option></select></li>

                <li>In case any condition is stipulated by any other lender that is more favorable to them than the terms stipulated by CFPL, CFPL shall at its discretion, apply to this loan such equivalent conditions to bring its loan at par with those of the other lenders. <select class="select hide" name="pre_post_condition[]">
                            <option>Applicable</option></select></li>

                <li>The borrower shall forward to CFPL, provisional balance sheet and Profit & Loss Account within <select class="select"><option>1</option><option>2</option><option>3</option><option>4</option></select>  months of year-end and audited accounts within 6 months of year end. Quarterly financial results shall be submitted within 60 days from the end of each quarter or with the filing with stock exchange for listed borrower.<select class="select hide" name="pre_post_condition[]"><option>Applicable</option></select></li> 

                <li>Inspection of assets charged to CFPL may be carried out once in <select class="select"><option>1</option><option>2</option><option>3</option><option>4</option></select>  months or at more frequent intervals as decided by CFPL by its own officials or through persons/firm appointed by CFPL. The cost of inspection is to be borne by the borrower.<select class="select hide" name="pre_post_condition[]"><option>Applicable</option></select></li>

                <li>During the currency of CFPL’s credit facility(s), the borrower will not without CFPL’s prior <select class="select"><option>Permission</option><option>Intimation</option></select> in writing: 
                  <ol>
                    <li>conclude any fresh borrowing arrangement either secured or unsecured with any other Bank or Financial Institutions, borrower or otherwise, not create any further charge over their fixed assets without our prior approval in writing. </li>
                    <li>undertake any expansion or fresh project or acquire fixed assets, while normal capital expenditure, e.g. replacement of parts, can be incurred. </li>
                    <li>invest by way of share capital in or lend or advance to or place deposits with any other concern (normal trade credit or security deposit in the routine course of business or advances to employees can, however, be extended). </li>
                    <li>formulate any scheme of amalgamation with any other borrower or reconstruction, acquire any borrower. </li>
                    <li>undertake guarantee obligations on behalf of any other borrower or any third party. </li>
                    <li>declare dividend for any year except out of profits relating to that year after making all the due and necessary provisions provided that no default had occurred in any repayment obligation and Bank’s permission is obtained. </li>
                    <li>make any repayment of the loans and deposits and discharge other liabilities except those shown in the funds flow statement submitted from time to time. </li>
                    <li>make any change in their management set-up. </li>
                  </ol>
                <select class="select hide" name="pre_post_condition[]"><option>Applicable</option></select></li>
                      </ol>
                    </div>
                </div>
                @else 
                <div class="card card-color mb-0">
                  <div class="card-header">
                    <a class="card-title ">Sanction letter for Supply Chain cannot be generated as limit offer has not added yet.</a>
                  </div>
                </div>
                @endif
              </form>
            </div>
            <div id="SanctionLeasing" class="tab-pane fade">
              <div class="card card-color mb-0 {{empty($sanction_expire_msg) ? 'hide' : '' }}">
                <div class="card-header">
                  <a class="card-title ">{{$sanction_expire_msg}}</a>
                </div>
              </div>
              @if( is_array($offerData)?count($offerData):$offerData->count())
              <div class=" form-fields">
                <div class="col-md-12">
                  <h5 class="card-title form-head-h5">Sanction Letter
                    @if(!empty($sanctionData))
                    <a data-toggle="modal" data-target="#previewSanctionLetter" data-height="500px" data-width="100%" data-placement="top" href="#" data-url="{{ route('preview_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId, 'upload'=>1, 'sanction_id'=>$sanction_id ]) }}" class="btn btn-success btn-sm float-right mt-3 ml-3">Preview/Send Mail</a>
                    @endif
                  </h5>
                  <div class="col-md-12">
                    <form action="{{route('save_sanction_letter')}}" method="POST">
                      @csrf
                      <table class="table table-bordered overview-table">
                        <tbody>
                          <tr>
                            <td with="25%"><b>Lessee</b></td>
                            <td with="25%">{{ $biz_entity_name ??  ''}}</td>
                            <td with="25%"><b>Lessor</b></td>
                            <td with="25%">
                              <input type="text" name="lessor" value="{{ $lessor ?? ''}}" class="form-control" />
                            </td>
                          </tr>
                          <tr>
                            <td with="25%"><b>Sanction Amount</b></td>
                            <td with="25%"> {{ count($leasingLimitData) > 0 ? 'INR '. number_format($leasingLimitData['0']['limit_amt']) : '' }}</td>
                          </tr>
                          <tr>
                            <td with="25%"><b>Sanction Validity</b></td>
                            <td with="25%" colspan="3">
                              <div class="row">
                                <div class="col-md-2">
                                  <input type="text" name="sanction_validity_date" value="{{old('sanction_validity_date', !empty($validity_date) ? \Carbon\Carbon::parse($validity_date)->format('d/m/Y') : date('d/m/Y'))}}" class="form-control" tabindex="5" placeholder="Enter Validity Date" autocomplete="off" >
                                </div>
                                <div class="col-md-2">
                                  <input type="text" name="sanction_expire_date" value="{{old('sanction_expire_date', 
                                  !empty($expire_date) ? \Carbon\Carbon::parse($expire_date)->format('d/m/Y') : '')}}" class="form-control" tabindex="5" placeholder="Enter Expire Date" autocomplete="off" >
                                </div>
                                <div class="col">
                                  <input type="text" class="form-control" placeholder="Enter Comment" name="sanction_validity_comment" value="{{ $validity_comment ?? ''}}">
                                </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <table class="table overview-table" cellpadding="0" cellspacing="0" border="1">
                        <thead>
                          <tr>
                            <td width="10%" style="background: #e9ecef;"><b>Facility Type</b></td>
                            <td width="20%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Equipment Type</b></td>
                            <td width="10%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Limit of the Equipment</b></td>
                            <td width="10%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Tenor (Months)</b></td>
                            <td width="20%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>PTP Frequency</b></td>
                            <td width="10%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>    XIRR/ </br>Discounting(%)</b></td>
                            <td width="10%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Processing Fee (%)</b></td>
                          </tr>
                        </thead>
                        <tbody>
                          @forelse($leaseOfferData as $key=>$leaseOffer)
                          <tr>
                            <td>{{isset($leaseOffer->facility_type_id) ?  $facilityTypeList[$leaseOffer->facility_type_id]  : ''}}</td>
                            <td>{{isset($leaseOffer->equipment_type_id) ?  (\Helpers::getEquipmentTypeById($leaseOffer->equipment_type_id)['equipment_name']) : ''}}</td>
                            <td>{!! isset($leaseOffer->prgm_limit_amt) ? ' INR '.number_format($leaseOffer->prgm_limit_amt)  : '0' !!}</td>
                            <td>{{isset($leaseOffer->tenor) ? $leaseOffer->tenor : ''}}</td>
                            <td>
                              @php 
                              $i = 1;
                              if(!empty($leaseOffer->offerPtpq)){
                              $total = count($leaseOffer->offerPtpq);
                              @endphp   
                              @foreach($leaseOffer->offerPtpq as $key => $arr) 
                              @if ($i > 1 && $i < $total)
                              ,
                              @elseif ($i > 1 && $i == $total)
                              and
                              @endif
                              {!!  'INR' !!} {{$arr->ptpq_rate}}  for  {{floor($arr->ptpq_from)}}- {{floor($arr->ptpq_to)}} {{$arrStaticData['rentalFrequencyForPTPQ'][$leaseOffer->rental_frequency]}}
                              @php 
                              $i++;
                              @endphp     
                              @endforeach
                              @php 
                            }
                            @endphp 
                          </td>
                          <td>
                            @if($leaseOffer->facility_type_id == 3)
                            {{$leaseOffer->discounting}}%
                            @else
                            <b>Ruby Sheet</b>: {{$leaseOffer->ruby_sheet_xirr}}%<br/><b>Cash Flow</b>: {{$leaseOffer->cash_flow_xirr}}%
                            @endif
                          </td>
                          <td>{{isset($leaseOffer->processing_fee) ? $leaseOffer->processing_fee.' %': ''}}</td>
                        </tr>
                        @empty
                        <tr>
                          <p>No Offer Found</p>
                        </div>
                        @endforelse  
                      </tbody>
                    </table>
                    <table class="table table-bordered overview-table">
                      <tbody>
                        <tr>
                          <td with="25%"><b>Payment Mechanism</b> </td>
                          <td colspan="3">
                            <div class="row">
                              <div class="col">
                                <select class="form-control" id="payment_type" name="payment_type">
                                  <option value="">Please Select...</option>
                                  <option @if(!empty($payment_type) && $payment_type == '1')selected @endif value="1">NACH</option>
                                  <option @if(!empty($payment_type) && $payment_type == '2')selected @endif value="2">RTGS</option>
                                  <option @if(!empty($payment_type) && $payment_type == '3')selected @endif value="3">NEFT</option>
                                  <option @if(!empty($payment_type) && $payment_type == '4')selected @endif value="4">Advance Cheque</option>
                                  <option @if(!empty($payment_type) && $payment_type == '5')selected @endif value="5">Other Specify</option>
                                </select>
                              </div>
                              <div class="col">
                                <input type="text" class="form-control @if(!empty($payment_type) && $payment_type != '5') hide @endif" name="payment_type_comment" id="payment_type_comment" placeholder="Enter Payment Mechanism" value="{{ $payment_type_other ?? '' }}">
                              </div>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td><b>Delayed payment charges</b></td>
                          <td  colspan="3">
                            <textarea class="form-control textarea" name="delay_pymt_chrg" id="delay_pymt_chrg" cols="30" rows="10">@if(!empty($delay_pymt_chrg) && $delay_pymt_chrg){!! $delay_pymt_chrg !!} @else Any delay in the payment of the rentals shall attract overdue interest on the rentals due but unpaid at the overdue rate mentioned in the Master Rental Agreement.@endif</textarea>
                          </td>
                        </tr>    
                        <tr>
                          <td><b>Insurance</b></td>
                          <td colspan="3">
                            <textarea class="form-control textarea" name="insurance" id="insurance" cols="30" rows="10">@if(!empty($insurance)) {!! $insurance !!} @else The equipment is required to be insured throughout the period of the rental for its full insurable value against physical loss and damage. Lessee may elect to: Insure the equipment through their own insurance company or request CFPL to insure the equipment. Should the lessee elect CFPL to insure the equipment, the lessor shall advise on the cost once full equipment details are known. Insurance policy of the assets under lease endorsed in favour of Capsave Finance Pvt Ltd within 30 days of disbursement of each tranche.@endif</textarea>
                          </td>
                        </tr>
                        <tr>   
                          <td><b>GST/Bank Charges</b></td>
                          <td colspan="3">
                            <textarea class="form-control textarea" name="bank_chrg" id="bank_chrg" cols="30" rows="10">@if(!empty($bank_chrg)) {!! $bank_chrg !!}@else Extra as applicable. It is not included in the above rental rates and would be for the account of the Lessee. Bank charges include LC and remittance charges.@endif</textarea>
                          </td>
                        </tr>
                        <tr>
                          <td><b>Legal Costs</b></td>
                          <td colspan="3">
                            <textarea class="form-control textarea" name="legal_cost" id="legal_cost" cols="30" rows="10">@if(!empty($legal_cost)) {!! $legal_cost !!} @else Any legal costs will be for the account of the Lessee. @endif</textarea>
                          </td>
                        </tr>
                        <tr>
                          <td><b>Purchase Orders</b></td>
                          <td colspan="3">
                            <textarea class="form-control textarea" name="po" id="po" cols="30" rows="10">@if(!empty($po)) {!! $po !!} @else Purchase orders shall not be raised by the Lessee on a vendor without our prior written approval and signed by an authorized person of CFPL. Any purchase order raised by CFPL shall be raised on the express understanding and agreement that it is being raised by CFPL as agent for you until such time as CFPL agrees to accept the rental order and or the invoice. CFPL shall not be responsible to pay any vendor any amount until such time that it agrees to do the aforementioned.@endif </textarea>
                          </td>
                        </tr>
                        <tr>
                          <td><b>Pre-disbursement conditions</b></td>
                          <td colspan="3">
                            <textarea class="form-control textarea" name="pdp" id="pdp" cols="30" rows="10">@if(!empty($pdp)) {!! $pdp !!} @else<b>One-time requirement:</b><br><ol><li>&nbsp;Accepted Sanction Letter<br></li><li>Signed MRA&nbsp;</li><li>Self-Attested KYC of client and Vendors (True Copy)&nbsp;</li><ul><li>&nbsp;Certificate of incorporation, MOA, AOA&nbsp;</li><li>&nbsp;Address Proof (Not older than 90 days)&nbsp;</li><li>&nbsp;PAN Card&nbsp;</li><li>&nbsp;GST registration letter&nbsp;</li></ul><li>Board Resolution signed by 2 directors or Company Secretary. or Power of Attorney in favour of company officials to execute such agreements or documents. BR should be dated before the MRA date.&nbsp;</li><li>Bank Guarantee or Lien on Fixed deposit of 70% of the invoice value.&nbsp;</li><li>Unconditional and irrevocable bank guarantee should be in CFPL approved format.&nbsp;</li><li>Bank guarantee should be valid for at least a quarter more than the expiry of the lease tenure.&nbsp;</li><li>KYC of authorized signatory:&nbsp;</li><ul><li>&nbsp;Name of authorized signatories with their Self Attested ID proof and address proof&nbsp;</li><li>&nbsp;Signature Verification of authorized signatories from company's banker&nbsp;</li></ul><li>CIN (company identification number)</li></ol>@endif</textarea>
                          </td>
                        </tr>
                        <tr>
                          <td><b>Disbursement Guidelines/Documentation</b></td>
                          <td colspan="3"> 
                            <textarea class="form-control textarea" name="disburs_guide" id="disburs_guide" cols="30" rows="10">@if(!empty($disburs_guide)){!! $disburs_guide !!} @else <b>With every tranche:</b><br><ol><li>Original Invoices, Delivery challans, lorry receipt, installation report&nbsp;</li><li>In case of import transactions, Packing list, Bill of Entry for home consumption in the joint name, Airway bill/Bill of Lading, Rewarehousing Certificate, PE certificate, TRC copy&nbsp;</li><li>Signed Rental Schedule&nbsp;</li><li>NACH form&nbsp;</li><li>All documents as mentioned in Annexure-1 </li></ol> @endif</textarea>
                          </td>
                        </tr>
                        <tr>
                          <td><b>Other Conditions</b></td>
                          <td colspan="3"> 
                            <textarea class="form-control textarea" name="other_cond" id="other_cond" cols="30" rows="10">@if(!empty($other_cond)){!! $other_cond !!}@else<ul><li>Rentals will be calculated on total invoice value.</li><li>First rental shall commence from date of invoice or date of payment to the vendor whichever is earlier.&nbsp;</li><li>Any financial or operational covenants stipulated by CFPL from time to time.&nbsp;</li><li>All the other conditions stipulated in MRA/Rental Schedule will remain applicable at all times </li></ul>@endif</textarea>
                          </td>
                        </tr>
                        <tr>
                          <td><b>Information and other covenants</b></td>
                          <td colspan="3"> 
                            <textarea class="form-control textarea" name="covenants" id="covenants" cols="30" rows="10">@if(!empty($covenants)) {!! $covenants !!}@else <ul><li>The Lessee hereby agrees &amp; gives consent for the disclosures by the Lender/Lessor of all or any such:&nbsp;</li><ol><li>&#65279;Information &amp; data relating to the lessee.&nbsp;</li><li>Information and data relating to any credit or lease facility availed/to be availed by the lessee and data relating to their obligations as lessee/guarantor.&nbsp;</li><li>Obligations assumed/to be assumed by the lessee in relation to the lease facility(ies).&nbsp;</li><li>In compliance with the regulatory requirements, CFPL shall disclose and furnish details&nbsp;of the transaction including defaults, if any, committed by Lessee in discharge of their obligations hereunder or under any Transaction Documents, to Credit Information Bureau Limited (“CIBIL”) or any other agency as authorized by Reserve Bank of India (“RBI”).&nbsp;</li></ol><li>The lessee declares that the information and data furnished by the lessee to the lender/lessor are true and correct.&nbsp;</li></ul>The Lessee undertakes that CIBIL or any other agency so authorized may use/process the said information and data disclosed by the lessee in the manner as may be deemed fit by them. CIBIL or any other agency so authorized may furnish for consideration the processed information, data, and products thereof prepared by them to banks, financial institutions, or credit granters or registered users as may be specified by RBI in this behalf.@endif</textarea>
                          </td>
                        </tr>                 
                      </tbody>
                    </table>
                    <input type="hidden" name="sanction_id" value="{{$sanction_id ?? ''}}">
                    <input type="hidden" name="app_id" value="{{$appId ?? ''}}">
                    <input type="hidden" name="offer_id" value="{{$offerId ?? ''}}">
                    <input type="hidden" name="biz_id" value="{{$bizId ?? ''}}">
                    <button type="submit" class="btn  btn-success btn-sm float-right">Submit</button>  
                  </form>
                </div>
              </div>
              @else 
              <div class="card card-color mb-0">
                <div class="card-header">
                  <a class="card-title ">Sanction letter cannot be generated for this application as limit offer has not be added.</a>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
{!!Helpers::makeIframePopup('previewSanctionLetter','Preview/Send Mail Sanction Letter', 'modal-lg')!!}
{!!Helpers::makeIframePopup('uploadSanctionLetter','Upload Sanction Letter', 'modal-md')!!}
@endsection
@section('jscript')
<script>
  var messages = {
    get_applications: "{{ URL::route('ajax_app_list') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",

  };
  CKEDITOR.replace('delay_pymt_chrg');
  CKEDITOR.replace('insurance');
  CKEDITOR.replace('bank_chrg');
  CKEDITOR.replace('legal_cost');
  CKEDITOR.replace('po');
  CKEDITOR.replace('pdp');
  CKEDITOR.replace('disburs_guide');
  CKEDITOR.replace('other_cond');
  CKEDITOR.replace('covenants');
  CKEDITOR.replace('rating_rational');
  $(document).ready(function(){
    $('#payment_type').on('change', function(){
      $('#payment_type_comment').val('');
      if($(this).val()  == '5'){
        $('#payment_type_comment').removeClass('hide');
      }else{
        $('#payment_type_comment').addClass('hide');
      }
    })

    $("input[name='sanction_validity_date']").datetimepicker({
      format: 'dd/mm/yyyy',
      autoclose: true,
      minView : 2,
      startDate: '-0m',
    }).on('changeDate', function(e) {
      $("input[name='sanction_expire_date']").val(ChangeDateFormat(e.date,'dmy','/', 30));

    });

    $("input[name='sanction_expire_date']").datetimepicker({
      format: 'dd/mm/yyyy',
      autoclose: true,
      minView : 2,
      startDate: '+1m'
    });
  });


  function ChangeDateFormat(dateObj,out_format='ymd', out_separator='/', dateAddMinus=0){
    dateObj.setDate(dateObj.getDate() + dateAddMinus);
    var twoDigitMonth = ((dateObj.getMonth().length+1) === 1)? (dateObj.getMonth()+1) : '0' + (dateObj.getMonth()+1);
    var twoDigitDate = dateObj.getDate()+"";if(twoDigitDate.length==1) twoDigitDate= "0" + twoDigitDate;
    var Digityear = dateObj.getFullYear();
    switch(out_format) {
      case 'myd':
      outdate = twoDigitMonth + out_separator + Digityear + out_separator + twoDigitDate;
      break;
      case 'ydm':
      outdate = Digityear + out_separator + twoDigitDate + out_separator + twoDigitMonth;
      break;
      case 'dmy':
      outdate = twoDigitDate + out_separator + twoDigitMonth + out_separator + Digityear;
      break;
      case 'dym':
      outdate = twoDigitDate + out_separator + Digityear + out_separator + twoDigitMonth;
      break;
      case 'mdy':
      outdate = twoDigitMonth + out_separator + twoDigitDate + out_separator + Digityear;
      break;
      default:
      outdate = Digityear + out_separator + twoDigitMonth + out_separator + twoDigitDate;
      break;
    }
    return outdate;
  }

  $(document).on('click','.clone_covenants', function() {
    covenants_clone_tr_html =  $('.covenants_clone_tr').html();
    $('.FinancialCovenantsTBody').append("<tr>"+ covenants_clone_tr_html +"</tr>");
  })
  $(document).on('click','.remove_covenants', function() {
    totalrows = $('.FinancialCovenantsTBody').children().length;
    if (totalrows > 1) {
      $('.FinancialCovenantsTBody tr:last-child').remove(); 
    }
  })

</script>
@endsection