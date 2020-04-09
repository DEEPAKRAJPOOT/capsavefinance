  //////////Get Promoter detail by Cin Number//////////  
      jQuery(document).ready(function () {
       var countOwnerRow = $("#rowcount").val();
        if (countOwnerRow > 0)
        {
           return false;
        } 

        $('.isloader').show();
         var CIN = '{{ (isset($cin_no)) ? $cin_no : "" }}';
        if(CIN=='')
        {
             $('.isloader').hide();
             $('#btnAddMore').trigger('click'); 
             return false;
        }
        var consent = "Y";
        var dataStore = ({'consent': consent, 'entityId': CIN,'_token': messages.token});
        var postData = dataStore;
        jQuery.ajax({
        url: messages.get_promoter_details_by_cin,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                console.log(xhr);
                $('.isloader').hide();
                $('#btnAddMore').trigger('click'); return false;
                },
                success: function (result) {

                $(".isloader").hide();
                obj = result.value;
                var count = 0;
                var arr = new Array();
                var x = 0;
                $(obj).each(function (k, v) {
                var temp = {};
                var dob = v.dob;
                var dateAr = dob.split('-');
                var newDate = '';
                if (dateAr != '')
                {

                var newDate = dateAr[0] + '/' + dateAr[1] + '/' + dateAr[2];
                }

                if (k >= 0)
                {

                temp['first_name'] = v.name;
                temp['address'] = v.address;
                temp['dob'] = newDate;
                arr.push(temp);
                //// $(".form-fields-appand").append("<div class='fornm-sections'><div class='row'><div class='col-md-12'><div class='col-md-12'><button class='close clsdiv' type='button'>x</button><h3>Promoter</h3></div><div class='col-md-12'><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name'>Promoter Name<span class='mandatory'>*</span></label><input type='hidden'class='owneridDynamic' id='ownerid"+x+"' name='ownerid[]' value=''><input type='text' name='first_name[]' vname='first_name" + x + "' id='first_name" + x + "' value='"+v.name+"' class='form-control first_name' placeholder='Enter First Name' ></div></div><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name' >Last Name</label><input type='text' name='last_name[]' id='last_name" + x + "' value='' class='form-control last_name' placeholder='Enter Last Name' ></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>DOB<span class='mandatory'>*</span></label><input type='text' name='date_of_birth[]'  id='date_of_birth" + x + "' value='"+newDate+"' class='form-control date_of_birth datepicker-dis-fdate'  placeholder='Enter Date Of Birth' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group password-input'><label for='gender'>Gender<span class='mandatory'>*</span></label><select class='form-control gender' name='gender[]'   id='gender" + x + "'><option value=''> Select Gender</option><option value='1'> Male </option><option value='2'>Female </option></select></div></div><div class='col-md-4'><div class='form-group'><label for='pan_no'>PAN Number<span class='mandatory'>*</span><span class='text-success' id='successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span></label><a href='javascript:void(0);' data-id='" + x + "' id='pan_verify" + x + "' class='verify-owner-no promoter_pan_verify'>Verify</a><input type='text' name='pan_no[]'  id='pan_no" + x + "' value='' class='form-control pan_no' placeholder='Enter Pan No' ><input name='response[] id='response" + x + "' type='hidden' value=''></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>Shareholding (%)<span class='mandatory'>*</span></label><input type='text' name='share_per[]' id='share_per" + x + "' id='employee' value='' class='form-control share_per'  placeholder='Enter Shareholder' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Educational Qualification</label><input type='text' name='edu_qualification[]'  id='edu_qualification" + x + "' value='' class='form-control edu_qualification'  placeholder='Enter Education Qualification.'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Other Ownerships</label><input type='text' name='other_ownership[]' id='other_ownership" + x + "' value='' class='form-control other_ownership'  placeholder='Enter Other Ownership'></div></div><div class='col-md-4'><div class='form-group INR'><label for='txtEmail'>Networth </label><a href='javascript:void(0);' class='verify-owner-no'><i class='fa fa-inr' aria-hidden='true'></i></a><input type='text' maxlength='15' name='networth[]' id='networth" + x + "' value='' class='form-control networth'  placeholder='Enter Networth'></div></div> </div></div><div class='col-md-8'><div class='form-group password-input'><label for='txtPassword'>Address<span class='mandatory'>*</span></label><textarea class='form-control textarea address' placeholder='Enter Address' name='owner_addr[]' id='address" + x + "'>"+v.address+"</textarea></div></div> <h5 class='card-title form-head-h5 mt-3'>Document </h5><div class='row mt-2 mb-4'><div class='col-md-12'> <div class='prtm-full-block'><div class='prtm-block-content'><div class='table-responsive ps ps--theme_default' data-ps-id='9615ce02-be28-0492-7403-d251d7f6339e'><table class='table text-center table-striped table-hover'><thead class='thead-primary'><tr><th class='text-left'>S.No</th><th>Document Name</th><th>Document ID No.</th><th>Action</th></tr></thead><tbody><tr><td class='text-left'>1</td><td width='30%'>Pan Card</td><td width='30%'><div class='col-md-12'><span class='text-success' id='v1successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v1failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span><a href='javascript:void(0);' id='ppan"+ x +"' data-id='"+ x +"' class='verify-owner-no verify-show veripan' style='top:0px'>Verify</a><input type='text'  name='veripan[]' id='veripan"+ x +"' value='' class='form-control'  placeholder='Enter PAN Number'></div></td><td width='28%'><div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details' data-id='" + x + "' data-type='3'> <i class='fa fa-eye'></i></button><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button><input type='file' name='verifyfile[]' class='verifyfile' id='verifyfile" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file'  name='panfile[]' data-id='" + x + "' class='panfile' id='panfile" + x + "'> </div> </td> </tr><tr> <td class='text-left'>2</td> <td width='30%'>Driving License</td> <td width='30%' > <div class='col-md-12'><span class='text-success' id='v2successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v2failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span> <a href='javascript:void(0);' id='ddriving" + x + "' data-id='" + x +"'  class='verify-owner-no verify-show veridl' style='top:0px;'>Verify</a> <input type='text' name='verifydl[]' id='verifydl" + x + "' value='' class='form-control verifydl'  placeholder='Enter DL Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "' data-type='5'> <i class='fa fa-eye'></i></button> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' id='downloaddl" + x + "' name='downloaddl[]' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple='' class='downloaddl'> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file'  name='dlfile[]' data-id='" + x + "' class='dlfile' id='dlfile" + x + "'> </div> </td> </tr> <tr> <td class='text-left'>3</td> <td width='30%'>Voter ID</td> <td width='30%' > <div class='col-md-12'><span class='text-success' id='v3successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v3failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span> <a href='javascript:void(0);' id='vvoter" + x + "' data-id='" + x +"'  class='verify-owner-no verify-show verivoter' style='top:0px;'>Verify</a> <input type='text' name='verifyvoter[]' id='verifyvoter" + x + "' value='' class='form-control verifyvoter'  placeholder='Enter Voter's Epic Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'><button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "'  data-type='4'> <i class='fa fa-eye'></i></button> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadvoter[]' class='downloadvoter' id='downloadvoter" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'  class='voterfile' name='voterfile[]' id='voterfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>4</td> <td width='30%'>Passport</td> <td width='30%' > <div class='col-md-12'> <span class='text-success' id='v4successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='v4failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span><a href='javascript:void(0);' id='ppassport" + x + "' data-id='" + x +"' class='verify-owner-no verify-show veripass' style='top:0px;'>Verify</a> <input type='text' name='verifypassport[]' id='verifypassport" + x + "' value='' class='form-control verifypassport'  placeholder='Enter File Number'> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm viewDocument' type='button' title='view Details'  data-id='" + x + "'  data-type='6'> <i class='fa fa-eye'></i></button><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadpassport[]' class='downloadpassport'  id='downloadpassport" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'   name='passportfile[]' class='passportfile' id='passportfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>5</td> <td width='30%'>Photo</td> <td width='30%' > </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadphoto[]' class='downloadphoto' id='downloadphoto" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "'  name='photofile[]' name='photofile' id='photofile" + x + "'> </div> </td> </tr> </tbody> </table> <div class='ps__scrollbar-x-rail' style='left: 0px; bottom: 0px;'><div class='ps__scrollbar-x'  style='left: 0px; width: 0px;'></div></div><div class='ps__scrollbar-y-rail' style='top: 0px; right: 0px;'><div class='ps__scrollbar-y'  style='top: 0px; height: 0px;'></div></div> </div> </div> </div> </div> </div> </div></div></div> ");
                x++;
                }
                count++;
                });
                var bizId = $('input[name=biz_id]').val();
                var appId = $('input[name=app_id]').val();
                var getRes = savePromoter(arr, bizId, appId);
                }
        });
        });
        
         /* save promoter details after cin number api hit */
        function  savePromoter(data, bizId, appId)
        {

        var data = {'data' : data, 'biz_id' : bizId, 'app_id' : appId};
        jQuery.ajax({
        url: "/application/promoter-save",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'post',
                contentType: "json",
                processData: false,
                data: JSON.stringify(data),
                success: function (data) {
                if (data.data.length > 0)    
                window.location.href = "{{ route('promoter_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}";
                var promoId = 0;
                $(data.data).each(function(k, v){
                console.log(v);
                $("#ownerid" + promoId).val(v);
                promoId++;
                });
                $("#rowcount").val(k);
                }
        });
        }
 
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
      if(invoice_approve_amount  > pro_limit)
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
     }
     
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
                            $("#submit").css("pointer-events","auto");
                            $("#submit").css("pointer-events","none");
                            return false;
                        }
                        else
                        {
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
     if ($('form#signupForm').validate().form()) {  
       $("#anchor_id" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter Anchor name",
        }
        });
       
      $("#supplier_id" ).rules( "add", {
        required: true,
        messages: {
        required: "Please Select Supplier Name",
        }
        });
          $("#program_id" ).rules( "add", {
        required: true,
        messages: {
        required: "Please Select Product Program Name",
        }
        });
        $("#invoice_no" ).rules( "add", {
        required: true,
        maxlength: 20,
        messages: {
        required: "Please enter Invoice No",
        maxlength: "Maximum 20  characters are necessary",
        }
        });
        
        $("#invoice_due_date" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter Invoice Due Date",
        }
        }); 
        $("#invoice_date" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter Invoice Date",
        }
        }); 
        
        $("#invoice_approve_amount" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter Invoice Approve Amount",
        }
        }); 
        $("#customFile" ).rules( "add", {
        required: true,
        messages: {
        required: "Please upload Invoice Copy",
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
          return false; 
      }
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
                        var tenor   =  data.tenor;
                        var tenor_old_invoice  = data.tenor_old_invoice;
                        $("#tenor_old_invoice").val(tenor_old_invoice);
                        $("#tenor").val(tenor);
                        $("#pro_limit").html('Program Limit : <span class="fa fa-inr"></span>  '+data.limit+'');
                        $("#pro_remain_limit").html('Remaining Program Balance : <span class="fa fa-inr"></span>  '+data.remain_limit+'');
                        $("#pro_limit_hide").val(data.remain_limit);  
                      
                }
        }); }); 
    
  $(document).on('change','#supplier_id',function(){
    var selValue = $(this).val();
    var selValueArr = selValue.split(",");
    $("#prgm_offer_id").val(selValueArr[2]);       
  });   