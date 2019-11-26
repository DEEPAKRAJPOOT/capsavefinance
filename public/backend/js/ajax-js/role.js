
/* global messages, message */

try {

    var oTables1
    jQuery(document).ready(function ($) {
        
         
    //Role Listing code
        oTables1 = $('#RoleList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: true,
            bSort: true,
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
                    {data: 'role_id'},
                    {data: 'name'},
                    {data: 'description'},
                    {data: 'active'},
                    {data: 'created_at'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [1,2,3,4]}]

        });  
       
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
