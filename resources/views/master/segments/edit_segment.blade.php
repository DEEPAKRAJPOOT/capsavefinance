@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="SegmentForm" name="SegmentForm" method="POST" action="{{route('save_segment')}}" target="_top">
              @csrf

      <div class="row">
        <input type="hidden" class="form-control" id="id" name="id" value="{{$segment_data->id}}">
        <div class="form-group col-6">
          <label for="name">Segments Name</label>
          <input type="text" class="form-control" id="name" name="name" value="{{$segment_data->name}}" placeholder="Enter Segments Name" maxlength="50">
        </div>
        <div class="form-group col-6">
             <label for="state_type">Status</label><br />
             <select class="form-control" name="is_active" id="is_active">
                  <option disabled value="" selected>Select</option>
                  <option value="1" {{$segment_data->is_active == 1 ? 'selected' : ''}}>Active</option>
                  <option value="2" {{$segment_data->is_active == 2 ? 'selected' : ''}}>In-Active</option>
              </select>
        </div>
      </div>
      <div class="row">
         <div class="form-group col-md-12 mb-0">
             <input type="submit" class="btn btn-success btn-sm" name="add_segment" id="add_segment" value="Submit"/>
        </div>
      </div>
   </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function () {


        $('#SegmentForm').validate({ // initialize the plugin
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
                    required: "Please enter Segment Name",
                },
                'is_active': {
                    required: "Please select Segment Status",
                },
            }
        });
    });
</script>
@endsection