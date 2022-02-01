  $(document).ready(function(){
    setInterval(function(){  localStorage.setItem('storageMsg',''); }, 1000);
    // var  msg = localStorage.getItem('storageMsg');
    var  msg = JSON.parse(localStorage.getItem("storageMsg") || "[]");
    if(typeof msg.text != 'undefined')
    {
        if (typeof msg.type != 'undefined' && msg.type == 'error') {
            $("#storeSuccessMsg").html("<div class='alert-danger alert' role='alert'><span><i class='fa fa-bell fa-lg' aria-hidden='true'></i></span>"+msg.text+"</div>");
        }else {
            $("#storeSuccessMsg").html("<div class='alert-success alert' role='alert'><span><i class='fa fa-bell fa-lg' aria-hidden='true'></i></span>"+msg.text+"</div>");
        }
    }
   })
 $(document).ready(function () {
       
       ///  document.getElementById('invoice_approve_amount').addEventListener('input', event =>
      ///   event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
         $("#program_bulk_id").append("<option value=''>No data found</option>");
         $("#program_bulk_id").append("<option value=''>No data found</option>");
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
 ///////////////////////For Invoice Approve////////////////////////
    $(document).on('click', '.pendingApproveInv', function () {
          
        $("#moveCase").html('');
        if (confirm('Are you sure? You want to approve it.'))
        {  $(".isloader").show(); 
            var invoice_id = $(this).attr('data-id');
            var user_id = $(this).attr('data-user');
            var amount = $(this).attr('data-amount');
            var postData = ({'amount':amount,'user_id':user_id,'invoice_id': invoice_id, 'status': 8, '_token': messages.token});
            th = this;
            jQuery.ajax({
                url: messages.update_icon_invoice_approve,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);
                },
                success: function (data) {
                     $(".isloader").hide(); 
                    if (data.eod_process) {
                        var alertmsg = '<div class="content-wrapper-msg"><div class=" alert-danger alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.message + '</div></div>';
                        parent.$("#iframeMessage").html(alertmsg);
                        return false;
                    }                            
                  else if(data==2)
                 {
                      replaceAlert('Limit Exceed', 'error');
                 }
                 else if(data==3)
                 {
                     $("#moveCase").html('(Exception Cases) Overdue');
                     $(th).parent('td').parent('tr').remove();  
                 }
                 else if(data==4)
                 {
                      $("#moveCase").html('(Exception Cases) You cannot approve invoice as customer limit has been expired.');
                     $(th).parent('td').parent('tr').remove(); 
                 }
                 else
                 {
                     $("#moveCase").html('Invoice successfully sent to  approve ');
                     $(th).parent('td').parent('tr').remove(); 
                 }
                }
            });
        } else
        {
            return false;
        }
    });
    
    ///////////////////////For Invoice Approve////////////////////////
    $(document).on('click', '.approveInv', function () {
          
        $("#moveCase").html('');
        if (confirm('Are you sure? You want to approve it.'))
        {  $(".isloader").show(); 
            var invoice_id = $(this).attr('data-id');
            var user_id = $(this).attr('data-user');
            var amount = $(this).attr('data-amount');
            var postData = ({'amount':amount,'user_id':user_id,'invoice_id': invoice_id, 'status': 8, '_token': messages.token});
            th = this;
            jQuery.ajax({
                url: messages.update_invoice_approve,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);
                },
                success: function (data) {
                     $(".isloader").hide(); 
                    if (data.eod_process) {
                        var alertmsg = '<div class="content-wrapper-msg"><div class=" alert-danger alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.message + '</div></div>';
                        parent.$("#iframeMessage").html(alertmsg);
                        return false;
                    }                            
                  else if(data==2)
                 {
                      replaceAlert('Limit Exceed', 'error');
                 }
                 else if(data==3)
                 {
                     $("#moveCase").html('(Exception Cases) Overdue');
                     $(th).parent('td').parent('tr').remove();  
                 }
                 else if(data==4)
                 {
                      $("#moveCase").html('(Exception Cases) You cannot approve invoice as customer limit has been expired.');
                     $(th).parent('td').parent('tr').remove(); 
                 }
                 else
                 {
                     $("#moveCase").html('Invoice successfully sent to  approve ');
                     $(th).parent('td').parent('tr').remove(); 
                 }
                }
            });
        } else
        {
            return false;
        }
    });
    
    
     ///////////////////////For Invoice Approve////////////////////////
    $(document).on('click', '.disburseInv', function () {
       
        $("#moveCase").html('');
        if (confirm('Are you sure? You want to disbursement queue.'))
        {
            $(".isloader").show(); 
            var invoice_id = $(this).attr('data-id');
            var postData = ({'invoice_id': invoice_id, 'status': 9, '_token': messages.token});
            th = this;
            jQuery.ajax({
                url: messages.update_invoice_approve_single_tab,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);
                },
                success: function (data) {
                      $(".isloader").hide(); 
                     $("#moveCase").html('Invoice successfully sent to  disbursement queue ');
                     $(th).parent('td').parent('tr').remove();
                   
                }
            });
        } else
        {
            return false;
        }
    });
    //////////////////// onchange anchor  id get data /////////////////

    $("#supplier_id").append("<option value=''>Select Customer</option>");
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

                            $("#supplier_id").append("<option value='" + v.user_id + "'>" + v.f_name + "</option>");

                        });
                    } else
                    {
                        $("#supplier_id").append("<option value=''>No data found</option>");

                    }


                }

            }
        });
    });

   //////////////////// onchange Business  id get Anchor /////////////////

    $("#changeAnchor").append("<option value=''>Select Anchor</option>");
    $(document).on('change', '.changeBiz', function () {
        var biz_id = $(this).val();
        $("#changeAnchor").empty();
        var postData = ({'status_id':7,'biz_id': biz_id, '_token': messages.token});
        jQuery.ajax({
            url: messages.get_biz_anchor,
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
                        $("#changeAnchor").append("<option value=''> Select Anchor </option>");
                        $(obj1).each(function (i, v) {
                           
                            $("#changeAnchor").append("<option value='" + v.anchor.anchor_id + "'>" + v.anchor.comp_name + "</option>");

                        });
                    } else
                    {
                        $("#changeAnchor").append("<option value=''>No data found</option>");

                    }


                }
               

            }
        });
    });
    
    function uploadInvoice()
    {
       $('.isloader').show();
       $("#submitInvoiceMsg").empty();
        var file  = $("#customFile")[0].files[0];
        var datafile = new FormData();
        var anchor_bulk_id  = $("#anchor_bulk_id").val();
        var program_bulk_id  = $("#program_bulk_id").val();
        var supplier_bulk_id  = $("#supplier_bulk_id").val();
        var pro_limit_hide  =  $("#pro_limit_hide").val();
        datafile.append('_token', messages.token );
        datafile.append('doc_file', file);
        datafile.append('anchor_bulk_id', anchor_bulk_id);
        datafile.append('program_bulk_id', program_bulk_id);
        datafile.append('supplier_bulk_id', supplier_bulk_id);
        datafile.append('pro_limit_hide', pro_limit_hide);
        $.ajax({
            headers: {'X-CSRF-TOKEN':  messages.token  },
            url : messages.upload_invoice_csv,
            type: "POST",
            data: datafile,
            processData: false,
            contentType: false,
            cache: false, // To unable request pages to be cached
            enctype: 'multipart/form-data',

            success: function(r){
                $(".isloader").hide();

                if(r.status==1)
                {
                     $("#submitInvoiceMsg").show();
                     $("#submitInvoiceMsg").text('Invoice Successfully uploaded');
                }
                else
                {
                     $("#submitInvoiceMsg").show();
                     $("#submitInvoiceMsg").text('Total Amount if invoice should not greater Program Limit');
                 } 
            }
        });
    }
 //////////////////// for upload invoice//////////////////////////////   
