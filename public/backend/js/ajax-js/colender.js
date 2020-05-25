try {
    var oTable;
    jQuery(document).ready(function ($) {   
        //User Listing code
        oTable = $('#appList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                "url": messages.get_colender_applications, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d.customer_id = $('input[name=customer_id]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#appList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#appList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'customer_id'},
                {data: 'app_id'},                
                {data: 'virtual_acc_id'},
                {data: 'customer_name'},
                {data: 'limit'},
                {data: 'consume_limit'},
                {data: 'available_limit'},
                {data: 'anchor'},
                {data: 'status'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,2,3,4,6,7,8]}]
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