@extends('layouts.backend.admin_popup_layout')
@section('content')
{!!
Form::open(
[
'route' => 'save_bank_account',
'name' => 'bank_account',
'autocomplete' => 'off', 
'id' => 'bank_account'
]
)
!!}



<div class="modal-body text-left">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Account Holder Name
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('acc_name', '',['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Account Holder Name']) !!}
                {!! $errors->first('acc_name', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Account Number
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('acc_no', '',['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Account Number']) !!}
                {!! $errors->first('acc_no', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Bank Name
                    <span class="mandatory">*</span>
                </label>
                {!! Form::select('bank_id', $bank_list,'',['class'=>'form-control form-control-sm'])!!}
                {!! $errors->first('bank_id', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">IFSC Code
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('ifsc_code', '',['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter IFSC Code']) !!}
                {!! $errors->first('ifsc_code', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Branch Name
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('branch_name', '',['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Branch Name']) !!}
                {!! $errors->first('branch_name', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Status</label><br>
                {!! Form::select('is_active', [''=>'Please Select','1'=>'Active','0'=>'Inactive'],'',['class'=>'form-control form-control-sm']) !!}
                {!! $errors->first('is_active', '<span class="error">:message</span>') !!}
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
                },
                'bank_id': {
                    required: true,
                },

                'ifsc_code': {
                    required: true,

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

            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    });
</script>
@endsection