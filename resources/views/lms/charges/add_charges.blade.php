@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="chargesForm" name="chargesForm" method="POST" action="{{route('save_manual_charges')}}" target="_top">
        @csrf

        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_name">Customer Name</label>
                <select class="form-control" id="user_id" name="user_id" readonly="readonly">
                    <option value="{{$customer->user_id}}">{{$customer->f_name}} {{$customer->l_name}}</option>
                </select>
                </select>
                 <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
            </div>

            <div class="form-group col-md-6">
                <label for="chrg_name">Charge Based On</label>
                <select class="form-control" id="based_on" name="based_on">
                    <option value="">Please Select</option>   
                    <option value="1" selected="">Program Based</option>
                    <option value="2">Customer Based</option>
                </select>
            </div>
            <div class="form-group col-md-6" id="program_div">
                <label for="chrg_name">Program</label>
                <select class="form-control" id="program_id" name="program_id">
                    <option value="">Please Select</option>
                    @if($program)
                    @foreach($program as $value)    
                    <option value="{{$value->prgm_id}}">{{$value->prgm_name}}</option>
                    @endforeach
                    @else
                    <option value="">No data found</option>
                    @endif
                </select>
                <span id="msgprogram" class="error"></span>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_name">Charge</label>
                <select class="form-control chrg_name" id="chrg_name" name="chrg_name">

                </select>
                <span id="chrgMsg" class="error"></span>
                 <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
            </div>
            <div class="form-group col-md-6">          
                <label for="chrg_type">Charge Type</label>&nbsp;&nbsp;<span id="RadioValidation" class="error"></span><br>            
                <div class="form-check-inline ">              
                    <label class="form-check-label fnt">               
                        <input type="radio" class="form-check-input chrgT" id="chrg_calculation_type1" name="chrg_calculation_type" value="1"> &nbsp;&nbsp;Fixed </label>            
                </div>
                <div class="form-check-inline">               
                    <label class="form-check-label fnt">               
                        <input type="radio" class="form-check-input chrgT" id="chrg_calculation_type2"  name="chrg_calculation_type" value="2">&nbsp;&nbsp;Percentage</label>

                </div>
            </div> 


        </div>
        <div class="row unsettledPayment" style="display: none">
            <div class="form-group col-md-12 payment">
                <label for="chrg_type">Select Payment</label>
                <select class="form-control" id="payment" name="payment">
                    <option value="" disabled selected>Choose Payment</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_name">Amount/Percent</label>
                <input type="text"  class="form-control" readonly="readonly" id="amount" name="amount" placeholder="Charge Calculation Amount" maxlength="50">

            </div>
            <div class="form-group col-md-6 chargeTypeCal" id="approved_limit_div"  style="display: none">
                <label for="chrg_type">Charge Applicable On</label>
                <select class="form-control chrg_applicable_id" name="chrg_applicable_id" id="chrg_applicable_id">

                </select>

            </div>

        </div>
        <div class="row">

            <div class="form-group col-md-6 chargeTypeCal" style="display: none">
                <label for="chrg_type">Limit Amount</label>
                <input type="text" readonly="readonly"  class="form-control" id="limit_amount_new" name="limit_amount_new">
            </div>
            <div class="form-group col-md-6 chargeTypeCal" style="display: none">
                <label for="chrg_name"> Charge Amount</label>
                <input type="text" readonly="readonly"  class="form-control" id="charge_amount_new" name="charge_amount_new"  value="" >
            </div>

        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="is_gst_applicable">GST Applicable</label><br>
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input gstAppli" id="is_gst_applicable1"  name="is_gst_applicable" value="1">Yes
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input gstAppli" id="is_gst_applicable2"  name="is_gst_applicable" value="2">No
                    </label>
                </div>
            </div>
            <div class="form-group col-md-6 chargeTypeGstCal"  style="display: none">
                <label for="chrg_name"> Charge Amount with GST</label>
                <input type="text" readonly="readonly"  class="form-control" id="charge_amount_gst_new" name="charge_amount_gst_new"  value="" >
            </div> </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_name"> Date</label>
                <input type="text" readonly="readonly"  class="form-control datepicker-charge_date" id="charge_date" name="charge_date" placeholder="Enter Date" value="{{ \Helpers::convertDateTimeFormat(\Helpers::getSysStartDate(), $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d/m/Y') }}" >
            </div>
        </div>


        <div class="row">
            <div class="form-group col-md-6 text-left">

            </div>
            <div class="form-group col-md-6 text-right">
                <span  id="submitMsg" class="error"></span>
                <input type="hidden"   id="id" name="id" >
                <input type="hidden"   id="app_id" name="app_id"  value="{{$user->app_id}}">
                <input type="hidden"   id="pay_from" name="pay_from"  value="{{$user->user->is_buyer}}">
                <input type="hidden"   id="charge_type" name="charge_type"  value="">
                <input type="hidden"   id="programamount" name="programamount" >
                <input type="hidden"   id="chrg_applicable_hidden_id" name="chrg_applicable_hidden_id" >
                <input type="submit" class="btn btn-success btn-sm" name="add_charge" id="add_charge" value="Submit"/>
            </div>
        </div>
    </form>

</div>
@endsection

@section('jscript')
<script src="{{ asset('backend/js/ajax-js/lms/add_charge.js') }}"></script>
<script type="text/javascript">

var messages = {
    get_chrg_amount: "{{ URL::route('get_chrg_amount') }}",
    get_trans_name: "{{ URL::route('get_trans_name') }}",
    get_payments: "{{URL::route('get_unsettled_payments',['user_id' => $customer->user_id])}}",
    get_calculation_amount: "{{ URL::route('get_calculation_amount') }}",
    charges: ["{{config('lms.CHARGE_TYPE.CHEQUE_BOUNCE')}}", "{{config('lms.CHARGE_TYPE.NACH_BOUNCE')}}"],
    token: "{{ csrf_token() }}",
    eod_sys_date: "{{ \Helpers::getSysStartDate() }}",
};


$(document).ready(function () {
    

    $(".datepicker-charge_date").datetimepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        minView: 2,
        endDate: messages.eod_sys_date
    });
});
    $('#based_on').on('change', function() {
        // alert($(this).val());
        if ($(this).val() == '2') {
            $('#program_div').hide();

            var basedOn  = $(this).val();
             if(basedOn=='')
            {
                return false;
            }
            $(".chrg_name").empty();
            $("#msgprogram").html('');
            var postData =  ({'app_id':$("#app_id").val(),'based_on': basedOn,'_token':messages.token});
            jQuery.ajax({
            url: messages.get_trans_name,
                    method: 'post',
                    dataType: 'json',
                    data: postData,
                    error: function (xhr, status, errorThrown) {
                    alert(errorThrown);
                    },
                    success: function (data) {
                    
                        if(data.status==1 && basedOn== 2)
                        {  $("#limit_amount_new").val(data.amount); 
                            $("#programamount").val(data.amount);
                            $(".chrg_name").append('<option value="">Please select</option>'); 
                            $(data.res).each(function(i,v){
                                $(".chrg_name").append('<option value="'+v.id+'">'+v.chrg_name+'</option>'); 
                            });
                        }
                        else
                        {
                                 $(".chrg_name").append('<option value="">No charge found</option>'); 
                           
                        }
                    }
            });
        }
        else{
            $('#program_div').show();
        }
});

</script>
@endsection