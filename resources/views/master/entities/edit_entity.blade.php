@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
     <form id="entityForm" name="entityForm" method="POST" action="{{route('save_entity')}}" target="_top">
              @csrf

      <div class="row">
        <div class="form-group col-md-6">
          <label for="entity_name">Entity Name</label>
          <input type="text" class="form-control" id="entity_name" name="entity_name" value="{{$entity_data->entity_name}}" placeholder="Enter Entity Name" maxlength="50">
          <input type="hidden" class="form-control" id="id" name="id" maxlength="5" value="{{$entity_data->id}}">
          <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
        </div>
      </div>
      
      <div class="row">
        <div class="form-group col-md-6">
             <label for="entity_type">Status</label><br />
             <select class="form-control" name="is_active" id="is_active">
                  <option disabled value="" selected>Select</option>
                  <option {{$entity_data->is_active == 1 ? 'selected' : ''}} value="1">Active</option>
                  <option {{$entity_data->is_active == 2 ? 'selected' : ''}} value="2">In-Active</option>
              </select>
        </div>
      </div>
      <div class="row">
         <div class="form-group col-md-12">
             <input type="submit" class="btn btn-success btn-sm" name="add_charge" id="add_charge" value="Submit"/>
        </div>
      </div>
   </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function () {


        $('#entityForm').validate({ // initialize the plugin
            rules: {
                'entity_name' : {
                    required : true,
                },
                'is_active' : {
                    required : true,
                },
            },
            messages: {
                'entity_name': {
                    required: "Please enter Entity Name",
                },
                'is_active': {
                    required: "Please select Entity Status",
                },
            }
        });
    });
</script>
@endsection