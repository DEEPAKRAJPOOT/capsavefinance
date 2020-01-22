@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="xlsxdocumentForm" style="width: 100%" method="POST" action="{{ Route('save_xlsx_document') }}" enctype="multipart/form-data" target="_top">
        <div id="pullMsg"></div>
        @csrf
        <input type="hidden" name="request_data" value="{{ $request_data }}">
          <div class="modal-body text-left">
              <div class="custom-file upload-btn-cls mb-3 mt-2">
                <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file[]" multiple="">
                <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
          </div>
            <button type="button" class="btn btn-success float-right btn-sm" id="uploadxlsxDocument" >Upload</button>
    </form>
 
@endsection

@section('jscript')
<script>
  $("#uploadxlsxDocument").on('click',function(e) {  
    e.preventDefault(); 
    if (window.FormData) {
       var formData = new FormData(document.getElementById("xlsxdocumentForm"));
    }
    formData.append('_token', '{{ csrf_token() }}');
    submitForm(formData);
  });

  function submitForm(formData) {
   $.ajax({
        url: $("form#xlsxdocumentForm").attr('action'),
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function() {
          $(".isloader").show();
        },
        success: function (result) {
           let mclass = result['status'] ? 'success' : 'danger';
            var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
            $("#pullMsg").html(html);
            $(".isloader").hide();
        },
        error:function(error) {
            // body...
        },
       complete: function() {
          $(".isloader").hide();
       },
    })
  }
</script>
@endsection