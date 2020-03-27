
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
                    d.customer_code = $('input[name=customer_code]').val();
                    d.selected_date = $('input[name=selected_date]').val();
                    d.batch_id = $('select[name=batch_id]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling

                    $("#disbursalCustomerList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#disbursalCustomerList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'batch_id'},
                {data: 'customer_id'},
                {data: 'ben_name'},
                {data: 'bank'},
                {data: 'total_actual_funded_amt'},
                {data: 'total_invoice'},
                {data: 'updated_at'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0, 2, 3, 4, 5, 6]}]
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


