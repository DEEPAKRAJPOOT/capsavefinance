@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')

@section('additional_css')
<style>
.card-title {
    font-size: 0.9rem;
    line-height: 1.375rem;
}
tr.border_bottom td {
  border-bottom:1pt solid #a19f9f;
}
</style>
@endsection

<div class="content-wrapper">
    <div class="row grid-margin mt-3 mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    @if($supplyOfferData->count() == 0 && $termOfferData->count() == 0 && $leaseOfferData->count() == 0 )
                    <div class="card card-color mb-0">
                        <div class="card-header">
                            <a class="card-title ">No offer found.</a>
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="accordion" class="accordion">
                                <!-- Start View Supply Chain Offer Block -->
                                @foreach($supplyOfferData as $key=>$supplyOffer)
                                @if($loop->first)
                                <div class="card card-color mb-0">
                                    <div class="card-header collapsed" data-toggle="collapse" href="#collapseOne" aria-expanded="false" style="background: #138864;color: #fff;"><h5 class="mb-0">Supply Chain Offer Details</h5>     
                                    </div>
                                    <div id="collapseOne" class="card-body bdr p-0 collapse" data-parent="#accordion" style="">
                                        <table cellspacing="0" cellpadding="0" width="100%" class="table table-striped table-bordered">
                                            <thead>
                                                <tr role="row" style="background: #62b59b;color: #fff; text-align: center;">
                                                   <th width="10%">Sr. No.</th>
                                                   <th width="70%" colspan="4">Offer Details</th>
                                                   <th width="20%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @endif
                                                <tr>
                                                    <td style="text-align: center;font-weight: 600;">{{$key+1}}</td>
                                                    <td><b>Apply Loan Amount: </b> </td>
                                                    <td>{{$supplyOffer->prgm_limit_amt}}</td>
                                                    <td><b>Documentation Fee (%): </b></td>
                                                    <td>{{$supplyOffer->document_fee}}</td>
                                                    <td><b>Status: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b> <label class="badge {{($supplyOffer->status == 1)? 'badge-success': 'badge-warning'}} current-status">{{($supplyOffer->status == 1)? 'Accepted': 'Pending'}}</label></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td></td>
                                                   <td><b>Interest Rate(%): </b></td>
                                                   <td>{{$supplyOffer->interest_rate}}</td>
                                                   <td><b>Tenor (Days) : </b></td>
                                                   <td>{{$supplyOffer->tenor}}</td>
                                                   <td><b>Created By: &nbsp;&nbsp;&nbsp;</b>{{\Helpers::getUserName($supplyOffer->created_by)}}</td>
                                                </tr>
                                                <tr>
                                                <td></td>
                                                   <td><b>Tenor for old invoice (Days): </b></td>
                                                   <td>{{$supplyOffer->tenor_old_invoice}}</td>
                                                   <td><b>Margin (%): </b></td>
                                                   <td>{{$supplyOffer->margin}}</td>
                                                   <td><b>Created At: &nbsp;&nbsp;&nbsp;</b>{{\Carbon\Carbon::parse($supplyOffer->created_at)->format('d-m-Y')}}</td>
                                                </tr>
                                                <tr>
                                                <td></td>
                                                    <td><b>Overdue Interest Rate (%): </b></td>
                                                    <td>{{$supplyOffer->overdue_interest_rate}}</td>
                                                    <td><b>Adhoc Interest Rate (%): </b></td>
                                                    <td>{{$supplyOffer->adhoc_interest_rate}}</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                <td></td>
                                                    <td><b>Grace Period (Days): </b></td>
                                                    <td>{{$supplyOffer->grace_period}}</td>
                                                    <td><b>Processing Fee (%): </b></td>
                                                    <td>{{$supplyOffer->processing_fee}}</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                <td></td>
                                                    <td><b>Comment: </b></td>
                                                    <td colspan="3">{{$supplyOffer->comment}}</td>
                                                    <td></td>
                                                </tr>
                                                @if($supplyOffer->offerPs->count() > 0)
                                                <tr>
                                                    <td></td>
                                                    <td colspan="4">
                                                        <table width="100%">
                                                            <tr>
                                                                <td rowspan="3"><b>Primary Security</b></td>
                                                                <td><b>Security</b></td>
                                                                <td><b>Type of Security</b></td>
                                                                <td><b>Status of Security</b></td>
                                                                <td><b>Time for security</b></td>
                                                                <td><b>Description of Security</b></td>
                                                            </tr>
                                                            @foreach($supplyOffer->offerPs as $key=>$ops)
                                                            <tr>
                                                                <td>{{($ops->ps_security_id != null)? config('common.ps_security_id')[$ops->ps_security_id]: 'NA'}}</td>
                                                                <td>{{($ops->ps_type_of_security_id != null)? config('common.ps_type_of_security_id')[$ops->ps_type_of_security_id]: 'NA'}}</td>
                                                                <td>{{($ops->ps_status_of_security_id != null)? config('common.ps_status_of_security_id')[$ops->ps_status_of_security_id]: 'NA'}}</td>
                                                                <td>{{($ops->ps_time_for_perfecting_security_id != null)? config('common.ps_time_for_perfecting_security_id')[$ops->ps_time_for_perfecting_security_id]: 'NA'}}</td>
                                                                <td>{{$ops->ps_desc_of_security}}</td>
                                                            </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                @endif

                                                @if($supplyOffer->offerCs->count() > 0)
                                                <tr>
                                                    <td></td>
                                                    <td colspan="4">
                                                        <table width="100%">
                                                            <tr>
                                                                <td rowspan="3"><b>Collateral Security</b></td>
                                                                <td><b>Description Security</b></td>
                                                                <td><b>Type of Security</b></td>
                                                                <td><b>Status of Security</b></td>
                                                                <td><b>Time for security</b></td>
                                                                <td><b>Description of Security</b></td>
                                                            </tr>
                                                            @foreach($supplyOffer->offerCs as $key=>$ocs)
                                                            <tr>
                                                                <td>{{($ocs->cs_desc_security_id != null)? config('common.cs_desc_security_id')[$ocs->cs_desc_security_id]: 'NA'}}</td>
                                                                <td>{{($ocs->cs_type_of_security_id != null)? config('common.cs_type_of_security_id')[$ocs->cs_type_of_security_id]: 'NA'}}</td>
                                                                <td>{{($ocs->cs_status_of_security_id != null)? config('common.cs_status_of_security_id')[$ocs->cs_status_of_security_id]: 'NA'}}</td>
                                                                <td>{{($ocs->cs_time_for_perfecting_security_id != null)? config('common.cs_time_for_perfecting_security_id')[$ocs->cs_time_for_perfecting_security_id]: 'NA'}}</td>
                                                                <td>{{$ocs->cs_desc_of_security}}</td>
                                                            </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                @endif

                                                @if($supplyOffer->offerPg->count() > 0)
                                                <tr>
                                                    <td></td>
                                                    <td colspan="4">
                                                        <table width="100%">
                                                            <tr>
                                                                <td rowspan="3"><b>Personal Guarantee</b></td>
                                                                <td><b>Guarantor</b></td>
                                                                <td><b>Time for security</b></td>
                                                                <td><b>Residential Address</b></td>
                                                                <td><b>Net worth as per ITR/CA Cert</b></td>
                                                                <td><b>Comments if any</b></td>
                                                            </tr>
                                                            @foreach($supplyOffer->offerPg as $key=>$opg)
                                                            <tr>
                                                                <td>{{($opg->owner)? $opg->owner->first_name: 'NA'}}</td>
                                                                <td>{{($opg->pg_time_for_perfecting_security_id != null)? config('common.pg_time_for_perfecting_security_id')[$opg->pg_time_for_perfecting_security_id]: 'NA'}}</td>
                                                                <td>{{$opg->pg_residential_address}}</td>
                                                                <td>{{$opg->pg_net_worth}}</td>
                                                                <td>{{$opg->pg_comments}}</td>
                                                            </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                @endif

                                                @if($supplyOffer->offerCg->count() > 0)
                                                <tr>
                                                    <td></td>
                                                    <td colspan="4">
                                                        <table width="100%">
                                                            <tr>
                                                                <td rowspan="3"><b>Corporate Guarantee</b></td>
                                                                <td><b>Type</b></td>
                                                                <td><b>Guarantor</b></td>
                                                                <td><b>Time for security</b></td>
                                                                <td><b>Residential Address</b></td>
                                                                <td><b>Comments if any</b></td>
                                                            </tr>
                                                            @foreach($supplyOffer->offerCg as $key=>$ocg)
                                                            <tr>
                                                                <td>{{($ocg->cg_type_id != null)? config('common.cg_type_id')[$ocg->cg_type_id]: 'NA'}}</td>
                                                                <td>{{($ocg->owner)? $ocg->owner->first_name: 'NA'}}</td>
                                                                <td>{{($ocg->cg_time_for_perfecting_security_id != null)? config('common.cg_time_for_perfecting_security_id')[$ocg->cg_time_for_perfecting_security_id]: 'NA'}}</td>
                                                                <td>{{$ocg->cg_residential_address}}</td>
                                                                <td>{{$ocg->cg_comments}}</td>
                                                            </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                @endif

                                                @if($supplyOffer->offerEm->count() > 0)
                                                <tr>
                                                    <td></td>
                                                    <td colspan="4">
                                                        <table width="100%">
                                                            <tr>
                                                                <td rowspan="3"><b>Escrow Mechanism</b></td>
                                                                <td><b>Debtor</b></td>
                                                                <td><b>Expected cash flow per month</b></td>
                                                                <td><b>Time for security</b></td>
                                                                <td><b>Mechanism</b></td>
                                                                <td><b>Comments if any</b></td>
                                                            </tr>
                                                            @foreach($supplyOffer->offerEm as $key=>$oem)
                                                            <tr>
                                                                <td>{{($oem->anchor)? $oem->anchor->comp_name: 'NA'}}</td>
                                                                <td>{{$oem->em_expected_cash_flow}}</td>
                                                                <td>{{($oem->em_time_for_perfecting_security_id != null)? config('common.em_time_for_perfecting_security_id')[$oem->em_time_for_perfecting_security_id]: 'NA'}}</td>
                                                                <td>{{($oem->em_mechanism_id != null)? config('common.em_mechanism_id')[$oem->em_mechanism_id]: 'NA'}}</td>
                                                                <td>{{$oem->em_comments}}</td>
                                                            </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                @endif

                                                @if($loop->last)
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                                <!-- End View Supply Chain Offer Block -->
                                <!-- Start View Term loan Offer Block -->
                                @foreach($termOfferData as $key=>$termOffer)
                                @if($loop->first)
                                <div class="card card-color mb-0">
                                    <div class="card-header collapsed" data-toggle="collapse" href="#collapseTwo" aria-expanded="false" style="background: #138864;color: #fff;"><h5 class="mb-0">Term Loan Offer Details</h5>     
                                    </div>
                                    <div id="collapseTwo" class="card-body bdr p-0 collapse" data-parent="#accordion" style="">
                                        <table cellspacing="0" cellpadding="0" width="100%" class="table table-striped table-bordered">
                                            <thead>
                                                <tr role="row" style="background: #62b59b;color: #fff; text-align: center;">
                                                   <th width="10%">Sr. No.</th>
                                                   <th width="70%" colspan="4">Offer Details</th>
                                                   <th width="20%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @endif
                                                <tr>
                                                    <td style="text-align: center;font-weight: 600;">{{$key+1}}</td>
                                                    <td><b>Facility Type: </b></td>
                                                    <td>Lease Loan</td>
                                                    <td><b>Apply Loan Amount: </b> </td>
                                                    <td>{{$termOffer->prgm_limit_amt}}</td>
                                                    <td><b>Status: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b> <label class="badge {{($termOffer->status == 1)? 'badge-success': 'badge-warning'}} current-status">{{($termOffer->status == 1)? 'Accepted': 'Pending'}}</label></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td></td>
                                                    <td><b>Tenor (Months): </b></td>
                                                    <td>{{$termOffer->tenor}}</td>
                                                    <td><b>Equipment Type: </b></td>
                                                    <td>{{\Helpers::getEquipmentTypeById($termOffer->equipment_type_id)->equipment_name}}</td>
                                                    <td><b>Created By: &nbsp;&nbsp;&nbsp;</b>{{\Helpers::getUserName($termOffer->created_by)}}</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td><b>Security Deposit: </b></td>
                                                    <td>{{$termOffer->security_deposit}}</td>
                                                    <td><b>Rental Frequency: </b></td>
                                                    <td>{{(($termOffer->rental_frequency == 1)?'Yearly':(($termOffer->rental_frequency == 2)? 'Bi-Yearly':(($termOffer->rental_frequency == 3)? 'Quaterly': 'Monthly')))}}</td>
                                                    <td><b>Created At: &nbsp;&nbsp;&nbsp;</b>{{\Carbon\Carbon::parse($termOffer->created_at)->format('d-m-Y')}}</td>
                                                </tr>
                                                <tr>
                                                <td></td>
                                                    <td><b>PTP Frequency: </b></td>
                                                    <td>
                                                        @if(isset($termOffer->offerPTPQ))   
                                                            @foreach ($termOffer->offerPTPQ as $ok => $ov)
                                                               {!!isset($ov->ptpq_from) ? '<b>From Period:</b> '.$ov->ptpq_from : ''!!}
                                                               {!!isset($ov->ptpq_to) ? '<b>&nbsp;&nbsp;&nbsp;To Period:</b> '.$ov->ptpq_to : ''!!}
                                                               {!!isset($ov->ptpq_rate) ? '<b>&nbsp;&nbsp;&nbsp;Rate:</b> '.$ov->ptpq_rate : ''!!}
                                                               <br/>
                                                            @endforeach 
                                                         @endif
                                                    </td>
                                                    <td><b>XIRR (%): </b></td>
                                                    <td>Ruby Sheet : {{$termOffer->ruby_sheet_xirr}}<br/>Cash Flow :{{$termOffer->cash_flow_xirr}}</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                <td></td>
                                                    <td><b>Additional Security: </b></td>
                                                    <td>
                                                        @php
                                                        $add_sec_arr = '';
                                                        if(isset($termOffer->addl_security)){
                                                            $addl_sec_arr = explode(',', $termOffer->addl_security);
                                                            foreach($addl_sec_arr as $k=>$v){
                                                                $add_sec_arr .= config('common.addl_security')[$v].', ';
                                                            }
                                                            if($termOffer->comment){
                                                                $add_sec_arr .= ' <b>Comment</b>:  '.$termOffer->comment;
                                                            }
                                                        }
                                                        @endphp 
                                                        {!! trim($add_sec_arr,', ') !!}
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                @if($loop->last)
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                                <!-- End View Term loan Offer Block -->
                                <!-- Start View Leasing Offer Block -->
                                @foreach($leaseOfferData as $key=>$leaseOffer)
                                @if($loop->first)
                                <div class="card card-color mb-0">
                                    <div class="card-header collapsed" data-toggle="collapse" href="#collapseThree" aria-expanded="false" style="background: #138864;color: #fff;"><h5 class="mb-0">Leasing Offer Details</h5>     
                                    </div>
                                    <div id="collapseThree" class="card-body bdr p-0 collapse" data-parent="#accordion" style="">
                                        <table cellspacing="0" cellpadding="0" width="100%" class="table table-striped table-bordered">
                                            <thead>
                                                <tr role="row" style="background: #62b59b;color: #fff; text-align: center;">
                                                   <th width="5%">Sr. No.</th>
                                                   <th width="80%" colspan="4">Offer Details</th>
                                                   <th width="15%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @endif
                                                <tr>
                                                    <td style="text-align: center;font-weight: 600;">{{$key+1}}</td>
                                                    <td width="15%"><b>Facility Type: </b></td>
                                                    <td width="25%">{{($leaseOffer->facility_type_id !='')? config('common.facility_type')[$leaseOffer->facility_type_id]: 'NA'}}</td>
                                                    <td><b>Equipment Type: </b></td>
                                                    <td>{{\Helpers::getEquipmentTypeById($leaseOffer->equipment_type_id)->equipment_name}}</td>
                                                    <td><b>Status: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b> <label class="badge {{($leaseOffer->status == 1)? 'badge-success': 'badge-warning'}} current-status">{{($leaseOffer->status == 1)? 'Accepted': 'Pending'}}</label></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td></td>
                                                    <td><b>Tenor (Months): </b></td>
                                                    <td>{{$leaseOffer->tenor}}</td>
                                                    <td width="15%" ><b>Limit of the Equipment: </b> </td>
                                                    <td width="25%">&#8377; {{number_format($leaseOffer->prgm_limit_amt)}}</td>
                                                    <td><b>Created By: &nbsp;&nbsp;&nbsp;</b>{{\Helpers::getUserName($leaseOffer->created_by)}}</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td><b>{{($leaseOffer->facility_type_id == 3)? 'Rental Discounting' : 'XIRR'}} (%): </b></td>
                                                    <td>
                                                    @if($leaseOffer->facility_type_id == 3)
                                                    {{$leaseOffer->discounting}}%
                                                    @else
                                                    <b>Ruby Sheet</b>: {{$leaseOffer->ruby_sheet_xirr}}%<br/><b>Cash Flow</b>: {{$leaseOffer->cash_flow_xirr}}%
                                                    @endif
                                                    </td>
                                                    <td><b>Rental Frequency: </b></td>
                                                    <td>{{(($leaseOffer->rental_frequency == 1)?'Yearly':(($leaseOffer->rental_frequency == 2)? 'Bi-Yearly':(($leaseOffer->rental_frequency == 3)? 'Quaterly': 'Monthly')))}} in {{($leaseOffer->rental_frequency_type == 1)? 'Advance' : 'Arrears'}}</td>
                                                    <td><b>Created At: &nbsp;&nbsp;&nbsp;</b>{{\Carbon\Carbon::parse($leaseOffer->created_at)->format('d-m-Y')}}</td>
                                                </tr>
                                                @if($leaseOffer->facility_type_id != 3)
                                                <tr>
                                                <td></td>
                                                    <td><b>PTP Frequency: </b></td>
                                                    <td>
                                                    @php 
                                                        $i = 1;
                                                        $arrStaticData['rentalFrequencyForPTPQ'] = array('1'=>'Year','2'=>'Bi-Yearly','3'=>'Quarter','4'=>'Months');
                                                        if(!empty($leaseOffer->offerPTPQ)){
                                                            $total = count($leaseOffer->offerPTPQ);
                                                    @endphp   
                                                    @foreach($leaseOffer->offerPTPQ as $key => $arr) 
                                                        @if($i > 1 && $i < $total)
                                                              ,
                                                        @elseif ($i > 1 && $i == $total)
                                                            and
                                                        @endif
                                                            &#8377; {{$arr->ptpq_rate}}  for  {{floor($arr->ptpq_from)}}- {{floor($arr->ptpq_to)}} {{$arrStaticData['rentalFrequencyForPTPQ'][$leaseOffer->rental_frequency]}}
                                                        @php 
                                                        $i++;
                                                        @endphp     
                                                    @endforeach
                                                    @php 
                                                        }
                                                    @endphp  
                                                    </td>
                                                    <td><b>Security Deposit: </b></td>
                                                    <td>
                                                    {{(($leaseOffer->security_deposit_type == 1)?'₹ ':'').$leaseOffer->security_deposit.(($leaseOffer->security_deposit_type == 2)?' %':'')}} of {{config('common.deposit_type')[$leaseOffer->security_deposit_of]}}
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                @endif
                                                <tr class="border_bottom">
                                                <td></td>
                                                    <td><b>Processing Fee (%):</b></td>
                                                    <td>{{$leaseOffer->processing_fee}} %</td>
                                                    <td><b>Additional Security: </b></td>
                                                    <td>
                                                        @php
                                                        $add_sec_arr = '';
                                                        if(isset($leaseOffer->addl_security) && $leaseOffer->addl_security !=''){
                                                            $addl_sec_arr = explode(',', $leaseOffer->addl_security);
                                                            foreach($addl_sec_arr as $k=>$v){
                                                                $add_sec_arr .= config('common.addl_security')[$v].', ';
                                                            }
                                                        }
                                                        if($leaseOffer->comment != '' && $leaseOffer->addl_security !=''){
                                                            $add_sec_arr .= ' <b>Comment</b>:  '.$leaseOffer->comment;
                                                        }else{
                                                            $add_sec_arr .= $leaseOffer->comment;
                                                        }
                                                        @endphp 
                                                        {!! trim($add_sec_arr,', ') !!}
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                @if($loop->last)
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                                <!-- End View Leasing Offer Block -->
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($offerStatus != 0 && $isSalesManager == 1)
                    <form method="POST" action="{{route('accept_offer')}}">
                        <div class="row">
                        @csrf
                        <input type="hidden" name="app_id" value="{{request()->get('app_id')}}">
                        <input type="hidden" name="biz_id" value="{{request()->get('biz_id')}}">
                        <div class="col-md-12">
                            <!-- <button class="btn btn-danger btn-sm float-right" type="submit" name="btn_reject_offer">Reject</button> -->
                            <button class="btn btn-success btn-sm float-right" type="submit" name="btn_accept_offer">Accept Offer</button>
                        </div>
                        </div>  
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('jscript')
<script>
$(document).ready(function(){
    $('.card-header').eq(0).removeClass('collapsed');
    $('.card-body').eq(1).addClass('show');
})
</script>
@endsection
