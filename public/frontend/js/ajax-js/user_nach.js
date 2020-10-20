try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#userNachList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.front_ajax_user_nach_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#userNachList").append('<tbody class="userNachList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#userNachList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'customer_id'},
                {data: 'customer_name'},
                {data: 'bank_name'},
                {data: 'start_date'},
                {data: 'end_date'},
                {data: 'status'},
                {data: 'created'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,2,3,4]}]
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