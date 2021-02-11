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
     if(invoice_approve_amount==0)
     {
         $("#invoice_approve_amount").val('');
         return false;
     }
    /*  if(invoice_approve_amount  > pro_limit)
     {
         $("#msgProLimit").text('Invoice amount should not be more than offered limit amount.');
         $("#submit").css("pointer-events","none");
         return false;
     }
     else
     {
         $("#msgProLimit").empty();
         $("#submit").css("pointer-events","auto");
         return true;
     } */
     
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
        required: "Please select product program name",
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
       $("#customFile").rules("add", {
         required: '.is_phy_inv[value="1"]:checked',
         submitHandler: function () {
           if ($('.is_phy_inv[value="1"]').is(":checked")) {
             $('.customFile_astrik').html('*');
           }
           if ($('.is_phy_inv[value="2"]').is(":checked")) {
             $('.customFile_astrik').html(' ');
           }
         },
         messages: {
           required: "Please upload invoice copy",
         }
       }); 
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
      if(anchor_id=='')
      {
            $("#pro_limit").empty();
             $("#pro_limit_hide").empty();
      }
      $("#program_id").empty();
      $("#anc_limit").empty();
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
                    if(data.status==1)
                    {
                        var obj1  = data.get_program;
                        var obj2   =  data.limit;
                        $("#anc_limit").html('Limit : <span class="fa fa-inr"></span>  '+obj2.anchor_limit+'');
                           $("#program_id").append("<option value=''>Please Select</option>");  
                            $(obj1).each(function(i,v){
                             if(v.program!=null)
                             {                                 
                                   $("#program_id").append("<option value='"+v.program.prgm_id+","+v.app_prgm_limit_id+"'>"+v.program.prgm_name+"</option>");  
                              }                   
                             });
                           
                        
                       
                    }
                    else
                    {
                       
                               $("#program_id").append("<option value=''>No data found</option>");  
                           
                      
                    }
                  
                }
        }); }); 
   
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
      var program_id =  $(this).val(); 
      var anchor_id =  $("#anchor_id").val(); 
      if(program_id=='')
      {
          return false; 
      }
      $("#supplier_id").empty();
      $("#pro_limit").empty();
      $("#pro_limit_hide").empty();
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
                     ///   $("#tenor_old_invoice").val(tenor_old_invoice);
                     ///   $("#tenor").val(tenor);
                     ///   $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  '+obj2.anchor_sub_limit+'');
                     ////   $("#pro_limit_hide").val(obj2.anchor_sub_limit);  
                        $("#supplier_id").empty();
                        $("#supplier_id").append("<option value=''>Please Select Customer</option>");  
                        $(obj1).each(function(i,v){
                                 var dApp = v.appCode;
                                 //$("#supplier_id").append("<option value='"+v.user_id+","+v.app_id+","+v.prgm_offer_id+"'>"+v.f_name+"&nbsp;"+v.l_name+" ("+ dApp +")</option>");
                                 $("#supplier_id").append("<option value='"+v.user_id+","+v.app_id+","+v.prgm_offer_id+"'>"+v.biz_entity_name+"&nbsp;&nbsp;("+v.customer_id+")</option>");  
                            });
                       
                    }
                    else
                    {
                        
                               $("#supplier_id").append("<option value=''>No data found</option>");  
                      
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
                      }
                        var tenor   =  data.tenor;
                        var tenor_old_invoice  = data.tenor_old_invoice;
                        $("#tenor_old_invoice").val(tenor_old_invoice);
                        $("#tenor").val(tenor);
                        $("#pro_limit").html('Prgm. Limit : <span class="fa fa-inr"></span>  '+data.limit+'');
                        $("#pro_remain_limit").html('Remaining Prgm. Balance : <span class="fa fa-inr"></span>  '+data.remain_limit+'');
                        $("#pro_limit_hide").val(data.remain_limit);  
                      
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