function uploadFile(app_id,id)
{
   $(".isloader").show(); 
   var file  = $(".file"+id)[0].files[0];
   var extension = file.name.split('.').pop().toLowerCase();
   var datafile = new FormData();
   datafile.append('_token', messages.token );
   datafile.append('app_id', app_id);
   datafile.append('doc_file', file);
   datafile.append('invoice_id', id);
    $.ajax({
        headers: {'X-CSRF-TOKEN':  messages.token  },
        url : messages.invoice_document_save,
        type: "POST",
        data: datafile,
        processData: false,
        contentType: false,
        cache: false, // To unable request pages to be cached
        enctype: 'multipart/form-data',
         success: function(r){
            $(".isloader").hide();
            location.reload();
        }
    });
}

    /////////// for pop up//////////////////


    //////////////////// onchange anchor  id get data /////////////////
    $(document).on('change', '.changeBulkAnchor', function () {

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

                        $("#program_bulk_id").append("<option value='" + v.program.prgm_id + "'>" + v.program.prgm_name + "</option>");
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

        var program_id = $(this).val();
        $("#supplier_bulk_id").empty();
        $("#pro_limit").empty();
        $("#pro_limit_hide").empty();
        var postData = ({'program_id': program_id, '_token': messages.token});
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

                    var obj1 = data.get_supplier;
                    var obj2 = data.limit;

                    $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  ' + obj2.anchor_limit + '');
                    $("#pro_limit_hide").val(obj2.anchor_limit);
                    $("#supplier_bulk_id").append("<option value=''>Please Select</option>");
                    $(obj1).each(function (i, v) {

                        $("#supplier_bulk_id").append("<option value='" + v.app.user.user_id + "'>" + v.app.user.f_name + "</option>");
                    });

                } else
                {

                    $("#supplier_bulk_id").append("<option value=''>No data found</option>");


                }

            }
        });
    });

