@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="baseRateForm" name="baseRateForm" method="POST" action="{{route('save_base_rate')}}" target="_top">
        @csrf
        <input type="hidden" name="filter_search_keyword" id ="filter_search_keyword" value="">
        <input type="hidden" class="form-control" name="id" maxlength="5" value="{{$baserate_data->id}}">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="bank_id">Bank Name</label>
                <select name="bank_id" id="bank_id" class='form-control'>
                    <option value="">Select Bank</option>
                    @foreach($bank_list as $key => $option)
                    <option {{ ($baserate_data->bank_id == $key) ? 'selected' : ''}} value="{{$key}}">{{$option}}</option>
                    @endforeach
                </select>
                {!! $errors->first('bank_id', '<span class="error">:message</span>') !!}
            </div>
            <div class="form-group col-md-6">
                <label for="base_rate">Base Rate(%)</label>
                <input type="text" class="form-control" id="name" name="base_rate" value="{{$baserate_data->base_rate}}" placeholder="Enter Base Rate Percentage" maxlength="50">
                {!! $errors->first('base_rate', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_type">Status</label><br />
                <select class="form-control" name="is_active" id="is_active">
                    <option value="" selected>Select</option>
                    <option {{$baserate_data->is_active == 1 ? 'selected' : ''}} value="1">Active</option>
                    <option {{$baserate_data->is_active == 2 ? 'selected' : ''}} value="2">In-Active</option>
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

        $('#baseRateForm').validate({// initialize the plugin
            rules: {
                bank_id: {
                    required: true,
                    digits: true,
                },
                base_rate: {
                    required: true,
                    number: true,
                    range: [0, 100],
                    rate_percent: 'input[name="base_rate"]',
                },
                
                is_active: {
                    required: true,
                    digits: true
                }
            },
            messages: {
                bank_id: {
                    required: "Please Select Bank",
                },
                base_rate: {
                    required: "Please Enter Base Rate",
                },
                is_active: {
                    required: "Please Select Status of Base Rate",
                },
            }
        });
    });
</script>
@endsection