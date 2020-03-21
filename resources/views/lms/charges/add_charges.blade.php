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
          <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
        </div>
         <div class="form-group col-md-6">          
          <label for="chrg_type">Charge Type</label><br>            
          <div class="form-check-inline ">              
          <label class="form-check-label fnt">               
          <input type="radio" class="form-check-input" id="chrg_calculation_type1" name="chrg_calculation_type" value="1"> &nbsp;&nbsp;Fixed </label>            
          </div>
          <div class="form-check-inline">               
              <label class="form-check-label fnt">               
                  <input type="radio" class="form-check-input" id="chrg_calculation_type2"  name="chrg_calculation_type" value="2">&nbsp;&nbsp;Percentage</label>
          </div> </div></div>
       <div class="row">
        <div class="form-group col-md-6">
          <label for="chrg_name">Amount/Percent</label>
            <input type="text"  class="form-control" id="amount" name="amount" placeholder="Charge Calculation Amount" maxlength="50">
        
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
          <input type="text" readonly="readonly"  class="form-control datepicker-dis-fdate" id="charge_date" name="charge_date" placeholder="Enter Date" value="{{Carbon\Carbon::today()->format('d/m/Y')}}" >
        </div>
      </div>
      
     
      <div class="row">
          <div class="form-group col-md-6 text-left">
              
          </div>
         <div class="form-group col-md-6 text-right">
             <span  id="submitMsg" class="error"></span>
              <input type="hidden"   id="id" name="id" >
              <input type="hidden"   id="app_id" name="app_id"  value="{{$user->app->app_id}}">
              <input type="hidden"   id="pay_from" name="pay_from"  value="{{$user->is_buyer}}">
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
<script type="text/javascript">
   
        var messages = {
            get_chrg_amount: "{{ URL::route('get_chrg_amount') }}",
            get_trans_name: "{{ URL::route('get_trans_name') }}",
            get_calculation_amount: "{{ URL::route('get_calculation_amount') }}", 
            token: "{{ csrf_token() }}",
 };
 
  $(document).on('click','.gstAppli',function(){
     var is_gst =  $(this).val();
     if(is_gst==1)
     {
        if($("#charge_type").val()==1)
        {
         var limitAmount =  $("#amount").val().replace(",", ""); 
         var fixedamount = parseInt(limitAmount*18/100);
         var finalTotalAmount  = parseInt(fixedamount)+ parseFloat(limitAmount);
         $("#charge_amount_gst_new").val(finalTotalAmount);
         $(".chargeTypeGstCal").css({"display":"inline"});
       }
       else
       {
            $(".chargeTypeGstCal").css("display","inline");
            var limitAmount =  $("#charge_amount_new").val();  
            var fixedamount = parseInt(limitAmount*18/100);
            var finalTotalAmount  = parseInt(fixedamount)+ parseFloat(limitAmount);
            $("#charge_amount_gst_new").val(finalTotalAmount);
       }
         
     }
     else
     {
          $(".chargeTypeGstCal").css({"display":"none"}); 
     }
 })
  $(document).on('keyup change','#amount',function(){
      var limitAmount =  $(this).val().replace(",", ""); 
     
      if($("#charge_type").val()==1)
        {
       
         var fixedamount = parseFloat(limitAmount*18/100);
         var finalTotalAmount  = parseInt(fixedamount)+ parseFloat(limitAmount);
         $("#charge_amount_gst_new").val(finalTotalAmount);
        
       }
       else
       {
            var limit_amount_new  =  $("#limit_amount_new").val();
            var afterPercent = parseInt(limit_amount_new*limitAmount/100);
            $("#charge_amount_new").val(afterPercent);
           var fixedamount = parseInt(afterPercent*18/100);
           var finalTotalAmount  = parseInt(fixedamount)+ parseFloat(afterPercent);
            $("#charge_amount_gst_new").val(finalTotalAmount);
       }
  });
 
    $(document).on('change','#program_id',function(){
        $(".chrg_name").empty();
        $("#msgprogram").html('');
        var postData =  ({'app_id':$("#app_id").val(),'prog_id':$("#program_id").val(),'_token':messages.token});
        jQuery.ajax({
        url: messages.get_trans_name,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                },
                success: function (data) {
                
                    if(data.status==1)
                    {  $("#limit_amount_new").val(data.amount); 
                        $("#programamount").val(data.amount);
                        $(".chrg_name").append('<option value="">Please select</option>'); 
                        $(data.res).each(function(i,v){
                            $(".chrg_name").append('<option value="'+v.charge.id+'">'+v.charge.chrg_name+'</option>'); 
                        });
                    }
                    else
                    {
                             $(".chrg_name").append('<option value="">No charge found</option>'); 
                       
                    }
                }
        });         
    });
    
    /////////// get calculation according ////////////////
    
    $(document).on('change','.chrg_applicable_id',function(){
      var chrg_applicable_id  =  $(this).val();   
      var is_gst_applicable =  $("input[name=is_gst_applicable]").val();
      var postData =  ({'is_gst_applicable':is_gst_applicable,'percent':$("#amount").val(),'app_id':$("#app_id").val(),'chrg_applicable_id':chrg_applicable_id,'prog_id':$("#program_id").val(),'user_id':$("#user_id").val(),'_token':messages.token});
       jQuery.ajax({
        url: messages.get_calculation_amount,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                },
                success: function (res) {
                                  $("#limit_amount_new").val(res.limit_amount);
                                  $("#charge_amount_new").val(res.charge_amount);
                                  $("#charge_amount_gst_new").val(res.gst_amount);
                                }
                      }); 
      }); 
  
    
    
    
  //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','#chrg_name',function(){
      $(".chargeTypeGstCal, #charge_amount_gst_new").css("display","inline");
      $("#chrg_applicable_id").empty();
      $("#chrg_calculation_type1").attr('disabled',false);
      $("#chrg_calculation_type2").attr('disabled',false);
      var chrg_name =  $(this).val(); 
      if($("#program_id").val()=='') 
      {    
            
             $(this).val('');
             $("#msgprogram").html('Please select program');
             return false;
      }
      if(chrg_name=='')
      {
             $("#chrg_calculation_type1").attr('checked',false);
             $("#chrg_calculation_type2").attr('checked',false);
             $("#amount").empty();
              return false;
      }
      var postData =  ({'app_id':$("#app_id").val(),'id':chrg_name,'prog_id':$("#program_id").val(),'user_id':$("#user_id").val(),'_token':messages.token});
       jQuery.ajax({
        url: messages.get_chrg_amount,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                },
                success: function (res) {
                      if(res.status==1)
                      {
                          $("#limit_amount_new").val(parseInt(res.limit));  
                          var  applicable  = res.applicable;  
                          $("#chrg_applicable_id").html(applicable);
                          $("#chrg_applicable_hidden_id").val(res.chrg_applicable_id);
                          $("#chrg_applicable_id option").attr('disabled','disabled');
                          ////**** calculation here for according charge applicable ******/
                          $("#amount").val(res.amount);
                          $("#id").val(res.id);
                          $("#charge_type").val(res.type);
                        if(res.type==1)
                         {
                           
                             $("#chrg_calculation_type2").attr('checked',false);
                             $("#approved_limit_div, .chargeTypeCal").hide();
                             $("#chrg_calculation_type1").attr('checked',true);
                             $("#chrg_calculation_type2").attr('disabled','disabled');
                            if(res.is_gst_applicable==1)
                           { 
                             var limitAmount =  $("#amount").val();  
                             var fixedamount = parseInt(limitAmount*18/100);
                             var finalTotalAmount  = parseInt(fixedamount)+ parseFloat(limitAmount);
                             $("#charge_amount_gst_new").val(finalTotalAmount);
                           }
                             
                         }  
                         else if(res.type==2)
                         {
                             $("#chrg_calculation_type1").attr('checked',false);
                             $("#approved_limit_div, .chargeTypeCal").show(); 
                             $("#chrg_calculation_type2").attr('checked',true);
                             $("#chrg_calculation_type1").attr('disabled','disabled');
                             var limit_amount_new  =  $("#limit_amount_new").val();
                             var afterPercent = parseInt(limit_amount_new*res.amount/100);
                             $("#charge_amount_new").val(afterPercent);
                         } 
                          if(res.is_gst_applicable==1)
                         {
                            $("#is_gst_applicable2").attr('disabled','disabled'); 
                             $("#is_gst_applicable1").prop('checked',true);
                            $("#is_gst_applicable2").prop('checked',false);
                            $(".chargeTypeGstCal").css({"display":"inline"});
                            if(res.type==2)
                            {
                            var afterPercentGst = parseInt(afterPercent*18/100);
                            finalTotalAmount  = parseInt(afterPercentGst+afterPercent);
                            $("#charge_amount_gst_new").val(finalTotalAmount);
                            }
                         }  
                         else if(res.is_gst_applicable==2)
                         {
                             $(".chargeTypeGstCal").css({"display":"none"});
                             $("#is_gst_applicable2").prop('checked',true);
                             $("#is_gst_applicable1").prop('checked',false);
                              $("#is_gst_applicable1").attr('disabled','disabled');
                            } 
                         
                      }
                      else
                      {
                         $("#chrg_name").val('');
                         alert('Something went wrong, Please try again');
                      }
                }
        }); 
    }); 
        
    $(document).on('change','#program_id_old',function(){
       var postData =  ({'app_id':$("#app_id").val(),'prog_id':$("#program_id").val(),'_token':messages.token});
       jQuery.ajax({
        url: messages.get_trans_name,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                },
                success: function (res) {
                      alert(res)
                }
        }); 
    });      
        
        
    $(document).ready(function () {
       $("#chrg_name").html('<option value="">No data found</option>'); 
       document.getElementById('amount').addEventListener('input', event =>
        event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
       /////////////// validation the time of final submit/////////////// 
      $(document).on('click','#add_charge',function(e){
        var amount = $("#amount").val()
        var amount = amount.replace(",", "");
        var chrg_calculation_type  =  $("input[name='chrg_calculation_type']:checked"). val();
      
       if ($('form#chargesForm').validate().form()) {
        $("#user_id" ).rules( "add", {
        required: true,
        messages: {
        required: "Please select user",
        }
        });
          $("#program_id" ).rules( "add", {
        required: true,
     
        messages: {
        required: "Please select program name",
        }
        });
          $("#chrg_name" ).rules( "add", {
        required: true,
        messages: {
        required: "Please select charge",
        }
        });
         
         $("#amount" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter amount",
        }
        });
        $("#charge_date" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter date",
        }
        });   
        if(amount==0)
        {
            if(chrg_calculation_type==1)
            {
              
                alert('Please Enter Amount');
                
            }
            else
            {
              
                 alert('Please Enter Percentage');
            }
            return false;
          }
       else if(amount > 100)
          {
              if(chrg_calculation_type==2)
              {    
               alert('Percentage should not  greater than 100%');
               return false;
              }
          }
        
        
       } else {
        /// alert();
        }  
     
    });   
    });
</script>
@endsection