//////////////////////////// for bulk approve invoice////////////////////


    $(document).on('click', '#bulkApprove', function () {
       
        $("#moveCase").html('');
        var arr = [];
        i = 0;
        th = this;
        $(".chkstatus:checked").each(function () {
            arr[i++] = $(this).val();
           
        });
     
        if (arr.length == 0) {
            replaceAlert('Please select atleast one checked', 'error');
            return false;
        }
        if (confirm('Are you sure, You want to approve it.'))
        {     $(".isloader").show(); 
            var status = $(this).attr('data-status');
            var postData = ({'invoice_id': arr, 'status': status, '_token': messages.token});
          jQuery.ajax({
                url: messages.update_bulk_invoice,
                method: 'post',
                dataType: 'json',
                data: postData,
                 error: function (xhr, status, errorThrown) {
                    alert(errorThrown);

                },
                success: function (data) {
                    $(".isloader").hide(); 
                    if (data.eod_process) {
                        var alertmsg = '<div class="content-wrapper-msg"><div class=" alert-danger alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.message + '</div></div>';
                        parent.$("#iframeMessage").html(alertmsg);
                        return false;
                    }
                   if(data.msg=="")
                    {
                    //    localStorage.setItem('storageMsg', 'Invoice successfully moved');
                        localStorage.setItem('storageMsg', JSON.stringify({text: 'Invoice successfully moved', type: 'success'}));
                       location.reload();
                    }
                    else
                    {
                       let alertMsg = 'You cannot mark the invoice ('+data.msg+') as Approved as the limit has been exceeded for the customer Or (Exception Cases)';                        
                       localStorage.setItem('storageMsg', JSON.stringify({text: alertMsg, type: 'error'}));
                       location.reload();
                    }
               }
            });
        } else
        {
            return false;
        }
    });
    
    //////////////////////////// for bulk disburse queue invoice////////////////////


    $(document).on('click', '#bulkDisburseApprove', function () {
       
        $("#moveCase").html('');
        var arr = [];
        i = 0;
        th = this;
        $(".chkstatus:checked").each(function () {
            arr[i++] = $(this).val();
        });
        if (arr.length == 0) {
            replaceAlert('Please select atleast one checked', 'error');
            return false;
        }
        if (confirm('Are you sure? You want to disbursement queue.'))
        {    $(".isloader").show(); 
            var status = $(this).attr('data-status');
            var postData = ({'invoice_id': arr, 'status': status, '_token': messages.token});
            jQuery.ajax({
                url: messages.update_disburse_bulk_invoice,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);

                },
                success: function (data) {

                     $(".isloader").hide(); 
                    if (data.eod_process) {
                        var alertmsg = '<div class="content-wrapper-msg"><div class=" alert-danger alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.message + '</div></div>';
                        parent.$("#iframeMessage").html(alertmsg);
                        return false;
                    }
                    if(data.msg=="")
                    {
                    //    localStorage.setItem('storageMsg', 'Invoice successfully moved');
                        localStorage.setItem('storageMsg', JSON.stringify({text: 'Invoice successfully moved', type: 'success'}));
                       location.reload();
                    }

              }
            });
        } else
        {
            return false;
        }
    });

