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
                    @if($supplyOfferData->count() == 0)
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
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($offerStatus != 0 && ($colenderShare && $colenderShare->co_lender_status == 0))
                    <form method="POST" action="{{route('accept_offer_by_colender')}}">
                        <div class="row">
                        @csrf
                        <input type="hidden" name="app_id" value="{{request()->get('app_id')}}">
                        <input type="hidden" name="biz_id" value="{{request()->get('biz_id')}}">
                        <input type="hidden" name="co_lenders_share_id" value="{{$colenderShare->co_lenders_share_id}}">
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
