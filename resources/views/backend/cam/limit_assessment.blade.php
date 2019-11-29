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
                                        $adhoc_interest_rate = \Helpers::customIsset($prgmData, 'adhoc_interest_rate');
                                        
                                        $is_grace_period = \Helpers::customIsset($prgmData, 'is_grace_period');
                                        $grace_period = \Helpers::customIsset($prgmData, 'grace_period');
                                        
                                        $interest_borne_by = \Helpers::customIsset($prgmData, 'interest_borne_by');
                                        $disburse_method = \Helpers::customIsset($prgmData, 'disburse_method');
                                        $repayment_method = \Helpers::customIsset($prgmData, 'repayment_method');
                                        
                                        $processing_fee = \Helpers::customIsset($prgmData, 'processing_fee');
                                        $processing_fee_d = $processing_fee ? \Helpers::formatCurreny($processing_fee) : '';                                         
                                        
                                        $check_bounce_fee = \Helpers::customIsset($prgmData, 'check_bounce_fee');
                                        $check_bounce_fee_d = $processing_fee ? \Helpers::formatCurreny($check_bounce_fee) : '';                                          
                                        
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
                                            <td>{{ \Helpers::customIsset($prgmData, 'margin') }}%</td>
                                        </tr>
                                        <tr>
                                            <td><b>Overdue Interest Rate (%) :</b></td>
                                            <td>{{ \Helpers::customIsset($prgmData, 'overdue_interest_rate') }}%</td>
                                        </tr>
                                        <tr>
                                            <td><b>Interest Linkage :</b></td>
                                            <td>{{ \Helpers::customIsset($prgmData, 'interest_linkage') }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Adhoc Facility :</b></td>
                                            <td>{{ config('common.yes_no.'.$is_adhoc_facility) }} 
                                                @if($is_adhoc_facility)
                                                (Max Interest Rate : {{ $adhoc_interest_rate }}%)
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><b>Grace Period :</b></td>
                                            <td>{{ config('common.yes_no.'.$is_grace_period) }} 
                                                @if($is_grace_period)
                                                (Grace Period  : {{ $grace_period }}Days)
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
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label for="txtPassword" class="col-md-4"><b>Apply Loan Amount :</b></label> 
                                                <div class="col-md-8">
                                                    <p><i class="fa fa-inr" aria-hidden="true"></i> 40,00,000</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row INR ">
                                                <label for="txtPassword" class="col-md-4"><b>Loan Offer :</b></label> 
                                                <div class="col-md-8">
                                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                    <input type="text" name="employee" class="form-control" placeholder="Loan Offer " required="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="txtPassword" class="col-md-4"><b>Interest Rate (%) :</b></label> 
                                                <div class="col-md-8">

                                                    <input type="text" name="employee" class="form-control" placeholder="Interest Rate " required="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="txtPassword" class="col-md-4"><b>Tenor (Days) :</b></label> 
                                                <div class="col-md-8">

                                                    <input type="text" name="employee" class="form-control" placeholder="Tenor" required="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="txtPassword" class="col-md-4"><b>Tenor for old invoice (Days) :</b></label> 
                                                <div class="col-md-8">

                                                    <input type="text" name="employee" class="form-control" placeholder="Tenor for old invoice" required="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="txtPassword" class="col-md-4"><b>Margin (%) :</b></label> 
                                                <div class="col-md-8">

                                                    <input type="text" name="employee" class="form-control" placeholder="Margin" required="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="txtPassword" class="col-md-4"><b>Overdue Interest Rate (%) :</b></label> 
                                                <div class="col-md-8">

                                                    <input type="text" name="employee" class="form-control" placeholder="Overdue Interest Rate" required="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="txtPassword" class="col-md-4"><b>Adhoc Interest Rate (%) :</b></label> 
                                                <div class="col-md-8">

                                                    <input type="text" name="employee" class="form-control" placeholder="Adhoc Interest Rate" required="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="txtPassword" class="col-md-4"><b>Grace Period  (Days) :</b></label> 
                                                <div class="col-md-8">

                                                    <input type="text" name="employee" class="form-control" placeholder="Grace Period" required="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  INR">
                                                <label for="txtPassword" class="col-md-4"><b>Processing Fee :</b></label> 
                                                <div class="col-md-8">
                                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                    <input type="text" name="employee" class="form-control" placeholder="Processing Fee" required="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  INR">
                                                <label for="txtPassword" class="col-md-4"><b>Check Bounce Fee :</b></label> 
                                                <div class="col-md-8">
                                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                    <input type="text" name="employee" class="form-control" placeholder="Check Bounce Fee" required="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group row  ">
                                                <label for="txtPassword" class="col-md-4"><b>Comment :</b></label> 
                                                <div class="col-md-8">
                                                    <textarea class="form-control" rows="5" col="5" placeholder="Comment"></textarea>

                                                </div>
                                            </div>
                                        </div>








                                    </div>

                                {!! Form::hidden('app_id', $appId) !!}
                                {!! Form::hidden('biz_id', $bizId) !!}
                                {!!
                                Form::close()
                                !!}
                            </div>
                        </div>














                        <button class="btn btn-success btn-sm float-right  mt-3 ml-3"> Save</button>	
                        <div class="clearfix"></div>
                    </div>

                </div>

            </div>
        </div>

    </div>    
</div>
@endsection
@section('jscript')

@endsection
