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
        <div class="form-group" style="">
            <label for="email">Assign To Role</label>
            <select class="form-control" name="file_bank_id">
                 <option disabled="" value="" selected="">Select Name</option>
                 <option value="1">ADCB</option>
                 <option value="386">Yes Bank</option>
                 <option value="387">Yeshwant Urban Co-Op Bank Ltd.</option>
                 <option value="388">Zoroastrian Bank</option>
            </select>
        </div>
       <div class="custom-file upload-btn-cls mb-3 mt-2">
            <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file[]" multiple="">
            <label class="custom-file-label" for="customFile">Choose file</label>
        </div>
        <div class="form-group">
            <label class="">Comment : </label> 
            {!!Form::textarea('comments', '', [ 'class'=>'form-control summernote', 'row'=>'3']) !!}
            <textarea class="form-control summernote"  name="qms_cmnt" rows="3"></textarea>
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



