
    $(document).ready(function () {
       
         document.getElementById('invoice_approve_amount').addEventListener('input', event =>
         event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
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
    $(document).on('click', '.approveInv', function () {
        $("#moveCase").html('');
        if (confirm('Are you sure? You want to approve it.'))
        {
            var invoice_id = $(this).attr('data-id');
            var postData = ({'invoice_id': invoice_id, 'status': 8, '_token': messages.token});
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
                    $("#moveCase").html('Invoice successfully sent to  approve ');
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
            url: messages.get_user_program_supplier,
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
            url: messages.get_user_biz_anchor,
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
            alert('Please select atleast one checked');
            return false;
        }
        if (confirm('Are you sure? You want to approve it.'))
        {
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
                    if (data == 1)
                    {
                        
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
