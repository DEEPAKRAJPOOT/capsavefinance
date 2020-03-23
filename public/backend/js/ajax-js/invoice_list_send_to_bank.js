
try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#disbursalCustomerList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.backend_get_invoice_list_bank, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling

                    $("#disbursalCustomerList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#disbursalCustomerList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'customer_id'},
                {data: 'batch_id'},
                {data: 'customer_code'},
                {data: 'ben_name'},
                {data: 'bank'},
                {data: 'total_actual_funded_amt'},
                {data: 'total_invoice'},
                {data: 'status'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0, 2, 3, 4, 5, 6, 7, 8]}]
        });

        //Search
        $('#searchbtn').on('click', function (e) {
            oTable.draw();
        });                  
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}


