@extends('layouts.backend.admin_popup_layout')
@section('content')
{!!
Form::open(
[
'route' => 'save_new_bank',
'name' => 'add_bank',
'autocomplete' => 'off', 
'id' => 'add_bank'
]
)
!!}

{!! Form::hidden('bank_id', isset($bankData->id) ? \Crypt::encrypt($bankData->id)  : null, ['id'=>'bank_id']) !!}

<div class="modal-body text-left">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="bank_name">Bank Name
                    <span class="mandatory">*</span>
                </label>
                {!! Form::text('bank_name', 
                isset($bankData->bank_name) ? $bankData->bank_name : null
                ,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Bank Name', 'maxlength'=>255]) !!}
                {!! $errors->first('bank_name', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="perfios_bank_id">Perfios Id
                    <span class="mandatory">*</span>
                </label>
                <!-- {!! Form::select('perfios_bank_id', $bank_list,isset($bankData->perfios_bank_id) ? $bankData->perfios_bank_id : null,['class'=>'form-control form-control-sm'])!!} -->
                {!! Form::text('perfios_bank_id', 
                isset($bankData->perfios_bank_id) ? $bankData->perfios_bank_id : null
                ,['class'=>'form-control form-control-sm acc_num_numeric' ,'placeholder'=>'Enter Perfios Bank Id', 'maxlength'=>10]) !!}
                {!! $errors->first('perfios_bank_id', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="is_active">Status</label><br>
                {!! Form::select('is_active', [''=>'Please Select','1'=>'Active','0'=>'Inactive'],isset($bankData->is_active) ? $bankData->is_active : null,['class'=>'form-control form-control-sm']) !!}
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
@if($operation_status == config('common.YES'))<script>
    try {
        var p = window.parent;
        p.jQuery('#addBankMaster').modal('hide');
        p.window.location.reload();
        p.jQuery('#iframeMessage').html('{!! Helpers::createAlertHTML($messages, 'success') !!}');
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
        unique_bank_master_url:"{{ route('check_unique_bank_master_url') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        id: "{{ isset($bankData->id) ? 'yes'  : '' }}",
    };
</script>
<script>
    jQuery.validator.addMethod("alphadotspace", function(value, element) {
        return this.optional(element) || /^[A-Za-z .-]+$/i.test(value);
    }, "Only letters, space, hyphen and dot allowed");

    $(".acc_num_numeric").keypress(function (e) {
      var keyCode = e.keyCode || e.which;
      var length = $(this).val().length;
      var regex = /^[0-9]+$/;
      var isValid = regex.test(String.fromCharCode(keyCode));
      if (!isValid && length < 6) {
         return false;
      }
      return isValid;
   });

    $(function () {
        $.validator.addMethod("uniqueBank",
            function(value, element, params) {
                var result = true;
                var data = {bank_name : value, _token: messages.token};
                if (params.bank_id) {
                    data['bank_id'] = params.bank_id;
                }
                $.ajax({
                    type:"POST",
                    async: false,
                    url: messages.unique_bank_master_url, // script to validate in server side
                    data: data,
                    success: function(data) {                        
                        result = (data.status == 1) ? false : true;
                    }
                });                
                return result;                
            },'Bank name is already exists'
        );

        $("form[name='add_bank']").validate({
            rules: {
                'bank_name': {
                    required: true,
                    alphadotspace: true,
                    uniqueBank: {
                        bank_id: (messages.id != '') ? $("#bank_id").val() : null
                    }
                },
                'perfios_bank_id': {
                    required: true
                },
                'is_active': {
                    required: true
                }
            },
            messages: {
                alphadotspace: {
                    required: 'Please enter bank name'
                },
                perfios_bank_id: {
                    required: 'Please enter perfios id'
                },
                is_active: {
                    required: 'Please select status'
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });        
    });
</script>
@endsection