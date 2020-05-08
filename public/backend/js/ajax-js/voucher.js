
/* global messages, message */

try {
    var oTable, otable1;
    jQuery(document).ready(function ($) {
        //Charges Listing code
        oTable = $('#voucherList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_vouchers_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#voucherList").append('<tbody class="voucherList-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#voucherList_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'voucher_code'},
                    {data: 'voucher_name'},
                    {data: 'transaction_type'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'aTargets': [0,1], 'bSortable': true}]
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
