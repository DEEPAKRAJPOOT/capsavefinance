@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="bankdocumentForm" style="width: 100%" method="POST" action="{{ Route('bank_document_save') }}" enctype="multipart/form-data" target="_top">
        <!-- Modal body -->
        <div id="pullMsg"></div>
        @csrf
        <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}">
        <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}">
        <input type="hidden" name="doc_id" value="{{ request()->get('doc_id') }}">
        <input type="hidden" name="app_doc_file_id" value="{{ request()->get('app_doc_file_id') }}">
        <input type="hidden" name="req_data" value="{{ $req_data }}">

          <div class="modal-body text-left">
              <div class="row">
                <div class="col-6">
                   <div class="form-group">
                      <label for="email">Select Month</label>
                      <select class="form-control" name="bank_month">
                         <option selected diabled value=''>Select Month</option>
                         @for($i=1;$i<=12;$i++)
                              <option value="{{$i}}" {{$bank_doc_data->gst_month == $i ? 'selected' : ''}}>{{date('F', strtotime("2019-$i-01"))}}</option>
                         @endfor
                      </select>
                   </div>
                </div>
                <div class="col-6">
                   <div class="form-group">
                      <label for="email">Select Year</label>
                      <select class="form-control" name="bank_year">
                         <option value=''>Select Year</option>
                        @for($i=-3;$i<=0;$i++)
                            <option {{$bank_doc_data->gst_year == date('Y')+$i ? 'selected' : ''}}>{{date('Y')+$i}}</option>
                       @endfor;
                      </select>
                   </div>
                </div>
              </div>
              <div class="form-group">
                  <label for="email">Select Bank Name</label>
                  <select class="form-control" name="file_bank_id" id="file_bank_id">
                      <option disabled value="" selected>Select Bank Name</option>
                      @foreach($bankdata as $bank)
                          <option {{$bank_doc_data->file_bank_id == $bank['id'] ? 'selected' : ''}} value="{{$bank['id']}}">{{$bank['bank_name']}}</option>
                       @endforeach
                  </select>
              </div>
              <div class="custom-file upload-btn-cls mb-3 mt-2">
                <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file[]" multiple="">
                <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
            <div class="row">
              <div class="col-6">
                 <label>Is Password Protected</label>
                 <div class="form-group">
                    <label for="is_password_y">
                      <input type="radio" name="is_pwd_protected" id="is_password_y" value="1"> Yes
                    </label>
                    <label for="is_password_n">
                      <input type="radio" name="is_pwd_protected" checked id="is_password_n" value="0"> No
                    </label>
                 </div>
              </div>
              <div class="col-6">
                 <label>Is Scanned</label>
                 <div class="form-group">
                    <label for="is_scanned_y">
                      <input type="radio" name="is_scanned" id="is_scanned_y" value="1"> Yes
                    </label>
                    <label for="is_scanned_n">
                      <input type="radio" name="is_scanned" checked id="is_scanned_n" value="0"> No
                    </label>
                 </div>
              </div>
            </div>
            <div class="row" style="display: none" id="password_file_div">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="pwd_txt">Enter File Password</label>
                        <input type="password" placeholder="Enter File Password" class="form-control" name="pwd_txt" id="pwd_txt">
                     </div>
                </div>
            </div>
          </div>
            <button type="button" class="btn btn-success float-right btn-sm" id="uploadBankDocument" >Re-upload</button>
            <button type="button" class="btn btn-success float-left btn-sm" id="updateBankData" >Re-Process</button>  
        </div>
    </form>
 
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ url('frontend/js/document.js') }}"></script>
<script>
    $(document).on('click','input[name="is_pwd_protected"]', function() {
      $('#password_file_div').hide();
      if ($(this).is(':checked') && $(this).val() == '1') {
        $('#password_file_div').show();
      }
    });

  $("#uploadBankDocument").on('click',function(e) {  
    e.preventDefault(); 
    if (window.FormData) {
       var formData = new FormData(document.getElementById("bankdocumentForm"));
    }
    formData.append('reupload', 'reupload');
    formData.append('_token', '{{ csrf_token() }}');
    submitForm(formData);
  });

  $("#updateBankData").on('click',function(e) {  
    e.preventDefault(); 
    if (window.FormData) {
       var formData = new FormData(document.getElementById("bankdocumentForm"));
    }
    formData.append('reprocess', 'reprocess');
    formData.append('_token', '{{ csrf_token() }}');
    submitForm(formData)
  });

  function submitForm(formData) {
    file_doc_id = '{{ request()->get("app_doc_file_id") }}';
   $.ajax({
        url: $("form#bankdocumentForm").attr('action'),
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

            if (result['status']) {
              var appended_span = parent.document.getElementById("append_" + file_doc_id);
              appended_span.classList.remove("error");
              appended_span.classList.add("success");
              appended_span.innerHTML = result['message'];
              var getReportbtn = parent.document.getElementById("getReport");
              getReportbtn.classList.remove("hide");
              getReportbtn.setAttribute("biz_perfios_id", result['biz_perfios_id']);
              parent.document.getElementById("reprocess_" + file_doc_id).classList.add("hide");
            }
            if(result['errors']){
               $errors = result['errors'];
               Object.keys($errors).forEach(function(key) {
                   $('#bank_doc_' + key).append('<small class="error">'+ $errors[key] +'</small>');
               });
            }
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