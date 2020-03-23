try {
    var oTable, oTableCustomers;
    jQuery(document).ready(function ($) {
        oTable = $('#bankInvoice').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: true,
            bSort: true,
            ajax: {
               "url": messages.get_ajax_bank_invoice, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling                   
                    $("#bankInvoice").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'batch_id'},
                    {data: 'total_users'},
                    {data: 'total_amt'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });

        oTableCustomers = $('#bankInvoiceCustomers').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: true,
            bSort: true,
            ajax: {
               "url": messages.get_ajax_bank_invoice_customers,
                "method": 'POST',
                data: function (d) {
                    d.batch_id = $('input[name=batch_id]').val();
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {                   
                    $("#bankInvoiceCustomers").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'customer_id'},
                    {data: 'biz_entity_name'},
                    {data: 'ben_name'},
                    {data: 'bank_detail'},
                    {data: 'total_amt'},
                    {data: 'total_invoice'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });

        oTableViewDisburseInvoice = $('#viewDisburseInvoice').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: true,
            bSort: true,
            ajax: {
               "url": messages.get_ajax_view_disburse_invoice,
                "method": 'POST',
                data: function (d) {
                    d.batch_id = $('input[name=batch_id]').val();
                    d.disbursed_user_id = $('input[name=disbursed_user_id]').val();
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {                   
                    $("#viewDisburseInvoice").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'app_id'},
                    {data: 'invoice_no'},
                    {data: 'disburse_date'},
                    {data: 'inv_due_date'},
                    {data: 'disburse_amount'},
                    {data: 'disburse_type'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
        });

        // //Search
        // $('#searchbtn').on('click', function (e) {
        //     oTable.draw();
        // });   
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
