@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    <div class="row grid-margin mt-3 mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class=" form-fields">
                        <div class="col-md-12">
                            <h5 class="card-title form-head-h5">Sanction Letter
                            <a data-toggle="modal" data-target="#uploadSanctionLetter" data-height="200px" data-width="100%" data-placement="top" href="#" data-url="{{ route('show_upload_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId, 'upload'=>1 ]) }}" class="btn btn-success btn-sm float-right mt-3 ml-3">Upload</a>    
                            <a href="{{ route('download_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId, 'download'=>1 ]) }}" class="btn btn-success btn-sm float-right mt-3 ml-3">Download</a>                            
                            </h5>
                            <div class="col-md-12">

                                @php
                                $loanAmount = \Helpers::customIsset($offerData, 'loan_amount');
                                $loan_offer = \Helpers::customIsset($offerData, 'loan_offer');
                                $interest_rate = \Helpers::customIsset($offerData, 'interest_rate');
                                $tenor = \Helpers::customIsset($offerData, 'tenor');
                                $tenor_old_invoice = \Helpers::customIsset($offerData, 'tenor_old_invoice');
                                $margin = \Helpers::customIsset($offerData, 'margin');
                                $overdue_interest_rate = \Helpers::customIsset($offerData, 'overdue_interest_rate');
                                $adhoc_interest_rate = \Helpers::customIsset($offerData, 'adhoc_interest_rate');                                
                                $grace_period = \Helpers::customIsset($offerData, 'grace_period');
                                $processing_fee = \Helpers::customIsset($offerData, 'processing_fee');
                                $check_bounce_fee = \Helpers::customIsset($offerData, 'check_bounce_fee');
                                $comment = \Helpers::customIsset($offerData, 'comment');
                                @endphp


                                <table class="table-striped table">
                                    <tbody><tr>
                                            <td><b>Apply Loan Amount :</b></td>
                                            <td>{!! $loanAmount ? \Helpers::formatCurreny($loanAmount) : '' !!}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Loan Offer :</b></td>
                                            <td>{{ $loan_offer }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Interest Rate (%) :</b></td>
                                            <td>{{ $interest_rate }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Tenor (Days) :</b></td>
                                            <td>{{ $tenor }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Tenor for old invoice (Days) :</b></td>
                                            <td>{{ $tenor_old_invoice }}</td>
                                        </tr> 

                                        <tr>
                                            <td><b>Margin (%) :</b></td>
                                            <td>{{ $margin }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Overdue Interest Rate (%) :</b></td>
                                            <td>{{ $overdue_interest_rate }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Adhoc Interest Rate (%) :</b></td>
                                            <td>{{ $adhoc_interest_rate }}</td>
                                        </tr> 

                                        <tr>
                                            <td><b>Grace Period  (Days) :</b></td>
                                            <td>{{ $grace_period }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Processing Fee :</b></td>
                                            <td>{{ $processing_fee }}</td>
                                        </tr>  

                                        <tr>
                                            <td><b>Check Bounce Fee :</b></td>
                                            <td>{{ $check_bounce_fee }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Comment :</b></td>
                                            <td>{{ $comment }}</td>
                                        </tr>                                        

                                    </tbody>
                                </table>

                            </div>
                        </div>	
                    </div>	 
                </div>
            </div>
        </div>
    </div>
</div>

{!!Helpers::makeIframePopup('uploadSanctionLetter','Upload Sanction Letter', 'modal-md')!!}
@endsection