class Apportionment {
    constructor(data) {
        this.data = data;
    }

    dataTableColumns(tableId){
        var columns = [];
        switch (tableId) {
            case 'unsettledTransactions':
            columns = [
                {data: 'disb_date'},
                {data: 'payment_due_date'},
                {data: 'invoice_no'},
                {data: 'trans_type'},
                {data: 'total_repay_amt'},                    
                {data: 'outstanding_amt'},
                // {data: 'payment_date'},
                {data: 'pay'},
                {data: 'select'}
            ];
            if(!this.data.payment_id){
                columns = columns.filter(function(d){ 
                    var gg = d.data;
                    if($.inArray(gg, ['payment_date','pay'])<0 ){
                        console.log(gg);
                        return d;
                    }
                });
            }
                break;
            case 'settledTransactions':
            columns = [
                {data: 'disb_date'},
                {data: 'payment_due_date'},
                {data: 'invoice_no'},
                {data: 'trans_type'},
                {data: 'total_repay_amt'},
                {data: 'settled_amt'},                    
                {data: 'payment_date'},
                // {data: 'select'}
            ];
                break;
            case 'refundTransactions':
            columns =  [
                {data: 'disb_date'},
                {data: 'payment_due_date'},
                {data: 'invoice_no'},
                {data: 'trans_type'},
                {data: 'total_repay_amt'},
                {data: 'outstanding_amt'},
                {data: 'refund'},
                {data: 'select'}
            ];
                break;
            case 'runningTransactions':
                columns = [
                    {data: 'disb_date'},
                    {data: 'invoice_no'},
                    {data: 'trans_type'},
                    {data: 'total_repay_amt'},                    
                    {data: 'outstanding_amt'},
                    {data: 'select'}
                ];
                break;
        }
        return columns;
    }

