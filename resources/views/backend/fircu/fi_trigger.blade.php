@extends('layouts.backend.admin_popup_layout')
@section('content')
<div>
  <form method="POST" action="{{route('save_assign_fi')}}">
    <div class="row">
      <input type="hidden" value="" name="address_ids" id="address_ids">
      <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
      <div class="col-md-12">
        <div class="form-group">
          <label for="email">Select Agency</label>
          <select class="form-control" name="agency_id">
             <option value="">Select Agency</option>
             <option value="1">Agency 1</option>
             <option value="2">Agency 2</option>
             <option value="3">Agency 3</option>
             <option value="4">Agency 4</option>
             <option value="5">Agency 5</option>
             <option value="6">Agency 6</option>
          </select>
        </div>

        <div class="form-group">
          <label for="email">Select User</label>
          <select class="form-control" name="to_id">
             <option value="">Select User</option>
             <option value="1">User 1</option>
             <option value="2">User 2</option>
             <option value="3">User 3</option>
             <option value="4">User 4</option>
             <option value="5">User 5</option>
             <option value="6">User 6</option>
          </select>
        </div>

        <div class="form-group">
            <label for="comment">Comment</label>
            <textarea class="form-control" name="comment"></textarea>
        </div>
        <button type="submit" class="btn btn-success btn-sm float-right">Submit</button>    
      </div>
    </div>
  </form>
</div>  
@endsection

@section('jscript')
<script>
$(document).ready(function(){
  $(parent.$('.address_id:checked')).each(function(i,ele){
    let current_id = $(ele).val();
    let org_ids = $('#address_ids').val();
    let address_ids = org_ids+'#'+current_id;
    $('#address_ids').val(address_ids);
  })
});
</script>
@endsection