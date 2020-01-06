@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    <div class="row grid-margin mt-3 mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class=" form-fields">
                        <h5 class="card-title form-head mt-0">Offer</h5>
                    </div>
                    <div class="row">
                        @forelse($offerData as $key=>$offer)
                        <div class="col-md-6" style="margin-bottom: 50px;">
                            <table class="table-striped table">
                                <tbody>
                                    <tr>
                                        <td><b>Apply Loan Amount :</b></td>
                                        <td>{{ $offer->prgm_limit_amt }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Loan Offer :</b></td>
                                        <td>{{ $offer->loan_offer }}</td>
                                    </tr>

                                    <tr>
                                        <td><b>Interest Rate (%) :</b></td>
                                        <td>{{ $offer->interest_rate }}</td>
                                    </tr>

                                    <tr>
                                        <td><b>Tenor (Days) :</b></td>
                                        <td>{{ $offer->tenor }}</td>
                                    </tr>

                                    <tr>
                                        <td><b>Tenor for old invoice (Days) :</b></td>
                                        <td>{{ $offer->tenor_old_invoice }}</td>
                                    </tr> 

                                    <tr>
                                        <td><b>Margin (%) :</b></td>
                                        <td>{{ $offer->margin }}</td>
                                    </tr>

                                    <tr>
                                        <td><b>Overdue Interest Rate (%) :</b></td>
                                        <td>{{ $offer->overdue_interest_rate }}</td>
                                    </tr>

                                    <tr>
                                        <td><b>Adhoc Interest Rate (%) :</b></td>
                                        <td>{{ $offer->adhoc_interest_rate }}</td>
                                    </tr> 

                                    <tr>
                                        <td><b>Grace Period  (Days) :</b></td>
                                        <td>{{ $offer->grace_period }}</td>
                                    </tr>

                                    <tr>
                                        <td><b>Processing Fee :</b></td>
                                        <td>{{ $offer->processing_fee }}</td>
                                    </tr>  

                                    <tr>
                                        <td><b>Check Bounce Fee :</b></td>
                                        <td>{{ $offer->check_bounce_fee }}</td>
                                    </tr>

                                    <tr>
                                        <td><b>Comment :</b></td>
                                        <td>{{ $offer->comment }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @empty
                        <p>No offers found</p>
                        @endforelse
                    </div>
                    <form method="POST" action="{{route('accept_offer')}}">
                        <div class="row">
                        @csrf
                        <input type="hidden" name="app_id" value="{{request()->get('app_id')}}">
                        <input type="hidden" name="biz_id" value="{{request()->get('biz_id')}}">
                        <div class="col-md-12">
                            <!-- <button class="btn btn-danger btn-sm float-right" type="submit" name="btn_reject_offer">Reject</button> -->
                            @if($offerData->count() && $offerData[0]->status == 0)
                            <button class="btn btn-success btn-sm float-right" type="submit" name="btn_accept_offer">Accept</button>
                            @endif
                        </div>
                        </div>  
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

