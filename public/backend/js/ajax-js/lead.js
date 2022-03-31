
/* global messages, message */

try {

    var oTable,oTables1,oTables2,oTables3;
    jQuery(document).ready(function ($) {
        
        //User Listing code
        oTables = $('#leadMaster').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: false,
            "dom": '<"top">rt<"bottom"flpi><"clear">',
            ajax: {
               "url": messages.get_lead, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_email = $('input[name=by_email]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d.pan = $('select[name=pan]').val();
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
                    {data: 'active'},
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
            order: [[5, "desc"]],
            ajax: {
               "url": messages.get_anch_user_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_email = $('input[name=by_email]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d.pan = $('select[name=pan]').val();
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
                    {data: 'biz_name'},
                    {data: 'email'},
                    {data: 'phone'},
                    {data: 'created_at'},
                    {data: 'action'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,3,4,6]}]

        });  
      
        $('#anchUserListSearchB').on('click', function (e) {
            oTables1.draw();

        });
        
      //User Listing code
        oTables2 = $('#anchleadList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            order: [[0, "desc"]],
            ajax: {
               "url": messages.get_anch_lead_list, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_email = $('input[name=by_email]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d.pan = $('select[name=pan]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling
                   
                    $("#anchleadList").append('<tbody class="leadMaster-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
           columns: [
                    {data: 'anchor_user_id'},
                    {data: 'name'},
                    {data: 'biz_name'},
                    {data: 'pan_no'},
                    {data: 'email'},
                    {data: 'phone'},
                    {data: 'assoc_anchor'},
                    {data: 'created_at'},
                    {data: 'status'}
                ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [1,3,4,5,6,8]}]

        });
        
        //Search
        $('#anchleadListSearchB').on('click', function (e) {
            oTables2.draw();

        });
        
         //Non Anchor Leads Listing code
         oTables3 = $('#nonAnchleadList').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            bSort: true,
            order: [[7, "desc"]],
            ajax: {
                "url": messages.get_non_anchor_leads, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.by_email = $('input[name=by_email]').val();
                    d._token = messages.token;
                },
                "error": function () {  // error handling            
                    $("#nonAnchleadList").append('<tbody class="nonAnchleadList-error"><tr><th colspan="6">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#nonAnchleadList_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'non_anchor_lead_id'},
                {data: 'name'},
                {data: 'biz_name'},
                {data: 'pan_no'},
                {data: 'email'},
                {data: 'phone'},
                {data: 'user_type'},
                {data: 'created_at'},
                {data: 'status'}
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [1,3,4,5,6,8]}]
        });

        //Search
        $('#nonAnchleadListSearch').on('click', function (e) {
            oTables3.draw();
        });
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
