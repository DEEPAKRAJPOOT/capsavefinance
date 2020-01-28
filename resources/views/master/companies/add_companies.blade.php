@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="companiesForm" name="companiesForm" method="POST" action="{{route('save_companies')}}" target="_top">
        @csrf

        {!! Form::hidden('company_id' , isset($comData['company_id']) ? $comData['company_id'] : null )  !!}
        <div class="row">
            <div class="form-group col-md-6">
                <label for="cmp_name">Company Name <span class="mandatory">*</span></label>
                <input type="text" class="form-control" id="cmp_name" name="cmp_name" placeholder="Enter Company Name" maxlength="50" value="{{ isset($comData['cmp_name']) ? $comData['cmp_name'] : ''}}">
                {!! $errors->first('cmp_name', '<span class="error">:message</span>') !!}
            </div>
            <div class="form-group col-md-6">
                <label for="cmp_add">Company Address <span class="mandatory">*</span></label>
                <input type="text" class="form-control" id="cmp_add" name="cmp_add" placeholder="Enter Company Address" maxlength="50"value="{{ isset($comData['cmp_add']) ? $comData['cmp_add'] : ''}}">
                {!! $errors->first('cmp_add', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="gst_no">GST No. <span class="mandatory">*</span></label>
                <input type="text" class="form-control gstnumber" id="gst_no" name="gst_no" placeholder="Enter GST No." maxlength="50" value="{{ isset($comData['gst_no']) ? $comData['gst_no'] : ''}}">
                {!! $errors->first('gst_no', '<span class="error">:message</span>') !!}
            </div>
            <div class="form-group col-md-6">
                <label for="pan_no">PAN No. <span class="mandatory">*</span></label>
                <input type="text" class="form-control pannumber" id="pan_no" name="pan_no" placeholder="Enter Pan No." maxlength="50" value="{{ isset($comData['pan_no']) ? $comData['pan_no'] : ''}}">
                {!! $errors->first('pan_no', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="cin_no">CIN No. <span class="mandatory">*</span></label>
                <input type="text" class="form-control cinnumber" id="cin_no" name="cin_no" placeholder="Enter CIN No." maxlength="50" value="{{ isset($comData['cin_no']) ? $comData['cin_no'] : ''}}">
                {!! $errors->first('cin_no', '<span class="error">:message</span>') !!}
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

            var values = $(this).val();
            var gstnoformat = new RegExp('^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$');

            if (gstnoformat.test(values)) {
                return true;
            } else {
                $('.gst_no_error').remove();
                $(this).after('<label id="gst_no-error" class="error gst_no_error " for="gst_no">Please Enter Valid GSTIN Number</label>');
                $(this).val('');
                $(this).focus();
            }

        });

        $(this).on('change', ".pannumber", function () {
            var values = $(this).val();
            var pannoformat = new RegExp('^[A-Z]{5}[0-9]{4}[A-Z]{1}$');

            if (pannoformat.test(values)) {
                return true;
            } else {
                $('.pan_no_error').remove();
                $(this).after('<label id="pan_no-error" class="error pan_no_error " for="pan_no">Please Enter Valid PAN Number</label>');
                $(this).val('');
                $(this).focus();
            }

        });

        $(this).on('change', ".cinnumber", function () {
            var values = $(this).val();
            var cinnoformat = new RegExp('^[L,U]{1}[0-9]{5}[A-Z]{2}[0-9]{4}[C,P,T,L,S,G,O,N]{3}[0-9]{6}$');

            if (cinnoformat.test(values)) {
                return true;
            } else {
                $('.cin_no_error').remove();
                $(this).after('<label id="cin_no-error" class="error cin_no_error " for="cin_no">Please Enter Valid CIN Number</label>');
                $(this).val('');
                $(this).focus();
            }

        });
//        

        $('#companiesForm').validate({// initialize the plugin
            rules: {
                'cmp_name': {
                    required: true,
                },
                'cmp_add': {
                    required: true,
                },
                'gst_no': {
                    required: true,
                    maxlength: 15,
                },
                'pan_no': {
                    required: true,
                    maxlength: 10,
                },
                'cin_no': {
                    required: true,
                    maxlength: 21,
                },
                'ifsc_code': {
                    required: true,
                },
                'is_active': {
                    required: true,
                },
            },
            messages: {
                'cmp_name': {
                    required: "Please Enter Company Name",
                },
                'cmp_add': {
                    required: "Please Enter Company Address",
                },
                'gst_no': {
                    required: "Please Enter GST No",
                },
                'pan_no': {
                    required: "Please Enter Pan No",
                },
                'cin_no': {
                    required: "Please Enter CIN No",
                },
                'bank_acc_no': {
                    required: "Please Enter Bank A/C No",
                },
                'conf_bank_acc_no': {
                    required: "Please Confirm Your Bank A/C No",
                    equalTo: 'Confirm Bank A/C No. and Bank A/C No. do not match.'
                },
                'ifsc_code': {
                    required: "Please Enter IFSC Code",
                },
                'is_active': {
                    required: "Please Select Status of Company",
                }
            }
        });
    });
</script>
@endsection