@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('save_fi_upload')}}" target="_top" enctype="multipart/form-data" id="documentForm">
    @csrf
    <input type="hidden" value="" name="fiaid" id="fiaid">
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <!-- <label for="email">Select Agency</label> -->
          <div class="custom-file upload-btn-cls mb-3 mt-2">
            <input type="hidden" name="fi_addr_id">
	        <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file">
	        <label class="custom-file-label" for="customFile">Choose file</label>
	      </div>
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-12">
        <button type="submit" class="btn btn-success btn-sm float-right" id="savedocument">Submit</button>
      </div>
    </div>   
  </form>
 
@endsection

@section('jscript')
<script>
$.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, 'File size must be less than {0}');

$(document).ready(function(){
	let fiaid = parent.$('#fiaid4upload').val();
	$('#fiaid').val(fiaid);

	$('.getFileName').change(function(e) {
        var fileName = e.target.files[0].name;
        $(this).parent('div').children('.custom-file-label').html(fileName);
    });
    /* FUNCTION FOR VALIDATE */
    $('#documentForm').validate({
            rules: {
                'doc_file': {
                    required: true,
                    extension: "jpg,jpeg,png,pdf,doc,docx",
                    filesize : 200000000,
                }
            },
            messages: {
                'doc_file': {
                    required: "Please select file",
                    extension:"Please select jpg, png, pdf, doc, docx type format only.",
                    filesize:"Maximum size for upload 20 MB.",
                }
            }
        });

        // $('#documentForm').validate();

        $("#savedocument").click(function(){
            if($('#documentForm').valid()){
                $('form#documentForm').submit();
                $("#savedocument").attr("disabled","disabled");
            }  
        }); 
    /* FUNCTION FOR VALIDATE */

    
});

function checkValidation(){
    unsetError('select[name=agency_id]');

    let flag = true;
    let agency_id = $('select[name=agency_id]').val();

    if(to_id == ''){
        setError('select[name=to_id]', 'Plese select User');
        flag = false;
    }

    if(flag){
        return true;
    }else{
        return false;
    }
}
</script>
@endsection