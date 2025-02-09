@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="companiesForm" name="companiesForm" method="POST" action="{{route('save_companies')}}" target="_top">
        @csrf

        {!! Form::hidden('comp_addr_id' , isset($comData['comp_addr_id']) ? $comData['comp_addr_id'] : null, ['id'=>'comp_addr_id'])  !!}
        <div class="row">
            <div class="form-group col-md-6">
                <label for="cmp_name">Company Name <span class="mandatory">*</span></label>

                <input type="text" class="form-control" id="cmp_name" name="cmp_name" placeholder="Enter Company Name" maxlength="50" value="{{ isset($comData['cmp_name']) ? $comData['cmp_name'] : 'CAPSAVE FINANCE PRIVATE LIMITED'}}" readonly="readonly">

                {!! $errors->first('cmp_name', '<span class="error">:message</span>') !!}
            </div>
            <div class="form-group col-md-6">
                <label for="cmp_add">Company Address <span class="mandatory">*</span></label>
                <textarea class="form-control" id="cmp_add" name="cmp_add" rows="1" cols="50" maxlength="2000" placeholder="Enter Company Address">{{ isset($comData['cmp_add']) ? $comData['cmp_add'] : old('cmp_add')}}</textarea>
                {!! $errors->first('cmp_add', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="cmp_email">Registered Email <span class="mandatory">*</span></label>
                <input type="email" class="form-control" id="cmp_email" name="cmp_email" placeholder="Enter Comapny Email ID" maxlength="50" value="{{ isset($comData['cmp_email']) ? $comData['cmp_email'] : old('cmp_email')}}">
                {!! $errors->first('cmp_email', '<span class="error">:message</span>') !!}
            </div>
            <div class="form-group col-md-6">
                <label for="cmp_mobile">Phone No.<span class="mandatory">*</span></label>
                <input type="text" class="form-control number_format" id="cmp_mobile" name="cmp_mobile" placeholder="Enter Comapny Phone No." maxlength="15" value="{{ isset($comData['cmp_mobile']) ? $comData['cmp_mobile'] : old('cmp_mobile')}}">
                {!! $errors->first('cmp_mobile', '<span class="error">:message</span>') !!}
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
                <label for="pincode">Pin Code</label>
                <input type="text" class="form-control number_format" id="pincode" name="pincode" placeholder="Enter Pin Code" maxlength="6" value="{{ isset($comData['pincode']) ? $comData['pincode'] : old('pincode')}}">
                {!! $errors->first('pincode', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="charge_prefix">Charge Prefix </label>
                <input type="text" class="form-control" id="charge_prefix" name="charge_prefix" placeholder="Enter Charge Prefix" maxlength="3" value="{{ isset($comData['charge_prefix']) ? $comData['charge_prefix'] : old('charge_prefix') }}" onkeyup="return checkChargeValidation();">

            </div>
            <div class="form-group col-md-6">
                <label for="interest_prefix">Interest Prefix</label>
                <input type="text" class="form-control" id="interest_prefix" name="interest_prefix" placeholder="Enter Interest Prefix" maxlength="3" value="{{ isset($comData['interest_prefix']) ? $comData['interest_prefix'] : old('interest_prefix')}}" onkeyup="return checkInterestValidation();">
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
            <div class="form-group col-md-6">
                <label for="utility_code">Utility Code<span class="mandatory">*</span></label>
                <input type="text" class="form-control" id="utility_code" name="utility_code" placeholder="Enter Utility Code" maxlength="50" value="{{ isset($comData['utility_code']) ? $comData['utility_code'] : old('utility_code') }}">
                {!! $errors->first('utility_code', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="member_id">Member Id<span class="mandatory">*</span></label>
                <input type="text" class="form-control" id="member_id" name="member_id" placeholder="Enter Member Id" maxlength="50" value="{{ isset($comData['city']) ? $comData['member_id'] : old('member_id') }}">
                {!! $errors->first('member_id', '<span class="error">:message</span>') !!}
            </div>
            <div class="form-group col-md-6">
                <label for="member_branch_code">Member Branch Code<span class="mandatory">*</span></label>
                <input type="text" class="form-control" id="member_branch_code" name="member_branch_code" placeholder="Enter Member Branch Code" maxlength="50" value="{{ isset($comData['member_branch_code']) ? $comData['member_branch_code'] : old('member_branch_code') }}">
                {!! $errors->first('member_branch_code', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
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
            <div class="form-group col-md-12 text-right">
                <input type="submit" class="btn btn-success btn-sm" name="add_company" id="add_company" value="Submit"/>
            </div>
        </div>
    </form>
</div>
@endsection
@section('jscript')
<script>
    var messages = {
        check_comp_add_exist: "{{ URL::route('check_comp_add_exist') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script type="text/javascript">
    
    $(document).ready(function () {

        $(this).on('blur', ".gstnumber", function () {
            $('label.gst_no_error, label#gst_no_error').remove();
            var values = $(this).val();
            var gstnoformat = new RegExp('^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$');

            if (/^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/.test(values)) {
                if (gstnoformat.test(values)) {
                    return true;
                } else {
                     $('label.error, label.gst_no_error').remove();
                    $(this).after('<label id="gst_no_error" class="error gst_no_error" for="gst_no">Please enter valid GSTIN Number</label>');
                    $(this).focus();
                }
            } else {

                $('label.error, label.gst_no_error').remove();

                $(this).after('<label id="gst_no_error" class="error gst_no_error" for="gst_no">Special characters not allowed</label>');
                $(this).focus();
            }
        });

        $(this).on('blur', ".pannumber", function () {
            $('label.pan_no_error, label#pan_no_error').remove();
            var values = $(this).val();
            var pannoformat = new RegExp('^[A-Z]{5}[0-9]{4}[A-Z]{1}$');

            if (/^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/.test(values)) {
                if (pannoformat.test(values)) {
                    return true;
                } else {
                    $('label.error, label.pan_no_error').remove();
                    $(this).after('<label id="pan_no_error" class="error pan_no_error " for="pan_no">Please enter valid PAN Number</label>');
                    $(this).focus();
                }
            } else {
                $('label.error, label.pan_no_error').remove();
                $(this).after('<label id="pan_no_error" class="error pan_no_error " for="pan_no">Special charactes not allowed</label>');
                $(this).focus();
            }

        });

        $(this).on('blur', ".cinnumber", function () {
            $('label.cin_no_error, label#cin_no_error').remove();
            var values = $(this).val();
            var cinnoformat = new RegExp('^[L,U]{1}[0-9]{5}[A-Z]{2}[0-9]{4}[C,P,T,L,S,G,O,N]{3}[0-9]{6}$');

            if (/^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/.test(values)) {
                if (cinnoformat.test(values)) {
                    return true;
                } else {
                    $('label.error, label.cin_no_error').remove();
                    $(this).after('<label id="cin_no_error" class="error cin_no_error " for="cin_no">Please enter valid CIN Number</label>');
                    $(this).focus();
                }
            } else {
                $('label.error, label.cin_no_error').remove();
                $(this).after('<label id="cin_no_error" class="error cin_no_error " for="cin_no">Special characters not allowed</label>');
                $(this).focus();
            }

        });

        $.validator.addMethod("unique_add", function (value, element) {
            var comp_add = value;
            var cmp_name = $('#cmp_name').val();
            var comp_id = $('#comp_addr_id').val();
            var status = false;
            $.ajax({
                url: messages.check_comp_add_exist,
                type: 'POST',
                datatype: 'json',
                async: false,
                cache: false,
                data: {
                    'comp_add': comp_add,
                    'cmp_name': cmp_name,
                    'comp_id': comp_id,
                    '_token': messages.token
                },
                success: function (response) {
                    if (response['status'] === 'true') {
                        status = true;
                    }
                }
            });
            return this.optional(element) || (status === true);
        });

        $.validator.addMethod("alphanumericdot", function (value, element) {
            return this.optional(element) || /^[A-Za-z0-9 -.,]*$/.test(value);
        });
        
        $(this).on('input', '.number_format', function (event) {
            if (event.which >= 37 && event.which <= 40)
                return;

            $(this).val(function (index, value) {
                return value.replace(/\D/g, "");
            });
        });

        $('#companiesForm').validate({// initialize the plugin
            rules: {
                'cmp_name': {
                    required: true
                },
                'cmp_add': {
                    required: true,
                    // alphanumericdot: true,
                    unique_add: true
                },
                'cmp_email': {
                    required: true,
                    email: true,
                    maxlength: 50
                },
                'cmp_mobile': {
                    required: true,
                    number: true,
                    maxlength: 15
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
                'is_active': {
                    required: true
                },
                'state': {
                    required: true
                },
                'city': {
                    required: true
                },
                'charge_prefix': {
                    required: true
                },
                'interest_prefix': {
                    required: true
                },
                'is_reg': {
                    required: true
                },
                'utility_code': {
                    required: true
                },
                'member_id': {
                    required: true
                },
                'member_branch_code': {
                    required: true
                }
            },
            messages: {
                'cmp_name': {
                    required: "Please enter Company Name"
                },
                'cmp_add': {
                    required: "Please enter Company Address",
                    // alphanumericdot: "Some special characters are allowed",
                    unique_add: 'The company branch is already present at this address.'
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
                'is_active': {
                    required: "Please select Status of Company"
                },
                'state': {
                    required: "Please select State"
                },
                'city': {
                    required: "Please enter City"
                },
                'charge_prefix': {
                    required: "Please enter Charge Prefix"
                },
                'interest_prefix': {
                    required: "Please enter Interest Prefix"
                },
                'utility_code': {
                    required: "Please enter Utility Code"
                },
                'member_branch_code': {
                    required: "Please enter Member Branch Code"
                },
                'member_id': {
                    required: "Please enter Member Id"
                }
            }
        });
    });

    var alphaNum = /^[a-zA-Z0-9]+$/;
    function checkChargeValidation() {
        let charge_prefix = document.getElementById('charge_prefix').value;

        if(!charge_prefix.match(alphaNum)) {
               document.getElementById('charge_prefix').value = "";
        } else if (charge_prefix.length >= 4) {
            document.getElementById('charge_prefix').value = "";
        };
    }
    function checkInterestValidation() {
        let interest_prefix = document.getElementById('interest_prefix').value;

        if(!interest_prefix.match(alphaNum)) {
               document.getElementById('interest_prefix').value = "";
        } else if (interest_prefix.length >= 4) {
            document.getElementById('interest_prefix').value = "";
        };
    }
</script>
@endsection