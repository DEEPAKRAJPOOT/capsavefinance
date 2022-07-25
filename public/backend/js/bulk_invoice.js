 $(document).ready(function(){
    setInterval(function(){  localStorage.setItem('storageMsg',''); }, 1000);
     var  msg = localStorage.getItem('storageMsg');
    if(msg)
     {
       $("#storeSuccessMsg").html("<div class='alert-success alert' role='alert'><span><i class='fa fa-bell fa-lg' aria-hidden='true'></i></span>"+msg+"</div>");
     }
   })
 ///* upload image and get ,name  */
    $('input[name="file_id"]').change(function (e) {
        $("#customFile_msg").html('');
       var get_sfm =  $("#customImageFile_msg").html();
        var fileName = e.target.files[0].name;
        var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
          if(fileNameExt!='csv')
       {
            $("#submit").css("pointer-events","none");
            $("#customFile_msg").show();
            $("#customFile_msg").text("File format is not correct, only csv file is allowed."); 
            return false;
       }
       else if(get_sfm=='')
       {
            $("#submit").css("pointer-events","auto");
       }
        $("#msgFile").html('The file "' + fileName + '" has been selected.');
    });
    
    ///* upload image and get ,name  */
    $('input[name="file_image_id"]').change(function (e) {
        $("#customImageFile_msg").html('');
        var get_sfm =  $("#customFile_msg").html();
        var fileName = e.target.files[0].name;
         var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
          if(fileNameExt!='zip')
       {
            $("#submit").css("pointer-events","none");
            $("#customImageFile_msg").show();
            $("#customImageFile_msg").text("File format is not correct, only zip file is allowed.");
            return false;
       }
       else if(get_sfm=='')
       {
            $("#submit").css("pointer-events","auto");
       }
        $("#msgImageFile").html('The file "' + fileName + '" has been selected.');
    });
    
   
    $(document).ready(function () {
        $(".finalButton").hide();
        $(".invoiceAppendData").append('<tr><td colspan="5">No data found...</td></tr>');
        ///  $("#program_bulk_id").append("<option value=''>No data found</option>");  


    });


    //////////////// for checked & unchecked////////////////
    $(document).on('click', '#chkAll', function () {
        var isChecked = $("#chkAll").is(':checked');
        if (isChecked)
        {
            $('input:checkbox').attr('checked', 'checked');
        } else
        {
            $('input:checkbox').removeAttr('checked');
        }
    });


    //////////////////// onchange anchor  id get data /////////////////

    $("#supplier_id").append("<option value=''>Select customer</option>");
    $(document).on('change', '.changeAnchor', function () {
        var anchor_id = $(this).val();
        $("#supplier_id").empty();

        var postData = ({'anchor_id': anchor_id, '_token': messages.token});
        jQuery.ajax({
            url: messages.get_program_supplier,
            method: 'post',
            dataType: 'json',
            data: postData,
            error: function (xhr, status, errorThrown) {
                alert(errorThrown);

            },
            success: function (data) {

                if (data.status == 1)
                {
                    var obj1 = data.userList;

                    ///////////////////// for suppllier array///////////////  

                    if (obj1.length > 0)
                    {
                        $("#supplier_id").append("<option value=''> Select Supplier </option>");
                        $(obj1).each(function (i, v) {

                            $("#supplier_id").append("<option value='" + v.app.user.user_id + "'>" + v.app.user.f_name + "</option>");

                        });
                    } else
                    {
                        $("#supplier_id").append("<option value=''>No data found</option>");

                    }


                }

            }
        });
    });


    /////////// for pop up//////////////////


    //////////////////// onchange anchor  id get data /////////////////
    $(document).on('change', '.changeBulkAnchor', function () {
        var  msg = localStorage.setItem('storageMsg','');
        $("#anchor_bulk_id_msg").hide();
        var anchor_id = $(this).val();
        if (anchor_id == '')
        {
            $("#pro_limit").empty();
            $("#pro_limit_hide").empty();
        }
        $("#program_bulk_id").empty();
        $("#anc_limit").empty();
        var postData = ({'anchor_id': anchor_id, '_token': messages.token});
        jQuery.ajax({
            url: messages.front_program_list,
            method: 'post',
            dataType: 'json',
            data: postData,
            error: function (xhr, status, errorThrown) {
                alert(errorThrown);

            },
            success: function (data) {
                if (data.status == 1)
                {

                    var obj1 = data.get_program;
                    var obj2 = data.limit;

                    $("#anc_limit").html('Limit : <span class="fa fa-inr"></span>  ' + obj2.anchor_limit + '');


                    $("#program_bulk_id").append("<option value=''>Please Select</option>");
                    $(obj1).each(function (i, v) {
                        if (v.program != null)
                        {
                            $("#program_bulk_id").append("<option value='" + v.program.prgm_id + "," + v.app_prgm_limit_id + "'>" + v.program.prgm_name + "</option>");
                        }
                    });
                } else
                {
                    $("#program_bulk_id").append("<option value=''>No data found</option>");
                }
            }
        });
    });

    //////////////////// onchange anchor  id get data /////////////////
    $(document).on('change', '.changeBulkSupplier', function () {

        $("#program_bulk_id_msg").hide();
        var program_id = $(this).val();
         if(program_id=='')
        {
            return false;
        }
        var anchor_id = $("#anchor_bulk_id").val();
        $("#supplier_bulk_id").empty();
        $("#pro_limit").empty();
        $("#pro_limit_hide").empty();
        var postData = ({'bulk': 1, 'anchor_id': anchor_id, 'program_id': program_id, '_token': messages.token});
        jQuery.ajax({
            url: messages.front_supplier_list,
            method: 'post',
            dataType: 'json',
            data: postData,
            error: function (xhr, status, errorThrown) {
                alert(errorThrown);

            },
            success: function (data) {
                if (data.status == 1)
                {
                     if(data.uploadAcess==0)
                        {
                            $("#tenorMsg").text("You don't have permission to upload invoice for this program.");           
                            $("#submit").hide();
                            
                        }
                        else
                        {
                             $("#submit").show();
                             $("#tenorMsg").text(" ");           
                           
                            
                        }
                    var obj1 = data.get_supplier;
                    var obj2 = data.limit;
                    var offer_id = data.offer_id;
                    var tenor = data.tenor;
                    var tenor_old_invoice = data.tenor_old_invoice;
                    $("#prgm_offer_id").val(offer_id);
                   // $("#tenor_old_invoice").val(tenor_old_invoice);
                   // $("#tenor").val(tenor);
                   /// $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  ' + obj2.anchor_sub_limit + '');
                   // $("#pro_limit_hide").val(obj2.anchor_sub_limit);
                    $("#supplier_bulk_id").append("<option value=''>Please Select Customer</option>");  
                    $(obj1).each(function (i, v) {
                        var dApp = "000000" + v.app_id;
                        //$("#supplier_id").append("<option value='"+v.user_id+","+v.app.app_id+"'>"+v.f_name+"&nbsp;"+v.l_name+"("+v.app.app_id+")</option>");  
                        $("#supplier_bulk_id").append("<option value='" + v.user_id + "," + v.app_id + "," + v.prgm_offer_id + "'>" + v.biz_entity_name + "&nbsp;&nbsp;(" + v.customer_id + ")</option>");
                    });

                } else
                {
                    $("#supplier_bulk_id").append("<option value=''>No data found</option>");
                }

            }
        });
    });
  //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','.getTenor',function(){
      var program_id =  $("#program_bulk_id").val(); 
      var anchor_id =  $("#anchor_bulk_id").val(); 
      var supplier_id  = $(this).val();
      if(supplier_id=='')
      {
          return false; 
      }
     var postData =  ({'bulk':1,'anchor_id':anchor_id,'supplier_id':supplier_id,'program_id':program_id,'_token':messages.token});
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
                        $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  '+data.limit+'');
                        $("#pro_limit_hide").val(data.limit);  
                      
                }
        }); }); 
   
    $(document).on('change', '#supplier_bulk_id', function () {
        if ($("#supplier_bulk_id").val() != '')
        {
            $("#supplier_bulk_id_msg").hide();
        }
    });
    $(document).on('change', '.fileUpload', function () {

        $("#customFile_msg").hide();

    });
    
    function ChangeDateFormat(date)
    {
        var datearray = date.split("/");
        return  newdate = datearray[1] + '/' + datearray[0] + '/' + datearray[2];

    }

    function findDaysWithDate(firstDate, secondDate)
    {
        var firstDate = ChangeDateFormat(firstDate);
        var secondDate = ChangeDateFormat(secondDate);
        var startDay = new Date(firstDate);
        var endDay = new Date(secondDate);
        var millisecondsPerDay = 1000 * 60 * 60 * 24;
        var millisBetween = startDay.getTime() - endDay.getTime();
        var days = millisBetween / millisecondsPerDay;
        return  Math.floor(days);
    }
    /////////////// validation the time of final submit/////////////// 
    $(document).on('click', '#final_submit', function (e) {
        
       var arr = $(".getUploadBulkId").map(function() {
                return $(this).attr("data-id");
              }).get().join();
      if(confirm('Are you sure, You want to final submit'))
      {
        $('.isloader').show();   
        var postData =  ({'id':arr,'_token':messages.token});
        jQuery.ajax({
         url: messages.upload_invoice_csv,
                 method: 'post',
                 dataType: 'json',
                 data: postData,
                 error: function (xhr, status, errorThrown) {
                 alert(errorThrown);

                 },
                 success: function (data) {
                       $('.isloader').hide();
                       if(data.status==1)
                       {
                            localStorage.setItem('storageMsg', 'Invoice successfully saved');
                            location.reload(); 
                       }else if(data.status == 0 && typeof data.message != 'undefined') {
                            localStorage.setItem('storageMsg', data.message);
                            location.reload(); 
                       }
                 }
         });  
       }
       else
       {
           return false;
       }
            ///var users = $('input:text.users').serialize();
        /* $("#final_submit_msg").hide();
        var p_limit = $("#pro_limit_hide").val();
        var sum = 0;
        if ($('form#signupForm').validate().form()) {
            $(".batchInvoice").rules("add", {
                required: true,
                messages: {
                    required: "Please enter invoice no",
                }
            });
            $(".batchInvoiceDueDate").rules("add", {
                required: true,

                messages: {
                    required: "Please enter currect invoice due date",
                }
            });
            $(".batchInvoiceDate").rules("add", {
                required: true,

                messages: {
                    required: "Please enter currect invoice due date",
                }
            });
            $(".subOfAmount").rules("add", {
                required: true,
                messages: {
                    required: "Please enter currect invoice amount",
                }
            });

            //////// check total amount /////////////
            $(".subOfAmount").each(function () {
                sum += parseInt($(this).val().replace(/,/g, ''));
            });
            if (sum > p_limit)
            {
                $("#final_submit_msg").show();
                e.preventDefault();
            }

            ////////// check tanor date///////////////////
            var count = 0;
            $(".batchInvoiceDate").each(function (i, v) {
                count++;
                var first = $(".invoiceTanor" + count).val();
                var second = $(this).val();
                var getDays = parseInt(findDaysWithDate(first, second));
                var tenor = parseInt($('#tenor').val());
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth() + 1; //As January is 0.
                var yyyy = today.getFullYear();
                var cDate = dd + "/" + mm + "/" + yyyy;
                var getOldDays = findDaysWithDate(cDate, second);
                var tenor = $('#tenor').val();
                var tenor_old_invoice = $('#tenor_old_invoice').val();
                /*if(getOldDays > tenor_old_invoice)
                 {
                 $("#tenorMsg").show(); 
                 $("#tenorMsg").html('Invoice Date & Current Date diffrence should be '+tenor_old_invoice+' days'); 
                 e.preventDefault();
                 }
                 else 
                if (getDays > tenor)
                {
                    $(".appendExcel" + count).css("background-color", "#ea9292");
                    $("#tenorMsg").show();
                    $("#tenorMsg").html('Invoice Date & Invoice Due Date diffrence should be ' + tenor + ' days');
                    e.preventDefault();
                } else if (getDays < 0)
                {

                    $("#tenorMsg").show();
                    $("#tenorMsg").html('Invoice Due Date should be  greater than invoice date');
                    e.preventDefault();
                } else
                {
                    $(".appendExcel" + count).css("background-color", "white");
                }
            });

        } else {
            /// alert();
        }  */

    });

    //////// String value not allowed in  amount filed//////////////////////
    $(document).on('keypress', '.subOfAmount', function (event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
        var invoice_approve_amount = $(this).attr("id");
        document.getElementById(invoice_approve_amount).addEventListener('input', event =>
            event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));

    });
    
       $(document).on('click', '#submit', function (e) {
        $("#storeSuccessMsg").hide();
        
        if ($("#anchor_bulk_id").val() == '')
        {
            $("#anchor_bulk_id_msg").show();
            $("#anchor_bulk_id_msg").text('Please Select Anchor Name');
            return false;
        }
       else if ($("#program_bulk_id").val() == '')
        {
            $("#program_bulk_id_msg").show();
            $("#program_bulk_id_msg").text("Please Select Product Program Name");
            return false;
        }
        else if ($("#customImageFile").val() == '' && $("#customImageFileval").val() == 1)
         {
             $("#customImageFile_msg").show();
             $("#customImageFile_msg").text("Please Select Invoice Copy Zip File");
             return false;
         }
      else   if ($("#customFile").val() == '')
        {
            $("#customFile_msg").show();
            $("#customFile_msg").text("Please Select Csv file");
            return false;
        } else
        {
             $(".isloader").show();
            return true;
           /* if (confirm("Are you sure? You want to upload CSV")) {
                $(".invoiceAppendData").empty();
                var file = $("#customFile")[0].files[0];
                var datafile = new FormData();
                var anchor_bulk_id = $("#anchor_bulk_id").val();
                var program_bulk_id = $("#program_bulk_id").val();
                var supplier_bulk_id = $("#supplier_bulk_id").val();
                var pro_limit_hide = $("#pro_limit_hide").val();
                var pay_calculation_on = $("#pay_calculation_on").val();
                datafile.append('_token', messages.token);
                datafile.append('doc_file', file);
                datafile.append('anchor_bulk_id', anchor_bulk_id);
                datafile.append('program_bulk_id', program_bulk_id);
                datafile.append('supplier_bulk_id', supplier_bulk_id);
                datafile.append('pro_limit_hide', pro_limit_hide);
                datafile.append('pay_calculation_on', pay_calculation_on);
                $('.isloader').show();
                $.ajax({
                    headers: {'X-CSRF-TOKEN': messages.token},
                    url: messages.upload_invoice_csv,
                    type: "POST",
                    data: datafile,
                    processData: false,
                    contentType: false,
                    cache: false, // To unable request pages to be cached
                    enctype: 'multipart/form-data',

                    success: function (r) {

                        $(".isloader").hide();

                        if (r.status == 1)
                        {
                            $('.isloader').hide();
                            $(".finalButton").show();

                            j = 0;
                            $(r.data).each(function (i, v) {
                                j++;
                                var invoice_approve_amount = v.invoice_approve_amount;
                                var date1 = v.invoice_due_date;
                                var dateAr = date1.split('-');
                                var invoice_due_date = '';
                                var invoice = '';
                                if (dateAr != '')
                                {

                                    var invoice_due_date = dateAr[2] + '/' + dateAr[1] + '/' + dateAr[0];
                                }
                                var date2 = v.invoice_date;
                                var dateAr1 = date2.split('-');
                                if (dateAr1 != '')
                                {

                                    var invoice_date = dateAr1[2] + '/' + dateAr1[1] + '/' + dateAr1[0];
                                }
                                if (parseInt(v.invoice_approve_amount) == '0.00')
                                {
                                    var invoice_approve_amount = "";
                                }

                                var getDays = parseInt(findDaysWithDate(invoice_due_date, invoice_date));
                                var tenor = parseInt($('#tenor').val());
                                var getClass = "";
                                if (getDays > tenor)
                                {
                                    var getClass = "background-color: #ea9292;";
                                }
                                var invoice_approve_amount = invoice_approve_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                                $(".invoiceAppendData").append('<tr id="deleteRow' + v.invoice_id + '" class="appendExcel' + j + '" style="' + getClass + '"><td>' + j + '</td><td><input type="hidden"  value="' + v.invoice_id + '" name="id[]"> <input type="text" maxlength="20" minlength="2" id="invoice_no' + v.invoice_id + '" name="invoice_no[]" class="form-control batchInvoice" value="' + v.invoice_no + '" placeholder="Invoice No"></td><td><input type="text" id="invoice_date' + v.invoice_id + '" name="invoice_date[]" readonly="readonly" placeholder="Invoice Date" class="form-control date_of_birth datepicker-dis-fdate batchInvoiceDate" value="' + invoice_date + '"></td><td><input type="text" id="invoice_due_date' + v.invoice_id + '" readonly="readonly" name="invoice_due_date[]" class="form-control date_of_birth datepicker-dis-pdate batchInvoiceDueDate invoiceTanor' + j + '" placeholder="Invoice Due Date" value="' + invoice_due_date + '"></td><td><input type="text" class="form-control subOfAmount" id="invoice_approve_amount' + j + '" name="invoice_approve_amount[]" placeholder="Invoice Approve Amount" value="' + invoice_approve_amount + '"></td><td><i class="fa fa-trash deleteTempInv" data-id="' + v.invoice_id + '" aria-hidden="true"></i></td></tr>');

                            });
                            datepickerDisFdate();
                            datepickerDisPdate();
                            return false;
                        } else if (r.status == 2)
                        {
                            $("#customFile_msg").show();
                        } else
                        {
                            ///$("#submitInvoiceMsg").show();
                            $(".invoiceAppendData").append('<tr><td colspan="5" class="error">' + r.message + '</td></tr>');

                            return false;
                        }
                    }
                });
            } else
            {
                return false;
            }  */
        }
    });

    $(document).on('click', '.deleteTempInv', function () {
        if (confirm("Are you sure? You want to delete it.")) {
            var invoice_bulk_upload_id = $(this).attr('data-id');
            var numItems = $('.deleteTempInv').length;
            var postData = ({'invoice_bulk_upload_id':invoice_bulk_upload_id, '_token': messages.token});
            jQuery.ajax({
                url: messages.delete_temp_invoice,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);

                },
                success: function (data) {
                    if (data.status == 1)
                    {
                        if(numItems==1)
                        {
                            location.reload();
                        }
                        $(".finalButton").show();
                        $("#deleteRow" + data.id).remove();
                    }
                }
            });
        } else
        {
            return false;
        }
    });