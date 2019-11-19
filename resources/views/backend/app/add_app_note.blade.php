@extends('layouts.backend.admin_popup_layout')

@section('content')
{!!
Form::open(
    array(
        'route' => 'save_app_note',
        'name' => 'addAppNote',
        'autocomplete' => 'off', 
        'id' => 'addAppNote',
        'target' => '_top'
    )
)
!!}
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="txtCreditPeriod"> Note
                <span class="mandatory">*</span>
            </label>
            <textarea type="text" name="notes" value="" class="form-control" tabindex="1" placeholder="Add Note" required=""></textarea>
        </div>
    </div>
</div>
{!! Form::hidden('app_id', $app_id) !!}
{!! Form::hidden('biz_id', $biz_id) !!}
<button type="submit" class="btn btn-primary float-right">Submit</button>  
{!!
Form::close()
!!}
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/assets/js/application.js') }}" type="text/javascript"></script>
@endsection            
