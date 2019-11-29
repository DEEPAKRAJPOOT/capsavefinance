
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
                    {data: 'active'},
                    {data: 'created_at'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,2,3,4,5,6]}]

        }); 
        
        oTables1.on( 'order.dt search.dt', function () {
        oTables1.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
    
    
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