///////////////////////////////////////// change invoice amount////////////////
    $(document).on('click', '.changeInvoiceAmount', function () {

        var limit = $(this).attr('data-limit');
        var approveAmount = $(this).attr('data-approve').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        var amount = $(this).attr('data-amount').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        var invoiceId = $(this).attr('data-id');
        $("#invoice_id").val(invoiceId);
        $("#invoice_amount").val(amount);
        $("#invoice_approve_amount").val(approveAmount);

    });

///////////////////////////////////////// change invoice amount////////////////
    $(document).on('click', '#UpdateInvoiceAmount', function () {

        var amount = parseFloat($("#invoice_amount").val().replace(/,/g, ''));
        var approveAmount = parseFloat($("#invoice_approve_amount").val().replace(/,/g, ''));
        if (approveAmount > amount)
        {
            $(".model7msg").show();
            $(".model7msg").html('Invoice Approve Amount should not greater amount');
            return false;
        } else
        {
            $(".model7msg").hide();
            return true;
        }
    });
///////////////////////////////////////// change invoice amount////////////////
    $(document).on('click', '.changeInvoiceProcessingFee', function () {

        // var limit = $(this).attr('data-limit');
        // var approveAmount = $(this).attr('data-approve').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        // var amount = $(this).attr('data-amount').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        var invoiceId = $(this).attr('data-id');
        $("#invoice_id").val(invoiceId);
        // $("#invoice_amount").val(amount);
        // $("#invoice_approve_amount").val(approveAmount);

    });

///////////////////////////////////////// change invoice amount////////////////
    $(document).on('click', '#UpdateInvoiceChrg', function () {

        // var amount = parseFloat($("#invoice_amount").val().replace(/,/g, ''));
        // var approveAmount = parseFloat($("#invoice_approve_amount").val().replace(/,/g, ''));
        // if (approveAmount > amount)
        // {
        //     $(".model7msg").show();
        //     $(".model7msg").html('Invoice Approve Amount should not greater amount');
        //     return false;
        // } else
        // {
        //     $(".model7msg").hide();
        //     return true;
        // }
    });
///////////////////////////////////////// change invoice tenor////////////////
    $(document).on('click', '.changeInvoiceTenor', function () {

        var tenor = $(this).attr('data-tenor').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        var offertenor = $(this).attr('data-offertenor').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        console.log(tenor);
        var invoiceId = $(this).attr('data-id');
        $("#tenor_invoice_id").val(invoiceId);
        $("#invoice_tenor").val(tenor);
        $("#offer_invoice_tenor").val(offertenor);

    });

