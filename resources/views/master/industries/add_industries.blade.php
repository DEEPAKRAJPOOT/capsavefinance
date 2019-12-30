@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="industriesForm" name="industriesForm" method="POST" action="{{route('save_industries')}}" target="_top">
              @csrf

      <div class="row">
        <div class="form-group col-md-12">
          <label for="chrg_name">Industry Name</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="Enter Industry Name" maxlength="50">
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-6">
             <label for="chrg_type">Status</label><br />
             <select class="form-control" name="is_active" id="is_active">
                  <option value="" selected>Select</option>
                  <option value="1">Active</option>
                  <option value="2">In-Active</option>
              </select>
        </div>
      </div>
      <div class="row">
         <div class="form-group col-md-12 text-right">
             <input type="submit" class="btn btn-success btn-sm" name="add_charge" id="add_charge" value="Submit"/>
        </div>
      </div>
   </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function () {
        $('#industriesForm').validate({ // initialize the plugin
            rules: {
                'name' : {
                    required : true,
                },
                'is_active' : {
                    required : true,
                },
            },
            messages: {
                'name': {
                    required: "Please enter Industry Name",
                },
                'is_active': {
                    required: "Please Select Status of Industry",
                },
            }
        });
    });
</script>
@endsection