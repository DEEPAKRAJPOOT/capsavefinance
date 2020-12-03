@extends('layouts.backend.admin-layout')
@section('content')


<div class="content-wrapper">
@include('backend.nach.common.section')
	<div class="row grid-margin mt-3">
		<div class="  col-md-12  ">
                     <div class="card">
                        @php
                            $users_nach_id = !empty($nachDetail) ? $nachDetail['users_nach_id'] : null;
                            $anchor_id = $anchor_id ?? null;
                            if($nachDetail != false) {
                                $role_type =  (($nachDetail->user_type == 2) ? 3 : 1);
                            }
                        @endphp
                        <div class="card">
                                <form id='nach_form' method="post" action="{{route('backend_save_nach_detail', ['acc_id' => $acc_id, 'user_id' => $user_id, 'anchor_id' => $anchor_id, 'role_type' => $role_type, 'users_nach_id' => $users_nach_id])}}">
				                @csrf
                                <div class="card-body">
                                    <div class="row">
										<div class="col-md-6">
                                            <div class="form-group">
                                                <h5>Frequency <span class="mandatory">*</span></h5>
                                                <ul class="custom-check-label">
                                                    <li>
                                                        <input id='frequency' type="radio" name="frequency" value='1' {{ !empty($nachDetail) && ($nachDetail['frequency'] == 1) ? 'checked' : '' }}>
                                                        {!! $errors->first('frequency', '<span class="error">:message</span>') !!}
                                                        <label for="frequency">Monthly
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <input type="radio" name="frequency" value='2' {{ !empty($nachDetail) && ($nachDetail['frequency'] == 2) ? 'checked' : '' }}>
                                                        {!! $errors->first('frequency', '<span class="error">:message</span>') !!}
                                                        <label for="frequency">Quaterly
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <input type="radio" name="frequency" value='3' {{ !empty($nachDetail) && ($nachDetail['frequency'] == 3) ? 'checked' : '' }}>
                                                        {!! $errors->first('frequency', '<span class="error">:message</span>') !!}
                                                        <label for="frequency">Half Yearly
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <input type="radio" name="frequency" value='4' {{ !empty($nachDetail) && ($nachDetail['frequency'] == 4) ? 'checked' : '' }}>
                                                        {!! $errors->first('frequency', '<span class="error">:message</span>') !!}
                                                        <label for="frequency">Yearly
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <input type="radio" name="frequency" value='5' {{ !empty($nachDetail) && ($nachDetail['frequency'] == 5) ? 'checked' : '' }}>
                                                        {!! $errors->first('frequency', '<span class="error">:message</span>') !!}
                                                        <label for="frequency">As & When Presented
                                                        </label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nach_date">NACH Date
                                                    <span class="mandatory">*</span>
                                                </label>
                                                {!! Form::text('nach_date', 
                                                 !empty($nachDetail) && ($nachDetail['nach_date'] != '') ? \Carbon\Carbon::parse($nachDetail['nach_date'])->format('d/m/Y') : ''
                                                ,['id'=>'nach_date', 'class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Date']) !!}
                                                {!! $errors->first('nach_date', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
										<div class="col-md-6">
                                            <div class="form-group">
                                                <h5>To Debit Tick (√) <span class="mandatory">*</span></h5>
                                                <ul class="custom-check-label">
                                                    <li>
                                                        <input id='debit_tick' type="radio" name="debit_tick" value='1' {{ !empty($nachDetail) && ($nachDetail['debit_tick'] == 1) ? 'checked' : '' }}>
                                                        {!! $errors->first('debit_tick', '<span class="error">:message</span>') !!}
                                                        <label for="debit_tick">SB
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <input id='debit_tick' type="radio" name="debit_tick" value='2' {{ !empty($nachDetail) && ($nachDetail['debit_tick'] == 2) ? 'checked' : '' }}>
                                                        {!! $errors->first('debit_tick', '<span class="error">:message</span>') !!}
                                                        <label for="debit_tick">CA
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <input id='debit_tick' type="radio" name="debit_tick" value='3' {{ !empty($nachDetail) && ($nachDetail['debit_tick'] == 3) ? 'checked' : '' }}>
                                                        {!! $errors->first('debit_tick', '<span class="error">:message</span>') !!}
                                                        <label for="debit_tick">CC
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <input id='debit_tick' type="radio" name="debit_tick" value='4' {{ !empty($nachDetail) && ($nachDetail['debit_tick'] == 5) ? 'checked' : '' }}>
                                                        {!! $errors->first('debit_tick', '<span class="error">:message</span>') !!}
                                                        <label for="debit_tick">Other
                                                        </label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="amount">Amount
                                                    <span class="mandatory">*</span>
                                                </label>
                                                {!! Form::text('amount', 
                                                 !empty($nachDetail) && ($nachDetail['amount'] != '') ? $nachDetail['amount'] : ''
                                                ,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Amount']) !!}
                                                {!! $errors->first('amount', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
					<div class="col-md-6">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Phone No.
                                                    <span class="mandatory">*</span>
                                                </label>
                                                {!! Form::text('phone_no', 
                                                 !empty($nachDetail) && ($nachDetail['phone_no'] != '') ? $nachDetail['phone_no'] : ''
                                                ,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Phone No']) !!}
                                                {!! $errors->first('phone_no', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Email
                                                    <span class="mandatory">*</span>
                                                </label>
                                                {!! Form::text('email_id', 
                                                 !empty($nachDetail) && ($nachDetail['email_id'] != '') ? $nachDetail['email_id'] : ''
                                                ,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Email Id']) !!}
                                                {!! $errors->first('email_id', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
					<div class="col-md-6">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Refference 1
                                                    <span class="mandatory">*</span>
                                                </label>
                                                {!! Form::text('reference_1', 
                                                 !empty($nachDetail) && ($nachDetail['reference_1'] != '') ? $nachDetail['reference_1'] : ''
                                                ,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Reference 1']) !!}
                                                {!! $errors->first('reference_1', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Reference 2
                                                    <span class="mandatory">*</span>
                                                </label>
                                                {!! Form::text('reference_2', 
                                                 !empty($nachDetail) && ($nachDetail['reference_2'] != '') ? $nachDetail['reference_2'] : ''
                                                ,['class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Reference 2']) !!}
                                                {!! $errors->first('reference_2', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="period_from_date">Period From
                                                    <span class="mandatory">*</span>
                                                </label>
                                                {!! Form::text('period_from', 
                                                 !empty($nachDetail) && ($nachDetail['period_from'] != null) ? \Carbon\Carbon::parse($nachDetail['period_from'])->format('d/m/Y') : ''
                                                ,['id'=>'period_from_date', 'class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Date']) !!}
                                                {!! $errors->first('period_from', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="period_to_date">Period To
                                                    <span class="mandatory">*</span>
                                                </label>
                                                {!! Form::text('period_to', 
                                                 !empty($nachDetail) && ($nachDetail['period_to'] != null) ? \Carbon\Carbon::parse($nachDetail['period_to'])->format('d/m/Y') : ''
                                                ,['id'=>'period_to_date','class'=>'form-control form-control-sm' ,'placeholder'=>'Enter Date']) !!}
                                                {!! $errors->first('period_to', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
										<div class="col-md-6">
                                            <div class="form-group">
                                                <h5>Debit Type <span class="mandatory">*</span></h5>
                                                <ul class="custom-check-label">
                                                    <li>
                                                        <input type="radio" name="debit_type" value='1' {{ !empty($nachDetail) && ($nachDetail['debit_type'] == 1) ? 'checked' : '' }}>
                                                        <label for="debit_type">Fixed Amount
                                                        {!! $errors->first('debit_type', '<span class="error">:message</span>') !!}
                                                    </li>
                                                    <li>
                                                        <input type="radio" name="debit_type" value='2' {{ !empty($nachDetail) && ($nachDetail['debit_type'] == 2) ? 'checked' : '' }}>
                                                        {!! $errors->first('debit_type', '<span class="error">:message</span>') !!}
                                                        <label for="debit_type">Maximum Amount
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">&nbsp;
                                                </label>
                                                <ul class="custom-check-label">
                                                    <li>
                                                        {!! Form::checkbox('period_until_cancelled',
                                                        1,
                                                        !empty($nachDetail) && ($nachDetail['period_until_cancelled'] == 1) ? 'checked' : ''
                                                       ,['id'=>'period_until_cancelled' ]) !!}
                                                       {!! $errors->first('period_until_cancelled', '<span class="error">:message</span>') !!}
                                                       <label for="period_until_cancelled">Period Until Cancelled
                                                    <span class="mandatory">*</span>
                                                </label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if (!$nachDetail)
                                                <input class="btn btn-success btn-sm pull-right" type='submit' name='submit' value='save'>
                                            @elseif($nachDetail->is_active == 0)
                                                <input class="btn btn-success btn-sm pull-right" type='submit' name='submit' value='save'>
                                            @else
                                                <input class="btn btn-default btn-sm pull-right mr-2" type='submit' name='submit' value='cancel'>
                                                <input class="btn btn-default btn-sm pull-right mr-2" type='submit' name='submit' value='modify'>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    </form>
				</div>
			</div>
		</div></div>
	</div>
</div>
@endsection

@section('jscript')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script>
    var messages = {
            data_not_found: "{{ trans('error_messages.data_not_found') }}",
            token: "{{ csrf_token() }}",
    };

    $.validator.addMethod('dateBefore', function (value, element, params) {
        // if end date is valid, validate it as well
        var end = $(params);
        if (!end.data('validation.running')) {
            $(element).data('validation.running', true);
            setTimeout($.proxy(

            function () {
                this.element(end);
            }, this), 0);
            // Ensure clearing the 'flag' happens after the validation of 'end' to prevent endless looping
            setTimeout(function () {
                $(element).data('validation.running', false);
            }, 0);
        }
        return this.optional(element) || this.optional(end[0]) || new Date(value) < new Date(end.val());

    }, 'Period From is samller then Period To');

    $.validator.addMethod('dateAfter', function (value, element, params) {
        // if start date is valid, validate it as well
        var start = $(params);
        if (!start.data('validation.running')) {
            $(element).data('validation.running', true);
            setTimeout($.proxy(

            function () {
                this.element(start);
            }, this), 0);
            setTimeout(function () {
                $(element).data('validation.running', false);
            }, 0);
        }
        return this.optional(element) || this.optional(start[0]) || new Date(value) > new Date($(params).val());

    }, 'Period To is larger then Period From');
    
    $("#nach_form").validate({
        rules: {
            frequency:{
                required:true
            },
            nach_date:{
                required:true
            },
            debit_tick:{
                required:true
            },
            amount:{
                required:true
            },
           phone_no:{
                required:true,
                minlength:9,
                maxlength:10,
                number: true
            },
            email_id:{
                required:true,
                email:true
            },
            reference_1:{
                required:true
            },
            reference_2:{
                required:true
            },
            debit_type:{
                required:true
            },
            period_from:{
                dateBefore: '#period_to_date',
                required:true
            },
            period_to:{
                dateAfter: '#period_from_date',
                required: function(element) {
                    return $('input[name="period_until_cancelled"]').val() == '';
                }
            },
            period_until_cancelled:{
                required: function(element) {
                    return $('input[name="period_to"]').val() == '';
                }
            }
        },
        messages: {
            mobile_no:{
                required:'Please enter Phone No.'
            },
            email_id:{
                reqired:'please enter Email Id'
            }
            },
        submitHandler: function (form) {
            form.submit();
        }
    });
    $('#nach_date').datetimepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        minView: 2, });
    $('#period_from_date').datetimepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        minView: 2, });
    $('#period_to_date').datetimepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        minView: 2, });

    $(document).ready(function () {
        var date = new Date();
        var date = $('#period_from_date').val();
        // date.setDate(date.getDate());
        $('#period_to_dateperiod_to_date').datetimepicker('setStartDate',  date);
        // var date2 = new Date();
        // date2.setDate(date2.getDate() + 7);
        // $('#value_date').datetimepicker('setEndDate',  date2);

        // parent.$('.modal-dialog').addClass('viewCiblReportModal .modal-lg').removeClass('modal-dialog modal-lg');
    });
</script>
@endsection