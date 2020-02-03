@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="SegmentForm" name="SegmentForm" method="POST" action="{{route('save_segment')}}" target="_top">
              @csrf

      <div class="row">
        <div class="form-group col-6">
          <label for="name">Segment Name</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="Enter Segment Name" maxlength="50">
        </div>
        <div class="form-group col-6">
             <label for="state_type">Status</label><br />
             <select class="form-control" name="is_active" id="is_active">
                  <option disabled value="" selected>Select</option>
                  <option value="1">Active</option>
                  <option value="2">In-Active</option>
              </select>
        </div>
      </div>
      <div class="row">
         <div class="form-group col-md-12 mb-0">
             <input type="submit" class="btn btn-success btn-sm pull-right" name="add_segment" id="add_segment" value="Submit"/>
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