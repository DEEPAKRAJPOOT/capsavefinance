@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="agencyForm" name="agencyForm" method="POST" action="{{route('save_charges')}}" target="_top">
              @csrf

      <div class="row">
        <div class="form-group col-md-12">
          <label for="chrg_name">Charge Name</label>
          <input type="text" class="form-control" id="chrg_name" name="chrg_name" placeholder="Enter Charge Name" maxlength="50">
          <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
        </div>
      </div>

      <div class="row">
         <div class="form-group col-md-12">
            <label for="chrg_type">Charge Description</label>
            <textarea class="form-control" id="chrg_desc" name="chrg_desc" placeholder="Charge Description" maxlength="500"></textarea>
        </div>
      </div>

      <div class="row">
         <div class="form-group col-md-6">
             <label for="chrg_type">Charge Type</label><br />
             <div class="form-check-inline ">
               <label class="form-check-label fnt">
               <input type="radio" class="form-check-input" checked name="chrg_type" value="1">Auto
               </label>
            </div>
            <div class="form-check-inline">
               <label class="form-check-label fnt">
               <input type="radio" class="form-check-input" name="chrg_type" value="2">Manual
               </label>
            </div>
        </div>
        <div class="form-group col-md-6">
             <label for="chrg_type">Charge Calculation</label><br />
             <div class="form-check-inline ">
               <label class="form-check-label fnt">
               <input type="radio" class="form-check-input" name="chrg_calculation_type" value="1">Fixed
               </label>
            </div>
            <div class="form-check-inline">
               <label class="form-check-label fnt">
               <input type="radio" class="form-check-input" checked name="chrg_calculation_type" value="2">Percentage
               </label>
            </div>
        </div>
      </div>
      <div class="row">
         <div class="form-group col-md-6">
             <label for="chrg_calculation_amt">Amount/Percent</label>
             <input type="text" class="form-control" id="chrg_calculation_amt" name="chrg_calculation_amt" placeholder="Charge Calculation Amount" maxlength="10">
         </div>
         <div class="form-group col-md-6">
             <label for="chrg_type">Approved Limit</label>
              <select class="form-control" name="chrg_applicable_id" id="chrg_applicable_id">
                  <option>Select</option>
                  <option value="1">Limit Amount</option>
                  <option value="2">Outstanding Amount</option>
                  <option value="3">Outstanding Principal</option>
                  <option value="4">Outstanding Interest</option>
                  <option value="5">Overdue Amount</option>
              </select>
         </div>
      </div>
      <div class="row">
         <div class="form-group col-md-6">
             <label for="is_gst_applicable">GST Applicable</label><br />
             <div class="form-check-inline">
               <label class="form-check-label fnt">
               <input type="radio" class="form-check-input" checked name="is_gst_applicable" value="1">Yes
               </label>
            </div>
            <div class="form-check-inline">
               <label class="form-check-label fnt">
               <input type="radio" class="form-check-input" name="is_gst_applicable" value="0">No
               </label>
            </div>
        </div>
        <div class="form-group col-md-6">
             <label for="chrg_type">GST Percent</label>
             <input type="text" class="form-control" id="chrg_type" placeholder="GST Percentage">
        </div>
      </div>
      <div class="row">
         <div class="form-group col-md-6">
             <label for="chrg_type">Charge Trigger</label>
             <select class="form-control" name="chrg_tiger_id" id="chrg_tiger_id">
                  <option>Select</option>
                  <option value="1">Limit Assignment</option>
                  <option value="2">First Invoice Disbursement</option>
              </select>
        </div>
        <div class="form-group col-md-6">
             <label for="chrg_type">Status</label><br />
             <select class="form-control" name="is_active" id="is_active">
                  <option>Select</option>
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