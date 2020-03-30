@extends('layouts.backend.admin_popup_layout')
@section('content')

<form method="POST" style="width:100%;" action="{{route('save_rcu_upload')}}" target="_top" id="documentForm" enctype="multipart/form-data">
    @csrf
    <input type="hidden" value="" name="rcu_doc_id" id="rcuDocId">
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="custom-file upload-btn-cls mb-3 mt-2">
	        <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file" accept="image/*,.xlsx,.xls,.doc,.docx,.pdf">
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

    $(document).ready(function () {
        /* rcu doc id filler*/
        
        let rcuDocId = parent.$('#rcuDId').val();
        $('#rcuDocId').val(rcuDocId);

        /* file name into input type file on choose */
        $('.getFileName').change(function(e) {
            var fileName = e.target.files[0].name;
            $(this).parent('div').children('.custom-file-label').html(fileName);
        });

        /* validator */
        
        $('#documentForm').validate({ // initialize the plugin
            rules: {
                'doc_file': {
                    required: true,
                    extension: "jpg,png,pdf,doc,dox,xls,xlsx,msg",
                    filesize : 200000000,
                }
            },
            messages: {
                'doc_file': {
                    required: "Please select file",
                    extension:"Please select jpg, png, pdf, doc, docx, xls, xlsx, msg type format only.",
                    filesize:"maximum size for upload 20 MB.",
                }
            }
        });

        $('#documentForm').validate();

        $("#savedocument").click(function(){
            if($('#documentForm').valid()){
                $('form#documentForm').submit();
                $("#savedocument").attr("disabled","disabled");
            }  
        });            

    });

</script>
@endsection