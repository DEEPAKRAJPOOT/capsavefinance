@extends('layouts.backend.admin_popup_layout')
@section('content')
{!!
Form::open(
[
'route' => 'save_anchor_bank_account',
'name' => 'bank_account',
'autocomplete' => 'off', 
'id' => 'bank_account'
]
)
!!}

{!! Form::hidden('bank_account_id', isset($bankAccount->bank_account_id) ? \Crypt::encrypt($bankAccount->bank_account_id)  : null ) !!}
{!! Form::hidden('anchor_id', isset($anchorId) ? \Crypt::encrypt($anchorId)  : null ) !!}

<div class="modal-body text-left">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="acc_name">Account Holder Name
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
                'id'=>'account_no','placeholder'=>'Enter Account Number']) !!}
                {!! $errors->first('acc_no', '<span class="error">:message</span>') !!}
            </div>
        </div>
        
        
         <div class="col-md-6">
            <div class="form-group">
                <label for="confim_acc_no">Confirm Account Number
                    <span class="mandatory">*</span>
                </label>
                {!! Form::password('confim_acc_no',
                ['class'=>'form-control form-control-sm', 'id'=>'confim_acc_no', 'placeholder'=>'Enter Account Number']) !!}
                
            </div>
        </div>
        
       
        <div class="col-md-6">
            <div class="form-group">
                <label for="ifsc_code">IFSC Code
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('ifsc_code', isset($bankAccount->ifsc_code) ? $bankAccount->ifsc_code : null,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter IFSC Code']) !!}
                {!! $errors->first('ifsc_code', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="branch_name">Branch Name
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('branch_name',isset($bankAccount->branch_name) ? $bankAccount->branch_name : null,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Branch Name']) !!}
                {!! $errors->first('branch_name', '<span class="error">:message</span>') !!}
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
    p.reloadDataTable();
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
</script>
@endif

<script>
    $('#confim_acc_no').val($('#account_no').val());
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
                    maxlength: 15,
                },
                'confim_acc_no': {
                    required: true,
                    equalTo: "#account_no",
                    maxlength: 15,
                },
                
                'bank_id': {
                    required: true,
                },

                'ifsc_code': {
                    required: true,

                },
                'branch_name': {
                    required: true,

                },
                'is_active': {
                    required: true,

                },
            },
            messages: {
                confim_acc_no:{
                    equalTo:'Confirm Account Number and Account number do not match.  '
                }

            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    });
</script>
@endsection