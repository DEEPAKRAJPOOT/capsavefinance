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
   .nav-tabs li{
   background-color: #ccc;
   padding: 5px;
   margin: 5px;
   border-radius: 5px;
   }
   .nav-tabs li a{
    color: #fff;
    text-decoration:none;
    cursor: pointer;
   }
   .offerdiv h5{
   background-color: #ccc;
   padding: 10px;
   margin-bottom: 0;
   }
</style>
<style media="print">
   .height{
   height:48px;
   }
</style>
<div class="content-wrapper">
   <div class="row grid-margin mt-3 mb-2">
      <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
         <div class="card">
            <ul class="nav nav-tabs">
               <li class="active"><a data-toggle="tab" href="#sanctionSupplyChain">SupplyChain</a></li>
               <li><a data-toggle="tab" href="#SanctionLeasing">Leasing</a></li>
            </ul>
            <div class="card-body">
               <div class="tab-content">
                  <div id="sanctionSupplyChain" class="tab-pane fadein active">
                    @if( is_array($supplyChaindata['offerData'])?count($supplyChaindata['offerData']):$supplyChaindata['offerData']->count())
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
                                 <select class="select">
                                    <option>Choose an Item</option>
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
                     @if($supplyChaindata['prgm_type'] == 2) 
                     <table  class="table table-border"  cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr>
                              <td width="33.33%">Facility No</td>
                              <td width="6.66%">{{$supplyChaindata['prgm_type']}}</td>
                              <td width="30%">Facility Name</td>
                              <td width="30%">Purchase Finance Facility  /  Channel Financing{{--$supplyChaindata['product_name']--}}</td>
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
                              <td width="66.66%" colspan="3">
                                 Limit will be valid for 1 year from date of 
                                 <select class="select">
                                    <option>Choose an Item</option>
                                    <option>date of sanction letter</option>
                                    <option>date of first disbusrement</option>
                                 </select>
                                 (Date will be selected from sanction letter itself) Documents required for renewal of facility to be submitted to Capsave Finance Pvt Limited at least 40 days prior to limit expiry.
                              </td>
                           </tr>
                           <tr>
                              <td width="33.33%">Investment Payment Frequency</td>
                              <td width="66.66%" colspan="3">..</td>
                           </tr>
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
                                       @foreach($supplyChaindata['offerData'] as $offerD)
                                       <tr>
                                          <td><input type="text" class="input_sanc" name="debtor_name[]" placeholder="Click here to enter text"></td>
                                          <td></td>
                                       </tr>
                                       @endforeach
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
                                       @foreach($supplyChaindata['offerData'] as $offerD)
                                       <tr>
                                          <td>
                                             <select class="select">
                                                <option>Choose an Item</option>
                                                <option>30 Days</option>
                                                <option>45 Days</option>
                                                <option>60 Days</option>
                                                <option>90 Days</option>
                                             </select>
                                          </td>
                                          <td>
                                             <select class="select">
                                                <option>Choose an Item</option>
                                                <option>7 Days</option>
                                                <option>15 Days</option>
                                                <option>30 Days</option>
                                                <option>45 Days</option>
                                             </select>
                                          </td>
                                          <td>
                                             <select class="select">
                                                <option>Choose an Item</option>
                                                <option>Invoice Date</option>
                                                <option>BOE Date</option>
                                                <option>GRN Date</option>
                                                <option>Date of Discounting</option>
                                             </select>
                                          </td>
                                          <td><input type="text" class="input_sanc" name="debtor_name[]" placeholder="Click here to enter text"></td>
                                       </tr>
                                       @endforeach
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                           <tr>
                              <td width="33.33%">Methodology for calculating for Drawing Power</td>
                              <td width="66.66%" colspan="3">As mentioned in Margin Section</td>
                           </tr>
                           <tr>
                              <td width="33.33%">Specific Condition</td>
                              <td width="66.66%" colspan="3">
                                 <ul style="padding:0px 0px 0px 15px; margin:0px; line-height:23px;list-style-type:unset;">
                                    <li>
                                       Invoices should not be older than 30 days from 
                                       <select class="select">
                                          <option>Choose an Item</option>
                                          <option>Invoice Date</option>
                                          <option>BOE Date</option>
                                          <option>GRN Date</option>
                                          <option>Date of Discounting</option>
                                       </select>
                                       On the date of Discounting.
                                    </li>
                                    <li>Discounting proceed to be credited to working capital account of the borrowers.</li>
                                 </ul>
                              </td>
                           </tr>
                           <tr>
                              <td width="33.33%">Specific Pre-disbursement Condition</td>
                              <td width="66.66%" colspan="3">
                              </td>
                           </tr>
                           <tr>
                              <td width="33.33%"> Specific Post-disbursement Condition</td>
                              <td width="66.66%" colspan="3">
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     @endif
                     <!-- Channle Program -->
                     @if($supplyChaindata['prgm_type'] == 1) 
                     <table  class="table table-border"  cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr>
                              <td width="33.33%">Facility No</td>
                              <td width="6.66%">{{$supplyChaindata['prgm_type']}}</td>
                              <td width="30%">Facility Name</td>
                              <td width="30%">Vendor Finance Facility</td>
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
                              <td width="66.66%" colspan="3">
                                 Limit will be valid for 1 year from date of 
                                 <select class="select">
                                    <option>Choose an Item</option>
                                    <option>date of sanction letter</option>
                                    <option>date of first disbusrement</option>
                                 </select>
                                 (Date will be selected from sanction letter itself) Documents required for renewal of facility to be submitted to Capsave Finance Pvt Limited at least 40 days prior to limit expiry.
                              </td>
                           </tr>
                           <tr>
                              <td width="33.33%">Investment Payment Frequency</td>
                              <td width="66.66%" colspan="3">..</td>
                           </tr>
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
                                       @foreach($supplyChaindata['offerData'] as $offerD)
                                       <tr>
                                          <td><input type="text" class="input_sanc" name="debtor_name[]" placeholder="Click here to enter text"></td>
                                          <td></td>
                                       </tr>
                                       @endforeach
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
                                       @foreach($supplyChaindata['offerData'] as $offerD)
                                       <tr>
                                          <td>
                                             <select class="select">
                                                <option>Choose an Item</option>
                                                <option>30 Days</option>
                                                <option>45 Days</option>
                                                <option>60 Days</option>
                                                <option>90 Days</option>
                                             </select>
                                          </td>
                                          <td>
                                             <select class="select">
                                                <option>Choose an Item</option>
                                                <option>7 Days</option>
                                                <option>15 Days</option>
                                                <option>30 Days</option>
                                                <option>45 Days</option>
                                             </select>
                                          </td>
                                          <td>
                                             <select class="select">
                                                <option>Choose an Item</option>
                                                <option>Invoice Date</option>
                                                <option>BOE Date</option>
                                                <option>GRN Date</option>
                                                <option>Date of Discounting</option>
                                             </select>
                                          </td>
                                          <td><input type="text" class="input_sanc" name="debtor_name[]" placeholder="Click here to enter text"></td>
                                       </tr>
                                       @endforeach
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                           <tr>
                              <td width="33.33%">Methodology for calculating for Drawing Power</td>
                              <td width="66.66%" colspan="3">As mentioned in Margin Section</td>
                           </tr>
                           <tr>
                              <td width="33.33%">Specific Condition</td>
                              <td width="66.66%" colspan="3">
                                 <ul style="padding:0px 0px 0px 15px; margin:0px; line-height:23px;">
                                    <li>Invoices should not be older than 30 days from .On the date of Discounting.</li>
                                    <li>Discounting proceed to be credited to working capital account of the borrowers.</li>
                                 </ul>
                              </td>
                           </tr>
                           <tr>
                              <td width="33.33%">Specific Pre-disbursement Condition</td>
                              <td width="66.66%" colspan="3">
                              </td>
                           </tr>
                           <tr>
                              <td width="33.33%"> Specific Post-disbursement Condition</td>
                              <td width="66.66%" colspan="3">
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     @endif
                     <table  class="table table-border"  cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr>
                              <td width="33.33%">Penal Interest</td>
                              <td width="66.66%">
                                 <select class="select">
                                    <option>Choose an Item</option>
                                    <option>2 %</option>
                                    <option>3 %</option>
                                    <option>4 %</option>
                                    <option>5 %</option>
                                    <option>18 %</option>
                                 </select>
                                 <select class="select">
                                    <option>Choose an Item</option>
                                    <option>over and above the rate for the last draw down  or Rollover of facility on</option>
                                    <option>On</option>
                                 </select>
                                 entire principal / payable interest on delay in repayment of principal / Interest / charges 
                                 <select class="select">
                                    <option>Choose an Item</option>
                                    <option>Applicable</option>
                                    <option>Not applicable</option>
                                 </select>
                                 .<br/>
                                 The rate of interest will be <input type="text" class="input_sanc" name="debtor_name[]" placeholder="Type Here">% higher than the rate stipulated under each of the facilities till the security is created
                                 <select class="select">
                                    <option>Choose an Item</option>
                                    <option>Applicable</option>
                                    <option>Not applicable</option>
                                 </select>
                                 .
                                 <br/>
                                 If security is not created within the stipulated timeframe then a penal interest of 
                                 <select class="select">
                                    <option>Choose an Item</option>
                                    <option>2 %</option>
                                    <option>3 %</option>
                                    <option>4 %</option>
                                    <option>5 %</option>
                                    <option>18 %</option>
                                 </select>
                                 p.a.  
                                 <select class="select">
                                    <option>Choose an Item</option>
                                    <option>over and above the rate for the last draw down  or Rollover of facility on</option>
                                    <option>On</option>
                                 </select>
                                 entire principle
                                 <select class="select">
                                    <option>Choose an Item</option>
                                    <option>Applicable</option>
                                    <option>Not applicable</option>
                                 </select>
                                 .
                              </td>
                           </tr>
                           <tr>
                              <td width="33.33%">Prepayment</td>
                              <td width="66.66%">
                                 In case borrower desires to prepay the loan, the prepayment of loan will be accepted on the terms and conditions to be decided
                                 by CFPL for time to time.
                              </td>
                           </tr>
                           <tr>
                              <td width="33.33%">Payment Mechanism of Interest</td>
                              <td width="66.66%">
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
                              <td width="33.33%">Payment Mechanism of Pricipal</td>
                              <td width="66.66%">
                                 <select class="select">
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
                     <h5>Section 3:Specific Security :- Choose an item</h5>
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
                              <td><input type="text" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc" placeholder="Click here to enter text"></td>
                              <td><input type="text" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc" placeholder="Click here to enter text"></td>
                           </tr>
                           <tr>
                              <td >Interest</td>
                              <td><input type="text" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc" placeholder="Click here to enter text"></td>
                              <td><input type="text" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc" placeholder="Click here to enter text"></td>
                           </tr>
                           <tr>
                              <td>Repayment</td>
                              <td><input type="text" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc" placeholder="Click here to enter text"></td>
                              <td><input type="text" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc" placeholder="Click here to enter text"></td>
                           </tr>
                           <tr>
                              <td>Other</td>
                              <td><input type="text" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc" placeholder="Click here to enter text"></td>
                              <td><input type="text" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc" placeholder="Click here to enter text"></td>
                           </tr>
                           <tr>
                              <td>security</td>
                              <td><input type="text" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc" placeholder="Click here to enter text"></td>
                              <td><input type="text" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc" placeholder="Click here to enter text"></td>
                           </tr>
                        </tbody>
                     </table>
                     <br />
                     <h5>NACH Mandate with undertaking</h5>
                     <table  class="table table-border"  cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr>
                              <td width="33.33%">Facility No</td>
                              <td width="6.66%"><input type="text" name="pdc_facility_no" id="pdc_facility_no" class="input_sanc"></td>
                              <td width="30%">Facility Name</td>
                              <td width="30%"><input type="text" name="pdc_facility_name" id="pdc_facility_name" class="input_sanc"></td>
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
                     <h5>DSRA</h5>
                     <table  class="table table-border"  cellpadding="0" cellspacing="0">
                        <thead>
                           <tr>
                              <th>Amount</th>
                              <th>Tenure</th>
                              <th>Comment if any</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td><input type="text" name="pdc_facility_amt" id="pdc_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                              <td><input type="text" name="pdc_facility_amt" id="pdc_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                              <td><input type="text" name="pdc_facility_amt" id="pdc_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                           </tr>
                        </tbody>
                     </table>
                     <br />
                     <h5>Any other security:Choose an item</h5>
                     <table  class="table table-border"  cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr>
                              <td><input type="text" name="pdc_facility_amt" id="pdc_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                           </tr>
                        </tbody>
                     </table>
                     <br />
                     <h5>Section 5:- Financial Covenants.</h5>
                     <table  class="table table-border"  cellpadding="0" cellspacing="0">
                        <thead>
                           <tr>
                              <th width="75%">Covenants < Multiple option don't limit to 3 ></th>
                              <th width="25%">Minimum/Maximum ratio</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td><input type="text" name="pdc_facility_amt" id="pdc_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                              <td><input type="text" name="pdc_facility_amt" id="pdc_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                           </tr>
                           <tr>
                              <td><input type="text" name="pdc_facility_amt" id="pdc_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                              <td><input type="text" name="pdc_facility_amt" id="pdc_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                           </tr>
                           <tr>
                              <td><input type="text" name="pdc_facility_amt" id="pdc_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                              <td><input type="text" name="pdc_facility_amt" id="pdc_facility_amt" class="input_sanc" placeholder="Click here to enter text"></td>
                           </tr>
                        </tbody>
                     </table>
                     <p>The financial covenants shall be tested on a choose an item.basis and shall be reported in the monitoring report to be submitted by choose an item.</p>
                  @else 
                  <div class="card card-color mb-0">
                     <div class="card-header">
                        <a class="card-title ">Sanction letter cannot be generated for this application as limit offer has not added.</a>
                     </div>
                  </div>
                  @endif
                  </div>
                  <div id="SanctionLeasing" class="tab-pane fade">
                     @if( is_array($offerData)?count($offerData):$offerData->count())
                     <div class=" form-fields">
                        <div class="col-md-12">
                            <h5 class="card-title form-head-h5">Sanction Letter
                            @if($sanctionData)
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
                                                <td with="25%">{{ $biz_entity_name }}</td>
                                                
                                                <td with="25%"><b>Lessor</b></td>
                                                <td with="25%">
                                                <input type="text" name="lessor" value="{{ $lessor }}" class="form-control" />
                                                 </td>
                                            </tr>
                                            
                                            <tr>
                                                <td with="25%"><b>Sanction Validity</b></td>
                                                <td with="25%" colspan="3">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <input type="text" name="sanction_validity_date" value="{{old('sanction_validity_date', \Carbon\Carbon::parse($validity_date)->format('d/m/Y'))}}" class="form-control" tabindex="5" placeholder="Enter Validity Date" autocomplete="off" >
                                                        </div>
                                                        <div class="col">
                                                        <input type="text" class="form-control" placeholder="Enter Comment" name="sanction_validity_comment" value="{{ $validity_comment }}">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Sanction Amount</b></td>
                                                <td with="25%"> {{ count($leasingLimitData) > 0 ? 'INR '. number_format($leasingLimitData['0']['limit_amt']) : '' }}</td>
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

                                           <!--  <tr>
                                                <td with="25%"><b>Nature of Facility</b></td>
                                                <td with="25%">Rental Facility </td>
                                                <td with="25%"><b>Sanction Amount</b></td>
                                                <td with="25%"> {!! $offerData->prgm_limit_amt ? \Helpers::formatCurreny($offerData->prgm_limit_amt) : '' !!}</td>
                                            </tr> -->
                                            <!-- <tr>
                                                <td with="25%"><b>Equipment Type</b></td>
                                                <td with="25%" colspan="3"> @if($equipmentData) {{ $equipmentData->equipment_name }}@endif </td>
                                            </tr> -->
                                            <!-- <tr>
                                                <td with="25%"><b>Lease Tenor</b></td>
                                                <td with="25%">
                                                    @if($offerData->tenor)
                                                        {{ $offerData->tenor }}
                                                        @if($product_id == 1)
                                                            @if($offerData->tenor>1)Days @else Day @endif 
                                                        @else
                                                            @if($offerData->tenor>1)Months @else Month @endif 
                                                        @endif
                                                    @endif
                                                </td>
                                                <td with="25%"><b>Rental Rate – 
                                                @switch ($offerData->rental_frequency)
                                                    @case(4) PTPM  @break
                                                    @case(3) PTPQ  @break
                                                    @case(2) PTPBi-Y  @break
                                                    @case(1) PTPY  @break
                                                @endswitch </b></td>
                                                <td with="25%">
                                                    @if($ptpqrData)
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <td>From Period</td>
                                                                <td>To Period</td>
                                                                <td>Rate</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($ptpqrData as $ptpqr)
                                                            <tr>
                                                                <td>{{ $ptpqr->ptpq_from }}</td>
                                                                <td>{{ $ptpqr->ptpq_to }}</td>
                                                                <td>{{ $ptpqr->ptpq_rate }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Refundable Security Deposit</b></td>
                                                <td with="25%">
                                                    @if($offerData->security_deposit_type == 1 &&  $offerData->security_deposit >0)
                                                        Flat {{ $offerData->security_deposit }} of the {{ $security_deposit_of }}
                                                    @elseif($offerData->security_deposit_type == 2 &&  $offerData->security_deposit >0)
                                                        {{ $offerData->security_deposit }} % of the {{ $security_deposit_of }}
                                                    @endif
                                                </td>
                                                <td with="25%"><b>Processing Fees</b></td>
                                                <td with="25%">{!! $offerData->processing_fee ? $offerData->processing_fee . ' %' : '' !!}</td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Security</b></td>
                                                <td with="25%">
                                                    @switch($offerData->addl_security)
                                                        @case(1)
                                                            BG
                                                            @break
                                                        @case(2)
                                                            MF
                                                            @break
                                                        @case(3)
                                                            {{ $offerData->comment }}
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td with="25%"><b>Rental Payment Frequency</b></td>
                                                <td with="25%">
                                                    Rentals are due 
                                                    @switch ($offerData->rental_frequency) 
                                                        @case(4) Monthly  @break
                                                        @case(3) Quaterly  @break
                                                        @case(2) Bi-Yearly  @break
                                                        @case(1) Yearly  @break
                                                    @endswitch 
                                                    in 
                                                    @switch($offerData->rental_frequency_type)
                                                        @case(1) Advance @break
                                                        @case(2) Arrears @break
                                                    @endswitch
                                                </td>
                                            </tr> -->
                                            <tr>
                                                <td with="25%"><b>Payment Mechanism</b> </td>
                                                <td colspan="3">
                                                    <div class="row">
                                                        <div class="col">
                                                            <select class="form-control" id="payment_type" name="payment_type">
                                                                <option value="">Please Select...</option>
                                                                <option @if($payment_type == '1')selected @endif value="1">NACH</option>
                                                                <option @if($payment_type == '2')selected @endif value="2">RTGS</option>
                                                                <option @if($payment_type == '3')selected @endif value="3">NEFT</option>
                                                                <option @if($payment_type == '4')selected @endif value="4">Advance Cheque</option>
                                                                <option @if($payment_type == '5')selected @endif value="5">Other Specify</option>
                                                            </select>
                                                        </div>
                                                        <div class="col">
                                                        <input type="text" class="form-control @if($payment_type != '5') hide @endif" name="payment_type_comment" id="payment_type_comment" placeholder="Enter Payment Mechanism" value="{{ $payment_type_other }}">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Delayed payment charges</b></td>
                                                <td  colspan="3">
                                                    <textarea class="form-control textarea" name="delay_pymt_chrg" id="delay_pymt_chrg" cols="30" rows="10">@if($delay_pymt_chrg){!! $delay_pymt_chrg !!} @else Any delay in the payment of the rentals shall attract overdue interest on the rentals due but unpaid at the overdue rate mentioned in the Master Rental Agreement.@endif</textarea>
                                                </td>
                                            </tr>    
                                            <tr>
                                                <td><b>Insurance</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="insurance" id="insurance" cols="30" rows="10">@if($insurance) {!! $insurance !!} @else The equipment is required to be insured throughout the period of the rental for its full insurable value against physical loss and damage. Lessee may elect to: Insure the equipment through their own insurance company or request CFPL to insure the equipment. Should the lessee elect CFPL to insure the equipment, the lessor shall advise on the cost once full equipment details are known. Insurance policy of the assets under lease endorsed in favour of Capsave Finance Pvt Ltd within 30 days of disbursement of each tranche.@endif</textarea>
                                                </td>
                                            </tr>
                                            <tr>   
                                                <td><b>GST/Bank Charges</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="bank_chrg" id="bank_chrg" cols="30" rows="10">@if($bank_chrg) {!! $bank_chrg !!}@else Extra as applicable. It is not included in the above rental rates and would be for the account of the Lessee. Bank charges include LC and remittance charges.@endif</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Legal Costs</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="legal_cost" id="legal_cost" cols="30" rows="10">@if($legal_cost) {!! $legal_cost !!} @else Any legal costs will be for the account of the Lessee. @endif</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Purchase Orders</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="po" id="po" cols="30" rows="10">@if($po) {!! $po !!} @else Purchase orders shall not be raised by the Lessee on a vendor without our prior written approval and signed by an authorized person of CFPL. Any purchase order raised by CFPL shall be raised on the express understanding and agreement that it is being raised by CFPL as agent for you until such time as CFPL agrees to accept the rental order and or the invoice. CFPL shall not be responsible to pay any vendor any amount until such time that it agrees to do the aforementioned.@endif </textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Pre-disbursement conditions</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="pdp" id="pdp" cols="30" rows="10">@if($pdp) {!! $pdp !!} @else<b>One-time requirement:</b><br><ol><li>&nbsp;Accepted Sanction Letter<br></li><li>Signed MRA&nbsp;</li><li>Self-Attested KYC of client and Vendors (True Copy)&nbsp;</li><ul><li>&nbsp;Certificate of incorporation, MOA, AOA&nbsp;</li><li>&nbsp;Address Proof (Not older than 90 days)&nbsp;</li><li>&nbsp;PAN Card&nbsp;</li><li>&nbsp;GST registration letter&nbsp;</li></ul><li>Board Resolution signed by 2 directors or Company Secretary. or Power of Attorney in favour of company officials to execute such agreements or documents. BR should be dated before the MRA date.&nbsp;</li><li>Bank Guarantee or Lien on Fixed deposit of 70% of the invoice value.&nbsp;</li><li>Unconditional and irrevocable bank guarantee should be in CFPL approved format.&nbsp;</li><li>Bank guarantee should be valid for at least a quarter more than the expiry of the lease tenure.&nbsp;</li><li>KYC of authorized signatory:&nbsp;</li><ul><li>&nbsp;Name of authorized signatories with their Self Attested ID proof and address proof&nbsp;</li><li>&nbsp;Signature Verification of authorized signatories from company's banker&nbsp;</li></ul><li>CIN (company identification number)</li></ol>@endif</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Disbursement Guidelines/Documentation</b></td>
                                                <td colspan="3"> 
                                                <textarea class="form-control textarea" name="disburs_guide" id="disburs_guide" cols="30" rows="10">@if($disburs_guide){!! $disburs_guide !!} @else <b>With every tranche:</b><br><ol><li>Original Invoices, Delivery challans, lorry receipt, installation report&nbsp;</li><li>In case of import transactions, Packing list, Bill of Entry for home consumption in the joint name, Airway bill/Bill of Lading, Rewarehousing Certificate, PE certificate, TRC copy&nbsp;</li><li>Signed Rental Schedule&nbsp;</li><li>NACH form&nbsp;</li><li>All documents as mentioned in Annexure-1 </li></ol> @endif</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Other Conditions</b></td>
                                                <td colspan="3"> 
                                                    <textarea class="form-control textarea" name="other_cond" id="other_cond" cols="30" rows="10">@if($other_cond){!! $other_cond !!}@else<ul><li>Rentals will be calculated on total invoice value.</li><li>First rental shall commence from date of invoice or date of payment to the vendor whichever is earlier.&nbsp;</li><li>Any financial or operational covenants stipulated by CFPL from time to time.&nbsp;</li><li>All the other conditions stipulated in MRA/Rental Schedule will remain applicable at all times </li></ul>@endif</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Information and other covenants</b></td>
                                                <td colspan="3"> 
                                                    <textarea class="form-control textarea" name="covenants" id="covenants" cols="30" rows="10">@if($covenants) {!! $covenants !!}@else <ul><li>The Lessee hereby agrees &amp; gives consent for the disclosures by the Lender/Lessor of all or any such:&nbsp;</li><ol><li>&#65279;Information &amp; data relating to the lessee.&nbsp;</li><li>Information and data relating to any credit or lease facility availed/to be availed by the lessee and data relating to their obligations as lessee/guarantor.&nbsp;</li><li>Obligations assumed/to be assumed by the lessee in relation to the lease facility(ies).&nbsp;</li><li>In compliance with the regulatory requirements, CFPL shall disclose and furnish details&nbsp;of the transaction including defaults, if any, committed by Lessee in discharge of their obligations hereunder or under any Transaction Documents, to Credit Information Bureau Limited (“CIBIL”) or any other agency as authorized by Reserve Bank of India (“RBI”).&nbsp;</li></ol><li>The lessee declares that the information and data furnished by the lessee to the lender/lessor are true and correct.&nbsp;</li></ul>The Lessee undertakes that CIBIL or any other agency so authorized may use/process the said information and data disclosed by the lessee in the manner as may be deemed fit by them. CIBIL or any other agency so authorized may furnish for consideration the processed information, data, and products thereof prepared by them to banks, financial institutions, or credit granters or registered users as may be specified by RBI in this behalf.@endif</textarea>
                                                </td>
                                            </tr>                 
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="sanction_id" value="{{$sanction_id}}">
                                    <input type="hidden" name="app_id" value="{{$appId}}">
                                    <input type="hidden" name="offer_id" value="{{$offerId}}">
                                    <input type="hidden" name="biz_id" value="{{$bizId}}">
                                    <button type="submit" class="btn  btn-success btn-sm float-right">Submit</button>  
                                </form>
                            </div>
                        </div>
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
       startDate: '+1m'
     });
   });
</script>
@endsection