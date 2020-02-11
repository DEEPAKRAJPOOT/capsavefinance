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
            <label for="txtCreditPeriod"> Personal Discussion Type: <span class="error_message_label">*</span></label>
            <div class="block-div clearfix">
              <div class="form-check-inline float-left">
                  <label class="form-check-label fnt">
                     {!!Form::radio('type','1' ,'',[ 'class'=>'form-check-input']) !!} <strong>Physical</strong>
                  </label>
              </div>
              <div class="form-check-inline float-left">
                  <label class="form-check-label fnt">
                     {!!Form::radio('type','2' ,'',[ 'class'=>'form-check-input']) !!} <strong>Tele</strong>
                  </label>
              </div>
            </div>
        </div>
            {!! $errors->first('type', '<span class="error">:message</span>') !!}

        <div class="form-group">
            <label for="usr">Title:</label>
            {!!Form::text('title', '', [ 'class'=>'form-control']) !!}
            {!! $errors->first('title', '<span class="error">:message</span>') !!}
        </div> 
          
        <div class="form-group">
            <label class="">Comment : </label> 
            {!!Form::textarea('comments', '', [ 'class'=>'form-control summernote', 'row'=>'3']) !!}
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
        window.parent.location.reload();
    } catch (e) {
        if (typeof console !== 'undefined') {
            console.log(e);
        }
    }
</script>

@endif


<script>
    $(document).ready(function() {
        $('.summernote').summernote({
              height: 150,
         });
        $(".note-popover").hide();
    });

    // $(function() {
    //   $("form[name='pdNotesForm']").validate({
    //     rules: {
    //       type: "required",
    //       title: "required",
    //       comments: "required",
    //     },
    //     messages: {
    //       type: "Type is required",
    //       title: "Title is required",
    //       comments: "Comment is required",
    //      },
    //     submitHandler: function(form) {
    //       form.submit();
    //     }
    //   });
    // });
    
</script>

@endsection	



