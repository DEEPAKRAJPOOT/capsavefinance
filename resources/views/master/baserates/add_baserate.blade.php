@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="baseRateForm" name="baseRateForm" method="POST" action="{{route('save_base_rate')}}" target="_top">
        @csrf

        <div class="row">
            <div class="form-group col-md-6">
                <label for="bank_id">Bank Name</label>
                <select name="bank_id" id="bank_id" class='form-control'>
                    <option value="">Select Bank</option>
                    @foreach($bank_list as $key => $option)
                    <option value="{{$key}}">{{$option}}</option>
                    @endforeach
                </select>
                {!! $errors->first('bank_id', '<span class="error">:message</span>') !!}
            </div>
        
            <div class="form-group col-md-6">
                <label for="base_rate">Base Rate(%)</label>
                <input type="text" class="form-control" name="base_rate" placeholder="Enter Base Rate Percentage">
                {!! $errors->first('base_rate', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="min_base_rate">Min Base Rate(%)</label>
                <input type="text" class="form-control" name="min_base_rate" placeholder="Enter Min Base Rate Percentage">
                {!! $errors->first('min_base_rate', '<span class="error">:message</span>') !!}
            </div>
            <div class="form-group col-md-6">
                <label for="max_base_rate">Max Base Rate(%)</label>
                <input type="text" class="form-control" name="max_base_rate" placeholder="Enter Max Base Rate Percentage">
                {!! $errors->first('max_base_rate', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="is_active">Status</label><br />
                <select class="form-control" name="is_active" id="is_active">
                    <option value="" selected>Select</option>
                    <option value="1">Active</option>
                    <option value="2">In-Active</option>
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

        $('#baseRateForm454').validate({// initialize the plugin
            rules: {
                company_name: {
                    required: true,
                    maxlength: 200
                },
                base_rate: {
                    required: true,
                    number: true,
                    range: [0, 100]
                },
                is_active: {
                    required: true,
                    digits: true
                }
            },
            messages: {
                company_name: {
                    required: "Please Enter Company Name",
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