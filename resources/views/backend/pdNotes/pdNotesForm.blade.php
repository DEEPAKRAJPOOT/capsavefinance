@extends('layouts.backend.admin_popup_layout')
@section('content')
{{ 
    Form::open([
    'url'=>route('save_pd_notes'),
    'autocomplete'=>'off',
        ]) 
}}
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="">Type : </label>
            {!!Form::radio('type','1' ,'', [   'class'=>'form-control' ]) !!} Physical
            {!!Form::radio('type','2','', [   'class'=>'form-control' ]) !!} Tele

        </div>
        <div class="form-group">
            <label class="">Comment : </label> 
            {!!Form::textarea('comments', '', [ 'class'=>'form-control'  , 'rows'=>3]) !!}
            <span id='msg'></span>
        </div> 
        <button type="submit" class="btn btn-primary float-right">Submit</button>
    </div>
</div>	
{!!Form::hidden('app_id',$app_id )!!} 
{{ Form::close() }}
@endsection
@section('jscript')
@php 
$operation_status = session()->get('operation_status', false);
$messages = trans('success_messages.pd_notes_saved')
@endphp
@if( $operation_status == config('common.YES'))
    
<script>
    try {
        var p = window.parent;       
        p.jQuery('#iframeMessage').html('{!! Helpers::createAlertHTML($messages, 'success') !!}');
        p.jQuery('#pdNoteFrame').modal('hide');
    } catch (e) {
        if (typeof console !== 'undefined') {
            console.log(e);
        }
    }
</script>

@endif





@endsection	



