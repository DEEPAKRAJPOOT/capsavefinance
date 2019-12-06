@extends('layouts.backend.admin_guest')

@section('content')
<div class="prtm-wrapper">
    <div class="prtm-main">
        <div class="login-banner"></div>
        <div class="login-form-wrapper mrgn-b-lg">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12 col-sm-9 col-md-8 col-lg-5 center-block">
                        <div class="prtm-form-block prtm-full-block overflow-wrappper">
                            <div class="login-bar"> <img src="{{ asset('backend/theme/assets/img/login-bars.png') }}" class="img-responsive" alt="login bar" width="743" height="7"> </div>
                            <div class="prtm-block-title text-center">
                                <div class="mrgn-b-lg">
                                    <a href="javascript:;"> <img src="{{ asset('backend/theme/assets/img/prtm-logo.svg') }}" alt="login logo" class="img-responsive display-ib" width="218" height="23"> </a>
                                </div>
                                <div class="login-top mrgn-b-lg">
                                    <div class="mrgn-b-md">
                                        <h2 class="text-capitalize base-dark font-2x fw-normal">Reset Password</h2> </div>
                                    <p>Please enter your Email</p>
                                </div>
                            </div>
                            <div class="prtm-block-content">
                                <form class="login-form" method="POST" action="{{ url('password/email') }}" id="backendForgotPassFrm" >
                                    {{ csrf_field() }}
                                    <div class="form-group has-feedback">
                                        <input id="email" type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" autofocus>
                                        <span class="glyphicon glyphicon-user form-control-feedback fa-lg" aria-hidden="true"></span> </div>
                                    <div class="login-meta mrgn-b-lg">
                                        <div class="row">
                                            <div class="col-xs-6 col-sm-6 col-md-6"></div>
                                            <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                                                <a href="{{ url('/') }}" class="text-primary password-style">Login</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mrgn-b-lg">
                                        <button type="submit" class="btn btn-success btn-block font-2x">Send Password Reset Link</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('pageTitle')
Admin- Forgot Password
@endsection
@section('jscript')
<script>
    var messages = {
        req_email: "{{ trans('admin_error_messages.req_email') }}",
        minlen_email: "{{ trans('admin_error_messages.minlen_email') }}",
        invalid_email: "{{ trans('admin_error_messages.invalid_email') }}",
    };
</script>
<script src="{{ asset('js/common/jquery.validate.js') }}"></script>
<script src="{{ asset('js/backend/validation/backend_forgot_password.js') }}"></script>
@endsection