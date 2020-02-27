@extends('layouts.backend.admin-layout')
@section('content')
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
                                <div class="col-md-8 col-md-offset-2">
                                </div>
                                <div class="ima"></div>
                                <div class="documents-detail inner-subform" id="terms">
                                    <div class="form-sections parent_div">
                                        <div class="col-md-12 subD">
                                            <div class="sub-progrem">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <h4 class="gc"><label>Program Name: </label> {{ isset($programData) ? $programData->prgm_name : null }} <span class="float-right mb-0"> <label>Anchor Name: </label> {{ isset($anchorData) ? $anchorData->f_name : null }}</span> </h4>
                                                        
                                                        <p class="float-left mr-3 mb-0">
                                                            <b>Total Anchor Limit : </b>
                                                            <i class="fa fa-inr" aria-hidden="true"></i> 
                                                            {!! isset($programData->anchor_limit) ?  \Helpers::formatCurreny($programData->anchor_limit )   : null !!}
                                                        </p>

                                                        
                                                        <p class="float-right mb-0">
                                                            <b>Remaining Anchor Limit : </b>
                                                            <i class="fa fa-inr" aria-hidden="true"></i>
                                                            {{ isset($remaningAmount) ?  number_format($remaningAmount)  : null }} 
                                                        </p>
                                                    </div>
                                                    <!--                                                    <div class="col-sm-3 text-right">
                                                       <a class="edit-btn" href="{{route('add_program',['program_id'=> $program_id ,'anchor_id'=>$anchor_id ])}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                       
                                                       </div>-->
                                                </div>
                                            </div>
                                            </br>


                                            {{ Form::open(['url'=>route('save_sub_program'),'id'=>'add_sub_program']) }}
                                            {!! Form::hidden('parent_prgm_id',$program_id) !!}
                                            {!! Form::hidden('program_id',isset($subProgramData->prgm_id) ? $subProgramData->prgm_id : null) !!}
                                            {!! Form::hidden('anchor_limit',isset($programData) ? $programData->anchor_limit : null) !!}
                                            {!! Form::hidden('product_id',isset($programData) ? $programData->product_id : null) !!}
                                            {!! Form::hidden('anchor_limit_re',isset($remaningAmount) ?  number_format($remaningAmount)  : null,['id'=>'anchor_limit'])   !!}
                                            {!! Form::hidden('anchor_id',$anchor_id) !!}
                                            {!! Form::hidden('anchor_user_id',isset($programData->anchor_user_id) ?$programData->anchor_user_id  : null ) !!}
                                            <div class="sub-form renew-form" id="subform">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="txtCreditPeriod">
                                                            {{ trans('backend.add_program.program_detail') }}
                                                            <span class="error_message_label">*</span></label>
                                                        <div class="block-div clearfix ">
                                                            <div class="form-check-inline float-left">
                                                                <label class="form-check-label fnt">
                                                                    {!! Form::radio('prgm_type','1',($programData->prgm_type=="1")? "checked" : "", ['class'=>'form-check-input']) !!}
                                                                    <strong>
                                                                        {{ trans('backend.add_program.vendor_finance') }}   
                                                                    </strong>
                                                                </label>
                                                            </div>
                                                            <div class="form-check-inline float-left">
                                                                <label class="form-check-label fnt">
                                                                    {!! Form::radio('prgm_type','2',($programData->prgm_type=="2")? "checked" : "", ['class'=>'form-check-input']) !!}
                                                                    <strong>
                                                                        {{ trans('backend.add_program.channel_finance') }}    
                                                                    </strong>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <label id="prgm_type-error" class="error mb-0" for="prgm_type"></label>
                                                    </div>

                                                </div>

                                                <div class="col-md-12">
                                                    <h5 class="card-title">Terms</h5>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group INR">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label for="txtCreditPeriod">Product line<span class="error_message_label">*</span> </label>
                                                                {!! Form::text('product_name',
                                                                isset($subProgramData->product_name) ? $subProgramData->product_name : null,
                                                                ['class'=>'form-control'])   !!}

                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="txtCreditPeriod">Limit <span class="error_message_label">*</span> </label>
                                                                <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                                {!! Form::text('anchor_sub_limit',
                                                                isset($subProgramData->anchor_sub_limit) ? number_format($subProgramData->anchor_sub_limit) : null,
                                                                ['class'=>'form-control number_format '])   !!}

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group INR">
                                                        <label for="txtCreditPeriod">Loan Size<span class="error_message_label">*</span> </label>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <a href="javascript:void(0);" class="verify-owner-no" style="top:12px;">
                                                                    <i class="fa fa-inr" aria-hidden="true"></i></a>
                                                                {!! Form::text('min_loan_size',
                                                                isset($subProgramData->min_loan_size) ?  number_format($subProgramData->min_loan_size) : null,
                                                                ['class'=>'form-control number_format ','placeholder'=>'Min'])   !!}

                                                            </div>
                                                            <div class="col-md-6">
                                                                <a href="javascript:void(0);" class="verify-owner-no" style="top:12px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                                {!! Form::text('max_loan_size',
                                                                isset($subProgramData->max_loan_size) ?  number_format($subProgramData->max_loan_size) : null,
                                                                ['class'=>'form-control max_loan_size number_format','placeholder'=>'Max'])   !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="txtCreditPeriod">Interest Rate (%)
                                                            <span class="error_message_label">*</span>
                                                        </label>
                                                        <div class="mb-3">
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label fnt">

                                                                    {!! Form::radio('interest_rate','1',
                                                                    isset($subProgramData->interest_rate) ? $subProgramData->interest_rate : null,
                                                                    ['class'=>'form-check-input int-checkbox '])    !!} 
                                                                    Fixed
                                                                </label>
                                                            </div>
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label fnt">
                                                                    {!! Form::radio('interest_rate','2',
                                                                    isset($subProgramData->interest_rate) ? $subProgramData->interest_rate : null,
                                                                    ['class'=>'form-check-input int-checkbox']) !!} 
                                                                    Floating
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="row floating " style="display:none;">
                                                            <div class="col-md-4">
                                                                {!!
                                                                Form::select('interest_linkage',
                                                                [
                                                                ''=>'Linkage', '12'=>'12%',   '15'=>'15%','20'=>'20%','25'=>'25%',
                                                                ],
                                                                isset($subProgramData->interest_linkage) ? $subProgramData->interest_linkage : null,
                                                                ['id' => 'interest_linkage',
                                                                'class'=>'form-control',
                                                                ])
                                                                !!}
                                                            </div>

                                                        </div>
                                                        <div class="row fixed" style="display:none;">
                                                            <div class="col-md-6">

                                                                {!! Form::text('min_interest_rate',
                                                                isset($subProgramData->min_interest_rate) ? $subProgramData->min_interest_rate : null,
                                                                ['class'=>'form-control percentage','placeholder'=>'Min'])   !!}

                                                            </div>
                                                            <div class="col-md-6">

                                                                {!! Form::text('max_interest_rate',
                                                                isset($subProgramData->max_interest_rate) ? $subProgramData->max_interest_rate : null,
                                                                ['class'=>'form-control percentage ','placeholder'=>'Max'])   
                                                                !!}

                                                            </div>
                                                        </div>

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
                                                    </div>
                                                </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="txtCreditPeriod">Adhoc Facility<span class="error_message_label">*</span></label>
                                                                    <div class="">
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
                                                                    <div class="mt-3">
                                                                        <div id="facility1" class="desc" style="display:none;">
                                                                            <label for="txtCreditPeriod">Max. Interset Rate (%) <span class="error_message_label">*</span></label>

                                                                            {!! Form::text('adhoc_interest_rate',
                                                                            isset($subProgramData->adhoc_interest_rate) ? $subProgramData->adhoc_interest_rate : null,
                                                                            ['class'=>'form-control valid_perc percentage','placeholder'=>'Max interset rate',
                                                                            'id'=>'employee'])   
                                                                            !!}

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
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
                                                                    <div class="mt-3">
                                                                        <div id="facility2" class="desc" style="display:none;">
                                                                            <label for="txtCreditPeriod">Grace Period (In Days) <span class="error_message_label">*</span></label>

                                                                            {!! Form::text('grace_period',
                                                                            isset($subProgramData->grace_period) ? $subProgramData->grace_period : null,
                                                                            ['class'=>'form-control numberOnly','placeholder'=>'Max interset rate',
                                                                            'id'=>'grace_period'])   
                                                                            !!}



                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <h5 class="card-title">Method</h5>
                                                    </div>
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

                                                    <div class="col-md-12">
                                                        <div class="form-group password-input">
                                                            <label for="txtPassword">Invoice Upload <span class="error_message_label">*</span></label>
                                                            <div class="row">
                                                                <div class="col-md-3">

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
                                                                <div class="col-md-3">
                                                                    {!!
                                                                    Form::checkbox('invoice_upload[]',
                                                                    2,
                                                                    $anchor_checked ,

                                                                    ['id' => 'invoice_upload_1',

                                                                    ])
                                                                    !!}
                                                                    <label for="invoice_upload_1"> Anchor</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    {!!
                                                                    Form::checkbox('invoice_upload[]',
                                                                    3,
                                                                    $customer_checked ,

                                                                    ['id' => 'invoice_upload_2',

                                                                    ])
                                                                    !!}
                                                                    <label for="invoice_upload_2"> Customer/Supplier</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
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
                                                                <div class="col-md-3">


                                                                    {!!
                                                                    Form::checkbox('bulk_invoice_upload[]',
                                                                    1,
                                                                    $invoice_upload ,

                                                                    ['id' => 'bulk_invoice_upload_0',

                                                                    ])
                                                                    !!}

                                                                    <label for="bulk_invoice_upload_0"> Admin</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    {!!
                                                                    Form::checkbox('bulk_invoice_upload[]',
                                                                    2,
                                                                    $anchor_checked ,

                                                                    ['id' => 'bulk_invoice_upload_1',

                                                                    ])
                                                                    !!}



                                                                    <label for="bulk_invoice_upload_1"> Anchor</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    {!!
                                                                    Form::checkbox('bulk_invoice_upload[]',
                                                                    3,
                                                                    $customer_checked ,

                                                                    ['id' => 'bulk_invoice_upload_2',

                                                                    ])
                                                                    !!}

                                                                    <label for="bulk_invoice_upload_2"> Customer/Supplier</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
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
                                                                <div class="col-md-3">
                                                                    {!!
                                                                    Form::checkbox('invoice_approval[]',
                                                                    1,
                                                                    $admin_checked ,

                                                                    ['id' => 'invoice_approval_0',

                                                                    ])
                                                                    !!}
                                                                    <label for="invoice_approval_0"> Admin</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    {!!
                                                                    Form::checkbox('invoice_approval[]',
                                                                    2,
                                                                    $anchor_checked ,

                                                                    ['id' => 'invoice_approval_1',

                                                                    ])
                                                                    !!}
                                                                    <label for="invoice_approval_1"> Anchor</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    {!!
                                                                    Form::checkbox('invoice_approval[]',
                                                                    3,
                                                                    $customer_checked ,

                                                                    ['id' => 'invoice_approval_2',

                                                                    ])
                                                                    !!}
                                                                    <label for="invoice_approval_2"> Customer/Supplier</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    {!!
                                                                    Form::checkbox('invoice_approval[]',
                                                                    4,
                                                                    $auto_approval ,

                                                                    ['id' => 'invoice_approval_4',

                                                                    ])
                                                                    !!}
                                                                    <label for="invoice_approval_4"> Auto Approval</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
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
                                                        <h5 class="card-title">Charges</h5>
                                                    </div>


                                                    @if(count($programCharges))


                                                    @foreach($programCharges as $keys =>$programChrg)

                                                    <div class="charge_parent_div">


                                                        <div class="col-md-12">
                                                            <div class="form-group password-input">
                                                                <label for="txtPassword">Select Charge Type <span class="error_message_label">*</span>
                                                                </label>
                                                                {!!
                                                                Form::select('charge['.$keys.']',
                                                                [''=>'Please select']+$charges,
                                                                $programChrg['charge_id'],
                                                                ['id' => 'charge_'.$keys,
                                                                'class'=>'form-control col-md-6 charges',
                                                                'required'=>'required',
                                                                'data-rel'=>$keys
                                                                ])
                                                                !!}


                                                            </div>
                                                        </div>
                                                        <div class="html_append">


                                                            @include('backend/lms/charges_html', ['data'=> (object) $programChrg , 'len'=>$keys ]) 


                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6 col-sm-6">
                                                                <div class="text-left mt-3">           
                                                                    <button style="display: none" type="button" class="btn btn-danger mr-2 btn-sm delete_btn"> Delete</button>
                                                                </div>
                                                            </div>


                                                            <div class="col-6 col-sm-6">
                                                                <div class="text-right mt-3">           
                                                                    <button  style="display: none"  type="button" class="btn btn-primary ml-2 btn-sm add_more"> Add More</button>
                                                                </div>
                                                            </div>



                                                        </div>

                                                    </div>
                                                    @endforeach
                                                    @else 

                                                    <div class="charge_parent_div">
                                                        <div class="col-md-12">
                                                            <div class="form-group password-input">
                                                                <label for="txtPassword">Select Charge Type <span class="error_message_label">*</span>
                                                                </label>
                                                                {!!
                                                                Form::select('charge[0]',
                                                                [''=>'Please select']+$charges,
                                                                null,
                                                                ['id' => 'charge_0',
                                                                'class'=>'form-control col-md-6 charges',
                                                                'required'=>'required',
                                                                'data-rel'=>0
                                                                ])
                                                                !!}


                                                            </div>
                                                        </div>
                                                        <div class="html_append"></div>
                                                        <div class="row">
                                                            <div class="col-6 col-sm-6">
                                                                <div class="text-left mt-3">           
                                                                    <button style="display: none" type="button" class="btn btn-danger mr-2 btn-sm delete_btn"> Delete</button>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-sm-6">
                                                                <div class="text-right mt-3">           
                                                                    <button type="button" class="btn btn-primary ml-2 btn-sm add_more"> Add More</button>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    @endif
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="txtCreditPeriod">
                                                                Status
                                                                <span class="error_message_label">*</span> </label>
                                                            {!! Form::select('status', [''=>trans('backend.please_select') ,1=>'Active',0 =>'In Active'],
                                                            isset($subProgramData->status) ? $subProgramData->status : null, ['class'=>'form-control col-md-6']) !!}
                                                        </div>
                                                    </div>

                                                </div>
                                                <!--@include('backend.lms.doalevel' ,['doaLevelList'=>$doaLevelList])-->

                                                <div class="col-md-12">
                                                    <div class="text-right mt-3">

                                                        <a class="btn btn-secondary btn-sm" href='{{  route('manage_sub_program', ['anchor_id' => $anchor_id, 'program_id' => \Session::get('list_program_id')]) }}'>  Cancel</a>
                                                        <button type="submit"  class="btn btn-primary ml-2 btn-sm save_sub_program"> Save</button>
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
    </div>
    @endsection
    @section('additional_css')
    <link rel="stylesheet" href="{{ url('backend/assets/css/bootstrap-multiselect.css') }}" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    @endsection
    @section('jscript')

    <script>

        var messages = {
            get_charges_html: "{{ URL::route('get_charges_html') }}",
            data_not_found: "{{ trans('error_messages.data_not_found') }}",
            token: "{{ csrf_token() }}",
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