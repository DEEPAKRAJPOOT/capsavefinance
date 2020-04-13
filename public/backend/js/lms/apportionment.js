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
            processing: true,
            serverSide: true,
            pageLength: '*',
            searching: false,
            bSort: true,
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
                    setPayAmount(1222,id);
                }
                alert( 'DataTables has redrawn the table' );
            }
        });
    }

    setPayAmount(amount, tableId){
        $("#"+tableId).each(function (index, element) {
            
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