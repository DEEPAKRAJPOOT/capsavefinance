@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    <div class="inner-container">
        <div class="card mt-4">
            <div class="card-body">
                <div class="data">

                    <h2 class="sub-title bg mb-4">Limit By Capsave</h2>

                    <div class="pl-4 pr-4 pb-4 pt-2">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="bg">Anchor Program Details</p>
                                <table class="table-striped table">
                                    <tbody><tr>
                                            <td><b>Program Name :</b></td>
                                            <td>{{ \Helpers::customIsset($prgmData, 'prgm_name') }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Industry :</b></td>
                                            <td>{{ \Helpers::customIsset($prgmData, 'industry_name') }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Sub-Industry :</b></td>
                                            <td>{{ \Helpers::customIsset($prgmData, 'sub_industry_name') }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Product Name :</b></td>
                                            <td>{{ \Helpers::customIsset($prgmData, 'product_name') }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>FLGD Applicable :</b></td>
                                            <td>{{ \Helpers::customIsset($prgmData, 'is_fldg_applicable') }}</td>
                                        </tr>

                                        @php
                                        $prgm_id = \Helpers::customIsset($prgmData, 'prgm_id'); 
                                        
                                        $anchor_limit = \Helpers::customIsset($prgmData, 'anchor_limit'); 
                                        $anchor_limit_d = $anchor_limit ? \Helpers::formatCurreny($anchor_limit) : '';
                                        
                                        $min_loan_size = \Helpers::customIsset($prgmData, 'min_loan_size');                                         
                                        $min_loan_size_d = $min_loan_size ? \Helpers::formatCurreny($min_loan_size) : '';
                                        
                                        $max_loan_size = \Helpers::customIsset($prgmData, 'max_loan_size');
                                        $max_loan_size_d = $max_loan_size ? \Helpers::formatCurreny($max_loan_size) : ''; 
                                        
                                        $min_interest_rate = \Helpers::customIsset($prgmData, 'min_interest_rate');
                                        $max_interest_rate = \Helpers::customIsset($prgmData, 'max_interest_rate');
                                        
                                        $min_tenor = \Helpers::customIsset($prgmData, 'min_tenor');
                                        $max_tenor = \Helpers::customIsset($prgmData, 'max_tenor');    
                                        
                                        $min_tenor_old_invoice = \Helpers::customIsset($prgmData, 'min_tenor_old_invoice');
                                        $max_tenor_old_invoice = \Helpers::customIsset($prgmData, 'max_tenor_old_invoice');                                          
                                       
                                        $is_adhoc_facility = \Helpers::customIsset($prgmData, 'is_adhoc_facility');
                                        $prgm_adhoc_interest_rate = \Helpers::customIsset($prgmData, 'adhoc_interest_rate');
                                        
                                        $is_grace_period = \Helpers::customIsset($prgmData, 'is_grace_period');
                                        $prgm_grace_period = \Helpers::customIsset($prgmData, 'grace_period');
                                        
                                        $interest_borne_by = \Helpers::customIsset($prgmData, 'interest_borne_by');
                                        $disburse_method = \Helpers::customIsset($prgmData, 'disburse_method');
                                        $repayment_method = \Helpers::customIsset($prgmData, 'repayment_method');
                                        
                                        $processing_fee = \Helpers::customIsset($prgmData, 'processing_fee');
                                        $processing_fee_d = $processing_fee ? \Helpers::formatCurreny($processing_fee) : '';                                         
                                        
                                        $check_bounce_fee = \Helpers::customIsset($prgmData, 'check_bounce_fee');
                                        $check_bounce_fee_d = $processing_fee ? \Helpers::formatCurreny($check_bounce_fee) : '';
                                        
                                        $prgm_overdue_interest_rate = \Helpers::customIsset($prgmData, 'overdue_interest_rate');
                                        $prgm_margin = \Helpers::customIsset($prgmData, 'margin');
                                        
                                        @endphp
                                        <tr>
                                            <td><b>Anchor Limit :</b></td>                                                                                        
                                            <td>{!! $anchor_limit_d !!}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Loan Size :</b></td>
                                            <td>{!! $min_loan_size_d !!} - {!! $max_loan_size_d !!}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Interest Rate :</b></td>
                                            <td>{{ $min_interest_rate }}% - {{ $max_interest_rate }}%</td>
                                        </tr>

                                        <tr>
                                            <td><b>Tenor(Days) :</b></td>
                                            <td>{{ $min_tenor }}days - {{ $max_tenor }}days</td>
                                        </tr>
                                        <tr>
                                            <td><b>Tenor for old invoice (Days) :</b></td>
                                            <td>{{ $min_tenor_old_invoice }}days - {{ $max_tenor_old_invoice }}days</td>
                                        </tr>
                                        <tr>
                                            <td><b>Margin (%) :</b></td>
                                            <td>{{ $prgm_margin }}%</td>
                                        </tr>
                                        <tr>
                                            <td><b>Overdue Interest Rate (%) :</b></td>
                                            <td>{{ $prgm_overdue_interest_rate }}%</td>
                                        </tr>
                                        <tr>
                                            <td><b>Interest Linkage :</b></td>
                                            <td>{{ \Helpers::customIsset($prgmData, 'interest_linkage') }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Adhoc Facility :</b></td>
                                            <td>{{ config('common.yes_no.'.$is_adhoc_facility) }} 
                                                @if($is_adhoc_facility)
                                                (Max Interest Rate : {{ $prgm_adhoc_interest_rate }}%)
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><b>Grace Period :</b></td>
                                            <td>{{ config('common.yes_no.'.$is_grace_period) }} 
                                                @if($is_grace_period)
                                                (Grace Period  : {{ $prgm_grace_period }}Days)
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><b>Interest Borne By :</b></td>
                                            <td>{{ config('common.interest_borne_by.'.$interest_borne_by) }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Disbursment Method :</b></td>
                                            <td>{{ config('common.disburse_method.'.$disburse_method) }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Repayment Method :</b></td>
                                            <td>{{ config('common.repayment_method.'.$repayment_method) }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Processing Fee  :</b></td>
                                            <td>{!! $processing_fee_d !!}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Check Bounce Fee  :</b></td>
                                            <td>{!! $check_bounce_fee_d !!}</td>
                                        </tr>
                                    </tbody></table>
                            </div>
                            <div class="col-md-6">
                               
                                {!!
                                Form::open(
                                    array(
                                        'route' => 'save_limit_assessment',
                                        'name' => 'frmLimitAssessment',
                                        'autocomplete' => 'off', 
                                        'id' => 'frmLimitAssessment',
                                        'class' => 'cmxform'
                                    )
                                )
                                !!}
                                
                                @php
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
                                $offer_status = \Helpers::customIsset($offerData, 'status');
                                
                                @endphp
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label for="loan_amount" class="col-md-4"><b>Apply Loan Amount :</b></label> 
                                                <div class="col-md-8">
                                                    <p><i class="fa fa-inr" aria-hidden="true"></i> {!! $loanAmount ? \Helpers::formatCurreny($loanAmount) : '' !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row INR ">
                                                <label for="loan_offer" class="col-md-4"><b>Loan Offer :</b></label> 
                                                <div class="col-md-8">
                                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                                                                        
                                                    {!! 
                                                        Form::text(
                                                            'loan_offer', 
                                                            $loan_offer ? $loan_offer : '', 
                                                            [
                                                            'class' => 'form-control', 
                                                            'placeholder' => 'Loan Offer'
                                                            ]
                                                        ) 
                                                    !!}
                                                     
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="interest_rate" class="col-md-4"><b>Interest Rate (%) :</b></label> 
                                                <div class="col-md-8">
                                                                                                        
                                                    {!! 
                                                        Form::text(
                                                            'interest_rate', 
                                                            $interest_rate, 
                                                            [
                                                            'class' => 'form-control', 
                                                            'placeholder' => 'Interest Rate'
                                                            ]
                                                        ) 
                                                    !!}
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="tenor" class="col-md-4"><b>Tenor (Days) :</b></label> 
                                                <div class="col-md-8">
                                                                                                        
                                                    {!! 
                                                        Form::text(
                                                            'tenor', 
                                                            $tenor, 
                                                            [
                                                            'class' => 'form-control', 
                                                            'placeholder' => 'Tenor'
                                                            ]
                                                        ) 
                                                    !!}
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="tenor_old_invoice" class="col-md-4"><b>Tenor for old invoice (Days) :</b></label> 
                                                <div class="col-md-8">                                                    
                                                    
                                                    {!! 
                                                        Form::text(
                                                            'tenor_old_invoice', 
                                                            $tenor_old_invoice, 
                                                            [
                                                            'class' => 'form-control', 
                                                            'placeholder' => 'Tenor for old invoice'
                                                            ]
                                                        ) 
                                                    !!}
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="margin" class="col-md-4"><b>Margin (%) :</b></label> 
                                                <div class="col-md-8">
                                                                                                        
                                                    {!!
                                                        Form::text(
                                                            'margin', 
                                                            $margin, 
                                                            [
                                                            'class' => 'form-control', 
                                                            'placeholder' => 'Margin'
                                                            ]
                                                        ) 
                                                    !!}
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="overdue_interest_rate" class="col-md-4"><b>Overdue Interest Rate (%) :</b></label> 
                                                <div class="col-md-8">
                                                                                                        
                                                    {!!
                                                        Form::text(
                                                            'overdue_interest_rate', 
                                                            $overdue_interest_rate, 
                                                            [
                                                            'class' => 'form-control', 
                                                            'placeholder' => 'Overdue Interest Rate'
                                                            ]
                                                        ) 
                                                    !!}
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        @if ($is_adhoc_facility)
                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="adhoc_interest_rate" class="col-md-4"><b>Adhoc Interest Rate (%) :</b></label> 
                                                <div class="col-md-8">
                                                                                                        
                                                    {!!
                                                        Form::text(
                                                            'adhoc_interest_rate', 
                                                            $adhoc_interest_rate, 
                                                            [
                                                            'class' => 'form-control', 
                                                            'placeholder' => 'Overdue Interest Rate'
                                                            ]
                                                        ) 
                                                    !!}
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if ($is_grace_period)
                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="grace_period" class="col-md-4"><b>Grace Period  (Days) :</b></label> 
                                                <div class="col-md-8">                                                                                                        
                                                    {!!
                                                        Form::text(
                                                            'grace_period', 
                                                            $grace_period, 
                                                            [
                                                            'class' => 'form-control', 
                                                            'placeholder' => 'Grace Period'
                                                            ]
                                                        ) 
                                                    !!}
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-md-12">
                                            <div class="form-group row  INR">
                                                <label for="txtPassword" class="col-md-4"><b>Processing Fee :</b></label> 
                                                <div class="col-md-8">
                                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                    
                                                    {!!
                                                        Form::text(
                                                            'processing_fee', 
                                                            $processing_fee, 
                                                            [
                                                            'class' => 'form-control', 
                                                            'placeholder' => 'Processing Fee'
                                                            ]
                                                        ) 
                                                    !!}                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  INR">
                                                <label for="txtPassword" class="col-md-4"><b>Check Bounce Fee :</b></label> 
                                                <div class="col-md-8">
                                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>                                                                                                        
                                                    {!!
                                                        Form::text(
                                                            'check_bounce_fee', 
                                                            $check_bounce_fee, 
                                                            [
                                                            'class' => 'form-control', 
                                                            'placeholder' => 'Check Bounce Fee'
                                                            ]
                                                        ) 
                                                    !!}                                                     
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="comment" class="col-md-4"><b>Comment :</b></label> 
                                                <div class="col-md-8">                                                    

                                                    {!! 
                                                        Form::textarea(
                                                            'comment', 
                                                            $comment,
                                                            [
                                                            'class' => 'form-control', 
                                                            'rows' => 5, 
                                                            'col' => 5
                                                            ]
                                                        ) 
                                                    !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                {!! Form::hidden('app_id', $appId) !!}
                                {!! Form::hidden('biz_id', $bizId) !!}
                                {!! Form::hidden('offer_id', $offerId) !!}
                                {!! Form::hidden('prgm_id', $prgm_id) !!}
                                {!! Form::hidden('loan_amount', $loanAmount) !!}
                                
                                @php  
                                if ($offer_status == 1) {                                    
                                    $btnAttr = ['disabled'=>'disabled'];
                                    $noteStr = 'Offer has been already accepted by user.';
                                    $btnLabel = 'Save';
                                } else if ($offer_status == 2) {                                    
                                    $btnAttr = [];
                                    $noteStr = 'Offer has been rejected by user.';
                                    $btnLabel = 'Save';
                                } else {
                                    $btnAttr = [];
                                    $noteStr = '';
                                    if (isset($offerData->offer_id)) {
                                        $btnLabel = 'Approve';
                                    } else {
                                        $btnLabel = 'Save & Approve';
                                    }
                                }
                                @endphp
                                @if(request()->get('view_only') || $currStageCode == 'approver')
                                {!! 
                                    Form::submit(
                                        $btnLabel, 
                                        [
                                            'name'=>'btn_save_offer', 
                                            'class' => 'btn btn-success btn-sm float-right  mt-3 ml-3'
                                        ] + $btnAttr
                                    )
                                !!}
                                @endif
                                <small class="float-right  mt-3 ml-3">{{ $noteStr }}</small>
                                
                                {!!
                                Form::close()
                                !!}
                            </div>
                        </div>


                        <div class="clearfix"></div>
                    </div>

                </div>

            </div>
        </div>

    </div>    
</div>
@endsection
@section('jscript')
<script>
var messages = {
    min_loan_offer : "{{ $min_loan_size > $loanAmount ? $loanAmount : $min_loan_size }}",
    max_loan_offer : "{{ $max_loan_size }}",
    min_interest_rate : "{{ $min_interest_rate }}",
    max_interest_rate : "{{ $max_interest_rate }}",
    min_tenor : "{{ $min_tenor }}",
    max_tenor : "{{ $max_tenor }}",
    min_tenor_old_invoice : "{{ $min_tenor_old_invoice }}",
    max_tenor_old_invoice : "{{ $max_tenor_old_invoice }}",     
    min_overdue_interest_rate : "0",
    max_overdue_interest_rate : "{{ $prgm_overdue_interest_rate }}",     
    min_margin : "0",
    max_margin : "{{ $prgm_margin }}",
    required_adhoc_interest_rate : "{{ $is_adhoc_facility }}",
    min_adhoc_interest_rate : "0",
    max_adhoc_interest_rate : "{{ $prgm_adhoc_interest_rate }}",    
    required_grace_period : "{{ $is_grace_period }}",
    min_grace_period : "0",
    max_grace_period : "{{ $prgm_grace_period }}",
};
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/assets/js/application.js') }}" type="text/javascript"></script>
@endsection
