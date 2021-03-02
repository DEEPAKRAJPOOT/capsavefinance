/* global messages, message */

try {
    var oTable, otable1;
    jQuery(document).ready(function ($) {
        //Charges Listing code
        oTable = $('#BankList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_bank_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#BankList").append('<tbody class="BankList-error"><tr><th colspan="5">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#BankList_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'id'},
                    {data: 'bank_name'},
                    {data: 'per_bank_id'},
                    {data: 'status'},
                    {data: 'action'},
                ],
            aoColumnDefs: [{'aTargets': [0,1,2,3,4], 'bSortable': true}]
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