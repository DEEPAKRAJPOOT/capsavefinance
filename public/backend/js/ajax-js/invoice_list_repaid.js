try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#invoiceListRepaid').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.backend_get_invoice_list_repaid, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.anchor_id = $('select[name=search_anchor]').val();
                    d.supplier_id = $('select[name=search_supplier]').val();
                    d.biz_id = $('input[name=search_biz]').val();
                    d.front = $('input[name=front]').val();
                    d._token = messages.token;
                    d.app_id = messages.appp_id;
                },
                "error": function () {  // error handling
                   
                    $("#invoiceListRepaid").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#appList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'invoice_id', width: '10%' },
                {data: 'anchor_name', width: '30%' },
                {data: 'supplier_name', width: '20%' },
                {data: 'invoice_date', width: '10%' },
                {data: 'invoice_amount', width: '20%' },
                {data: 'updated_at', width: '10%' },
                
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