///////////////////////////////////////// change invoice tenor////////////////
    $(document).on('click', '#UpdateInvoiceTenor', function () {

        var tenor = parseFloat($("#invoice_tenor").val().replace(/,/g, ''));
        if (tenor > 365)
        {
            $(".model7msg").show();
            $(".model7msg").html('Invoice Tenor should not greater 365 days');
            return false;
        } else
        {
            $(".model7msg").hide();
            return true;
        }
    });
    
    
     ///////////////////////For Invoice Approve////////////////////////
    $(document).on('change', '.approveInv1', function () {
       
        var status = $(this).val();
        $("#moveCase").html('');
        if (status == 0)
        {
            return false;
        }
        else if (status == 7)
        {
            var st = "Pending";
        }
        else if (status == 8)
        {
            var st = "Approve";
        }
        else if (status == 9)
        {
            var st = "Disbursement Queue";
        }
       else if (status == 14)
        {
            var st = "Reject";
        }
        if (confirm('Are you sure? You want to ' + st + ' it.'))
        {
             $(".isloader").show(); 
            var invoice_id = $(this).attr('data-id');
             var user_id = $(this).attr('data-user');
            var amount = $(this).attr('data-amount');
            var postData = ({'amount':amount,'user_id':user_id,'invoice_id': invoice_id, 'status': status, '_token': messages.token});
            var $tr = $(this).closest('tr');
            jQuery.ajax({
                url: messages.update_invoice_approve,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);
                },
                success: function (data) {
                   $(".isloader").hide(); 
                    if (data.eod_process) {
                        var alertmsg = '<div class="content-wrapper-msg"><div class=" alert-danger alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.message + '</div></div>';
                        parent.$("#iframeMessage").html(alertmsg);
                        return false;
                    }
                    else if(data==2)
                    {
                         replaceAlert('Limit Exceed', 'error');
                    }
                    else if(data==3)
                    {
                        $("#moveCase").html('(Exception Cases) Overdue');
                        $(th).parent('td').parent('tr').remove();  
                    }
                    else if(data==4)
                    {
                         $("#moveCase").html('(Exception Cases) You cannot approve invoice as customer limit has been expired.');
                        $(th).parent('td').parent('tr').remove(); 
                    }
                    else
                    {
                        $("#moveCase").html('Invoice successfully sent to  '+st+' ');
                        $tr.remove();
                    }
                    

                }
            });
        } else
        {
            return false;
        }
    });
    
    
       ///////////////////////For Invoice Approve////////////////////////
    $(document).on('change', '.approveInv2', function () {
       
        var status = $(this).val();
        $("#moveCase").html('');
        if (status == 0)
        {
            return false;
        }
        else if (status == 7)
        {
            var st = "Pending";
        }
        else if (status == 8)
        {
            var st = "Approve";
        }
        else if (status == 9)
        {
            var st = "Disbursement Queue";
        }
       else if (status == 14)
        {
            var st = "Reject";
        }
        if (confirm('Are you sure? You want to ' + st + ' it.'))
        {
             $(".isloader").show(); 
            var invoice_id = $(this).attr('data-id');
             var user_id = $(this).attr('data-user');
            var amount = $(this).attr('data-amount');
            var postData = ({'amount':amount,'user_id':user_id,'invoice_id': invoice_id, 'status': status, '_token': messages.token});
            var $tr = $(this).closest('tr');
            jQuery.ajax({
                url: messages.update_invoice_approve_tab,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);
                },
                success: function (data) {
                   $(".isloader").hide(); 
                    if (data.eod_process) {
                        var alertmsg = '<div class="content-wrapper-msg"><div class=" alert-danger alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.message + '</div></div>';
                        parent.$("#iframeMessage").html(alertmsg);
                        return false;
                    }
                    else if(data==2)
                    {
                         replaceAlert('Limit Exceed', 'error');
                    }
                    else if(data==3)
                    {
                        $("#moveCase").html('(Exception Cases) Overdue');
                        $(th).parent('td').parent('tr').remove();  
                    }
                    else if(data==4)
                    {
                         $("#moveCase").html('(Exception Cases) You cannot approve invoice as customer limit has been expired.');
                        $(th).parent('td').parent('tr').remove(); 
                    }
                    else
                    {
                        $("#moveCase").html('Invoice successfully sent to  '+st+' ');
                        $tr.remove();
                    }
                    

                }
            });
        } else
        {
            return false;
        }
    });
    
    
       ///////////////////////For Invoice Approve////////////////////////
    $(document).on('change', '.approveInv3', function () {
       
        var status = $(this).val();
        $("#moveCase").html('');
        if (status == 0)
        {
            return false;
        }
        else if (status == 7)
        {
            var st = "Pending";
        }
        else if (status == 8)
        {
            var st = "Approve";
        }
        else if (status == 9)
        {
            var st = "Disbursement Queue";
        }
       else if (status == 14)
        {
            var st = "Reject";
        }
        if (confirm('Are you sure? You want to ' + st + ' it.'))
        {
             $(".isloader").show(); 
            var invoice_id = $(this).attr('data-id');
             var user_id = $(this).attr('data-user');
            var amount = $(this).attr('data-amount');
            var postData = ({'amount':amount,'user_id':user_id,'invoice_id': invoice_id, 'status': status, '_token': messages.token});
            var $tr = $(this).closest('tr');
            jQuery.ajax({
                url: messages.update_invoice_disb_que_tab,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);
                },
                success: function (data) {
                   $(".isloader").hide(); 
                    if (data.eod_process) {
                        var alertmsg = '<div class="content-wrapper-msg"><div class=" alert-danger alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.message + '</div></div>';
                        parent.$("#iframeMessage").html(alertmsg);
                        return false;
                    }
                    else if(data==2)
                    {
                         replaceAlert('Limit Exceed', 'error');
                    }
                    else if(data==3)
                    {
                        $("#moveCase").html('(Exception Cases) Overdue');
                        $(th).parent('td').parent('tr').remove();  
                    }
                    else if(data==4)
                    {
                         $("#moveCase").html('(Exception Cases) You cannot approve invoice as customer limit has been expired.');
                        $(th).parent('td').parent('tr').remove(); 
                    }
                    else
                    {
                        $("#moveCase").html('Invoice successfully sent to  '+st+' ');
                        $tr.remove();
                    }
                    

                }
            });
        } else
        {
            return false;
        }
    });
    
    
       ///////////////////////For Invoice Approve////////////////////////
    $(document).on('change', '.approveInv4', function () {
       
        var status = $(this).val();
        $("#moveCase").html('');
        if (status == 0)
        {
            return false;
        }
        else if (status == 7)
        {
            var st = "Pending";
        }
        else if (status == 8)
        {
            var st = "Approve";
        }
        else if (status == 9)
        {
            var st = "Disbursement Queue";
        }
       else if (status == 14)
        {
            var st = "Reject";
        }
        if (confirm('Are you sure? You want to ' + st + ' it.'))
        {
             $(".isloader").show(); 
            var invoice_id = $(this).attr('data-id');
             var user_id = $(this).attr('data-user');
            var amount = $(this).attr('data-amount');
            var postData = ({'amount':amount,'user_id':user_id,'invoice_id': invoice_id, 'status': status, '_token': messages.token});
            var $tr = $(this).closest('tr');
            jQuery.ajax({
                url: messages.update_invoice_failed_disb_tab,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);
                },
                success: function (data) {
                   $(".isloader").hide(); 
                    if (data.eod_process) {
                        var alertmsg = '<div class="content-wrapper-msg"><div class=" alert-danger alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.message + '</div></div>';
                        parent.$("#iframeMessage").html(alertmsg);
                        return false;
                    }
                    else if(data==2)
                    {
                         replaceAlert('Limit Exceed', 'error');
                    }
                    else if(data==3)
                    {
                        $("#moveCase").html('(Exception Cases) Overdue');
                        $(th).parent('td').parent('tr').remove();  
                    }
                    else if(data==4)
                    {
                         $("#moveCase").html('(Exception Cases) You cannot approve invoice as customer limit has been expired.');
                        $(th).parent('td').parent('tr').remove(); 
                    }
                    else
                    {
                        $("#moveCase").html('Invoice successfully sent to  '+st+' ');
                        $tr.remove();
                    }
                    

                }
            });
        } else
        {
            return false;
        }
    });
    
    
       ///////////////////////For Invoice Approve////////////////////////
    $(document).on('change', '.approveInv5', function () {
       
        var status = $(this).val();
        $("#moveCase").html('');
        if (status == 0)
        {
            return false;
        }
        else if (status == 7)
        {
            var st = "Pending";
        }
        else if (status == 8)
        {
            var st = "Approve";
        }
        else if (status == 9)
        {
            var st = "Disbursement Queue";
        }
       else if (status == 14)
        {
            var st = "Reject";
        }
        if (confirm('Are you sure? You want to ' + st + ' it.'))
        {
             $(".isloader").show(); 
            var invoice_id = $(this).attr('data-id');
             var user_id = $(this).attr('data-user');
            var amount = $(this).attr('data-amount');
            var postData = ({'amount':amount,'user_id':user_id,'invoice_id': invoice_id, 'status': status, '_token': messages.token});
            var $tr = $(this).closest('tr');
            jQuery.ajax({
                url: messages.update_invoice_reject_tab,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);
                },
                success: function (data) {
                   $(".isloader").hide(); 
                    if (data.eod_process) {
                        var alertmsg = '<div class="content-wrapper-msg"><div class=" alert-danger alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.message + '</div></div>';
                        parent.$("#iframeMessage").html(alertmsg);
                        return false;
                    }
                    else if(data==2)
                    {
                         replaceAlert('Limit Exceed', 'error');
                    }
                    else if(data==3)
                    {
                        $("#moveCase").html('(Exception Cases) Overdue');
                        $(th).parent('td').parent('tr').remove();  
                    }
                    else if(data==4)
                    {
                         $("#moveCase").html('(Exception Cases) You cannot approve invoice as customer limit has been expired.');
                        $(th).parent('td').parent('tr').remove(); 
                    }
                    else
                    {
                        $("#moveCase").html('Invoice successfully sent to  '+st+' ');
                        $tr.remove();
                    }
                    

                }
            });
        } else
        {
            return false;
        }
    });
    
         ///////////////////////For Invoice Approve////////////////////////
    $(document).on('change', '.approveInv6', function () {
       
        var status = $(this).val();
        $("#moveCase").html('');
        if (status == 0)
        {
            return false;
        }
        else if (status == 7)
        {
            var st = "Pending";
        }
        else if (status == 8)
        {
            var st = "Approve";
        }
        else if (status == 9)
        {
            var st = "Disbursement Queue";
        }
       else if (status == 14)
        {
            var st = "Reject";
        }
        if (confirm('Are you sure? You want to ' + st + ' it.'))
        {
             $(".isloader").show(); 
            var invoice_id = $(this).attr('data-id');
             var user_id = $(this).attr('data-user');
            var amount = $(this).attr('data-amount');
            var postData = ({'amount':amount,'user_id':user_id,'invoice_id': invoice_id, 'status': status, '_token': messages.token});
            var $tr = $(this).closest('tr');
            jQuery.ajax({
                url: messages.update_invoice_exception_tab,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);
                },
                success: function (data) {
                   $(".isloader").hide(); 
                    if (data.eod_process) {
                        var alertmsg = '<div class="content-wrapper-msg"><div class=" alert-danger alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.message + '</div></div>';
                        parent.$("#iframeMessage").html(alertmsg);
                        return false;
                    }
                    else if(data==2)
                    {
                         replaceAlert('Limit Exceed', 'error');
                    }
                    else if(data==3)
                    {
                        $("#moveCase").html('(Exception Cases) Overdue');
                        $(th).parent('td').parent('tr').remove();  
                    }
                    else if(data==4)
                    {
                         $("#moveCase").html('(Exception Cases) You cannot approve invoice as customer limit has been expired.');
                        $(th).parent('td').parent('tr').remove(); 
                    }
                    else
                    {
                        $("#moveCase").html('Invoice successfully sent to  '+st+' ');
                        $tr.remove();
                    }
                    

                }
            });
        } else
        {
            return false;
        }
    });