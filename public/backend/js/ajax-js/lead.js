
/* global messages, message */

try {

    var oTable,oTables1;
    jQuery(document).ready(function ($) {
        
        //User Listing code
        oTables = $('#leadMaster').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_lead, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_email = $('input[name=by_email]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#leadMaster").append('<tbody class="leadMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                 // {data: 'checkbox'},
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'mobile_no'},
                    {data: 'anchor'},
                    {data: 'userType'},
                    {data: 'salesper'},
                    {data: 'assigned'},
                    //{data: 'biz_name'},
                    {data: 'created_at'},
                    //{data: 'status'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,3,4,5,6,7]}]

        });

        //Search
        $('#searchB').on('click', function (e) {
            oTables.draw();

        });
        
     
      
    //User Listing code
        oTables1 = $('#anchUserList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            ajax: {
               "url": messages.get_anch_user_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_email = $('input[name=by_email]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#anchUserList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'anchor_id'},
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'phone'},
                    {data: 'email'},
                    {data: 'created_at'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,3,4,5,6]}]

        });  
      
      
      
       
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
