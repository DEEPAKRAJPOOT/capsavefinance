@extends('layouts.backend.admin_popup_layout') 

@section('content')
    

<div class="modal-body text-left">
                            <div class="sub-progrem">
                                <div class="row">
                                    <div class="col-sm-12">
                                        
                                        <p class="float-left mr-3 mb-0">
                                            <b>Remaining Anchor Limit : </b>
                                            <i class="fa fa-inr" aria-hidden="true"></i>
                                            {{ isset($remaningAmount) ?  number_format($remaningAmount)  : null }} 
                                        </p>
                                                                               
                                        
                                        <p class="float-right mb-0">                                            
                                            <b>Utilized Limit in Offer : </b>
                                            <i class="fa fa-inr" aria-hidden="true"></i>
                                            {{ isset($utilizedLimit) ?  number_format($utilizedLimit)  : null }}                                             
                                        </p>                                        
                                    </div>                                    
                                </div>
                            </div>
                            </br>
                            {{ Form::open(array('url' => route('save_program') ,'id'=>'addProgram')) }}

                                                            
                                    <div class="clearfix"></div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="product_id">
                                                   Product Type
                                                    <span class="error_message_label">*</span></label>
                                                {!! Form::select('product_id', $productList, $program->product_id,['class'=>'form-control'])!!}
                                                {!! $errors->first('product_id', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="anchor_id">
                                                    {{ trans('backend.add_program.anchor_name') }}
                                                    <span class="error_message_label">*</span></label>
                                             
                                                <select name="anchor_id" class="form-control" disabled="disabled">
                                                    <option value="" >Please select</option>                                                
                                                    @foreach($anchorList as $anch_id => $anchor)                                             
                                                    <option value="{{ $anch_id }}" @if($anchor_id == $anch_id) selected @endif >{{ $anchor['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('anchor_id', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">    
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="prgm_name">
                                                    {{ trans('backend.add_program.program_name') }}
                                                    <span class="error_message_label">*</span>
                                                </label>
                                                {!! Form::text('prgm_name',$program->prgm_name, ['class'=>'form-control','placeholder'=>"Enter Programe Name", 'readonly'=>true])!!}
                                                @if(Session::has('error') && Session::get('error'))
                                                    <label class='error'>{{Session::get('error')}}</label>
                                                @endif
                                                {!! $errors->first('prgm_name', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">
                                                    {{ trans('backend.add_program.anchor_limit') }}
                                                    <span class="error_message_label">*</span> </label>
                                                <div class="relative">
                                                <a href="javascript:void(0);" class="remaining"><i class="fa fa-inr" aria-hidden="true"></i></a> {!! Form::text('anchor_limit', number_format($program->anchor_limit), ['class'=>'form-control number_format','placeholder'=>trans('backend.add_program.enter_anchor_limit')])!!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row"> 
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="industry_id"> {{ trans('backend.add_program.industry') }}
                                                    <span class="error_message_label">*</span></label>
                                                {!! Form::select('industry_id', [''=>trans('backend.please_select')] + $industryList, $program->industry_id, ['class'=>'form-control industry_change']) !!}
                                                {!! $errors->first('industry_id', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sub_industry_id">
                                                    {{ trans('backend.add_program.sub_industry') }}
                                                    <span class="error_message_label"></span> </label>
                                                {!! Form::select('sub_industry_id', [''=>trans('backend.please_select')]+ $subIndustryList, $program->sub_industry_id, ['class'=>'form-control sub_industry']) !!}
                                                {!! $errors->first('sub_industry_id', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row"> 
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">
                                                    {{ trans('backend.add_program.fldf_applicable') }}
                                                    <span class="error_message_label">*</span>
                                                </label>
                                                <div class="">
                                                    <div class="form-check-inline">
                                                        <label class="form-check-label fnt">
                                                            {!! Form::radio('is_fldg_applicable',1, $program->is_fldg_applicable == '1' ? true : false, ['class'=>'form-check-input'])!!} Yes
                                                        </label>
                                                    </div>
                                                    <div class="form-check-inline ">
                                                        <label class="form-check-label fnt">
                                                            {!! Form::radio('is_fldg_applicable',0,$program->is_fldg_applicable == '0' ? true : false, ['class'=>'form-check-input'])!!} No
                                                        </label>
                                                    </div>
                                                </div>
                                                <label id="is_fldg_applicable-error" class="error" for="is_fldg_applicable"></label>
                                            </div>
                                        </div>
                                        @if ($action_type != 'anchor_program')
                                        <div class="col-md-6">
                                            <div class="form-group password-input">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="txtCreditPeriod">Status <span class="error_message_label">*</span> </label>
                                                        {!! Form::select('status', [''=>trans('backend.please_select') ,1=>'Active',0 =>'In Active'],
                                                        isset($program->status) ? $program->status : null, ['class'=>'form-control required']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                
                                <div class="ima"></div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="text-right mt-3">

                                                <button id="close_btn" type="button" class="btn btn-secondary btn-sm">Cancel</button> {!! Form::submit('Save', ['class'=>'btn btn-primary btn-sm submit' ,'id'=>'pre3']) !!}

                                            </div>
                                        </div>
                                    </div>
                                </div>                            
                            {!! Form::hidden('anchor_id', $anchor_id) !!}
                            {!! Form::hidden('program_id', $program->prgm_id, ['id' => 'program_id']) !!}
                            {!! Form::hidden('utilized_amount', $utilizedLimit, ['id'=>'utilized_amount']) !!}
                            {!! Form::hidden('type', $action_type) !!}
                             
                            {{ Form::close() }}

                        </div>
@endsection 

@section('additional_css')
<link rel="stylesheet" href="{{url('backend/assets/css/custom.css')}}?v="{{Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'd-m-Y h:i A')}}"" /> 
@endsection 

@section('jscript')
<script>
    var messages = {
        get_sub_industry: "{{ URL::route('get_sub_industry') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        please_select: "{{ trans('backend.please_select') }}",        
        is_accept: "{{ Session::get('is_accept') }}",    
        error_code : "{{ Session::get('error_code') }}",
        msg : "{{ Session::pull('msg') }}",
        route_url : "{{ Session::pull('route_url') }}",
    };
    $(document).on('input', '.format_with_decimal', function(event) {
        if(event.which >= 37 && event.which <= 40) return;
        $(this).val(function(index, value) {
            thisval = value.replace(/[^0-9.]/g, '');
            let decimal_part = thisval.split('.')[0];
            let float_part = thisval.split('.')[1];
            formatted_num = decimal_part.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            if (thisval.indexOf('.') != -1) {
                formatted_num = formatted_num + "." + float_part.substr(0,2); 
            }
            if (event.originalEvent.data == '.' && thisval.indexOf('.') == -1) {
                formatted_num = formatted_num + '.';
            }
           return formatted_num;
        });
    })
    
    $(document).ready(function(){    
        if(messages.is_accept == 1){         
           parent.jQuery("#iframeMessage").html('<div class=" alert-success alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+messages.msg+'</div>');
           //parent.oTable.draw(); 
           parent.jQuery("#editProgram").modal('hide');
           parent.jQuery("#modifyProgramLimit").modal('hide');
           parent.$('.isloader').hide();        
           parent.window.location = messages.route_url;
        }
        
        if(messages.error_code != ''){
            jQuery("#iframeMessage").show();
            jQuery("#iframeMessage").html('<div class=" alert-success alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+messages.msg+'</div>');
        }
        
        $('#close_btn').click(function() {            
            parent.$('#editProgram').modal('hide');
        });
    })        
</script>
<script src="{{ asset('backend/assets/js/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/common.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend/js/lms/program.js') }}" type="text/javascript"></script>
@endsection