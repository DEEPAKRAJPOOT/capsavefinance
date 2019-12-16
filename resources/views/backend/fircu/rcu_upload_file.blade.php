@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('save_rcu_upload')}}" target="_top" onsubmit="return checkValidation1();" enctype="multipart/form-data">
    @csrf
    <input type="hidden" value="" name="rcu_doc_id" id="rcuDocId">
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <div class="custom-file upload-btn-cls mb-3 mt-2">
	        <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file">
	        <label class="custom-file-label" for="customFile">Choose file</label>
	      </div>
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-12">
        <button type="submit" class="btn btn-success btn-sm float-right">Submit</button>
      </div>
    </div>   
  </form>
 
@endsection

@section('jscript')
<script>
$(document).ready(function(){
    let rcuDocId = parent.$('#rcuDId').val();
    $('#rcuDocId').val(rcuDocId);

    $('.getFileName').change(function(e) {
        var fileName = e.target.files[0].name;
        $(this).parent('div').children('.custom-file-label').html(fileName);
    });

    
});

</script>
@endsection