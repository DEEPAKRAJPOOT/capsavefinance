try {
    var oTable;
    
    jQuery(document).ready(function ($) {
        //User Listing code
        if($('#requestList').length){
            oTable = $('#requestList').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                searching: false,
                bSort: true,
                ajax: {
                    "url": messages.url, // json datasource
                    "method": 'POST',
                    data: function (d) {
                        d._token = messages.token;
                    },
                    "error": function () {  // error handling

                        $("#requestList").append('<tbody class="appList-error"><tr><th colspan="8">' + messages.data_not_found + '</th></tr></tbody>');
                        $("#requestList_processing").css("display", "none");
                    }
                },
                columns: [
                    {data: 'ref_code'},
                    {data: 'type'},
                    {data: 'amount'},
                    {data: 'created_at'},
                    {data: 'assignee'},
                    {data: 'assignedBy'},
                    {data: 'status'},
                    {data: 'action'}
                ],
                aoColumnDefs: [{'bSortable': false, 'aTargets': [0]}]
            });
        }

        /*        
        //Search
        $('#searchbtn').on('click', function (e) {
            oTable.draw();
        }); 
        */

    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}