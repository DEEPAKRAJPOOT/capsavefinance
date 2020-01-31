@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="addressForm" name="addressForm" method="POST" action="{{route('save_addr')}}" target="_top">
        {!! Form::hidden('user_id' , isset($user_id) ?$user_id : null ) !!}
        {!! Form::hidden('biz_addr_id' , isset($biz_addr_id) ?$biz_addr_id : null ) !!}
        @csrf

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
            <div class="form-group col-md-6">
                <label for="address_type">Status</label><br />
                <select class="form-control" name="rcu_status" id="rcu_status">
                    <option disabled value="" selected>Select</option>
                    <option {{$userAddress_data->rcu_status == 1 ? 'selected' : ''}} value="1">Active</option>
                    <option {{$userAddress_data->rcu_status == 0 ? 'selected' : ''}} value="0">In-Active</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 mb-0">
                <input type="submit" class="btn btn-success btn-sm" name="add_address" id="add_address" value="Submit" />
            </div>
        </div>
    </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function() {


        $('#addressForm').validate({ // initialize the plugin
            rules: {
                'addr_1': {
                    required: true,
                },
                'city_name': {
                    required: true,
                },
                'state_id': {
                    required: true,
                },
                'pin_code': {
                    required: true,
                    digits: true,
                },
                'rcu_status': {
                    required: true,
                },
            },
            messages: {
                'addr_1': {
                    required: "Please enter Address",
                },
                'city_name': {
                    required: "Please enter city name",
                },
                'state_id': {
                    required: "Please enter state name",
                },
                'pin_code': {
                    required: "Please enter pincode",
                },
                'rcu_status': {
                    required: "Please select Status",
                },
            }
        });
    });
</script>
<script>
    let pincode = document.getElementById('pin_code');

    pincode.addEventListener('input', function() {
        let pinVal = document.getElementById('pin_code').value;
        let pinStr = pinVal.toString();

        if (isNaN(pincode.value) || pinStr.length >= 7) {
            pincode.value = "";
        }
    });
</script>
@endsection