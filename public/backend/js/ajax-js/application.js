/* global messages, message */

try {
var oTable;
        $(document).ready(function () {
            oTable1 = $('#appList').DataTable({
                "order" : [[0, "asc"]],
                //"sDom": "<'row'<'col-md-2'l><'col-md-7'a><'col-md-2'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                //"sPaginationType": "bootstrap",
                "processing": true,
                "searching": false,
                ajax: {
                "url": messages.get_applications, // json datasource
                "method": 'POST',
                data: function (d) {                    
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                    $("#appList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#appList_processing").css("display", "none");
                }
            },
                columns: [
                    {data: 'app_id'},
                    {data: 'biz_entity_name'},
                    {data: 'assoc_anchor'},
                    {data: 'user_type'},
                    {data: 'assignee'},
                    {data: 'shared_detail'},
                    {data: 'status'},
                    {data: 'action'}
                ],
                 aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4]}]
            });
           //Search
        $('#searchForm').on('submit', function (e) {            
            e.preventDefault();
            oTable1.draw();

        });
            
    });
        
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
