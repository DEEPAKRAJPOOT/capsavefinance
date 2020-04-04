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
            <h3>Add Repayment & Wave Off</h3>
            <!-- <small>Application List</small> -->
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Payment</li>
                <li class="active">Add Repayment & Wave Off</li>
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
                  
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Search business name <span class="error_message_label">*</span> </label>
                                                <input type="text" name="search_bus" id="search_bus" class="form-control searchBusiness">
                                           </div>
                                        </div>
                                        <input type="hidden" name="customer_id" id="customer_id">
                                        <input type="hidden" name="biz_id" id="biz_id">
                                        <input type="hidden" name="user_id" id="user_id">
                                        
                                        {{-- <span id="business_name_error" style="color: red"></span>
                                        <ul class="business_list"></ul> --}}
                                      
                                        <div class="col-md-4">
                                            <div class="form-group">

                                                <label for="txtCreditPeriod">Virtual Account No.<span class="error_message_label">*</span> </label>
                                                {{-- <!-- <select class="form-control" name="bank_name">
                                                    <option> Select</option>
                                                    @foreach($bank as $row)
                                                    <option value="{{$row->id}}">{{$row->bank_name}}</option>
                                                    @endforeach
                                                </select> --> --}}
                                                <input type="text" name="virtual_acc" id="virtual_acc" readonly="readonly" class="form-control">
                                                
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Action Type <span class="error_message_label">*</span></label>
                                                <select class="form-control" name="action_type" id="action_type">
                                                    <option value="">Select Action Type</option>
                                                    <option value="1">Receipt</option>
                                                    <option value="2">Wave Off</option>
                                                    <option value="3">TDS</option>
                                                </select>
                                                <span id="action_type_error" class="error"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Transation Type <span class="error_message_label">*</span></label>
                                                <select class="form-control trans_type" name="trans_type" id="trans_type">
                                                    <option value="">Select Transation Type</option>
                                                </select>
                                                <span id="trans_type_error" class="error"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-4" id="waiveoff_div" style="display: none">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">Transactions On <span class="error_message_label">*</span></label>
                                                <select class="form-control" name="charges" id="charges">
                                                    <option value="">Select Charges</option>
                                                </select>
                                                <span id="waiveoff_charges_error" class="error"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group ">
                                                <label for="txtCreditPeriod">Transaction Date<span class="error_message_label">*</span> </label>
                                                <input type="text" name="date_of_payment" id="date_of_payment" readonly="readonly" class="form-control">
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
                                        <div class="col-md-4 payment-methods">
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
                                        <div class="col-md-4 payment-methods">
                                            <div class="form-group">
                                                <span id="appendInput"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 tds_certificate">
                                            <div class="form-group">
                                                <label for="txtCreditPeriod">TDS Certificate No <span class="error_message_label">*</span> </label>
                                                <input type="text" id="tds_certificate_no" name="tds_certificate_no" class="form-control">
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
<script src="{{ asset('backend\theme\assets\plugins\typeahead\handlebars.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend\theme\assets\plugins\bootstrap-tagsinput\typeahead.bundle.js') }}" type="text/javascript"></script>
<script>
    var messages = {
        get_val: "{{URL::route('get_field_val')}}",
        search_business: "{{URL::route('search_business')}}",
        get_repayment_amount_url: "{{ route('get_repayment_amount') }}",
        token: "{{ csrf_token() }}",
        get_remaining_charges_url : "{{route('get_remaining_charges')}}",
        get_customer: "{{ route('get_customer') }}",
        get_all_unsettled_trans_type:"{{ route('get_all_unsettled_trans_type') }}"
    };

    var userData = '';

    $(document).ready(function() {
        // document.getElementById('amount').addEventListener('input', event =>
        // event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));

        $("#date_of_payment").datetimepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                minView : 2,
            });

        var sample_data = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch:messages.get_customer,
            remote:{
                url:messages.get_customer+'?query=%QUERY',
                wildcard:'%QUERY'
            }
        });
        
        $('#search_bus ').typeahead(null, {
            name: 'sample_data',
            display: 'customer_id',
            source:sample_data,
            limit:10,
            templates:{
                suggestion:Handlebars.compile(' <div class="row"> <div class="col-md-12" style="padding-right:5px; padding-left:5px;">@{{biz_entity_name}} <small>( @{{customer_id}} )</small></div> </div>') 
            },
        }).bind('typeahead:select', function(ev, suggestion) {
            setClientDetails(suggestion)
        }).bind('typeahead:change', function(ev, suggestion) {
            var customer_id = $.trim($("#customer_id").val());
            if(customer_id != suggestion)
            setClientDetails(suggestion)
        }).bind('typeahead:cursorchange', function(ev, suggestion) {
            setClientDetails(suggestion)
        });

        $("#action_type").on('change',function(){
            $("#trans_type").val('');
            $("#charges").val('');
            $("#date_of_payment").val('');
            $("#amount").val('');
            userData['action_type'] = $(this).val();
            
            get_all_unsettled_trans_type(userData);
            $(".payment-methods").hide();
            $(".tds_certificate").hide();
            
            switch ($(this).val()) {
                case 1:
                    $(".payment-methods").show();
                    break;
                case 2:
                    $(".tds_certificate").show();
                    break;
            }
        });

        $("#trans_type").on('change',function(){
            $("#charges").val('');
            $("#date_of_payment").val('');
            $("#amount").val('');
            let user_id = $('#user_id').val();
            if (!user_id) {
               $('#business_name_error').text('Please Search business name'); 
               $('#search_bus').focus();
                return false;
            }
            var action_type = $.trim($("#action_type").val());
            var trans_type = $.trim($(this).val());

            if(action_type=='2'){
                get_remaining_charges();
            }else{
                if(trans_type==17){
                    $('#date_of_payment').datetimepicker('setStartDate', '2000-01-01');
                    $('#waiveoff_div').hide();
                    get_repayment_amount();
                }else{
                    get_remaining_charges();
                }
            }
        });

        $("#charges").on('change',function(e){
            $("#date_of_payment").val('');
            $("#amount").val('');
            var element = $(this).find('option:selected'); 
            var index = element.attr("index"); 
            var chargeData = userData['charges'][index];
            if(chargeData){
                $('#date_of_payment').datetimepicker('setStartDate', chargeData['trans_date']);
                $('#amount').val(Math.round(chargeData['remaining'],2)); 
                $('#amount').attr('max',chargeData['remaining']);
            }else{
                $('#date_of_payment').datetimepicker('setStartDate', new Date());
                $('#amount').val(0);
            }
        });

        $("#payment_type").on('change', function() {
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
        
        
        $('#savePayFrm').validate( {
                rules: {
                search_bus: {
                    required: true,
                    },
                    action_type:{
                        required:true,
                    },
                    trans_type: {
                        required: true,
                    },
                    charges:{
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

    function setClientDetails(data){
        $("#action_type").val('');
        $("#trans_type").val('');
        $("#charges").val('');
        $("#date_of_payment").val('');
        $("#amount").val('');
        this.userData = data;
        $("#biz_id").val(data.biz_id);
        $("#user_id").val(data.user_id);
        $("#customer_id").val(data.customer_id);
        $("#virtual_acc").val(data.virtual_acc_id);
    }

    function get_remaining_charges() {
        $.ajax({
            type: 'GET',
            async: false,
            url: messages.get_remaining_charges_url,
            data: {"user_id":$("#user_id").val(), trans_type:$("#trans_type").val(), token: messages.token},
            beforeSend: function( xhr ) {
                $('.isloader').show();
            },
            success: function(res) {
                if (res.status == 'success') {
                    chargeResult = res.result;
                    userData['charges'] = chargeResult;
                    $(chargeResult).each(function(i,v){
                        $('#waiveoff_div').show();
                        $('#charges').html('<option value="">Select Transaction</option>');
                        $('#charges').append('<option index="'+ i +'" value="'+ v.trans_id +'">' + v.trans_name +'<small>('+v.trans_date+')</small>'+ '</option>');
                    })
                }else{
                    $('#waiveoff_div').hide();
                    $('#charges').html('<option value="">Select Transaction</option>');
                    $('#amount').val(''); 
                }
                $('.isloader').hide();     
            } 
        });
    }

    function get_all_unsettled_trans_type(data) {
        $.ajax({
            type: 'GET',
            async: false,
            url: messages.get_all_unsettled_trans_type,
            data: {"user_id":data.user_id, action_type:data.action_type, token: messages.token},
            beforeSend: function( xhr ) {
                $('.isloader').show();
            },
            success: function(res) {
                $('#trans_type').html('<option value="">Select Transaction Type</option>');
                if (res.status == 'success') {
                    chargeResult = res.result;
                    $(chargeResult).each(function(i,v){
                        $('#trans_type').append('<option value="'+ v.id +'">' + v.trans_name + '</option>');
                    })
                }
                $('.isloader').hide();
            }     
        });
    }
    
    function get_repayment_amount() {
        $.ajax({
            type: 'POST',                    
            url: messages.get_repayment_amount_url,
            data: {user_id: $("#user_id").val(), trans_type: $("#trans_type").val(), _token: messages.token},
            beforeSend: function( xhr ) {
                $('.isloader').show();
            },
            success: function(resultData) {                        
                if (resultData.repayment_amount != ""){
                    $("#amount").val(Math.round(resultData.repayment_amount,2)); 
                    $('#amount').attr('max',resultData.repayment_amount);                          
                } else {
                    $("#amount").val("");
                }
                $('.isloader').hide();
            }
       });
    }

    $(document).ready(function(){ 
        $(".processFeeElmnt").hide();
        $(".noGstShow").hide();
        $(".showGSTVal").hide();
        $(".showIGSTVal").hide();
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
    });
   
</script>
@endsection