    datatableView(id,columns){
        var data = this.data;
        var columns = this.dataTableColumns(id);
        var parentRef = this;
        return $("#"+id).DataTable({
            processing: false,
            serverSide: true,
            pageLength: '*',
            searching: false,
            bSort: false,
            bPaginate: false,
            info: false,
            ajax: {
                "url": data.url, // json datasource
                "method": 'POST',
                data: function (d) {
                    
                    d.user_id = data.user_id;
                    if(data.payment_id){
                        d.payment_id = data.payment_id;
                    }
                    d._token = data.token;
                },
                "error": function () {  // error handling
                    $("#"+id).append('<tbody class="appList-error"><tr><th colspan="8">' + data.data_not_found + '</th></tr></tbody>');
                    $("#"+id+"_processing").css("display", "none");
                }
            },
            columns: columns,
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}],
            drawCallback: function( settings ) {
                if(id == 'unsettledTransactions'){
                    if($(".pay").length){
                        $('#mark_settle_btn').removeAttr("disabled");
                        $('#dwnldUnTransCsv').removeClass("disabled");
                        $('#uploadUnTransCsv').removeClass("disabled");
                        $('#dltUnTransCsv').removeClass("disabled");
                    }else{
                        $('#mark_settle_btn').prop("disabled", true);
                        $('#dwnldUnTransCsv').prop("disabled", true);
                        $('#uploadUnTransCsv').prop("disabled", true);
                        $('#dltUnTransCsv').prop("disabled", true);
                    }
                    parentRef.setTransactionAmt();
                }
                var rows = this.fnGetData();
                // if ( rows.length === 0 ) {
                //     $('.action-btn').hide();
                // }else{
                //     $('.action-btn').show();
                // }
            },
        });
    }

    setTransactionAmt(){
        var oldData = this.data.old_data;
        var paymentAmt = parseFloat(this.data.payment_amt).toFixed(2);
        if($.isEmptyObject(oldData.payment) && $.isEmptyObject(oldData.check)) {
            $(".pay").each(function (index, element) {
                let id = $(this).attr('id');
                let payEnabled = $('#check_' + id).attr('payenabled');
                if(paymentAmt > 0 && payEnabled == 1){
                    let value =  parseFloat($(this).attr('max'));
                    if(paymentAmt>=value){
                        $(this).val(value.toFixed(2));
                        $(this).attr('disabled',false);
                        $("input[name='check["+id+"]']").prop("checked", true);
                        paymentAmt = paymentAmt-value.toFixed(2);
                    }else{
                        if (typeof paymentAmt == 'number' ){
                            $(this).val(paymentAmt.toFixed(2));
                        }else{
                            $(this).val(paymentAmt);
                        }
                        $(this).attr('disabled',false);
                        $("input[name='check["+id+"]']").prop("checked", true);
                        paymentAmt= 0;
                    }
                }
            });
        }
        else{
            $(".pay").each(function (index, element) {
                let id = parseInt($(this).attr('id'));
                if(!$.isEmptyObject(oldData.payment[id])){
                    $("input[name='payment["+id+"]']").val(oldData.payment[id]);
                }
                if(!$.isEmptyObject(oldData.check[id])){
                    $("input[name='check["+id+"]']").prop("checked", true);
                }else{
                    $("input[name='payment["+id+"]']").attr('disabled',true);
                }
            });
        }
        this.calculateUnAppliedAmt();
    }

    calculateUnAppliedAmt(){
        var payment_amt = parseFloat(this.data.payment_amt).toFixed(2);
        var settled_amt = 0;
        $(".pay").each(function (index, element) {
            var payamt = parseFloat($(this).val()).toFixed(2);
            if($.isNumeric(payamt)){
                settled_amt = (parseFloat(settled_amt) + parseFloat(payamt)).toFixed(2);
            }
        });
        var unapplied_amt = payment_amt-settled_amt;
        if(parseFloat(unapplied_amt.toFixed(2)) < 0 ){
            replaceAlert("Sum of your total entries is Greater than Re-payment amount", 'error');
        } 
        $('#unappliledAmt').text('₹ '+unapplied_amt.toFixed(2));
    }

    onPaymentChange(transId){
        this.calculateUnAppliedAmt()
    }

    onCheckChange(transId){
        $("input[name='payment["+transId+"]']").val('');
        if ($("input[name='check["+transId+"]']").is(":checked")) { 
            $("input[name='payment["+transId+"]']").removeAttr('disabled');
        } else { 
            $("input[name='payment["+transId+"]']").attr('disabled',true);
        }
        this.calculateUnAppliedAmt()
    }
    
    validateMarkSettled(el){
        var check = $('.check');
        var status = true;
        var message = '';
        var paymentAmt = parseFloat(this.data.payment_amt).toFixed(2);
        var totalSettledAmt = 0;
        if(check.length == 0 ||  check.filter(':checked').length == 0){
            message = "Please Select at least one ";
            status = false;
        }

        var action = $("input[name='action']").val();
        if(action == 'Mark Settled'){
            $("#unsettlementFrom").attr('action',this.data.confirm_settle);
            if(status){
                check.each(function (index, element) {
                    if($(this). is(":checked")){
                        var name = $(this).attr('name');
                        name =  name.replace('check','');
                        var value = parseFloat($("input[name='payment"+name+"']").val()).toFixed(2);;
                        if(isNaN(value)){
                            message = "Please enter valid value in Pay at row no - "+(index+1);
                            status = false;
                        }
                        else if(value <= 0){
                            message =  "Please enter value greater than 0 in Pay at row no - "+(index+1);
                            status = false;
                        }else{
                            totalSettledAmt = (parseFloat(totalSettledAmt) + parseFloat(value)).toFixed(2);
                        }
                        if(!status){
                            return false;
                        }   
                    }
                });
            }
    
            if(status){
                if(parseFloat(totalSettledAmt.toFixed(2)) > parseFloat(paymentAmt.toFixed(2))){
                    message =  "Sum of your total entries is Greater than Re-payment amount";
                    status = false;
                }
            } 
        }
        else if (action == 'Write Off'){
            $("#unsettlementFrom").attr('action',this.data.confirm_writeoff); 
        }
        else if(action == 'Adjustment'){
            $("#unsettlementFrom").attr('action',this.data.confirm_adjustment); 
            if(status){
                check.each(function (index, element) {
                    if($(this). is(":checked")){
                        var name = $(this).attr('name');
                        name =  name.replace('check','');
                        var value = parseFloat($("input[name='refund"+name+"']").val()).toFixed(2);
                        if(isNaN(value)){
                            message = "Please enter valid value in field at row no - "+(index+1);
                            status = false;
                        }
                        else if(value <= 0){
                            message = "Please enter value greater than 0 in field at row no - "+(index+1);
                            status = false;
                        }
                        if(!status){
                            return false;
                        }   
                    }
                });
            }
        }
    
        if(!status){
            replaceAlert(message, 'error');
            return status;
        }
    }

    validateMarkSettledTDS(el){
        var check = $('.check');
        var status = true;
        var message = '';
        var paymentAmt = parseFloat(this.data.payment_amt).toFixed(2);

        var totalSettledAmt = 0;
        if(check.length > 0 &&  check.filter(':checked').length == 0){
            message = "Please Select at least one ";
            status = false;
        }

        var action = $("input[name='action']").val();
        if(action == 'Mark Settled'){
            $("#unsettlementFrom").attr('action',this.data.confirm_settle);
            if(status){
                check.each(function (index, element) {
                    if($(this). is(":checked")){
                        var name = $(this).attr('name');
                        name =  name.replace('check','');
                        var value = parseFloat($("input[name='payment"+name+"']").val()).toFixed(2);
                        if(isNaN(value)){
                            message = "Please enter valid value in Pay at row no - "+(index+1);
                            status = false;
                        }
                        else if(value <= 0){
                            message =  "Please enter value greater than 0 in Pay at row no - "+(index+1);
                            status = false;
                        }else{
                            totalSettledAmt = (parseFloat(totalSettledAmt) + parseFloat(value)).toFixed(2);
                        }
                        if(!status){
                            return false;
                        }
                    }
                });
            }
            if(status){
                if(totalSettledAmt.toFixed(2) > paymentAmt){
                    message =  "Sum of your total entries is Greater than TDS amount";
                    status = false;
                }
            }
        }

        if(!status){
            replaceAlert(message, 'error');
            return status;
        }
    }

    validateRunningPosted(){
        
        var check = $('.check');
        var status = true;
        var message = '';
        var paymentAmt = parseFloat(this.data.payment_amt).toFixed(2);
        var selectAmt = 0;
        if(check.filter(':checked').length == 0){
            message = "Please Select at least one ";
            status = false;
        } 
        check.filter(':checked').each(function (index, element) {
            selectAmt = (parseFloat(selectAmt) + parseFloat($(element).val())).toFixed(2);
        });

        if(parseFloat(selectAmt) > parseFloat(paymentAmt)){
            message = "Requested Amount: "+selectAmt+ " is greater than Unsettled Amount: "+paymentAmt;
            status = false;
        }
        if(!confirm('Are you sure? You want to Mark Posted.'))
        return false;
        if(!status){
            replaceAlert(message, 'error');
            return status;
        }
    }

    onWaveOff(){
       var data = this.data;
       var numberOfChecked = $('input:checkbox:checked').length;
       if (numberOfChecked == 1) {
           var checkedName = $('input:checkbox:checked').attr('name');
           var transtype = $('input:checkbox:checked').attr('transtype');
           if (!transtype && transtype != 'charges' && transtype != 'interest') {
                 replaceAlert('Waived off is not allowed except charges and interests', 'error');
                 return false;
           }
           var transId = checkedName.replace(/[^0-9]/g, '');
           var givenUrl = data.trans_waiveoff_url;
           var targetUrl = givenUrl + '&trans_id=' + transId;
           if(data.payment_id){
                targetUrl += '&payment_id=' + data.payment_id;
            }
           $('.view_detail_transaction').attr('data-url', targetUrl);
           $('.view_detail_transaction').trigger('click');
       }else{
            replaceAlert('Please select only one checkbox', 'error');
       }
    }

    onWriteOff(){
        var data = this.data;
        var checkedTransIds = $('.check:checked');
        var numberOfChecked = checkedTransIds.length;
        var transId = [];
        if (numberOfChecked > 0) {
            checkedTransIds.each(function (index, element) {
                var checkedName = $(this).attr('name');
                transId.push(checkedName.replace(/[^0-9]/g, ''))
            });
            
            var transIdString = transId.toString();
            var givenUrl = data.trans_writeoff_url;
            var targetUrl = givenUrl + '&trans_ids=' + transIdString;
           
            $('.view_detail_transaction').attr('data-url', targetUrl);
            $('.view_detail_transaction').trigger('click');
        }else{
            replaceAlert('Please select at least one checkbox', 'error');
        }
    }

    onReversalAmount(){
       var data = this.data;
       var numberOfChecked = $('input:checkbox:checked').length;
       if (numberOfChecked == 1) {
           var checkedName = $('input:checkbox:checked').attr('name');
           var transId = checkedName.replace(/[^0-9]/g, '');
           var givenUrl = data.trans_reversal_url;
           var targetUrl = givenUrl + '&trans_id=' + transId;
           if(data.payment_id){
                targetUrl += '&payment_id=' + data.payment_id;
            }
           $('.view_detail_transaction').attr('data-url', targetUrl);
           $('.view_detail_transaction').trigger('click');
       }else{
            replaceAlert('Please select only one checkbox to reverse.', 'error');
       }
    }

    selectAllChecks(checkallId){
      if ($('#' + checkallId).is(':checked')) {
        $('.check[type="checkbox"]').prop('checked', true);
        $('.pay[type="text"]').removeAttr('disabled');
      }else{
         $('.check[type="checkbox"]').prop('checked', false);
         $('.pay[type="text"]').attr('disabled',true);
      }
      $('.pay[type="text"]').val('');
      this.calculateUnAppliedAmt()
    }

    onRefundChange(transId){

    }

    onRefundCheckChange(transId){
        if ($("input[name='check["+transId+"]']").is(":checked")) {
            var amt = $("input[name='refund["+transId+"]']").attr('max');
            $("input[name='refund["+transId+"]']").val(amt);
            $("input[name='refund["+transId+"]']").attr('disabled',false);
        } else { 
            $("input[name='refund["+transId+"]']").attr('disabled',true);
            $("input[name='refund["+transId+"]']").val('');
        }
    }

    selectAllRefundCheck(checkallId){
        if ($('#' + checkallId).is(':checked')) {
            $('.refund[type="text"]').each(function(){
                var amt = $(this).attr('max');
                $(this).val(amt);
            });
            $('.check[type="checkbox"]').prop('checked', true);
            $('.refund[type="text"]').attr('disabled',false);
        }else{
            $('.check[type="checkbox"]').prop('checked', false);
            $('.refund[type="text"]').attr('disabled',true);
            $('.refund[type="text"]').val(''); 
        }
    }

}

