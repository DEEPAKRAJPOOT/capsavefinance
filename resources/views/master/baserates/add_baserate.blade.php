@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="baseRateForm" name="baseRateForm" method="POST" action="{{route('save_base_rate')}}" target="_top">
        @csrf

        <div class="row">
            <div class="form-group col-md-12">
                <label for="company_name">Company Name</label>
                <input type="text" class="form-control" id="name" name="company_name" placeholder="Enter Company Name" maxlength="50">
                {!! $errors->first('company_name', '<span class="error">:message</span>') !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <label for="base_rate">Base Rate(%)</label>
                <input type="text" class="form-control" id="name" name="base_rate" placeholder="Enter Base Rate Percentage" maxlength="50">
                {!! $errors->first('base_rate', '<span class="error">:message</span>') !!}
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
        $('#baseRateForm').validate({// initialize the plugin
            rules: {
                'company_name': {
                    required: true,
                },
                'base_rate': {
                    required:true,
                    number:true,
                    maxlength: 3,
                }
                'is_active': {
                    required: true,
                },
            },
            messages: {
                'company_name': {
                    required: "Please enter Company Name",
                },
                'base_rate': {
                    required: "Please enter base rate",
                },
                'is_active': {
                    required: "Please Select Status of Industry",
                },
            }
        });
    });
</script>
@endsection