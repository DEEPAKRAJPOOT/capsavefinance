@extends('layouts.backend.admin_popup_layout')

@section('content')
  <div class="modal-body text-left">
{!!
Form::open(
    array(
        'route' => 'save_app_rejection',
        'name' => 'addAppRejection',
        'autocomplete' => 'off', 
        'id' => 'addAppRejection',
        'target' => '_top'
    )
)
!!}
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="reason"> Decline Reason
                <span class="mandatory">*</span>
            </label>
            <textarea type="text" name="reason" value="" class="form-control" tabindex="1" maxlength="500" placeholder="write reason..." required=""></textarea>
        </div>
    </div>
</div>
{!! Form::hidden('app_id', $app_id) !!}
{!! Form::hidden('biz_id', $biz_id) !!}
{!! Form::hidden('user_id', $user_id) !!}
<button type="submit" class="btn btn-success btn-sm float-right">Submit</button>  
{!!
Form::close()
!!}
  </div>
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/assets/js/application.js') }}" type="text/javascript"></script>
@endsection            
