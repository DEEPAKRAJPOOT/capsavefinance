@extends('layouts.backend.admin_popup_layout')
@section('content')
{!!
Form::open(
[
'route' => 'save_company_bank_account',
'name' => 'bank_account',
'autocomplete' => 'off', 
'id' => 'bank_account'
]
)
!!}

{!! Form::hidden('bank_account_id', isset($bankAccount->bank_account_id) ? \Crypt::encrypt($bankAccount->bank_account_id)  : null, ['id'=>'acc_id']) !!}
{!! Form::hidden('comp_addr_id', isset($companyId) ? \Crypt::encrypt($companyId)  : null,['id'=>'company_id'] ) !!}

<div class="modal-body text-left">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="acc_name">Account Holder Name
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('acc_name', 
                isset($bankAccount->acc_name) ? $bankAccount->acc_name : null
                ,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Account Holder Name', 'maxlength'=>50]) !!}
                {!! $errors->first('acc_name', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="bank_id">Bank Name
                    <span class="mandatory">*</span>
                </label>
                {!! Form::select('bank_id', $bank_list,isset($bankAccount->bank_id) ? $bankAccount->bank_id : null,['class'=>'form-control form-control-sm'])!!}
                {!! $errors->first('bank_id', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="acc_no">Account Number
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('acc_no', isset($bankAccount->acc_no) ? $bankAccount->acc_no : null,
                ['class'=>'form-control form-control-sm' ,
                'id'=>'account_no','placeholder'=>'Enter Account Number', 'maxlength'=>18, 'autocomplete' => 'off']) !!}
                {!! $errors->first('acc_no', '<span class="error">:message</span>') !!}
            </div>
        </div>


        <div class="col-md-6">
            <div class="form-group">
                <label for="confim_acc_no">Confirm Account Number
                    <span class="mandatory">*</span>
                </label>
                <input type="password" style="display:none">
                {!! Form::password('confim_acc_no',
                ['class'=>'form-control form-control-sm', 'id'=>'confim_acc_no', 'placeholder'=>'Enter Account Number', 'maxlength'=>18, 'autocomplete' => 'off']) !!}

            </div>
        </div>


        <div class="col-md-6">
            <div class="form-group">
                <label for="ifsc_code">IFSC Code
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('ifsc_code', isset($bankAccount->ifsc_code) ? $bankAccount->ifsc_code : null,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter IFSC Code', 'maxlength'=>11, 'autocomplete' => 'off', 'id'=>'ifsc_code']) !!}
                {!! $errors->first('ifsc_code', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="branch_name">Branch Name
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('branch_name',isset($bankAccount->branch_name) ? $bankAccount->branch_name : null,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Branch Name', 'maxlength'=>30]) !!}
                {!! $errors->first('branch_name', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="micr_code">MICR Code
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('micr_code', isset($bankAccount->micr_code) ? $bankAccount->micr_code : null,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter MICR Code', 'maxlength'=>11, 'autocomplete' => 'off', 'id'=>'micr_code']) !!}
                {!! $errors->first('micr_code', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="acc_type">Account TYpe
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('acc_type',isset($bankAccount->acc_type) ? $bankAccount->acc_type : null,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Branch Name', 'maxlength'=>30]) !!}
                {!! $errors->first('acc_type', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="is_active">Status</label><br>
                {!! Form::select('is_active', [''=>'Please Select','1'=>'Active','0'=>'Inactive'],isset($bankAccount->is_active) ? $bankAccount->is_active : null,['class'=>'form-control form-control-sm']) !!}
                {!! $errors->first('is_active', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="sponser_bank">Sponser Branch Code
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('sponser_bank',isset($bankAccount->sponser_bank_code) ? $bankAccount->sponser_bank_code : null,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Sponser Branch Code', 'maxlength'=>30]) !!}
                {!! $errors->first('sponser_bank', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <span style="background-color:yellow">Do you want to set this bank account as by default for all transactions?</span>
                <span style="margin-left:10px"></span><input type='checkbox' name='by_default' id='by_name' {{ ($bankAccount['is_default']) == 1 ? 'checked' : '' }} value='1'>
            </div>
        </div>
    </div>
    {!! Form::submit('Submit',['class'=>'btn btn-success float-right btn-sm mt-3']) !!}
</div>
{!! Form::close() !!}
@endsection
@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/common.js') }}" type="text/javascript"></script>

@php 
$operation_status = session()->get('operation_status', false);
$messages = session()->get('message', false);
@endphp
@if($operation_status == config('common.YES'))

<script>
try {
    var p = window.parent;
    p.jQuery('#iframeMessage').html('{!! Helpers::createAlertHTML($messages, 'success') !!}');
    p.jQuery('#add_bank_account').modal('hide');
    p.window.location.reload();
    p.reloadDataTable();
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
</script>
@endif
<script>

    var messages = {
        check_bank_acc_exist: "{{ URL::route('check_bank_acc_exist') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script>
    $(document).on('input', '.number_format', function (event) {
        // skip for arrow keys
        if (event.which >= 37 && event.which <= 40)
            return;

        // format number
        $(this).val(function (index, value) {
            return value.replace(/\D/g, "");
        });
    });
    
    jQuery.validator.addMethod("alphanumeric", function(value, element) {
        return this.optional(element) || /^[A-Za-z0-9]+$/i.test(value);
    }, "Letters and numbers only please");

    $('#confim_acc_no').val($('#account_no').val());

    $.validator.addMethod("alphanumericonly", function (value, element) {
        return this.optional(element) || /^[A-Za-z0-9]*$/.test(value);
    });

    $.validator.addMethod("unique_acc", function (value, element) {
        var acc_no = value;
        var comp_id = $('#company_id').val();
        var acc_id = $('#acc_id').val();
        let status = false;
        $.ajax({
            url: messages.check_bank_acc_exist,
            type: 'POST',
            async: false,
            cache: false,
            datatype: 'json',
            data: {
                'acc_no': acc_no,
                'comp_id': comp_id,
                'acc_id': acc_id,
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
    
    $('#confim_acc_no').on("cut copy paste",function(e) {
      e.preventDefault();
   });

    $(function () {
        $("form[name='bank_account']").validate({
            rules: {
                'acc_name': {
                    required: true,
                    lettersonly: true,
                    maxlength: 50
                },
                'acc_no': {
                    required: true,
                    alphanumeric: true,
                    minlength: 6,
                    maxlength: 18,
                    unique_acc: true
                },
                'confim_acc_no': {
                    required: true,
                    minlength: 6,
                    maxlength: 18,
                    equalTo: "#account_no"
                },
                'bank_id': {
                    required: true
                },
                'ifsc_code': {
                    required: true,
                    alphanumericonly: true,
                    maxlength: 11
                },
                'micr_code': {
                    required: true,
                    alphanumericonly: true,
                    maxlength: 11
                },
                'acc_type': {
                    required: true,
                },
                'branch_name': {
                    required: true,
                    maxlength: 30
                },
                'is_active': {
                    required: true
                },
                'sponser_bank':{
                    required: true
                }
            },
            messages: {
                acc_no: {
                    unique_acc: 'This account number is already exists.'
                },
                confim_acc_no: {
                    equalTo: 'Account number do not match.'
                },
                ifsc_code: {
                    alphanumericonly: 'please enter alphanumeric characters.',
                    maxlength: 'IFSC code should be only 11 characters.'
                },
                micr_code: {
                    alphanumericonly: 'please enter alphanumeric characters.',
                    maxlength: 'IFSC code should be only 11 characters.'
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
        
        if (/firefox/.test(navigator.userAgent.toLowerCase())) {            
            $("#account_no").attr('autocomplete', 'new-password');
            $("#confim_acc_no").attr('autocomplete', 'new-password');
            $("#ifsc_code").attr('autocomplete', 'new-password');
        }         
    });
</script>
@endsection