@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="addressForm" name="woApprDissapprForm" method="POST" action="{{route('wo_save_appr_dissappr')}}" target="_top">
        @csrf
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
            <div class="form-group col-md-12 mb-0">
                <input type="submit" class="btn btn-success btn-sm pull-right" name="Submit" id="add_address" value="Submit" />
            </div>
        </div>
    </form>
</div>
@endsection
@section('jscript')
<script>
var messages = {
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}"
};
</script>
<script src="{{ asset('backend/js/lms/address.js') }}"></script>
@endsection