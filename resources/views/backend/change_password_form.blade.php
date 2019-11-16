@extends('layouts.backend.admin-layout')

@section('content')

<div class="prtm-page-bar">
    <ul class="breadcrumb">
        <li class="breadcrumb-item text-capitalize">
            <h3>Change password</h3> </li>
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Change password</a></li>
    </ul>
</div>
<div class="user-setting row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="prtm-block">
            <div class="pad-b-md border-btm mrgn-b-lg">
                <h3 class="mrgn-all-none">Change password</h3> </div>
                {!!
                    Form::open(
                        array(
                        'name' => 'frmChangePassword',
                        'id' => 'frmChangePassword',
                        'url' => route('change_password'),
                        )
                    )
                !!}
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-8 {{ $errors->has('old_password') ? ' has-error' : '' }}">
                            <label><span class="text-danger">*</span>Old Password</label>
                            {!!
                                Form::text('old_password',
                                null,
                                [
                                'class' => 'form-control',
                                'placeholder' => 'Old Password',
                                'id'=>'old_password'
                                ])
                            !!}
                            @if ($errors->has('old_password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('old_password') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-8 {{ $errors->has('new_password') ? ' has-error' : '' }}">
                            <label><span class="text-danger">*</span>New password</label>
                            {!!
                                Form::text('new_password',
                                null,
                                [
                                'class' => 'form-control',
                                'placeholder' => 'New password',
                                'id'=>'new_password',
                                ])
                            !!}
                            @if ($errors->has('new_password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('new_password') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-8 {{ $errors->has('confirm_password') ? ' has-error' : '' }}">
                            <label><span class="text-danger">*</span>Confirm password</label>
                            {!!
                                Form::text('confirm_password',
                                null,
                                [
                                'class' => 'form-control',
                                'placeholder' => 'Confirm password',
                                'id'=>'confirm_password',
                                ])
                            !!}
                            @if ($errors->has('confirm_password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('confirm_password') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg">Update Password</button>
                </div>
             {!! Form::close() !!}
        </div>
    </div>
</div>
         
@endsection
@section('pageTitle')
Dashboard
@endsection
@section('jscript')
<script>
    var messages = {
        _token: "{{ csrf_token() }}",
        req_old_password: "{{ trans('error_messages.admin.req_old_password') }}",
        req_new_password: "{{ trans('error_messages.admin.req_new_password') }}",
        req_confirm_password: "{{ trans('error_messages.admin.req_confirm_password') }}",
        minlen_password: "{{ trans('error_messages.admin.minlen_password') }}",
        minlen_password: "{{ trans('error_messages.admin.same_confirm_password') }}",
    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('js/backend/validation/change_password.js') }}"></script>
@endsection