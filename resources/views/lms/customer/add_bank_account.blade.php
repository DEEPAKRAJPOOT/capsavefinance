@extends('layouts.backend.admin_popup_layout')
@section('content')

@foreach ($errors->all() as $error)
    <div class="alert alert-danger" role="alert">{{ $error }}</div>
@endforeach

{!!
Form::open(
[
'route' => 'save_bank_account',
'name' => 'bank_account',
'autocomplete' => 'off', 
'id' => 'bank_account',
'files' => true
]
)
!!}
{!! Form::hidden('bank_account_id', isset($bankAccount->bank_account_id) ? \Crypt::encrypt($bankAccount->bank_account_id)  : null, ['id'=>'bank_account_id'] ) !!}
<input type="hidden" name="user_id" value="{{ request()->get('user_id') }}">
<div class="modal-body text-left">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Account Holder Name
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('acc_name', 
                isset($bankAccount->acc_name) ? $bankAccount->acc_name : null
                ,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Account Holder Name']) !!}
                {!! $errors->first('acc_name', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Account Number
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('acc_no', isset($bankAccount->acc_no) ? $bankAccount->acc_no : null,
                ['class'=>'form-control form-control-sm number_format' ,
                'id'=>'acc_no','placeholder'=>'Enter Account Number', 'maxlength' => "18", 'autocomplete' => 'off']) !!}
                {!! $errors->first('acc_no', '<span class="error">:message</span>') !!}
            </div>
        </div>
        
        @if(!isset($bankAccount->bank_account_id))
         <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Confirm Account Number
                    <span class="mandatory">*</span>
                </label>

                {!! Form::password('confim_acc_no',
                ['class'=>'form-control form-control-sm number_format' ,'placeholder'=>'Enter Account Number', 'id' => 'confim_acc_no', 'maxlength' => "18", 'autocomplete' => 'off']) !!}
                
            </div>
        </div>
        @else
            {!! Form::hidden('confim_acc_no', isset($bankAccount->acc_no) ? $bankAccount->acc_no : null,null) !!}
        @endif
       
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Bank Name
                    <span class="mandatory">*</span>
                </label>
                {!! Form::select('bank_id', $bank_list,isset($bankAccount->bank_id) ? $bankAccount->bank_id : null,['class'=>'form-control form-control-sm'])!!}
                {!! $errors->first('bank_id', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">IFSC Code
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('ifsc_code', isset($bankAccount->ifsc_code) ? $bankAccount->ifsc_code : null,['class'=>'form-control form-control-sm ifsc_code' ,'placeholder'=>'Enter IFSC Code', 'id' => 'ifsc_code', 'autocomplete' => 'off']) !!}
                {!! $errors->first('ifsc_code', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Branch Name
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('branch_name',isset($bankAccount->branch_name) ? $bankAccount->branch_name : null,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Branch Name']) !!}
                {!! $errors->first('branch_name', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Status</label> <span class="mandatory">*</span><br>
                
                {!! Form::select('is_active', [''=>'Please Select','1'=>'Active','0'=>'Inactive'],isset($bankAccount->is_active) ? $bankAccount->is_active : null,['class'=>'form-control form-control-sm']) !!}
                {!! $errors->first('is_active', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Upload</label>  {{-- <span class="mandatory">*</span> --}}<br>
               
                 <input type="file" {{isset($bankAccount->bank_account_id) ?  null : '' }} class="form-control" id="customFile" name="doc_file">
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
    // p.reloadDataTable();
    p.location.reload();
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
</script>
@endif
<script>
    var messages = {
        check_bank_acc_ifsc_exist: "{{ URL::route('check_bank_acc_ifsc_exist') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script>
    
    $.validator.addMethod("unique_acc", function (value, element) {
        var acc_no = value;
        var ifsc = $("input[name='ifsc_code']").val();
        var acc_id = $('#bank_account_id').val();
        let status = false;
        $.ajax({
            url: messages.check_bank_acc_ifsc_exist,
            type: 'POST',
            async: false,
            cache: false,
            datatype: 'json',
            data: {
                'acc_no': acc_no,
                'ifsc': ifsc,
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

   $(document).on('input', '.number_format', function (event) {
        // skip for arrow keys
        if (event.which >= 37 && event.which <= 40)
            return;

        // format number
        $(this).val(function (index, value) {
            return value.replace(/\D/g, "");
        });
    });

   $.validator.addMethod("alphanumericonly", function (value, element) {
        return this.optional(element) || /^[A-Za-z0-9]*$/.test(value);
    });

    $(function () {
        
        $("form[name='bank_account']").validate({
            rules: {
                'acc_name': {
                    required: true,
                    lettersonly: true
                },
                'acc_no': {
                    required: true,
                    number: true,
                    unique_acc: true
                },
                'confim_acc_no': {
                    required: true,
                    equalTo: "#acc_no"
                    
                },
                
                'bank_id': {
                    required: true,
                },

                'ifsc_code': {
                    required: true,
                    alphanumericonly: true,
                    maxlength: 11
                },
                'branch_name': {
                    required: true,
                    lettersonly: true

                },
                'is_active': {
                    required: true,

                },
               
            },
            messages: {
                acc_no: {
                    unique_acc: 'This account number is already exists with entered IFSC Code.'
                },
                confim_acc_no:{
                    equalTo:'Confirm Account Number and Account number do not match.  '
                },
                ifsc_code: {
                    alphanumericonly: 'please enter alphanumeric characters.',
                    maxlength: 'IFSC code should be only 11 characters.'
                }

            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    });
</script>
@endsection