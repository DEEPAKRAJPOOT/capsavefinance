  ///* upload image and get ,name  */
   $('input[type="file"]'). change(function(e){
        $("#customFile-error").hide();
        var fileName = e. target. files[0]. name;
        $("#msgFile").html('The file "' + fileName + '" has been selected.' );
    });

   ///////////////// invoice approve amount check here///////////
   $(document).on('change blur keyup','#invoice_approve_amount', function() {
     var pro_limit = parseInt($("#pro_limit_hide").val());
     var invoice_approve_amount = $("#invoice_approve_amount").val();
     var invoice_approve_amount = invoice_approve_amount.replace(/\,/g,'');
     var margin = $("#margin").val();

     if(invoice_approve_amount==0)
     {
         $("#invoice_approve_amount").val('');
         return false;
     }

    // if (typeof margin != 'undefined' && margin > 0) {
    //   margin = parseFloat(margin).toFixed(2);
    //   var marginAmt = (invoice_approve_amount * margin) / 100;
    //   invoice_approve_amount = invoice_approve_amount - marginAmt;

    //   if(invoice_approve_amount  > pro_limit)
    //   {
    //     $("#msgProLimit").text('Invoice amount should not be greater than the remaining limit amount after excluding the margin amount.');
    //     $("#submit").css("pointer-events","none");
    //     return false;
    //   }
    // }

    // if(invoice_approve_amount  > pro_limit)
    //  {
    //      $("#msgProLimit").text('Invoice amount should not be more than balance limit amount.');
    //      $("#submit").css("pointer-events","none");
    //      return false;
    //  }
    //  else
    //  {
    //      $("#msgProLimit").empty();
    //      $("#submit").css("pointer-events","auto");
    //      return true;
    //  }     
});

 //////////// check duplicate invoice ////////////////////
 
  $(document).on('change blur keyup','#invoice_no,#supplier_id', function() {
     var invoice = $("#invoice_no").val();
     var user_id  = $("#supplier_id").val();
     var user_id  =  user_id.split(',');
     var user  =  user_id[0];
     if(user==""  || invoice=="")
     {
         return false;
     }
    
      var postData =  ({'user_id':user,'invoice':invoice,'_token':messages.token});
       jQuery.ajax({
        url: messages.check_duplicate_invoice,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                
                },
                success: function (data) {
                      if(data.status==1)
                        {
                            $("#msgInvoiceDupli").text('Invoice No already exists');
                            $("#submit").css("pointer-events","none");
                            return false;
                        }
                        else
                        {
                             $("#submit").css("pointer-events","auto");
                            $("#msgInvoiceDupli").empty();
                           return true;
                        }
                }
            });
});


   function ChangeDateFormat(date)
   {
            var datearray = date.split("/");
            return  newdate = datearray[1] + '/' + datearray[0] + '/' + datearray[2];

   }

    function findDaysWithDate(firstDate,secondDate)
    {
        var firstDate  =   ChangeDateFormat(firstDate);
        var secondDate  =  ChangeDateFormat(secondDate);
        var startDay = new Date(firstDate);
        var endDay = new Date(secondDate);
        var millisecondsPerDay = 1000 * 60 * 60 * 24;
        var  millisBetween = startDay.getTime() - endDay.getTime();
        var    days = millisBetween / millisecondsPerDay;
        return  Math.floor(days);
    }
  

 $(document).ready(function () {
      //////////// comma seprate value in amount   //////////////////////// 
      
        document.getElementById('invoice_approve_amount').addEventListener('input', event =>
        event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
      ///  $("#program_id").append("<option value=''>No data found</option>");  
        $("#supplier_id").append("<option value=''>No data found</option>");                         
  /////// jquery validate on submit button/////////////////////
  $('#submit').on('click', function (e) {
        $("#tenorMsg").text('');
        var first  = $('#invoice_due_date').val();
        var second = $('#invoice_date').val();
        var getDays  = findDaysWithDate(first,second);
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //As January is 0.
        var yyyy = today.getFullYear();
        var cDate  = dd+"/"+mm+"/"+yyyy;
        var getOldDays  = findDaysWithDate(cDate,second);
        var tenor  = $('#tenor').val();
        var tenor_old_invoice  = $('#tenor_old_invoice').val();
        var invoice_approve_amount = $("#invoice_approve_amount").val();
        var invoice_approve_amount = parseInt(invoice_approve_amount.replace(/\,/g,''));
        var pro_limit_hide  = parseInt($('#pro_limit_hide').val());
        var is_adhok  = $("#limit_type").is(":checked");
        
     if ($('form#signupForm').validate().form()) {  
       $("#anchor_id" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter anchor name",
        }
        });
       
      $("#supplier_id" ).rules( "add", {
        required: true,
        messages: {
        required: "Please select supplier name",
        }
        });
          $("#program_id" ).rules( "add", {
        required: true,
        messages: {
        // required: "Please select product program name",
        required: "Please select supplier name",
        }
        });
        $("#invoice_no" ).rules( "add", {
        required: true,
        minlength: 3,
        maxlength: 25,
        messages: {
        required: "Please enter invoice no.",
        minlength: "Minimum 3  characters are required.",
        maxlength: "Maximum 25  characters are required.",
        }
        });
        
        $("#invoice_due_date" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter invoice due date",
        }
        }); 
        $("#invoice_date" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter invoice date",
        }
        }); 
        
        $("#invoice_approve_amount" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter invoice approve amount",
        }
        }); 
      //  $("#customFile").rules("add", {
      //    required: true,
      //    messages: {
      //      required: "Please upload invoice copy",
      //    }
      //  }); 
         $("#remark" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter remark",
        }
        }); 
         if(getDays > tenor)
        {
           $("#tenorMsg").show(); 
           $("#tenorMsg").html('Invoice date & invoice due date difference should not be more than '+tenor+' days'); 
           e.preventDefault();
        }
        
       else if(getOldDays > tenor_old_invoice)
        {
          // $("#tenorMsg").show(); 
          // $("#tenorMsg").html('Invoice date & current date difference should not be more than '+tenor_old_invoice+' days.'); 
          /// e.preventDefault();
          $("#exception").val(28);
        }
       else if(is_adhok==true)
       {
           
         if(invoice_approve_amount > pro_limit_hide)
        {
         
           $("#msgProLimit").show(); 
           $("#msgProLimit").html('Invoice amount limit exceed'); 
           e.preventDefault();
        }
       }  
        } else {
        /// alert();
        }  
     });         
  });  
  
  ////////////// get due date depend on tenor date ///////////
   $(document).on('keyup change','.getInvoiceD',function(){
        var date = $(this).val(); 
        if($("#program_id").val()!='' && date!='')
      {
       
        var date = ChangeDateFormat(date);
        var oldDate = new Date(date);
        var days  = parseInt($('#tenor').val());
        var nextday =new Date(oldDate.getFullYear(),oldDate.getMonth(),oldDate.getDate()+days);
        var dueDate  = (nextday.getDate()+'/'+(nextday.getMonth()+1)+'/'+nextday.getFullYear());
        $("#invoice_due_date").val(dueDate);
    }
   });
  //////////////////// onchange anchor  id get data /////////////////
 
  $(document).on('change','.changeAnchor',function(){
      $("#limit_type").prop("checked", false);
      $("#adhoc_msg").hide();
      var anchor_id =  $("#anchor_id").val(); 
      $("#offer_data").val('');
      $('#text_payment_frequency').empty();
      $('#text_benchmark_date').empty();
      $("#upFrontAmount").empty().hide();
      $('#calculateUpfrontInt').addClass('hide');
      if(anchor_id=='')
      {
            $("#pro_limit").empty();
             $("#pro_limit_hide").empty();
             $("#pro_remain_limit").empty();
             $("#program_id").html("<option value=''>No data found</option>");
            return false;
      }
      $("#program_id").empty();
      $("#anc_limit").empty();
      $("#pro_limit").empty();
      $("#pro_remain_limit").empty();
      $(".isloader").show();
      var postData =  ({'anchor_id':anchor_id,'_token':messages.token});
       jQuery.ajax({
        url: messages.front_program_list,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                
                },
                
                success: function (data) {
                    $(".isloader").hide(); 
                    if(data.status==1)
                    {
                        var obj1  = data.get_program;
                        var obj2   =  data.limit;
                        var obj3 = data.get_supplier;
                        $("#anc_limit").html('Limit : <span class="fa fa-inr"></span>  '+obj2.anchor_limit+'');
                        $("#program_id").append("<option value=''>Please Select Customer</option>"); 
                          //  $("#program_id").append("<option value=''>Please Select Program</option>");
                         if(obj1.length > 0){
                            $(obj1).each(function(i,v){
                             if(v.program!=null)
                             {  
                                getSupplierByPrgmId = (obj3[v.program.prgm_id])?obj3[v.program.prgm_id]:'';
                                $dropDown = "<optgroup label='"+v.program.prgm_name+"'>";
                                if(getSupplierByPrgmId != ''){
                                  $(getSupplierByPrgmId).each(function(j,v1){
                                    if(v1 !=null)
                                    {           
                                      $dropDown +="<option value='"+v.program.prgm_id+","+v.app_prgm_limit_id+","+v1.user_id+","+v1.app_id+","+v1.prgm_offer_id+"'>"+v1.biz_entity_name+"&nbsp;&nbsp;("+v1.customer_id+")</option>";  
                                    }                   
                                    });
                                }
                                $dropDown += "</optgroup>";
                                $("#program_id").append($dropDown);
                                  
                              }                   
                             });
                          }else{
                            $("#program_id").html("<option value=''>No data found</option>");
                          }
                    }
                    else
                    {
                       
                      $("#program_id").append("<option value=''>No data found</option>");  
                           
                      
                    }
                  
                }
        });
          
      }); 
   
    //////// String value not allowed in  amount filed//////////////////////
 $(document).on('keypress','#invoice_approve_amount',function(event){       
  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
    event.preventDefault();
  }
});
  //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','.changeSupplier',function(){
      $("#limit_type").prop("checked", false);
      $("#adhoc_msg").hide();
      $("#invoice_date").val('');
      $("#offer_data").val('');
      $('#text_payment_frequency').empty();
      $('#text_benchmark_date').empty();
      $("#upFrontAmount").empty().hide();
      $('#calculateUpfrontInt').addClass('hide');
      var program_id =  $(this).val(); 
      var anchor_id =  $("#anchor_id").val(); 
      if(program_id=='')
      {
          $("#pro_limit").empty();
          $("#pro_remain_limit").empty();
          return false; 
      }
      $("#supplier_id").empty();
      $("#pro_limit").empty();
      $("#pro_limit_hide").empty();
      IdArray = program_id.split(",");
      var prgmId = IdArray[0];
      var appLimitId = IdArray[1];
      IdsArray = [prgmId,appLimitId];
      program_id = IdsArray.join(",");
      var postData =  ({'bulk':0,'program_id':program_id,'_token':messages.token});
       jQuery.ajax({
        url: messages.front_supplier_list,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                
                },
                success: function (data) {
                    if(data.status==1)
                    {
                         if(data.uploadAcess==0)
                        {
                            $("#tenorMsg").text("You don't have permission to upload invoice for this program.");           
                            $("#ApprovePro").hide();
                            
                        }
                        else
                        {
                             $("#ApprovePro").show();
                             $("#tenorMsg").text(" ");           
                           
                            
                        }
                        var obj1  = data.get_supplier;
                        var obj2   =  data.limit;
                        var offer_id   =  data.offer_id;
                        var tenor   =  data.tenor;
                        var tenor_old_invoice  = data.tenor_old_invoice;
                        $("#prgm_offer_id").val(offer_id);
                        var userId = IdArray[2];
                        var appId = IdArray[3];
                        var prgmOfferId = IdArray[4];
                        supplierIdArray = [userId,appId,prgmOfferId];
                        supplierId = supplierIdArray.join(",");
                        $("#supplier_id").val('');
                        $("#supplier_id").val(supplierId);  
                        $('.getTenor').val(supplierId).trigger("change");
                     ///   $("#tenor_old_invoice").val(tenor_old_invoice);
                     ///   $("#tenor").val(tenor);
                     ///   $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  '+obj2.anchor_sub_limit+'');
                     ////   $("#pro_limit_hide").val(obj2.anchor_sub_limit);  
                        $("#supplier_id").empty();
                        $("#supplier_id").val('');
                        $("#supplier_id").val(supplierId);  
                        // $(obj1).each(function(i,v){
                        //          var dApp = v.appCode;
                        //          //$("#supplier_id").append("<option value='"+v.user_id+","+v.app_id+","+v.prgm_offer_id+"'>"+v.f_name+"&nbsp;"+v.l_name+" ("+ dApp +")</option>");
                        //          $("#supplier_id").append("<option value='"+v.user_id+","+v.app_id+","+v.prgm_offer_id+"'>"+v.biz_entity_name+"&nbsp;&nbsp;("+v.customer_id+")</option>");  
                        //     });
                       
                    }
                    else
                    {
                        
                              //  $("#supplier_id").append("<option value=''>No data found</option>");
                               $("#supplier_id").val("");  
                      
                    }
                  
                }
        }); }); 
   
  //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','.getTenor',function(){
      
      var program_id =  $("#program_id").val(); 
      var anchor_id =  $("#anchor_id").val();
      var supplier_id  = $(this).val();
       $("#invoice_date, #invoice_due_date, #invoice_approve_amount").val(''); 
      if(supplier_id=='')
      {
            $("#limit_type").prop("checked", false);
            $("#pro_limit").html('');
            $("#pro_remain_limit").html('');
            $("#adhoc_msg").hide();
             return false; 
      }
       $(".isloader").show(); 
        IdArray = program_id.split(",");
        var prgmId = IdArray[0];
        var appLimitId = IdArray[1];
        IdsArray = [prgmId,appLimitId];
        program_id = IdsArray.join(",");
     var postData =  ({'bulk':0,'anchor_id':anchor_id,'supplier_id':supplier_id,'program_id':program_id,'_token':messages.token});
       jQuery.ajax({
        url: messages.get_tenor,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                
                },
                success: function (data) {
                      $(".isloader").hide(); 
                      if(data.is_adhoc!=0)
                      {
                        $("#adhoc_msg").show();
                      }else {
                        $("#adhoc_msg").hide();
                      }
                        var tenor   =  data.tenor;
                        var tenor_old_invoice  = data.tenor_old_invoice;
                        $("#tenor_old_invoice").val(tenor_old_invoice);
                        $("#tenor").val(tenor);
                        $("#pro_limit").html('Prgm. Limit : <span class="fa fa-inr"></span>  '+data.limit+'');
                        $("#pro_remain_limit").html('Remaining Prgm. Balance : <span class="fa fa-inr"></span>  '+data.remain_limit+'');
                        $("#pro_limit_hide").val(data.remain_limit);  
                        $("#margin").val(data.margin);
                        $('#calculateUpfrontInt').addClass('hide');
                        $('#upFrontAmount').addClass('hide');
                        if (data.offerData !== null) {
                          // Parse the JSON string into an object
                          const decodedOfferData = atob(data.offerData);
                          const offerData = JSON.parse(decodedOfferData);
                          if (offerData.payment_frequency != null){
                            switch (offerData.payment_frequency) {
                              case '1':
                                 textPayFre = '<b>Payment Frequency :</b> Upfront';
                                 $('#calculateUpfrontInt').removeClass('hide');
                                 $('#upFrontAmount').removeClass('hide');
                                break;
                              case '2':
                                textPayFre = '<b>Payment Frequency :</b> Monthly';
                                break;
                              case '3':
                                textPayFre = '<b>Payment Frequency :</b> Rear Ended';
                                break;
                              default:
                                // Handle the case where the payment frequency is not recognized
                                textPayFre = '';
                                break;
                            }
                            $('#text_payment_frequency').empty().html(textPayFre);
                          }
                          if (offerData.benchmark_date != null){
                              switch (offerData.benchmark_date) {
                                case '1':
                                  textBencMarDate = '<b>Benchmark Date :</b> Invoice Date';
                                  break;
                                case '2':
                                  textBencMarDate = '<b>Benchmark Date :</b> Date of discounting';
                                  break;
                                default:
                                  // Handle the case where the payment frequency is not recognized
                                  textBencMarDate = '';
                                  break;
                              }
                              $('#text_benchmark_date').empty().html(textBencMarDate);
                          }

                          // Find the hidden input field on the page
                          var hiddenInputField = $('input[id="offer_data"]');
                          // Set the value of the hidden input field to the JSON string
                          hiddenInputField.val('').val(data.offerData);
                        }  
                }
        }); }); 
  
  
    //////////////////// onchange anchor  id get data /////////////////
  $(document).on('click','.get_adhoc',function(){
        $(".isloader").show(); 
        $("#msgProLimit").hide();
        var is_adhok  = $(this).is(":checked");
        var program_id =  $("#program_id").val(); 
        var anchor_id =  $("#anchor_id").val(); 
        var supplier_id  = $("#supplier_id").val();
        IdArray = program_id.split(",");
        var prgmId = IdArray[0];
        var appLimitId = IdArray[1];
        IdsArray = [prgmId,appLimitId];
        program_id = IdsArray.join(",");
        var postData =  ({'bulk':0,'anchor_id':anchor_id,'supplier_id':supplier_id,'program_id':program_id,'is_adhoc':is_adhok,'_token':messages.token});
       jQuery.ajax({
        url: messages.get_adhoc,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                
                },
                success: function (data) {
                        $(".isloader").hide(); 
                        var tenor   =  data.tenor;
                        var tenor_old_invoice  = data.tenor_old_invoice;
                        $("#tenor_old_invoice").val(tenor_old_invoice);
                        $("#tenor").val(tenor);
                       if(data.is_adhoc==1)
                       {
                        $("#pro_limit").html('Adhoc Limit : <span class="fa fa-inr"></span>  '+data.limit+'');
                        $("#pro_remain_limit").html('Remaining Adhoc Balance : <span class="fa fa-inr"></span>  '+data.remain_limit+'');
                        $("#pro_limit_hide").val(data.remain_limit); 
                    }
                    else
                    {
                        $("#pro_limit").html('Prgm. Limit : <span class="fa fa-inr"></span>  '+data.limit+'');
                        $("#pro_remain_limit").html('Remaining Prgm. Balance : <span class="fa fa-inr"></span>  '+data.remain_limit+'');
                        $("#pro_limit_hide").val(data.remain_limit); 
                     
                    }
                      
                }
        }); }); 
    
    
  $(document).on('change','#supplier_id',function(){
    var selValue = $(this).val();
    var selValueArr = selValue.split(",");
    $("#prgm_offer_id").val(selValueArr[2]);       
  });

  $('#calculateUpfrontInt').click(function(){
    const offerDataJson = $('#offer_data').val();
    console.log(offerDataJson);
    // Decode the base64-encoded string
    const decodedOfferData = atob(offerDataJson);
    console.log(decodedOfferData);
    // Parse the JSON string into an object
    const offerData = JSON.parse(decodedOfferData);
    console.log(offerData);
    calculateUpfrontInterest(offerData);       
  });
  
  function calculateUpfrontInterest(offerData) {
    if (!offerData || offerData.payment_frequency != 1) {
      return null;
    }
    $("#upFrontAmount").show();
    var invoice_due_date  = $('#invoice_due_date').val();
    var invoice_date = $('#invoice_date').val();
    var invoice_approve_amount = $("#invoice_approve_amount").val();
    var invoice_approve_amount = parseInt(invoice_approve_amount.replace(/\,/g,''));
    if (!invoice_due_date || !invoice_date || !invoice_approve_amount) {
      $("#upFrontAmount").empty().html('<b style="color:red;">Invoice Date, Invoice Due Date, Invoice Amount is required to Calculate Upfront Interest Amount</b>').show();
      setTimeout(function(){ 
          $("#upFrontAmount").hide();
      }, 5000); // hide after 5 seconds
      return false;
    }
    var tenor = findDaysWithDate(invoice_due_date, invoice_date);
    var margin = calMargin(invoice_approve_amount, offerData.margin);
    var fundedAmount = invoice_approve_amount - margin;
    var interestRate = parseFloat(offerData.interest_rate);
    if (offerData.benchmark_date == 1) {
      var currentDate = new Date(offerData.currentDate);
      var options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      var curDate = currentDate.toLocaleDateString(undefined, options);
      tenor = findDaysWithDate(invoice_due_date, curDate);
      console.log(currentDate, curDate, invoice_due_date, curDate);
    }
    var upfrontInterest = calculateInterest(fundedAmount, interestRate, tenor);
    console.log(upfrontInterest.toFixed(2),fundedAmount, interestRate, tenor);
    $("#upFrontAmount").empty().html(
      '<b style="color:rgb(5, 88, 19);">Upfront Interest Amount: ' + upfrontInterest.toFixed(2) + '</b>,&nbsp;&nbsp;' +
      '<b style="color:rgb(5, 88, 19);">Upfront Interest Days: ' + tenor + '</b>'
    );    
  }
  // Define the calMargin and calInterest functions as needed.
  function calculateInterest(principalAmt, interestRate, tenorDays) {
    var interest = parseFloat(((principalAmt * (interestRate / 365)) / 100).toFixed(2));
    return parseFloat((tenorDays * interest).toFixed(2));
  }

  function calMargin(amt, val) {
    return parseFloat(((amt * val) / 100).toFixed(2));
  }  