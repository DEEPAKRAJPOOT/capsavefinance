@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="chargesForm" name="chargesForm" method="POST" action="{{route('save_charges')}}" target="_top">
        @csrf

        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_name">Charge Name</label>
                <input type="text" class="form-control" id="chrg_name" name="chrg_name" value="{{$charge_data->chrg_name}}" placeholder="Enter Charge Name" maxlength="50">
                <input type="hidden" class="form-control" id="id" name="id" maxlength="5" value="{{$charge_data->id}}">
                <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
            </div>
            <div class="form-group col-md-6">
                <label for="chrg_type">Charge Description</label>
                <textarea class="form-control" id="chrg_desc" name="chrg_desc" placeholder="Charge Description" maxlength="500" style="height:35px;">{{$charge_data->chrg_desc}}</textarea>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_type">Credit Description</label>
                <textarea class="form-control" id="credit_desc" name="credit_desc" placeholder="Credit Description" maxlength="200" style="height:35px;">{{$charge_data->credit_desc}}</textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="chrg_type">Debit Description</label>
                <textarea class="form-control" id="debit_desc" name="debit_desc" placeholder="Debit Description" maxlength="200" style="height:35px;">{{$charge_data->debit_desc}}</textarea>
            </div>
        </div>       
        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_type">Charge Based On</label><br />
                <div class="form-check-inline ">
                    <label class="form-check-label fnt">
                        <input disabled="" type="radio" class="form-check-input" checked name="based_on" value="1" {{ ($charge_data->based_on == 1) ? 'checked' : '' }}>Program Based
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input disabled="" type="radio" class="form-check-input" name="based_on" value="2" {{ ($charge_data->based_on == 2) ? 'checked' : '' }}>Customer Based
                    </label>
                </div>
            </div>
        </div>               

        <div class="row">
            <div class="form-group col-md-6">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="chrg_type">Charge Calculation</label><br />
                        <div class="form-check-inline ">
                            <label class="form-check-label fnt">
                                <input type="radio" class="form-check-input charge_calculation_type" {{$charge_data->chrg_calculation_type == 1 ? 'checked' : ($charge_data->chrg_calculation_type != 2 ? 'checked' : '' )}} name="chrg_calculation_type" value="1">Fixed
                            </label>
                        </div>
                        <div class="form-check-inline" id="cust_hide_div">
                            <label class="form-check-label fnt">
                                <input type="radio" class="form-check-input charge_calculation_type" {{$charge_data->chrg_calculation_type == 2 ? 'checked' : ''}} name="chrg_calculation_type" value="2">Percentage
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-md-6" id="approved_limit_div">
                        <label for="chrg_type">Charge Applicable On</label>
                        <select class="form-control" name="chrg_applicable_id" id="chrg_applicable_id">
                            <option value="" selected>Select</option>
                            <option {{$charge_data->chrg_applicable_id == 1 ? 'selected' : ''}} value="1">Limit Amount</option>
                            <option {{$charge_data->chrg_applicable_id == 2 ? 'selected' : ''}} value="2">Outstanding Amount</option>
                            <option {{$charge_data->chrg_applicable_id == 3 ? 'selected' : ''}} value="3">Outstanding Principal</option>
                            <!-- <option {{$charge_data->chrg_applicable_id == 4 ? 'selected' : ''}} value="4">Outstanding Interest</option> -->
                            <!-- <option {{$charge_data->chrg_applicable_id == 5 ? 'selected' : ''}} value="5">Overdue Amount</option> -->
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-6" id="chrg_calculation_amt_div">
                <label for="chrg_calculation_amt" id="amount_label">Amount/Percent</label>
                <input type="text" class="form-control {{$charge_data->chrg_calculation_type == 1 ? 'formatNum' : 'amtpercnt' }}" id="chrg_calculation_amt" name="chrg_calculation_amt" placeholder="Charge Calculation Amount" value="{{$charge_data->chrg_calculation_amt}}" maxlength="10">
            </div>

        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_type">Charge Type</label><br />
                <div class="form-check-inline ">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$charge_data->chrg_type == 1 ? 'checked' : ($charge_data->chrg_type == 1 ? 'checked' : '' )}} name="chrg_type" value="1">Auto
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$charge_data->chrg_type == 2 ? 'checked' : ($charge_data->chrg_type == 2 ? 'checked' : '' )}} name="chrg_type" value="2">Manual
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$charge_data->chrg_type == 3 ? 'checked' : ($charge_data->chrg_type == 3 ? 'checked' : '' )}} name="chrg_type" value="3">Both
                    </label>
                </div>
            </div>
            <div class="form-group col-md-3">
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
            <div class="form-group col-md-3" id="gst_div">
                <label for="chrg_type"></label>
                <input type="text" class="form-control" readonly="readonly" name="gst_percentage" value="{{($charge_data->gst_percentage) ? $charge_data->gst_percentage : Config::get('payment.gst')}}" placeholder="GST Percentage" style="height:35px; margin-bottom: -0px">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6" id="chrg_tiger_div">
                <label for="chrg_type">Charge Trigger</label>
                <select class="form-control" name="chrg_tiger_id" id="chrg_tiger_id">
                    <option value="" selected>Select</option>
                    <option {{$charge_data->chrg_tiger_id == 3 ? 'selected' : ''}} value="3">None</option>
                    <option {{$charge_data->chrg_tiger_id == 1 ? 'selected' : ''}} value="1">Limit Assignment</option>
                    <option {{$charge_data->chrg_tiger_id == 2 ? 'selected' : ''}} value="2">First Invoice Disbursement</option>

                    <option {{$charge_data->chrg_tiger_id == 4 ? 'selected' : ''}} value="4">Limit Enhancement</option>
                    <option {{$charge_data->chrg_tiger_id == 5 ? 'selected' : ''}} value="5">Limit Renewal</option>
                    <option {{$charge_data->chrg_tiger_id == 6 ? 'selected' : ''}} value="6">Limit Closure</option>
                </select>
            </div>
            <div class="form-group col-md-6 float-md-right">
                <label for="sac_code">SAC Code</label>
                <input type="text" class="form-control" id="sac_code" name="sac_code" value="{{$charge_data->sac_code}}" placeholder="Enter SAC Code" maxlength="10" onkeyup="sac_validation()">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_type">Status</label><br />
                <select class="form-control" name="is_active" id="is_active">
                    <option value="" selected>Select</option>
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
    var messages={
        check_applied_charge_url:"{{ route('check_applied_charge') }}",
        token: "{{ csrf_token() }}",
        gst_percentage: "{{Config::get('payment.gst')}}"
    }
    $(document).on('click', 'input[name="chrg_calculation_type"]', function (e) {
        var basedOn = $('input[name="based_on"]:checked').val();
        if ($(this).val() == '2' && basedOn == 2){
            $('#approved_limit_div').hide();
            $('#chrg_tiger_div').hide();
            // $('#chrg_calculation_amt_div').hide();
        }
        if ($(this).val() == '2') {
            // $('#chrg_calculation_amt_div').hide();
            $('#approved_limit_div').show();
        } else {
            // $('#chrg_calculation_amt_div').show();
            $('#chrg_applicable_id option:selected').removeAttr('selected');
            $('#approved_limit_div').hide();
        }
    })

    $(document).on('click', 'input[name="is_gst_applicable"]', function (e) {
        if ($(this).val() == '1') {
            $('#gst_div').show();
            $('input[name="gst_percentage"]').val(messages.gst_percentage);
        } else {
            $('input[name="gst_percentage"]').val('');
            $('#gst_div').hide();
        }
    })


    $(document).on('click', 'input[name="based_on"]', function (e) {
        if ($(this).val() == '2') {
            $('#gst_div').show();
            $('#approved_limit_div').hide();
            $('#chrg_tiger_div').hide();
            $('input[name="chrg_calculation_type"]:checked').attr('disabled', true);
            // $('#chrg_calculation_amt_div').hide();
        }
        else{
            // $('input[name="gst_percentage"]').val('');
            $('#approved_limit_div').show();
            $('#chrg_tiger_div').show();
            $('#gst_div').show();
            // $('#chrg_calculation_amt_div').show();
        }
    })
    $(document).ready(function () {
        
        $.validator.addMethod("isChrgApplied",
            function(value, element, params) {
                var result = true;
                var data = {chrg_id : params.chrg_id, _token: messages.token};
                
                $.ajax({
                    type:"POST",
                    async: false,
                    url: messages.check_applied_charge_url,
                    data: data,
                    success: function(data) {                         
                        if (value == 2) {                            
                            result = (data.is_active == 1) ? false : true;
                        } else {
                            result = true;
                        }
                    }
                });                
                return result;                
            },'This charge is already applied, You can\'t make in-active.'
        );  

        var is_gst_applicable = $('input[name="is_gst_applicable"]:checked');
        var chrg_calculation_type = $('input[name="chrg_calculation_type"]:checked');

        if (is_gst_applicable.val() == 1) {
            $('#gst_div').show();
        } else {
            $('#gst_div').hide();
            $('input[name="gst_percentage"]').val('');
        }

        if (chrg_calculation_type.val() == 2) {
            $('#approved_limit_div').show();
        } else {
            $('#chrg_applicable_id option:selected').removeAttr('selected');
            $('#approved_limit_div').hide();
        }

        if ($('input[name="based_on"]:checked').val() == '2') {
            $('#approved_limit_div').hide();
            $('#chrg_tiger_div').hide();
            $("#chrg_calculation_type1").prop('disabled',false);
            $("#chrg_calculation_type1").attr('checked', true);
            $("#cust_hide_div").css({"display":"none"});
            $("#amount_label").text("Amount");
            // $('#chrg_calculation_amt_div').hide();
        }
        else{
            // $('input[name="gst_percentage"]').val('');
            $('#approved_limit_div').show();
            $('#chrg_tiger_div').show();
            $('#gst_div').show();
            $('#chrg_calculation_amt_div').show();
            $("#amount_label").text("Amount/Percent");
        }


        $('#chargesForm').validate({// initialize the plugin
            rules: {
                'chrg_name': {
                    required: true,
                    uniqueCharge: {
                        chrg_id:$("#id").val()
                    }
                },
                'chrg_desc': {
                    required: true,
                },
                'chrg_calculation_amt': {
                    required: true,
                },
                'gst_percentage': {
                    required: true,
                },
                'chrg_applicable_id': {
                    required: true,
                },

                'sac_code': {
                    required: true,
                    digits: true,
                    maxlength: 4,
                },
                'is_active': {
                    required: true,
                    isChrgApplied: {
                        chrg_id:$("#id").val()
                    }                    
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

                'sac_code': {
                    required: "Please enter SAC Code",
                },
                'is_active': {
                    required: "Please select charge Status",
                },
            }
        });
        
        $(document).on('click', '.charge_calculation_type', function () {
            
            $('#chrg_calculation_amt').val('');            

            if ($(this).val() == 1) {
                 $('#chrg_calculation_amt').addClass('formatNum').removeClass('amtpercnt');                
            } else {
                 $('#chrg_calculation_amt').addClass('amtpercnt').removeClass('formatNum');
            }
        });         
    });

    function sac_validation() {
        var val = document.getElementById('sac_code').value;
        if(isNaN(val)) {
            document.getElementById('sac_code').value = ""
        } else if(val.length > 4) {
            document.getElementById('sac_code').value = ""
        }
    }
</script>
@endsection