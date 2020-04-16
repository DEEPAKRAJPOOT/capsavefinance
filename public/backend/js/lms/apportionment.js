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
                {data: 'payment_date'},
                {data: 'pay'},
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
                    var paymentAmt = data.payment_amt;
                    $(".pay").each(function (index, element) {
                        if(paymentAmt>0){
                            let value =  parseFloat($(this).attr('max'));
                            let id = $(this).attr('id');
                            if(paymentAmt>=value){
                                $(this).val(value);
                                $(this).attr('readonly',false)
                                $("input[name='check["+id+"]']").prop("checked", true);
                                paymentAmt = paymentAmt-value;
                            }else{
                                $(this).val(paymentAmt);
                                $(this).attr('readonly',false)
                                $("input[name='check["+id+"]']").prop("checked", true);
                                paymentAmt= 0;
                            }
                        }
                    });
                }
            }
        });
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