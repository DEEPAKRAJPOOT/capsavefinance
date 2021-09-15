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
                {data: 'invoice_no'},
                {data: 'trans_type'},
                {data: 'total_repay_amt'},
                {data: 'settled_amt'},                    
                {data: 'payment_date'},
                {data: 'select'}
            ];
                break;
            case 'refundTransactions':
            columns =  [
                {data: 'disb_date'},
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
        var paymentAmt = parseFloat(this.data.payment_amt);
        if($.isEmptyObject(oldData.payment) && $.isEmptyObject(oldData.check)) {
            $(".pay").each(function (index, element) {
                let id = $(this).attr('id');
                let payEnabled = $('#check_' + id).attr('payenabled');
                if(paymentAmt>0 && payEnabled == 1){
                    let value =  parseFloat($(this).attr('max'));
                    if(paymentAmt>=value){
                        $(this).val(value.toFixed(2));
                        $(this).attr('readonly',false);
                        $("input[name='check["+id+"]']").prop("checked", true);
                        paymentAmt = paymentAmt-value.toFixed(2);
                    }else{
                        $(this).val(paymentAmt.toFixed(2));
                        $(this).attr('readonly',false);
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
                    $("input[name='payment["+id+"]']").attr('readonly',true);
                }
            });
        }
        this.calculateUnAppliedAmt();
    }

    calculateUnAppliedAmt(){
        var payment_amt = parseFloat(this.data.payment_amt);
        var settled_amt = 0;
        $(".pay").each(function (index, element) {
            var payamt = parseFloat($(this).val());
            if($.isNumeric(payamt)){
                settled_amt += payamt;
            }
        });
        var unapplied_amt = payment_amt.toFixed(2)-settled_amt.toFixed(2);
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
            $("input[name='payment["+transId+"]']").removeAttr('readonly');
        } else { 
            $("input[name='payment["+transId+"]']").attr('readonly',true);
        }
        this.calculateUnAppliedAmt()
    }
    
    validateMarkSettled(el){
        var check = $('.check');
        var status = true;
        var message = '';
        var paymentAmt = parseFloat(this.data.payment_amt);
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
                        var value = parseFloat($("input[name='payment"+name+"']").val());
                        if(isNaN(value)){
                            message = "Please enter valid value in Pay at row no - "+(index+1);
                            status = false;
                        }
                        else if(value <= 0){
                            message =  "Please enter value greater than 0 in Pay at row no - "+(index+1);
                            status = false;
                        }else{
                            totalSettledAmt +=value;
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
                        var value = parseFloat($("input[name='refund"+name+"']").val());
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
                        var value = parseFloat($("input[name='payment"+name+"']").val());
                        if(isNaN(value)){
                            message = "Please enter valid value in Pay at row no - "+(index+1);
                            status = false;
                        }
                        else if(value <= 0){
                            message =  "Please enter value greater than 0 in Pay at row no - "+(index+1);
                            status = false;
                        }else{
                            totalSettledAmt +=value;
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
        var paymentAmt = parseFloat(this.data.payment_amt);
        var selectAmt = 0;
        if(check.filter(':checked').length == 0){
            message = "Please Select at least one ";
            status = false;
        } 
        check.filter(':checked').each(function (index, element) {
            selectAmt += parseFloat($(element).val());
        });

        if(selectAmt>paymentAmt){
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
        $('.pay[type="text"]').removeAttr('readonly');
      }else{
         $('.check[type="checkbox"]').prop('checked', false);
         $('.pay[type="text"]').attr('readonly',true);
      }
      $('.pay[type="text"]').val('');
      this.calculateUnAppliedAmt()
    }

    onRefundChange(transId){

    }

    onRefundCheckChange(transId){
        if ($("input[name='check["+transId+"]']").is(":checked")) { 
            $("input[name='refund["+transId+"]']").attr('readonly',false);
        } else { 
            $("input[name='refund["+transId+"]']").attr('readonly',true);
        }
        $("input[name='refund["+transId+"]']").val('');
    }

    selectAllRefundCheck(checkallId){
        if ($('#' + checkallId).is(':checked')) {
            $('.check[type="checkbox"]').prop('checked', true);
            $('.refund[type="text"]').attr('readonly',false);
        }else{
            $('.check[type="checkbox"]').prop('checked', false);
            $('.refund[type="text"]').attr('readonly',true);
        }
        $('.refund[type="text"]').val(''); 
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

});

$(document).on('propertychange change click keyup input paste','.pay',function(){
    this.value = this.value.replace(/[^0-9\.]/g,'');
    apport.onPaymentChange($(this).attr('id'));
});

$(document).on('propertychange change click keyup input paste','.refund',function(){
    this.value = this.value.replace(/[^0-9\.]/g,'');
    apport.onRefundChange($(this).attr('id'));
});