@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="baseRateForm" name="baseRateForm" method="POST" action="{{route('save_base_rate')}}" target="_top">
        @csrf
        <input type="hidden" name="filter_search_keyword" id ="filter_search_keyword" value="">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="chrg_name">Company Name</label>
                <input type="text" class="form-control" id="name" name="company_name" value="{{$baserate_data->company_name}}" placeholder="Enter Company Name" maxlength="50">
                <input type="hidden" class="form-control" name="id" maxlength="5" value="{{$baserate_data->id}}">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <label for="chrg_name">Base Rate(%)</label>
                <input type="text" class="form-control" id="name" name="base_rate" value="{{$baserate_data->base_rate}}" placeholder="Enter Base Rate Percentage" maxlength="50">
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
    
    $(document).ready(function(){
        $('#filter_search_keyword').val(parent.$('#search_keyword').val());
	console.log(parent.$('#search_keyword').val());
    });
    
     $(document).ready(function () {
        $('#baseRateForm').validate({// initialize the plugin
            rules: {
                'company_name': {
                    required: true,
                },
                'base_rate': {
                    required: true,
                    number: true,
                    range: [0,100]
                },
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