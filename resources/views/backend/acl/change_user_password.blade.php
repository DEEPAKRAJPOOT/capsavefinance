@extends('layouts.backend.admin_popup_layout')
@section('content')
@php
    $email = "readonly";
@endphp
<div class="modal-body text-left">
    <form id="editPassword" name="editPassword" method="POST" action="{{ route('save_user_role_password') }}" target="_top">
        @csrf

        <div class="row">
            <div class="form-group col-12" id="email">
                <input type="text" class="form-control" id="email" {{ isset($userData->email) ? $email : ' '}} name="email" value="{{$userData->email}}" placeholder="User Email">
            </div>
        </div>
        <div class="row">
            <input type="hidden" class="form-control" id="user_id" name="user_id" value="{{$userData->user_id}}">
            <div class="form-group col-6">
                <label for="password">Enter Password</label>
                <input type="password" class="form-control" id="password" name="password" value="" placeholder="Enter Password">
            </div>
            <div class="form-group col-6">
                <label for="confpassword">Conform Password</label>
                <input type="password" class="form-control" id="confpassword" name="confpassword" value="" placeholder="Confirm Password" maxlength="50">
            </div>
        </div>
        
        <div class="row">
            <div class="form-group col-md-12 mb-0">
                <input type="submit" class="btn btn-success btn-sm pull-right" name="add_password" id="add_password" value="Submit" />
            </div>
        </div>
    </form>
</div>

@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function() {


        $('#editPassword').validate({ // initialize the plugin
            rules: {
                'password': {
                    required: true,
                },
                'confpassword': {
                    required: true,
                    equalTo: "#password"
                },
            },
            messages: {
                'password': {
                    required: "Please enter Password",
                },
                'confpassword': {
                    required: "Please Confirm Password",
                },
            }
        });
    });
</script>
@endsection