@extends('layouts.backend.admin-layout')
@section('content')
@php
$dis_element = $copied_prgm_id ? ['readonly' => true] : [];
$actionUrl = $action != 'view' ? route('save_sub_program') : '#';
$defaultSubProgramLimit = ($programData->anchor_limit && $anchorData->is_fungible) ? number_format($programData->anchor_limit ) : NULL;
$defaultSubProgramLimitReadOnly = ($anchorData->is_fungible) ? 'readonly' : '';
$defaultMinimumLoanSize = ($anchorData->is_fungible) ? 1 : NULL;
@endphp
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa fa-clipboard"></i>
        </div>
        <div class="header-title">
            <h3>
                {{ trans('backend.mange_program.add_sub_program') }}
            </h3>
            <ol class="breadcrumb">
                <li style="color:#374767;">  {{ trans('backend.mange_program.home') }} </li>
                <li style="color:#374767;"> <a href='{{ $redirectUrl }}'>  {{ trans('backend.mange_program.manage_program') }} </a></li>
                <li style="color:#374767;"> <a href='{{  route('manage_sub_program', ['anchor_id' => $anchor_id, 'program_id' => \Session::get('list_program_id')]) }}'>  {{ trans('backend.mange_program.manage_sub_program') }} </a></li>
                <li class="active"> {{ trans('backend.mange_program.add_sub_program') }}</li>
            </ol>
        </div>
    </section>
    <div class="row grid-margin mt-3">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-fields">
                        <div class="active" id="details">
                            <div class="form-sections">
                                <div class="documents-detail inner-subform" id="terms">
                                    <div class="form-sections parent_div">
                                        <div class=" ">
                                            <div class="sub-progrem">
                                                <div class="row">
                                                    <div class="col-sm-12 d-flex">
                                                        <div>
                                                            <h6 class="gc"><label>Anchor Name: </label> {{ isset($anchorData) ? $anchorData->f_name : null }} </h6>
                                                            <h6 class="gc"><label>Program Name: </label> {{ isset($programData) ? $programData->prgm_name : null }} </h6>
                                                        </div>
                                                        <p class="ml-auto">
                                                            <b>Total Anchor Limit : </b>
                                                            <i class="fa fa-inr" aria-hidden="true"></i>
                                                            <span id="total-anchor-limit" class="number_format">{!! isset($programData->anchor_limit) ?  number_format($programData->anchor_limit )   : null !!}</span>
                                                        </p>
                                                        &nbsp;&nbsp;&nbsp;
                                                        @if ($action == 'view')
                                                        <p class="float-right mb-0">
                                                            @if(isset($anchorData) && isset($anchorData->is_fungible) && !$anchorData->is_fungible)
                                                                <b>Remaining Anchor Limit : </b>
                                                                <i class="fa fa-inr" aria-hidden="true"></i>
                                                                <span id="remaining-anchor-limit" class="number_format">{{ isset($programData->anchor_limit) ?  number_format($programData->anchor_limit - $anchorUtilizedBalance)  : null }}</span>
                                                                <br>
                                                                <b>Utilized Limit in Offer : </b>
                                                                <i class="fa fa-inr" aria-hidden="true"></i>
                                                                <span>{{ isset($utilizedLimit) ?  number_format($utilizedLimit)  : null }}</span>
                                                                <br>
                                                            @endif
                                                        </p>
                                                        @else
                                                        <p class="float-right mb-0">
                                                            @if(isset($anchorData) && isset($anchorData->is_fungible) && !$anchorData->is_fungible)
                                                                <b>Remaining Anchor Limit : </b>
                                                                <i class="fa fa-inr" aria-hidden="true"></i>
                                                                <span id="remaining-anchor-limit" class="number_format">{{ isset($remaningAmount) ?  number_format($remaningAmount)  : null }}</span>
                                                                <br>
                                                                <b>Utilized Limit in Offer : </b>
                                                                <i class="fa fa-inr" aria-hidden="true"></i>
                                                                <span>{{ isset($utilizedLimit) ?  number_format($utilizedLimit)  : null }}</span>                                                                
                                                                <br>
                                                            @endif
                                                        </p>
                                                        @endif
                                                    </div>
                                                    <!--                                                    <div class="col-sm-3 text-right">
                                                       <a class="edit-btn" href="{{route('add_program',['program_id'=> $program_id ,'anchor_id'=>$anchor_id ])}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                                                       </div>-->
                                                </div>
                                            </div>
                                            </br>


                                            {{ Form::open(['url'=>$actionUrl,'id'=>'add_sub_program']) }}
                                            {!! Form::hidden('parent_prgm_id',$program_id) !!}
                                            {!! Form::hidden('program_id',isset($subProgramData->prgm_id) ? $subProgramData->prgm_id : null) !!}
                                            {!! Form::hidden('product_id',isset($programData) ? $programData->product_id : null) !!}
                                            {!! Form::hidden('anchor_limit_re',isset($remaningAmount) ?  number_format($remaningAmount)  : null,['id'=>'anchor_limit_re'])   !!}
                                            {!! Form::hidden('anchor_id',$anchor_id) !!}
                                            {!! Form::hidden('anchor_user_id',isset($programData->anchor_user_id) ?$programData->anchor_user_id  : null ) !!}
                                            {!! Form::hidden('copied_prgm_id', $copied_prgm_id) !!}
                                            {!! Form::hidden('utilized_amount', $utilizedLimit, ['id'=>'utilized_amount']) !!}
                                            {!! Form::hidden('total_anchor_sub_limit', $anchorSubLimitTotal, ['id'=>'total_anchor_sub_limit']) !!}
                                            {!! Form::hidden('old_anchor_limit', $pAnchorLimit, ['id'=>'old_anchor_limit']) !!}
                                            {!! Form::hidden('old_anchor_sub_limit', $pAnchorSubLimit, ['id'=>'old_anchor_sub_limit']) !!}
                                            {!! Form::hidden('is_reject', 0, ['id'=>'is_reject']) !!}
                                            {!! Form::hidden('reason_type', $reason_type, ['id'=>'reason_type']) !!}


                                            <div class="sub-form renew-form " id="subform">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group INR">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label for="txtCreditPeriod">Total Anchor Limit <span class="error_message_label">*</span> </label>
                                                                            <div class="relative">
                                                                            <a href="javascript:void(0);" class="remaining"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                                            {!! Form::text('anchor_limit',
                                                                            isset($programData->anchor_limit) ?  number_format($programData->anchor_limit )   : null,
                                                                            ['class'=>'form-control number_format ', 'id' => 'anchor_limit'])   !!}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="txtCreditPeriod">
                                                                        {{ trans('backend.add_program.sub_program_detail') }}
                                                                        <span class="error_message_label">*</span>
                                                                    </label>
                                                                    <div class="" style="color:black;">
                                                                            <div class="form-check-inline">
                                                                                <label class="form-check-label fnt" for="prgm_type">
                                                                                    {!! Form::radio('prgm_type','1',($programData->prgm_type=="1")? "checked" : "", ['class'=>'form-check-input'] + $dis_element) !!}
                                                                                    <strong>
                                                                                        {{ trans('backend.add_program.vendor_finance') }}
                                                                                    </strong>
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check-inline">
                                                                                <label class="form-check-label fnt" for="prgm_type">
                                                                                    {!! Form::radio('prgm_type','2',($programData->prgm_type=="2")? "checked" : "", ['class'=>'form-check-input'] + $dis_element) !!}
                                                                                    <strong>
                                                                                        {{ trans('backend.add_program.channel_finance') }}
                                                                                    </strong>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    <label id="prgm_type-error" class="error mb-0" for="prgm_type"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <h5 class="card-title mt-0">Terms</h5>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group INR">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label for="txtCreditPeriod">Product line<span class="error_message_label">*</span> </label>
                                                                    {!! Form::text('product_name',
                                                                    isset($subProgramData->product_name) ? $subProgramData->product_name : null,
                                                                    ['class'=>'form-control']+$dis_element)   !!}

                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="txtCreditPeriod">Limit <span class="error_message_label">*</span> </label>
                                                                    <div class="relative">
                                                                    <a href="javascript:void(0);" class="remaining"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                                    {!! Form::text('anchor_sub_limit',
                                                                    isset($subProgramData->anchor_sub_limit) ? number_format($subProgramData->anchor_sub_limit) : $defaultSubProgramLimit,
                                                                    ['id' => 'anchor_sub_limit','class'=>'form-control number_format '])   !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12" style="display: {{$anchorData->is_fungible ? 'block' : 'block'}}">
                                                        <div class="form-group INR">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label for="txtCreditPeriod">Min Loan Size<span class="error_message_label">*</span> </label>
                                                                    <div class="relative">
                                                                    <a href="javascript:void(0);" class="remaining">
                                                                        <i class="fa fa-inr" aria-hidden="true"></i></a>
                                                                    {!! Form::text('min_loan_size',
                                                                    isset($subProgramData->min_loan_size) ?  number_format($subProgramData->min_loan_size) : $defaultMinimumLoanSize,
                                                                    ['class'=>'form-control number_format ','placeholder'=>'Min'])   !!}
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="txtCreditPeriod">Max Loan Size<span class="error_message_label">*</span> </label>
                                                                    <div class="relative">
                                                                    <a href="javascript:void(0);" class="remaining"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                                    {!! Form::text('max_loan_size',
                                                                    isset($subProgramData->max_loan_size) ?  number_format($subProgramData->max_loan_size) : $defaultSubProgramLimit,
                                                                    ['class'=>'form-control max_loan_size number_format','placeholder'=>'Max'])!!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label for="txtCreditPeriod">Interest Rate (%)
                                                                        <span class="error_message_label">*</span>
                                                                    </label>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-check-inline">
                                                                                <label class="form-check-label fnt">

                                                                                    {!! Form::radio('interest_rate','1',
                                                                                    isset($subProgramData->interest_rate) && ($subProgramData->interest_rate=="1") ? "checked" : "",
                                                                                    ['class'=>'form-check-input int-checkbox '])    !!}
                                                                                    Fixed
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check-inline">
                                                                                <label class="form-check-label fnt">
                                                                                    {!! Form::radio('interest_rate','2',
                                                                                    isset($subProgramData->interest_rate) && ($subProgramData->interest_rate=="2") ? "checked" : "",
                                                                                    ['class'=>'form-check-input int-checkbox']) !!}
                                                                                    Floating
                                                                                </label>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6 floating" style="display:none; margin-top: -30px;">
                                                                            <label for="interest_linkage" >Base Rate(%)
                                                                                <span class="error_message_label hide"></span>
                                                                            </label>
                                                                            <select id="interest_linkage" class="form-control" name="interest_linkage" tabindex="9">
                                                                                <option value="">Select Base Rate</option>
                                                                                @foreach($baserate_list as $key=>$baserate)
                                                                                <option @if(isset($subProgramData->base_rate_id) && $baserate->id == $subProgramData->base_rate_id) selected @endif value="{{$baserate->id}}">{{$baserate->base_rate}}%&nbsp;&nbsp;({{$baserate->bank->bank_name}})</option>
                                                                                @endforeach
                                                                            </select>
                                                                            {!! $errors->first('interest_linkage', '<span class="error">:message</span>') !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row fixed" style="display:none;">
                                                                        <div class="col-md-6">
                                                                            <label for="txtCreditPeriod">Min Interest Rate
                                                                                <span class="error_message_label">*</span>
                                                                            </label>
                                                                            {!! Form::text('min_interest_rate',
                                                                            isset($subProgramData->min_interest_rate) ? $subProgramData->min_interest_rate : null,
                                                                            ['class'=>'form-control percentage','placeholder'=>'Min', 'id'=>'min_interest_rate'])   !!}

                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label for="txtCreditPeriod">Max Interest Rate
                                                                                <span class="error_message_label">*</span>
                                                                            </label>
                                                                            {!! Form::text('max_interest_rate',
                                                                            isset($subProgramData->max_interest_rate) ? $subProgramData->max_interest_rate : null,
                                                                            ['class'=>'form-control percentage ','placeholder'=>'Max', 'id'=>'max_interest_rate'])
                                                                            !!}

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <label id="prgm_type-error" class="error mb-0" for="interest_rate"></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="txtCreditPeriod">Overdue Interest Rate (%) <span class="error_message_label">*</span> </label>

                                                                    {!! Form::text('overdue_interest_rate',
                                                                    isset($subProgramData->overdue_interest_rate) ? $subProgramData->overdue_interest_rate : null,
                                                                    ['class'=>'form-control valid_perc percentage','placeholder'=>'Overdue interest rate',
                                                                    'id'=>'overdue_interest_rate'])
                                                                    !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="txtCreditPeriod">Margin (%) <span class="error_message_label">*</span></label>
                                                                    {!! Form::text('margin',
                                                                    isset($subProgramData->margin) ? $subProgramData->margin : null,
                                                                    ['class'=>'form-control valid_perc percentage','placeholder'=>'Margin',
                                                                    'id'=>'margin'])
                                                                    !!}

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="txtCreditPeriod"> Interest Borne By <span class="error_message_label">*</span> </label>
                                                                    {!!
                                                                    Form::select('interest_borne_by',
                                                                    [
                                                                    ''=>'Select', '1'=>'Anchor',   '2'=>'Customer/Supplier',
                                                                    ],
                                                                    isset($subProgramData->interest_borne_by) ? $subProgramData->interest_borne_by : null,
                                                                    ['id' => 'interest_borne_by',
                                                                    'class'=>'form-control',
                                                                    ])
                                                                    !!}

                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="txtCreditPeriod"> Overdue Interest Borne By <span class="error_message_label">*</span> </label>
                                                                    {!!
                                                                    Form::select('overdue_interest_borne_by',
                                                                    [
                                                                    ''=>'Select', '1'=>'Anchor',   '2'=>'Customer/Supplier',
                                                                    ],
                                                                    isset($subProgramData->overdue_interest_borne_by) ? $subProgramData->overdue_interest_borne_by : null,
                                                                    ['id' => 'overdue_interest_borne_by',
                                                                    'class'=>'form-control',
                                                                    ])
                                                                    !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="txtCreditPeriod">Grace Period <span class="error_message_label">*</span></label>
                                                                            <div class="clearfix"></div>
                                                                            <div class="">
                                                                                <div class="form-check-inline">
                                                                                    <label class="form-check-label fnt">


                                                                                        {!! Form::radio('is_grace_period',
                                                                                        '1',
                                                                                        isset($subProgramData->is_grace_period) && ($subProgramData->is_grace_period == 1) ? true : false,
                                                                                        ['class'=>'form-check-input grace',
                                                                                        'id'=>'is_grace_period'])
                                                                                        !!}

                                                                                        Yes
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-check-inline ">
                                                                                    <label class="form-check-label fnt">
                                                                                        {!! Form::radio('is_grace_period',
                                                                                        '0',
                                                                                        isset($subProgramData->is_grace_period) && ($subProgramData->is_grace_period == 0) ? true : false,
                                                                                        ['class'=>'form-check-input grace',
                                                                                        'id'=>'is_grace_period'])
                                                                                        !!}

                                                                                        No
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <label id="prgm_type-error" class="error mb-0" for="is_grace_period"></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div id="facility2" class="desc" style="display:none;">
                                                                            <label for="txtCreditPeriod">Grace Period (In Days) <span class="error_message_label">*</span></label>

                                                                            {!! Form::text('grace_period',
                                                                            isset($subProgramData->grace_period) ? $subProgramData->grace_period : null,
                                                                            ['class'=>'form-control numberOnly','placeholder'=>'Grace Period (In Days)',
                                                                            'id'=>'grace_period'])
                                                                            !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label for="is_adhoc_facility">Adhoc Facility<span class="error_message_label">*</span</label>
                                                                            <div class="" style="color:black;">
                                                                                <div class="form-check-inline">
                                                                                    <label class="form-check-label fnt">
                                                                                        {!! Form::radio('is_adhoc_facility',
                                                                                        1,
                                                                                        isset($subProgramData->is_adhoc_facility) && ($subProgramData->is_adhoc_facility == 1) ? true : false,
                                                                                        ['class'=>'form-check-input adhoc',
                                                                                        'id'=>'is_adhoc_facility'])
                                                                                        !!}

                                                                                        Yes
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-check-inline">
                                                                                    <label class="form-check-label fnt">
                                                                                        {!! Form::radio('is_adhoc_facility',
                                                                                        0,
                                                                                        isset($subProgramData->is_adhoc_facility) && ($subProgramData->is_adhoc_facility == 0) ? true : false,
                                                                                        ['class'=>'form-check-input adhoc',
                                                                                        'id'=>'is_adhoc_facility'])
                                                                                        !!}No
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <label id="prgm_type-error" class="error mb-0" for="is_adhoc_facility"></label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div id="facility1" class="desc" style="display:none; color:black;">
                                                                                <label for="txtCreditPeriod">Max. Interset Rate (%) <span class="error_message_label">*</span></label>

                                                                                {!! Form::text('adhoc_interest_rate',
                                                                                isset($subProgramData->adhoc_interest_rate) ? $subProgramData->adhoc_interest_rate : null,
                                                                                ['class'=>'form-control  percentage','placeholder'=>'Max interset rate',
                                                                                'id'=>'employee'])
                                                                                !!}

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-top: -35px;">
                                                    <h5 class="card-title">Method</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group password-input">
                                                                <label for="txtPassword">Disbursement Method <span class="error_message_label">*</span></label>

                                                                {!!
                                                                Form::select('disburse_method',
                                                                [
                                                                ''=>'Select', '1'=>'To Anchor',   '2'=>'To Customer/Supplier ',
                                                                ],
                                                                isset($subProgramData->disburse_method) ? $subProgramData->disburse_method : null,
                                                                ['id' => 'disburse_method',
                                                                'class'=>'form-control',
                                                                ])
                                                                !!}

                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group password-input">
                                                                <label for="txtPassword">Invoice Upload <span class="error_message_label">*</span></label>
                                                                <div class="row">
                                                                    <div class="col-md-2">

                                                                        @php   $invoice_upload = [] @endphp
                                                                        @if(isset($subProgramData->invoice_upload))
                                                                        @php  $invoice_upload = explode(',',  $subProgramData->invoice_upload);  @endphp
                                                                        @endif

                                                                        @php


                                                                        $admin_checked = in_array(1 , $invoice_upload) ;
                                                                        $anchor_checked = in_array(2 , $invoice_upload) ;
                                                                        $customer_checked = in_array(3 , $invoice_upload) ;
                                                                        @endphp
                                                                        {!!
                                                                        Form::checkbox('invoice_upload[]',
                                                                        1,
                                                                        $admin_checked ,

                                                                        ['id' => 'invoice_upload_0',

                                                                        ])
                                                                        !!}
                                                                        <label for="invoice_upload_0"> Admin</label>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        {!!
                                                                        Form::checkbox('invoice_upload[]',
                                                                        2,
                                                                        $anchor_checked ,

                                                                        ['id' => 'invoice_upload_1',

                                                                        ])
                                                                        !!}
                                                                        <label for="invoice_upload_1"> Anchor</label>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        {!!
                                                                        Form::checkbox('invoice_upload[]',
                                                                        3,
                                                                        $customer_checked ,

                                                                        ['id' => 'invoice_upload_2',
                                                                         'class' => 'customer_upload',
                                                                        ])
                                                                        !!}
                                                                        <label for="invoice_upload_2"> Customer/Supplier</label>
                                                                    </div>
                                                                </div>
                                                                <label id="prgm_type-error" class="error mb-0" for="invoice_upload[]"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group password-input">
                                                                <label for="txtPassword">Bulk Invoice Upload <span class="error_message_label">*</span></label>
                                                                @php   $bulk_invoice_upload = [] @endphp
                                                                @if(isset($subProgramData->bulk_invoice_upload))
                                                                @php  $bulk_invoice_upload = explode(',',  $subProgramData->bulk_invoice_upload);  @endphp
                                                                @endif
                                                                @php

                                                                $admin_checked = in_array(1 , $bulk_invoice_upload) ;
                                                                $anchor_checked = in_array(2 , $bulk_invoice_upload) ;
                                                                $customer_checked = in_array(3 , $bulk_invoice_upload) ;
                                                                @endphp

                                                                <div class="row">
                                                                    <div class="col-md-2">


                                                                        {!!
                                                                        Form::checkbox('bulk_invoice_upload[]',
                                                                        1,
                                                                        $invoice_upload ,

                                                                        ['id' => 'bulk_invoice_upload_0',

                                                                        ])
                                                                        !!}

                                                                        <label for="bulk_invoice_upload_0"> Admin</label>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        {!!
                                                                        Form::checkbox('bulk_invoice_upload[]',
                                                                        2,
                                                                        $anchor_checked ,

                                                                        ['id' => 'bulk_invoice_upload_1',

                                                                        ])
                                                                        !!}



                                                                        <label for="bulk_invoice_upload_1"> Anchor</label>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        {!!
                                                                        Form::checkbox('bulk_invoice_upload[]',
                                                                        3,
                                                                        $customer_checked ,

                                                                        ['id' => 'bulk_invoice_upload_2',
                                                                         'class' => 'customer_upload',

                                                                        ])
                                                                        !!}

                                                                        <label for="bulk_invoice_upload_2"> Customer/Supplier</label>
                                                                    </div>
                                                                </div>
                                                                <label id="prgm_type-error" class="error mb-0" for="bulk_invoice_upload[]"></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group password-input">
                                                                <label for="txtPassword">Invoice Approval <span class="error_message_label">*</span></label>

                                                                @php   $invoice_approval = [] @endphp
                                                                @if(isset($subProgramData->invoice_approval))
                                                                @php  $invoice_approval = explode(',',  $subProgramData->invoice_approval);  @endphp
                                                                @endif

                                                                @php

                                                                $admin_checked = in_array(1 , $invoice_approval) ;
                                                                $anchor_checked = in_array(2 , $invoice_approval) ;
                                                                $customer_checked = in_array(3 , $invoice_approval) ;
                                                                $auto_approval = in_array(4 , $invoice_approval) ;
                                                                @endphp


                                                                <div class="row">
                                                                    <div class="col-md-2">
                                                                        {!!
                                                                        Form::checkbox('invoice_approval[]',
                                                                        1,
                                                                        $admin_checked ,

                                                                        ['id' => 'invoice_approval_0',
                                                                         'class' => 'customer_upload',

                                                                        ])
                                                                        !!}
                                                                        <label for="invoice_approval_0"> Admin</label>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        {!!
                                                                        Form::checkbox('invoice_approval[]',
                                                                        2,
                                                                        $anchor_checked ,

                                                                        ['id' => 'invoice_approval_1',
                                                                         'class' => 'customer_upload',
                                                                        ])
                                                                        !!}
                                                                        <label for="invoice_approval_1"> Anchor</label>
                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        {!!
                                                                        Form::checkbox('invoice_approval[]',
                                                                        4,
                                                                        $auto_approval ,

                                                                        ['id' => 'invoice_approval_4',
                                                                          'class' => 'customer_upload',
                                                                        ])
                                                                        !!}
                                                                        <label for="invoice_approval_4"> Auto Approval</label>
                                                                    </div>
                                                                </div>
                                                                <label id="prgm_type-error" class="error mb-0" for="invoice_approval[]"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-top: -45px">
                                                    <h5 class="card-title">Document Type </h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group password-input">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h5>Pre Sanction </h5>

                                                                {!!
                                                                Form::select('pre_sanction[]',
                                                                $preSanction,
                                                                isset($sanctionData['pre']) ? $sanctionData['pre'] : null,
                                                                ['id' => 'pre_sanction',
                                                                'class'=>'form-control multi-select-demo ',
                                                                'multiple'=>'multiple'])
                                                                !!}
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h5>Post Sanction </h5>
                                                                {!!
                                                                Form::select('post_sanction[]',
                                                                $postSanction,
                                                                isset($sanctionData['post']) ? $sanctionData['post'] : null,
                                                                ['id' => 'post_sanction',
                                                                'class'=>'form-control multi-select-demo ',
                                                                'multiple'=>'multiple'])
                                                                !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                    <div class="col-md-12">
                                        <div class="form-group password-input">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="txtCreditPeriod">Status <span class="error_message_label">*</span> </label>
                                                    {!! Form::select('status', [''=>trans('backend.please_select') ,1=>'Active',0 =>'In Active'],
                                                    isset($subProgramData->status) ? $subProgramData->status : null, ['class'=>'form-control required']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                                <div class="col-md-12">
                                                    <h5 class="card-title">Charges</h5>
                                                </div>
                                            </div>

                                            <div class="col-md-12" style="margin-top: -20px">
                                                @if(count($programCharges))


                                                @foreach($programCharges as $keys =>$programChrg)

                                                <div class="charge_parent_div">
                                                    <div class="row" style="background-color: #e1f0eb;">
                                                        <div class="col-md-6">
                                                            <div class="form-group password-input">
                                                                <label for="txtPassword">Select Charge Type <span class="error_message_label">*</span>
                                                                </label>
                                                                {!!
                                                                Form::select('charge['.$keys.']',
                                                                [''=>'Please select']+$charges,
                                                                $programChrg['charge_id'],
                                                                ['id' => 'charge_'.$keys,
                                                                'class'=>'form-control  charges',
                                                                'required'=>'required',
                                                                'data-rel'=>$keys
                                                                ])
                                                                !!}


                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-sm-6">
                                                            <label for="txtPassword">&nbsp;
                                                            </label> <br/>
                                                            <button style="display: none" type="button" class="btn btn-danger mr-2 btn-sm delete_btn"><i class="fa  fa-times-circle"></i></button>
                                                            <button  style="display: none"  type="button" class="btn btn-primary  btn-sm add_more"> <i class="fa  fa-plus-circle"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-sm-12">
                                                        <div class="html_append">
                                                            @include('backend/lms/charges_html', ['data'=> (object) $programChrg , 'len'=>$keys ])
                                                        </div>
                                                    </div>

                                                </div>

                                                @endforeach
                                                @else

                                                <div class="charge_parent_div">
                                                    <div class="row" style="background-color: #e1f0eb;">
                                                        <div class="col-md-6">
                                                            <div class="form-group password-input">
                                                                <label for="txtPassword">Select Charge Type <span class="error_message_label">*</span>
                                                                </label>
                                                                {!!
                                                                Form::select('charge[0]',
                                                                [''=>'Please select']+$charges,
                                                                null,
                                                                ['id' => 'charge_0',
                                                                'class'=>'form-control charges',
                                                                'required'=>'required',
                                                                'data-rel'=>0
                                                                ])
                                                                !!}


                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-sm-6">
                                                            <label for="txtPassword">&nbsp;
                                                            </label> <br/>
                                                            <button style="display: none" type="button" class="btn btn-danger mr-2 btn-sm delete_btn"><i class="fa  fa-times-circle"></i></button>
                                                            <button  style="display: none"  type="" class="btn btn-primary  btn-sm add_more"> <i class="fa  fa-plus-circle"></i></button>


                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-sm-12">
                                                        <div class="html_append"></div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <!--@include('backend.lms.doalevel' ,['doaLevelList'=>$doaLevelList])-->
                                        </div>

                                    </div>
                                    <div class="col-md-12">
                                        <div class="text-right mt-3">

                                            <!--<a class="btn btn-secondary btn-sm" href='{{  route('manage_sub_program', ['anchor_id' => $anchor_id, 'program_id' => \Session::get('list_program_id')]) }}'>  Cancel</a>-->
                                            @if ($reason_type != '' && isset($subProgramData->status) && $subProgramData->status == '0')
                                            <input type="submit"  class="btn btn-primary ml-2 btn-sm save_sub_program" name="reject_btn" id="reject_btn" value="Reject">
                                            @else
                                            <a class="btn btn-secondary btn-sm" href='{{  route('manage_sub_program', ['anchor_id' => $anchor_id, 'program_id' => \Session::get('list_program_id')]) }}'>  Cancel</a>
                                            @endif

                                            @if (\Helpers::checkPermission('save_sub_program') && $action != 'view')
                                            <button type="submit"  class="btn btn-primary ml-2 btn-sm save_sub_program"> Save</button>
                                            @endif
                                        </div>
                                    </div>

                                    {{ Form::close()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('additional_css')
<link rel="stylesheet" href="{{ url('backend/assets/css/bootstrap-multiselect.css') }}" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
@endsection
@section('jscript')
<script>

    $(document).on('keyup','#anchor_sub_limit', function () {
       let $anchor_sub_limit = $(this).val();
       $('input[name="max_loan_size"]').val($anchor_sub_limit || 0);
    })
   $(document).on('click','.customer_upload',function(){

        if ($('#invoice_upload_2').is(":checked") || $('#bulk_invoice_upload_2').is(":checked"))
        {
            admin_app    = ($("#invoice_approval_0").is(":checked"));
            admin_anc    = ($("#invoice_approval_1").is(":checked"));
            admin_auto    = ($("#invoice_approval_4").is(":checked"));
            if(admin_app==false && admin_anc==false && admin_auto==true)
            {
                $("#invoice_approval_4").prop("checked", false)
                $("#invoice_approval_4").attr("disabled", true);
                return false;
            }
            else
            {
                $("#invoice_approval_4").attr("disabled", false);
                return true;
            }
        }
        else
        {
                $("#invoice_approval_4").attr("disabled", false);
                return true;
        }


   })
</script>
<script>

    var messages = {
        get_charges_html: "{{ URL::route('get_charges_html') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        is_fungible: "{{ $anchorData->is_fungible }}",
        please_select: "{{ trans('backend.please_select') }}",
        invoiceDataCount: "{{ ($invoiceDataCount > 0) ? 'true' : 'false' }}"
    };



</script>
<script src="{{ asset('backend/assets/js/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script src="{{ asset('backend/js/common.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend/js/lms/program.js') }}" type="text/javascript"></script>
@endsection