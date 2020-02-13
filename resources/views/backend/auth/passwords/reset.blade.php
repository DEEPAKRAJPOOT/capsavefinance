@extends('layouts.backend.admin_guest')

@section('content')

    <div class="prtm-wrapper">
    <div class="prtm-main">
        <div class="login-banner"></div>
        <div class="login-form-wrapper mrgn-b-lg">
            <div class="container-fluid">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default" style="margin-top: -10em">
                <div class="panel-heading">Reset Password</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ url('password/reset') }}" onsubmit="return resetValidations()">
                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" readonly="readonly" type="text" class="form-control" name="email" value="{{ $email }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Reset Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>   
@endsection

@section('jscript')
<script>
  function resetValidations(){
    unsetError('input[name=email]');
    unsetError('input[name=password]'); 
    unsetError('input[name=password_confirmation]');

    let flag = true;
    let email = $('input[name=email]').val();
    let password = $('input[name=password]').val().trim();
    let password_confirmation = $('input[name=password_confirmation]').val().trim();

    if(email.length == 0){
        setError('input[name=email]', 'Please fill email address');
        flag = false;
    }

    if(password.length == 0){
        setError('input[name=password]', 'Please fill Password');
        flag = false;
    }else if(password.match(/\s/g)){
        setError('input[name=password]', 'In Password space not allowed');
        flag = false;
    }else if(password.length < 8){
        setError('input[name=password]', 'The password must be at least 8 characters.');
        flag = false;
    }

    if(password_confirmation.length == 0){
        setError('input[name=password_confirmation]', 'Please fill Confirm Password');
        flag = false;
    }else if(password_confirmation.match(/\s/g)){
        setError('input[name=password]', 'In Password space not allowed');
        flag = false;
    }else if(password != password_confirmation){
        setError('input[name=password_confirmation]', 'The password confirmation does not match.');
        flag = false;
    }


    if(flag){
        return true;
    }else{
        return false;
    }
  }
</script>
@endsection