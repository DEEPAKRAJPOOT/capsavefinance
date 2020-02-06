@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="documentForm" style="width: 100%" method="POST" action="{{ Route('pp_document_save') }}" enctype="multipart/form-data" target="_top">
        <!-- Modal body -->
        @csrf
        <input type="hidden" name="doc_id" id="doc_id" value="">
        <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}">
        <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}">

        <div class="modal-body text-left">

            <div class="custom-file upload-btn-cls mb-3 mt-2">
                <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file[]" multiple="">
                <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
            <div class="row">
                <div class="col-md-12">
                   <div class="form-group">
                      <label >Comment *</label>
                      <textarea type="text" name="comment" value="" class="form-control" tabindex="1" placeholder="Enter comment here ." required=""></textarea>
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
<script>
    
   $(document).ready(function(){
        var docId = parent.$('#uploadDocId').val();
        $('#myModal').modal('show');
        $('input[name=docId]').val(docId);
        $('input[name=doc_id]').val(docId);
        
    });
</script>
@endsection