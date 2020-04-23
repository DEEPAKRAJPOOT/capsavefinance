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
                {data: 'payment_date'},
                {data: 'pay'},
                {data: 'select'}
            ];
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
                {data: 'payment_date'},
                {data: 'pay'},
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
            }
        });
    }

    setTransactionAmt(){
        var oldData = this.data.old_data;
        var paymentAmt = this.data.payment_amt;
        if($.isEmptyObject(oldData.payment) && $.isEmptyObject(oldData.check)) {
            $(".pay").each(function (index, element) {
                if(paymentAmt>0){
                    let value =  parseFloat($(this).attr('max'));
                    let id = $(this).attr('id');
                    if(paymentAmt>=value){
                        $(this).val(value);
                        $(this).attr('readonly',false);
                        $("input[name='check["+id+"]']").prop("checked", true);
                        paymentAmt = paymentAmt-value;
                    }else{
                        $(this).val(paymentAmt);
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
        var payment_amt = this.data.payment_amt;
        var settled_amt = 0;
        $(".pay").each(function (index, element) {
            var payamt = parseFloat($(this).val());
            if($.isNumeric(payamt)){
                settled_amt += payamt;
            }
        });
        var unapplied_amt = payment_amt-settled_amt;
        if(unapplied_amt < 0 ){
            alert("Sum of your total entries is grater than Re-payment amount");
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
        var paymentAmt = this.data.payment_amt;
        var totalSettledAmt = 0;
        if($('.check').filter(':checked').length == 0){
            message = "Please Select at least one ";
            status = false;
        } 
        if(status){
            $('.check').each(function (index, element) {
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
            if(totalSettledAmt > paymentAmt){
                message =  "Sum of your total entries is grater than Re-payment amount";
                status = false;
            }
        }
            
        if(!status){
            alert(message);
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
                 alert('Waive off is not allowed except charges and interests');
                 return false;
           }
           var transId = checkedName.replace(/[^0-9]/g, '');
           var givenUrl = data.trans_waiveoff_url;
           var targetUrl = givenUrl + '?trans_id=' + transId;
           if(data.payment_id){
                targetUrl += '&payment_id=' + data.payment_id;
            }
           $('.view_detail_transaction').attr('data-url', targetUrl);
           $('.view_detail_transaction').trigger('click');
       }else{
            alert('Please select only one checkbox');
       }
    }

    onReversalAmount(){
       var data = this.data;
       var numberOfChecked = $('input:checkbox:checked').length;
       if (numberOfChecked == 1) {
           var checkedName = $('input:checkbox:checked').attr('name');
           var transId = checkedName.replace(/[^0-9]/g, '');
           var givenUrl = data.trans_reversal_url;
           var targetUrl = givenUrl + '?trans_id=' + transId;
           if(data.payment_id){
                targetUrl += '&payment_id=' + data.payment_id;
            }
            console.log(targetUrl);
           $('.view_detail_transaction').attr('data-url', targetUrl);
           $('.view_detail_transaction').trigger('click');
       }else{
            alert('Please select only one checkbox to reverse.');
       }
    }

    selectAllChecks(checkallId){
      if ($('#' + checkallId).is(':checked')) {
        $('.check[type="checkbox"]').prop('checked', true);
      }else{
         $('.check[type="checkbox"]').prop('checked', false);
      }
      $('.pay[type="text"]').val('');
      this.calculateUnAppliedAmt()
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

});