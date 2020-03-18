@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="chargesForm" name="chargesForm" method="POST" action="{{route('save_charges')}}" target="_top">
        @csrf

        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_name">Charge Name</label>
                <input type="text" class="form-control" id="chrg_name" name="chrg_name" placeholder="Enter Charge Name" maxlength="50">
                <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
            </div>
            <!--      </div>
            
                  <div class="row">-->
            <div class="form-group col-md-6">
                <label for="chrg_type">Charge Description</label>
                <textarea class="form-control" id="chrg_desc" name="chrg_desc" placeholder="Charge Description" maxlength="500" style="height:35px;"></textarea>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_type">Credit Description</label>
                <textarea class="form-control" id="credit_desc" name="credit_desc" placeholder="Credit Description" maxlength="200" style="height:35px;"></textarea>
            </div>
            <!--      </div>
                    <div class="row">-->
            <div class="form-group col-md-6">
                <label for="chrg_type">Debit Description</label>
                <textarea class="form-control" id="debit_desc" name="debit_desc" placeholder="Debit Description" maxlength="200" style="height:35px;"></textarea>
            </div>
        </div>       

        <div class="row">
            <div class="form-group col-md-6">
                <div class="row">
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
                    <div class="form-group col-md-6" id="approved_limit_div">
                        <label for="chrg_type">Charge Applicable On</label>
                        <select class="form-control" name="chrg_applicable_id" id="chrg_applicable_id">
                            <option value="" selected>Select</option>
                            <option value="1">Limit Amount</option>
                            <option value="2">Outstanding Amount</option>
                            <option value="3">Outstanding Principal</option>
                            <option value="4">Outstanding Interest</option>
                            <option value="5">Overdue Amount</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-6 float-md-right">
                <label for="chrg_calculation_amt">Amount/Percent</label>
                <input type="text" class="form-control" id="chrg_calculation_amt" name="chrg_calculation_amt" placeholder="Charge Calculation Amount" maxlength="10">
            </div>

        </div>
        <div class="row">


            <!--      </div>
                 <div class="row">-->
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
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" name="chrg_type" value="3">Both
                    </label>
                </div>
            </div> 
            <!--        </div>           
                    <div class="row">-->
            <div class="form-group col-md-3">
                <label for="is_gst_applicable">GST Applicable</label><br />
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" checked name="is_gst_applicable" value="1">Yes
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" name="is_gst_applicable" value="2">No
                    </label>
                </div>
            </div>
            <div class="form-group col-md-3" id="gst_div">
             <label for="chrg_type"></label>
             <input type="hidden" class="form-control" name="gst_percentage" placeholder="GST Percentage" readonly="readonly" value="{{Config::get('payment.gst')}}" style="height:35px; margin-bottom: -20px">
        </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_tiger_id">Charge Trigger</label>
                <select class="form-control" name="chrg_tiger_id" id="chrg_tiger_id">
                    <option value="" selected>Select</option>
                    <option value="1">Limit Assignment</option>
                    <option value="2">First Invoice Disbursement</option>
                </select>
            </div>
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

        $(document).on('click', 'input[name="chrg_calculation_type"]', function (e) {
            if ($(this).val() == '2')
                $('#approved_limit_div').show();
            else
                $('#approved_limit_div').hide();
        })

        $(document).on('click', 'input[name="is_gst_applicable"]', function (e) {
            if ($(this).val() == '1')
                $('#gst_div').show();
            else
                $('#gst_div').hide();
        })


        $('#chargesForm').validate({// initialize the plugin
            rules: {
                'chrg_name': {
                    required: true,
                },
                'chrg_desc': {
                    required: true,
                },
                'credit_desc': {
                    required: true,
                },
                'debit_desc': {
                    required: true,
                },
                'chrg_calculation_amt': {
                    required: true,
                },
                'gst_percentage': {
                    required: true,
                },
                'chrg_tiger_id': {
                    required: true,
                },
                'chrg_applicable_id': {
                    required: true,
                },
                'is_active': {
                    required: true,
                },
            },
            messages: {
                'chrg_name': {
                    required: "Please enter Charge Name",
                },
                'chrg_desc': {
                    required: "Please enter Charge Description",
                },
                'credit_desc': {
                    required: "Please enter Credit Description",
                },
                'debit_desc': {
                    required: "Please enter Debit Description",
                },
                'chrg_calculation_amt': {
                    required: "Please enter Charge Amount",
                },
                'gst_percentage': {
                    required: "Please enter GST Percentage",
                },
                'chrg_tiger_id': {
                    required: "Please Select Charge Trigger",
                },
                'chrg_applicable_id': {
                    required: "Please Select Approved limit",
                },
                'is_active': {
                    required: "Please select charge Status",
                },
            }
        });
    });
</script>
@endsection