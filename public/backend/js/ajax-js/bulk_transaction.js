try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#invoiceListTransaction').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: false,
            ajax: {
                "url": messages.backend_get_bulk_transaction, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.type = $('select[name=type]').val();
                    d.date = $('input[name=date]').val();
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#invoiceListTransaction").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#appList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'customer_id'},
                {data: 'customer_name'},
                {data: 'customer_detail'},
                {data: 'trans_type'},
                {data: 'comment'},
                {data: 'created_by'},
                {data: 'action'},
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,2]}]
        });

        //Search
        $('.searchbtn').on('click', function (e) {
            oTable.draw();
        });                   
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

