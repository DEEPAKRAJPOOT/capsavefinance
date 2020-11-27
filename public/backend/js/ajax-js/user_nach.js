try {
    var oTable2, oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#anchorUserNachList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.anchor_ajax_user_nach_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#anchorUserNachList").append('<tbody class="anchorUserNachList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#userNachList_processing").css("display", "none");
                }
            },
            columns: [
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

    oTable2 = $('#backendUserNachList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.backend_ajax_user_nach_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.nach_status = $('select[name=nach_status]').val();
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#backendUserNachList").append('<tbody class="backendUserNachList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#backendUserNachList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'user_type'},
                {data: 'customer_name'},
                { data: 'email_id'},
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
            oTable2.draw();
        });                   
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}