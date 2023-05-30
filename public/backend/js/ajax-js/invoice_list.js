try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#invoiceList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ordering: false,
            ajax: {
                "url": messages.backend_get_invoice_list, // json datasource
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
                   
                    $("#invoiceList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#appList_processing").css("display", "none");
                }
            },
            columns: [
                { data: 'anchor_id', width: '2%' },
                { data: 'invoice_id', width: '10%' },
                { data: 'anchor_name', width: '25%' },
                { data: 'supplier_name', width: '20%' },
                { data: 'invoice_date_detail', width: '10%' },
                { data: 'invoice_amount', width: '15%' },
                { data: 'updated_at', width: '10%' },
                { data: 'action', width: '10%' }
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,2]}],
            createdRow: function (row, data, dataIndex) {
                var upfrontInterest = data.upfront_interest; // assuming this value is in the data object
                if (typeof upfrontInterest !== 'undefined' && upfrontInterest != null) {
                    $('td:eq(5)', row).append('<br/><b>Upfront Int. Amt:</b> ' + upfrontInterest); // append the value to the 6th column (index 5)
                }
            }
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

