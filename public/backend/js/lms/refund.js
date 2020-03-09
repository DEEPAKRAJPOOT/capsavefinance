try {
    var oTable;
    jQuery(document).ready(function ($) {
        //User Listing code
        oTable = $('#refundCustomerList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.lms_get_refund_customer, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.from_date = $('input[name="from_date"]').val();
                    d.to_date = $('input[name="to_date"]').val();
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling

                    $("#refundCustomerList").append('<tbody class="appList-error"><tr><th colspan="8">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#refundCustomerList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'invoice_id'},
                {data: 'customer_code'},
                {data: 'ben_name'},
                {data: 'ben_bank_name'},
                {data: 'ben_ifsc'},
                {data: 'ben_account_no'},
                {data: 'surplus_amount'},
                {data: 'status'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
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