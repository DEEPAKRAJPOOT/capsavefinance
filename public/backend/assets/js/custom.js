function ValidatePAN() {
        var Obj = document.getElementById("pan_no");
        if (Obj.value != "") {
            ObjVal = Obj.value;
            var panPat = /^([a-zA-Z]{5})(\d{4})([a-zA-Z]{1})$/;
            if (ObjVal.search(panPat) == -1) {
                alert("Invalid PAN No");
                // Obj.focus();
                // return false;
            }
        }
    }


    $(document).ready(function () {
        var message = $( '.message' );
            if ( message.length ) {
            setTimeout( function() {
            message.fadeOut( 'slow' );
            }, 5000 );
            }

            $.extend($.validator.messages, {
                required: "This field is required.",
                remote: "Please fix this field.",
                email: "Please enter a valid email address.",
                url: "Please enter a valid URL.",
                date: "Please enter a valid date.",
                dateISO: "Please enter a valid date (ISO).",
                number: "Please enter a valid number.",
                digits: "Please enter only digits.",
                creditcard: "Please enter a valid credit card number.",
                equalTo: "Please enter the same value again.",
                accept: "Please enter a value with a valid extension.",
                maxlength: $.validator.format("Please enter no more than {0} characters."),
                minlength: $.validator.format("Please enter at least {0} characters."),
                rangelength: $.validator.format("Please enter a value between {0} and {1} characters long."),
                range: $.validator.format("Please enter a value between {0} and {1}."),
                max: $.validator.format("Please enter a value less than or equal to {0}."),
                min: $.validator.format("Please enter a value greater than or equal to {0}.")
            });

            $("#signupForm").validate({
                ignore: [],
              rules:{
                c_street:{
                  required: function(element) {
                		if ($("#chkAddress").prop('checked') == true) {
                			return true;
                		} else {
                			return false;
                		}
                	}
                },
                c_city:{
                  required: function(element) {
                		if ($("#chkAddress").prop('checked') == true) {
                			return true;
                		} else {
                			return false;
                		}
                	}
                },
                c_state:{
                  required: function(element) {
                		if ($("#chkAddress").prop('checked') == true) {
                			return true;
                		} else {
                			return false;
                		}
                	}
                },
                c_pin:{
                  required: function(element) {
                		if ($("#chkAddress").prop('checked') == true) {
                			return true;
                		} else {
                			return false;
                		}
                	}
                },
              }
            });

            $('#signupForm1').validate({
                rules: {
                    recommended_limit: {
                       required: true,
                       digits: true,
                       min :1
                    },
                    discount_rate: {
                        required: true,
                        number:true,
                        min :1

                    },
                    discount_percentage: {
                        required: true,
                        number:true,
                        min :1

                    }

                },
                messages: {

                }
            });

            $("#directorForm").validate();

        $("#txtPO").change(function() {
            if($(this).val()>0) {
                $("#inv01").css("display", "block");
                $("#inv02").css("display", "block");
                $("#inv03").css("display", "block");
            }
            else {
                $("#inv01").css("display", "none");
                $("#inv02").css("display", "none");
                $("#inv03").css("display", "none");
            }
        });
        var set_date = new Date()
       // document.getElementById('issueDate_label').innerHTML=set_date.getFullYear()+'-'+parseInt(set_date.getMonth()+1)+'-'+set_date.getDate()
      //  document.getElementById('issueDate').value = set_date.getFullYear()+'-'+parseInt(set_date.getMonth()+1)+'-'+set_date.getDate()
        $('#discountRate').on('keyup',function(){
            document.getElementById('paymentDue').value = document.getElementById('totalValue').value - document.getElementById('discountAmount').value
        })

        $( "#supplier").change(function(){
            var s = []
            var x = {}


             x.emailId = ""
             x.id = ""
             s.push(x)


             console.log(s)
            for( var i in s){
                if(s[i].id == $( "#supplier option:selected" ).val()) {
            document.getElementById('supplierName').value =  s[i].emailId
            }}
        })



        $("#tblProduct").on("click", ".btnDeleteProduct", function() {
            $(this).parents("tr").remove();
        });


        var varTotal = $("#txtTotal").html()/1;

        var varGTotal = (varTotal * ($("#txtTax").html() / 1)/100);

        varGTotal = varGTotal / 1;

        $("#divTax").html(varGTotal.toFixed(2));

        $("#txtGTotal").html((varGTotal + varTotal).toFixed(2));




        $("#tblProduct").on("blur", ".prodQty", function() {

            var tot = 0;

            $(this).parent("td").next("td").next("td").find(".prodTot").html(($(this).val() * $(this).parent("td").next("td").find(".prodPrice").val()).toFixed(2));


            $(".prodTot").each(function(index) {
                tot = tot + $(this).html() / 1;
            });

            $("#txtTotal").html(tot.toFixed(2));

            var varTotal = $("#txtTotal").html()/1;

            var varGTotal = (varTotal * ($("#txtTax").html() / 1)/100);

            varGTotal = varGTotal / 1;

            $("#divTax").html(varGTotal.toFixed(2));

            $("#txtGTotal").html((varGTotal + varTotal).toFixed(2));

        });



        $(".notifications-menu").click(function() {
            $.get( "/removeNotifications", function( data ) {
                $("#notiCount").remove();
            });
        });



        $("#totalValue").blur(function() {
            $("#txtProductPrice").val($(this).val());
        });



    var userform = $("#userform");
    userform.validate({
        ignore: [],
        rules : {
            emailId:{
                email :true
            },
            password : {
                minlength : 5
            },
            confirmpassword : {
                minlength : 5,
                equalTo : "#password"
            }
        },
        messages: {
           confirmpassword: "Enter Confirm Password Same as Password"
        }
         });


         var selectPlaceholder = "Select an option";
         $( ".select2-single-option, .select2-multiple-option" ).select2( {
             allowClear: true,
             placeholder: selectPlaceholder,
             width: null,
             containerCssClass: ':all:'
         } );

})




