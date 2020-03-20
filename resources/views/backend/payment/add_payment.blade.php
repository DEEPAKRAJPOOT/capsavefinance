@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
<div class="content-wrapper">
<section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Add Manual Payment</h3>
            <!-- <small>Application List</small> -->
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Payment</li>
                <li class="active">Add Manual Payment</li>
            </ol>
        </div>
    </section>
    <div class="row grid-margin mt-3">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-fields">
                        <div class="active" id="details">
                            <div class="form-sections">
                            {!!
                                Form::open(
                                array(
                                'route' => 'save_payment',
                                'name' => 'savePayFrm',
                                'autocomplete' => 'off',
                                'id' => 'savePayFrm',
                                'method'=> 'POST'
                                )
                                )
                                !!}
                                    <div class="row">
                  
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Search business name <span class="error_message_label">*</span> </label>
                                                <input type="hidden" name="customer_id" id="customer_id">
                                                <input type="hidden" name="biz_id" id="biz_id">
                                                <input type="text" name="search_bus"   id="search_bus" class="form-control searchBusiness">
                                                <ul class="business_list">
                                                </ul>
                                           </div>
                                        </div>
                                      
                                        <div class="col-md-6">
                                            <div class="form-group">

                                                <label for="txtCreditPeriod">Virtual Account No.<span class="error_message_label">*</span> </label>

                                                <!-- <select class="form-control" name="bank_name">
                                                    <option> Select</option>
                                                    @foreach($bank as $row)
                                                    <option value="{{$row->id}}">{{$row->bank_name}}</option>
                                                    @endforeach
                                                </select> -->
                                                <input type="text" name="virtual_acc" id="virtual_acc" readonly="readonly" class="form-control">
                                                
                                            </div>
                                        </div>
                                          <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Transation Type <span class="error_message_label">*</span></label>
                                                <select class="form-control trans_type" name="trans_type" id="trans_type">
                                                    <option value="">Select Transation Type</option>
                                                    @foreach($tranType as $key => $value)
                                                    <option value="{{$value->id}}"> {{$value->credit_desc}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group ">
                                                <label for="txtCreditPeriod">Transaction Date<span class="error_message_label">*</span> </label>
                                                <input type="text" name="date_of_payment" id="date_of_payment" readonly="readonly" class="form-control datepicker-dis-fdate">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group INR">
                                                <label for="txtCreditPeriod">Transaction Amount <span class="error_message_label">*</span> </label>
                                                <a href="javascript:void(0);" class="verify-owner-no" style="top:29px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                                <input type="text" id="amount" name="amount" class="form-control">
                                            </div>
                                        </div>
                                      
                                        <!--start processing fees code-->
                                      <!--  <div class="col-md-4 processFeeElmnt">
                                            <div class="form-group INR ">
                                                <label for="txtCreditPeriod">Transaction Amount inclusive GST ? <span class="error_message_label">*</span> </label>
                                                <br>
                                                <input type="radio" id="incl_gst" name="incl_gst" value="1">Yes &nbsp;&nbsp;  <input type="radio" id="incl_gst" name="incl_gst" value="0" checked>No
                                            </div>
                                        </div>   -->
                                        <div class="col-md-4 processFeeElmnt noGstShow">
                                            <div class="form-group INR">
                                                <label for="txtCreditPeriod">Select GST Option <span class="error_message_label">*</span> </label>
                                                <select id="gst" name="gst" class="form-control valid" aria-invalid="false">
                                                    <option value="">Select GST Option</option>
                                                    @foreach($getGstDropVal as $key=>$val)
                                                    @if($val->tax_name=='GST')             
                                                    <option value="{{$val->tax_id}}" data-name="{{$val->tax_name}}" data-cgst="{{$val->cgst}}" data-sgst="{{$val->sgst}}" data-igst="{{$val->igst}}">{{$val->tax_name}}- {{$val->tax_value}} % (SGST: {{$val->sgst}}% / CGST: {{$val->cgst}}%);</option>
                                                    @else
                                                    <option value="{{$val->tax_id}}" data-name="{{$val->tax_name}}" data-cgst="{{$val->cgst}}" data-sgst="{{$val->sgst}}" data-igst="{{$val->igst}}">{{$val->tax_name}}-{{$val->tax_value}}% (IGST: {{$val->igst}}%)</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 processFeeElmnt noGstShow showGSTVal">
                                            <div class="form-group ">
                                                <label for="txtCreditPeriod">SGST Amount <span class="error_message_label">*</span> </label>
                                                <input type="text" name="sgst_amt" id="sgst_amt" readonly="readonly" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 processFeeElmnt noGstShow showGSTVal">
                                            <div class="form-group ">
                                                <label for="txtCreditPeriod">CGST Amount<span class="error_message_label">*</span> </label>
                                                <input type="text" name="cgst_amt" id="cgst_amt" readonly="readonly" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 processFeeElmnt noGstShow showIGSTVal">
                                            <div class="form-group ">
                                                <label for="txtCreditPeriod">IGST Amount<span class="error_message_label">*</span> </label>
                                                <input type="text" name="igst_amt" id="igst_amt" readonly="readonly" class="form-control" value="">
                                            </div>
                                        </div>
                                        <!--<div class="col-md-4">
                                            <div class="form-group ">
                                                <label for="txtCreditPeriod">Transaction Id<span class="error_message_label">*</span> </label>

                                                <input type="text" name="txn_id" id="txn_id" class="form-control">
                                            </div>
                                        </div>  -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Payment Method <span class="error_message_label">*</span></label>
                                                @php
                                                $get = Config::get('payment.type');
                                                @endphp

                                                <select class="form-control amountRepay" name="payment_type" id="payment_type">
                                                    <option value=""> Select Payment Mode </option>
                                                    @foreach($get as $key=>$val)
                                                    <option value="{{$key}}"> {{$val}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <span id="appendInput"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group ">
                                                <label for="txtCreditPeriod">Comment <span class="error_message_label">*</span> </label>

                                                <textarea name="description" id="description" class="form-control" rows="3" cols="3"></textarea>
                                            </div>
                                        </div>
                                      
                                        <div class="col-md-12">
                                            <div class="text-right ">
                                                <input type="reset" id="pre3" class="btn btn-secondary btn-sm" value="Cancel">
                                                <input type="submit" id="savePayBtn" class="btn btn-success ml-2 btn-sm" value="Submit">
                                            </div>
                                        </div>

                                    </div>
                                    {!! Form::close() !!}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <h5>Are you sure you want to submit the programe?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('jscript')
<style>
 .business_list {
   background-color:aliceblue;  
   border: 2px solid #f7f7f7;
} 
.business_list_li {
  background-color:#f9f9f9;
border: 1px solid antiquewhite;  
cursor: pointer;

}

</style>
<script>
    var messages = {
        get_val: "{{URL::route('get_field_val')}}",
        search_business: "{{URL::route('search_business')}}",
        get_repayment_amount_url: "{{ route('get_repayment_amount') }}",
        token: "{{ csrf_token() }}",
    };

      $(document).ready(function() {
      document.getElementById('amount').addEventListener('input', event =>
      event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
    });

    $(document).on('change', '#payment_type', function() {
        $('#appendInput').empty();
        var status = $(this).val();
        if (status == 1) {
            $('#appendInput').append('<label for="repaid_amount" class="form-control-label"><span class="payment_text">Online RTGS/NEFT</span></label><span class="error_message_label">*</span><input type="text" class="form-control amountRepay" id="utr_no" name="utr_no" value=""><span id="utr_no_msg" class="error"></span>');

        } else if (status == 2) {
            $('#appendInput').append('<label for="repaid_amount" class="form-control-label"><span class="payment_text">Cheque Number</span></label><span class="error_message_label">*</span><input type="text" class="form-control amountRepay" id="utr_no" name="utr_no" value=""><span id="utr_no_msg" class="error"></span>');

        } else if (status == 3) {
            $('#appendInput').append('<label for="repaid_amount" class="form-control-label"><span class="payment_text">UNR Number</span></label><span class="error_message_label">*</span><input type="text" class="form-control amountRepay" id="utr_no" name="utr_no" value=""><span id="utr_no_msg" class="error"></span>');

        }
    });
    $(document).ready(function(){
        $(".processFeeElmnt").hide();
        $(".noGstShow").hide();
        $(".showGSTVal").hide();
        $(".showIGSTVal").hide();
       var gstRadio= $("#incl_gst").val();
        
        $("#trans_type").on('change',function(){
        // ($(this).find(':selected').data('ip') == '1')? $('#paytodiv').show():$('#paytodiv').hide();
        // if($(this).find(':selected').data('it') == '1'){ $('#taxdiv').show();  }else { $('#taxdiv').hide(); }
        $('#cgst, #sgst, #gst').val('');
        var tranTp=$("#trans_type").val();
        if(tranTp==5 || tranTp==9 || tranTp==11){
            $("#checkTranType").show();
            $("#checkTDSPer").hide();
        }else if(tranTp==7){
            $("#checkTDSPer").show();
            $("#checkTranType").hide();
        }else{
            $("#checkTranType").hide();
            $("#checkTDSPer").hide();
        }

        var tranTypeVal=$("#trans_type").val();
        if(tranTypeVal==4 || tranTypeVal==5){
            $("#trans_amt").attr('readonly', true);
            $(".processFeeElmnt").show();
            $(".noGstShow").hide();
            $(".showGSTVal").hide();
          $(".showIGSTVal").hide();
        }else{
            $("#trans_amt").val("");
            $("#trans_amt").attr('readonly', false); 
        }
    });

    $("input[name='incl_gst']").on('change', function () {
    if( $("input[name='incl_gst']:checked").val() == '1'){
        $(".noGstShow").show();
        $(".showGSTVal").hide();
        $(".showIGSTVal").hide();
    }else{
        $('#gst').prop('selectedIndex',0);
        $(".noGstShow").hide();
        $(".showGSTVal").hide();
        $(".showIGSTVal").hide();
    }
    });
    $("#gst").change(function(){
        var chkAmt=$("#amount").val();
        chkAmt=  chkAmt.replace(/,/g, '')
        if(chkAmt!='' && chkAmt>0){      
        if($(this).find(':selected').data('name').trim() == 'GST'){
            $(".noGstShow").show(); 
            $('.showGSTVal').show()
            $('.showIGSTVal').hide(); 
        }else {
            $(".noGstShow").show();
             $('.showIGSTVal').show();
             $('.showGSTVal').hide(); 
      }
    }else{
        $('#gst').prop('selectedIndex',0);
        alert("Please enter transaction amount.");
    }
    
    });

    $("#gst, #amount, input[name='incl_gst']").on('change', function () {

var getGstTxt=$("#gst option:selected").text();
getGstTxt=getGstTxt.split("-");
getGstTxt=getGstTxt[0].toLowerCase();

if ($("#amount").valid()) {
    let cgst = $("#gst option:selected").data('cgst');
    let sgst = $("#gst option:selected").data('sgst');
    let igst = $("#gst option:selected").data('igst');
    let trans_amt = $('#amount').val();
    trans_amt=trans_amt.replace(/,/g, '');
    var gstval=$("#gst").val();
    if( $("input[name='incl_gst']:checked").val() == '1'){
        if(getGstTxt=='gst'){ 
        var cgstval=((cgst * trans_amt / 100).toFixed(2));
        var sgstVal=((sgst * trans_amt / 100).toFixed(2));
        cgstval =(cgstval && !isNaN(cgstval))?cgstval:'';
        sgstVal =(sgstVal && !isNaN(sgstVal))?sgstVal:'';
        $('#cgst_amt').val(cgstval);
        $('#sgst_amt').val(sgstVal);
        $('#igst_amt').val('');
    }else if(getGstTxt=='igst'){ 
        var igstval=((igst * trans_amt / 100).toFixed(2));                
        igstval =(igstval && !isNaN(igstval))?igstval:'';              
        $('#igst_amt').val(igstval);
        $('#cgst_amt').val('');
        $('#sgst_amt').val('');
    }
        $("#notAddGST").show();
    }else
    {  
        $("#notAddGST").hide();
        $(".showGSTVal").hide();
        $(".showIGSTVal").hide();                
        $('#gst').val('');
        $('#cgst').val('');
        $('#sgst').val('');
        $('#igst').val(''); 
    }
    
}
});

    $(".getCustomer").change(function(){
               $.ajax({
                     type: 'GET',
                     async: false,
                     url: messages.get_val,
                    data: {tableName:'lms_users',whereId:'user_id',fieldVal:$('#customer_id').val(),column:'virtual_acc_id', token: messages.token},
                     success: function(resultData) {
                     if(resultData!=""){
                    $("#virtual_acc").val(resultData);
                   $("#virtual_acc-error").css("display","none");
                     }else{
                        $("#virtual_acc").val("");
                        $("#virtual_acc-error").css("display","block");
                     }
                     }      
               });
            });        
});
$(document).ready(function () {  
            $('#savePayFrm').validate( {
                  rules: {
                    search_bus: {
                        required: true,
                     },
                     trans_type: {
                        required: true,
                     },
                     virtual_acc: {
                        required: true,
                     },
                     date_of_payment:{
                         required:true,
                     },
                     amount:{
                         required:true,
                     },
                     payment_type:{
                         required:true,
                     },
                     description:{
                         required:true,
                     },
                     incl_gst:{
                         required:$("#trans_type").val()>0?false:true,
                     },
                     gst:{
                          required:$("#incl_gst:checked").val()>0?false:true,
                          }
                  },
                  messages: {
                    customer_id: {
                        //required: "Please select file",
                     }
                  }
               });
        });
        
    $("#trans_type").change(function(){
               $.ajax({
                    type: 'POST',                    
                    url: messages.get_repayment_amount_url,
                    data: {user_id: $("#customer_id").val(), trans_type: $("#trans_type").val(), _token: messages.token},
                    beforeSend: function( xhr ) {
                        $('.isloader').show();
                    },
                    success: function(resultData) {                        
                        if (resultData.repayment_amount != ""){
                            $("#amount").val(resultData.repayment_amount);                           
                        } else {
                            $("#amount").val("");
                        }
                        $('.isloader').hide();
                    }
               });
            });    
   $(document).on('keyup','.searchBusiness',function(){
       $(".business_list").empty();
       var search  =  $(this).val();
      if(search.length > 1)
      {
       var postData =  ({'search':search,'_token':messages.token});
       jQuery.ajax({
        url: messages.search_business,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                
                },
                success: function (data) {
                     if(data.status > 0) { 
                       $(data.result).each(function(i,v){
                            if(v.lms_user!=null)
                            {

                                  $(".business_list").append("<li class='business_list_li' data-user_id="+v.user_id+" data-biz_id="+v.biz_id+" data-virtual_acc="+v.lms_user.virtual_acc_id+">"+v.biz_entity_name+" / "+ v.lms_user.customer_id+"</li>");
                            } 
                           })
                        }
                        else
                        {
                            $(".business_list").append("<li class='business_list_li'>No data found</li>"); 
                     }
                 }
       })  
      }
      else
      {
           return false; 
        }
   }) 
   
   $(document).on('click','.business_list_li',function(){
       var business_name =  $(this).text();
       var user_id =  $(this).attr('data-user_id'); 
       var virtual_acc = $(this).attr('data-virtual_acc'); 
       var biz_id = $(this).attr('data-biz_id'); 
       var business_name    = business_name.split("/");
       $("#search_bus").val(business_name[0]);
       $("#customer_id").val(user_id);
       $("#virtual_acc").val(virtual_acc);
       $("#biz_id").val(biz_id);
       $(".business_list").empty();
    })
</script>
@endsection