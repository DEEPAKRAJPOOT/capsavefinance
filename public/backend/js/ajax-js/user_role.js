
/* global messages, message */

try {

    var oTables1
    jQuery(document).ready(function ($) {
        
         
    //Role Listing code
        oTables1 = $('#RoleList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            order: [[ 1, 'asc' ]],
            "dom": '<"top">rt<"bottom"flpi><"clear">',
            ajax: {
               "url": messages.get_role_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_email = $('input[name=by_email]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#RoleList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'srno'},
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'mobile'},
                    {data: 'rolename'},
                    {data: 'reporting_mgr'},
                    {data: 'active'},
                    {data: 'created_at'},
                    // {data: 'updated_by'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4,5,6,7,8]}]

        }); 
        
        oTables1.on( 'draw.dt', function () {
            var PageInfo = $('#RoleList').DataTable().page.info();
            oTables1.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                } );
            } );
    
    
     //Search
        $('#searchB').on('click', function (e) {
            oTables1.draw();

        });
       
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}


