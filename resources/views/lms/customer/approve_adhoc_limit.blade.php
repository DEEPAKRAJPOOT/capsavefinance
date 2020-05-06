@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="documentForm" style="width: 100%" method="POST" action="{{ Route('save_approve_adhoc_limit') }}" enctype="multipart/form-data" target="_top">
    <!-- Modal body -->
    @csrf
    <input type="hidden" name="user_id" value="{{ request()->get('user_id') }}">
    <input type="hidden" name="app_offer_adhoc_limit_id" value="{{ request()->get('app_offer_adhoc_limit_id') }}">

    <div class="modal-body text-left">
        
        <button type="submit" class="btn btn-success float-right btn-sm" id="savedocument" >Approve</button>  
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