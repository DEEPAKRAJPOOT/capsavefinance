@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="addressForm" name="addressForm" method="POST" action="{{route('save_addr')}}" target="_top">
        {!! Form::hidden('user_id' , isset($user_id) ?$user_id : null ) !!}
        {!! Form::hidden('biz_addr_id' , isset($biz_addr_id) ?$biz_addr_id : null ) !!}
        @csrf

        <div class="row">
            <div class="form-group col-md-6">
                <label for="address_type">GST Number <small>(if you want to prefill address based on GST)</small></label><br />
                <select class="form-control" name="gst_no" id="gst_no" onchange="fillAddress(this.value)">
                    <option disabled value="" selected>Select GST</option>
                    @foreach($gsts as $gst)
                    <option value="{{$gst->pan_gst_hash}}">{{$gst->pan_gst_hash}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label for="addr_1">Enter Address</label>
                <input type="text" class="form-control" id="addr_1" name="addr_1" value="{{$userAddress_data->addr_1}}" placeholder="Enter Address">
            </div>
            <div class="form-group col-6">
                <label for="city_name">City Name</label>
                <input type="text" class="form-control" id="city_name" name="city_name" value="{{$userAddress_data->city_name}}" placeholder="Enter City">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label for="state_id">State Name</label>
                <!-- <input type="text" class="form-control" id="state_id" name="state_id" placeholder="Enter State"> -->
                <select class="form-control" name="state_id" id="state_id">
                    <option disabled value="" selected>Select State</option>
                    @foreach($state_list as $stateName=>$stateList)
                    <option {{$stateList == $userAddress_data->state_id ? 'selected' : ''}} value="{{$stateList}}">{{$stateName}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-6">
                <label for="pin_code">Pincode</label>
                <input type="text" class="form-control" id="pin_code" name="pin_code" value="{{$userAddress_data->pin_code}}" placeholder="Enter Pincode">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-6">
                <label for="address_type">Status</label><br />
                <select class="form-control" name="rcu_status" id="rcu_status">
                    <option disabled value="" selected>Select</option>
                    <option {{$userAddress_data->rcu_status == 1 ? 'selected' : ''}} value="1">Active</option>
                    <option {{$userAddress_data->rcu_status == 0 ? 'selected' : ''}} value="0">In-Active</option>
                </select>
            </div>
            <div class="form-group col-6">
                <label for="address_type">Set as Default</label><br />
                <input type="checkbox" name="is_default" value="1" {{($userAddress_data->is_default)? 'checked': ''}} style="width: 25px; height: 25px;">
            </div>
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
    biz_gst_to_entity_karza: "{{ URL::route('chk_biz_gst_to_entity') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}"
};
</script>
<script src="{{ asset('backend/js/lms/address.js') }}"></script>
@endsection