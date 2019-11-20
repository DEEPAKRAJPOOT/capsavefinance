@extends('layouts.guest')
@section('content')
        @if (count($errors) > 0)
        <div class="alertMsgBox">
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
<div class="form-content no-padding sign-up mt-5">
 <div class="row justify-content-center align-items-center m-0">
    <div class="col-md-4 form-design">
        <div class="sign-up-box">
            <form class="form-horizontal" method="POST" id="resetForgotFm" action="{{ url('password/reset') }}">
                <div class="section-header" style="background: #2a8b6a;">
                    <h4 class="section-title"> {{trans('master.resetForm.heading')}}</h4>
               </div>
                {{ csrf_field() }}

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.resetForm.email')}}</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ $email}}" autofocus>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.resetForm.new_pass')}}</label>
                            <input id="password" type="password" class="form-control required" name="password" >
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="pwd">{{trans('master.resetForm.conf_pass')}}</label>
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-next btn-fill btn-warning btn-wd">
                            {{trans('master.resetForm.reset_pass')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    </div>
</div>
<script>
    var messages = {
        req_email: "{{ trans('error_messages.req_user_name') }}",
        req_password: "{{ trans('error_messages.req_password') }}",

    };
</script>
<script src="{{ asset('frontend/outside/js/validation/forgot.js') }}"></script>
@endsection