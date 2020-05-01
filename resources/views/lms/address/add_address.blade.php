@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="addressForm" name="addressForm" method="POST" action="{{route('save_addr')}}" target="_top">
        {!! Form::hidden('user_id' , isset($user_id) ?$user_id : null ) !!}
        {!! Form::hidden('biz_pan_gst_api_id') !!}
        {!! Form::hidden('biz_pan_gst_id') !!}
        @csrf

        <div class="row" style="display: none;">
            <div class="form-group col-md-6">
                <label for="address_type">GST Number <small>(if you want to prefill address based on GST)</small></label><br />
                <select class="form-control" name="gst_no" id="gst_no" onchange="fillAddress(this.value)">
                    <option disabled value="" data-id="" selected>Select GST</option>
                    @foreach($gsts as $gst)
                        @if($gst->is_gst_hide == 0 && !in_array($gst->pan_gst_hash, $app_gsts))
                        <option value="{{$gst->pan_gst_hash}}" data-id="{{$gst->biz_pan_gst_id}}">{{$gst->pan_gst_hash}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label for="addr_1">Enter Address</label>
                <input type="text" class="form-control" id="addr_1" name="addr_1" placeholder="Enter Address">
            </div>
            <div class="form-group col-6">
                <label for="city_name">City Name</label>
                <input type="text" class="form-control" id="city_name" name="city_name" placeholder="Enter City">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label for="state_id">State Name</label>
                <!-- <input type="text" class="form-control" id="state_id" name="state_id" placeholder="Enter State"> -->
                <select class="form-control" name="state_id" id="state_id">
                    <option value="">Select State</option>
                    @foreach($state_list as $stateName=>$stateList)
                    <option value="{{$stateList}}">{{$stateName}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-6">
                <label for="pin_code">Pincode</label>
                <input type="text" class="form-control" id="pin_code" name="pin_code" placeholder="Enter Pincode">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-6">
                <label for="address_type">Status</label><br />
                <select class="form-control" name="is_active" id="is_active">
                    <option disabled value="" selected>Select</option>
                    <option value="1">Active</option>
                    <option value="0">In-Active</option>
                </select>
            </div>
            @if($is_show_default && 0)
            <div class="form-group col-6">
                <label for="address_type">Set as Default</label><br />
                <input type="checkbox" name="is_default" value="1" style="width: 25px; height: 25px;">
            </div>
            @endif
        </div>
        <div class="row">
            <div class="form-group col-md-12 mb-0">
                <input type="submit" class="btn btn-success btn-sm pull-right" name="add_address" id="add_address" value="Submit" />
            </div>
        </div>
    </form>
</div>
@endsection
@section('jscript')
<script>
var messages = {
    get_address_by_gst: "{{ URL::route('get_address_by_gst') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}"
};

$(document).ready(function(){
    if($('#gst_no option').length > 1){
        $('#gst_no').parent('div').parent('div').show();
    }
})
</script>
<script src="{{ asset('backend/js/lms/address.js') }}"></script>
@endsection