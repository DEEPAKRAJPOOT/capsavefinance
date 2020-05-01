@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="baseRateForm" name="baseRateForm" method="POST" action="{{route('save_base_rate')}}" target="_top">
        @csrf
        {!! Form::hidden('is_default', 1)  !!}
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
                <input type="text" name="start_date" id="start_date" readonly="readonly" class="form-control" value="{{old('start_date')}}">
                {!! $errors->first('start_date', '<span class="error">:message</span>') !!}
            </div>

            <div class="form-group col-md-6">
                <label for="end_date">End Date</label>
                <input type="text" name="end_date" id="end_date" readonly="readonly" class="form-control" value="{{old('end_date')}}">
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
        }, "base rate can't be exceed two digits after decimal.");
        
//        const toDate = (dateStr) => {
//            const [day, month, year] = dateStr.split("/")
//            return new Date(year, month - 1, day);
//        }
//        
//        var startdate = toDate($('#start_date').val());

        $("#start_date").datetimepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            minView: 2,
            endDate: new Date()
        });
        
        $("#end_date").datetimepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            minView: 2,
            startDate: new Date()
        });

//        $.validator.addMethod("greaterStart", function (value, element, params) {
//            if (!/Invalid|NaN/.test(new Date(value))) {
//                return new Date(value) > new Date($(params).val());
//            }
//
//            return isNaN(value) && isNaN($(params).val())
//                    || (Number(value) > Number($(params).val()));
//            
//        });

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
//                    greaterStart: "#start_date"
                },
                is_active: {
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
//                    greaterStart: "Must be greater than start date."
                },
                is_active: {
                    required: "Please Select Status of Base Rate"
                }
            }
        });
    });
</script>
@endsection