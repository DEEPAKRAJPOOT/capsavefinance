try {
    var oTable;
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
