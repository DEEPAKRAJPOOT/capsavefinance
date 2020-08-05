@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="tdsForm" name="tdsForm" method="POST" action="{{ route('save_tds') }}" target="_top">
              @csrf

      <div class="row">
        <div class="form-group col-6">
          <label for="tds_percentage">TDS Percentage</label>
          <input type="text" class="form-control" id="tds_percentage" name="tds_percentage" placeholder="Enter TDS Percentage" maxlength="50">
        </div>
        <div class="form-group col-6">
             <label for="state_type">Status</label><br />
             <select class="form-control" name="is_active" id="is_active">
                  <option disabled value="" selected>Select</option>
                  <option value="1">Active</option>
                  <option value="0">In-Active</option>
              </select>
        </div>
      </div>
      <div class="row">
         <div class="form-group col-md-12 mb-0">
             <input type="submit" class="btn btn-success btn-sm pull-right" name="add_tds_percentage" id="add_tds_percentage" value="Submit"/>
        </div>
      </div>
   </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function () {


        $('#tdsForm').validate({ // initialize the plugin
            rules: {
                'tds_percentage' : {
                    required : true,
                },
                'is_active' : {
                    required : true,
                },
            },
            messages: {
                'tds_percentage': {
                    required: "Please enter TDS Percentage",
                },
                'is_active': {
                    required: "Please select TDS Status",
                },
            }
        });
    });
</script>
@endsection