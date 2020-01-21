@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('save_assign_fi')}}" target="_top" onsubmit="return checkValidation();">
    @csrf
    <input type="hidden" value="" name="address_ids" id="address_ids">
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="email">Select Agency</label>
          <select class="form-control" name="agency_id" id="agency_id" onchange="fillAgencyUser(this.value);">
             <option value="">Select Agency</option>
             @forelse($agencies as $agency)
             <option value="{{$agency->agency_id}}">{{$agency->comp_name}}</option>
             @empty
             @endforelse
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="email">Select User</label>
          <select class="form-control" style="text-transform: capitalize;" name="to_id" id="to_id">
             <!-- <option value="">Select User</option> -->
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
            <label for="comment">Comment</label>
            <textarea class="form-control" name="comment" maxlength="250"></textarea>
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
var users = {!! json_encode($agency_users) !!};
$(document).ready(function(){
  $(parent.$('.address_id:checked')).each(function(i,ele){
    let current_id = $(ele).val();
    let org_ids = $('#address_ids').val();
    let address_ids = org_ids+'#'+current_id;
    $('#address_ids').val(address_ids);
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

function fillAgencyUser(agency_id){
  let html = '<option value="">Select User</option>';
  $.each(users, function(i,user){
    if(agency_id == user.agency_id)
      html += '<option value="'+user.user_id+'">'+user.f_name+' '+user.l_name+'</option>';
  });
  $('#to_id').html(html);
}
</script>
@endsection