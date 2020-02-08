@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="EquipmentForm" name="EquipmentForm" method="POST" action="{{route('save_equipment')}}" target="_top">
              @csrf

      <div class="row">
        <div class="form-group col-6">
          <label for="equipment_name">Equipment Name</label>
          <input type="text" class="form-control" id="equipment_name" name="equipment_name" value="{{$equipment_data->equipment_name}}" placeholder="Enter Equipment Name" maxlength="50">
          <input type="hidden" class="form-control" id="id" name="id" value="{{$equipment_data->id}}">
        </div>
        <div class="form-group col-6">
             <label for="state_type">Status</label><br />
             <select class="form-control" name="is_active" id="is_active">
                  <option disabled value="" selected>Select</option>
                  <option value="1" {{$equipment_data->is_active == 1 ? 'selected' : ''}}>Active</option>
                  <option value="2" {{$equipment_data->is_active == 2 ? 'selected' : ''}}>In-Active</option>
              </select>
        </div>
      </div>
      <div class="row">
         <div class="form-group col-md-12 mb-0">
             <input type="submit" class="btn btn-success btn-sm pull-right" name="add_equipment" id="add_equipment" value="Submit"/>
        </div>
      </div>
   </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function () {


        $('#EquipmentForm').validate({ // initialize the plugin
            rules: {
                'equipment_name' : {
                    required : true,
                },
                'is_active' : {
                    required : true,
                },
            },
            messages: {
                'equipment_name': {
                    required: "Please enter Equipment Name",
                },
                'is_active': {
                    required: "Please select Equipment Status",
                },
            }
        });
    });
</script>
@endsection