@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="baseRateForm" name="baseRateForm" method="POST" action="{{route('save_base_rate')}}" target="_top">
        @csrf
        <input type="hidden" name="filter_search_keyword" id ="filter_search_keyword" value="">
        <input type="hidden" class="form-control" name="id" maxlength="5" value="{{$baserate_data->id}}">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="bank_id">Bank Name <span class="mandatory">*</span></label>
                <select name="bank_id" id="bank_id" class='form-control'>
                    <option value="">Select Bank</option>
                    @foreach($bank_list as $key => $option)
                    <option {{ ($baserate_data->bank_id == $key) ? 'selected' : ''}} value="{{$key}}">{{$option}}</option>
                    @endforeach
                </select>
                {!! $errors->first('bank_id', '<span class="error">:message</span>') !!}
            </div>
            <div class="form-group col-md-6">
                <label for="base_rate">Base Rate(%) <span class="mandatory">*</span></label>
                <input type="text" class="form-control" id="name" name="base_rate" value="{{$baserate_data->base_rate}}" placeholder="Enter Base Rate Percentage" maxlength="50">
                {!! $errors->first('base_rate', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="start_date">Start Date <span class="mandatory">*</span></label>
                <input type="text" name="start_date" id="start_date" readonly="readonly" class="form-control" value="{{$baserate_data->start_date}}">
                {!! $errors->first('start_date', '<span class="error">:message</span>') !!}
            </div>

            <div class="form-group col-md-6">
                <label for="end_date">End Date</label>
                <input type="text" name="end_date" id="end_date" readonly="readonly" class="form-control" value="{{$baserate_data->end_date}}">
                {!! $errors->first('end_date', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_type">Status <span class="mandatory">*</span></label><br />
                <select class="form-control" name="is_active" id="is_active">
                    <option value="" selected>Select</option>
                    <option {{$baserate_data->is_active == 1 ? 'selected' : ''}} value="1">Active</option>
                    <option {{$baserate_data->is_active == 0 ? 'selected' : ''}} value="0">In-Active</option>
                </select>
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
        $('#filter_search_keyword').val(parent.$('#search_keyword').val());
        console.log(parent.$('#search_keyword').val());
    });

    $(document).ready(function () {
        
        $.validator.addMethod("rate_percent", function (value, element) {
            return this.optional(element) || /^\d+(\.\d{1,2})?$/.test(value);
        }, "Please specify a valid base rate percent");
        
        $("#end_date").datetimepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            minView: 2
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
                    required: true
                },
                end_date: {
//                    required: true
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
                    required: "Please Select Start Date"
                },
                is_active: {
                    required: "Please Select Status of Base Rate"
                }
            }
        });
    });
</script>
@endsection