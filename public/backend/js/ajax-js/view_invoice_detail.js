try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#invoiceActivityList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.backend_activity_invoice_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.inv_name = $('input[name=inv_name]').val();
                    d.supplier_id = $('select[name=search_supplier]').val();
                    d.biz_id = $('select[name=search_biz]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#invoiceActivityList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#appList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'DT_RowIndex'},
                {data: 'amount'},
                {data: 'comment'},
                {data: 'status'},
                {data: 'update'},
                {data: 'timestamp'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,2]}]
        });

        //Search
        $('.searchbtn').on('change', function (e) {
            oTable.draw();
        });                   
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}



$(document).ready(function(){
 ///////////// use for amount comma seprate//////////////////////////   
document.getElementById('invoice_approve_amount').addEventListener('input', event =>
event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));

});
    
///////////////////////////////////////// change invoice amount////////////////
$(document).on('click','#UpdateInvoiceAmount',function(){
    
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

