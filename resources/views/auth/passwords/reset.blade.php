@extends('layouts.withought_login')
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
<div class="content-wrap height-auto">
    <div class="login-section">
        <div class="logo-box text-center marB20">
            <a href="index.html"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
            <h2 class="head-line2 marT25">{{trans('master.resetForm.heading')}}</h2>
        </div>

        <div class="sign-up-box">
            <form class="form-horizontal" method="POST" id="resetForgotFm" action="{{ url('password/reset') }}">
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
<script>
    var messages = {
        req_email: "{{ trans('error_messages.req_user_name') }}",
        req_password: "{{ trans('error_messages.req_password') }}",

    };
</script>
<script src="{{ asset('frontend/outside/js/validation/forgot.js') }}"></script>
@endsection