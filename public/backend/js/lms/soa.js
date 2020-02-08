try {
    var oTable;
    jQuery(document).ready(function ($) {
        //User Listing code
        oTable = $('#lmsSoaList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.lms_get_soa_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling

                    $("#lmsSoaList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#lmsSoaList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'virtual_acc_id'},
                {data: 'trans_date'},
                {data: 'value_date'},
                {data: 'trans_type'},
                {data: 'currency'},
                {data: 'debit'},
                {data: 'credit'},
                {data: 'balance'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [4]}]
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