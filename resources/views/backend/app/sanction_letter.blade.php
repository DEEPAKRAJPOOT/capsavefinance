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
   .section6>ol>li,.section8>ol>li{
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
                        @csrf
                        @if(!empty($supplyChaindata['offerData']) && $supplyChaindata['offerData']->count())
                        <div class="form-fields">
                           <h5 class="card-title form-head-h5">Sanction Letter for Supply Chain
                           @if(!empty($supplyChainFormData))
                           <a data-toggle="modal" data-target="#previewSupplyChainSanctionLetter" data-height="500px" data-width="100%" data-placement="top" href="#" data-url="{{ route('preview_supply_chain_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId]) }}" class="btn btn-success btn-sm float-right mt-3 ml-3" style="margin: 5px 0 10px 0;">Preview/Send Mail</a>
                           @endif
                           </h5>
                           <table class="table" cellpadding="0" cellspacing="0">
                              <tr>
                                 <td><b>To</b></td>
                              </tr>
                              @if(!empty($supplyChaindata['ConcernedPersonName']))
                              <tr>
                                <td>{{$supplyChaindata['ConcernedPersonName']}}</td>
                              </tr>
                              @endif
                              @if(!empty($supplyChaindata['EntityName']))
                              <tr>
                                <td>{{$supplyChaindata['EntityName']}}</td>
                              </tr>
                               @endif
                               @if(!empty(trim($supplyChaindata['Address'])))
                              <tr>
                                <td>{{$supplyChaindata['Address']}}</td>
                              </tr>
                              @endif
                              @if(!empty($supplyChaindata['EmailId']))
                              <tr>
                                <td>{{$supplyChaindata['EmailId']}}</td>
                              </tr>
                              @endif
                              @if(!empty($supplyChaindata['MobileNumber']))
                              <tr>
                                <td>{{$supplyChaindata['MobileNumber']}}</td>
                              </tr>
                              @endif
                           </table>
                           <br />
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
                                    <td class="">&#8377; {{number_format($supplyChaindata['tot_limit_amt'])}}</td>
                                    <td class="">
                                       <select class="select" name="sublimit_of" id="sublimit_of">
                                       <option {{ !empty($supplyChainFormData['sublimit_of']) && $supplyChainFormData['sublimit_of'] == 'Term Loan' ? 'selected' : '' }}>Term Loan</option>
                                       <option {{ !empty($supplyChainFormData['sublimit_of']) && $supplyChainFormData['sublimit_of'] == 'Purchase Finance Facility' ? 'selected' : '' }}>Purchase Finance Facility</option>
                                       <option {{ !empty($supplyChainFormData['sublimit_of']) && $supplyChainFormData['sublimit_of'] == 'Invoice Discounting Facility' ? 'selected' : '' }}>Invoice Discounting Facility</option>
                                       <option {{ !empty($supplyChainFormData['sublimit_of']) && $supplyChainFormData['sublimit_of'] == 'Vendor Finance' ? 'selected' : '' }}>Vendor Finance</option>
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
                                    <td width="66.66%" colspan="3">&#8377; {{number_format($supplyChaindata['limit_amt'])}}</td>
                                 </tr>
                                 <tr>
                                    <td width="33.33%">Purpose</td>
                                    <td width="66.66%" colspan="3">{{$supplyChaindata['purpose']}}</td>
                                 </tr>
                                 <tr>
                                    <td width="33.33%">Expiry of Limit</td>
                                    <td width="66.66%" colspan="3"> Limit will be valid for 1 year from
                                       <select class="select" name="expiry_of_limit">
                                       <option {{ !empty($supplyChainFormData['expiry_of_limit']) && $supplyChainFormData['expiry_of_limit'] == 'date of sanction letter' ? 'selected' : '' }}>date of sanction letter</option>
                                       <option {{ !empty($supplyChainFormData['expiry_of_limit']) && $supplyChainFormData['expiry_of_limit'] == 'date of first disbusrement' ? 'selected' : '' }}>date of first disbusrement</option>
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
                                             <option {{!empty($supplyChainFormData['specific_cond']) && $supplyChainFormData['specific_cond'] == 'Invoice Date' ? 'selected' : '' }}>Invoice Date</option>
                                             <option {{!empty($supplyChainFormData['specific_cond']) && $supplyChainFormData['specific_cond'] == 'BOE Date' ? 'selected' : '' }}>BOE Date</option>
                                             <option {{!empty($supplyChainFormData['specific_cond']) && $supplyChainFormData['specific_cond'] == 'GRN Date' ? 'selected' : '' }}>GRN Date</option>
                                             <option {{!empty($supplyChainFormData['specific_cond']) && $supplyChainFormData['specific_cond'] == 'Date of Discounting' ? 'selected' : '' }}>Date of Discounting</option>
                                             </select> On the date of Discounting.
                                          </li>
                                          <li>Discounting proceed to be credited to working capital account of the borrowers.</li>
                                       </ul>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td width="33.33%">Specific Pre-disbursement Condition</td>
                                    <td width="66.66%" colspan="3">
                                        @if(!empty($supplyChaindata['reviewerSummaryData']['preCond']))
                                       <ul style="padding:0px 0px 0px 15px; margin:0px; line-height:23px;list-style-type:unset;">
                                          @foreach($supplyChaindata['reviewerSummaryData']['preCond'] as $k => $precond)
                                          <li>{{$precond}}</li>
                                          @endforeach
                                       </ul>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td width="33.33%"> Specific Post-disbursement Condition</td>
                                    <td width="66.66%" colspan="3">
                                       @if(!empty($supplyChaindata['reviewerSummaryData']['postCond']))
                                       <ul style="padding:0px 0px 0px 15px; margin:0px; line-height:23px;list-style-type:unset;">
                                          @foreach($supplyChaindata['reviewerSummaryData']['postCond'] as $k => $postcond)
                                          <li>{{$postcond}}</li>
                                          @endforeach
                                       </ul>
                                       @endif
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                           <br />
                           @foreach($supplyChaindata['offerData'] as $key =>  $offerD)
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
                                                <td>&#8377; {{number_format($offerD->prgm_limit_amt)}}</td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </td>
                                    <td width="66.66%" colspan="3" class="pd-0" style="padding: 0px !important;">
                                       <table class="table-border table table-inner" cellpadding="0" cellspacing="0">
                                          <thead>
                                             <tr>
                                                <th width="30%">Max. Discounting Period (days)</th>
                                                <th width="20%">Grace Period (days)</th>
                                                <th width="15%">ROI (%)</th>
                                                <th width="20%">Bench Mark Date</th>
                                                <th width="15%">Margin (%)</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                             <tr>
                                                <td>{{$offerD->tenor}}</td>
                                                <td>{{$offerD->grace_period}}</td>
                                                <td>{{$offerD->interest_rate}}</td>
                                                <td>{{getBenchmarkType($offerD->benchmark_date)}}</td>
                                                <td>{{$offerD->margin}}</td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td width="33.33%">Investment Payment Frequency</td>
                                    <td width="66.66%" colspan="3">{{getInvestmentPaymentFrequency($offerD['payment_frequency'])}}</td>
                                 </tr>
                                 <tr>
                                    <td width="33.33%">Methodology for calculating for  Drawing Power</td>
                                    <td width="66.66%" colspan="3">As mentioned in Margin Section</td>
                                  </tr>
                                 <tr>
                                    <td width="33.33%">Penal Interest</td>
                                    <td width="66.66%">
                                       <ul style="padding:0px 0px 0px 15px; margin:0px; line-height:23px;list-style-type:unset;">
                                          <li>{{!empty($offerD['overdue_interest_rate']) ? $offerD['overdue_interest_rate'] .'%' : ''}}
                                             <select class="select" name="penal_on[{{$key}}][]">
                                             <option {{!empty($supplyChainFormData['penal_on'][$key][0]) && $supplyChainFormData['penal_on'][$key][0] == 'On' ? 'selected' : '' }}>On</option>
                                             <option {{!empty($supplyChainFormData['penal_on'][$key][0]) && $supplyChainFormData['penal_on'][$key][0] == 'over and above the rate for the last draw down or Rollover of facility on' ? 'selected' : '' }}>over and above the rate for the last draw down  or Rollover of facility on</option>
                                             </select> entire principal / payable interest on delay in repayment of principal / Interest / charges <select class="select" name="penal_applicable[{{$key}}][]">
                                             <option {{!empty($supplyChainFormData['penal_applicable'][$key][0]) && $supplyChainFormData['penal_applicable'][$key][0] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                             <option {{!empty($supplyChainFormData['penal_applicable'][$key][0]) && $supplyChainFormData['penal_applicable'][$key][0] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                             </select>
                                          </li>
                                          <li>The rate of interest will be {{!empty($offerD['overdue_interest_rate']) ? $offerD['overdue_interest_rate'] .'%' : ''}} higher than the rate stipulated under each of the facilities till the security is created
                                             <select class="select" name="penal_applicable[{{$key}}][]">
                                             <option {{ !empty($supplyChainFormData['penal_applicable'][$key][1]) &&  $supplyChainFormData['penal_applicable'][$key][1] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                             <option {{ !empty($supplyChainFormData['penal_applicable'][$key][1]) &&  $supplyChainFormData['penal_applicable'][$key][1] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                             </select>
                                          </li>
                                          <li>If security is not created within the stipulated timeframe then a penal interest of 
                                             {{!empty($offerD['overdue_interest_rate']) ? $offerD['overdue_interest_rate'] .'%' : ''}} p.a.  
                                             <select class="select" name="penal_on[{{$key}}][]">
                                             <option {{!empty($supplyChainFormData['penal_on'][$key][1]) && $supplyChainFormData['penal_on'][$key][1] == 'On' ? 'selected' : '' }}>On</option>
                                             <option {{!empty($supplyChainFormData['penal_on'][$key][1]) && $supplyChainFormData['penal_on'][$key][1] == 'over and above the rate for the last draw down or Rollover of facility on' ? 'selected' : '' }}>over and above the rate for the last draw down  or Rollover of facility on</option>
                                             </select> entire principle <select class="select" name="penal_applicable[{{$key}}][]"><option {{!empty($supplyChainFormData['penal_applicable'][$key][2]) && $supplyChainFormData['penal_applicable'][$key][2] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                             <option {{!empty($supplyChainFormData['penal_applicable'][$key][2]) && $supplyChainFormData['penal_applicable'][$key][2] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option></select>
                                          </li>
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
                                    <td width="66.66%"><textarea class="form-control" name="prepayment">{{$supplyChainFormData['prepayment'] ?? 'In case borrower desires to prepay the loan, the prepayment of loan will be accepted on the terms and conditions to be decided by CFPL for time to time.'}}</textarea>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td width="33.33%">Payment Mechanism of Interest</td>
                                    <td width="66.66%">
                                       <select class="select" name="payment_machanism_of_interest">
                                       <option {{ !empty($supplyChainFormData['payment_machanism_of_interest']) && $supplyChainFormData['payment_machanism_of_interest'] == 'UDC' ? 'selected' : '' }}>UDC</option>
                                       <option {{ !empty($supplyChainFormData['payment_machanism_of_interest']) && $supplyChainFormData['payment_machanism_of_interest'] == 'PDC' ? 'selected' : '' }}>PDC</option>
                                       <option {{ !empty($supplyChainFormData['payment_machanism_of_interest']) && $supplyChainFormData['payment_machanism_of_interest'] == 'ECS Mandate' ? 'selected' : '' }}>ECS Mandate</option>
                                       <option {{ !empty($supplyChainFormData['payment_machanism_of_interest']) && $supplyChainFormData['payment_machanism_of_interest'] == 'RTGS' ? 'selected' : '' }}>RTGS</option>
                                       </select>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td width="33.33%">Payment Mechanism of Principal</td>
                                    <td width="66.66%">
                                       <select class="select" name="payment_machanism_of_principal">
                                       <option {{ !empty($supplyChainFormData['payment_machanism_of_principal']) &&  $supplyChainFormData['payment_machanism_of_principal'] == 'UDC' ? 'selected' : '' }}>UDC</option>
                                       <option {{ !empty($supplyChainFormData['payment_machanism_of_principal']) &&  $supplyChainFormData['payment_machanism_of_principal'] == 'PDC' ? 'selected' : '' }}>PDC</option>
                                       <option {{ !empty($supplyChainFormData['payment_machanism_of_principal']) &&  $supplyChainFormData['payment_machanism_of_principal'] == 'ECS Mandate' ? 'selected' : '' }}>ECS Mandate</option>
                                       <option {{ !empty($supplyChainFormData['payment_machanism_of_principal']) &&  $supplyChainFormData['payment_machanism_of_principal'] == 'RTGS' ? 'selected' : '' }}>RTGS</option>
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
                                       <td>&#8377; {{number_format($PersonalGuarantee->pg_net_worth)}}</td>
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
                                       <td>{{$supplyChaindata['anchorData'][$EscrowMechanism->em_debtor_id]['comp_name'] ?? ''}}</td>
                                       <td>&#8377; {{number_format($EscrowMechanism->em_expected_cash_flow)}}</td>
                                       <td>{{config('common.em_time_for_perfecting_security_id.'.$EscrowMechanism->em_time_for_perfecting_security_id)}}</td>
                                       <td>{{getMechanism($EscrowMechanism->em_mechanism_id)}}</td>
                                       <td>{{$EscrowMechanism->em_comments}}</td>
                                    </tr>
                                    @endforeach
                                 </tbody>
                              </table>
                              @endif
                           </div>
                           @endforeach
                           <h5>Section 3:Specific Security<select class="select" name="specific_security">
                              <option {{!empty($supplyChainFormData['specific_security']) && $supplyChainFormData['specific_security'] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                              <option {{!empty($supplyChainFormData['specific_security']) && $supplyChainFormData['specific_security'] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                              </select>
                           </h5>
                           <h5>Section 4:- Security PDCs/ECS Mandate with Undertaking, DSRA and Other Securities</h5>
                           <h5 style="background-color: #ccc;padding: 10px;margin-bottom: 0;">PDC</h5>
                           <table  class="table table-border"  cellpadding="0" cellspacing="0">
                              <tbody>
                                 <tr>
                                    <td width="20%">Facility No</td>
                                    <td width="20%"><input type="text" value="{{$supplyChainFormData['pdc_facility_no'] ?? ''}}" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc" placeholder="Click here to enter text"></td>
                                    <td width="30%">Facility Name</td>
                                    <td width="30%"><input type="text" value="{{$supplyChainFormData['pdc_facility_name'] ?? ''}}" name="pdc_facility_name" id="pdc_facility_name" class="input_sanc" placeholder="Click here to enter text"></td>
                                 </tr>
                                 <tr>
                                    <td width="33.33%">Facility Amount</td>
                                    <td width="66.66%" colspan="3"><input type="text" value="&#8377; {{number_format($supplyChainFormData['pdc_facility_amt']) ?? ''}}" name="pdc_facility_amt" id="pdc_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                                 </tr>
                                 <tr>
                                    <td width="33.33%">Purpose</td>
                                    <td width="66.66%" colspan="3"><input type="text" value="{{$supplyChainFormData['pdc_facility_purpose'] ?? ''}}" name="pdc_facility_purpose" id="pdc_facility_purpose" class="input_sanc" placeholder="Click here to enter text"></td>
                                 </tr>
                              </tbody>
                           </table>
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
                                    <td><input type="text" value="{{$supplyChainFormData['pdc_no_of_cheque']['0'] ?? ''}}" name="pdc_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                                    <td><input type="text" value="{{$supplyChainFormData['pdc_not_above']['0'] ?? ''}}" name="pdc_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                                 </tr>
                                 <tr>
                                    <td >Interest</td>
                                    <td><input type="text" value="{{$supplyChainFormData['pdc_no_of_cheque']['1'] ?? ''}}" name="pdc_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                                    <td><input type="text" value="{{$supplyChainFormData['pdc_not_above']['1'] ?? ''}}" name="pdc_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                                 </tr>
                                 <tr>
                                    <td>Repayment</td>
                                    <td><input type="text" value="{{$supplyChainFormData['pdc_no_of_cheque']['2'] ?? ''}}" name="pdc_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                                    <td><input type="text" value="{{$supplyChainFormData['pdc_not_above']['2'] ?? ''}}" name="pdc_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                                 </tr>
                                 <tr>
                                    <td>Other</td>
                                    <td><input type="text" value="{{$supplyChainFormData['pdc_no_of_cheque']['3'] ?? ''}}" name="pdc_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                                    <td><input type="text" value="{{$supplyChainFormData['pdc_not_above']['3'] ?? ''}}" name="pdc_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                                 </tr>
                                 <tr>
                                    <td>security</td>
                                    <td><input type="text" value="{{$supplyChainFormData['pdc_no_of_cheque']['4'] ?? ''}}" name="pdc_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                                    <td><input type="text" value="{{$supplyChainFormData['pdc_not_above']['4'] ?? ''}}" name="pdc_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                                 </tr>
                              </tbody>
                           </table>
                           <br />
                           <h5 style="background-color: #ccc;padding: 10px;margin-bottom: 0;">NACH Mandate with undertaking</h5>
                           <table  class="table table-border"  cellpadding="0" cellspacing="0">
                              <tbody>
                                 <tr>
                                    <td width="20%">Facility No</td>
                                    <td width="20%"><input type="text" value="{{$supplyChainFormData['nach_facility_no'] ?? ''}}" name="nach_facility_no" id="nach_facility_no" class="input_sanc"></td>
                                    <td width="30%">Facility Name</td>
                                    <td width="30%"><input type="text" value="{{$supplyChainFormData['nach_facility_name'] ?? ''}}" name="nach_facility_name" id="nach_facility_name" class="input_sanc"></td>
                                 </tr>
                                 <tr>
                                    <td width="33.33%">Facility Amount</td>
                                    <td width="66.66%" colspan="3"><input type="text" value="&#8377; {{number_format($supplyChainFormData['nach_facility_amt']) ?? ''}}" name="nach_facility_amt" id="nach_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                                 </tr>
                                 <tr>
                                    <td width="33.33%">Purpose</td>
                                    <td width="66.66%" colspan="3"><input type="text" value="{{$supplyChainFormData['nach_facility_purpose'] ?? ''}}" name="nach_facility_purpose" id="nach_facility_purpose" class="input_sanc" placeholder="Click here to enter text"></td>
                                 </tr>
                              </tbody>
                           </table>
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
                                    <td><input type="text" value="{{$supplyChainFormData['nach_no_of_cheque']['0'] ?? ''}}" name="nach_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                                    <td><input type="text" value="{{$supplyChainFormData['nach_not_above']['0'] ?? ''}}" name="nach_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                                 </tr>
                                 <tr>
                                    <td >Interest</td>
                                    <td><input type="text" value="{{$supplyChainFormData['nach_no_of_cheque']['1'] ?? ''}}" name="nach_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                                    <td><input type="text" value="{{$supplyChainFormData['nach_not_above']['1'] ?? ''}}" name="nach_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                                 </tr>
                                 <tr>
                                    <td>Repayment</td>
                                    <td><input type="text" value="{{$supplyChainFormData['nach_no_of_cheque']['2'] ?? ''}}" name="nach_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                                    <td><input type="text" value="{{$supplyChainFormData['nach_not_above']['2'] ?? ''}}" name="nach_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                                 </tr>
                                 <tr>
                                    <td>Other</td>
                                    <td><input type="text" value="{{$supplyChainFormData['nach_no_of_cheque']['3'] ?? ''}}" name="nach_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                                    <td><input type="text" value="{{$supplyChainFormData['nach_not_above']['3'] ?? ''}}" name="nach_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                                 </tr>
                                 <tr>
                                    <td>security</td>
                                    <td><input type="text" value="{{$supplyChainFormData['nach_no_of_cheque']['4'] ?? ''}}" name="nach_no_of_cheque[]" class="input_sanc" placeholder="Enter no of Cheques"></td>
                                    <td><input type="text" value="{{$supplyChainFormData['nach_not_above']['4'] ?? ''}}" name="nach_not_above[]" class="input_sanc" placeholder="Enter Not above"></td>
                                 </tr>
                              </tbody>
                           </table>
                           <br />
                           <h5>DSRA <select class="select" name="dsra_applicability">
                              <option {{!empty($supplyChainFormData['dsra_applicability']) && $supplyChainFormData['dsra_applicability'] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                              <option {{!empty($supplyChainFormData['dsra_applicability']) && $supplyChainFormData['dsra_applicability'] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                              </select>
                           </h5>
                           <table  class="table table-border"  cellpadding="0" cellspacing="0">
                              <thead>
                                 <tr>
                                    <th>Amount (lacs in INR )</th>
                                    <th>Tenure (months)</th>
                                    <th>Comment if any</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr>
                                    <td><input value="&#8377; {{number_format($supplyChainFormData['dsra_amt']) ?? ''}}" type="text" name="dsra_amt" class="input_sanc" placeholder="Enter DSRA Amount"></td>
                                    <td><input value="{{$supplyChainFormData['dsra_tenure'] ?? ''}}" type="text" name="dsra_tenure" class="input_sanc" placeholder="Enter DSRA Tenure"></td>
                                    <td><input value="{{$supplyChainFormData['dsra_comment'] ?? ''}}" type="text" name="dsra_comment" class="input_sanc" placeholder="Comment if any"></td>
                                 </tr>
                              </tbody>
                           </table>
                           <br />
                           <h5>Any other security <select class="select" name="dsra_applicability">
                              <option {{!empty($supplyChainFormData['dsra_applicability']) && $supplyChainFormData['dsra_applicability'] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                              <option {{!empty($supplyChainFormData['dsra_applicability']) && $supplyChainFormData['dsra_applicability'] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                              </select>
                           </h5>
                           <table  class="table table-border"  cellpadding="0" cellspacing="0">
                              <tbody>
                                 <tr>
                                    <td><input value="{{$supplyChainFormData['other_sucurities'] ?? ''}}" type="text" name="other_sucurities" id="other_sucurities" class="input_sanc" placeholder="Click here to enter Securities"></td>
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
                                 <?php
                                    $i = 0; 
                                    do { ?>
                                 <tr class="covenants_clone_tr">
                                    <td><input value="{{ $supplyChainFormData['covenants']['name'][$i] ?? ''}}" type="text" name="covenants[name][]" class="input_sanc" placeholder="Enter Covenants"></td>
                                    <td><input value="{{ $supplyChainFormData['covenants']['ratio'][$i] ?? ''}}" type="text" name="covenants[ratio][]" class="input_sanc" placeholder="Enter Minimum/Maximum ratio"></td>
                                    <td><select class="select" name="covenants[ratio_applicability][]">
                                       <option {{!empty($supplyChainFormData['covenants']['ratio_applicability'][$i]) && $supplyChainFormData['covenants']['ratio_applicability'][$i] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                       <option {{!empty($supplyChainFormData['covenants']['ratio_applicability'][$i]) && $supplyChainFormData['covenants']['ratio_applicability'][$i] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                       </select>
                                    </td>
                                 </tr>
                                 <?php $i++; } while(!empty($supplyChainFormData['covenants']['name'][$i])); ?>
                              </tbody>
                           </table>
                           <p>The financial covenants shall be tested on a choose an item.basis and shall be reported in the monitoring report to be submitted by choose an item.</p>
                           <h5>Section 6:- General Pre-disbursement and Post Disbursement conditions</h5>
                           <div class="section6">
                              <ol>
                                 <li>Form CHG-1 to be filed with ROC within 30 days from the date of execution of Security Documents of the borrower/Corporate Guarantor<select class="select hide" name="pre_post_condition[]">
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][0]) && $supplyChainFormData['pre_post_condition'][0] == 'Applicable' ? 'selected' : '' }}>Applicable</option></select>
                                 </li>
                                 <li>CFPL shall, at its discretion, obtain a confidential credit report on the borrower from its other lenders.
                                    <select class="select" name="pre_post_condition[]">
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][1]) && $supplyChainFormData['pre_post_condition'][1] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][1]) && $supplyChainFormData['pre_post_condition'][1] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                    </select>
                                 </li>
                                 <li>All the assets charged to the CFPL are to be insured for full value covering all risks with usual CFPL clause. A copy of the insurance policy(ies) to be furnished to the CFPL within 30 days of security perfection.<select class="select hide" name="pre_post_condition[]"><option {{!empty($supplyChainFormData['pre_post_condition'][2]) && $supplyChainFormData['pre_post_condition'][2] == 'Applicable' ? 'selected' : '' }}>Applicable</option></select>
                                 </li>
                                 <li>The obligation of the Lender to make disbursements out of the Facility shall be subject to the Borrower complying with the following conditions to the satisfaction of CFPL .The Borrower shall complete all documentation as stipulated, to the satisfaction of CFPL.The Borrower to furnish title investigation search and valuation of security ( being mortgaged to CFPL) prior to disbursement.<select class="select" name="pre_post_condition[]">
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][3]) && $supplyChainFormData['pre_post_condition'][3] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][3]) && $supplyChainFormData['pre_post_condition'][3] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                    </select>
                                 </li>
                                 <li>The borrower shall finalise its selling arrangements to the satisfaction of CFPL.
                                    <select class="select" name="pre_post_condition[]">
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][4]) && $supplyChainFormData['pre_post_condition'][4] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][4]) && $supplyChainFormData['pre_post_condition'][4] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                    </select>
                                 </li>
                                 <li>The borrower shall obtain necessary sanction of power, water, fuel, etc from the relevant authorities to the satisfaction of CFPL. 
                                    <select class="select" name="pre_post_condition[]">
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][5]) && $supplyChainFormData['pre_post_condition'][5] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][5]) && $supplyChainFormData['pre_post_condition'][5] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                    </select>
                                 </li>
                                 <li>The borrower shall make adequate arrangements for treatment and disposal of effluents, solid waste and emissions from its project and shall furnish appropriate approvals from the authorities in this regard.
                                    <select class="select" name="pre_post_condition[]">
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][6]) && $supplyChainFormData['pre_post_condition'][6] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][6]) && $supplyChainFormData['pre_post_condition'][6] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                    </select>
                                 </li>
                                 <li>The borrower shall broadbase its Board of Directors and finalise and strengthen its management set-up to the satisfaction of CFPL, if necessary. <select class="select" name="pre_post_condition[]">
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][7]) && $supplyChainFormData['pre_post_condition'][7] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][7]) && $supplyChainFormData['pre_post_condition'][7] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                    </select>
                                 </li>
                                 <li>The borrower shall carry out safety/environment/energy audit of its project to the satisfaction of CFPL.
                                    <select class="select" name="pre_post_condition[]">
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][8]) && $supplyChainFormData['pre_post_condition'][8] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                    <option {{!empty($supplyChainFormData['pre_post_condition'][8]) && $supplyChainFormData['pre_post_condition'][8] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                    </select>
                                 </li>
                                 <li>CFPL reserves the right to appoint qualified accountants / technical experts /management consultants of its choice to examine the books of accounts, factories and operations of the borrower or to carry out a full concurrent/statutory audit. The cost of such inspection shall be borne by the <select class="select" name="abfl_or_borrower"><option {{!empty($supplyChainFormData['abfl_or_borrower']) && $supplyChainFormData['abfl_or_borrower'] == 'Borrower' ? 'selected' : ''}}>Borrower</option><option {{!empty($supplyChainFormData['abfl_or_borrower']) && $supplyChainFormData['abfl_or_borrower'] == 'ABFL' ? 'selected' : ''}}>ABFL</option></select><select class="select hide" name="pre_post_condition[]"><option {{!empty($supplyChainFormData['pre_post_condition'][9]) && $supplyChainFormData['pre_post_condition'][9] == 'Applicable' ? 'selected' : '' }}>Applicable</option></select></li>
                                 <li>In case any condition is stipulated by any other lender that is more favorable to them than the terms stipulated by CFPL, CFPL shall at its discretion, apply to this loan such equivalent conditions to bring its loan at par with those of the other lenders. <select class="select hide" name="pre_post_condition[]"><option {{!empty($supplyChainFormData['pre_post_condition']['10']) && $supplyChainFormData['pre_post_condition']['10'] == 'Applicable' ? 'selected' : '' }}>Applicable</option></select></li>
                                 <li>
                                    The borrower shall forward to CFPL, provisional balance sheet and Profit & Loss Account within 
                                    <select class="select" name="profit_loss_account_within">
                                        <option {{!empty($supplyChainFormData['profit_loss_account_within']) && $supplyChainFormData['profit_loss_account_within'] == '1' ? 'selected' : '' }}>1</option>
                                       <option {{!empty($supplyChainFormData['profit_loss_account_within']) && $supplyChainFormData['profit_loss_account_within'] == '2' ? 'selected' : '' }}>2</option>
                                       <option {{!empty($supplyChainFormData['profit_loss_account_within']) && $supplyChainFormData['profit_loss_account_within'] == '3' ? 'selected' : '' }}>3</option>
                                       <option {{!empty($supplyChainFormData['profit_loss_account_within']) && $supplyChainFormData['profit_loss_account_within'] == '4' ? 'selected' : '' }}>4</option>
                                    </select>
                                    months of year-end and audited accounts within 6 months of year end. Quarterly financial results shall be submitted within 60 days from the end of each quarter or with the filing with stock exchange for listed borrower.<select class="select hide" name="pre_post_condition[]"><option {{!empty($supplyChainFormData['pre_post_condition'][11]) && $supplyChainFormData['pre_post_condition'][11] == 'Applicable' ? 'selected' : '' }}>Applicable</option></select>
                                 </li>
                                 <li>
                                    Inspection of assets charged to CFPL may be carried out once in 
                                    <select class="select" name="cfpl_carried_in">
                                       <option {{!empty($supplyChainFormData['cfpl_carried_in']) && $supplyChainFormData['cfpl_carried_in'] == '1' ? 'selected' : '' }}>1</option>
                                       <option {{!empty($supplyChainFormData['cfpl_carried_in']) && $supplyChainFormData['cfpl_carried_in'] == '2' ? 'selected' : '' }}>2</option>
                                       <option {{!empty($supplyChainFormData['cfpl_carried_in']) && $supplyChainFormData['cfpl_carried_in'] == '3' ? 'selected' : '' }}>3</option>
                                       <option {{!empty($supplyChainFormData['cfpl_carried_in']) && $supplyChainFormData['cfpl_carried_in'] == '4' ? 'selected' : '' }}>4</option>
                                    </select>
                                    months or at more frequent intervals as decided by CFPL by its own officials or through persons/firm appointed by CFPL. The cost of inspection is to be borne by the borrower.<select class="select hide" name="pre_post_condition[]"><option {{!empty($supplyChainFormData['pre_post_condition'][12]) && $supplyChainFormData['pre_post_condition'][12] == 'Applicable' ? 'selected' : '' }}>Applicable</option></select>
                                 </li>
                                 <li>
                                    During the currency of CFPL’s credit facility(s), the borrower will not without CFPL’s prior <select class="select" name="cfpl_prior"><option {{!empty($supplyChainFormData['cfpl_prior']) && $supplyChainFormData['cfpl_prior'] == 'Permission' ? 'selected' : ''}}>Permission</option><option {{!empty($supplyChainFormData['cfpl_prior']) && $supplyChainFormData['cfpl_prior'] == 'Intimation' ? 'selected' : ''}}>Intimation</option></select> in writing: 
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
                                    <select class="select hide" name="pre_post_condition[]"><option {{!empty($supplyChainFormData['pre_post_condition'][13]) && $supplyChainFormData['pre_post_condition'][13] == 'Applicable' ? 'selected' : '' }}>Applicable</option></select>
                                 </li>
                              </ol>
                           </div>
                           <h5>Section 7:- Monitoring Conditions </h5>
                           <div class="section7">
                                  <ul style="list-style-type:unset;">
                                    <li><select class="select" name="monitoring_condition_1">
                                    <option {{!empty($supplyChainFormData['monitoring_condition_1']) && $supplyChainFormData['monitoring_condition_1'] == 'Quarterly Information Statement(QIS)' ? 'selected' : '' }}>Quarterly Information Statement(QIS)</option>
                                    <option {{!empty($supplyChainFormData['monitoring_condition_1']) && $supplyChainFormData['monitoring_condition_1'] == 'Financial Followup Report (FFR)' ? 'selected' : '' }}>Financial Followup Report (FFR)</option>
                                    </select> or <select class="select" name="monitoring_condition_2">
                                    <option {{!empty($supplyChainFormData['monitoring_condition_2']) && $supplyChainFormData['monitoring_condition_2'] == 'Quarterly Information Statement(QIS)' ? 'selected' : '' }}>Quarterly Information Statement(QIS)</option>
                                    <option {{!empty($supplyChainFormData['monitoring_condition_2']) && $supplyChainFormData['monitoring_condition_2'] == 'Financial Followup Report (FFR)' ? 'selected' : '' }}>Financial Followup Report (FFR)</option>
                                    </select> to be submitted as under:
                                    </li>
                                    <li>
                                    Stock and Book Debt statements <select class="select" name="stock_n_book_statement">
                                    <option {{!empty($supplyChainFormData['stock_n_book_statement']) && $supplyChainFormData['stock_n_book_statement'] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                    <option {{!empty($supplyChainFormData['stock_n_book_statement']) && $supplyChainFormData['stock_n_book_statement'] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                    </select>
                                    </li>
                                    <li>
                                      The stock and book debt statement as on the last day of the month is to be submitted by
                                      <select class="select" name="stock_n_book_debt_date1">
                                       <option {{!empty($supplyChainFormData['stock_n_book_debt_date1']) && $supplyChainFormData['stock_n_book_debt_date1'] == '4' ? 'selected' : '' }}>4</option>
                                       <option {{!empty($supplyChainFormData['stock_n_book_debt_date1']) && $supplyChainFormData['stock_n_book_debt_date1'] == '5' ? 'selected' : '' }}>5</option>
                                       <option {{!empty($supplyChainFormData['stock_n_book_debt_date1']) && $supplyChainFormData['stock_n_book_debt_date1'] == '6' ? 'selected' : '' }}>6</option>
                                       <option {{!empty($supplyChainFormData['stock_n_book_debt_date1']) && $supplyChainFormData['stock_n_book_debt_date1'] == '7' ? 'selected' : '' }}>7</option>
                                       <option {{!empty($supplyChainFormData['stock_n_book_debt_date1']) && $supplyChainFormData['stock_n_book_debt_date1'] == '8' ? 'selected' : '' }}>8</option>
                                    </select>
                                    <select class="select" name="stock_n_book_debt_date2">
                                       <option {{!empty($supplyChainFormData['stock_n_book_debt_date2']) && $supplyChainFormData['stock_n_book_debt_date2'] == '4' ? 'selected' : '' }}>4</option>
                                       <option {{!empty($supplyChainFormData['stock_n_book_debt_date2']) && $supplyChainFormData['stock_n_book_debt_date2'] == '5' ? 'selected' : '' }}>5</option>
                                       <option {{!empty($supplyChainFormData['stock_n_book_debt_date2']) && $supplyChainFormData['stock_n_book_debt_date2'] == '6' ? 'selected' : '' }}>6</option>
                                       <option {{!empty($supplyChainFormData['stock_n_book_debt_date2']) && $supplyChainFormData['stock_n_book_debt_date2'] == '7' ? 'selected' : '' }}>7</option>
                                       <option {{!empty($supplyChainFormData['stock_n_book_debt_date2']) && $supplyChainFormData['stock_n_book_debt_date2'] == '8' ? 'selected' : '' }}>8</option>
                                    </select>
                                       th of next month.Basis of Valuation of Inventory and Book Debts.<select class="select" name="stock_n_book_statement_applicable">
                                    <option {{!empty($supplyChainFormData['stock_n_book_statement_applicable']) && $supplyChainFormData['stock_n_book_statement_applicable'] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                    <option {{!empty($supplyChainFormData['stock_n_book_statement_applicable']) && $supplyChainFormData['stock_n_book_statement_applicable'] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                    </select>
                                    </li>
                                  </ul>
                                 <table  class="table table-border"  cellpadding="0" cellspacing="0">
                                    <tbody>
                                       <tr>
                                          <td width="33.33%">Raw Material</td>
                                          <td width="66.67%">At Cost Price or Market Price, whichever is lower</td>
                                       </tr>
                                       <tr>
                                          <td>Stock in Process</td>
                                          <td>At Cost of production</td>
                                       </tr>
                                       <tr>
                                          <td>Stores and Spares</td>
                                          <td>At Cost Price or Market Price, whichever is lower</td>
                                       </tr>
                                       <tr>
                                          <td>Finished Goods</td>
                                          <td>At Cost of Sales or Controlled Price or Market Price, whichever is lower</td>
                                       </tr>
                                       <tr>
                                          <td>Domestic receivables(Period upto 90/120 days)</td>
                                          <td>At invoice Value</td>
                                       </tr>
                                       <tr>
                                          <td>Export receivables(Period upto 90/120 days)</td>
                                          <td>At invoice Value</td>
                                       </tr>
                                    </tbody>
                                 </table>
                                 <ul style="list-style-type:unset;">
                                    <li>Any other document for post disbursement monitoring <select class="select" name="any_other_doc_monitoring">
                                    <option {{!empty($supplyChainFormData['any_other_doc_monitoring']) && $supplyChainFormData['any_other_doc_monitoring'] == 'Applicable' ? 'selected' : '' }}>Applicable</option>
                                    <option {{!empty($supplyChainFormData['any_other_doc_monitoring']) && $supplyChainFormData['any_other_doc_monitoring'] == 'Not applicable' ? 'selected' : '' }}>Not applicable</option>
                                    </select>
                                    </li>
                                    <li><input type="text" value="{{$supplyChainFormData['any_other_doc_monitoring_1'] ?? ''}}" name="any_other_doc_monitoring_1" id="any_other_doc_monitoring_1" class="input_sanc" placeholder="Click here to enter text"></li>
                                    <li><input type="text" value="{{$supplyChainFormData['any_other_doc_monitoring_2'] ?? ''}}" name="any_other_doc_monitoring_2" id="any_other_doc_monitoring_2" class="input_sanc" placeholder="Click here to enter text"></li>
                                    <li><input type="text" value="{{$supplyChainFormData['any_other_doc_monitoring_3'] ?? ''}}" name="any_other_doc_monitoring_3" id="any_other_doc_monitoring_3" class="input_sanc" placeholder="Click here to enter text"></li>
                                  </ul>
                                  <p>
                                     Non submission of any of the above mentioned documents within the stipulated timelines, CFPL shall reserve the right to charge penalty from the due date of such submission at 2% p.a over and above the prevailing interest rates.
                                  </p>
                                 <br />
                           </div>
                           <h5>Section 8:- General Conditions </h5>
                           <div class="section8">
                              <ol>
                                    <li>The loan shall be utilised for the purpose for which it is sanctioned and it should not be utilised for -
                                      <ul style="list-style-type:unset;">
                                        <li>Subscription to or purchase of shares/debentures.</li>
                                        <li>Extending loans to subsidiary companies/associates or for making inter-corporate deposits.</li>
                                        <li>Any speculative purposes.</li>
                                      </ul>
                                    </li>
                                    <li>The borrower shall maintain adequate books and records which should correctly reflect their financial position and operations and it should submit to CFPL at regular intervals such statements as may be prescribed by CFPL in terms of the RBI / Bank's instructions issued from time to time.</li>
                                    <li>The borrower will keep CFPL informed of the happening of any event which is likely to have an impact on their profit or business and more particularly, if the monthly production or sale and profit are likely to be substantially lower than already indicated to CFPL. The borrower will inform accordingly with reasons and the remedial steps proposed to be taken. </li>
                                    <li>CFPL will have the right to examine at all times the borrower's books of accounts and to have the borrower's factory(s)/branches inspected from time to time by officer(s) of the CFPL and/or qualified auditors including stock audit and/or technical experts and/or management consultants of CFPL's choice and/or we can also get the stock audit conducted by other banker. The cost of such inspections will be borne by the borrower.</li>
                                    <li>The borrower should not pay any consideration by way of commission, brokerage, fees or in any other form to guarantors directly or indirectly.</li>
                                    <li>The Borrower and Guarantor(s) shall be deemed to have given their express consent to CFPL to disclose the information and data furnished by them to CFPL and also those regarding the credit facility/ies enjoyed by the borrower, conduct of accounts and guarantee obligations undertaken by guarantor to the Credit Information Bureau (India) Ltd. ("CIBIL"), or RBI or any other agencies specified by RBI who are authorised to seek and publish information.</li>
                                    <li>The Borrower will keep the CFPL advised of any circumstances adversely affecting their financial position including any action taken by any creditor, Government authority against them.</li>
                                    <li>The borrower shall procure a consent every year from the auditors appointed by the borrower to comply with and give report / specific comments in respect of any query or requisition made by us as regards the audited accounts or balance sheet of the borrower. We may provide information and documents to the Auditors in order to enable the Auditors to carry out the investigation requested by us. In that event, we shall be entitled to make specific queries to the Auditors in the light of Statements, particulars and other information submitted by the borrower to us for the purpose of availing finance, and the Auditors shall give specific comments on the queries made by us.</li>
                                    <li>The sanction limits would be valid for acceptance for 30 days from the date of the issuance of letter.</li>
                                    <li>CFPL reserves the right to alter, amend any of the condition or withdraw the facility, at any time without assigning any reason and without giving any notice.</li>
                                    <li>Provided further that notwithstanding anything to the contrary contained in this Agreement, CFPL may at its sole and absolute discretion at any time, terminate, cancel or withdraw the Loan or any part thereof (even if partial or no disbursement is made) without any liability and without any obligation to give any reason whatsoever, whereupon all principal monies, interest thereon and all other costs, charges, expenses and other monies outstanding (if any) shall become due and payable to CFPL by the Borrower forthwith upon demand from CFPL</li>
                              </ol>
                           </div>
                        </div>
                        <input type="hidden" name="app_id" value="{{$appId ?? ''}}">
                        <input type="hidden" name="offer_id" value="{{$offerId ?? ''}}">
                        <input type="hidden" name="biz_id" value="{{$bizId ?? ''}}">
                        <button type="submit" class="btn  btn-success btn-sm float-right">Submit</button>  
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
{!!Helpers::makeIframePopup('previewSupplyChainSanctionLetter','Send Mail SupplyChain Letter', 'modal-lg')!!}
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