var apport =  new Apportionment(messages);
  
jQuery(document).ready(function ($) {
    var oTable ; 

    if($('#refundTransactions').length){
        oTable = apport.datatableView('refundTransactions');
    }
    if($('#unsettledTransactions').length){
        oTable = apport.datatableView('unsettledTransactions');
    }
    if($('#settledTransactions').length){
        oTable = apport.datatableView('settledTransactions');
    }
    if($('#runningTransactions').length){
        oTable = apport.datatableView('runningTransactions');
    }

    $(document).on('click', '#Confirm', function () {
        if ($("input[name='confirm']").is(":checked")) {
            return true;
        }else{
            replaceAlert('Please select checkbox.', 'error');
            return false;
        }
    });
});

$(document).on('propertychange change click keyup input paste','.pay',function(){
    this.value = this.value.replace(/[^0-9\.]/g,'');
    apport.onPaymentChange($(this).attr('id'));
});

$(document).on('propertychange change click keyup input paste','.refund',function(){
    this.value = this.value.replace(/[^0-9\.]/g,'');
    apport.onRefundChange($(this).attr('id'));
});

/*$("#dwnldUnTransCsv").click(function(e) {   
    e.preventDefault();

    //open download link in new page
    window.open($(this).attr("href"));

    //redirect current page to success page
    //window.location.href = messages.apporUnsettleRedirect;
    window.focus();
    // location.reload();
});*/
let checkDownloadCsvEntry = function () {
    let data = "user_id=" + messages.user_id + "&payment_id=" + messages.payment_id + "&sanctionPageView=" + messages.sanctionPageView + "&payment_appor_id=" + messages.payment_appor_id + "&action_type=checkDownloadCsvEntry&_token=" + messages.token;
    $.ajax({
        type: "POST",
        url: messages.deleteCsvApport,
        data: data,
        beforeSend: function () {
            $("#dwnldUnTransCsv").addClass("disabled").text("Please wait...");
            $("#uploadUnTransCsv").addClass("disabled");
            $("input[name=action]").addClass("disabled");
        },
        error: function (xhr, status, error) {
            if (status === "timeout" || status === "error") {
                alert("Timeout or unable to receive statistics!");
            }
        },
        success: function (response) {
            if (response.status == 1) {
                $("#msg_action").fadeIn().html("<font color='green'><b>"+response.message+"</b></font>").fadeOut(3000);
                $aId = $("#dwnldUnTransCsv");
                $aId2 = $("#MarkSettled");
                $aId.removeClass("btn-success");
                $aId.addClass("btn-danger");
                $aId.attr("id", "dltUnTransCsv");
                $aId.text("Delete CSV");
                $aId2.attr("type", "button");
                $aId2.attr("onclick", "alert('You cannot perform this action as you have not uploaded  the unsettled payment apportionment CSV file.')");
                $aId3 = $('#dltUnTransCsv');
                $aId3.attr("href", "javascript:void(0);");
                $("#dwnldUnTransCsv,#dltUnTransCsv").removeClass("disabled").text("Delete CSV");
                $("#uploadUnTransCsv").removeClass("disabled");
                $("input[name=action]").removeClass("disabled");
                //window.focus();
            } else {
                console.log(response.message);
                $("#dwnldUnTransCsv,#dltUnTransCsv").removeClass("disabled");
                $("#uploadUnTransCsv").removeClass("disabled");
                $("input[name=action]").removeClass("disabled");
            }
        },
        complete: function (jqXHR, status) {
            if (status !== "timeout" && status !== "error") {
                //setTimeout(myfunc, 3000);
            }
            $("#dwnldUnTransCsv,#dltUnTransCsv").removeClass("disabled");
            $("#uploadUnTransCsv").removeClass("disabled");
            $("input[name=action]").removeClass("disabled");
        },
        timeout: 5000
    });
}

