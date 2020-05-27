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