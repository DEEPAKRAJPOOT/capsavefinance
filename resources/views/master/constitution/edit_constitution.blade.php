@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="stateForm" name="stateForm" method="POST" action="{{route('save_constitution')}}" target="_top">
              @csrf

      <div class="row">
          <input type="hidden" class="form-control" id="id" name="id" value="{{$consti_data->id}}">
        <div class="form-group col-6">
          <label for="name">Constitution Name</label>
          <input type="text" class="form-control" id="name" name="name" value="{{$consti_data->name}}" placeholder="Enter Constitution Name" maxlength="50">
        </div>
        <div class="form-group col-6">
             <label for="state_type">Status</label><br />
             <select class="form-control" name="is_active" id="is_active">
                  <option disabled value="" selected>Select</option>
                  <option value="1" {{$consti_data->is_active == 1 ? 'selected' : ''}}>Active</option>
                  <option value="2" {{$consti_data->is_active == 2 ? 'selected' : ''}}>In-Active</option>
              </select>
        </div>
      </div>
      <div class="row">
         <div class="form-group col-md-12 mb-0">
             <input type="submit" class="btn btn-success btn-sm" name="add_constitution" id="add_constitution" value="Submit"/>
        </div>
      </div>
   </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function () {


        $('#stateForm').validate({ // initialize the plugin
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
                    required: "Please enter Constitution Name",
                },
                'is_active': {
                    required: "Please select Constitution Status",
                },
            }
        });
    });
</script>
@endsection