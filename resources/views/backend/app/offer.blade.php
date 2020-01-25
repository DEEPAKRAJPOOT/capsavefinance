@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    <div class="row grid-margin mt-3 mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    @if($isAccessible)
                    @if($supplyOfferData->count() == 0 && $termOfferData->count() == 0 && $termOfferData->count() == 0 )
                    <div class="row"><h3>No offer found .</h3></div>
                    @endif
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="accordion" class="accordion">
                                <!-- Start View Supply Chain Offer Block -->
                                @foreach($supplyOfferData as $key=>$supplyOffer)
                                @if($loop->first)
                                <div class="card card-color mb-0">
                                    <div class="card-header" data-toggle="collapse" href="#collapseOne" aria-expanded="false"><h5 class="mb-0">Supply Chain Offer Details</h5>     
                                    </div>
                                    <div id="collapseOne" class="card-body bdr p-0 show" data-parent="#accordion" style="">
                                        <table cellspacing="0" cellpadding="0" width="100%" class="table-i table-offer">
                                            <thead>
                                                <tr role="row">
                                                   <th width="10%">Sr. No.</th>
                                                   <th width="40%">Offer Details</th>
                                                   <th width="25%">Created By</th>
                                                   <th width="15%">Status</th>
                                                   <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @endif
                                                <tr role="row" class="odd">
                                                    <td width="10%">{{$key+1}}</td>
                                                    <td width="40%">
                                                        <table class="" width="70%">
                                                            <tbody>
                                                                <tr>
                                                                    <td><b>Apply Loan Amount : </b></td>
                                                                    <td>{{$supplyOffer->prgm_limit_amt}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Loan Offer : </b></td>
                                                                    <td>{{$supplyOffer->prgm_limit_amt}}</td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Interest Rate(%) : </b></td>
                                                                   <td>{{$supplyOffer->interest_rate}}</td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Tenor (Days) : </b></td>
                                                                   <td>{{$supplyOffer->tenor}}</td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Tenor for old invoice (Days): </b></td>
                                                                   <td>{{$supplyOffer->tenor_old_invoice}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Margin (%): </b></td>
                                                                    <td>{{$supplyOffer->margin}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Overdue Interest Rate (%): </b></td>
                                                                    <td>{{$supplyOffer->overdue_interest_rate}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Adhoc Interest Rate (%): </b></td>
                                                                    <td>{{$supplyOffer->adhoc_interest_rate}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Grace Period (Days): </b></td>
                                                                    <td>{{$supplyOffer->grace_period}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Processing Fee: </b></td>
                                                                    <td>{{$supplyOffer->processing_fee}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Check Bounce Fee: </b></td>
                                                                    <td>{{$supplyOffer->check_bounce_fee}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Comment: </b></td>
                                                                    <td>{{$supplyOffer->comment}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td width="25%">{{$supplyOffer->created_by}}</td>
                                                    <td width="15%"><label class="badge badge-success current-status">Approved</label></td>
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
                                    <div class="card-header collapsed" data-toggle="collapse" href="#collapseTwo" aria-expanded="false"><h5 class="mb-0">Term Loan Offer Details</h5>     
                                    </div>
                                    <div id="collapseTwo" class="card-body bdr p-0 collapse" data-parent="#accordion" style="">
                                        <table cellspacing="0" cellpadding="0" width="100%" class="table-i table-offer">
                                            <thead>
                                                <tr role="row">
                                                   <th width="10%">Sr. No.</th>
                                                   <th width="40%">Offer Details</th>
                                                   <th width="25%">Created By</th>
                                                   <th width="15%">Status</th>
                                                   <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @endif
                                                <tr role="row" class="odd">
                                                    <td width="10%">{{$key+1}}</td>
                                                    <td width="40%">
                                                        <table class="" width="70%">
                                                            <tbody>
                                                                <tr>
                                                                    <td><b>Apply Loan Amount : </b></td>
                                                                    <td>{{$termOffer->prgm_limit_amt}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Loan Offer : </b></td>
                                                                    <td>{{$termOffer->prgm_limit_amt}}</td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Interest Rate(%) : </b></td>
                                                                   <td>{{$termOffer->interest_rate}}</td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Tenor (Days) : </b></td>
                                                                   <td>{{$termOffer->tenor}}</td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Tenor for old invoice (Days): </b></td>
                                                                   <td>{{$termOffer->tenor_old_invoice}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Margin (%): </b></td>
                                                                    <td>{{$termOffer->margin}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Overdue Interest Rate (%): </b></td>
                                                                    <td>{{$termOffer->overdue_interest_rate}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Adhoc Interest Rate (%): </b></td>
                                                                    <td>{{$termOffer->adhoc_interest_rate}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Grace Period (Days): </b></td>
                                                                    <td>{{$termOffer->grace_period}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Processing Fee: </b></td>
                                                                    <td>{{$termOffer->processing_fee}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Check Bounce Fee: </b></td>
                                                                    <td>{{$termOffer->check_bounce_fee}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Comment: </b></td>
                                                                    <td>{{$termOffer->comment}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td width="25%">{{$termOffer->created_by}}</td>
                                                    <td width="15%"><label class="badge badge-success current-status">Approved</label></td>
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
                                    <div class="card-header collapsed" data-toggle="collapse" href="#collapseThree" aria-expanded="false"><h5 class="mb-0">Leasing Offer Details</h5>     
                                    </div>
                                    <div id="collapseThree" class="card-body bdr p-0 collapse" data-parent="#accordion" style="">
                                        <table cellspacing="0" cellpadding="0" width="100%" class="table-i table-offer">
                                            <thead>
                                                <tr role="row">
                                                   <th width="10%">Sr. No.</th>
                                                   <th width="40%">Offer Details</th>
                                                   <th width="25%">Created By</th>
                                                   <th width="15%">Status</th>
                                                   <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @endif
                                                <tr role="row" class="odd">
                                                    <td width="10%">{{$key+1}}</td>
                                                    <td width="40%">
                                                        <table class="" width="70%">
                                                            <tbody>
                                                                <tr>
                                                                    <td><b>Apply Loan Amount : </b></td>
                                                                    <td>{{$leaseOffer->prgm_limit_amt}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Loan Offer : </b></td>
                                                                    <td>{{$leaseOffer->prgm_limit_amt}}</td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Interest Rate(%) : </b></td>
                                                                   <td>{{$leaseOffer->interest_rate}}</td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Tenor (Days) : </b></td>
                                                                   <td>{{$leaseOffer->tenor}}</td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Tenor for old invoice (Days): </b></td>
                                                                   <td>{{$leaseOffer->tenor_old_invoice}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Margin (%): </b></td>
                                                                    <td>{{$leaseOffer->margin}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Overdue Interest Rate (%): </b></td>
                                                                    <td>{{$leaseOffer->overdue_interest_rate}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Adhoc Interest Rate (%): </b></td>
                                                                    <td>{{$leaseOffer->adhoc_interest_rate}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Grace Period (Days): </b></td>
                                                                    <td>{{$leaseOffer->grace_period}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Processing Fee: </b></td>
                                                                    <td>{{$leaseOffer->processing_fee}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Check Bounce Fee: </b></td>
                                                                    <td>{{$leaseOffer->check_bounce_fee}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Comment: </b></td>
                                                                    <td>{{$leaseOffer->comment}}</td>
                                                                </tr>
                                                                @if($loop->last)
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td width="25%">{{$leaseOffer->created_by}}</td>
                                                    <td width="15%"><label class="badge badge-success current-status">Approved</label></td>
                                                    <td></td>
                                                </tr>
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
                    <form method="POST" action="{{route('accept_offer')}}">
                        <div class="row">
                        @csrf
                        <input type="hidden" name="app_id" value="{{request()->get('app_id')}}">
                        <input type="hidden" name="biz_id" value="{{request()->get('biz_id')}}">
                        <div class="col-md-12">
                            @if($offerStatus != 0)
                            <!-- <button class="btn btn-danger btn-sm float-right" type="submit" name="btn_reject_offer">Reject</button> -->
                            <button class="btn btn-success btn-sm float-right" type="submit" name="btn_accept_offer">Accept</button>
                            @endif
                        </div>
                        </div>  
                    </form>
                    @else
                    <div class="row"><h3>You are not authorised</h3></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

