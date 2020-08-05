@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="tdsForm" name="tdsForm" method="POST" action="{{ route('save_tds') }}" target="_top">
              @csrf

      <div class="row">
        <div class="form-group col-6">
          <label for="tds_per">TDS Percentage</label>
          <input type="text" class="form-control" id="tds_per" name="tds_per" placeholder="Enter TDS Percentage" maxlength="50">
        </div>
        <div class="form-group col-6">
            <label for="start_date">Start Date <span class="mandatory">*</span></label>
            <input type="text" name="start_date" id="start_date" readonly="readonly" class="form-control" value="{{old('start_date')}}">
            {!! $errors->first('start_date', '<span class="error">:message</span>') !!}
        </div>
      </div>
      <div class="row">
        <div class="form-group col-12">
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
             <input type="submit" class="btn btn-success btn-sm pull-right" name="add_tds_per" id="add_tds_per" value="Submit"/>
        </div>
      </div>
   </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function () {

        $("#start_date").datetimepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            minView: 2,
            endDate: new Date()
        });
        
        $("#end_date").datetimepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            minView: 2,
            startDate: new Date()
        });


        $('#tdsForm').validate({ // initialize the plugin
            rules: {
                'tds_per' : {
                    required : true,
                    number: true,
                    max: 100,
                },
                'is_active' : {
                    required : true,
                },
            },
            messages: {
                'tds_per': {
                    required: "Please enter TDS Percentage",
                },
                'is_active': {
                    required: "Please select TDS Status",
                },
            }
        });
    });
    
    
    document.getElementById('tds_per').addEventListener('input', event =>{
        let values = document.getElementById('tds_per').value;
        let s = values.toString();
        if(isNaN(document.getElementById('tds_per').value || event.keyCode(190))) {
            document.getElementById('tds_per').value = ""

        }
        if(s.length >= 6) {
            document.getElementById('tds_per').value = ""
        }
    });
</script>
@endsection