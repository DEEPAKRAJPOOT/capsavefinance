@extends('layouts.backend.admin-layout') @section('content')
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
                <li style="color:#374767;"> {{ trans('backend.mange_program.home') }} </li>
                <li style="color:#374767;"> <a href='{{ $redirectUrl }}'>  {{ trans('backend.mange_program.manage_program') }} </a></li>
                <li class="active"> {{ trans('backend.mange_program.add_program') }}</li>
            </ol>
        </div>
    </section>
    <div class="row grid-margin mt-3">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-fields">
                        <div class="active" id="details">

                            {{ Form::open(array('url' => route('save_program') ,'id'=>'addProgram')) }}

                            <div class="form-sections">
                                <div class="col-md-12">

                                    <h3 class="pull-left"><small>
                                 {{ trans('backend.add_program.add_program') }}
                                 </small>
                              </h3>

                                    <div class="clearfix"></div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="product_id">
                                                   Product Type
                                                    <span class="error_message_label">*</span></label>
                                                {!! Form::select('product_id', $productList,'',['class'=>'form-control'])!!}
                                                {!! $errors->first('product_id', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="anchor_id">
                                                    {{ trans('backend.add_program.anchor_name') }}
                                                    <span class="error_message_label">*</span></label>
                                             
                                                <select name="anchor_id" class="form-control">
                                                    <option value="" >Please select</option>                                                
                                                    @foreach($anchorList as $anchor_id => $anchor)                                             
                                                    <option value="{{ $anchor_id }}" @if($anchor['used']) disabled="disabled"  @endif >{{ $anchor['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('anchor_id', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="prgm_name">
                                                    {{ trans('backend.add_program.program_name') }}
                                                    <span class="error_message_label">*</span>
                                                </label>
                                                {!! Form::text('prgm_name','', ['class'=>'form-control','placeholder'=>"Enter Programe Name"])!!}
                                                @if(Session::has('error') && Session::get('error'))
                                                    <label class='error'>{{Session::get('error')}}</label>
                                                @endif
                                                {!! $errors->first('prgm_name', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group INR">
                                                <label for="txtCreditPeriod">
                                                    {{ trans('backend.add_program.anchor_limit') }}
                                                    <span class="error_message_label">*</span> </label>
                                                <div class="relative">
                                                <a href="javascript:void(0);" class="remaining"><i class="fa fa-inr" aria-hidden="true"></i></a> {!! Form::text('anchor_limit','', ['class'=>'form-control number_format','placeholder'=>trans('backend.add_program.enter_anchor_limit')])!!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="industry_id"> {{ trans('backend.add_program.industry') }}
                                                    <span class="error_message_label">*</span></label>
                                                {!! Form::select('industry_id', [''=>trans('backend.please_select')] + $industryList, '', ['class'=>'form-control industry_change']) !!}
                                                {!! $errors->first('industry_id', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sub_industry_id">
                                                    {{ trans('backend.add_program.sub_industry') }}
                                                    <span class="error_message_label hide"></span> </label>
                                                {!! Form::select('sub_industry_id', [''=>trans('backend.please_select')], '', ['class'=>'form-control sub_industry']) !!}
                                                {!! $errors->first('sub_industry_id', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">
                                                    {{ trans('backend.add_program.fldf_applicable') }}
                                                    <span class="error_message_label">*</span>
                                                </label>
                                                <div class="">
                                                    <div class="form-check-inline">
                                                        <label class="form-check-label fnt">
                                                            {!! Form::radio('is_fldg_applicable',1,'', ['class'=>'form-check-input'])!!} Yes
                                                        </label>
                                                    </div>
                                                    <div class="form-check-inline ">
                                                        <label class="form-check-label fnt">
                                                            {!! Form::radio('is_fldg_applicable',0,'', ['class'=>'form-check-input'])!!} No
                                                        </label>
                                                    </div>
                                                </div>
                                                <label id="is_fldg_applicable-error" class="error" for="is_fldg_applicable"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ima"></div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="text-right mt-3">

                                                <a class="btn btn-secondary btn-sm" href='{{ $redirectUrl }}'> Cancel </a> {!! Form::submit('Save and Next', ['class'=>'btn btn-primary btn-sm submit' ,'id'=>'pre3']) !!}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {!! Form::hidden('program_id', '', ['id' => 'program_id']) !!}
                            {{ Form::close() }}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection @section('additional_css')

<link rel="stylesheet" href="{{url('backend/assets/css/custom.css')}}?v="{{Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'd-m-Y h:i A')}}"" /> @endsection @section('jscript')
<script>
    var messages = {
        get_sub_industry: "{{ URL::route('get_sub_industry') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        please_select: "{{ trans('backend.please_select') }}",

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
</script>
<script src="{{ asset('backend/assets/js/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/common.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend/js/lms/program.js') }}" type="text/javascript"></script>
@endsection