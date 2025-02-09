try {
    var oTable, oTable1;
    jQuery(document).ready(function ($) {
        
        //User Listing code
        oTable = $('#appList').DataTable({
            "dom": '<"top">rt<"bottom"flpi><"clear">',
            autoWidth:false,
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: false,
                // "scrollY": 400,
                // "scrollX": true,
                // scrollCollapse: true,            
            ajax: {
                "url": messages.get_applications, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.search_type = $('select[name=search_type]').val();
                    d.search_keyword = $('input[name=search_keyword]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d.status = $('select[name=status]').val();
                    d.pan = $('select[name=pan]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#appList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#appList_processing").css("display", "none");
                }
            },
           columns: [
                {data: 'app_code'},
                {data: 'ucic_code'},
                {data: 'biz_entity_name'},
                {data: 'name'},
                {data: 'contact'},
                // {data: 'email'},
               // {data: 'mobile_no'},
                // {data: 'assoc_anchor'},
               // {data: 'user_type'},
                {data: 'assignee'},
                {data: 'assigned_by'},
               // {data: 'shared_detail'},
                {data: 'status'},
                {data: 'action'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,3,4,5,6,7]}]

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
