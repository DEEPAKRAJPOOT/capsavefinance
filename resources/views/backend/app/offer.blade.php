@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')

@section('additional_css')
<style>
.card-title {
    font-size: 0.9rem;
    line-height: 1.375rem;
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
                                                    <td>{{$key+1}}</td>
                                                    <td><b>Apply Loan Amount: </b> </td>
                                                    <td>{{$supplyOffer->prgm_limit_amt}}</td>
                                                    <td><b>Check Bounce Fee: </b></td>
                                                    <td>{{$supplyOffer->check_bounce_fee}}</td>
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
                                                    <td><b>Processing Fee: </b></td>
                                                    <td>{{$supplyOffer->processing_fee}}</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                <td></td>
                                                    <td><b>Comment: </b></td>
                                                    <td colspan="3">{{$supplyOffer->comment}}</td>
                                                    <td></td>
                                                </tr>
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
                                                    <td>{{$key+1}}</td>
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
                                                        $addl_sec_arr = explode(',', $termOffer->addl_security);
                                                        foreach($addl_sec_arr as $k=>$v){
                                                            if($v == 4){
                                                                $add_sec_arr .= ', '.config('common.addl_security')[$v];
                                                                $add_sec_arr .= ' - <b>Comment</b>:  '.$termOffer->comment;
                                                            }else{
                                                                $add_sec_arr .= ', '.config('common.addl_security')[$v];
                                                            }
                                                        }
                                                        @endphp 
                                                        {!! trim($add_sec_arr, ', ') !!}
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
                                                   <th width="10%">Sr. No.</th>
                                                   <th width="70%" colspan="4">Offer Details</th>
                                                   <th width="20%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @endif
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td><b>Facility Type: </b></td>
                                                    <td>Lease Loan</td>
                                                    <td><b>Apply Loan Amount: </b> </td>
                                                    <td>{{$leaseOffer->prgm_limit_amt}}</td>
                                                    <td><b>Status: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b> <label class="badge {{($leaseOffer->status == 1)? 'badge-success': 'badge-warning'}} current-status">{{($leaseOffer->status == 1)? 'Accepted': 'Pending'}}</label></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td></td>
                                                    <td><b>Tenor (Months): </b></td>
                                                    <td>{{$leaseOffer->tenor}}</td>
                                                    <td><b>Equipment Type: </b></td>
                                                    <td>{{\Helpers::getEquipmentTypeById($leaseOffer->equipment_type_id)->equipment_name}}</td>
                                                    <td><b>Created By: &nbsp;&nbsp;&nbsp;</b>{{\Helpers::getUserName($leaseOffer->created_by)}}</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td><b>Security Deposit: </b></td>
                                                    <td>{{$leaseOffer->security_deposit}}</td>
                                                    <td><b>Rental Frequency: </b></td>
                                                    <td>{{(($leaseOffer->rental_frequency == 1)?'Yearly':(($leaseOffer->rental_frequency == 2)? 'Bi-Yearly':(($leaseOffer->rental_frequency == 3)? 'Quaterly': 'Monthly')))}}</td>
                                                    <td><b>Created At: &nbsp;&nbsp;&nbsp;</b>{{\Carbon\Carbon::parse($leaseOffer->created_at)->format('d-m-Y')}}</td>
                                                </tr>
                                                <tr>
                                                <td></td>
                                                    <td><b>PTP Frequency: </b></td>
                                                    <td>
                                                        @if(isset($leaseOffer->offerPTPQ))   
                                                            @foreach ($leaseOffer->offerPTPQ as $ok => $ov)
                                                               {!!isset($ov->ptpq_from) ? '<b>From Period:</b> '.$ov->ptpq_from : ''!!}
                                                               {!!isset($ov->ptpq_to) ? '<b>&nbsp;&nbsp;&nbsp;To Period:</b> '.$ov->ptpq_to : ''!!}
                                                               {!!isset($ov->ptpq_rate) ? '<b>&nbsp;&nbsp;&nbsp;Rate:</b> '.$ov->ptpq_rate : ''!!}
                                                               <br/>
                                                            @endforeach 
                                                         @endif
                                                    </td>
                                                    <td><b>XIRR (%): </b></td>
                                                    <td>Ruby Sheet : {{$leaseOffer->ruby_sheet_xirr}}<br/>Cash Flow :{{$leaseOffer->cash_flow_xirr}}</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                <td></td>
                                                    <td><b>Additional Security: </b></td>
                                                    <td>
                                                        @php
                                                        $add_sec_arr = '';
                                                        $addl_sec_arr = explode(',', $leaseOffer->addl_security);
                                                        foreach($addl_sec_arr as $k=>$v){
                                                            if($v == 4){
                                                                $add_sec_arr .= ', '.config('common.addl_security')[$v];
                                                                $add_sec_arr .= ' - <b>Comment</b>:  '.$leaseOffer->comment;
                                                            }else{
                                                                $add_sec_arr .= ', '.config('common.addl_security')[$v];
                                                            }
                                                        }
                                                        @endphp 
                                                        {!! trim($add_sec_arr, ', ') !!}
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
