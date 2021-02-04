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
        var pid  = $(this).val();
         if(pid=='')
        {
            return false;
        }
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
      $("#chrgMsg").html('');
      $("#chrg_applicable_id").empty();
      var chrg_name =  $(this).val(); 
      if($("#program_id").val()=='' && $('#based_on').val() == 1) 
      {    
             $(this).val('');
             $("#msgprogram").html('Please select program');
             return false;
      }
      if(chrg_name=='')
      {
             $(".chrgT").prop("checked", false);
             $("#amount").empty();
              return false;
      }
      $("#chrg_calculation_type1").prop('disabled',false);
      $("#chrg_calculation_type12").prop('disabled',false);
      $("#RadioValidation").html('');
      getpayments(chrg_name);
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
                          
                          var gst_percentage =   res.gst_percentage;
                          $("#limit_amount_new").val(parseFloat(res.limit));  
                          var  applicable  = res.applicable;  
                          $("#chrg_applicable_id").html(applicable);
                          $("#chrg_applicable_hidden_id").val(res.chrg_applicable_id);
                          $("#chrg_applicable_id option").attr('disabled','disabled');
                          ////**** calculation here for according charge applicable ******/
                          var ram = res.amount.replace(",", ""); 
                          $("#amount").val(ram);
                          $("#id").val(res.id);
                          $("#charge_type").val(res.type);
                        if(res.type==1)
                         {
                             
                            
                             $("#approved_limit_div, .chargeTypeCal").hide();
                             $("#chrg_calculation_type1").prop('checked',true);
                             $("#chrg_calculation_type2").prop('checked',false);
                             $("#chrg_calculation_type2").prop('disabled',true);
                            if(res.is_gst_applicable==1)
                           { 
                             var limitAmount  =  $("#amount").val();  
                             var limitAmount  =  limitAmount.replace(",", ""); 
                             var fixedamount  =  parseFloat(limitAmount*parseFloat(gst_percentage)/100);
                             var finalTotalAmount  = parseFloat(fixedamount)+ parseFloat(limitAmount);
                             var finalTotalAmount =  Math.round(finalTotalAmount * 100) / 100;
                             $("#charge_amount_gst_new").val(finalTotalAmount);
                           }
                             
                         }  
                         else if(res.type==2)
                         {
                             $("#chrg_calculation_type1").prop('checked',false);
                             $("#chrg_calculation_type2").prop('checked',true);
                              $("#chrg_calculation_type1").prop('disabled',true);
                             $("#approved_limit_div, .chargeTypeCal").show(); 
                             var limit_amount_new  =  $("#limit_amount_new").val();
                             var limit_amount_new =   limit_amount_new.replace(",", ""); 
                             var afterPercent = parseFloat(limit_amount_new*res.amount/100);
                             var afterPercent =  Math.round(afterPercent * 100) / 100;
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
                            var afterPercentGst = parseFloat(afterPercent*parseFloat(gst_percentage)/100);
                            finalTotalAmount  = parseFloat(afterPercentGst+afterPercent);
                            var finalTotalAmount =  Math.round(finalTotalAmount * 100) / 100;
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
                         // replaceAlert('Something went wrong, Please try again', 'error');
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
                      //alert(res)
                }
        }); 
    });      
        
        
    $(document).ready(function () {
       $("#chrg_name").html('<option value="">No data found</option>'); 
      //// document.getElementById('amount').addEventListener('input', event =>
       ///// event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
       /////////////// validation the time of final submit/////////////// 
      $(document).on('click','#add_charge',function(e){
        var amount = $("#amount").val()
        var amount = amount.replace(",", "");
         var chrgT = $('.chrgT').val();
        var chrg_calculation_type  =  $("input[name='chrg_calculation_type']:checked"). val();
      
       if ($('form#chargesForm').validate().form()) {
        $("#msgprogram").html('');
        $("#chrgMsg").html('');
        $("#user_id" ).rules( "add", {
        required: true,
        messages: {
        required: "Please select user",
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
        if ($(".chrgT:checked").length == 0)
        {
                $('#RadioValidation').text("Charge Type is required.");
                return false;
        }
        if($("#program_id").val()=='')
        {
     
                $('#msgprogram').text("Please select program");
                return false;
        }
        if($("#chrg_name").val()=='')
        {
                $('#chrgMsg').text("Please select charge.");
                return false;
        }
        if(amount==0)
        {
            if(chrg_calculation_type==1)
            {
              
                replaceAlert('Please Enter Amount', 'error');
                
            }
            else
            {
              
                 replaceAlert('Please Enter Percentage', 'error');
            }
            return false;
          }
       else if(amount > 100)
          {
              if(chrg_calculation_type==2)
              {    
               replaceAlert('Percentage should not  greater than 100%', 'error');
               return false;
              }
          }
        
        
       } else {
        /// alert();
        }  
     
    });   
    });

function getpayments(chrgId) {
  if($.inArray(chrgId, messages.charges) >=0){
    $(".unsettledPayment").show();
    $("#payment").html('<option value="" disabled selected>Choose Paymeny</option>');
    $.ajax({
      type: "get",
      url: messages.get_payments,
      data: { chrg_id: chrgId },
      dataType: 'json',
      success: function (data) {
        if(data.status == 1){
          $(data.res).each(function (i, v) {
            $("#payment").append('<option value="' + v.id + '">Date:-'+v.date_of_payment+' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Amount:- ₹ ' + v.amount +'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Transaction No:-'+v.transactionno+'</option>');
          });
        }else{
          $("#payment").html('<option value="" disabled selected>No Payment found</option>');
        }
      }
    });
  }else{
    $(".unsettledPayment").hide();
  }
}


$(document).on('click','.chrgT',function(){
    var chargeType =  $(this).val();
    if(chargeType)
    {
        var program_id =   $("#program_id").val();
        var chrg_name =   $("#chrg_name").val();
        if(program_id=='')
        {
            $("#msgprogram").html('Please select program');
            $(".chrgT").prop("checked", false);
            $("#submit").css("pointer-events","none");
            return false;
        }
        else if(chrg_name=='')
        {
           
            $("#chrgMsg").html('Please select charge');
            $(".chrgT").prop("checked", false);
            $("#submit").css("pointer-events","none");
            return false;
        }
        else
        {
             $("#msgprogram").html('');
             $("#chrgMsg").html('');
             $("#submit").css("pointer-events","auto");
            return true;
        }
    }
});