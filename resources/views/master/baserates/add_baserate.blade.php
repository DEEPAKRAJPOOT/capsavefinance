@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="baseRateForm" name="baseRateForm" method="POST" action="{{route('save_base_rate')}}" target="_top">
        @csrf

        <div class="row">
            <div class="form-group col-md-6">
                <label for="bank_id">Bank Name <span class="mandatory">*</span></label>
                <select name="bank_id" id="bank_id" class='form-control'>
                    <option value="">Select Bank</option>
                    @foreach($bank_list as $key => $option)
                    <option value="{{$key}}">{{$option}}</option>
                    @endforeach
                </select>
                {!! $errors->first('bank_id', '<span class="error">:message</span>') !!}
            </div>

            <div class="form-group col-md-6">
                <label for="base_rate">Base Rate(%) <span class="mandatory">*</span></label>
                <input type="text" class="form-control" name="base_rate" placeholder="Enter Base Rate Percentage">
                {!! $errors->first('base_rate', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="start_date">Start Date <span class="mandatory">*</span></label>
                <input type="text" name="start_date" id="start_date" readonly="readonly" class="form-control" value="">
                {!! $errors->first('start_date', '<span class="error">:message</span>') !!}
            </div>

            <div class="form-group col-md-6">
                <label for="end_date">End Date</label>
                <input type="text" name="end_date" id="end_date" readonly="readonly" class="form-control">
                {!! $errors->first('end_date', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="is_active">Status <span class="mandatory">*</span></label><br />
                <select class="form-control" name="is_active" id="is_active">
                    <option value="" selected>Select</option>
                    <option value="1">Active</option>
                    <option value="0">In-Active</option>
                </select>
                {!! $errors->first('is_active', '<span class="error">:message</span>') !!}
            </div>
            <div class="form-group col-md-6">
                <label for="is_default">Is Default Base Rate? <span class="mandatory">*</span></label><br />
                <select class="form-control" name="is_default" id="is_default">
                    <option value="" selected>Select</option>
                    <option value="1">YES</option>
                    <option value="0">NO</option>
                </select>
                {!! $errors->first('is_default', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 text-right">
                <input type="submit" class="btn btn-success btn-sm" name="add_baserate" id="add_baserate" value="Submit"/>
            </div>
        </div>
    </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function () {

        $.validator.addMethod("rate_percent", function (value, element) {
            return this.optional(element) || /^\d+(\.\d{1,2})?$/.test(value);
        }, "Please specify a valid base rate percent");

        $("#end_date, #start_date").datetimepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            minView: 2
        });
        
        $.validator.addMethod("greaterStart", function (value, element) {
            var startDate = ($('#start_date').val()).split('/');
            var endDate = ($('#end_date').val()).split('/');
            var startDateSum = parseInt(startDate[0]) + parseInt(startDate[1]) + parseInt(startDate[2]);
            var endDateSum = parseInt(endDate[0]) + parseInt(endDate[1]) + parseInt(endDate[2]);
            console.log(startDate,startDateSum);
            console.log(endDate,endDateSum);
            return this.optional(element) || endDateSum >= startDateSum;
        });
        
        $('#baseRateForm').validate({// initialize the plugin
            rules: {
                bank_id: {
                    required: true,
                    digits: true
                },
                base_rate: {
                    required: true,
                    number: true,
                    range: [0, 100],
                    rate_percent: 'input[name="base_rate"]'
                },
                start_date: {
                    required: true,
//                    smallerEnd: "#end_date"
                },
                end_date: {
//                    required: true,
                    greaterStart: true
                },
                is_active: {
                    required: true,
                    digits: true
                },
                is_default: {
                    required: true,
                    digits: true
                }
            },
            messages: {
                bank_id: {
                    required: "Please Select Bank"
                },
                base_rate: {
                    required: "Please Enter Base Rate"
                },
                start_date: {
                    required: "Please Enter Start Date",
//                    smallerEnd: "Must be smaller than end date."
                },
                end_date: {
                    greaterStart: "Must be greater than start date."
                },
                is_active: {
                    required: "Please Select Status of Base Rate"
                },
                is_default: {
                    required: "Please Select Default Base Rate"
                }
            }
        });
    });
</script>
@endsection