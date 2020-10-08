@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="documentForm" style="width: 100%" method="POST" action="{{ Route('nach_document_save', ['user_id' => request()->get('user_id'), 'users_nach_id' => request()->get('users_nach_id')]) }}" enctype="multipart/form-data" target="_top">       
    @csrf
    <div class="modal-body text-left">
        <div class="custom-file upload-btn-cls mb-3 mt-2">
            <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file[]" multiple="">
            <label class="custom-file-label" for="customFile">Choose file</label>
        </div>
        <button type="submit" class="btn btn-success float-right btn-sm" id="savedocument" >Submit</button>  
    </div>
</form>
 
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ url('frontend/js/document.js') }}"></script>
@php 
$operation_status = session()->get('operation_status', false);
$messages = session()->get('message', false);
@endphp

@if($operation_status == config('common.YES'))
    <script>
    try {
        var p = window.parent;
        p.jQuery('#iframeMessage').html('{!! Helpers::createAlertHTML($messages, 'success') !!}');
        p.jQuery('#uploadNachDocument').modal('hide');
        p.location.reload();
    } catch (e) {
        if (typeof console !== 'undefined') {
            console.log(e);
        }
    }
    </script>
@endif
@endsection