$(document).ready(function() {

    // $('.datepicker').datepicker({
    //     format: 'yyyy-mm-dd',
    //     todayHighlight: true,
    //     changeMonth: true,
    //     changeYear: true,

    // });


 $("#owl-demo").owlCarousel({

      navigation : false, // Show next and prev buttons
      slideSpeed : 300,
      paginationSpeed : 400,
      singleItem:true

      // "singleItem:true" is a shortcut for:
      // items : 1,
      // itemsDesktop : false,
      // itemsDesktopSmall : false,
      // itemsTablet: false,
      // itemsMobile : false

  });

  $("#owl-demo3").owlCarousel({

      navigation : false, // Show next and prev buttons
      slideSpeed : 300,
      paginationSpeed : 400,
      singleItem:true

      // "singleItem:true" is a shortcut for:
      // items : 1,
      // itemsDesktop : false,
      // itemsDesktopSmall : false,
      // itemsTablet: false,
      // itemsMobile : false

  });

 var owl = $("#owl-demo2");
  owl.owlCarousel({
     autoPlay: 3000,
      itemsCustom : [
        [0, 2],
        [450, 3],
        [600, 3],
        [700, 3],
        [1000, 4],
        [1200, 5],
        [1400, 5],
        [1600, 6]
      ],
      navigation : false,
  });

  ///waoo js
	 new WOW().init();
	 //grid

	$('#myTabs a').click(function (e) {
	  e.preventDefault()
	  $(this).tab('show')
	})


	$('.nav.nav-tabs li a.all').on('click',function(){
	//	alert('alll')
		$('.tab-content').find('.tab-pane').addClass('active wow slideInDown')
	})

	$('.nav.nav-tabs li').on('click',function(){
		$('.nav.nav-tabs li ').removeClass('active2 active')
  	$(this).addClass('active2')
    var _this = $(this)
    getSupplierRecord(_this)
	})
 // get supplier information
      function getSupplierRecord(obj){
         var _this = $(obj)
         var data_val = _this.find('a').attr('data-type');
         if(data_val == 'DOC'){
           $(".loader-ring").show();
         }
         var va  = $('input[name=var_id]').val()
        //run ajax
        $.ajax({
                type: "post",
                url: '/ajaxroute/get-data',
                data: {supplier_id : va, data_type : data_val},
                dataType: "json",
                success: function (response) {
                    if(response.success == '1'){
                      $(".loader-ring").hide();
                        $("#Suppliers_18").html(response.Data)
                    }
                },
                async:true
            });
        return false;
      }
	$('#myModal').on('shown.bs.modal', function () {
  		$('#myInput').focus()
  })

  "use strict";

  $("#btnAddMore").click(function() {

    $('#addMore').append($("#divOwner").html());


    for(var i = 0; i < $(".txtPan").length; i = i + 1) {

        $(".txtPan").eq(i).attr("name", $(".txtPan").eq(i).attr("name").replace('Pan-0', 'Pan-'+i));
    }

    for(var i = 0; i < $(".txtAddress").length; i = i + 1) {

        $(".txtAddress").eq(i).attr("name", $(".txtAddress").eq(i).attr("name").replace('Address-0', 'Address-'+i));
    }

    for(var i = 0; i < $(".txtPhoto").length; i = i + 1) {
        $(".txtPhoto").eq(i).attr("name", $(".txtPhoto").eq(i).attr("name").replace('Photo-0', 'Photo-'+i));
    }
});

$(".btnDelete").click(function() {


    var varRow = $(this);

    var ownerID = "";

    ownerID = $(this).attr("id").split("___");

    $.get("/admin/deleteowner?pan=" + ownerID[0] + "&userId=" + ownerID[1], function (response) {

        console.log(response);
        varRow.parent("td").parent("tr").hide();

    }, "json");


    $(".btnViewTansaction").click(function () {
      var Data=$(this).attr("id").split("###");


                  $("#txid").text(Data[0]);
                  $("#timestamp").text(Data[1]);
                  $("#nonce").text(Data[2]);
                  $("#cert").text(Data[3]);
                  $("#signature").text(Data[4]);
                  //$("#chainId").val(data.chaincodeID);
                  //alert( data.chaincodeID );
                  $('#myModal').modal('show');


  });

  $(".btnFindTx").click(function () {
   var url = "/getTxById?txid="+$("#transacId").val();


                  $.get( url, function( data ) {
                      console.log("TX-",data.id)
                      if (data.id&&data.id.length>0){
                  $("#txid").text(data.id);
                  $("#timestamp").text(data.timeStamp);
                  $("#nonce").text(data.nonce);
                  $("#cert").text(data.certificate);
                  $("#signature").text(data.signature);
                  //$("#chainId").val(data.chaincodeID);
                  //alert( data.chaincodeID );


                  $('#myModal').modal('show');
                      }else{
                          alert ("No such transaction recorded.")
                      }
                  });
  });

  $(".btnFindBlock").click(function () {


      var url = "/getBlockByNumber?number="+$("#blockNum").val();


                  $.get( url, function( data ) {
                      console.log("blockNumber",data.dataHash);
                      if (data.dataHash&&data.dataHash.length>0){


                       var output='';
                  for (var i=0;i<data.transactions.length;i++){
                          output+='<span style="color:darkolivegreen;font-weight:bold">Txn ID</span><br><span id="blockTXtxid"  name="blockTXtxid" style="overflow-wrap: break-word;">'+data.transactions[i].id+'</span><br>                            <span style="color:darkolivegreen;font-weight:bold">Timestamp</span><br>                            <span id="blockTXtimestamp"  name="blockTXtimestamp" style="overflow-wrap: break-word;">'+data.transactions[i].timeStamp+'</span><br>                            <span style="color:darkolivegreen;font-weight:bold">Nonce</span><br>                            <span id="blockTXnonce" name="blockTXnonce" style="overflow-wrap: break-word;">'+data.transactions[i].nonce+'</span><br>                            <span style="color:darkolivegreen;font-weight:bold">Cert</span><br>                            <span id="blockTXcert" name="blockTXcert" style="overflow-wrap: break-word;">'+data.transactions[i].certificate+'</span><br>                            <span style="color:darkolivegreen;font-weight:bold">Signature</span><br>                            <span id="blockTXsignature" name="blockTXsignature" style="overflow-wrap: break-word;">'+data.transactions[i].signature+'</span><br>'

                      }
                  $("#changeHere").html(output);
                  $("#number").text(data.number);
                  $("#dataHash").text(data.dataHash);
                  $("#previousHash").text(data.previous_hash);
                  $("#txCount").text(data.transactionNum);
                  //$("#chainId").val(data.chaincodeID);
                  //alert( data.chaincodeID );
                  $('#myModal2').modal('show');
                  }else{
                      alert("No such block recorded yet");
                  }
                  });
  });
  $(".btnViewBlock").click(function () {
      var Data=$(this).attr("id").split("###");


                  $("#number").text(Data[0]);
                  $("#dataHash").text(Data[1]);
                  $("#previousHash").text(Data[2]);
                  $("#txCount").text(Data[3]);
                  var output='';



                      var url = "/getBlockByNumber?number="+Data[0];


                  $.get( url, function( data ) {
                      console.log("blockNumber",data);
                  for (var i=0;i<data.transactions.length;i++){
                          output+='<span style="color:darkolivegreen;font-weight:bold">Txn ID</span><br><span id="blockTXtxid"  name="blockTXtxid" style="overflow-wrap: break-word;">'+data.transactions[i].id+'</span><br>                            <span style="color:darkolivegreen;font-weight:bold">Timestamp</span><br>                            <span id="blockTXtimestamp"  name="blockTXtimestamp" style="overflow-wrap: break-word;">'+data.transactions[i].timeStamp+'</span><br>                            <span style="color:darkolivegreen;font-weight:bold">Nonce</span><br>                            <span id="blockTXnonce" name="blockTXnonce" style="overflow-wrap: break-word;">'+data.transactions[i].nonce+'</span><br>                            <span style="color:darkolivegreen;font-weight:bold">Cert</span><br>                            <span id="blockTXcert" name="blockTXcert" style="overflow-wrap: break-word;">'+data.transactions[i].certificate+'</span><br>                            <span style="color:darkolivegreen;font-weight:bold">Signature</span><br>                            <span id="blockTXsignature" name="blockTXsignature" style="overflow-wrap: break-word;">'+data.transactions[i].signature+'</span><br>'

                      }
                      document.getElementById("changeHere").innerHTML=output;
                //  $("changeHere").html(output);
                  //$("#chainId").val(data.chaincodeID);
                  //alert( data.chaincodeID );
                  $('#myModal2').modal('show');
                  });
  });

});

 // notification
//  setTimeout(function () {

//     toastr.options = {
//         closeButton: true,
//         progressBar: true,
//         showMethod: 'slideDown',
//         timeOut: 4000
// //                        positionClass: "toast-top-left"
//     };
//     toastr.success('Manage PO', 'Welcome to SofoSupply');

// }, 1300);




// $(".notifications-menu").click(function() {
//     $.get( "/removeNotifications", function( data ) {
//         $("#notiCount").remove();
//     });
// });



$("#viewOffer").click(function() {

    $(this).parent("td").parent("tr").next("tr").slideToggle();


});

$("#txtPO").change(function() {
    if($(this).val()>0) {
        $("#inv01").css("display", "block");
        $("#inv02").css("display", "block");
        $("#inv03").css("display", "block");
    }
    else {
        $("#inv01").css("display", "none");
        $("#inv02").css("display", "none");
        $("#inv03").css("display", "none");
    }
});


$("#tblProduct").on("click", ".btnDeleteProduct", function() {
    $(this).parents("tr").remove();
});


var varTotal = $("#txtTotal").html()/1;

var varGTotal = (varTotal * ($("#txtTax").html() / 1)/100);

varGTotal = varGTotal / 1;

$("#divTax").html(varGTotal.toFixed(2));

$("#txtGTotal").html((varGTotal + varTotal).toFixed(2));




$("#tblProduct").on("blur", ".prodQty", function() {

    var tot = 0;

    $(this).parent("td").next("td").next("td").find(".prodTot").html(($(this).val() * $(this).parent("td").next("td").find(".prodPrice").val()).toFixed(2));


    $(".prodTot").each(function(index) {
        tot = tot + $(this).html() / 1;
    });

    $("#txtTotal").html(tot.toFixed(2));

    var varTotal = $("#txtTotal").html()/1;

    var varGTotal = (varTotal * ($("#txtTax").html() / 1)/100);

    varGTotal = varGTotal / 1;

    $("#divTax").html(varGTotal.toFixed(2));

    $("#txtGTotal").html((varGTotal + varTotal).toFixed(2));

});


$(".notifications-menu").click(function() {
    $.get( "/removeNotifications", function( data ) {
        $("#notiCount").remove();
    });
});



$("#totalValue").blur(function() {
    $("#txtProductPrice").val($(this).val());
});


// $("#invoiceDT").datepicker({});


$(document).on('click', '.active2 #15', function (e) {
    if($(".suppliers-content #22").length > 0){
       $('body').find(".suppliers-content #22").trigger('click');
    }

    });







 });
 function getAssessmentData(obj){
    $(".loader-ring").show();
    var supplier_id = $("input[name=var_id]").val();
    var fileId = $(obj).attr('data-file-id')
    $.ajax({
            type: "post",
            url: '/ajaxroute/get-bank-assessments',
            data: {supplier_id : supplier_id, fileId, fileId},
            dataType: "json",
            cache: false,
            success: function (response) {
                if(response.success == '1'){

                        if ($('.toast').length > 0)
                       {
                           $('.toast').remove();
                       }
                        toastr.success(response.data);

                        $(".suppliers-content").find("#30").trigger('click');

                }else if(response.failure == '0'){

                        if ($('.toast').length > 0)
                       {
                           $('.toast').remove();
                       }
                        toastr.error(response.data);

                }else{
                    return false
                }
                var file_url = response.fileUrl;
                var download_html = '<div class="download-assessment"><a target="_blank" href="/admin/supplierdocs?file='+file_url+'&supplier_id='+supplier_id+'">Download Assessment</a></div>'
                $(obj).closest('.image-uploader').append(download_html)
                $(obj).closest('.upload-icon').remove();

                $(".loader-ring").hide();
            },
            async:true
        });
    return false;

 }


 function getGSTTransaction(obj){
    var _this = $(obj)
    $(".loader-ring").show();
    var gstNumber = $('input[name=GST_No]').val()
    var supplier_id = $("input[name=var_id]").val()
    $.ajax({
            type: "post",
            url: '/ajaxroute/open-gst-transaction-modal',
            data: {supplier_id : supplier_id, gst_number: gstNumber},
            dataType: "json",
            success: function (response) {
                $(".loader-ring").hide();
                $("#GSTTransactionModal").find(".modal-dialog").removeClass('modal-lg')
                $("#GSTTransactionModal").find('.modal-content').html(response.Data)
                $("#GSTTransactionModal").modal('show');

            },
            async:true
        });
    return false;


    }


    function pullGSTTransaction(obj){

        var modalform = $("#gst_transaction_form");
        modalform.validate({
            rules: {
                gst_number: {
                          required: true,
                      },
                gst_username: {
                          required: true,
                      },
                gst_password: {
                          required: true,
                      },
                },
            messages: {
                      }
             });

        var IsValid= modalform.valid();

        if(IsValid){

        var form = modalform.get(0);
        var nfd = new FormData(form);
        $.ajax({
            type: "POST",
                url: '/ajaxroute/pull-gst-transaction',
                dataType : 'json',
                data: nfd,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function(){
                    modalform.css("opacity",".5");
                },
                success: function (res) {
                    $(".loader-ring").hide();
                    if (res.Result == 'Ok') {
                        $("#GSTTransactionModal").modal('hide');
                        if ($('.toast').length > 0)
                           {
                               $('.toast').remove();
                           }
                       toastr.success(res.Message);
                       modalform.css("opacity","");
                       $('.pull-gst').hide();
                       var downloadLink = "/admin/supplierdocs?file="+res.File+"&supplier_id="+$('input[name=var_id]').val()
                       $(".download-gst-report").attr('href',downloadLink)
                       $(".download-gst-report").show()
                       //display download button

                    //    $("#GSTTransactionModal").find(".modal-dialog").addClass('modal-lg')
                    //    $('.modal-footer .btn-sm').css("display","none");
                    //    $("#GSTTransactionModal").find(".modal-body").html(res.Data)

                   } else if (res.Result == 'Failure') {
                           if ($('.toast').length > 0)
                           {
                               $('.toast').remove();
                           }
                           toastr.error(res.Message);

                       }
                },
                async:true
            });

    return false;
}
    }

    $("#checkAll").click(function(){
        $('body').find('input:checkbox').prop('checked', 'checked');
    });
   $("#uncheckAll").click(function(){
        $('body').find('input:checkbox').prop('checked', false);
    });


    $(".blue-btn").on('click', function(){
            var _this = $(this);
            var values = _this.val();
            if(values){

            }
    });

    $('.ms-box-title').click(function () {
        var hidden = $(".main-filter-dv").is(":hidden");
        $('.main-filter-dv').slideToggle();
        if (hidden === true) {
            $('.filter-open').hide();
            $('.filter-close').show();
        } else {
            $('.filter-open').show();
            $('.filter-close').hide();
        }
    });
    var filterForm = $("#filter-form")
    var lenderSelect = $('#filter-lender');

    function getOfferList(obj, type){
        var values = $(obj).val();
        $.ajax({
                type: "POST",
                url: '/ajaxroute/get-offer/'+type,
                data: {var_id : values},
                dataType: "json",                 
                beforeSend: function(){
                    $('.submitFilter').attr("disabled","disabled");
                    filterForm.css("opacity",".5");

                },
                success: function (res) {
                    $(".loader-ring").hide();
                    filterForm.css("opacity","");
                    $(".submitFilter").removeAttr("disabled");
                    lenderSelect.empty();
                   
                    var option = new Option("Please Select", "", true, true);
                    lenderSelect.append(option);

                    if (res.Result == 'Ok') {
                        
                        if ($('.toast').length > 0)
                           {
                               $('.toast').remove();
                           }
                       toastr.success(res.Message);
                       
                       $.each(res.Data, function (i, item) {
                            var option = new Option(item.emailId, item.id, true, true);
                            lenderSelect.append(option);
                                
                        });
                        lenderSelect.val('')
                    //   lenderSelect.select2("val", null).trigger('change');
                    

                    } else if (res.Result == 'Failure') {
                           if ($('.toast').length > 0)
                           {
                               $('.toast').remove();
                           }
                           toastr.error(res.Message);
                           
                       }
                },
                async:true
            });

    return false;
    }   

    $(document).ready(function() {
        var tab = $("#tab-hidden").val();
        //alert(tab);
        if(tab) {
            var subtab = $("#subtab-hidden").val();            
            if(tab=="cam") {
                $(".supplier-tabs-left li").removeClass('active2');
                $(".supplier-tabs-left li:eq(5)").addClass('active2');
                $(".supplier-tabs-left li:eq(5) a").trigger('click');   
                if(subtab=='gstLedger') {
                    $("#Suppliers_15.tabbed div").removeClass('active-fircu');
                    $("#Suppliers_15.tabbed div#26").addClass('active-fircu');
                    $("div#24").trigger('click')
                } else if(subtab=='limitAsses') {    
                    //alert(subtab);                
                    $("#Suppliers_15.tabbed div").removeClass('active-fircu');
                    $("#Suppliers_15.tabbed div#26").addClass('active-fircu');
                    $("div#26").trigger('click')
                } else if(subtab=='financial') {               
                    $("#Suppliers_15.tabbed div").removeClass('active-fircu');
                    $("#Suppliers_15.tabbed div#25").addClass('active-fircu');
                    $("div#25").trigger('click')
                }  else if(subtab=='cibil') {               
                    $("#Suppliers_15.tabbed div").removeClass('active-fircu');
                    $("#Suppliers_15.tabbed div#23").addClass('active-fircu');
                    $("div#23").trigger('click')
                }             
            } else if(tab=="authSign") {
                $(".supplier-tabs-left li").removeClass('active2');
                $(".supplier-tabs-left li:eq(1)").addClass('active2');
                $(".supplier-tabs-left li:eq(1) a").trigger('click');                
            } else if(tab=="logistics") {
                $(".supplier-tabs-left li").removeClass('active2');
                $(".supplier-tabs-left li:eq(4)").addClass('active2');
                $(".supplier-tabs-left li:eq(4) a").trigger('click');                
            } else if(tab=="buyers") {
                $(".supplier-tabs-left li").removeClass('active2');
                $(".supplier-tabs-left li:eq(3)").addClass('active2');
                $(".supplier-tabs-left li:eq(3) a").trigger('click');
            } else if(tab=="documents") {
                $(".supplier-tabs-left li").removeClass('active2');
                $(".supplier-tabs-left li:eq(2)").addClass('active2');
                $(".supplier-tabs-left li:eq(2) a").trigger('click');
                //$("#18").trigger('click');            
            } else {
                $(".supplier-tabs-left li").removeClass('active2');
                $(".supplier-tabs-left li:eq(0)").addClass('active2');
                $(".supplier-tabs-left li:eq(0) a").trigger('click');
            }
        } else {
            $(".supplier-tabs-left li:first").addClass('active2')
            if($(".showRow.active2").length > 0){
                setTimeout(function() {
                    
                    var flag = $("#flag-hidden").val();
                    if(flag > 0){
                        console.log("meenu"+flag)
                        $('.nav.nav-tabs li ').removeClass('active2 active')
                        $('#'+flag).trigger('click')
                        $('#'+flag).closest('li').addClass('active2')
                    }else{
                        $(".supplier-tabs-left li:first a").trigger('click')
                    }
                }, 1000);
            }
        }
    });