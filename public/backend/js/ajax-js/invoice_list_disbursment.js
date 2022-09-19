try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#invoiceListDisbursed').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.backend_get_invoice_list_disbursed, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.anchor_id = $('select[name=search_anchor]').val();
                    d.supplier_id = $('select[name=search_supplier]').val();
                     d.biz_id = $('input[name=search_biz]').val();
                    d.front = $('input[name=front]').val();
                    d.front = $('input[name=front]').val();
                    d.customer_id = $('input[name=customer_id]').val();
                    d.batch_id = $('select[name=batch_id]').val();
                    d._token = messages.token;
                    d.app_id = messages.appp_id;
                },
                "error": function () {  // error handling
                   
                    $("#invoiceListDisbursed").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#appList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'invoice_id'},
                {data: 'batch_id'},
                {data: 'anchor_name'},
                {data: 'customer_detail'},
                {data: 'invoice_date'},
                {data: 'invoice_amount'},
                {data: 'updated_at'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,2]}]
        });

        //Search
         $('#search_biz').on('click', function (e) {
            oTable.draw();
        });                   
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

