@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('update_limit_offer')}}" target="_top" onsubmit="return checkSupplyValidations()">
    @csrf
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <input type="hidden" value="{{request()->get('app_prgm_limit_id')}}" name="app_prgm_limit_id">
    <input type="hidden" value="{{request()->get('prgm_offer_id')}}" name="offer_id" id="offer_id">
    
    <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Product</b></label> 
        <input type="text" class="form-control" value="Supply Chain" placeholder="Facility Type" maxlength="15" disabled>
      </div>
    </div>

    @php
    $currentOfferAmount = $offerData->prgm_limit_amt ?? 0;
    $limitBalance = (int)$limitData->limit_amt - (int)$subTotalAmount + (int)$currentOfferAmount;
    @endphp

    <div class="col-md-6">
      <div class="form-group INR">
        <label for="txtPassword" ><b>Proposed Product Limit</b></label> 
        <a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" class="form-control number_format" value="{{isset($limitData->limit_amt)? number_format($limitData->limit_amt): ''}}" placeholder="Limit" maxlength="15" readonly>
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Select Anchor</b></label> 
            <select name="anchor_id" id="anchor_id" class="form-control">
                <option value="">Select Anchor</option>
                @foreach($anchors as $key=>$anchor)
                <option value="{{$anchor->anchor_id}}" {{(isset($offerData->anchor_id) && $anchor->anchor_id == $offerData->anchor_id)? 'selected': ''}}>{{$anchor->comp_name}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Select Program</b></label> 
            <select name="prgm_id" id="program_id" class="form-control">
            </select>
        </div>
    </div>

    <div class="col-md-6">
      <div class="form-group INR">
        <label for="txtPassword"><b>Program Limit</b></label>
        <span class="text-success limit"></span>
        <span class="float-right text-success">Balance: <i class="fa fa-inr"></i>{{($limitBalance<0)? 0: $limitBalance}}</span>
        <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr"></i></a>
        <input type="text" name="prgm_limit_amt" class="form-control number_format" value="{{isset($offerData->prgm_limit_amt)? number_format($offerData->prgm_limit_amt): ''}}" placeholder="Program Limit" maxlength="15">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Interest Rate (%)</b></label>
        <span class="float-right text-success limit"></span>
        <input type="text" name="interest_rate" class="form-control" value="{{isset($offerData->interest_rate)? $offerData->interest_rate: ''}}" placeholder="Interest Rate" maxlength="5">
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Select Payment Frequency</b></label> 
        <select name="payment_frequency" class="form-control">
            <option value="">Select Payment Frequency</option>
            <option value="1" {{(isset($offerData->payment_frequency) && $offerData->payment_frequency == 1)? 'selected': ''}}>Up Front</option>
            <option value="2" {{(isset($offerData->payment_frequency) && $offerData->payment_frequency == 2)? 'selected': ''}}>Monthly</option>
            <option value="3" {{(isset($offerData->payment_frequency) && $offerData->payment_frequency == 3)? 'selected': ''}}>Rear Ended</option>
        </select>
      </div>
    </div>
        
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Benchmark Date</b></label> 
        <select name="benchmark_date" class="form-control">
            <option value="">Select Benchmark Date</option>
            <option value="1" {{(isset($offerData->benchmark_date) && $offerData->benchmark_date == 1)? 'selected': ''}}>Invoice Date</option>
            <option value="2" {{(isset($offerData->benchmark_date) && $offerData->benchmark_date == 2)? 'selected': ''}}>Date of discounting</option>
        </select>
      </div>
    </div>

<div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Invoice Tenor (In Days)</b></label> 
        <input type="text" name="tenor" class="form-control" value="{{isset($offerData->tenor)? $offerData->tenor: ''}}" placeholder="Invoice Tenor (In Days)" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Old Invoice Tenor (In Days)</b></label> 
        <input type="text" name="tenor_old_invoice" class="form-control" value="{{isset($offerData->tenor_old_invoice)? $offerData->tenor_old_invoice: ''}}" placeholder="Old Invoice Tenor (In Days)" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Margin (%)</b></label> 
        <input type="text" name="margin" class="form-control" value="{{isset($offerData->margin)? $offerData->margin: ''}}" placeholder="Margin" maxlength="5">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Overdue Interest Rate (%)</b></label> 
        <input type="text" name="overdue_interest_rate" class="form-control" value="{{isset($offerData->overdue_interest_rate)? $offerData->overdue_interest_rate: ''}}" placeholder="Overdue Interest Rate" maxlength="5">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Adhoc Interest Rate (%)</b></label> 
        <input type="text" name="adhoc_interest_rate" class="form-control" value="{{isset($offerData->adhoc_interest_rate)? $offerData->adhoc_interest_rate: ''}}" placeholder="Adhoc Interest Rate" maxlength="5">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Grace Period (In Days)</b></label> 
        <input type="text" name="grace_period" class="form-control" value="{{isset($offerData->grace_period)? $offerData->grace_period: ''}}" placeholder="Grace Period" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
      </div>
    </div>
    <div class="charges_block" style="width: 100%;display: contents;">
        @if(isset($offerData->offerCharges))
        @foreach($offerData->offerCharges as $key=>$offerCharge)
        <div class="col-md-6">
          <div class="form-group">
              <label for="txtPassword"><b>{{$offerCharge->chargeName->chrg_name.(($offerCharge->chrg_type == 2)? ' (%)': '')}}</b></label>
                <input type="text" name="charge_names[{{$offerCharge->charge_id.'#'.$offerCharge->chrg_type}}]" class="form-control" data-type="{{$offerCharge->chrg_type}}" data-name="{{$offerCharge->chargeName->chrg_name}}" value="{{$offerCharge->chrg_value}}" maxlength="6">
          </div>
        </div>
        @endforeach
        @endif
    </div>
    
    {{--<div class="col-md-6">
      <div class="form-group">
          <label for="txtPassword"><b>Processing Fee <span id="processing_fee_type">(%)</span></b></label>
            <input type="text" name="processing_fee" class="form-control" value="{{isset($offerData->processing_fee)? $offerData->processing_fee: ''}}" placeholder="Processing Fee" maxlength="6">
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="form-group INR">
        <label for="txtPassword"><b>Documentation Fee <span id="document_fee_type">(%)</span></b></label>         
        <input type="text" name="document_fee" class="form-control" value="{{isset($offerData->document_fee)? $offerData->document_fee : ''}}" placeholder="Documentation Fee" maxlength="6">
      </div>
    </div>--}}
    
    <!-- -------------- PRIMARY SECURITY BLOCK ------------ -->
    <div class="col-md-12">
          <div class="form-group row">
            <label for="txtPassword" class="col-md-12" style="background-color: #F2F2F2;padding: 5px 0px 5px 20px;"><b>Primary Security</b></label>
            <div class="col-md-6">
                <select name="primary_security" class="form-control show-hide" data-block_id="#primary-security-block">
                    <option value="">Select Primary Security</option>
                    <option value="1" {{(isset($offerData->offerPs) && count($offerData->offerPs) > 0)? 'selected': ''}}>Applicable</option>
                    <option value="2" {{(isset($offerData->offerPs) && count($offerData->offerPs) == 0)? 'selected': ''}}>Not Applicable</option>
                </select>
            </div>
            <div class="col-md-12" id="primary-security-block" style="display: {{(isset($offerData->offerPs) && count($offerData->offerPs) > 0)? 'block': 'none'}};">
            @if(isset($offerData->offerPs) && count($offerData->offerPs) > 0)
                @foreach($offerData->offerPs as $key=>$ps)
                <div class="row mt10">
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Security</b></label>
                        @endif
                        <select name="ps[ps_security_id][]" class="form-control">
                            <option value="">Select Security</option>
                            <option value="1" {{($ps->ps_security_id == 1)? 'selected': ''}}>Current assets</option>
                            <option value="2" {{($ps->ps_security_id == 2)? 'selected': ''}}>Plant and Machinery</option>
                            <option value="3" {{($ps->ps_security_id == 3)? 'selected': ''}}>Land & Building</option>
                            <option value="4" {{($ps->ps_security_id == 4)? 'selected': ''}}>Commercial Property</option>
                            <option value="5" {{($ps->ps_security_id == 5)? 'selected': ''}}>Land</option>
                            <option value="6" {{($ps->ps_security_id == 6)? 'selected': ''}}>Industrial Premises</option>
                            <option value="7" {{($ps->ps_security_id == 7)? 'selected': ''}}>Residential Property</option>
                            <option value="8" {{($ps->ps_security_id == 8)? 'selected': ''}}>Farm House & Land</option>
                            <option value="9" {{($ps->ps_security_id == 9)? 'selected': ''}}>Listed Share</option>
                            <option value="10" {{($ps->ps_security_id == 10)? 'selected': ''}}>Unlisted Share</option>
                            <option value="11" {{($ps->ps_security_id == 11)? 'selected': ''}}>Mutual Funds</option>
                            <option value="12" {{($ps->ps_security_id == 12)? 'selected': ''}}>Intercorporate Deposits</option>
                            <option value="13" {{($ps->ps_security_id == 13)? 'selected': ''}}>Bank Guarantee</option>
                            <option value="14" {{($ps->ps_security_id == 14)? 'selected': ''}}>SBLC</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Type of Security</b></label>
                        @endif
                        <select name="ps[ps_type_of_security_id][]" class="form-control">
                            <option value="">Select type of Security</option>
                            <option value="1" {{($ps->ps_type_of_security_id == 1)? 'selected': ''}}>Registered Mortgage</option>
                            <option value="2" {{($ps->ps_type_of_security_id == 2)? 'selected': ''}}>Equitable Mortgage</option>
                            <option value="3" {{($ps->ps_type_of_security_id == 3)? 'selected': ''}}>Hypothecation</option>
                            <option value="4" {{($ps->ps_type_of_security_id == 4)? 'selected': ''}}>Pledge</option>
                            <option value="5" {{($ps->ps_type_of_security_id == 5)? 'selected': ''}}>Lien</option>
                            <option value="6" {{($ps->ps_type_of_security_id == 6)? 'selected': ''}}>Negative Lien</option>
                            <option value="7" {{($ps->ps_type_of_security_id == 7)? 'selected': ''}}>Deposit of Title deeds</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword"><b>Status of Security</b></label>
                        @endif
                        <select name="ps[ps_status_of_security_id][]" class="form-control">
                            <option value="">Select status of Security</option>
                            <option value="1" {{($ps->ps_status_of_security_id == 1)? 'selected': ''}}>First Pari-pasu</option>
                            <option value="2" {{($ps->ps_status_of_security_id == 2)? 'selected': ''}}>Exclusive</option>
                            <option value="3" {{($ps->ps_status_of_security_id == 3)? 'selected': ''}}>Third Pari-pasu</option>
                            <option value="4" {{($ps->ps_status_of_security_id == 4)? 'selected': ''}}>Second Pari-pasu</option>
                            <option value="5" {{($ps->ps_status_of_security_id == 5)? 'selected': ''}}>Sub-Servient</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Time for security</b></label>
                        @endif
                        <select name="ps[ps_time_for_perfecting_security_id][]" class="form-control">
                            <option value="">Select time for perfecting security</option>
                            <option value="1" {{($ps->ps_time_for_perfecting_security_id == 1)? 'selected': ''}}>Before Disbusrement</option>
                            <option value="2" {{($ps->ps_time_for_perfecting_security_id == 2)? 'selected': ''}}>With in 30 days from date of first disbusrement</option>
                            <option value="3" {{($ps->ps_time_for_perfecting_security_id == 3)? 'selected': ''}}>With in 60 days from date of first disbsurement</option>
                            <option value="4" {{($ps->ps_time_for_perfecting_security_id == 4)? 'selected': ''}}>With in 90 days from date of first disbursement </option>
                            <option value="5" {{($ps->ps_time_for_perfecting_security_id == 5)? 'selected': ''}}>With in 120 days from date of first disbursement</option>
                            <option value="6" {{($ps->ps_time_for_perfecting_security_id == 6)? 'selected': ''}}>with in 180 days from date of first disbursement</option>
                            <option value="7" {{($ps->ps_time_for_perfecting_security_id == 7)? 'selected': ''}}>with in 360 days from date of first disbsurement</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Desc of Security</b></label>
                        @endif
                        <input name="ps[ps_desc_of_security][]" class="form-control" value="{{$ps->ps_desc_of_security}}">
                    </div>
                    <div class="col-md-2 center">
                        @if($loop->first)
                        <i class="fa fa-2x fa-plus-circle add-primary-security-block mt-8"></i>
                        @else
                        <i class="fa fa-2x fa-times-circle remove-primary-security-block" style="color: red;"></i>
                        @endif
                    </div>
                </div>
                @endforeach
                @else
                <div class="row mt10">
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Security</b></label>
                        <select name="ps[ps_security_id][]" class="form-control">
                            <option value="">Select Security</option>
                            <option value="1">Current assets</option>
                            <option value="2">Plant and Machinery</option>
                            <option value="3">Land & Building</option>
                            <option value="4">Commercial Property</option>
                            <option value="5">Land</option>
                            <option value="6">Industrial Premises</option>
                            <option value="7">Residential Property</option>
                            <option value="8">Farm House & Land</option>
                            <option value="9">Listed Share</option>
                            <option value="10">Unlisted Share</option>
                            <option value="11">Mutual Funds</option>
                            <option value="12">Intercorporate Deposits</option>
                            <option value="13">Bank Guarantee</option>
                            <option value="14">SBLC</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Type of Security</b></label>
                        <select name="ps[ps_type_of_security_id][]" class="form-control">
                            <option value="">Select type of Security</option>
                            <option value="1">Registered Mortgage</option>
                            <option value="2">Equitable Mortgage</option>
                            <option value="3">Hypothecation</option>
                            <option value="4">Pledge</option>
                            <option value="5">Lien</option>
                            <option value="6">Negative Lien</option>
                            <option value="7">Deposit of Title deeds</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword"><b>Status of Security</b></label>
                        <select name="ps[ps_status_of_security_id][]" class="form-control">
                            <option value="">Select status of Security</option>
                            <option value="1">First Pari-pasu</option>
                            <option value="2">Exclusive</option>
                            <option value="3">Third Pari-pasu</option>
                            <option value="4">Second Pari-pasu</option>
                            <option value="5">Sub-Servient</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Time for security</b></label>
                        <select name="ps[ps_time_for_perfecting_security_id][]" class="form-control">
                            <option value="">Select time for perfecting security</option>
                            <option value="1">Before Disbusrement</option>
                            <option value="2">With in 30 days from date of first disbusrement</option>
                            <option value="3">With in 60 days from date of first disbsurement</option>
                            <option value="4">With in 90 days from date of first disbursement </option>
                            <option value="5">With in 120 days from date of first disbursement</option>
                            <option value="6">with in 180 days from date of first disbursement</option>
                            <option value="7">with in 360 days from date of first disbsurement</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Description of Security</b></label>
                        <input name="ps[ps_desc_of_security][]" class="form-control" value="">
                    </div>
                    <div class="col-md-2 center">
                        <i class="fa fa-2x fa-plus-circle add-primary-security-block mt-8"></i>
                    </div>
                </div>
                @endif
            </div>
          </div>
        </div>
    <!-- -------------- -->

    <!-- -------------- COLLATERAL SECURITY BLOCK ------------ -->
    <div class="col-md-12">
          <div class="form-group row">
            <label for="txtPassword" class="col-md-12" style="background-color: #F2F2F2;padding: 5px 0px 5px 20px;"><b>Collateral Security</b></label>
            <div class="col-md-6">
                <select name="collateral_security" class="form-control show-hide" data-block_id="#collateral-security-block">
                    <option value="">Select Collateral Security</option>
                    <option value="1" {{(isset($offerData->offerCs) && count($offerData->offerCs) > 0)? 'selected': ''}}>Applicable</option>
                    <option value="2" {{(isset($offerData->offerCs) && count($offerData->offerCs) == 0)? 'selected': ''}}>Not Applicable</option>
                </select>
            </div>
            <div class="col-md-12" id="collateral-security-block" style="display: {{(isset($offerData->offerCs) && count($offerData->offerCs) > 0)? 'block': 'none'}};">
            @if(isset($offerData->offerCs) && count($offerData->offerCs) > 0)
                @foreach($offerData->offerCs as $key=>$cs)
                <div class="row mt10">
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Description Security</b></label>
                        @endif
                        <select name="cs[cs_desc_security_id][]" class="form-control">
                            <option value="">Select Security</option>
                            <option value="1" {{($cs->cs_desc_security_id == 1)? 'selected': ''}}>Current assets</option>
                            <option value="2" {{($cs->cs_desc_security_id == 2)? 'selected': ''}}>Plant and Machinery</option>
                            <option value="3" {{($cs->cs_desc_security_id == 3)? 'selected': ''}}>Land & Building</option>
                            <option value="4" {{($cs->cs_desc_security_id == 4)? 'selected': ''}}>Commercial Property</option>
                            <option value="5" {{($cs->cs_desc_security_id == 5)? 'selected': ''}}>Land</option>
                            <option value="6" {{($cs->cs_desc_security_id == 6)? 'selected': ''}}>Industrial Premises</option>
                            <option value="7" {{($cs->cs_desc_security_id == 7)? 'selected': ''}}>Residential Property</option>
                            <option value="8" {{($cs->cs_desc_security_id == 8)? 'selected': ''}}>Farm House & Land</option>
                            <option value="9" {{($cs->cs_desc_security_id == 9)? 'selected': ''}}>Listed Share</option>
                            <option value="10" {{($cs->cs_desc_security_id == 10)? 'selected': ''}}>Unlisted Share</option>
                            <option value="11" {{($cs->cs_desc_security_id == 11)? 'selected': ''}}>Mutual Funds</option>
                            <option value="12" {{($cs->cs_desc_security_id == 12)? 'selected': ''}}>Intercorporate Deposits</option>
                            <option value="13" {{($cs->cs_desc_security_id == 13)? 'selected': ''}}>Bank Guarantee</option>
                            <option value="14" {{($cs->cs_desc_security_id == 14)? 'selected': ''}}>SBLC</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Type of Security</b></label>
                        @endif
                        <select name="cs[cs_type_of_security_id][]" class="form-control">
                            <option value="">Select type of Security</option>
                            <option value="1" {{($cs->cs_type_of_security_id == 1)? 'selected': ''}}>Registered Mortgage</option>
                            <option value="2" {{($cs->cs_type_of_security_id == 2)? 'selected': ''}}>Equitable Mortgage</option>
                            <option value="3" {{($cs->cs_type_of_security_id == 3)? 'selected': ''}}>Hypothecation</option>
                            <option value="4" {{($cs->cs_type_of_security_id == 4)? 'selected': ''}}>Pledge</option>
                            <option value="5" {{($cs->cs_type_of_security_id == 5)? 'selected': ''}}>Lien</option>
                            <option value="6" {{($cs->cs_type_of_security_id == 6)? 'selected': ''}}>Negative Lien</option>
                            <option value="7" {{($cs->cs_type_of_security_id == 7)? 'selected': ''}}>Deposit of Title deeds</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword"><b>Status of Security</b></label>
                        @endif
                        <select name="cs[cs_status_of_security_id][]" class="form-control">
                            <option value="">Select status of Security</option>
                            <option value="1" {{($cs->cs_status_of_security_id == 1)? 'selected': ''}}>First Pari-pasu</option>
                            <option value="2" {{($cs->cs_status_of_security_id == 2)? 'selected': ''}}>Exclusive</option>
                            <option value="3" {{($cs->cs_status_of_security_id == 3)? 'selected': ''}}>Third Pari-pasu</option>
                            <option value="4" {{($cs->cs_status_of_security_id == 4)? 'selected': ''}}>Second Pari-pasu</option>
                            <option value="5" {{($cs->cs_status_of_security_id == 5)? 'selected': ''}}>Sub-Servient</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Time for security</b></label>
                        @endif
                        <select name="cs[cs_time_for_perfecting_security_id][]" class="form-control">
                            <option value="">Select time for perfecting security</option>
                            <option value="1" {{($cs->cs_time_for_perfecting_security_id == 1)? 'selected': ''}}>Before Disbusrement</option>
                            <option value="2" {{($cs->cs_time_for_perfecting_security_id == 2)? 'selected': ''}}>With in 30 days from date of first disbusrement</option>
                            <option value="3" {{($cs->cs_time_for_perfecting_security_id == 3)? 'selected': ''}}>With in 60 days from date of first disbsurement</option>
                            <option value="4" {{($cs->cs_time_for_perfecting_security_id == 4)? 'selected': ''}}>With in 90 days from date of first disbursement </option>
                            <option value="5" {{($cs->cs_time_for_perfecting_security_id == 5)? 'selected': ''}}>With in 120 days from date of first disbursement</option>
                            <option value="6" {{($cs->cs_time_for_perfecting_security_id == 6)? 'selected': ''}}>with in 180 days from date of first disbursement</option>
                            <option value="7" {{($cs->cs_time_for_perfecting_security_id == 7)? 'selected': ''}}>with in 360 days from date of first disbsurement</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Description of Security</b></label>
                        @endif
                        <input name="cs[cs_desc_of_security][]" class="form-control" value="{{$cs->cs_desc_of_security}}">
                    </div>
                    <div class="col-md-2 center">
                        @if($loop->first)
                        <i class="fa fa-2x fa-plus-circle add-collateral-security-block mt-8"></i>
                        @else
                        <i class="fa fa-2x fa-times-circle remove-collateral-security-block" style="color: red;"></i>
                        @endif
                    </div>
                </div>
                @endforeach
                @else
                <div class="row mt10">
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Description Security</b></label>
                        <select name="cs[cs_desc_security_id][]" class="form-control">
                            <option value="">Select Security</option>
                            <option value="1">Current assets</option>
                            <option value="2">Plant and Machinery</option>
                            <option value="3">Land & Building</option>
                            <option value="4">Commercial Property</option>
                            <option value="5">Land</option>
                            <option value="6">Industrial Premises</option>
                            <option value="7">Residential Property</option>
                            <option value="8">Farm House & Land</option>
                            <option value="9">Listed Share</option>
                            <option value="10">Unlisted Share</option>
                            <option value="11">Mutual Funds</option>
                            <option value="12">Intercorporate Deposits</option>
                            <option value="13">Bank Guarantee</option>
                            <option value="14">SBLC</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Type of Security</b></label>
                        <select name="cs[cs_type_of_security_id][]" class="form-control">
                            <option value="">Select type of Security</option>
                            <option value="1">Registered Mortgage</option>
                            <option value="2">Equitable Mortgage</option>
                            <option value="3">Hypothecation</option>
                            <option value="4">Pledge</option>
                            <option value="5">Lien</option>
                            <option value="6">Negative Lien</option>
                            <option value="7">Deposit of Title deeds</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword"><b>Status of Security</b></label>
                        <select name="cs[cs_status_of_security_id][]" class="form-control">
                            <option value="">Select status of Security</option>
                            <option value="1">First Pari-pasu</option>
                            <option value="2">Exclusive</option>
                            <option value="3">Third Pari-pasu</option>
                            <option value="4">Second Pari-pasu</option>
                            <option value="5">Sub-Servient</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Time for security</b></label>
                        <select name="cs[cs_time_for_perfecting_security_id][]" class="form-control">
                            <option value="">Select time for perfecting security</option>
                            <option value="1">Before Disbusrement</option>
                            <option value="2">With in 30 days from date of first disbusrement</option>
                            <option value="3">With in 60 days from date of first disbsurement</option>
                            <option value="4">With in 90 days from date of first disbursement </option>
                            <option value="5">With in 120 days from date of first disbursement</option>
                            <option value="6">with in 180 days from date of first disbursement</option>
                            <option value="7">with in 360 days from date of first disbsurement</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Desc of Security</b></label>
                        <input name="cs[cs_desc_of_security][]" class="form-control" value="">
                    </div>
                    <div class="col-md-2 center">
                        <i class="fa fa-2x fa-plus-circle add-collateral-security-block mt-8"></i>
                    </div>
                </div>
                @endif
            </div>
          </div>
        </div>
    <!-- -------------- -->

    <!-- -------------- PERSONAL GUARANTEE BLOCK ------------ -->
    <div class="col-md-12">
          <div class="form-group row">
            <label for="txtPassword" class="col-md-12" style="background-color: #F2F2F2;padding: 5px 0px 5px 20px;"><b>Personal Guarantee</b></label>
            <div class="col-md-6">
                <select name="personal_guarantee" class="form-control show-hide" data-block_id="#personal-guarantee-block">
                    <option value="">Select Personal Guarantee</option>
                    <option value="1" {{(isset($offerData->offerPg) && count($offerData->offerPg) > 0)? 'selected': ''}}>Applicable</option>
                    <option value="2" {{(isset($offerData->offerPg) && count($offerData->offerPg) == 0)? 'selected': ''}}>Not Applicable</option>
                </select>
            </div>
            <div class="col-md-12" id="personal-guarantee-block" style="display: {{(isset($offerData->offerPg) && count($offerData->offerPg) > 0)? 'block': 'none'}};">
            @if(isset($offerData->offerPg) && count($offerData->offerPg) > 0)
                @foreach($offerData->offerPg as $key=>$pg)
                <div class="row mt10">
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Select Guarantor</b></label>
                        @endif
                        <select name="pg[pg_name_of_guarantor_id][]" class="form-control">
                            <option value="">Select Guarantor</option>
                            @foreach($bizOwners as $key=>$bizOwner)
                            <option value="{{$bizOwner->biz_owner_id}}" {{($pg->pg_name_of_guarantor_id == $bizOwner->biz_owner_id)? 'selected': ''}}>{{ucwords($bizOwner->first_name)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Time for security</b></label>
                        @endif
                        <select name="pg[pg_time_for_perfecting_security_id][]" class="form-control">
                            <option value="">Select time for perfecting security</option>
                            <option value="1" {{($pg->pg_time_for_perfecting_security_id == 1)? 'selected': ''}}>Before Disbusrement</option>
                            <option value="2" {{($pg->pg_time_for_perfecting_security_id == 2)? 'selected': ''}}>With in 30 days from date of first disbusrement</option>
                            <option value="3" {{($pg->pg_time_for_perfecting_security_id == 3)? 'selected': ''}}>With in 60 days from date of first disbsurement</option>
                            <option value="4" {{($pg->pg_time_for_perfecting_security_id == 4)? 'selected': ''}}>With in 90 days from date of first disbursement </option>
                            <option value="5" {{($pg->pg_time_for_perfecting_security_id == 5)? 'selected': ''}}>With in 120 days from date of first disbursement</option>
                            <option value="6" {{($pg->pg_time_for_perfecting_security_id == 6)? 'selected': ''}}>with in 180 days from date of first disbursement</option>
                            <option value="7" {{($pg->pg_time_for_perfecting_security_id == 7)? 'selected': ''}}>with in 360 days from date of first disbsurement</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword"><b>Residential Address </b></label>
                        @endif
                        <input name="pg[pg_residential_address][]" class="form-control" value="{{$pg->pg_residential_address}}">
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword"><b>Net worth as per ITR/CA Cert</b></label>
                        @endif
                        <input name="pg[pg_net_worth][]" class="form-control" value="{{$pg->pg_net_worth}}">
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Comments if any</b></label>
                        @endif
                        <input name="pg[pg_comments][]" class="form-control" value="{{$pg->pg_comments}}">
                    </div>
                    <div class="col-md-2 center">
                        @if($loop->first)
                        <i class="fa fa-2x fa-plus-circle add-personal-guarantee-block mt-8"></i>
                        @else
                        <i class="fa fa-2x fa-times-circle remove-personal-guarantee-block" style="color: red;"></i>
                        @endif
                    </div>
                </div>
                @endforeach
                @else
                <div class="row mt10">
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Select Guarantor</b></label>
                        <select name="pg[pg_name_of_guarantor_id][]" class="form-control">
                            <option value="">Select Guarantor</option>
                            @foreach($bizOwners as $key=>$bizOwner)
                            <option value="{{$bizOwner->biz_owner_id}}">{{ucwords($bizOwner->first_name)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Time for security</b></label>
                        <select name="pg[pg_time_for_perfecting_security_id][]" class="form-control">
                            <option value="">Select time for perfecting security</option>
                            <option value="1">Before Disbusrement</option>
                            <option value="2">With in 30 days from date of first disbusrement</option>
                            <option value="3">With in 60 days from date of first disbsurement</option>
                            <option value="4">With in 90 days from date of first disbursement </option>
                            <option value="5">With in 120 days from date of first disbursement</option>
                            <option value="6">with in 180 days from date of first disbursement</option>
                            <option value="7">with in 360 days from date of first disbsurement</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword"><b>Residential Address </b></label>
                        <input name="pg[pg_residential_address][]" class="form-control" value="">
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword"><b>Net worth as ITR/CA Cert</b></label>
                        <input name="pg[pg_net_worth][]" class="form-control" value="">
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Comments if any</b></label>
                        <input name="pg[pg_comments][]" class="form-control" value="">
                    </div>
                    <div class="col-md-2 center">
                        <i class="fa fa-2x fa-plus-circle add-personal-guarantee-block mt-8"></i>
                    </div>
                </div>
                @endif
            </div>
          </div>
        </div>
    <!-- -------------- -->

    <!-- -------------- CORPORATE GUARANTEE BLOCK ------------ -->
    <div class="col-md-12">
          <div class="form-group row">
            <label for="txtPassword" class="col-md-12" style="background-color: #F2F2F2;padding: 5px 0px 5px 20px;"><b>Corporate Guarantee</b></label>
            <div class="col-md-6">
                <select name="corporate_guarantee" class="form-control show-hide" data-block_id="#corporate-guarantee-block">
                    <option value="">Select Corporate Guarantee</option>
                    <option value="1" {{(isset($offerData->offerCg) && count($offerData->offerCg) > 0)? 'selected': ''}}>Applicable</option>
                    <option value="2" {{(isset($offerData->offerCg) && count($offerData->offerCg) == 0)? 'selected': ''}}>Not Applicable</option>
                </select>
            </div>
            <div class="col-md-12" id="corporate-guarantee-block" style="display: {{(isset($offerData->offerCg) && count($offerData->offerCg) > 0)? 'block': 'none'}};">
            @if(isset($offerData->offerCg) && count($offerData->offerCg) > 0)
                @foreach($offerData->offerCg as $key=>$cg)
                <div class="row mt10">
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Type</b></label>
                        @endif
                        <select name="cg[cg_type_id][]" class="form-control">
                            <option value="">Select type</option>
                            <option value="1" {{($cg->cg_type_id == 1)? 'selected': ''}}>Corporate Guarante with BR</option>
                            <option value="2" {{($cg->cg_type_id == 2)? 'selected': ''}}>Letter of Comfort with BR</option>
                            <option value="3" {{($cg->cg_type_id == 3)? 'selected': ''}}>Corporate Guarantee w/o BR</option>
                            <option value="4" {{($cg->cg_type_id == 4)? 'selected': ''}}>Letter of Comfort w/o BR</option>
                            <option value="5" {{($cg->cg_type_id == 5)? 'selected': ''}}>Put option with BR</option>
                            <option value="6" {{($cg->cg_type_id == 6)? 'selected': ''}}>Put option w/o BR</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Select Guarantor</b></label>
                        @endif
                        <select name="cg[cg_name_of_guarantor_id][]" class="form-control">
                            <option value="">Select Guarantor</option>
                            @foreach($bizOwners as $key=>$bizOwner)
                            <option value="{{$bizOwner->biz_owner_id}}" {{($cg->cg_name_of_guarantor_id == $bizOwner->biz_owner_id)? 'selected': ''}}>{{ucwords($bizOwner->first_name)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Time for security</b></label>
                        @endif
                        <select name="cg[cg_time_for_perfecting_security_id][]" class="form-control">
                            <option value="">Select time for perfecting security</option>
                            <option value="1" {{($cg->cg_time_for_perfecting_security_id == 1)? 'selected': ''}}>Before Disbusrement</option>
                            <option value="2" {{($cg->cg_time_for_perfecting_security_id == 2)? 'selected': ''}}>With in 30 days from date of first disbusrement</option>
                            <option value="3" {{($cg->cg_time_for_perfecting_security_id == 3)? 'selected': ''}}>With in 60 days from date of first disbsurement</option>
                            <option value="4" {{($cg->cg_time_for_perfecting_security_id == 4)? 'selected': ''}}>With in 90 days from date of first disbursement </option>
                            <option value="5" {{($cg->cg_time_for_perfecting_security_id == 5)? 'selected': ''}}>With in 120 days from date of first disbursement</option>
                            <option value="6" {{($cg->cg_time_for_perfecting_security_id == 6)? 'selected': ''}}>with in 180 days from date of first disbursement</option>
                            <option value="7" {{($cg->cg_time_for_perfecting_security_id == 7)? 'selected': ''}}>with in 360 days from date of first disbsurement</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword"><b>Residential Address</b></label>
                        @endif
                        <input name="cg[cg_residential_address][]" class="form-control" value="{{$cg->cg_residential_address}}">
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Comments if any</b></label>
                        @endif
                        <input name="cg[cg_comments][]" class="form-control" value="{{$cg->cg_comments}}">
                    </div>
                    <div class="col-md-2 center">
                        @if($loop->first)
                        <i class="fa fa-2x fa-plus-circle add-corporate-guarantee-block mt-8"></i>
                        @else
                        <i class="fa fa-2x fa-times-circle remove-corporate-guarantee-block" style="color: red;"></i>
                        @endif
                    </div>
                </div>
                @endforeach
                @else
                <div class="row mt10">
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Type</b></label>
                        <select name="cg[cg_type_id][]" class="form-control">
                            <option value="">Select type</option>
                            <option value="1">Corporate Guarante with BR</option>
                            <option value="2">Letter of Comfort with BR</option>
                            <option value="3">Corporate Guarantee w/o BR</option>
                            <option value="4">Letter of Comfort w/o BR</option>
                            <option value="5">Put option with BR</option>
                            <option value="6">Put option w/o BR</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Select Guarantor</b></label>
                        <select name="cg[cg_name_of_guarantor_id][]" class="form-control">
                            <option value="">Select Guarantor</option>
                            @foreach($bizOwners as $key=>$bizOwner)
                            <option value="{{$bizOwner->biz_owner_id}}">{{ucwords($bizOwner->first_name)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Time for security</b></label>
                        <select name="cg[cg_time_for_perfecting_security_id][]" class="form-control">
                            <option value="">Select time for perfecting security</option>
                            <option value="1">Before Disbusrement</option>
                            <option value="2">With in 30 days from date of first disbusrement</option>
                            <option value="3">With in 60 days from date of first disbsurement</option>
                            <option value="4">With in 90 days from date of first disbursement </option>
                            <option value="5">With in 120 days from date of first disbursement</option>
                            <option value="6">with in 180 days from date of first disbursement</option>
                            <option value="7">with in 360 days from date of first disbsurement</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword"><b>Residential Address</b></label>
                        <input name="cg[cg_residential_address][]" class="form-control" value="">
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Comments if any</b></label>
                        <input name="cg[cg_comments][]" class="form-control" value="">
                    </div>
                    <div class="col-md-2 center">
                        <i class="fa fa-2x fa-plus-circle add-corporate-guarantee-block mt-8"></i>
                    </div>
                </div>
                @endif
            </div>
          </div>
        </div>
    <!-- -------------- -->

    <!-- -------------- ESCROW MECHANISM BLOCK ------------ -->
    <div class="col-md-12">
          <div class="form-group row">
            <label for="txtPassword" class="col-md-12" style="background-color: #F2F2F2;padding: 5px 0px 5px 20px;"><b>Escrow Mechanism</b></label>
            <div class="col-md-6">
                <select name="escrow_mechanism" class="form-control show-hide" data-block_id="#escrow-mechanism-block">
                    <option value="">Select Escrow Mechanism</option>
                    <option value="1" {{(isset($offerData->offerEm) && count($offerData->offerEm) > 0)? 'selected': ''}}>Applicable</option>
                    <option value="2" {{(isset($offerData->offerEm) && count($offerData->offerEm) == 0)? 'selected': ''}}>Not Applicable</option>
                </select>
            </div>
            <div class="col-md-12" id="escrow-mechanism-block" style="display: {{(isset($offerData->offerEm) && count($offerData->offerEm) > 0)? 'block': 'none'}};">
            @if(isset($offerData->offerEm) && count($offerData->offerEm) > 0)
                @foreach($offerData->offerEm as $key=>$em)
                <div class="row mt10">
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Name of Debtor</b></label>
                        @endif
                        <select name="em[em_debtor_id][]" class="form-control">
                            <option value="">Select Debtor</option>
                            @foreach($anchors as $key=>$anchor)
                            <option value="{{$anchor->anchor_id}}" {{($em->em_debtor_id == $anchor->anchor_id)? 'selected': ''}}>{{$anchor->comp_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword"><b>Expected cash flow per month</b></label>
                        @endif
                        <input name="em[em_expected_cash_flow][]" class="form-control" value="{{$em->em_expected_cash_flow}}">
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Time for security</b></label>
                        @endif
                        <select name="em[em_time_for_perfecting_security_id][]" class="form-control">
                            <option value="">Select time for perfecting security</option>
                            <option value="1" {{($em->em_time_for_perfecting_security_id == 1)? 'selected': ''}}>Before Disbusrement</option>
                            <option value="2" {{($em->em_time_for_perfecting_security_id == 2)? 'selected': ''}}>With in 30 days from date of first disbusrement</option>
                            <option value="3" {{($em->em_time_for_perfecting_security_id == 3)? 'selected': ''}}>With in 60 days from date of first disbsurement</option>
                            <option value="4" {{($em->em_time_for_perfecting_security_id == 4)? 'selected': ''}}>With in 90 days from date of first disbursement </option>
                            <option value="5" {{($em->em_time_for_perfecting_security_id == 5)? 'selected': ''}}>With in 120 days from date of first disbursement</option>
                            <option value="6" {{($em->em_time_for_perfecting_security_id == 6)? 'selected': ''}}>with in 180 days from date of first disbursement</option>
                            <option value="7" {{($em->em_time_for_perfecting_security_id == 7)? 'selected': ''}}>with in 360 days from date of first disbsurement</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Mechanism</b></label>
                        @endif
                        <select name="em[em_mechanism_id][]" class="form-control">
                            <option value="">Select Mechanism</option>
                            <option value="1" {{($em->em_mechanism_id == 1)? 'selected': ''}}>With direct Payment confirmation</option>
                            <option value="2" {{($em->em_mechanism_id == 2)? 'selected': ''}}>W/o direct payment confirmation</option>
                            <option value="3" {{($em->em_mechanism_id == 3)? 'selected': ''}}>With payment confirmation with Escrow a/c</option>
                            <option value="4" {{($em->em_mechanism_id == 4)? 'selected': ''}}>W/o payment confirmation w/o Escrow a/c</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($loop->first)
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Comments if any</b></label>
                        @endif
                        <input name="em[em_comments][]" class="form-control" value="{{$em->em_comments}}">
                    </div>
                    <div class="col-md-2 center">
                        @if($loop->first)
                        <i class="fa fa-2x fa-plus-circle add-escrow-mechanism-block mt-8"></i>
                        @else
                        <i class="fa fa-2x fa-times-circle remove-escrow-mechanism-block" style="color: red;"></i>
                        @endif
                    </div>
                </div>
                @endforeach
                @else
                <div class="row mt10">
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Name of Debtor</b></label>
                        <select name="em[em_debtor_id][]" class="form-control">
                            <option value="">Select Debtor</option>
                            @foreach($anchors as $key=>$anchor)
                            <option value="{{$anchor->anchor_id}}">{{$anchor->comp_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword"><b>Expected cash flow per month</b></label>
                        <input name="em[em_expected_cash_flow][]" class="form-control" value="">
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Time for security</b></label>
                        <select name="em[em_time_for_perfecting_security_id][]" class="form-control">
                            <option value="">Select time for perfecting security</option>
                            <option value="1">Before Disbusrement</option>
                            <option value="2">With in 30 days from date of first disbusrement</option>
                            <option value="3">With in 60 days from date of first disbsurement</option>
                            <option value="4">With in 90 days from date of first disbursement </option>
                            <option value="5">With in 120 days from date of first disbursement</option>
                            <option value="6">with in 180 days from date of first disbursement</option>
                            <option value="7">with in 360 days from date of first disbsurement</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Mechanism</b></label>
                        <select name="em[em_mechanism_id][]" class="form-control">
                            <option value="">Select Mechanism</option>
                            <option value="1">With direct Payment confirmation</option>
                            <option value="2">W/o direct payment confirmation</option>
                            <option value="3">With payment confirmation with Escrow a/c</option>
                            <option value="4">W/o payment confirmation w/o Escrow a/c</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="txtPassword" style="margin-bottom: 30px;"><b>Comments if any</b></label>
                        <input name="em[em_comments][]" class="form-control" value="">
                    </div>
                    <div class="col-md-2 center">
                        <i class="fa fa-2x fa-plus-circle add-escrow-mechanism-block mt-8"></i>
                    </div>
                </div>
                @endif
            </div>
          </div>
        </div>
    <!-- -------------- -->
    
    
    
    <div class="col-md-6">
      <div class="form-group">
        <label for="txtPassword"><b>Comment</b></label> 
        <textarea class="form-control" name="comment" rows="3" col="3" placeholder="Comment" maxlength="250">{{isset($offerData->comment)? $offerData->comment: ''}}</textarea>
      </div>
    </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <button type="submit" class="btn btn-success btn-sm float-right">Submit</button>
      </div>
    </div>   
  </form>
 
@endsection

@section('jscript')
<script>
    var bizOwners = {!! json_encode($bizOwners) !!};
    var anchors = {!! json_encode($anchors) !!};
    
    function anchorDropdown(anchors){
        let $html='<option value="">Select Debtor</option>';
        $.each(anchors,function(i,anchor){
            $html += '<option value="'+anchor.anchor_id+'">'+anchor.comp_name+'</option>';
        })
        return $html;
    }

    function guarantorDropdown(bizOwners){
        let $html='<option value="">Select Guarantor</option>';
        $.each(bizOwners,function(i,bizOwner){
            $html += '<option value="'+bizOwner.biz_owner_id+'">'+bizOwner.first_name+'</option>';
        })
        return $html;
    }

    var messages = {
        "get_program_balance_limit" : "{{route('ajax_get_program_balance_limit')}}",
        "token" : "{{ csrf_token() }}"  
    };
    var current_offer_amt = "{{$currentOfferAmount}}";
    var prgm_consumed_limit = 0;
    var anchorPrgms = {!! json_encode($anchorPrgms) !!};
    var anchor_id = {{$offerData->anchor_id ?? 0}};
    var program_id = {{$offerData->prgm_id ?? 0}};
    var limit_balance = {{$limitBalance}};
    $(document).ready(function(){
        fillPrograms(anchor_id, anchorPrgms)
    })

    $('#anchor_id').on('change',function(){
        unsetError('input[name=prgm_limit_amt]');
        unsetError('input[name=interest_rate]');
        let anchor_id = $('#anchor_id').val();
        setLimit('input[name=prgm_limit_amt]', '');
        setLimit('input[name=interest_rate]', '');
        fillPrograms(anchor_id, anchorPrgms);                
    });

    function fillPrograms(anchor_id, programs){
        let html = '<option value="" data-sub_limit="0" data-min_rate="0" data-max_rate="0" data-min_limit="0" data-max_limit="0">Select Program</option>';
        $.each(programs, function(i,program){
            if(program.prgm_name != null && program.anchor_id == anchor_id){

                let base_rate = (program.base_rate != null)? program.base_rate.base_rate: 0;
                let min_rate = parseFloat(program.min_interest_rate) + parseFloat(base_rate);
                let max_rate = parseFloat(program.max_interest_rate) + parseFloat(base_rate);
                html += '<option value="'+program.prgm_id+'" data-sub_limit="'+program.anchor_sub_limit+'" data-min_rate="'+min_rate.toFixed(2)+'"  data-max_rate="'+max_rate.toFixed(2)+'" data-min_limit="'+program.min_loan_size+'" data-max_limit="'+program.max_loan_size+'" '+((program.prgm_id == program_id)? "selected": "")+'>'+program.prgm_name+'</option>';
            }
        });
        $('#program_id').html(html);
    }

    $('#program_id').on('change',function(){
        if ($("#offer_id").val() == "") {            
            $('input[name=margin]').val("");
            $('input[name=overdue_interest_rate]').val("");                    
            $('input[name=adhoc_interest_rate]').val("");                                        
            $('input[name=grace_period]').val("");                                        
            $('input[name="processing_fee"]').val("");                                        
            $('input[name="document_fee"]').val(""); 
        }
                        
        unsetError('input[name=prgm_limit_amt]');
        unsetError('input[name=interest_rate]');
        let program_min_rate = $('#program_id option:selected').data('min_rate');
        let program_max_rate = $('#program_id option:selected').data('max_rate');
        let program_min_limit = parseInt($('#program_id option:selected').data('min_limit'));
        let program_max_limit = parseInt($('#program_id option:selected').data('max_limit'));
        let program_id = $('#program_id').val();
        setLimit('input[name=prgm_limit_amt]', '');
        setLimit('input[name=interest_rate]', '');
        fillProgramData(anchorPrgms);

        if(program_id == ''){
            unsetError('select[name=prgm_id]');
            setLimit('input[name=prgm_limit_amt]', '');
            setLimit('input[name=interest_rate]', '');
            return;
        }else{
            unsetError('select[name=prgm_id]');
            setLimit('input[name=prgm_limit_amt]', '(<i class="fa fa-inr" aria-hidden="true"></i> '+program_min_limit+'-<i class="fa fa-inr" aria-hidden="true"></i> '+program_max_limit+')');
            setLimit('input[name=interest_rate]', '('+program_min_rate+'%-'+program_max_rate+'%)');
        }
        let token = "{{ csrf_token() }}";            

        $('.isloader').show();
        $.ajax({
            'url':messages.get_program_balance_limit,
            'type':"POST",
            'data':{"_token" : messages.token, "program_id" : program_id},
            error:function (xhr, status, errorThrown) {
                $('.isloader').hide();
                alert(errorThrown);
            },
            success:function(res){
                res = JSON.parse(res);
                /*if ($("#offer_id").val() == "") {
                    var prgm_data = res.prgm_data;
                    $('input[name=margin]').val(prgm_data.margin);
                    $('input[name=overdue_interest_rate]').val(prgm_data.overdue_interest_rate);
                    if (prgm_data.is_adhoc_facility == '1') {
                        $('input[name=adhoc_interest_rate]').val(prgm_data.adhoc_interest_rate);
                    }
                    if (prgm_data.is_grace_period == '1') {
                        $('input[name=grace_period]').val(prgm_data.grace_period);
                    }
                    if (prgm_data.processing_fee_amt != '') {
                        var processing_fee_type = prgm_data.processing_fee_type == '2' ? '%' : '&#8377;';
                        $("#processing_fee_type").html("(" + processing_fee_type + ")");
                        $('input[name="processing_fee"]').val(prgm_data.processing_fee_amt);
                    }
                    if (prgm_data.document_fee_amt != '') {
                        var document_fee_type = prgm_data.document_fee_type == '2' ? '%' : '&#8377;';
                        $("#document_fee_type").html("(" + document_fee_type + ")");
                        $('input[name="document_fee"]').val(prgm_data.document_fee_amt);
                    }
                }*/
                prgm_consumed_limit = parseInt(res.prgm_limit) - current_offer_amt;
                $('.isloader').hide();
            }
        })
    });

  function checkSupplyValidations(){
    var limitObj={
        'prgm_min_rate':$('#program_id option:selected').data('min_rate'),
        'prgm_max_rate':$('#program_id option:selected').data('max_rate'),
        'prgm_min_limit':$('#program_id option:selected').data('min_limit'),
        'prgm_max_limit':$('#program_id option:selected').data('max_limit'),
        'limit_balance_amt':"{{(int)$limitData->limit_amt - (int)$subTotalAmount + (int)$currentOfferAmount}}",
        'prgm_balance_limit':$('#program_id option:selected').data('sub_limit') - prgm_consumed_limit,
    };

    unsetError('select[name=anchor_id]');
    unsetError('select[name=prgm_id]');
    unsetError('input[name=prgm_limit_amt]');
    unsetError('input[name=interest_rate]');
    unsetError('input[name=tenor]');
    unsetError('input[name=tenor_old_invoice]');
    unsetError('input[name=margin]');
    unsetError('input[name=overdue_interest_rate]');
    unsetError('input[name=adhoc_interest_rate]');
    unsetError('input[name=grace_period]');
    //unsetError('input[name=processing_fee]');
    //unsetError('input[name=document_fee]');
    unsetError('input[name*=charge_names]');

    let flag = true;
    let anchor_id = $('select[name=anchor_id]').val();
    let prgm_id = $('select[name=prgm_id]').val();
    let prgm_limit_amt = $('input[name=prgm_limit_amt]').val();
    let interest_rate = $('input[name=interest_rate]').val();
    let tenor = $('input[name=tenor]').val();
    let tenor_old_invoice = $('input[name=tenor_old_invoice]').val().trim();
    let margin = $('input[name=margin]').val().trim();
    let overdue_interest_rate = $('input[name=overdue_interest_rate]').val().trim();
    let adhoc_interest_rate = $('input[name=adhoc_interest_rate]').val().trim();
    let grace_period = $('input[name=grace_period]').val().trim();
    //let processing_fee = $('input[name=processing_fee]').val().trim();
    //let document_fee = $('input[name=document_fee]').val().trim();

    if(anchor_id == ''){
        setError('select[name=anchor_id]', 'Please select Anchor');
        flag = false;
    }

    if(prgm_id == ''){
        setError('select[name=prgm_id]', 'Please select Program');
        flag = false;
    }

    if(prgm_limit_amt.length == 0 || parseInt(prgm_limit_amt.replace(/,/g, '')) == 0){
        setError('input[name=prgm_limit_amt]', 'Please fill program limit amount');
        flag = false;
    }else if(anchor_id !='' && prgm_id != ''){
        if((parseInt(prgm_limit_amt.replace(/,/g, '')) < parseInt(limitObj.prgm_min_limit)) ||(parseInt(prgm_limit_amt.replace(/,/g, '')) > parseInt(limitObj.prgm_max_limit))){
            setError('input[name=prgm_limit_amt]', 'Limit amount should be ('+parseInt(limitObj.prgm_min_limit)+'-'+parseInt(limitObj.prgm_max_limit)+') program range');
            flag = false;
        }else if(parseInt(prgm_limit_amt.replace(/,/g, '')) > parseInt(limitObj.prgm_balance_limit)){
            setError('input[name=prgm_limit_amt]', 'Limit amount should be less than ('+limitObj.prgm_balance_limit+') program balance limit');
            flag = false;
        }else if(parseInt(prgm_limit_amt.replace(/,/g, '')) > parseInt(limit_balance)){
            setError('input[name=prgm_limit_amt]', 'Limit amount should not greater than balance limit');
            flag = false;
        }else{
            //TAKE REST limit_balance
        }
    }

    if(interest_rate == '' || isNaN(interest_rate)){
        setError('input[name=interest_rate]', 'Please fill intereset rate');
        flag = false;
    }else if(anchor_id !='' && prgm_id != ''){
        if(parseFloat(interest_rate) > 100){
            setError('input[name=interest_rate]', 'Please fill correct intereset rate');
            flag = false;
        }else if((parseFloat(interest_rate) < parseFloat(limitObj.prgm_min_rate)) || parseFloat(interest_rate) > parseFloat(limitObj.prgm_max_rate)){
            setError('input[name=interest_rate]', 'Interest rate should be ('+limitObj.prgm_min_rate+'% - '+limitObj.prgm_max_rate+'%) range');
            flag = false;
        }else{
            //TAKE REST
        }
    } 

    if(tenor == ''){
        setError('input[name=tenor]', 'Please flll invoice tenor');
        flag = false;
    }

    if(tenor_old_invoice == ''){
        setError('input[name=tenor_old_invoice]', 'Please fill old invoice tenor');
        flag = false;
    }

    if(margin == '' || isNaN(margin)){
        setError('input[name=margin]', 'Please fill margin');
        flag = false;
    }else if(parseFloat(margin) > 100){
        setError('input[name=margin]', 'Please fill correct margin rate');
        flag = false;
    }

    if(overdue_interest_rate == '' || isNaN(overdue_interest_rate)){
        setError('input[name=overdue_interest_rate]', 'Please fill Overdue intereset rate');
        flag = false;
    }else if(parseFloat(overdue_interest_rate) > 100){
        setError('input[name=overdue_interest_rate]', 'Please fill correct overdue interest rate');
        flag = false;
    }

    if(adhoc_interest_rate == '' || isNaN(adhoc_interest_rate)){
        setError('input[name=adhoc_interest_rate]', 'Please fill adhoc interest rate');
        flag = false;
    }else if(parseFloat(adhoc_interest_rate) > 100){
        setError('input[name=adhoc_interest_rate]', 'Please fill correct adhoc interest rate');
        flag = false;
    }

    if(grace_period == ''){
        setError('input[name=grace_period]', 'Please fill grace period');
        flag = false;
    }


    $.each($('input[name*=charge_names]'), function(i, val){
        let data = $(this).val();
        let type = $(this).data('type');
        let name = $(this).data('name');
        if(data == '' || isNaN(data)){
            setError('input[name*=charge_names]:eq('+i+')', 'Please fill '+name);
            flag = false;
        }else if(type == 2 && parseFloat(data) > 100){
            setError('input[name*=charge_names]:eq('+i+')', name+' can not be greater than 100 percent');
            flag = false;
        }
    })

    /*if(processing_fee == '' || isNaN(processing_fee)){
        setError('input[name=processing_fee]', 'Please fill processing fee');
        flag = false;
    }else if(parseFloat(processing_fee) > 100){
        setError('input[name=processing_fee]', 'Processing fee can not be greater than 100 percent');
        flag = false;
    }*/

    /*if(document_fee == '' || isNaN(document_fee)){
        setError('input[name=document_fee]', 'Please fill document fee');
        flag = false;
    }else if(parseFloat(document_fee) > 100){
        setError('input[name=document_fee]', 'Document fee can not be greater than 100 percent');
        flag = false;
    }*/

    if(flag){
        return true;
    }else{
        return false;
    }
  }

  $(document).on('click', '.add-primary-security-block', function(){
    let primary_security_block = '<div class="row mt10">'+
            '<div class="col-md-2">'+
                '<select name="ps[ps_security_id][]" class="form-control">'+
                    '<option value="">Select Security</option>'+
                    '<option value="1">Current assets</option>'+
                    '<option value="2">Plant and Machinery</option>'+
                    '<option value="3">Land & Building</option>'+
                    '<option value="4">Commercial Property</option>'+
                    '<option value="5">Land</option>'+
                    '<option value="6">Industrial Premises</option>'+
                    '<option value="7">Residential Property</option>'+
                    '<option value="8">Farm House & Land</option>'+
                    '<option value="9">Listed Share</option>'+
                    '<option value="10">Unlisted Share</option>'+
                    '<option value="11">Mutual Funds</option>'+
                    '<option value="12">Intercorporate Deposits</option>'+
                    '<option value="13">Bank Guarantee</option>'+
                    '<option value="14">SBLC</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<select name="ps[ps_type_of_security_id][]" class="form-control">'+
                    '<option value="">Select type of Security</option>'+
                    '<option value="1">Registered Mortgage</option>'+
                    '<option value="2">Equitable Mortgage</option>'+
                    '<option value="3">Hypothecation</option>'+
                    '<option value="4">Pledge</option>'+
                    '<option value="5">Lien</option>'+
                    '<option value="6">Negative Lien</option>'+
                    '<option value="7">Deposit of Title deeds</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<select name="ps[ps_status_of_security_id][]" class="form-control">'+
                    '<option value="">Select status of Security</option>'+
                    '<option value="1">First Pari-pasu</option>'+
                    '<option value="2">Exclusive</option>'+
                    '<option value="3">Third Pari-pasu</option>'+
                    '<option value="4">Second Pari-pasu</option>'+
                    '<option value="5">Sub-Servient</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<select name="ps[ps_time_for_perfecting_security_id][]" class="form-control">'+
                    '<option value="">Select time for perfecting security</option>'+
                    '<option value="1">Before Disbusrement</option>'+
                    '<option value="2">With in 30 days from date of first disbusrement</option>'+
                    '<option value="3">With in 60 days from date of first disbsurement</option>'+
                    '<option value="4">With in 90 days from date of first disbursement </option>'+
                    '<option value="5">With in 120 days from date of first disbursement</option>'+
                    '<option value="6">with in 180 days from date of first disbursement</option>'+
                    '<option value="7">with in 360 days from date of first disbsurement</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<input name="ps[ps_desc_of_security][]" class="form-control" value="">'+
            '</div>'+
            '<div class="col-md-2 center">'+
                '<i class="fa fa-2x fa-times-circle remove-primary-security-block" style="color: red;"></i>'+
            '</div>'+
        '</div>';
    $('#primary-security-block').append(primary_security_block);
  });

  $(document).on('click', '.add-collateral-security-block', function(){
    let primary_security_block = '<div class="row mt10">'+
            '<div class="col-md-2">'+
                '<select name="cs[cs_desc_security_id][]" class="form-control">'+
                    '<option value="">Select Security</option>'+
                    '<option value="1">Current assets</option>'+
                    '<option value="2">Plant and Machinery</option>'+
                    '<option value="3">Land & Building</option>'+
                    '<option value="4">Commercial Property</option>'+
                    '<option value="5">Land</option>'+
                    '<option value="6">Industrial Premises</option>'+
                    '<option value="7">Residential Property</option>'+
                    '<option value="8">Farm House & Land</option>'+
                    '<option value="9">Listed Share</option>'+
                    '<option value="10">Unlisted Share</option>'+
                    '<option value="11">Mutual Funds</option>'+
                    '<option value="12">Intercorporate Deposits</option>'+
                    '<option value="13">Bank Guarantee</option>'+
                    '<option value="14">SBLC</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<select name="cs[cs_type_of_security_id][]" class="form-control">'+
                    '<option value="">Select type of Security</option>'+
                    '<option value="1">Registered Mortgage</option>'+
                    '<option value="2">Equitable Mortgage</option>'+
                    '<option value="3">Hypothecation</option>'+
                    '<option value="4">Pledge</option>'+
                    '<option value="5">Lien</option>'+
                    '<option value="6">Negative Lien</option>'+
                    '<option value="7">Deposit of Title deeds</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<select name="cs[cs_status_of_security_id][]" class="form-control">'+
                    '<option value="">Select status of Security</option>'+
                    '<option value="1">First Pari-pasu</option>'+
                    '<option value="2">Exclusive</option>'+
                    '<option value="3">Third Pari-pasu</option>'+
                    '<option value="4">Second Pari-pasu</option>'+
                    '<option value="5">Sub-Servient</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<select name="cs[cs_time_for_perfecting_security_id][]" class="form-control">'+
                    '<option value="">Select time for perfecting security</option>'+
                    '<option value="1">Before Disbusrement</option>'+
                    '<option value="2">With in 30 days from date of first disbusrement</option>'+
                    '<option value="3">With in 60 days from date of first disbsurement</option>'+
                    '<option value="4">With in 90 days from date of first disbursement </option>'+
                    '<option value="5">With in 120 days from date of first disbursement</option>'+
                    '<option value="6">with in 180 days from date of first disbursement</option>'+
                    '<option value="7">with in 360 days from date of first disbsurement</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<input name="cs[cs_desc_of_security][]" class="form-control" value="">'+
            '</div>'+
            '<div class="col-md-2 center">'+
                '<i class="fa fa-2x fa-times-circle remove-collateral-security-block" style="color: red;"></i>'+
            '</div>'+
        '</div>';
    $('#collateral-security-block').append(primary_security_block);
  });

  $(document).on('click', '.add-personal-guarantee-block', function(){
    let guarantorOption = guarantorDropdown(bizOwners);
    let personal_guarantee_block = '<div class="row mt10">'+
            '<div class="col-md-2">'+
                '<select name="pg[pg_name_of_guarantor_id][]" class="form-control">'+guarantorOption+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<select name="pg[pg_time_for_perfecting_security_id][]" class="form-control">'+
                    '<option value="">Select time for perfecting security</option>'+
                    '<option value="1">Before Disbusrement</option>'+
                    '<option value="2">With in 30 days from date of first disbusrement</option>'+
                    '<option value="3">With in 60 days from date of first disbsurement</option>'+
                    '<option value="4">With in 90 days from date of first disbursement </option>'+
                    '<option value="5">With in 120 days from date of first disbursement</option>'+
                    '<option value="6">with in 180 days from date of first disbursement</option>'+
                    '<option value="7">with in 360 days from date of first disbsurement</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<input name="pg[pg_residential_address][]" class="form-control" value="">'+
            '</div>'+
            '<div class="col-md-2">'+
                '<input name="pg[pg_net_worth][]" class="form-control" value="">'+
            '</div>'+
            '<div class="col-md-2">'+
                '<input name="pg[pg_comments][]" class="form-control" value="">'+
            '</div>'+
            '<div class="col-md-2 center">'+
                '<i class="fa fa-2x fa-times-circle remove-personal-guarantee-block" style="color: red;"></i>'+
            '</div>'+
        '</div>';
    $('#personal-guarantee-block').append(personal_guarantee_block);
  });

  $(document).on('click', '.add-corporate-guarantee-block', function(){
    let guarantorOption = guarantorDropdown(bizOwners);
    let corporate_guarantee_block = '<div class="row mt10">'+
            '<div class="col-md-2">'+
                '<select name="cg[cg_type_id][]" class="form-control">'+
                    '<option value="">Select type</option>'+
                    '<option value="1">Corporate Guarante with BR</option>'+
                    '<option value="2">Letter of Comfort with BR</option>'+
                    '<option value="3">Corporate Guarantee w/o BR</option>'+
                    '<option value="4">Letter of Comfort w/o BR</option>'+
                    '<option value="5">Put option with BR</option>'+
                    '<option value="6">Put option w/o BR</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<select name="cg[cg_name_of_guarantor_id][]" class="form-control">'+guarantorOption+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<select name="cg[cg_time_for_perfecting_security_id][]" class="form-control">'+
                    '<option value="">Select time for perfecting security</option>'+
                    '<option value="1">Before Disbusrement</option>'+
                    '<option value="2">With in 30 days from date of first disbusrement</option>'+
                    '<option value="3">With in 60 days from date of first disbsurement</option>'+
                    '<option value="4">With in 90 days from date of first disbursement </option>'+
                    '<option value="5">With in 120 days from date of first disbursement</option>'+
                    '<option value="6">with in 180 days from date of first disbursement</option>'+
                    '<option value="7">with in 360 days from date of first disbsurement</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<input name="cg[cg_residential_address][]" class="form-control" value="">'+
            '</div>'+
            '<div class="col-md-2">'+
                '<input name="cg[cg_comments][]" class="form-control" value="">'+
            '</div>'+
            '<div class="col-md-2 center">'+
                '<i class="fa fa-2x fa-times-circle remove-corporate-guarantee-block" style="color: red;"></i>'+
            '</div>'+
        '</div>';
    $('#corporate-guarantee-block').append(corporate_guarantee_block);
  });

  $(document).on('click', '.add-escrow-mechanism-block', function(){
    let anchorOption = anchorDropdown(anchors);
    let escrow_mechanism_block = '<div class="row mt10">'+
            '<div class="col-md-2">'+
                '<select name="em[em_debtor_id][]" class="form-control">'+anchorOption+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<input name="em[em_expected_cash_flow][]" class="form-control" value="">'+
            '</div>'+
            '<div class="col-md-2">'+
                '<select name="em[em_time_for_perfecting_security_id][]" class="form-control">'+
                    '<option value="">Select time for perfecting security</option>'+
                    '<option value="1">Before Disbusrement</option>'+
                    '<option value="2">With in 30 days from date of first disbusrement</option>'+
                    '<option value="3">With in 60 days from date of first disbsurement</option>'+
                    '<option value="4">With in 90 days from date of first disbursement </option>'+
                    '<option value="5">With in 120 days from date of first disbursement</option>'+
                    '<option value="6">with in 180 days from date of first disbursement</option>'+
                    '<option value="7">with in 360 days from date of first disbsurement</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<select name="em[em_mechanism_id][]" class="form-control">'+
                    '<option value="">Select Mechanism</option>'+
                    '<option value="1">With direct Payment confirmation</option>'+
                    '<option value="2">W/o direct payment confirmation</option>'+
                    '<option value="3">With payment confirmation with Escrow a/c</option>'+
                    '<option value="4">W/o payment confirmation w/o Escrow a/c</option>'+
                '</select>'+
            '</div>'+
            '<div class="col-md-2">'+
                '<input name="em[em_comments][]" class="form-control" value="">'+
            '</div>'+
            '<div class="col-md-2 center">'+
                '<i class="fa fa-2x fa-times-circle remove-escrow-mechanism-block" style="color: red;"></i>'+
            '</div>'+
        '</div>';
    $('#escrow-mechanism-block').append(escrow_mechanism_block);
  });

  $(document).on('click', '.remove-primary-security-block, .remove-collateral-security-block, .remove-personal-guarantee-block, .remove-corporate-guarantee-block, .remove-escrow-mechanism-block', function(){
    $(this).parent('div').parent('div').remove();
  });

  $(document).on('change', '.show-hide', function(){
    let selected_val = $(this).find('option:selected').val();
    let selector = $(this).data('block_id');

    if(selected_val == 1){
        $(selector).show();
    }else{
        $(selector).hide();
        $(selector+'>div:not(:first)').remove();
    }
  })

  function fillProgramData(anchorPrgms){
    let program_id = $('#program_id option:selected').val();
    if(typeof program_id == 'undefined' || program_id == '' || program_id == null){
        //$('input[name=tenor]').val();
        //$('input[name=tenor_old_invoice]').val();
        $('input[name=margin]').val('');
        $('input[name=overdue_interest_rate]').val('');
        $('input[name=adhoc_interest_rate]').val('');
        $('input[name=grace_period]').val('');
        //$('input[name=processing_fee]').val('');
        //$('input[name=document_fee]').val();
        return;
    }
    $.each(anchorPrgms, function(i,program){
        if(program.prgm_id == program_id){
            //$('input[name=tenor]').val();
            //$('input[name=tenor_old_invoice]').val();
            $('input[name=margin]').val(program.margin);
            $('input[name=overdue_interest_rate]').val(program.overdue_interest_rate);
            $('input[name=adhoc_interest_rate]').val(program.adhoc_interest_rate);
            $('input[name=grace_period]').val(program.grace_period);
            //$('input[name=processing_fee]').val(program.processing_fee);
            //$('input[name=document_fee]').val();
            fillChargesBlock(program);
        }
    });
  }

  function fillChargesBlock(program){
    console.log(program);
    let html='';
    $.each(program.program_charges, function(i,program_charge){
        if(program_charge.charge_name.chrg_tiger_id == 1){
            html += '<div class="col-md-6">'+
                '<div class="form-group">'+
                    '<label for="txtPassword"><b>'+program_charge.charge_name.chrg_name+((program_charge.charge_name.chrg_calculation_type == 2)? ' (%)':'')+'</b></label>'+
                    '<input type="text" name="charge_names['+program_charge.charge_id+'#'+program_charge.charge_name.chrg_calculation_type+']" data-type="'+program_charge.charge_name.chrg_calculation_type+'" class="form-control" data-name="'+program_charge.charge_name.chrg_name+'" placeholder="'+program_charge.charge_name.chrg_name+'" maxlength="6">'+
                '</div>'+
            '</div>';
            //value="'+program_charge.chrg_calculation_amt+'"
        }
    });
    $('.charges_block').html(html);
    //$(html).insertAfter(".charges_block");
  }
</script>
@endsection