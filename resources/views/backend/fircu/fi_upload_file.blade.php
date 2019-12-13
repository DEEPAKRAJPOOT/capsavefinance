@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('save_fi_upload')}}" target="_top" onsubmit="return checkValidation1();" enctype="multipart/form-data">
    @csrf
    <input type="hidden" value="" name="address_ids" id="address_ids">
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <!-- <label for="email">Select Agency</label> -->
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
	let address_id = parent.$('.address_id:checked').val();
	$('#address_ids').val(address_ids);

	$('.getFileName').change(function(e) {
        var fileName = e.target.files[0].name;
        $(this).parent('div').children('.custom-file-label').html(fileName);
    });

    
});

function checkValidation(){
    unsetError('select[name=agency_id]');
    unsetError('select[name=to_id]');
    //unsetError('textarea[name=comment]');

    let flag = true;
    let agency_id = $('select[name=agency_id]').val();
    let to_id = $('select[name=to_id]').val();
    //let comment = $('textarea[name=comment]').val().trim();

    if(agency_id == ''){
        setError('select[name=agency_id]', 'Plese select Agency');
        flag = false;
    }

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