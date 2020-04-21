@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="companiesForm" name="companiesForm" method="POST" action="{{route('save_companies')}}" target="_top">
        @csrf

        {!! Form::hidden('company_id' , isset($comData['company_id']) ? $comData['company_id'] : null )  !!}
        <div class="row">
            <div class="form-group col-md-6">
                <label for="cmp_name">Company Name <span class="mandatory">*</span></label>
                <input type="text" class="form-control" id="cmp_name" name="cmp_name" placeholder="Enter Company Name" maxlength="50" value="{{ isset($comData['cmp_name']) ? $comData['cmp_name'] : old('cmp_name')}}">
                {!! $errors->first('cmp_name', '<span class="error">:message</span>') !!}
            </div>
            <div class="form-group col-md-6">
                <label for="cmp_add">Company Address <span class="mandatory">*</span></label>
                <textarea class="form-control" id="cmp_add" name="cmp_add" rows="1" cols="50" maxlength="100" placeholder="Enter Company Address">{{ isset($comData['cmp_add']) ? $comData['cmp_add'] : old('cmp_add')}}</textarea>
                {!! $errors->first('cmp_add', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="gst_no">GST No. <span class="mandatory">*</span></label>
                <input type="text" class="form-control gstnumber" id="gst_no" name="gst_no" placeholder="Enter GST No." maxlength="15" value="{{ isset($comData['gst_no']) ? $comData['gst_no'] : old('gst_no')}}">
                {!! $errors->first('gst_no', '<span class="error">:message</span>') !!}
            </div>
            <div class="form-group col-md-6">
                <label for="pan_no">PAN No. <span class="mandatory">*</span></label>
                <input type="text" class="form-control pannumber" id="pan_no" name="pan_no" placeholder="Enter Pan No." maxlength="10" value="{{ isset($comData['pan_no']) ? $comData['pan_no'] : old('pan_no')}}">
                {!! $errors->first('pan_no', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="cin_no">CIN No. <span class="mandatory"></span></label>
                <input type="text" class="form-control cinnumber" id="cin_no" name="cin_no" placeholder="Enter CIN No." maxlength="21" value="{{ isset($comData['cin_no']) ? $comData['cin_no'] : old('cin_no')}}">
                {!! $errors->first('cin_no', '<span class="error">:message</span>') !!}
            </div>
            <div class="form-group col-md-6">
                <label for="chrg_type">State<span class="mandatory">*</span></label><br />
                <select class="form-control" name="state" id="state">
                    <option value="">Please Select</option>
                    @foreach($state as $key=>$val)
                    @php
                    if($key == $comData['state']['name']){
                    $sel = 'selected';
                    }else{
                    $sel = '';
                    }
                    @endphp
                    <option  value="{{$val}}" {{$sel}}>{{$key}}</option>
                    @endforeach
                </select>

            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="cin_no">City <span class="mandatory">*</span></label>
                <input type="text" class="form-control" id="city" name="city" placeholder="Enter City" maxlength="50" value="{{ isset($comData['city']) ? $comData['city'] : old('city') }}">

            </div>
            <div class="form-group col-md-6">
                <label for="chrg_type">Status <span class="mandatory">*</span></label><br />
                <select class="form-control" name="is_active" id="is_active">
                    <option value="" selected>Select</option>
                    <option {{$comData['is_active'] == 1 ? 'selected' : ''}} value="1">Active</option>
                    <option {{$comData['is_active'] == 0 ? 'selected' : ''}} value="0">In-Active</option>
                </select>
                {!! $errors->first('is_active', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="is_reg">Is registered office<span class="mandatory">*</span></label>
                <select class="form-control" name="is_reg" id="is_reg">
                    <option value="" selected>Select</option>
                    <option {{$comData['is_reg'] == 1 ? 'selected' : ''}} value="1">Yes</option>
                    <option {{$comData['is_reg'] == 0 ? 'selected' : ''}} value="0">No</option>
                </select>
                {!! $errors->first('is_reg', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 text-right">
                <input type="submit" class="btn btn-success btn-sm" name="add_company" id="add_company" value="Submit"/>
            </div>
        </div>
    </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function () {

        $(this).on('change', ".gstnumber", function () {
            $('.gst_no_error, #gst_no_error').remove();
            var values = $(this).val();
            var gstnoformat = new RegExp('^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$');

            if (/^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/.test(values)) {
                if (gstnoformat.test(values)) {
                    return true;
                } else {
                    $(this).after('<label id="gst_no-error" class="error gst_no_error" for="gst_no">Please Enter Valid GSTIN Number</label>');
                    $(this).val(values);
                    $(this).focus();
                }
            } else {
                console.log('special characters are not allowed');
            }

        });

        $(this).on('change', ".pannumber", function () {
            $('.pan_no_error, #pan_no_error').remove();
            var values = $(this).val();
            var pannoformat = new RegExp('^[A-Z]{5}[0-9]{4}[A-Z]{1}$');

            if (/^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/.test(values)) {
                if (pannoformat.test(values)) {
                    return true;
                } else {
                    $(this).after('<label id="pan_no-error" class="error pan_no_error " for="pan_no">Please Enter Valid PAN Number</label>');
                    $(this).val('');
                    $(this).focus();
                }
            } else {
                console.log('special characters are not allowed');
            }

        });

        $(this).on('change', ".cinnumber", function () {
            $('.cin_no_error, #cin_no_error').remove();
            var values = $(this).val();
            var cinnoformat = new RegExp('^[L,U]{1}[0-9]{5}[A-Z]{2}[0-9]{4}[C,P,T,L,S,G,O,N]{3}[0-9]{6}$');

            if (/^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/.test(values)) {
                if (cinnoformat.test(values)) {
                    return true;
                } else {
                    $(this).after('<label id="cin_no-error" class="error cin_no_error " for="cin_no">Please Enter Valid CIN Number</label>');
                    $(this).val('');
                    $(this).focus();
                }
            } else {
                console.log('special characters are not allowed');
            }

        });
//        

        $('#companiesForm').validate({// initialize the plugin
            rules: {
                'cmp_name': {
                    required: true
                },
                'cmp_add': {
                    required: true
                },
                'gst_no': {
                    required: true,
                    maxlength: 15
                },
                'pan_no': {
                    required: true,
                    maxlength: 10
                },
                'cin_no': {
                    required: true
                },
                'ifsc_code': {
                    required: true
                },
                'is_active': {
                    required: true
                },
                'state': {
                    required: true
                },
                'city': {
                    required: true
                },
                'is_reg': {
                    required: true
                }
            },
            messages: {
                'cmp_name': {
                    required: "Please enter Company Name"
                },
                'cmp_add': {
                    required: "Please enter Company Address"
                },
                'gst_no': {
                    required: "Please enter GST Number",
                    maxlength: "GST Number can not more than 15 characters"
                },
                'pan_no': {
                    required: "Please enter Pan Number",
                    maxlength: "PAN Number can not more than 10 characters"
                },
                'cin_no': {
                    required: "Please enter CIN Number",
                    maxlength: "CIN Number can not more than 21 characters"
                },
                'bank_acc_no': {
                    required: "Please enter Bank A/C Number"
                },
                'conf_bank_acc_no': {
                    required: "Please confirm Your Bank A/C No.",
                    equalTo: 'Confirm Bank A/C No. and Bank A/C No. do not match.'
                },
                'ifsc_code': {
                    required: "Please enter IFSC Code"
                },
                'is_active': {
                    required: "Please select Status of Company"
                },
                'state': {
                    required: "Please select State"
                },
                'city': {
                    required: "Please enter City"
                }
            }
        });
    });
</script>
@endsection