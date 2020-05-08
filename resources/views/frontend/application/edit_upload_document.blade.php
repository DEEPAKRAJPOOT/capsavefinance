@extends('layouts.popup_layout')
@section('content')
<form id="documentForm" style="width: 100%" method="POST" action="{{ Route('front_update_edit_upload_document') }}" enctype="multipart/form-data" target="_top">
        @csrf
        <input type="hidden" name="app_doc_file_id" value="{{ $data->app_doc_file_id}}">
        <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}">
        <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}">
        
        <div class="modal-body text-left">
            <div class="row">
                <div class="col-md-12">
                   <div class="form-group">
                      <label for="email">Comment</label>
                      <textarea type="text" name="comment" value="" class="form-control" tabindex="1" placeholder="Enter comment here ."></textarea>
                   </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success float-right btn-sm" id="savedocument" >Submit</button>  
        </div>
    </form>
 
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ url('frontend/js/document.js') }}"></script>
@endsection