$(document).on("click", "#dwnldUnTransCsv", function (e) {
    e.preventDefault();
    window.location.href = $(this).attr("href");

    var tid = setInterval(function(){
        checkDownloadCsvEntry();
    },5000); //delay is in milliseconds 

    setTimeout(function(){
        clearInterval(tid); //clear above interval after 5 seconds
    },15000);
});

$(document).on("click", "#dltUnTransCsv", function (e) {
    e.preventDefault();
    if (confirm('Are you sure you want to delete csv?')) {
        let data = "user_id=" + messages.user_id + "&payment_id=" + messages.payment_id + "&sanctionPageView=" + messages.sanctionPageView + "&payment_appor_id=" + messages.payment_appor_id + "&_token=" + messages.token;
        $.ajax({
            type: "POST",
            url: messages.deleteCsvApport,
            data: data,
            beforeSend: function () {
                $("#dwnldUnTransCsv,#dltUnTransCsv").addClass("disabled").text("Please wait...");
                $("#uploadUnTransCsv").addClass("disabled");
                $("input[name=action]").addClass("disabled");
            },
        }).done(function (response) {
            //console.log(response);
            if (response.status == 1) {
                $("#msg_action").fadeIn().html("<font color='green'><b>"+response.message+"</b></font>").fadeOut(3000);
                $aId = $('#dltUnTransCsv');
                $aId.attr("href", messages.downloadCsvApport);
                $aId.removeClass("btn-danger");
                $aId.addClass("btn-success");
                $aId.attr("id", "dwnldUnTransCsv");
                $aId.text("Download CSV");
                $aId2 = $("#MarkSettled");
                $aId2.attr("type", "submit");
                $aId2.removeAttr("onclick");
                $("#dwnldUnTransCsv,#dltUnTransCsv").removeClass("disabled");
                $("#uploadUnTransCsv").removeClass("disabled");
                $("input[name=action]").removeClass("disabled");
            } else {
                console.log(response.message);
                $("#dwnldUnTransCsv,#dltUnTransCsv").removeClass("disabled");
                $("#uploadUnTransCsv").removeClass("disabled");
                $("input[name=action]").removeClass("disabled");
            }
        });
    }
});