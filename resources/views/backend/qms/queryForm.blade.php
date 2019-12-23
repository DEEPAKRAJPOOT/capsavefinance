@extends('layouts.backend.admin_popup_layout')

@section('content')
{{ 
    Form::open([
    'url'=>route('save_query_management'),
    'autocomplete'=>'off',
    'name' => 'queryManagementForm',
    'enctype' => 'multipart/form-data',
        ]) 
}}

<div class="row">
    <div class="col-md-12">
        <div class="form-group" style="">
            <label for="email">Assign To Role</label>
            <select class="form-control" name="assignRoleId" >
                 <option disabled="" value="" selected="">Select Name</option>

                 @foreach($arrRole as $key=>$arr)
                    <option {{old('assignRoleId') == $key ? 'selected' : ''}} value="{{$key}}">{{$arr}}</option>
                 @endforeach
                
            </select>
            {!! $errors->first('assignRoleId', '<span class="error">:message</span>') !!}
        </div>

        <div class="form-group">
            <label class="">Comment : </label> 
            <textarea class="form-control summernote"  name="qms_cmnt" rows="3">{{old('qms_cmnt')}}</textarea>
            {!! $errors->first('qms_cmnt', '<span class="error">:message</span>') !!}
        </div> 

        <div class="custom-file upload-btn-cls mb-3 mt-2">
            <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file[]" multiple="">
            <label class="custom-file-label" for="customFile">Choose file</label>
            {!! $errors->first('doc_file', '<span class="error">:message</span>') !!}
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
$messages = trans('success_messages.query_management_saved')
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

    $(function() {
      $("form[name='queryManagementForm']").validate({
        rules: {
          'assignRoleId' : {
                    required : true,
                },
          'qms_cmnt' : {
                    required : true,
                },
          'doc_file[]': {
                    required: true,
                    extension: "jpg,png,pdf,doc,dox,xls,xlsx",
                    filesize : 200000000,
                },
        },
        messages: {
         'assignRoleId': {
                    required: "Please select role.",
                },
        'qms_cmnt': {
                    required: "Please enter query.",
                },
          'doc_file[]': {
                    required: "Please select file",
                    extension:"Please select jpg,png,pdf,doc,dox,xls,xlsx type format only.",
                    filesize:"maximum size for upload 20 MB.",
                },
         },
        submitHandler: function(form) {
          form.submit();
        }
      });
    });
    
</script>

@endsection	



