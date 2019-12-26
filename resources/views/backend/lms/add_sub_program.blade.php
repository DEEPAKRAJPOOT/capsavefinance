@extends('layouts.backend.admin-layout')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa fa-clipboard"></i>
        </div>
        <div class="header-title">
            <h3>
                {{ trans('backend.mange_program.add_program') }} 
            </h3>
            <ol class="breadcrumb">
                <li style="color:#374767;">  {{ trans('backend.mange_program.home') }} </li>
                <li style="color:#374767;"> {{ trans('backend.mange_program.manage_program') }}</li>
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
                                        <div class="col-md-8 col-md-offset-2 subD">
                                            <div class="sub-progrem">
                                                <div class="row">
                                                    <div class="col-sm-9">
                                                        <h4 class="gc"> {{ isset($anchorData) ? $anchorData->f_name : null }}</h4>
                                                        <p class="float-left mr-3 mb-0">
                                                            <b>Anchor Limit : </b>
                                                            <i class="fa fa-inr" aria-hidden="true"></i> 
                                                            {{ isset($programData) ? $programData->anchor_limit : null }}
                                                        </p>
                                                        <p class="float-left mb-0"><b>Programe Type : </b> {{ \Helpers::getProgramType($programData->prgm_type) }} </p>
                                                    </div>
                                                    <!--                                                    <div class="col-sm-3 text-right">
                                                       <a class="edit-btn" href="{{route('add_program',['program_id'=> $program_id ,'anchor_id'=>$anchor_id ])}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                       
                                                       </div>-->
                                                </div>
                                            </div>
                                            </br>


                                            {{ Form::open(['url'=>route('save_sub_program'),'id'=>'add_sub_program']) }}
                                            {!! Form::hidden('parent_prgm_id',$program_id) !!}
                                            {!! Form::hidden('anchor_limit',isset($programData) ? $programData->anchor_limit : null) !!}
                                           
                                            {!! Form::hidden('anchor_id',$anchor_id) !!}
                                            {!! Form::hidden('anchor_user_id',isset($programData->anchor_user_id) ?$programData->anchor_user_id  : null ) !!}
                                            <div class="sub-form renew-form" id="subform">
                                                <div class="col-md-12">
                                                    <div class="form-group INR">
                                                        <label for="txtCreditPeriod">Anchor Limit</label>
                                                        <a href="javascript:void(0);" class="verify-owner-no" style="top:42px;">
                                                            <i class="fa fa-inr" aria-hidden="true"></i>
                                                        </a>   
                                                        {!! Form::text('anchor_limit_re',isset($remaningAmount) ? $remaningAmount : null,['class'=>'form-control' ,'readonly'=>true ,'id'=>'anchor_limit'])   !!}
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
                                                                {!! Form::text('product_name','',['class'=>'form-control'])   !!}

                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="txtCreditPeriod">Limit<span class="error_message_label">*</span> </label>
                                                                {!! Form::text('anchor_sub_limit','',['class'=>'form-control'])   !!}

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
                                                                {!! Form::text('min_loan_size','',['class'=>'form-control','placeholder'=>'Min'])   !!}

                                                            </div>
                                                            <div class="col-md-6">
                                                                <a href="javascript:void(0);" class="verify-owner-no" style="top:12px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                                {!! Form::text('max_loan_size','',['class'=>'form-control','placeholder'=>'Max'])   !!}
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

                                                                    {!! Form::radio('interest_rate','1','',  ['class'=>'form-check-input int-checkbox'])    !!} 
                                                                    Fixed
                                                                </label>
                                                            </div>
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label fnt">
                                                                    {!! Form::radio('interest_rate','2','',  ['class'=>'form-check-input int-checkbox']) !!} 
                                                                    Floating
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="row floating " style="display:none;">
                                                            <div class="col-md-4">
                                                                <select  name="interest_linkage" class="form-control">
                                                                    <option>Linkage</option>
                                                                    <option value="12" >12%</option>
                                                                    <option value="15">15%</option>
                                                                    <option value="20">20%</option>
                                                                    <option value="25">25%</option>
                                                                </select>
                                                            </div>

                                                        </div>
                                                        <div class="row fixed" style="display:none;">
                                                            <div class="col-md-6">
                                                                <input type="text" name="min_interest_rate" class="form-control" placeholder="Min " >
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" name="max_interest_rate" class="form-control" placeholder="Max" >
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="txtCreditPeriod">Overdue Interest Rate (%) <span class="error_message_label">*</span> </label>
                                                        <input type="text" name="overdue_interest_rate" id="overdue_interest_rate" value="" class="form-control" placeholder="Overdue interest rate" >
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="txtCreditPeriod"> Interest Borne By <span class="error_message_label">*</span> </label>
                                                        <select name="interest_borne_by" class="form-control">
                                                            <option> Select</option>
                                                            <option value="1">Anchor</option>
                                                            <option value="2">Customer/Supplier </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="txtCreditPeriod">Margin (%) <span class="error_message_label">*</span></label>
                                                        <input type="text" name="margin" id="margin" value="" class="form-control" placeholder="Margin" >
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="txtCreditPeriod">Adhoc Facility<span class="error_message_label">*</span> </label>
                                                                <div class="">
                                                                    <div class="form-check-inline">
                                                                        <label class="form-check-label fnt">
                                                                            <input type="radio" class="form-check-input adhoc" name="is_adhoc_facility" value="yes">Yes
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-check-inline">
                                                                        <label class="form-check-label fnt">
                                                                            <input type="radio" class="form-check-input adhoc" name="is_adhoc_facility" value="no">No
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-3">
                                                                    <div id="facility1" class="desc" style="display:none;">
                                                                        <label for="txtCreditPeriod">Max. Interset Rate (%) <span class="error_message_label">*</span></label>
                                                                        <input type="text" name="adhoc_interest_rate" id="employee" value="" class="form-control" placeholder="Max interset rate" >
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
                                                                            <input type="radio" class="form-check-input grace" name="is_grace_period" value="yes">Yes
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-check-inline ">
                                                                        <label class="form-check-label fnt">
                                                                            <input type="radio" class="form-check-input grace" name="is_grace_period" value="no">No
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-3">
                                                                    <div id="facility2" class="desc" style="display:none;">
                                                                        <label for="txtCreditPeriod">Grace Period (In Days) <span class="error_message_label">*</span></label>
                                                                        <input type="text" name="grace_period" id="grace_period" value="" class="form-control " placeholder="Max interset rate" >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <h5 class="card-title">Method</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group password-input">
                                                        <label for="txtPassword">Disbursement Method <span class="error_message_label">*</span></label>
                                                        <select  name="disburse_method" class="form-control">
                                                            <option> Select</option>
                                                            <option value="1"> To Anchor</option>
                                                            <option value="2"> To Customer/Supplier </option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group password-input">
                                                        <label for="txtPassword">Invoice Upload <span class="error_message_label">*</span></label>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <input type="checkbox" value="1"  id="invoice_upload_0" name="invoice_upload[]">
                                                                <label for="invoice_upload_0"> Admin</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="checkbox" value="2"  id="invoice_upload_1" name="invoice_upload[]">
                                                                <label for="invoice_upload_1"> Anchor</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="checkbox" value="3"  id="invoice_upload_2" name="invoice_upload[]">
                                                                <label for="invoice_upload_2"> Customer/Supplier</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group password-input">
                                                        <label for="txtPassword">Bulk Invoice Upload <span class="error_message_label">*</span></label>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <input type="checkbox" value="1" id="bulk_invoice_upload_0" 
                                                                       name="bulk_invoice_upload[]"><label for="bulk_invoice_upload_0"> Admin</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="checkbox" value="2"  id="bulk_invoice_upload_1" name="bulk_invoice_upload[]">
                                                                <label for="bulk_invoice_upload_1"> Anchor</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="checkbox"  value="3"   id="bulk_invoice_upload_2" name="bulk_invoice_upload[]">
                                                                <label for="bulk_invoice_upload_2"> Customer/Supplier</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group password-input">
                                                        <label for="txtPassword">Invoice Approval <span class="error_message_label">*</span></label>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <input type="checkbox" value="1"  id="invoice_approval_0" name="invoice_approval[]"><label for="invoice_approval_0"> Admin</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="checkbox" value="2" id="invoice_approval_1" name="invoice_approval[]"><label for="invoice_approval_1"> Anchor</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="checkbox" value="3" id="invoice_approval_2" name="invoice_approval[]"><label for="invoice_approval_2"> Customer/Supplier</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="checkbox"  value="4" id="invoice_approval_4" name="invoice_approval[]"><label for="invoice_approval_4"> Auto Approval</label>
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
                                                                [''=>'Please select']+$preSanction,
                                                                null,
                                                                ['id' => 'pre_sanction',
                                                                'class'=>'form-control multi-select-demo ',
                                                                'multiple'=>'multiple'])
                                                                !!}



                                                            </div>
                                                            <div class="col-md-6">
                                                                <h5>Post Sanction </h5>
                                                                {!!
                                                                Form::select('post_sanction[]',
                                                                [''=>'Please select']+$postSanction,
                                                                null,
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

                                                <div class="charge_parent_div">
                                                    <div class="col-md-12">
                                                        <div class="form-group password-input">
                                                            <label for="txtPassword">Select Charge Type <span class="error_message_label">*</span>
                                                            </label>
                                                            {!!
                                                            Form::select('charge[1]',
                                                            [''=>'Please select']+$charges,
                                                            null,
                                                            ['id' => 'charge_1',
                                                            'class'=>'form-control charges',
                                                            'required'=>'required'
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
                                            </div>
                                            <div class="col-md-12">
                                                <div class="text-right mt-3">
                                                    <button type="button" id="" class="btn btn-secondary btn-sm"> Cancel</button>
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
        please_select: "{{ trans('backend.please_select') }}"

    };



</script>
<script src="{{ asset('backend/assets/js/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script src="{{ asset('backend/js/common.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend/js/lms/program.js') }}" type="text/javascript"></script>
@endsection