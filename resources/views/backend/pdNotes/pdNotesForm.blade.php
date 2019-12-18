@extends('layouts.backend.admin_popup_layout')

@section('content')
{{ 
    Form::open([
    'url'=>route('save_pd_notes'),
    'autocomplete'=>'off',
    'name' => 'pdNotesForm',
        ]) 
}}
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="">Type : </label>
            {!!Form::radio('type','1' ,'', [   'class'=>'form-control' ]) !!} Physical
            {!!Form::radio('type','2','', [   'class'=>'form-control']) !!} Tele
        </div>
            {!! $errors->first('type', '<span class="error">:message</span>') !!}

        <div class="form-group">
            <label for="usr">Title of Feature:</label>
            {!!Form::text('title', '', [ 'class'=>'form-control']) !!}
        </div> 
          
        <div class="form-group">
            <label class="">Comment : </label> 
            {!!Form::textarea('comments', '', [ 'class'=>'form-control summernote']) !!}
            {!! $errors->first('comments', '<span class="error">:message</span>') !!}
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

<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>

<script>
    $(document).ready(function() {
        $('.summernote').summernote({
              height: 300,
         });
    });

    $(function() {
      $("form[name='pdNotesForm']").validate({
        rules: {
          type: "required",
          comments: "required",
        },
        // Specify validation error messages
        messages: {
          type: "Type is required",
          comments: "Comment is required",
         },
        submitHandler: function(form) {
          form.submit();
        }
      });
    });
    
</script>

@endsection	



