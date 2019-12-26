@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="chargesForm" name="chargesForm" method="POST" action="{{route('save_charges')}}" target="_top">
              @csrf

      <div class="row">
        <div class="form-group col-md-12">
          <label for="chrg_name">Charge Name</label>
          <input type="text" class="form-control" id="chrg_name" name="chrg_name" value="{{$charge_data->chrg_name}}" placeholder="Enter Charge Name" maxlength="50">
          <input type="hidden" class="form-control" id="id" name="id" maxlength="5" value="{{$charge_data->id}}">
          <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
        </div>
      </div>

      <div class="row">
         <div class="form-group col-md-12">
            <label for="chrg_type">Charge Description</label>
            <textarea class="form-control" id="chrg_desc" name="chrg_desc" placeholder="Charge Description" maxlength="500">{{$charge_data->chrg_desc}}</textarea>
        </div>
      </div>

      <div class="row">
         <div class="form-group col-md-6">
             <label for="chrg_type">Charge Type</label><br />
             <div class="form-check-inline ">
               <label class="form-check-label fnt">
               <input type="radio" class="form-check-input" {{$charge_data->chrg_type == 1 ? 'checked' : ($charge_data->chrg_type != 2 ? 'checked' : '' )}} name="chrg_type" value="1">Auto
               </label>
            </div>
            <div class="form-check-inline">
               <label class="form-check-label fnt">
               <input type="radio" class="form-check-input" {{$charge_data->chrg_type == 2 ? 'checked' : ''}} name="chrg_type" value="2">Manual
               </label>
            </div>
        </div>
        <div class="form-group col-md-6">
             <label for="chrg_type">Charge Calculation</label><br />
             <div class="form-check-inline ">
               <label class="form-check-label fnt">
               <input type="radio" class="form-check-input" {{$charge_data->chrg_calculation_type == 1 ? 'checked' : ($charge_data->chrg_calculation_type != 2 ? 'checked' : '' )}} name="chrg_calculation_type" value="1">Fixed
               </label>
            </div>
            <div class="form-check-inline">
               <label class="form-check-label fnt">
               <input type="radio" class="form-check-input" {{$charge_data->chrg_calculation_type == 2 ? 'checked' : ''}} name="chrg_calculation_type" value="2">Percentage
               </label>
            </div>
        </div>
      </div>
      <div class="row">
         <div class="form-group col-md-6">
             <label for="chrg_calculation_amt">Amount/Percent</label>
             <input type="text" class="form-control" id="chrg_calculation_amt" name="chrg_calculation_amt" placeholder="Charge Calculation Amount" value="{{$charge_data->chrg_calculation_amt}}" maxlength="10">
         </div>
         <div class="form-group col-md-6" id="approved_limit_div">
             <label for="chrg_type">Charge Applicable On</label>
              <select class="form-control" name="chrg_applicable_id" id="chrg_applicable_id">
                  <option disabled value="" selected>Select</option>
                  <option {{$charge_data->chrg_applicable_id == 1 ? 'selected' : ''}} value="1">Limit Amount</option>
                  <option {{$charge_data->chrg_applicable_id == 2 ? 'selected' : ''}} value="2">Outstanding Amount</option>
                  <option {{$charge_data->chrg_applicable_id == 3 ? 'selected' : ''}} value="3">Outstanding Principal</option>
                  <option {{$charge_data->chrg_applicable_id == 4 ? 'selected' : ''}} value="4">Outstanding Interest</option>
                  <option {{$charge_data->chrg_applicable_id == 5 ? 'selected' : ''}} value="5">Overdue Amount</option>
              </select>
         </div>
      </div>
      <div class="row">
         <div class="form-group col-md-6">
             <label for="is_gst_applicable">GST Applicable</label><br />
             <div class="form-check-inline">
               <label class="form-check-label fnt">
               <input type="radio" class="form-check-input" {{$charge_data->is_gst_applicable == 1 ? 'checked' : ($charge_data->is_gst_applicable != 2 ? 'checked' : '' )}} name="is_gst_applicable" value="1">Yes
               </label>
            </div>
            <div class="form-check-inline">
               <label class="form-check-label fnt">
               <input type="radio" class="form-check-input" {{$charge_data->is_gst_applicable == 2 ? 'checked' : ''}} name="is_gst_applicable" value="2">No
               </label>
            </div>
        </div>
        <div class="form-group col-md-6" id="gst_div">
             <label for="chrg_type">GST Percent</label>
             <input type="text" class="form-control" name="gst_percentage" value="{{$charge_data->gst_percentage}}" placeholder="GST Percentage">
        </div>
      </div>
      <div class="row">
         <div class="form-group col-md-6">
             <label for="chrg_type">Charge Trigger</label>
             <select class="form-control" name="chrg_tiger_id" id="chrg_tiger_id">
                  <option disabled value="" selected>Select</option>
                  <option {{$charge_data->chrg_tiger_id == 1 ? 'selected' : ''}} value="1">Limit Assignment</option>
                  <option {{$charge_data->chrg_tiger_id == 2 ? 'selected' : ''}} value="2">First Invoice Disbursement</option>
              </select>
        </div>
        <div class="form-group col-md-6">
             <label for="chrg_type">Status</label><br />
             <select class="form-control" name="is_active" id="is_active">
                  <option disabled value="" selected>Select</option>
                  <option {{$charge_data->is_active == 1 ? 'selected' : ''}} value="1">Active</option>
                  <option {{$charge_data->is_active == 2 ? 'selected' : ''}} value="2">In-Active</option>
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

     $(document).on('click', 'input[name="chrg_calculation_type"]', function (e) {
          if ($(this).val() == '2'){
             $('#approved_limit_div').show();
          }
          else {
            $('#approved_limit_div').hide();
          }
     })

    $(document).on('click', 'input[name="is_gst_applicable"]', function (e) {
      if ($(this).val() == '1'){ 
        $('#gst_div').show();
      }else{ 
        $('input[name="gst_percentage"]').val('');
        $('#gst_div').hide();
      }
    })

    $(document).ready(function () {
      var is_gst_applicable = $('input[name="is_gst_applicable"]:checked');
      var chrg_calculation_type = $('input[name="chrg_calculation_type"]:checked');

      if (is_gst_applicable.val() == 1){
       $('#gst_div').show();
      }else{
       $('input[name="gst_percentage"]').val('');
       $('#gst_div').hide();
      }

      if (chrg_calculation_type.val() == 2) {
        $('#approved_limit_div').show();
      }else{
       $('#approved_limit_div').hide();
     }


        $('#chargesForm').validate({ // initialize the plugin
            rules: {
                'chrg_name' : {
                    required : true,
                },
                'chrg_desc' : {
                    required : true,
                },
                'chrg_calculation_amt' : {
                    required : true,
                },
                'gst_percentage' : {
                    required : true,
                },
                'chrg_applicable_id' : {
                    required : true,
                },
                'chrg_tiger_id' : {
                    required : true,
                },
                'is_active' : {
                    required : true,
                },
            },
            messages: {
                'chrg_name': {
                    required: "Please enter Charge Name",
                },
                'chrg_desc': {
                    required: "Please enter Charge Description",
                },
                'chrg_calculation_amt': {
                    required: "Please enter Charge Amount",
                },
                'gst_percentage': {
                    required: "Please enter GST Percentage",
                },
                'chrg_applicable_id': {
                    required: "Please Select Approved limit",
                },
                'chrg_tiger_id': {
                    required: "Please select Charge trigger",
                },
                'is_active': {
                    required: "Please select charge Status",
                },
            }
        });
    });
</script>
@endsection