class Apportionment {
    constructor(data) {
        this.data = data;
    }
    
    datatableView(id){
        var data = this.data;
        return $("#"+id).DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": data.url, // json datasource
                "method": 'POST',
                data: function (d) {
                    d._token = data.token;
                },
                "error": function () {  // error handling
                    $("#"+id).append('<tbody class="appList-error"><tr><th colspan="8">' + data.data_not_found + '</th></tr></tbody>');
                    $("#"+id+"_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'disb_date'},
                {data: 'invoice_no'},
                {data: 'trans_type'},
                {data: 'total_repay_amt'},                    
                {data: 'outstanding_amt'},
                {data: 'payment_date'},
                {data: 'pay'},
                {data: 'select'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
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