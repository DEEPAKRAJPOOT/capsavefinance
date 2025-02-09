try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#invoiceListDisbursedQue').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.backend_get_ep_list_approve, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.anchor_id = $('select[name=search_anchor]').val();
                    d.supplier_id = $('select[name=search_supplier]').val();
                    d.biz_id = $('select[name=search_biz]').val();
                    d.front = $('input[name=front]').val();
                    d._token = messages.token;
                    d.app_id = messages.appp_id;
                },
                "error": function () {  // error handling
                   
                    $("#invoiceListDisbursedQue").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#appList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'invoice_id'},
                {data: 'anchor_name'},
                {data: 'supplier_name'},
                {data: 'invoice_date'},
                {data: 'invoice_amount'},
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

