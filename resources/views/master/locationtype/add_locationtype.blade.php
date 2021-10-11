@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="locationytpeForm" name="locationytpeForm" method="POST" action="{{route('add_location_type')}}"
        target="_top">
        @csrf

        <div class="row">
            <div class="form-group col-md-12">
                <label for="name">Location Type</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Location Type"
                    maxlength="50">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <label for="location_code">Location Code</label>
                <input type="text" class="form-control" id="location_code" name="location_code"
                    placeholder="Enter Location Code" maxlength="50">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_type">Status</label><br />
                <select class="form-control" name="is_active" id="is_active">
                    <option value="" selected>Select</option>
                    <option value="1">Active</option>
                    <option value="2">In-Active</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 text-right">
                <input type="submit" class="btn btn-success btn-sm" name="add_location" id="add_location"
                    value="Submit" />
            </div>
        </div>
    </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">

    var messages = {
        unique_location_url: "{{ route('check_unique_location_url') }}",
        unique_location_code_url: "{{ route('check_unique_location_code_url') }}",
        token: "{{ csrf_token() }}"
    }

    $(document).ready(function () {
        $.validator.addMethod("uniqueLocation",
            function (value, element, params) {
                var result = true;
                var data = { name: value, _token: messages.token };
                if (params.location_id) {
                    data['location_id'] = params.location_id;
                }
                $.ajax({
                    type: "POST",
                    async: false,
                    url: messages.unique_location_url, // script to validate in server side
                    data: data,
                    success: function (data) {
                        result = (data.status == 1) ? false : true;
                    }
                });
                return result;
            }, 'Location Type already exists'
        );
        $.validator.addMethod("uniqueLocCode",
            function (value, element, params) {
                var result = true;
                var data = { location_code: value, _token: messages.token };
                if (params.location_id) {
                    data['location_id'] = params.location_id;
                }
                $.ajax({
                    type: "POST",
                    async: false,
                    url: messages.unique_location_code_url, // script to validate in server side
                    data: data,
                    success: function (data) {
                        result = (data.status == 1) ? false : true;
                    }
                });
                return result;
            }, 'Location Code is already exists'
        );

        $('#locationytpeForm').validate({ // initialize the plugin
            rules: {
                'name': {
                    required: true,
                    uniqueLocation: true
                },
                'location_code': {
                    required: true,
                    uniqueLocCode: true
                },
                'is_active': {
                    required: true,
                },
            },
            messages: {
                'name': {
                    required: "Please Enter Location Type",
                },
                'location_code': {
                    required: "Please Enter Location Code",
                },
                'is_active': {
                    required: "Please Select Status of Location",
                },
            }
        });
    });
</script>
@endsection